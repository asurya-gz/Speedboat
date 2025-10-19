@extends('layouts.app')

@section('title', 'Tambah Speedboat')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Speedboat Baru
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('speedboats.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Kode Speedboat -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Kode Speedboat
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" 
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 uppercase @error('code') border-red-500 @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}"
                               placeholder="Contoh: SB001"
                               maxlength="10"
                               required>
                        <button type="button" 
                                id="generateCodeBtn"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Generate
                        </button>
                    </div>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maksimal 10 karakter, akan otomatis kapital</p>
                </div>

                <!-- Nama Speedboat -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M8 21l4-7 4 7M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        Nama Speedboat
                    </label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('name') border-red-500 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           placeholder="Contoh: Speedboat Express 1"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kapasitas -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Kapasitas Penumpang
                    </label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('capacity') border-red-500 @enderror" 
                           id="capacity" 
                           name="capacity" 
                           value="{{ old('capacity') }}"
                           placeholder="Contoh: 25"
                           min="1"
                           required>
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah maksimal penumpang yang dapat ditampung</p>
                </div>

                <!-- Tipe Speedboat -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Tipe Speedboat
                    </label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 @error('type') border-red-500 @enderror" 
                            id="type" 
                            name="type">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Fast Boat" {{ old('type') == 'Fast Boat' ? 'selected' : '' }}>Fast Boat</option>
                        <option value="Speed Boat" {{ old('type') == 'Speed Boat' ? 'selected' : '' }}>Speed Boat</option>
                        <option value="Express Boat" {{ old('type') == 'Express Boat' ? 'selected' : '' }}>Express Boat</option>
                        <option value="Ferry" {{ old('type') == 'Ferry' ? 'selected' : '' }}>Ferry</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Opsional - pilih tipe speedboat</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Deskripsi
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('description') border-red-500 @enderror"
                              id="description"
                              name="description"
                              rows="4"
                              placeholder="Contoh: Speedboat modern dengan AC dan fasilitas lengkap">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Opsional - informasi tambahan tentang speedboat</p>
                </div>

                <!-- Divider -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-600">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        WooCommerce Integration (Opsional)
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Pilih salah satu: buat product baru otomatis di WooCommerce, atau mapping ke product yang sudah ada.
                    </p>
                </div>

                <!-- Auto Create WooCommerce Product -->
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <input type="checkbox"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded mt-1"
                               id="auto_create_woocommerce"
                               name="auto_create_woocommerce"
                               value="1"
                               {{ old('auto_create_woocommerce') ? 'checked' : '' }}>
                        <div class="ml-3">
                            <label for="auto_create_woocommerce" class="block text-sm font-medium text-purple-900 dark:text-purple-100">
                                <svg class="w-4 h-4 inline text-purple-600 dark:text-purple-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Auto-Create Product di WooCommerce
                            </label>
                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                                Centang ini untuk otomatis membuat product baru di WooCommerce berdasarkan data speedboat ini. Product ID akan otomatis ter-mapping setelah dibuat.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Manual Mapping Section -->
                <div id="manual-mapping-section" class="space-y-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 -mt-2">
                        Atau isi field di bawah untuk mapping manual ke product yang sudah ada:
                    </p>

                <!-- WooCommerce Product ID -->
                <div>
                    <label for="woocommerce_product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-purple-600 dark:text-purple-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        WooCommerce Product ID
                    </label>
                    <input type="number"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('woocommerce_product_id') border-red-500 @enderror"
                           id="woocommerce_product_id"
                           name="woocommerce_product_id"
                           value="{{ old('woocommerce_product_id') }}"
                           placeholder="Contoh: 5964"
                           min="1">
                    @error('woocommerce_product_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        ID Product di WooCommerce (cek di:
                        <a href="https://naikspeed.com/wp-admin/edit.php?post_type=product" target="_blank" class="text-purple-600 dark:text-purple-400 hover:underline">
                            WooCommerce Products
                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </p>
                </div>

                <!-- WooCommerce Bus ID -->
                <div>
                    <label for="woocommerce_bus_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-purple-600 dark:text-purple-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M8 21l4-7 4 7M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        WooCommerce Bus ID
                    </label>
                    <input type="text"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('woocommerce_bus_id') border-red-500 @enderror"
                           id="woocommerce_bus_id"
                           name="woocommerce_bus_id"
                           value="{{ old('woocommerce_bus_id') }}"
                           placeholder="Contoh: 64"
                           maxlength="50">
                    @error('woocommerce_bus_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bus ID dari WooCommerce Bus Booking plugin</p>
                </div>
                </div>
                <!-- End Manual Mapping Section -->

                <!-- Status Aktif -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded @error('is_active') border-red-500 @enderror" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Speedboat Aktif
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Speedboat yang aktif dapat digunakan untuk penjadwalan</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <a href="{{ route('speedboats.index') }}" 
                       class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Speedboat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto uppercase for code input
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Generate code functionality
    const generateBtn = document.getElementById('generateCodeBtn');
    generateBtn.addEventListener('click', async function() {
        const originalText = this.innerHTML;

        // Show loading state
        this.innerHTML = `
            <svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        `;
        this.disabled = true;

        try {
            const response = await fetch('{{ route("speedboats.generate-code") }}');
            const data = await response.json();

            if (response.ok && data.code) {
                codeInput.value = data.code;
                codeInput.focus();
            } else {
                alert('Gagal generate kode: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat generate kode');
        } finally {
            // Reset button state
            this.innerHTML = originalText;
            this.disabled = false;
        }
    });

    // Auto-create WooCommerce toggle functionality
    const autoCreateCheckbox = document.getElementById('auto_create_woocommerce');
    const manualMappingSection = document.getElementById('manual-mapping-section');
    const productIdInput = document.getElementById('woocommerce_product_id');
    const busIdInput = document.getElementById('woocommerce_bus_id');

    function toggleManualMapping() {
        if (autoCreateCheckbox.checked) {
            manualMappingSection.style.opacity = '0.5';
            productIdInput.disabled = true;
            busIdInput.disabled = true;
            productIdInput.value = '';
            busIdInput.value = '';
        } else {
            manualMappingSection.style.opacity = '1';
            productIdInput.disabled = false;
            busIdInput.disabled = false;
        }
    }

    autoCreateCheckbox.addEventListener('change', toggleManualMapping);
    toggleManualMapping(); // Initialize on page load
});
</script>

<style>
/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection