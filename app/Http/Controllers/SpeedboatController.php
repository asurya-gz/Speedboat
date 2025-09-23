<?php

namespace App\Http\Controllers;

use App\Models\Speedboat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpeedboatController extends Controller
{
    public function index()
    {
        $speedboats = Speedboat::latest()->paginate(10);
        return view('speedboats.index', compact('speedboats'));
    }

    public function create()
    {
        return view('speedboats.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:speedboats',
            'capacity' => 'required|integer|min:1',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'capacity', 'type', 'description']);
        $data['is_active'] = $request->has('is_active');

        Speedboat::create($data);
        return redirect()->route('speedboats.index')->with('success', 'Speedboat berhasil ditambahkan');
    }

    public function show(Speedboat $speedboat)
    {
        return view('speedboats.show', compact('speedboat'));
    }

    public function edit(Speedboat $speedboat)
    {
        return view('speedboats.edit', compact('speedboat'));
    }

    public function update(Request $request, Speedboat $speedboat)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:speedboats,code,' . $speedboat->id,
            'capacity' => 'required|integer|min:1',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'capacity', 'type', 'description']);
        $data['is_active'] = $request->has('is_active');

        $speedboat->update($data);
        return redirect()->route('speedboats.index')->with('success', 'Speedboat berhasil diupdate');
    }

    public function destroy(Speedboat $speedboat)
    {
        $speedboat->delete();
        return redirect()->route('speedboats.index')->with('success', 'Speedboat berhasil dihapus');
    }

    public function toggleStatus(Speedboat $speedboat)
    {
        $speedboat->update(['is_active' => !$speedboat->is_active]);

        $status = $speedboat->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('speedboats.index')
            ->with('success', "Speedboat berhasil {$status}.");
    }

    public function generateCode()
    {
        $attempts = 0;
        $maxAttempts = 100;
        
        do {
            $length = rand(3, 6);
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $code = '';
            
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $attempts++;
            $exists = Speedboat::where('code', $code)->exists();
            
        } while ($exists && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            return response()->json(['error' => 'Unable to generate unique code'], 500);
        }
        
        return response()->json(['code' => $code]);
    }
}
