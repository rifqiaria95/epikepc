<?php

namespace App\Services\Certificate;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CertificateManagementService
{
    public function __construct(
        protected CertificateImageService $images,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $user): Certificate
    {
        return DB::transaction(function () use ($data, $user) {
            $data['slug'] = $this->uniqueSlug($data['title']);
            $data['created_by'] = $user->id;
            $data['status'] = $data['status'] ?? CertificateStatus::Draft;
            $data['display_order'] = $data['display_order'] ?? $this->nextDisplayOrder();

            return Certificate::query()->create($data);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Certificate $certificate, array $data, User $user): Certificate
    {
        return DB::transaction(function () use ($certificate, $data, $user) {
            if (isset($data['title']) && $data['title'] !== $certificate->title) {
                $data['slug'] = $this->uniqueSlug($data['title'], $certificate->id);
            }

            $data['updated_by'] = $user->id;
            $certificate->update($data);

            return $certificate->fresh();
        });
    }

    public function publish(Certificate $certificate, User $user): Certificate
    {
        if (! $certificate->canPublish()) {
            throw new InvalidArgumentException('Gambar dan teks alternatif wajib diisi sebelum sertifikat dipublikasikan.');
        }

        return DB::transaction(function () use ($certificate, $user) {
            $certificate->update([
                'status' => CertificateStatus::Published,
                'published_at' => $certificate->published_at ?? now(),
                'updated_by' => $user->id,
            ]);

            return $certificate->fresh();
        });
    }

    public function unpublish(Certificate $certificate, User $user): Certificate
    {
        return DB::transaction(function () use ($certificate, $user) {
            $certificate->update([
                'status' => CertificateStatus::Draft,
                'updated_by' => $user->id,
            ]);

            return $certificate->fresh();
        });
    }

    public function archive(Certificate $certificate, User $user): Certificate
    {
        return DB::transaction(function () use ($certificate, $user) {
            $certificate->update([
                'status' => CertificateStatus::Archived,
                'updated_by' => $user->id,
            ]);

            return $certificate->fresh();
        });
    }

    public function softDelete(Certificate $certificate, User $user): void
    {
        DB::transaction(function () use ($certificate, $user) {
            $this->images->deletePaths($certificate->image_path, $certificate->thumbnail_path);

            $certificate->deleted_by = $user->id;
            $certificate->save();
            $certificate->delete();
        });
    }

    /**
     * @param  array<int, string>  $orderedIds
     */
    public function reorder(array $orderedIds, User $user): void
    {
        if ($orderedIds === []) {
            throw new InvalidArgumentException('Daftar urutan sertifikat tidak boleh kosong.');
        }

        DB::transaction(function () use ($orderedIds, $user) {
            $existing = Certificate::query()
                ->withoutTrashed()
                ->whereIn('id', $orderedIds)
                ->pluck('id')
                ->all();

            if (count($existing) !== count(array_unique($orderedIds))) {
                throw new InvalidArgumentException('Satu atau lebih sertifikat tidak ditemukan.');
            }

            $cases = [];
            $bindings = [];
            $order = 1;

            foreach ($orderedIds as $id) {
                $cases[] = 'WHEN ? THEN ?';
                $bindings[] = $id;
                $bindings[] = $order++;
            }

            $placeholders = implode(',', array_fill(0, count($orderedIds), '?'));
            $sql = 'UPDATE certificates SET display_order = CASE id '.implode(' ', $cases)." END, updated_by = ?, updated_at = ? WHERE id IN ({$placeholders}) AND deleted_at IS NULL";

            $bindings[] = $user->id;
            $bindings[] = now();
            $bindings = array_merge($bindings, $orderedIds);

            DB::update($sql, $bindings);
        });
    }

    protected function nextDisplayOrder(): int
    {
        $max = Certificate::query()->withoutTrashed()->max('display_order');

        return ((int) $max) + 1;
    }

    protected function uniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?string $ignoreId = null): bool
    {
        return Certificate::query()
            ->withoutTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }
}
