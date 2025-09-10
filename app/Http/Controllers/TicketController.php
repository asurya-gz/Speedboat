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
            // Decode QR data
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData || !isset($qrData['ticket_code'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau rusak'
                ], 400);
            }

            // Find ticket by code
            $ticket = Ticket::with(['transaction.schedule.destination'])
                ->where('ticket_code', $qrData['ticket_code'])
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

            // Validate ticket and mark as boarded
            DB::beginTransaction();
            try {
                $ticket->update([
                    'validated_at' => now(),
                    'validated_by' => Auth::id(),
                    'boarding_time' => now(),
                    'status' => 'boarded'
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
}
