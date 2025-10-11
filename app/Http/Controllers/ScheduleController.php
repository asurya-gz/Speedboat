<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Destination;
use App\Models\Speedboat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with('destination', 'speedboat');
        
        // Handle search parameter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('destination', function ($destQuery) use ($search) {
                      $destQuery->where('departure_location', 'like', '%' . $search . '%')
                               ->orWhere('destination_location', 'like', '%' . $search . '%')
                               ->orWhere('code', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('speedboat', function ($speedQuery) use ($search) {
                      $speedQuery->where('name', 'like', '%' . $search . '%')
                                 ->orWhere('code', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Handle status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Handle time period filter
        if ($request->filled('time_period')) {
            $timePeriod = $request->get('time_period');
            if ($timePeriod === 'morning') {
                $query->whereTime('departure_time', '>=', '06:00')
                      ->whereTime('departure_time', '<=', '11:59');
            } elseif ($timePeriod === 'afternoon') {
                $query->whereTime('departure_time', '>=', '12:00')
                      ->whereTime('departure_time', '<=', '17:59');
            } elseif ($timePeriod === 'evening') {
                $query->whereTime('departure_time', '>=', '18:00')
                      ->whereTime('departure_time', '<=', '23:59');
            }
        }
        
        // Handle destination filter
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->get('destination_id'));
        }
        
        $schedules = $query->orderBy('is_active', 'desc')
                          ->orderBy('departure_time', 'asc')
                          ->paginate(10)
                          ->appends($request->query());
        
        // Get destinations for the filter dropdown
        $destinations = Destination::where('is_active', true)->get();
        
        return view('schedules.index', compact('schedules', 'destinations'));
    }

    public function create()
    {
        $destinations = Destination::where('is_active', true)->get();
        $speedboats = Speedboat::where('is_active', true)->get();
        return view('schedules.create', compact('destinations', 'speedboats'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required|exists:destinations,id',
            'speedboat_id' => 'required|exists:speedboats,id',
            'name' => 'required|string|max:255',
            'departure_time' => 'required',
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1|max:20',
            'columns' => 'required|integer|min:1|max:10',
            'seat_numbers' => 'nullable|json'
        ]);

        // Add custom validation for capacity not exceeding speedboat capacity
        $validator->after(function ($validator) use ($request) {
            if ($request->speedboat_id && $request->capacity) {
                $speedboat = Speedboat::find($request->speedboat_id);
                if ($speedboat && $request->capacity > $speedboat->capacity) {
                    $validator->errors()->add('capacity', 'Kapasitas tidak boleh melebihi kapasitas speedboat (' . $speedboat->capacity . ' penumpang)');
                }
            }

            // Validate capacity is within acceptable range for rows * columns
            if ($request->rows && $request->columns && $request->capacity) {
                $maxCapacity = $request->rows * $request->columns;
                $minCapacity = ($request->rows - 1) * $request->columns + 1;

                if ($request->capacity > $maxCapacity) {
                    $validator->errors()->add('capacity', 'Kapasitas tidak boleh lebih dari ' . $maxCapacity . ' (' . $request->rows . ' baris × ' . $request->columns . ' kolom)');
                } elseif ($request->capacity < $minCapacity && $request->rows > 1) {
                    $validator->errors()->add('capacity', 'Dengan ' . $request->columns . ' kolom, kapasitas ' . $request->capacity . ' hanya memerlukan ' . ceil($request->capacity / $request->columns) . ' baris');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['destination_id', 'speedboat_id', 'name', 'departure_time', 'capacity', 'rows', 'columns']);
        $data['is_active'] = $request->has('is_active');

        // Parse and store seat numbers
        if ($request->filled('seat_numbers')) {
            $seatNumbers = json_decode($request->seat_numbers, true);
            $data['seat_numbers'] = $seatNumbers;
        }

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
        $speedboats = Speedboat::where('is_active', true)->get();
        return view('schedules.edit', compact('schedule', 'destinations', 'speedboats'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required|exists:destinations,id',
            'speedboat_id' => 'required|exists:speedboats,id',
            'name' => 'required|string|max:255',
            'departure_time' => 'required',
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1|max:20',
            'columns' => 'required|integer|min:1|max:10',
            'seat_numbers' => 'nullable|json'
        ]);

        // Add custom validation for capacity not exceeding speedboat capacity
        $validator->after(function ($validator) use ($request) {
            if ($request->speedboat_id && $request->capacity) {
                $speedboat = Speedboat::find($request->speedboat_id);
                if ($speedboat && $request->capacity > $speedboat->capacity) {
                    $validator->errors()->add('capacity', 'Kapasitas tidak boleh melebihi kapasitas speedboat (' . $speedboat->capacity . ' penumpang)');
                }
            }

            // Validate capacity is within acceptable range for rows * columns
            if ($request->rows && $request->columns && $request->capacity) {
                $maxCapacity = $request->rows * $request->columns;
                $minCapacity = ($request->rows - 1) * $request->columns + 1;

                if ($request->capacity > $maxCapacity) {
                    $validator->errors()->add('capacity', 'Kapasitas tidak boleh lebih dari ' . $maxCapacity . ' (' . $request->rows . ' baris × ' . $request->columns . ' kolom)');
                } elseif ($request->capacity < $minCapacity && $request->rows > 1) {
                    $validator->errors()->add('capacity', 'Dengan ' . $request->columns . ' kolom, kapasitas ' . $request->capacity . ' hanya memerlukan ' . ceil($request->capacity / $request->columns) . ' baris');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare update data
        $data = [
            'destination_id' => $request->destination_id,
            'speedboat_id' => $request->speedboat_id,
            'name' => $request->name,
            'departure_time' => $request->departure_time,
            'capacity' => $request->capacity,
            'rows' => $request->rows,
            'columns' => $request->columns,
            'is_active' => $request->has('is_active')
        ];

        // Parse and store seat numbers
        if ($request->filled('seat_numbers')) {
            $seatNumbers = json_decode($request->seat_numbers, true);
            $data['seat_numbers'] = $seatNumbers;
        }

        // Update the schedule
        $schedule->update($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy(Schedule $schedule)
    {
        // Check if schedule has any transactions
        if ($schedule->transactions()->count() > 0) {
            return redirect()->route('schedules.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah memiliki transaksi. Anda dapat menonaktifkan jadwal ini.');
        }

        // Delete the schedule
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus dari database');
    }

    /**
     * Toggle schedule active status.
     */
    public function toggleStatus(Schedule $schedule)
    {
        $schedule->update(['is_active' => !$schedule->is_active]);

        $status = $schedule->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('schedules.index')
            ->with('success', "Jadwal berhasil {$status}.");
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
