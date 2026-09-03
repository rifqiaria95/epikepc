<?php

namespace App\Services\Career;

use App\Contracts\MalwareScannerInterface;
use App\Enums\Career\DocumentType;
use App\Exceptions\Career\CareerDomainException;
use App\Models\Career\CareerDocumentAccessLog;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateDocumentService
{
    public function __construct(
        protected MalwareScannerInterface $scanner,
    ) {}

    /**
     * Store a document on the private disk. Returns the model after persist.
     * Caller must wrap this in a transaction and call cleanupStoredPath() on rollback.
     *
     * @return array{document: JobApplicationDocument, stored_path: string}
     */
    public function store(
        JobApplication $application,
        UploadedFile $file,
        DocumentType $type,
    ): array {
        $this->assertSafeUpload($file, $type);

        $extension = $this->resolvedExtension($file);
        $storedName = (string) Str::uuid().'.'.$extension;
        $directory = trim((string) config('career.documents.directory'), '/').'/'.$application->id;
        $path = $directory.'/'.$storedName;
        $disk = (string) config('career.documents.disk', 'local');

        $written = Storage::disk($disk)->putFileAs($directory, $file, $storedName);

        if (! $written) {
            throw new CareerDomainException('Berkas gagal disimpan. Silakan coba lagi.', 500, [
                'cv' => ['Berkas gagal disimpan. Silakan coba lagi.'],
            ]);
        }

        $scanStatus = $this->scanner->scan($disk, $path, $file);

        $document = JobApplicationDocument::query()->create([
            'job_application_id' => $application->id,
            'document_type' => $type,
            'original_name' => $this->safeOriginalName($file),
            'stored_name' => $storedName,
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize() ?: 0,
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'scan_status' => $scanStatus,
            'uploaded_at' => now(),
        ]);

        return ['document' => $document, 'stored_path' => $path];
    }

    public function cleanupStoredPath(string $path): void
    {
        $disk = (string) config('career.documents.disk', 'local');

        if ($path !== '' && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function download(JobApplicationDocument $document, User $user, string $ip, ?string $userAgent): StreamedResponse
    {
        if (! $document->isTrustedForDownload()) {
            throw new CareerDomainException('Dokumen ini tidak dapat diunduh karena status pemindaian tidak aman.', 403, [
                'document' => ['Dokumen ini tidak dapat diunduh.'],
            ]);
        }

        if (! Storage::disk($document->disk)->exists($document->path)) {
            throw new CareerDomainException('Berkas tidak ditemukan di penyimpanan.', 404);
        }

        CareerDocumentAccessLog::query()->create([
            'job_application_document_id' => $document->id,
            'user_id' => $user->id,
            'action' => 'download',
            'ip_address' => $ip,
            'user_agent' => Str::limit((string) $userAgent, 255, ''),
            'created_at' => now(),
        ]);

        $downloadName = $this->safeOriginalNameFromStored($document);

        return Storage::disk($document->disk)->download(
            $document->path,
            $downloadName,
            [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function assertSafeUpload(UploadedFile $file, DocumentType $type): void
    {
        if (! $file->isValid()) {
            throw new CareerDomainException('Berkas tidak valid.', 422, [
                'cv' => ['Berkas tidak valid.'],
            ]);
        }

        $maxKb = (int) config('career.documents.max_cv_kilobytes', 5120);

        if (($file->getSize() ?: 0) > $maxKb * 1024) {
            throw new CareerDomainException('Ukuran CV maksimal 5 MB.', 422, [
                'cv' => ['Ukuran CV maksimal 5 MB.'],
            ]);
        }

        $clientExt = strtolower((string) $file->getClientOriginalExtension());
        $blocked = config('career.documents.blocked_extensions', []);

        if (in_array($clientExt, $blocked, true)) {
            throw new CareerDomainException('Tipe berkas tidak diizinkan.', 422, [
                'cv' => ['CV wajib diunggah dalam format PDF, DOC, atau DOCX.'],
            ]);
        }

        $allowedExt = $type === DocumentType::Cv
            ? config('career.documents.allowed_cv_mimes', ['pdf', 'doc', 'docx'])
            : config('career.documents.allowed_certificate_mimes', ['pdf', 'jpg', 'jpeg', 'png']);

        $resolvedExt = $this->resolvedExtension($file);

        if (! in_array($resolvedExt, $allowedExt, true)) {
            throw new CareerDomainException('CV wajib diunggah dalam format PDF, DOC, atau DOCX.', 422, [
                'cv' => ['CV wajib diunggah dalam format PDF, DOC, atau DOCX.'],
            ]);
        }

        $allowedMimes = config('career.documents.allowed_cv_mime_types', []);
        $mime = (string) $file->getMimeType();

        if ($type === DocumentType::Cv && $allowedMimes !== [] && ! in_array($mime, $allowedMimes, true)) {
            throw new CareerDomainException('CV wajib diunggah dalam format PDF, DOC, atau DOCX.', 422, [
                'cv' => ['CV wajib diunggah dalam format PDF, DOC, atau DOCX.'],
            ]);
        }

        $this->assertMagicBytes($file, $resolvedExt);
    }

    protected function assertMagicBytes(UploadedFile $file, string $extension): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            throw new CareerDomainException('Berkas tidak dapat dibaca.', 422, [
                'cv' => ['Berkas tidak dapat dibaca.'],
            ]);
        }

        $header = fread($handle, 8) ?: '';
        fclose($handle);

        $valid = match ($extension) {
            'pdf' => str_starts_with($header, '%PDF'),
            'doc' => str_starts_with($header, "\xD0\xCF\x11\xE0"),
            'docx' => str_starts_with($header, 'PK'),
            'jpg', 'jpeg' => str_starts_with($header, "\xFF\xD8\xFF"),
            'png' => str_starts_with($header, "\x89PNG"),
            default => false,
        };

        if (! $valid) {
            throw new CareerDomainException('Isi berkas tidak sesuai dengan format yang diizinkan.', 422, [
                'cv' => ['CV wajib diunggah dalam format PDF, DOC, atau DOCX.'],
            ]);
        }

        if (preg_match('/<(?:html|script|svg|iframe|!doctype)/i', $header)) {
            throw new CareerDomainException('Tipe berkas tidak diizinkan.', 422, [
                'cv' => ['Tipe berkas tidak diizinkan.'],
            ]);
        }
    }

    protected function resolvedExtension(UploadedFile $file): string
    {
        $mime = (string) $file->getMimeType();

        return match ($mime) {
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => strtolower((string) $file->guessExtension() ?: $file->getClientOriginalExtension()),
        };
    }

    protected function safeOriginalName(UploadedFile $file): string
    {
        $name = basename((string) $file->getClientOriginalName());
        $name = preg_replace('/[^\w.\- ()\[\]]+/u', '_', $name) ?: 'document';

        return Str::limit($name, 180, '');
    }

    protected function safeOriginalNameFromStored(JobApplicationDocument $document): string
    {
        $name = $document->original_name ?: $document->stored_name;

        return str_replace(['"', "\r", "\n"], '', $name);
    }
}
