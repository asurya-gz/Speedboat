<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Destination;
use App\Models\Schedule;
use App\Models\Transaction;

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

    public function analytics()
    {
        $user = Auth::user();
        
        // Check if user must change password
        if ($user->must_change_password) {
            return redirect()->route('password.change.form')->with('info', 'Anda harus mengganti password sebelum melanjutkan.');
        }
        
        // Get analytics data
        $analytics = $this->getAnalyticsData();
        
        return view('dashboard.analytics', compact('user', 'analytics'));
    }

    private function getAnalyticsData()
    {
        // Total Revenue
        $totalRevenue = Transaction::where('payment_status', 'paid')->sum('total_amount');
        
        // Total Tickets Sold
        $totalTicketsSold = Transaction::where('payment_status', 'paid')
            ->sum(DB::raw('adult_count + child_count + COALESCE(toddler_count, 0)'));
        
        // Total Destinations
        $totalDestinations = Destination::where('is_active', true)->count();
        
        // Active Schedules (next 15 days)
        $activeSchedules = Schedule::where('departure_time', '>=', now())
            ->where('departure_time', '<=', now()->addDays(15))
            ->where('is_active', true)
            ->count();
        
        // Monthly Revenue (last 6 months)
        $monthlyRevenue = Transaction::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as revenue')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Popular Destinations (top 5 by transaction count)
        $popularDestinations = Transaction::join('schedules', 'transactions.schedule_id', '=', 'schedules.id')
            ->join('destinations', 'schedules.destination_id', '=', 'destinations.id')
            ->selectRaw('destinations.name, destinations.code, COUNT(*) as transaction_count')
            ->where('transactions.payment_status', 'paid')
            ->groupBy('destinations.id', 'destinations.name', 'destinations.code')
            ->orderBy('transaction_count', 'desc')
            ->limit(5)
            ->get();
        
        // Daily Sales Trend (last 7 days)
        $dailySales = Transaction::selectRaw('DATE(created_at) as date, SUM(adult_count + child_count + COALESCE(toddler_count, 0)) as tickets_sold')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Recent Activities (last 10 transactions)
        $recentActivities = Transaction::with(['schedule.destination'])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Calculate percentage changes (comparing with previous month)
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        
        $currentMonthRevenue = Transaction::where('payment_status', 'paid')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->sum('total_amount');
            
        $previousMonthRevenue = Transaction::where('payment_status', 'paid')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$previousMonth])
            ->sum('total_amount');
        
        $revenueChange = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;
        
        $currentMonthTickets = Transaction::where('payment_status', 'paid')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->sum(DB::raw('adult_count + child_count + COALESCE(toddler_count, 0)'));
            
        $previousMonthTickets = Transaction::where('payment_status', 'paid')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$previousMonth])
            ->sum(DB::raw('adult_count + child_count + COALESCE(toddler_count, 0)'));
        
        $ticketsChange = $previousMonthTickets > 0 
            ? (($currentMonthTickets - $previousMonthTickets) / $previousMonthTickets) * 100 
            : 0;
        
        return [
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold,
            'totalDestinations' => $totalDestinations,
            'activeSchedules' => $activeSchedules,
            'monthlyRevenue' => $monthlyRevenue,
            'popularDestinations' => $popularDestinations,
            'dailySales' => $dailySales,
            'recentActivities' => $recentActivities,
            'revenueChange' => $revenueChange,
            'ticketsChange' => $ticketsChange,
        ];
    }
}
