<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ])->withInput();
        }
        
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Update last login
            $user->update(['last_login_at' => now()]);
            
            // Check if user must change password
            if ($user->must_change_password) {
                return redirect()->route('password.change.form')->with('info', 'Anda harus mengganti password sebelum melanjutkan.');
            }
            
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }

    public function showChangePassword()
    {
        $user = Auth::user();
        
        // Only allow if user must change password
        if (!$user->must_change_password) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        
        // Only allow if user must change password
        if (!$user->must_change_password) {
            return redirect()->route('dashboard');
        }
        
        $request->validate([
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 karakter khusus (@$!%*?&).',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Update password and remove change requirement
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diganti.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'confirm_password' => 'required|same:new_password'
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 karakter khusus (@$!%*?&).',
            'confirm_password.required' => 'Konfirmasi password baru wajib diisi.',
            'confirm_password.same' => 'Konfirmasi password baru tidak cocok.'
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Password saat ini salah.']
                ]
            ], 422);
        }

        // Check if new password is different from current
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'new_password' => ['Password baru harus berbeda dari password saat ini.']
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user must change password
        if ($user->must_change_password) {
            return redirect()->route('password.change.form')->with('info', 'Anda harus mengganti password sebelum melanjutkan.');
        }
        
        // All roles now use the admin dashboard view with admin styling
        return view('dashboard.admin', compact('user'));
    }
}
