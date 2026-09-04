<?php

namespace App\Http\Requests\Certificate;

use App\Enums\CertificateStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'issuer' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'certificate_number' => ['nullable', 'string', 'max:120'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'credential_url' => ['nullable', 'url', 'regex:/^https?:\/\//i', 'max:2048'],
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:'.config('certificates.max_file_size_kb')],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['nullable', Rule::in(CertificateStatus::values())],
            'published_at' => ['nullable', 'date'],
            'display_order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul sertifikat wajib diisi.',
            'issuer.required' => 'Nama lembaga penerbit wajib diisi.',
            'expires_at.after_or_equal' => 'Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal penerbitan.',
            'credential_url.url' => 'Credential URL harus berupa alamat HTTP atau HTTPS yang valid.',
            'credential_url.regex' => 'Credential URL harus berupa alamat HTTP atau HTTPS yang valid.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar sertifikat maksimal '.(config('certificates.max_file_size_kb') / 1024).' MB.',
            'display_order.min' => 'Urutan tampilan harus berupa angka positif.',
        ];
    }
}
