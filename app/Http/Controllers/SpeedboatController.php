<?php

namespace App\Http\Controllers;

use App\Models\Speedboat;
use App\Services\WooCommerceProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpeedboatController extends Controller
{
    public function index(Request $request)
    {
        $query = Speedboat::query();
        
        // Handle search parameter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
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
        
        // Handle capacity filter
        if ($request->filled('capacity')) {
            $capacity = $request->get('capacity');
            if ($capacity === 'small') {
                $query->where('capacity', '<=', 20);
            } elseif ($capacity === 'medium') {
                $query->whereBetween('capacity', [21, 50]);
            } elseif ($capacity === 'large') {
                $query->where('capacity', '>=', 51);
            }
        }
        
        $speedboats = $query->latest()->paginate(10)->appends($request->query());
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
            'description' => 'nullable|string',
            'woocommerce_product_id' => 'nullable|integer',
            'woocommerce_bus_id' => 'nullable|string|max:50',
            'auto_create_woocommerce' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'capacity', 'type', 'description', 'woocommerce_product_id', 'woocommerce_bus_id']);
        $data['is_active'] = $request->has('is_active');

        $speedboat = Speedboat::create($data);

        // Auto-create WooCommerce product if requested and not manually mapped
        if ($request->has('auto_create_woocommerce') && !$request->filled('woocommerce_product_id')) {
            $wooCommerceService = new WooCommerceProductService();
            $result = $wooCommerceService->createProduct($speedboat);

            if ($result['success']) {
                // Update speedboat with WooCommerce Product ID
                $speedboat->update([
                    'woocommerce_product_id' => $result['product_id'],
                ]);

                return redirect()->route('speedboats.index')
                    ->with('success', 'Speedboat berhasil ditambahkan dan product WooCommerce telah dibuat (ID: ' . $result['product_id'] . ')');
            } else {
                return redirect()->route('speedboats.index')
                    ->with('warning', 'Speedboat berhasil ditambahkan, tetapi gagal membuat product WooCommerce: ' . $result['message']);
            }
        }

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
            'description' => 'nullable|string',
            'woocommerce_product_id' => 'nullable|integer',
            'woocommerce_bus_id' => 'nullable|string|max:50',
            'auto_sync_woocommerce' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'code', 'capacity', 'type', 'description', 'woocommerce_product_id', 'woocommerce_bus_id']);
        $data['is_active'] = $request->has('is_active');

        $speedboat->update($data);

        // Auto-sync to WooCommerce if requested and product is mapped
        if ($request->has('auto_sync_woocommerce') && $speedboat->woocommerce_product_id) {
            $wooCommerceService = new WooCommerceProductService();
            $result = $wooCommerceService->updateProduct($speedboat);

            if ($result['success']) {
                return redirect()->route('speedboats.index')
                    ->with('success', 'Speedboat berhasil diupdate dan disinkronisasi ke WooCommerce');
            } else {
                return redirect()->route('speedboats.index')
                    ->with('warning', 'Speedboat berhasil diupdate, tetapi gagal sync ke WooCommerce: ' . $result['message']);
            }
        }

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
