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
        $schedules = Schedule::with('destination', 'speedboat')
            ->where('is_active', true)
            ->orderBy('departure_time')
            ->get();

        return view('transactions.create', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'adult_names' => 'required|array|min:1',
            'adult_names.*' => 'required|string|max:255',
            'toddler_names' => 'nullable|array',
            'toddler_names.*' => 'required|string|max:255',
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'required|integer|min:0',
            'toddler_count' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        $schedule = Schedule::with('destination')->findOrFail($request->schedule_id);
        $totalPassengers = $request->adult_count + $request->child_count + $request->toddler_count;

        // Calculate available seats from capacity minus existing bookings
        $bookedSeats = $schedule->transactions()->sum('adult_count') + 
                      $schedule->transactions()->sum('child_count') + 
                      $schedule->transactions()->sum('toddler_count');
        $availableSeats = $schedule->capacity - $bookedSeats;
        
        if ($availableSeats < $totalPassengers) {
            return back()->withErrors(['seats' => 'Not enough available seats.']);
        }

        $totalAmount = ($request->adult_count * $schedule->destination->adult_price) + 
                      ($request->child_count * $schedule->destination->child_price) + 
                      ($request->toddler_count * ($schedule->destination->toddler_price ?? 0));

        // Collect all passenger names
        $adultNames = $request->adult_names ?? [];
        $toddlerNames = $request->toddler_names ?? [];
        $allPassengerNames = array_merge($adultNames, $toddlerNames);
        
        // Use first adult name as main passenger name for transaction
        $mainPassengerName = $adultNames[0] ?? 'Unknown';

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'schedule_id' => $request->schedule_id,
                'passenger_name' => $mainPassengerName,
                'adult_count' => $request->adult_count,
                'child_count' => $request->child_count,
                'toddler_count' => $request->toddler_count,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
                'created_by' => Auth::id(),
                'notes' => $request->notes,
                'paid_at' => $request->payment_method === 'cash' ? now() : null,
                'payment_reference' => $request->payment_reference
            ]);

            $ticketIndex = 0;
            
            // Create tickets for adults
            foreach ($adultNames as $adultName) {
                $ticketIndex++;
                $ticketCode = 'TKT-' . $transaction->id . '-' . str_pad($ticketIndex, 3, '0', STR_PAD_LEFT);
                
                // Generate QR Code data
                $qrData = json_encode([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'schedule_id' => $schedule->id,
                    'passenger_type' => 'adult',
                    'departure_time' => $schedule->departure_time->format('H:i'),
                    'destination_code' => $schedule->destination->code,
                    'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
                ]);
                
                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $adultName,
                    'passenger_type' => 'adult',
                    'price' => $schedule->destination->adult_price,
                    'qr_code' => $qrData,
                    'status' => 'active'
                ]);
            }
            
            // Create tickets for toddlers
            foreach ($toddlerNames as $toddlerName) {
                $ticketIndex++;
                $ticketCode = 'TKT-' . $transaction->id . '-' . str_pad($ticketIndex, 3, '0', STR_PAD_LEFT);
                
                // Generate QR Code data
                $qrData = json_encode([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'schedule_id' => $schedule->id,
                    'passenger_type' => 'toddler',
                    'departure_time' => $schedule->departure_time->format('H:i'),
                    'destination_code' => $schedule->destination->code,
                    'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
                ]);
                
                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $toddlerName,
                    'passenger_type' => 'toddler',
                    'price' => $schedule->destination->toddler_price ?? 0,
                    'qr_code' => $qrData,
                    'status' => 'active'
                ]);
            }

            // No need to update available_seats as it's calculated dynamically

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
