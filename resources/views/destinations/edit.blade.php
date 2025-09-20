@extends('layouts.app')

@section('title', 'Edit Destinasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Destinasi: {{ $destination->name }}
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('destinations.update', $destination) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Kode Destinasi -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Kode Destinasi
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" 
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 uppercase @error('code') border-red-500 @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $destination->code) }}"
                               placeholder="Contoh: PTD"
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tempat Awal (Keberangkatan) -->
                    <div>
                        <label for="departure_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Tempat Awal (Keberangkatan)
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('departure_location') border-red-500 @enderror" 
                               id="departure_location" 
                               name="departure_location" 
                               value="{{ old('departure_location', $destination->departure_location) }}"
                               placeholder="Contoh: Marina Ancol"
                               required>
                        @error('departure_location')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Destinasi Tujuan -->
                    <div>
                        <label for="destination_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-red-600 dark:text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Destinasi Tujuan
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('destination_location') border-red-500 @enderror" 
                               id="destination_location" 
                               name="destination_location" 
                               value="{{ old('destination_location', $destination->destination_location) }}"
                               placeholder="Contoh: Pulau Tidung"
                               required>
                        @error('destination_location')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                   value="{{ old('adult_price', $destination->adult_price) }}"
                                   placeholder="50.000"
                                   inputmode="numeric">
                            <input type="hidden" id="adult_price" name="adult_price" value="{{ old('adult_price', $destination->adult_price) }}" required>
                        </div>
                        @error('adult_price')
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
                                   value="{{ old('toddler_price', $destination->toddler_price ?? '') }}"
                                   placeholder="15.000"
                                   inputmode="numeric">
                            <input type="hidden" id="toddler_price" name="toddler_price" value="{{ old('toddler_price', $destination->toddler_price ?? '') }}" required>
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
                              placeholder="Deskripsi destinasi (opsional)">{{ old('description', $destination->description) }}</textarea>
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
                               {{ old('is_active', $destination->is_active) ? 'checked' : '' }}>
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
                    <button type="submit" id="submitBtn" class="btn btn-warning" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update Destinasi
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
    

    // Apply to toddler price input
    const toddlerPriceDisplay = document.getElementById('toddler_price_display');
    const toddlerPriceHidden = document.getElementById('toddler_price');
    
    toddlerPriceDisplay.addEventListener('keydown', handlePriceInput);

    // Form validation function
    function validateForm() {
        const code = document.getElementById('code').value.trim();
        const departureLocation = document.getElementById('departure_location').value.trim();
        const destinationLocation = document.getElementById('destination_location').value.trim();
        const adultPrice = document.getElementById('adult_price').value;
        const toddlerPrice = document.getElementById('toddler_price').value;
        const submitBtn = document.getElementById('submitBtn');
        
        // Check if all required fields are filled
        const isValid = code !== '' && departureLocation !== '' && destinationLocation !== '' && adultPrice !== '' && toddlerPrice !== '';
        
        if (isValid) {
            // Enable button and make it orange (warning color)
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
            submitBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
        } else {
            // Disable button and make it gray
            submitBtn.disabled = true;
            submitBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
        }
    }

    // Add event listeners to all required fields
    document.getElementById('code').addEventListener('input', validateForm);
    document.getElementById('departure_location').addEventListener('input', validateForm);
    document.getElementById('destination_location').addEventListener('input', validateForm);
    adultPriceDisplay.addEventListener('input', function() {
        // Update hidden field with unformatted value
        const rawValue = this.value.replace(/\D/g, '');
        adultPriceHidden.value = rawValue;
        // Format display
        formatPrice(this);
        // Validate form
        validateForm();
    });
    
    toddlerPriceDisplay.addEventListener('input', function() {
        // Update hidden field with unformatted value
        const rawValue = this.value.replace(/\D/g, '');
        toddlerPriceHidden.value = rawValue;
        // Format display
        formatPrice(this);
        // Validate form
        validateForm();
    });

    // Format existing values on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (adultPriceDisplay.value) {
            // Convert decimal to integer (remove .00 if present)
            const rawValue = Math.floor(parseFloat(adultPriceDisplay.value) || 0).toString();
            adultPriceHidden.value = rawValue;
            adultPriceDisplay.value = rawValue;
            formatPrice(adultPriceDisplay);
        }
        if (toddlerPriceDisplay.value) {
            // Convert decimal to integer (remove .00 if present)
            const rawValue = Math.floor(parseFloat(toddlerPriceDisplay.value) || 0).toString();
            toddlerPriceHidden.value = rawValue;
            toddlerPriceDisplay.value = rawValue;
            formatPrice(toddlerPriceDisplay);
        }
        
        // Initial validation on page load
        validateForm();

        // Generate code functionality
        document.getElementById('generateCodeBtn').addEventListener('click', function() {
            const button = this;
            const codeInput = document.getElementById('code');
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = `
                <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generate...
            `;
            
            // Make API call to generate code
            fetch('{{ route("destinations.generate-code") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.code) {
                        codeInput.value = data.code.toUpperCase();
                        // Trigger validation
                        validateForm();
                    } else {
                        alert('Gagal generate kode. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat generate kode.');
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Generate
                    `;
                });
        });
    });
</script>
@endpush
@endsection