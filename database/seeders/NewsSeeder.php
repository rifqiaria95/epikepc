<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user as author
        $user = User::first();
        
        if (!$user) {
            // Create a default user if none exists
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $newsData = [
            [
                'title' => 'Teknologi AI Terbaru dalam Pengembangan Web',
                'slug' => 'teknologi-ai-terbaru-dalam-pengembangan-web',
                'content' => 'Artificial Intelligence (AI) telah menjadi game-changer dalam industri pengembangan web. Dengan kemajuan teknologi machine learning dan deep learning, para developer sekarang dapat menciptakan aplikasi web yang lebih cerdas dan responsif.\n\nAI memungkinkan personalisasi konten yang lebih baik, chatbot yang lebih natural, dan sistem rekomendasi yang akurat. Teknologi ini juga membantu dalam optimasi performa website dan meningkatkan user experience secara signifikan.',
                'summary' => 'Pembahasan mendalam tentang bagaimana AI mengubah cara pengembangan web modern dan meningkatkan user experience.',
                'thumbnail' => '/images/blog/blog-img1.jpg',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Responsive Design: Pentingnya Mobile-First Approach',
                'slug' => 'responsive-design-pentingnya-mobile-first-approach',
                'content' => 'Dalam era digital saat ini, mobile-first approach bukan lagi pilihan, melainkan keharusan. Dengan lebih dari 60% traffic web berasal dari perangkat mobile, desain responsif menjadi kunci kesuksesan website.\n\nMobile-first design memastikan website terlihat dan berfungsi optimal di semua ukuran layar, dari smartphone hingga desktop. Pendekatan ini juga meningkatkan SEO dan user engagement secara signifikan.',
                'summary' => 'Strategi mobile-first dalam pengembangan website modern untuk meningkatkan user experience dan SEO.',
                'thumbnail' => '/images/blog/blog-img2.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Keamanan Website: Best Practices 2024',
                'slug' => 'keamanan-website-best-practices-2024',
                'content' => 'Keamanan website menjadi prioritas utama di tahun 2024. Dengan meningkatnya serangan cyber, implementasi security best practices menjadi wajib bagi setiap website.\n\nHTTPS, SSL certificates, regular updates, dan secure coding practices adalah beberapa hal yang harus diperhatikan. Selain itu, implementasi Content Security Policy (CSP) dan regular security audits juga sangat penting.',
                'summary' => 'Panduan lengkap tentang implementasi keamanan website terbaru untuk melindungi data pengguna.',
                'thumbnail' => '/images/blog/blog-img3.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Performance Optimization: Teknik Mempercepat Website',
                'slug' => 'performance-optimization-teknik-mempercepat-website',
                'content' => 'Website yang cepat adalah website yang sukses. Performance optimization menjadi faktor kritis dalam user experience dan SEO ranking.\n\nTeknik seperti lazy loading, image optimization, minification, dan caching dapat meningkatkan loading speed secara signifikan. Tools seperti Google PageSpeed Insights membantu mengidentifikasi area yang perlu dioptimasi.',
                'summary' => 'Teknik-teknik optimasi performa website untuk meningkatkan loading speed dan user experience.',
                'thumbnail' => '/images/blog/blog-img4.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
            [
                'title' => 'Progressive Web Apps: Masa Depan Web Development',
                'slug' => 'progressive-web-apps-masa-depan-web-development',
                'content' => 'Progressive Web Apps (PWA) menggabungkan keunggulan web dan aplikasi mobile. Dengan fitur seperti offline functionality, push notifications, dan app-like experience, PWA menjadi solusi ideal untuk banyak bisnis.\n\nPWA memungkinkan pengguna mengakses website seperti aplikasi native, dengan performa yang cepat dan pengalaman yang konsisten di semua platform.',
                'summary' => 'Eksplorasi teknologi PWA dan bagaimana mengimplementasikannya untuk meningkatkan user engagement.',
                'thumbnail' => '/images/blog/blog-img5.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
            [
                'title' => 'SEO untuk Developer: Panduan Lengkap',
                'slug' => 'seo-untuk-developer-panduan-lengkap',
                'content' => 'SEO bukan hanya tanggung jawab content writer, tetapi juga developer. Technical SEO memainkan peran penting dalam ranking website di search engine.\n\nImplementasi structured data, optimasi Core Web Vitals, dan technical SEO best practices dapat meningkatkan visibility website secara signifikan. Developer harus memahami prinsip-prinsip SEO untuk menciptakan website yang SEO-friendly.',
                'summary' => 'Panduan technical SEO untuk developer dalam mengoptimasi website untuk search engine.',
                'thumbnail' => '/images/blog/blog-img6.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'author_id' => $user->id,
                'created_by' => $user->id,
            ],
        ];

        foreach ($newsData as $news) {
            News::create($news);
        }
    }
}