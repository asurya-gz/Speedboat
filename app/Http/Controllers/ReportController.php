<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Schedule;
use App\Models\Destination;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with(['schedule.destination', 'creator']);
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->filled('destination_id')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('destination_id', $request->destination_id);
            });
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);
        $destinations = Destination::where('status', 'active')->get();
        
        // Summary data
        $totalRevenue = $query->where('payment_status', 'paid')->sum('total_amount');
        $totalTransactions = $query->count();
        $totalPassengers = $query->sum('adult_count') + $query->sum('child_count') + $query->sum('toddler_count');
        
        return view('reports.transactions', compact(
            'transactions', 
            'destinations', 
            'totalRevenue', 
            'totalTransactions', 
            'totalPassengers'
        ));
    }

    public function tickets(Request $request)
    {
        $query = Ticket::with(['transaction.schedule.destination']);
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->filled('destination_id')) {
            $query->whereHas('transaction.schedule', function($q) use ($request) {
                $q->where('destination_id', $request->destination_id);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(50);
        $destinations = Destination::where('status', 'active')->get();
        
        // Summary data
        $totalTickets = $query->count();
        $validatedTickets = $query->where('status', 'validated')->count();
        $pendingTickets = $query->where('status', 'pending')->count();
        
        return view('reports.tickets', compact(
            'tickets', 
            'destinations', 
            'totalTickets', 
            'validatedTickets', 
            'pendingTickets'
        ));
    }

    public function dashboard()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Today's statistics
        $todayRevenue = Transaction::where('payment_status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total_amount');
            
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        
        $todayPassengers = Transaction::whereDate('created_at', $today)
            ->sum('adult_count') + Transaction::whereDate('created_at', $today)
            ->sum('child_count') + Transaction::whereDate('created_at', $today)
            ->sum('toddler_count');
        
        // This month's statistics
        $monthRevenue = Transaction::where('payment_status', 'paid')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total_amount');
            
        $monthTransactions = Transaction::where('created_at', '>=', $thisMonth)->count();
        
        // Popular destinations this month
        $popularDestinations = Destination::withCount(['schedules as transactions_count' => function($query) use ($thisMonth) {
            $query->join('transactions', 'schedules.id', '=', 'transactions.schedule_id')
                  ->where('transactions.created_at', '>=', $thisMonth);
        }])->orderBy('transactions_count', 'desc')->take(5)->get();
        
        return view('reports.dashboard', compact(
            'todayRevenue',
            'todayTransactions', 
            'todayPassengers',
            'monthRevenue',
            'monthTransactions',
            'popularDestinations'
        ));
    }
}
