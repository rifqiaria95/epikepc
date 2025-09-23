<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimoni;
use App\Models\User;

class TestimoniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama sebagai created_by
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $testimonis = [
            [
                'nama' => 'Ahmad Rizki',
                'testimoni' => 'Sangat puas dengan layanan yang diberikan. Tim sangat profesional dan responsif dalam menangani kebutuhan kami. Hasil yang diperoleh sesuai dengan ekspektasi.',
                'instansi' => 'PT Maju Bersama',
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'nama' => 'Sarah Amelia',
                'testimoni' => 'Pengalaman yang luar biasa! Proses dari awal hingga akhir berjalan dengan lancar. Komunikasi yang baik dan hasil yang memuaskan.',
                'instansi' => 'CV Sukses Mandiri',
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'nama' => 'Budi Santoso',
                'testimoni' => 'Terima kasih atas kerjasamanya yang sangat baik. Tim sangat kompeten dan memberikan solusi yang tepat untuk kebutuhan bisnis kami.',
                'instansi' => 'UD Makmur Jaya',
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'nama' => 'Dewi Sartika',
                'testimoni' => 'Layanan yang sangat memuaskan. Tim bekerja dengan efisien dan hasil yang diperoleh melebihi ekspektasi kami. Sangat direkomendasikan!',
                'instansi' => 'PT Sejahtera Abadi',
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'nama' => 'Rudi Hermawan',
                'testimoni' => 'Kerjasama yang sangat menyenangkan. Tim sangat profesional dan memberikan hasil yang berkualitas tinggi. Akan menggunakan layanan lagi di masa depan.',
                'instansi' => 'CV Berkah Sentosa',
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        ];

        foreach ($testimonis as $testimoni) {
            Testimoni::create($testimoni);
        }

        $this->command->info('Testimoni data seeded successfully!');
    }
}
