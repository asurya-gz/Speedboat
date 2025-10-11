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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        // Validate per_page parameter
        if (!in_array($perPage, [3, 5, 10])) {
            $perPage = 10;
        }
        
        $query = Transaction::with(['schedule.destination', 'creator']);
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhereHas('schedule.destination', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('departure_location', 'like', "%{$search}%")
                        ->orWhere('destination_location', 'like', "%{$search}%");
                  });
            });
        }
        
        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('transactions.index', compact('transactions', 'perPage'));
    }

    public function create()
    {
        $speedboats = \App\Models\Speedboat::where('is_active', true)
            ->orderBy('name')
            ->get();

        $destinations = \App\Models\Destination::where('is_active', true)
            ->orderBy('departure_location')
            ->get();

        // Get all schedules for JavaScript
        $allSchedules = Schedule::with(['destination', 'speedboat'])
            ->where('is_active', true)
            ->orderBy('departure_time')
            ->get();

        return view('transactions.create', compact('speedboats', 'destinations', 'allSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'departure_date' => 'required|date|after_or_equal:today',
            'selected_seats' => 'required|json',
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

        // Calculate available seats from capacity minus existing bookings for the selected date
        $bookedSeats = $schedule->transactions()
            ->whereDate('departure_date', $request->departure_date)
            ->sum(DB::raw('adult_count + child_count + toddler_count'));
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

        // Parse seat assignments
        $seatAssignments = json_decode($request->selected_seats, true);
        if (count($seatAssignments) !== $totalPassengers) {
            return back()->withErrors(['seats' => 'Number of seat assignments must match total passengers.']);
        }

        // Extract seat numbers for availability check
        $selectedSeatNumbers = array_column($seatAssignments, 'seatNumber');
        
        // Check if selected seats are available
        $existingBookings = \App\Models\SeatBooking::where('schedule_id', $request->schedule_id)
            ->where('departure_date', $request->departure_date)
            ->whereIn('seat_number', $selectedSeatNumbers)
            ->where('status', 'booked')
            ->count();

        if ($existingBookings > 0) {
            return back()->withErrors(['seats' => 'Some selected seats are no longer available.']);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'schedule_id' => $request->schedule_id,
                'departure_date' => $request->departure_date,
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

            // Create tickets and seat bookings based on seat assignments
            foreach ($seatAssignments as $assignment) {
                $ticketIndex = $assignment['passengerIndex'] + 1;

                // Generate more complex but still readable ticket code
                // Format: SB-YYMMDD-XXX-CCC
                // SB = Speedboat prefix
                // YYMMDD = Departure date
                // XXX = Ticket number (3 digits)
                // CCC = 3-character checksum (letters + numbers)
                $datePart = date('ymd', strtotime($request->departure_date));
                $ticketNumber = str_pad($ticketIndex, 3, '0', STR_PAD_LEFT);

                // Generate checksum: combine transaction ID, ticket index, and date
                // Use only alphanumeric characters (no ambiguous like 0/O, 1/I)
                $checksumSource = $transaction->id . $ticketIndex . $datePart;
                $checksum = strtoupper(substr(md5($checksumSource), 0, 3));
                // Replace ambiguous characters
                $checksum = str_replace(['0', 'O', 'I', '1'], ['A', 'B', 'C', 'D'], $checksum);

                $ticketCode = "SB-{$datePart}-{$ticketNumber}-{$checksum}";
                
                // Determine price based on passenger type
                $price = $assignment['passengerType'] === 'adult' 
                    ? $schedule->destination->adult_price 
                    : ($schedule->destination->toddler_price ?? 0);
                
                // Generate QR Code data with seat information
                $qrData = json_encode([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'schedule_id' => $schedule->id,
                    'passenger_type' => $assignment['passengerType'],
                    'seat_number' => $assignment['seatNumber'],
                    'departure_time' => $schedule->departure_time->format('H:i'),
                    'destination_code' => $schedule->destination->code,
                    'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
                ]);
                
                // Create ticket
                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $assignment['passengerName'],
                    'passenger_type' => $assignment['passengerType'],
                    'price' => $price,
                    'qr_code' => $qrData,
                    'status' => 'active',
                    'seat_number' => $assignment['seatNumber']
                ]);
                
                // Create seat booking
                \App\Models\SeatBooking::create([
                    'schedule_id' => $request->schedule_id,
                    'departure_date' => $request->departure_date,
                    'seat_number' => $assignment['seatNumber'],
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $assignment['passengerName'],
                    'passenger_type' => $assignment['passengerType'],
                    'status' => 'booked'
                ]);
            }

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
    
    public function export(Request $request)
    {
        $query = Transaction::with(['schedule.destination', 'creator']);
        
        // Apply same filters as index method
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhereHas('schedule.destination', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('departure_location', 'like', "%{$search}%")
                        ->orWhere('destination_location', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        return Excel::download(new TransactionsExport($transactions), 'transactions-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function getFilteredSchedules(Request $request)
    {
        try {
            \Log::info('getFilteredSchedules called', $request->all());
            
            $query = Schedule::with(['destination', 'speedboat'])
                ->where('is_active', true);

            if ($request->filled('speedboat_id')) {
                $query->where('speedboat_id', $request->speedboat_id);
            }

            if ($request->filled('destination_id')) {
                $query->where('destination_id', $request->destination_id);
            }

            $schedules = $query->orderBy('departure_time')->get();
            \Log::info('Found schedules count: ' . $schedules->count());

            // Get selected date (default to today if not provided)
            $selectedDate = $request->get('departure_date', now()->format('Y-m-d'));

            // Calculate available seats for each schedule based on selected date
            $schedules->each(function ($schedule) use ($selectedDate) {
                // Count booked seats for this schedule on the selected date
                $bookedSeats = $schedule->transactions()
                    ->whereDate('departure_date', $selectedDate)
                    ->sum(DB::raw('adult_count + child_count + toddler_count'));
                
                $schedule->available_seats = max(0, $schedule->capacity - $bookedSeats);
                $schedule->booked_seats = $bookedSeats;
                $schedule->selected_date = $selectedDate;
                
                // Add status indicator
                if ($schedule->available_seats == 0) {
                    $schedule->status = 'full';
                } else if ($schedule->available_seats <= 5) {
                    $schedule->status = 'limited';
                } else {
                    $schedule->status = 'available';
                }
            });

            \Log::info('Returning schedules with capacity data');
            return response()->json($schedules);
        } catch (\Exception $e) {
            \Log::error('Error in getFilteredSchedules: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function getSeatMap(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'departure_date' => 'required|date'
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $seatLayout = \App\Models\SeatBooking::generateSeatLayout(
            $request->schedule_id,
            $request->departure_date,
            $schedule->capacity
        );

        return response()->json([
            'schedule' => $schedule,
            'seat_layout' => $seatLayout,
            'capacity' => $schedule->capacity
        ]);
    }
}
