<?php

namespace App\Http\Requests;

use App\Models\CoverageLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoverageLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        $type = $this->input('type', CoverageLocation::TYPE_DUKUH);
        $isReference = $type === CoverageLocation::TYPE_REFERENCE;

        return [
            'kabupaten' => [$isReference ? 'nullable' : 'required', 'string', 'max:120'],
            'kelurahan' => [$isReference ? 'nullable' : 'required', 'string', 'max:120'],
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique('coverage_locations', 'name')
                    ->where(fn ($query) => $query
                        ->where('type', $type)
                        ->where('kabupaten', $this->input('kabupaten'))
                        ->where('kelurahan', $this->input('kelurahan'))
                        ->whereNull('deleted_at'))
                    ->ignore($id),
            ],
            'type' => ['required', Rule::in([
                CoverageLocation::TYPE_DUKUH,
                CoverageLocation::TYPE_PERUMAHAN,
                CoverageLocation::TYPE_REFERENCE,
            ])],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kabupaten' => 'kabupaten/kota',
            'kelurahan' => 'kelurahan',
            'name' => 'nama lokasi',
            'type' => 'tipe lokasi',
            'sort_order' => 'urutan',
        ];
    }
}
