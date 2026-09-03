<?php

namespace App\Models\Career;

use App\Enums\Career\DocumentScanStatus;
use App\Enums\Career\DocumentType;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplicationDocument extends Model
{
    use HasFactory, HasUuidPrimaryKey;

    protected $table = 'job_application_documents';

    protected $fillable = [
        'job_application_id',
        'document_type',
        'original_name',
        'stored_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'checksum',
        'scan_status',
        'uploaded_at',
    ];

    protected $hidden = [
        'path',
        'disk',
        'stored_name',
        'checksum',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'scan_status' => DocumentScanStatus::class,
            'size' => 'integer',
            'uploaded_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(CareerDocumentAccessLog::class, 'job_application_document_id');
    }

    public function isTrustedForDownload(): bool
    {
        // When scanner is unavailable, PENDING is the honest state.
        // CMS may still download PENDING with explicit warning + permission.
        return $this->scan_status->isTrustedForDownload()
            || $this->scan_status === DocumentScanStatus::Pending;
    }
}
