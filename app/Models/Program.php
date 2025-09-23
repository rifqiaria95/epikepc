<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'jenis_program_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'kuota',
        'kategori',
        'sumber_dana',
        'status',
        'created_by'
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requirements()
    {
        return $this->hasMany(ProgramRequirement::class, 'program_id');
    }

    public function registrations()
    {
        return $this->hasMany(ProgramRegistration::class, 'program_id');
    }

    public function recipients()
    {
        return $this->hasMany(ProgramRecipient::class, 'program_id');
    }

    public function jenisProgram()
    {
        return $this->belongsTo(JenisProgram::class);
    }
}
