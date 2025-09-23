<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'user_id',
        'nik',
        'kk',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'jenis_kelamin',
        'id_kelurahan',
        'id_kecamatan',
        'id_kota',
        'id_provinsi',
        'no_hp',
        'pekerjaan',
        'penghasilan',
        'foto_ktp',
        'foto_kk',
        'status_verifikasi',
        'verifikasi_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
