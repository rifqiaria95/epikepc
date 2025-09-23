<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Console\Command;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-verification {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test custom email verification notification dengan email yang diberikan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }
        
        if ($user->hasVerifiedEmail()) {
            $this->info("User {$user->name} sudah memverifikasi email.");
            
            $confirm = $this->confirm('Apakah Anda ingin tetap mengirim email test?');
            if (!$confirm) {
                return 0;
            }
        }
        
        try {
            // Kirim notification
            $user->notify(new CustomVerifyEmail);
            
            $this->info("✅ Email verification berhasil dikirim ke: {$user->email}");
            $this->info("👤 Nama: {$user->name}");
            $this->line("");
            $this->info("📧 Silakan cek email Anda (atau log mail jika menggunakan log driver)");
            
        } catch (\Exception $e) {
            $this->error("❌ Gagal mengirim email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
