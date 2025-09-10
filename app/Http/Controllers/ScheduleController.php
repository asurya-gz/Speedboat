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
        $schedules = Schedule::with('destination')->where('is_active', true)->orderBy('departure_date', 'asc')->orderBy('departure_time', 'asc')->get();
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
}
