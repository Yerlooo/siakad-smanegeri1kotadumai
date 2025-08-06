<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaliKelasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$privileges): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user()->load('role');
        
        if (!$user->role) {
            abort(403, 'Unauthorized. No role assigned.');
        }

        // Allow admin roles (kepala_tatausaha, tata_usaha) to access everything
        if (in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha'])) {
            return $next($request);
        }

        // Check if user is guru
        if (!$user->isGuru()) {
            abort(403, 'Unauthorized. Only teachers can access this resource.');
        }

        // If no specific privileges are defined, just check if user is wali kelas
        if (empty($privileges)) {
            if (!$user->isWaliKelas()) {
                abort(403, 'Unauthorized. Only homeroom teachers can access this resource.');
            }
            return $next($request);
        }

        // Check specific wali kelas privileges
        foreach ($privileges as $privilege) {
            switch ($privilege) {
                case 'wali_kelas_only':
                    if (!$user->isWaliKelas()) {
                        abort(403, 'Unauthorized. Only homeroom teachers can access this resource.');
                    }
                    break;
                    
                case 'wali_kelas_or_teaching':
                    // Allow if user is wali kelas OR teaching in the requested class
                    $kelasId = $request->route('kelas') ? $request->route('kelas')->id : $request->get('kelas_id');
                    if ($kelasId) {
                        $hasAccess = $user->canAccessAsWaliKelas($kelasId) || 
                                   $user->jadwalPelajaran()->where('kelas_id', $kelasId)->exists();
                        if (!$hasAccess) {
                            abort(403, 'Unauthorized. You can only access classes you teach or supervise as homeroom teacher.');
                        }
                    }
                    break;
                    
                case 'enhanced_access':
                    // Enhanced access for wali kelas - allows additional operations
                    if (!$user->isWaliKelas() && !in_array($user->role->name, ['kepala_tatausaha'])) {
                        abort(403, 'Unauthorized. Enhanced access requires homeroom teacher privileges.');
                    }
                    break;
            }
        }

        return $next($request);
    }
}
