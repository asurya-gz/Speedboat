<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::latest()->paginate(10);
        return view('destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('destinations.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:destinations',
            'adult_price' => 'required|numeric|min:0',
            'child_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'adult_price', 'child_price', 'description']);
        $data['is_active'] = $request->has('is_active');

        Destination::create($data);
        return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil ditambahkan');
    }

    public function show(Destination $destination)
    {
        return view('destinations.show', compact('destination'));
    }

    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:destinations,code,' . $destination->id,
            'adult_price' => 'required|numeric|min:0',
            'child_price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'adult_price', 'child_price', 'description']);
        $data['is_active'] = $request->has('is_active');

        $destination->update($data);
        return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil diupdate');
    }

    public function destroy(Destination $destination)
    {
        $destination->update(['is_active' => false]);
        return redirect()->route('destinations.index')->with('success', 'Destinasi berhasil dihapus');
    }

    public function export()
    {
        $destinations = Destination::all();
        
        $csvData = [];
        $csvData[] = ['Kode', 'Nama Destinasi', 'Harga Dewasa', 'Harga Anak', 'Status', 'Deskripsi', 'Dibuat Tanggal'];
        
        foreach ($destinations as $destination) {
            $csvData[] = [
                $destination->code,
                $destination->name,
                $destination->adult_price,
                $destination->child_price,
                $destination->is_active ? 'Aktif' : 'Nonaktif',
                $destination->description ?? '',
                $destination->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $filename = 'destinasi_' . date('Y-m-d_H-i-s') . '.csv';
        
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
