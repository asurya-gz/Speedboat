<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('destination')->where('is_active', true)->orderBy('departure_date', 'asc')->orderBy('departure_time', 'asc')->paginate(10);
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $destinations = Destination::where('is_active', true)->get();
        return view('schedules.create', compact('destinations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required|exists:destinations,id',
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required',
            'capacity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['available_seats'] = $request->capacity;
        
        Schedule::create($data);
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load('destination', 'transactions');
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $destinations = Destination::where('is_active', true)->get();
        return view('schedules.edit', compact('schedule', 'destinations'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required|exists:destinations,id',
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'capacity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $schedule->update($request->all());
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->update(['is_active' => false]);
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus');
    }

    public function export()
    {
        $schedules = Schedule::with('destination')->where('is_active', true)->orderBy('departure_date', 'asc')->orderBy('departure_time', 'asc')->get();
        
        $csvData = [];
        $csvData[] = ['Destinasi', 'Kode Destinasi', 'Tanggal Keberangkatan', 'Waktu Keberangkatan', 'Kapasitas', 'Kursi Tersedia', 'Harga Dewasa', 'Harga Anak', 'Status', 'Dibuat Tanggal'];
        
        foreach ($schedules as $schedule) {
            $csvData[] = [
                $schedule->destination->name,
                $schedule->destination->code,
                $schedule->departure_date->format('Y-m-d'),
                $schedule->departure_time->format('H:i'),
                $schedule->capacity,
                $schedule->available_seats,
                $schedule->destination->adult_price,
                $schedule->destination->child_price,
                $schedule->is_active ? 'Aktif' : 'Nonaktif',
                $schedule->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $filename = 'jadwal_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
