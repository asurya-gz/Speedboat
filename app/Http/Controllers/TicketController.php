<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function validateForm()
    {
        return view('tickets.validate');
    }

    public function processValidation(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            $ticketCode = null;
            
            // Try to decode as JSON first (from QR scan)
            $qrData = json_decode($request->qr_data, true);
            
            if ($qrData && isset($qrData['ticket_code'])) {
                // Valid QR JSON format
                $ticketCode = $qrData['ticket_code'];
            } else {
                // Assume it's a direct ticket code (manual input)
                $ticketCode = trim($request->qr_data);
                
                // Validate ticket code format
                if (empty($ticketCode)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode tiket tidak boleh kosong'
                    ], 400);
                }
            }

            // Find ticket by code
            $ticket = Ticket::with(['transaction.schedule.destination'])
                ->where('ticket_code', $ticketCode)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan'
                ], 404);
            }

            // Check if ticket is already validated
            if ($ticket->isValidated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah pernah divalidasi pada ' . $ticket->validated_at->format('d M Y H:i'),
                    'ticket' => $ticket
                ], 400);
            }

            // Check if ticket status is active
            if ($ticket->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tiket: ' . ucfirst($ticket->status)
                ], 400);
            }

            // Check payment status
            if ($ticket->transaction->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket belum dibayar (Status: ' . ucfirst($ticket->transaction->payment_status) . ')'
                ], 400);
            }

            // Validate ticket and mark as used
            DB::beginTransaction();
            try {
                $ticket->update([
                    'validated_at' => now(),
                    'validated_by' => Auth::id(),
                    'boarding_time' => now(),
                    'status' => 'used'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Tiket berhasil divalidasi! Penumpang boleh naik.',
                    'ticket' => $ticket->load(['transaction.schedule.destination'])
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memvalidasi tiket: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memproses QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchByCode(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string'
        ]);

        $ticket = Ticket::with(['transaction.schedule.destination'])
            ->where('ticket_code', 'like', '%' . $request->ticket_code . '%')
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    public function validationHistory(Request $request)
    {
        $query = Ticket::with([
            'transaction.schedule.destination', 
            'validator'
        ])->whereNotNull('validated_at');

        // Search by ticket code
        if ($request->filled('ticket_code')) {
            $query->where('ticket_code', 'LIKE', '%' . $request->ticket_code . '%');
        }

        // Search by passenger name
        if ($request->filled('passenger_name')) {
            $query->where('passenger_name', 'LIKE', '%' . $request->passenger_name . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('validated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('validated_at', '<=', $request->date_to);
        }

        // Filter by destination
        if ($request->filled('destination_id')) {
            $query->whereHas('transaction.schedule', function($q) use ($request) {
                $q->where('destination_id', $request->destination_id);
            });
        }

        // Filter by validator
        if ($request->filled('validator_id')) {
            $query->where('validated_by', $request->validator_id);
        }

        $validatedTickets = $query->orderBy('validated_at', 'desc')
                                 ->paginate(20)
                                 ->appends($request->query());

        // Get destinations for filter dropdown
        $destinations = \App\Models\Destination::where('is_active', true)->get();
        
        // Get validators for filter dropdown
        $validators = \App\Models\User::whereIn('role', ['admin', 'boarding'])
                                    ->orderBy('name')
                                    ->get();

        return view('tickets.validation-history', compact('validatedTickets', 'destinations', 'validators'));
    }

    public function exportValidationHistory(Request $request)
    {
        $query = Ticket::with([
            'transaction.schedule.destination', 
            'validator'
        ])->whereNotNull('validated_at');

        // Apply same filters as validationHistory method
        if ($request->filled('ticket_code')) {
            $query->where('ticket_code', 'LIKE', '%' . $request->ticket_code . '%');
        }

        if ($request->filled('passenger_name')) {
            $query->where('passenger_name', 'LIKE', '%' . $request->passenger_name . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('validated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('validated_at', '<=', $request->date_to);
        }

        if ($request->filled('destination_id')) {
            $query->whereHas('transaction.schedule', function($q) use ($request) {
                $q->where('destination_id', $request->destination_id);
            });
        }

        if ($request->filled('validator_id')) {
            $query->where('validated_by', $request->validator_id);
        }

        $tickets = $query->orderBy('validated_at', 'desc')->get();

        $filename = 'riwayat-validasi-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                'Kode Tiket',
                'Nama Penumpang', 
                'Tipe Penumpang',
                'Tujuan',
                'Tanggal Keberangkatan',
                'Jam Keberangkatan',
                'Waktu Validasi',
                'Divalidasi Oleh',
                'Status'
            ]);

            // CSV Data
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_code,
                    $ticket->passenger_name,
                    ucfirst($ticket->passenger_type),
                    $ticket->transaction->schedule->destination->name,
                    $ticket->transaction->schedule->departure_date,
                    $ticket->transaction->schedule->departure_time,
                    $ticket->validated_at->format('d/m/Y H:i:s'),
                    $ticket->validator->name ?? 'N/A',
                    'Sudah Boarding'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
