<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');
        
        return [
            // Account Details
            'name'  => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                'max:255',
                Rule:: unique('users', 'email')->ignore($userId)
            ],
            'password'              => $userId ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'password_confirmation' => $userId ? 'nullable' : 'required',
            
            // Identity Information
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule:: unique('user_profiles', 'nik')->ignore($userId, 'user_id')
            ],
            'kk' => [
                'required',
                'string', 
                'size:16',
                'regex:/^[0-9]+$/',
                Rule:: unique('user_profiles', 'kk')->ignore($userId, 'user_id')
            ],
            'tempat_lahir'  => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'no_hp'         => 'required|string|max:20|regex:/^[0-9+]+$/',
            'pekerjaan'     => 'nullable|string|max:100',
            'penghasilan'   => 'nullable|numeric|min:0',
            
            // Address Information
            'alamat'        => 'required|string|max:500',
            'rt'            => 'required|string|max:3|regex:/^[0-9]+$/',
            'rw'            => 'required|string|max:3|regex:/^[0-9]+$/',
            'kode_pos'      => 'required|string|max:5|regex:/^[0-9]+$/',
            'jenis_kelamin' => 'required|string|max:255',
            'id_provinsi'   => 'required|string|max:50',
            'id_kota'       => 'required|string|max:50',
            'id_kecamatan'  => 'required|string|max:50',
            'id_kelurahan'  => 'required|string|max:50',
            
            // File Uploads
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kk'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // Admin fields (optional for updates)
            'active' => 'nullable|integer|in:0,1',
            'role'   => 'nullable|string|max:50'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
            'nik' => 'NIK',
            'kk' => 'nomor kartu keluarga',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'no_hp' => 'nomor HP',
            'pekerjaan' => 'pekerjaan',
            'penghasilan' => 'penghasilan',
            'alamat' => 'alamat',
            'rt' => 'RT',
            'rw' => 'RW',
            'id_provinsi' => 'provinsi',
            'id_kota' => 'kota/kabupaten',
            'id_kecamatan' => 'kecamatan',
            'id_kelurahan' => 'kelurahan/desa',
            'foto_ktp' => 'foto KTP',
            'foto_kk' => 'foto kartu keluarga',
            'active' => 'status aktif'
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal 255 karakter.',
            
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            
            'kk.required' => 'Nomor Kartu Keluarga wajib diisi.',
            'kk.size' => 'Nomor Kartu Keluarga harus 16 digit.',
            'kk.regex' => 'Nomor Kartu Keluarga hanya boleh berisi angka.',
            'kk.unique' => 'Nomor Kartu Keluarga sudah terdaftar.',
            
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',
            
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka dan tanda +.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            
            'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',
            
            'penghasilan.numeric' => 'Penghasilan harus berupa angka.',
            'penghasilan.min' => 'Penghasilan tidak boleh negatif.',
            
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            
            'rt.required' => 'RT wajib diisi.',
            'rt.max' => 'RT maksimal 3 karakter.',
            'rt.regex' => 'RT hanya boleh berisi angka.',
            
            'rw.required' => 'RW wajib diisi.',
            'rw.max' => 'RW maksimal 3 karakter.',
            'rw.regex' => 'RW hanya boleh berisi angka.',
            
            'id_provinsi.required' => 'Provinsi wajib dipilih.',
            'id_kota.required' => 'Kota/Kabupaten wajib dipilih.',
            'id_kecamatan.required' => 'Kecamatan wajib dipilih.',
            'id_kelurahan.required' => 'Kelurahan/Desa wajib dipilih.',
            
            'foto_ktp.image' => 'File foto KTP harus berupa gambar.',
            'foto_ktp.mimes' => 'Format foto KTP harus jpg, jpeg, atau png.',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
            
            'foto_kk.image' => 'File foto Kartu Keluarga harus berupa gambar.',
            'foto_kk.mimes' => 'Format foto Kartu Keluarga harus jpg, jpeg, atau png.',
            'foto_kk.max' => 'Ukuran foto Kartu Keluarga maksimal 2MB.',
            
            'active.in' => 'Status aktif harus 0 atau 1.'
        ];
    }
}
