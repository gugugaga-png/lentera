<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Filament\Pages\MemberDashboard;
use App\Filament\Pages\MyBorrowings;

class RedirectBorrower
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isBorrower()) {
            $allowedPaths = [
                'member-dashboard',
                'my-borrowings',
                'livewire',
                'api',
                'logout',
                'profile',
            ];
            
            $currentPath = $request->path();
            
            // Cek apakah path saat ini ada dalam daftar yang diizinkan
            $isAllowed = false;
            foreach ($allowedPaths as $path) {
                if (str_contains($currentPath, $path)) {
                    $isAllowed = true;
                    break;
                }
            }
            
            // Jika mencoba akses halaman admin yang tidak diizinkan
            if (!$isAllowed && str_contains($currentPath, 'admin/')) {
                return redirect('/admin/member-dashboard');
            }
        }

        return $next($request);
    }
}