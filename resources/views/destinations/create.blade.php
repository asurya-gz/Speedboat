@extends('layouts.app')

@section('title', 'Tambah Destinasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Destinasi Baru
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('destinations.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Destinasi -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Nama Destinasi
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('name') border-red-500 @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: Pulau Tidung"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Kode Destinasi -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Kode Destinasi
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 uppercase @error('code') border-red-500 @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}"
                               placeholder="Contoh: PTD"
                               maxlength="10"
                               required>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maksimal 10 karakter, akan otomatis kapital</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Harga Dewasa -->
                    <div>
                        <label for="adult_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Harga Dewasa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" 
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('adult_price') border-red-500 @enderror" 
                                   id="adult_price_display" 
                                   value="{{ old('adult_price') }}"
                                   placeholder="50.000"
                                   inputmode="numeric">
                            <input type="hidden" id="adult_price" name="adult_price" value="{{ old('adult_price') }}" required>
                        </div>
                        @error('adult_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Harga Anak -->
                    <div>
                        <label for="child_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-cyan-600 dark:text-cyan-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Harga Anak
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" 
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('child_price') border-red-500 @enderror" 
                                   id="child_price_display" 
                                   value="{{ old('child_price') }}"
                                   placeholder="30.000"
                                   inputmode="numeric">
                            <input type="hidden" id="child_price" name="child_price" value="{{ old('child_price') }}" required>
                        </div>
                        @error('child_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Harga Balita -->
                    <div>
                        <label for="toddler_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-pink-600 dark:text-pink-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Harga Balita
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" 
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('toddler_price') border-red-500 @enderror" 
                                   id="toddler_price_display" 
                                   value="{{ old('toddler_price') }}"
                                   placeholder="15.000"
                                   inputmode="numeric">
                            <input type="hidden" id="toddler_price" name="toddler_price" value="{{ old('toddler_price') }}" required>
                        </div>
                        @error('toddler_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Deskripsi
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('description') border-red-500 @enderror" 
                              id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Deskripsi destinasi (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div>
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300" for="is_active">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Destinasi aktif
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Destinasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto uppercase code field
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Format price inputs with thousand separators
    function formatPrice(input) {
        // Remove all non-digit characters
        let value = input.value.replace(/\D/g, '');
        
        // Add thousand separators (dots) - use manual formatting to ensure consistency
        if (value) {
            // Convert to number and format manually with dots
            let num = parseInt(value);
            value = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        
        input.value = value;
    }

    // Allow only numbers and handle formatting for price inputs
    function handlePriceInput(e) {
        const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End', 'ArrowLeft', 'ArrowRight', 'Clear', 'Copy', 'Paste'];
        
        // Allow special keys
        if (allowedKeys.indexOf(e.key) !== -1) {
            return;
        }
        
        // Prevent if not a number
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
        }
    }

    // Apply to adult price input
    const adultPriceDisplay = document.getElementById('adult_price_display');
    const adultPriceHidden = document.getElementById('adult_price');
    
    adultPriceDisplay.addEventListener('keydown', handlePriceInput);
    adultPriceDisplay.addEventListener('input', function() {
        // Update hidden field with unformatted value
        const rawValue = this.value.replace(/\D/g, '');
        adultPriceHidden.value = rawValue;
        // Format display
        formatPrice(this);
    });

    // Apply to child price input
    const childPriceDisplay = document.getElementById('child_price_display');
    const childPriceHidden = document.getElementById('child_price');
    
    childPriceDisplay.addEventListener('keydown', handlePriceInput);
    childPriceDisplay.addEventListener('input', function() {
        // Update hidden field with unformatted value
        const rawValue = this.value.replace(/\D/g, '');
        childPriceHidden.value = rawValue;
        // Format display
        formatPrice(this);
    });

    // Apply to toddler price input
    const toddlerPriceDisplay = document.getElementById('toddler_price_display');
    const toddlerPriceHidden = document.getElementById('toddler_price');
    
    toddlerPriceDisplay.addEventListener('keydown', handlePriceInput);
    toddlerPriceDisplay.addEventListener('input', function() {
        // Update hidden field with unformatted value
        const rawValue = this.value.replace(/\D/g, '');
        toddlerPriceHidden.value = rawValue;
        // Format display
        formatPrice(this);
    });

    // Format existing values on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (adultPriceDisplay.value) {
            adultPriceHidden.value = adultPriceDisplay.value.replace(/\D/g, '');
            formatPrice(adultPriceDisplay);
        }
        if (childPriceDisplay.value) {
            childPriceHidden.value = childPriceDisplay.value.replace(/\D/g, '');
            formatPrice(childPriceDisplay);
        }
        if (toddlerPriceDisplay.value) {
            toddlerPriceHidden.value = toddlerPriceDisplay.value.replace(/\D/g, '');
            formatPrice(toddlerPriceDisplay);
        }
    });
</script>
@endpush
@endsection