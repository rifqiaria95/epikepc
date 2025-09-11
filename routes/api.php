<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GaleriController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\TestimoniController;
use App\Http\Controllers\Api\VisiMisiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Chat conversation endpoint
Route::post('/chat', [ChatController::class, 'handle']);

// About endpoint
Route::get('/about', [AboutController::class, 'index']);

// Visi Misi endpoint
Route::get('/visimisi', [VisiMisiController::class, 'index']);

// Galeri endpoint
Route::get('/galeri', [GaleriController::class, 'index']);

// Kategori Galeri endpoint
Route::get('/kategori-galeri', [App\Http\Controllers\Api\KategoriGaleriController::class, 'index']);

// Education endpoint
Route::get('/education', [EducationController::class, 'index']);

// Organisasi endpoint
Route::get('/organisasi', [OrganisasiController::class, 'index']);

// News endpoint
Route::get('/news', [NewsController::class, 'index']);

// Experience endpoint
Route::get('/experience', [ExperienceController::class, 'index']);

// Program endpoint
Route::get('/programs/open', [App\Http\Controllers\Api\ProgramController::class, 'getOpenPrograms']);

// Testimoni endpoint
Route::get('/testimoni', [TestimoniController::class, 'index']);

// Auth endpoints
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Route untuk gambar dinamis - support both public images and storage images
Route::get('/images/{filename}', function ($filename) {
    // First try to find in public/images directory
    $publicPath = public_path('images/' . $filename);
    
    if (file_exists($publicPath)) {
        $file = file_get_contents($publicPath);
        $type = mime_content_type($publicPath);
        
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
    
    // If not found in public, try storage directory
    $storagePath = storage_path('app/public/' . $filename);
    
    if (file_exists($storagePath)) {
        $file = file_get_contents($storagePath);
        $type = mime_content_type($storagePath);
        
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
    
    // If still not found, try to get from storage disk
    if (\Storage::disk('public')->exists($filename)) {
        $file = \Storage::disk('public')->get($filename);
        $type = \Storage::disk('public')->mimeType($filename);
        
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
    
    \Log::warning("Image file not found: {$filename}");
    
    // Return default image instead of 404
    $defaultPath = public_path('images/blog/blog-img1.jpg');
    if (file_exists($defaultPath)) {
        $file = file_get_contents($defaultPath);
        $type = mime_content_type($defaultPath);
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
    
    abort(404, "Image not found: {$filename}");
})->where('filename', '.*');
