<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        // Validate per_page parameter
        if (!in_array($perPage, [3, 5, 10])) {
            $perPage = 10;
        }
        
        $transactions = Transaction::with(['schedule.destination', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('transactions.index', compact('transactions', 'perPage'));
    }

    public function create()
    {
        $schedules = Schedule::with('destination')
            ->where('is_active', true)
            ->where('departure_date', '>=', now()->format('Y-m-d'))
            ->where('available_seats', '>', 0)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        return view('transactions.create', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'passenger_name' => 'required|string|max:255',
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        $schedule = Schedule::with('destination')->findOrFail($request->schedule_id);
        $totalPassengers = $request->adult_count + $request->child_count;

        if ($schedule->available_seats < $totalPassengers) {
            return back()->withErrors(['seats' => 'Not enough available seats.']);
        }

        $totalAmount = ($request->adult_count * $schedule->destination->adult_price) + ($request->child_count * $schedule->destination->child_price);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'schedule_id' => $request->schedule_id,
                'passenger_name' => $request->passenger_name,
                'adult_count' => $request->adult_count,
                'child_count' => $request->child_count,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
                'created_by' => Auth::id(),
                'notes' => $request->notes,
                'paid_at' => $request->payment_method === 'cash' ? now() : null,
                'payment_reference' => $request->payment_reference
            ]);

            for ($i = 1; $i <= $totalPassengers; $i++) {
                $ticketCode = 'TKT-' . $transaction->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $passengerType = $i <= $request->adult_count ? 'adult' : 'child';
                $price = $passengerType === 'adult' ? $schedule->destination->adult_price : $schedule->destination->child_price;
                
                // Generate QR Code data
                $qrData = json_encode([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'schedule_id' => $schedule->id,
                    'passenger_type' => $passengerType,
                    'departure_date' => $schedule->departure_date->format('Y-m-d'),
                    'departure_time' => $schedule->departure_time->format('H:i'),
                    'destination' => $schedule->destination->name
                ]);
                
                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $i == 1 ? $request->passenger_name : $request->passenger_name . ' (' . $i . ')',
                    'passenger_type' => $passengerType,
                    'price' => $price,
                    'qr_code' => $qrData,
                    'status' => 'active'
                ]);
            }

            $schedule->update([
                'available_seats' => $schedule->available_seats - $totalPassengers
            ]);

            DB::commit();

            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaction created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['schedule.destination', 'tickets', 'creator']);
        
        return view('transactions.show', compact('transaction'));
    }

    public function confirmPayment(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_reference' => 'required|string|max:255'
        ]);

        $transaction->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $request->payment_reference
        ]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Payment confirmed successfully!');
    }

    public function printTickets(Transaction $transaction)
    {
        $transaction->load(['schedule.destination', 'tickets']);
        
        return view('transactions.print', compact('transaction'));
    }
}
