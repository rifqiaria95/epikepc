<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validasi ekstensi file yang diizinkan
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $filename = $request->route('filename');
        
        if ($filename) {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedExtensions)) {
                abort(403, 'File type not allowed');
            }
            
            // Validasi path untuk mencegah directory traversal
            if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
                abort(403, 'Invalid file path');
            }
        }
        
        return $next($request);
    }
}
