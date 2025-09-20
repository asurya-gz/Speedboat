@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Jadwal: {{ $schedule->destination->name }}
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('schedules.update', $schedule) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Destinasi -->
                <div>
                    <label for="destination_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Destinasi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('destination_id') border-red-500 @enderror" 
                               id="destination_search" 
                               placeholder="Ketik untuk mencari destinasi..."
                               autocomplete="off">
                        <input type="hidden" id="destination_id" name="destination_id" value="{{ old('destination_id', $schedule->destination_id) }}" required>
                        
                        <!-- Dropdown hasil pencarian -->
                        <div id="destination_dropdown" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <!-- Hasil akan dimuat di sini -->
                        </div>
                    </div>
                    @error('destination_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Keberangkatan -->
                    <div>
                        <label for="departure_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Tanggal Keberangkatan
                        </label>
                        <input type="date" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 @error('departure_date') border-red-500 @enderror" 
                               id="departure_date" 
                               name="departure_date" 
                               value="{{ old('departure_date', $schedule->departure_date->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               required>
                        @error('departure_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Waktu Keberangkatan -->
                    <div>
                        <label for="departure_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Waktu Keberangkatan
                        </label>
                        <input type="time" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 @error('departure_time') border-red-500 @enderror" 
                               id="departure_time" 
                               name="departure_time" 
                               value="{{ old('departure_time', $schedule->departure_time->format('H:i')) }}"
                               required>
                        @error('departure_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kapasitas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Ubah Kapasitas Penumpang
                    </label>
                    
                    <!-- Current capacity info -->
                    <div class="mb-4 p-3 bg-blue-50 dark:bg-gray-800 border border-blue-200 dark:border-gray-600 rounded-lg">
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <span class="font-medium">Kapasitas saat ini:</span>
                                    <div class="text-lg font-bold text-blue-800 dark:text-white">{{ $schedule->capacity }}</div>
                                </div>
                                <div>
                                    <span class="font-medium">Tersedia:</span>
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ $schedule->available_seats }}</div>
                                </div>
                                <div>
                                    <span class="font-medium">Terjual:</span>
                                    <div class="text-lg font-bold text-red-600 dark:text-red-400">{{ $schedule->capacity - $schedule->available_seats }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Capacity adjustment options -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Pilih Aksi:</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="capacity-option flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="capacity_action" value="add" class="mr-3" onchange="toggleCapacityInput()">
                                    <div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300">Tambah Kapasitas</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Menambah kursi baru</div>
                                    </div>
                                </label>
                                
                                <label class="capacity-option flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="capacity_action" value="reduce" class="mr-3" onchange="toggleCapacityInput()">
                                    <div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300">Kurangi Kapasitas</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Mengurangi kursi tersedia</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div id="capacityInputSection" class="hidden">
                            <label for="capacity_change" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Jumlah kursi: <span id="actionText"></span>
                            </label>
                            <input type="number" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400" 
                                   id="capacity_change" 
                                   name="capacity_change" 
                                   placeholder="Masukkan jumlah kursi"
                                   min="1"
                                   onchange="previewCapacityChange()">
                            
                            <!-- Preview hasil perubahan -->
                            <div id="capacityPreview" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg hidden">
                                <div class="text-sm text-yellow-800 dark:text-yellow-300">
                                    <div class="font-medium mb-2">Preview setelah perubahan:</div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <span>Kapasitas total:</span>
                                            <div class="font-bold" id="previewTotal">-</div>
                                        </div>
                                        <div>
                                            <span>Tersedia:</span>
                                            <div class="font-bold text-green-600 dark:text-green-400" id="previewAvailable">-</div>
                                        </div>
                                        <div>
                                            <span>Terjual:</span>
                                            <div class="font-bold text-red-600 dark:text-red-400" id="previewSold">{{ $schedule->capacity - $schedule->available_seats }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="capacityWarning" class="mt-3 p-3 bg-red-50 dark:bg-red-900/50 rounded-lg hidden">
                                <div class="text-sm text-red-800 dark:text-red-300">
                                    <div class="font-medium">⚠️ Peringatan!</div>
                                    <div id="warningText"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @error('capacity_action')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('capacity_change')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
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
                               {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                        <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300" for="is_active">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Jadwal aktif
                        </label>
                    </div>
                </div>

                <!-- Current Status Info -->
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status Saat Ini
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-900 dark:text-white">Kapasitas:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $schedule->capacity }} orang</span>
                        </div>
                        <div>
                            <span class="text-gray-900 dark:text-white">Tersedia:</span>
                            <span class="font-medium text-green-800 dark:text-green-300 ml-1">{{ $schedule->available_seats }} kursi</span>
                        </div>
                        <div>
                            <span class="text-gray-900 dark:text-white">Terbooked:</span>
                            <span class="font-medium text-red-800 dark:text-red-300 ml-1">{{ $schedule->capacity - $schedule->available_seats }} kursi</span>
                        </div>
                    </div>
                </div>

                <!-- Preview harga -->
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4" id="price-preview">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Harga Tiket
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Dewasa</div>
                                <div class="text-sm text-green-600 dark:text-green-400 font-semibold" id="adult-price">Rp {{ number_format($schedule->destination->adult_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Anak</div>
                                <div class="text-sm text-cyan-600 dark:text-cyan-400 font-semibold" id="child-price">Rp {{ number_format($schedule->destination->child_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" id="submitBtn" class="btn btn-warning" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Capacity option styling */
.capacity-option {
    transition: all 0.2s ease-in-out;
    border: 2px solid #e5e7eb;
}

html.dark .capacity-option {
    border: 2px solid #4b5563;
}

.capacity-option:hover {
    border-color: #bfdbfe !important;
    background-color: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

html.dark .capacity-option:hover {
    border-color: #3b82f6 !important;
    background-color: #1f2937 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.capacity-option.selected {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
    box-shadow: 0 0 0 1px #3b82f6, 0 4px 6px rgba(59, 130, 246, 0.1);
}

html.dark .capacity-option.selected {
    background-color: #1e3a8a !important;
    box-shadow: 0 0 0 1px #3b82f6, 0 4px 6px rgba(59, 130, 246, 0.3);
}

.capacity-option {
    position: relative;
}

.capacity-option.selected::before {
    content: '';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background-color: #3b82f6;
    border-radius: 50%;
    z-index: 1;
}

/* Radio buttons styling */
input[type="radio"] {
    accent-color: #3b82f6;
    width: 18px;
    height: 18px;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
    const destinations = [
        @foreach($destinations as $destination)
        {
            id: {{ $destination->id }},
            name: @json($destination->name),
            code: @json($destination->code),
            adult_price: {{ $destination->adult_price }},
            child_price: {{ $destination->child_price }},
            display: @json($destination->code . ' - ' . $destination->name)
        },
        @endforeach
    ];

    const searchInput = document.getElementById('destination_search');
    const hiddenInput = document.getElementById('destination_id');
    const dropdown = document.getElementById('destination_dropdown');
    const pricePreview = document.getElementById('price-preview');
    
    let selectedDestination = null;

    // Set initial value if editing
    const initialId = hiddenInput.value;
    if (initialId) {
        const initial = destinations.find(d => d.id == initialId);
        if (initial) {
            searchInput.value = initial.display;
            selectedDestination = initial;
            updatePricePreview(initial);
        }
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        if (query.length < 1) {
            dropdown.classList.add('hidden');
            hiddenInput.value = '';
            selectedDestination = null;
            validateForm();
            return;
        }

        const filteredDestinations = destinations.filter(destination => 
            destination.name.toLowerCase().includes(query) || 
            destination.code.toLowerCase().includes(query) ||
            destination.display.toLowerCase().includes(query)
        );

        renderDropdown(filteredDestinations);
        validateForm();
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 1) {
            const query = this.value.toLowerCase();
            const filteredDestinations = destinations.filter(destination => 
                destination.name.toLowerCase().includes(query) || 
                destination.code.toLowerCase().includes(query) ||
                destination.display.toLowerCase().includes(query)
            );
            renderDropdown(filteredDestinations);
        }
    });

    searchInput.addEventListener('blur', function() {
        // Delay hiding to allow click on dropdown
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 200);
    });

    function renderDropdown(filteredDestinations) {
        if (filteredDestinations.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Tidak ada destinasi ditemukan</div>';
        } else {
            dropdown.innerHTML = filteredDestinations.map(destination => `
                <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0" 
                     onclick="selectDestination(${destination.id}, '${destination.display}', ${destination.adult_price}, ${destination.child_price})">
                    <div class="flex items-center">
                        <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mr-2">
                            ${destination.code}
                        </span>
                        <span class="text-gray-900 dark:text-white">${destination.name}</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Dewasa: Rp ${new Intl.NumberFormat('id-ID').format(destination.adult_price)} | 
                        Anak: Rp ${new Intl.NumberFormat('id-ID').format(destination.child_price)}
                    </div>
                </div>
            `).join('');
        }
        dropdown.classList.remove('hidden');
    }

    function selectDestination(id, display, adultPrice, childPrice) {
        searchInput.value = display;
        hiddenInput.value = id;
        selectedDestination = { id, display, adult_price: adultPrice, child_price: childPrice };
        dropdown.classList.add('hidden');
        
        updatePricePreview({ adult_price: adultPrice, child_price: childPrice });
        validateForm();
    }

    function updatePricePreview(destination) {
        if (destination) {
            document.getElementById('adult-price').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(destination.adult_price);
            document.getElementById('child-price').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(destination.child_price);
        }
    }

    // Constants for capacity calculation
    const currentCapacity = {{ $schedule->capacity }};
    const soldTickets = {{ $schedule->capacity - $schedule->available_seats }};
    const availableSeats = {{ $schedule->available_seats }};
    
    // Toggle capacity input section
    function toggleCapacityInput() {
        const capacityAction = document.querySelector('input[name="capacity_action"]:checked');
        const inputSection = document.getElementById('capacityInputSection');
        const actionText = document.getElementById('actionText');
        const capacityInput = document.getElementById('capacity_change');
        
        if (capacityAction) {
            inputSection.classList.remove('hidden');
            capacityInput.value = '';
            document.getElementById('capacityPreview').classList.add('hidden');
            document.getElementById('capacityWarning').classList.add('hidden');
            
            if (capacityAction.value === 'add') {
                actionText.textContent = 'yang akan ditambahkan';
                capacityInput.setAttribute('max', '500'); // Reasonable max limit
            } else {
                actionText.textContent = 'yang akan dikurangi';
                capacityInput.setAttribute('max', availableSeats.toString());
            }
        } else {
            inputSection.classList.add('hidden');
        }
        
        validateForm();
    }
    
    // Preview capacity changes
    function previewCapacityChange() {
        const capacityAction = document.querySelector('input[name="capacity_action"]:checked');
        const changeAmount = parseInt(document.getElementById('capacity_change').value) || 0;
        const preview = document.getElementById('capacityPreview');
        const warning = document.getElementById('capacityWarning');
        const warningText = document.getElementById('warningText');
        
        if (!capacityAction || changeAmount <= 0) {
            preview.classList.add('hidden');
            warning.classList.add('hidden');
            validateForm();
            return;
        }
        
        let newCapacity, newAvailable;
        let showWarning = false;
        let warningMessage = '';
        
        if (capacityAction.value === 'add') {
            newCapacity = currentCapacity + changeAmount;
            newAvailable = availableSeats + changeAmount;
        } else {
            newCapacity = currentCapacity - changeAmount;
            newAvailable = availableSeats - changeAmount;
            
            // Check if reduction would cause issues
            if (changeAmount > availableSeats) {
                showWarning = true;
                warningMessage = `Tidak dapat mengurangi ${changeAmount} kursi karena hanya ${availableSeats} kursi yang tersedia. Maksimal pengurangan adalah ${availableSeats} kursi.`;
            } else if (newCapacity < soldTickets) {
                showWarning = true;
                warningMessage = `Pengurangan ini akan membuat kapasitas (${newCapacity}) lebih kecil dari tiket yang sudah terjual (${soldTickets}).`;
            }
        }
        
        // Show preview
        document.getElementById('previewTotal').textContent = newCapacity;
        document.getElementById('previewAvailable').textContent = Math.max(0, newAvailable);
        preview.classList.remove('hidden');
        
        // Show/hide warning
        if (showWarning) {
            warningText.textContent = warningMessage;
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
        
        validateForm();
    }

    // Form validation function
    function validateForm() {
        const destinationId = document.getElementById('destination_id').value;
        const departureDate = document.getElementById('departure_date').value;
        const departureTime = document.getElementById('departure_time').value;
        const capacityAction = document.querySelector('input[name="capacity_action"]:checked');
        const capacityChange = document.getElementById('capacity_change').value;
        const submitBtn = document.getElementById('submitBtn');
        
        // Check if all required fields are filled
        let isValid = destinationId !== '' && 
                     departureDate !== '' && 
                     departureTime !== '';
        
        // Check capacity fields if capacity action is selected
        if (capacityAction) {
            isValid = isValid && capacityChange !== '' && parseInt(capacityChange) > 0;
            
            // Additional validation for reduce action
            if (capacityAction.value === 'reduce') {
                const changeAmount = parseInt(capacityChange) || 0;
                isValid = isValid && changeAmount <= availableSeats && changeAmount > 0;
            }
        } else {
            // If no capacity action is selected, form is still valid for other updates
            isValid = isValid;
        }
        
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

    // Add event listeners for form validation
    document.getElementById('departure_date').addEventListener('input', validateForm);
    document.getElementById('departure_time').addEventListener('input', validateForm);
    document.getElementById('capacity_change').addEventListener('input', function() {
        previewCapacityChange();
        validateForm();
    });
    
    // Add event listeners for capacity action radio buttons
    document.querySelectorAll('input[name="capacity_action"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleCapacityInput();
            updateCapacityOptionSelection();
        });
    });
    
    // Update visual selection for capacity options
    function updateCapacityOptionSelection() {
        const allOptions = document.querySelectorAll('.capacity-option');
        const selectedRadio = document.querySelector('input[name="capacity_action"]:checked');
        
        // Remove selected class from all options
        allOptions.forEach(option => {
            option.classList.remove('selected');
        });
        
        // Add selected class to the chosen option
        if (selectedRadio) {
            const selectedOption = selectedRadio.closest('.capacity-option');
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }
    }
    
    // Initial form validation on page load
    document.addEventListener('DOMContentLoaded', function() {
        validateForm();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection