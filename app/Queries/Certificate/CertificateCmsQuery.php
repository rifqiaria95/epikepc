<?php

namespace App\Queries\Certificate;

use App\Models\Certificate;
use App\Support\Career\QueryLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CertificateCmsQuery
{
    use QueryLike;

    public function baseQuery(Request $request): Builder
    {
        $query = Certificate::query()
            ->withoutTrashed()
            ->select([
                'id', 'title', 'slug', 'issuer', 'certificate_number',
                'issued_at', 'expires_at', 'image_path', 'thumbnail_path',
                'image_alt', 'status', 'is_featured', 'display_order',
                'published_at', 'created_by', 'updated_by', 'created_at', 'updated_at',
            ])
            ->with([
                'createdBy:id,name',
                'updatedBy:id,name',
            ]);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($year = $request->input('issued_year')) {
            $query->whereYear('issued_at', (int) $year);
        }

        if ($request->input('expiry_state') === 'expired') {
            $query->whereNotNull('expires_at')->whereDate('expires_at', '<', now());
        } elseif ($request->input('expiry_state') === 'active') {
            $query->where(function (Builder $inner) {
                $inner->whereNull('expires_at')->orWhereDate('expires_at', '>=', now());
            });
        }

        $term = $this->datatablesSearchTerm($request);
        if ($term !== '') {
            $query->where(function (Builder $inner) use ($term) {
                $like = '%'.$term.'%';
                $inner->where('title', 'like', $like)
                    ->orWhere('issuer', 'like', $like)
                    ->orWhere('certificate_number', 'like', $like);
            });
        }

        return $query->orderBy('display_order')->orderBy('title');
    }
}
