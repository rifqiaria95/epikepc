<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelanggan>
 */
class PelangganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Pelanggan::class;

    public function definition()
    {
        $provinsi  = Province::inRandomOrder()->first();
        $kota = $provinsi
            ? City::where('province_code', $provinsi->code)->inRandomOrder()->first()
            : null;

        return [
            'id'               => Str::uuid(),
            'nm_pelanggan'     => $this->faker->name,
            'alamat_pelanggan' => $this->faker->address,
            'no_telp_pelanggan'  => $this->faker->numerify('08##########'),
            'deskripsi'        => $this->faker->optional()->sentence,
            'status'           => $this->faker->boolean,
            'id_provinsi'      => $provinsi->id_provinsi ?? 1,
            'id_kota'          => $kota->id_kota ?? 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }
}
