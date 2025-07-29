<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Password berhasil diperbarui.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $errors = $e->errors();
            
            if (isset($errors['current_password'])) {
                return back()->with('error', 'Password lama yang Anda masukkan salah.');
            }
            
            if (isset($errors['password'])) {
                $passwordErrors = $errors['password'];
                if (in_array('The password field confirmation does not match.', $passwordErrors)) {
                    return back()->with('error', 'Konfirmasi password tidak sama.');
                }
                // Handle password strength errors
                return back()->with('error', 'Password baru tidak memenuhi kriteria keamanan.');
            }
            
            return back()->with('error', 'Terjadi kesalahan saat mengubah password.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}
