@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Jadwal Keberangkatan
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('schedules.store') }}" method="POST" class="space-y-6">
                @csrf
                
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
                        <input type="hidden" id="destination_id" name="destination_id" value="{{ old('destination_id') }}" required>
                        
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
                    <!-- Speedboat -->
                    <div>
                        <label for="speedboat_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Speedboat
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('speedboat_id') border-red-500 @enderror" 
                                   id="speedboat_search" 
                                   placeholder="Ketik untuk mencari speedboat..."
                                   autocomplete="off">
                            <input type="hidden" id="speedboat_id" name="speedboat_id" value="{{ old('speedboat_id') }}" required>
                            
                            <!-- Dropdown hasil pencarian -->
                            <div id="speedboat_dropdown" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                                <!-- Hasil akan dimuat di sini -->
                            </div>
                        </div>
                        @error('speedboat_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <input type="hidden" id="name" name="name" value="">
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
                               value="{{ old('departure_time') }}"
                               required>
                        @error('departure_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Konfigurasi Layout Kursi -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                        </svg>
                        Layout Kursi
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="columns" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kursi Menyamping (Kolom)
                            </label>
                            <input type="number"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700"
                                   id="columns"
                                   name="columns"
                                   value="{{ old('columns', 5) }}"
                                   min="1"
                                   max="10"
                                   oninput="calculateTotalSeatsAndGenerateLayout()"
                                   required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total kursi per baris</p>
                        </div>

                        <div>
                            <label for="rows" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kursi Kebelakang (Baris)
                            </label>
                            <input type="number"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700"
                                   id="rows"
                                   name="rows"
                                   value="{{ old('rows', 5) }}"
                                   min="1"
                                   max="20"
                                   oninput="calculateTotalSeatsAndGenerateLayout()"
                                   required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah baris kursi</p>
                        </div>

                        <div>
                            <label for="left_columns" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kursi Kiri (Lorong)
                            </label>
                            <input type="number"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700"
                                   id="left_columns"
                                   name="left_columns"
                                   value="{{ old('left_columns', 2) }}"
                                   min="0"
                                   max="10"
                                   oninput="calculateTotalSeatsAndGenerateLayout()"
                                   required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kursi di sisi kiri lorong</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-600 rounded px-3 py-2 text-sm text-gray-700 dark:text-gray-300 mb-4">
                        <strong>Total Layout Kursi:</strong> <span id="total_seats_display">-</span> kursi
                        <span id="seat_capacity_info" class="ml-2"></span>
                    </div>

                    <!-- Preview Layout Kursi -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview Layout Kursi
                        </h5>
                        <div id="seatLayoutPreview" class="text-center">
                            <!-- Preview akan di-generate di sini -->
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <p>💡 Klik nomor kursi untuk mengubahnya. Nomor default: A1, A2, B1, B2, dst.</p>
                        </div>
                    </div>

                    <!-- Hidden input untuk menyimpan seat numbers -->
                    <input type="hidden" id="seat_numbers" name="seat_numbers" value="">
                </div>

                <!-- Modal Edit Seat Number -->
                <div id="editSeatModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Nomor Kursi</h3>
                        </div>
                        <div class="p-6">
                            <label for="seatNumberInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nomor Kursi Baru
                            </label>
                            <input type="text"
                                   id="seatNumberInput"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700"
                                   placeholder="Contoh: A1, B2, VIP1">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Masukkan nomor kursi yang unik</p>
                            <p id="seatErrorMsg" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end space-x-3">
                            <button type="button"
                                    onclick="closeSeatModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500">
                                Batal
                            </button>
                            <button type="button"
                                    onclick="saveSeatNumber()"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kapasitas -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Kapasitas Penumpang
                    </label>
                    
                    <!-- Checkbox untuk menggunakan kapasitas maksimal speedboat -->
                    <div class="mb-3" id="maxCapacityOption" style="display: none;">
                        <div class="flex items-center">
                            <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" 
                                   type="checkbox" 
                                   id="use_max_capacity" 
                                   name="use_max_capacity">
                            <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300" for="use_max_capacity">
                                <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Gunakan kapasitas maksimal speedboat (<span id="speedboat-max-capacity">-</span> penumpang)
                            </label>
                        </div>
                    </div>

                    <input type="number"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('capacity') border-red-500 @enderror"
                           id="capacity"
                           name="capacity"
                           value="{{ old('capacity', 0) }}"
                           placeholder="0"
                           min="1"
                           max=""
                           onchange="calculateRowsAndGenerateLayout()"
                           required>
                    
                    <!-- Info kapasitas speedboat -->
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 hidden" id="capacity-info">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Kapasitas maksimal speedboat: <span id="max-capacity-display">-</span> penumpang
                        </div>
                    </div>
                    
                    <!-- Error message untuk kapasitas melebihi maksimal -->
                    <div class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" id="capacity-error">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Kapasitas tidak boleh melebihi kapasitas maksimal speedboat
                    </div>
                    
                    @error('capacity')
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
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300" for="is_active">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Jadwal aktif
                        </label>
                    </div>
                </div>

                <!-- Preview harga -->
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hidden" id="price-preview">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Preview Harga Tiket
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Dewasa</div>
                                <div class="text-sm text-green-600 dark:text-green-400 font-semibold" id="adult-price">-</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Balita</div>
                                <div class="text-sm text-pink-600 dark:text-pink-400 font-semibold" id="toddler-price">-</div>
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
                    <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const destinations = [
        @foreach($destinations as $destination)
        {
            id: {{ $destination->id }},
            code: @json($destination->code),
            departure_location: @json($destination->departure_location),
            destination_location: @json($destination->destination_location),
            adult_price: {{ $destination->adult_price }},
            toddler_price: {{ $destination->toddler_price ?? 0 }},
            display: @json($destination->code . ' - ' . $destination->departure_location . ' → ' . $destination->destination_location)
        },
        @endforeach
    ];

    const speedboats = [
        @foreach($speedboats as $speedboat)
        {
            id: {{ $speedboat->id }},
            code: @json($speedboat->code),
            name: @json($speedboat->name),
            type: @json($speedboat->type),
            capacity: {{ $speedboat->capacity }},
            display: @json($speedboat->name . ' (' . $speedboat->code . ') - ' . $speedboat->type . ' - Kapasitas: ' . $speedboat->capacity)
        },
        @endforeach
    ];

// Global variables for dropdown functionality
let searchInput, hiddenInput, dropdown, pricePreview;
let speedboatSearchInput, speedboatHiddenInput, speedboatDropdown, nameHiddenInput;
let selectedDestination = null;
let selectedSpeedboat = null;

document.addEventListener('DOMContentLoaded', function() {
    searchInput = document.getElementById('destination_search');
    hiddenInput = document.getElementById('destination_id');
    dropdown = document.getElementById('destination_dropdown');
    pricePreview = document.getElementById('price-preview');

    speedboatSearchInput = document.getElementById('speedboat_search');
    speedboatHiddenInput = document.getElementById('speedboat_id');
    speedboatDropdown = document.getElementById('speedboat_dropdown');
    nameHiddenInput = document.getElementById('name');

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

    // Set initial speedboat value if editing
    const initialSpeedboatId = speedboatHiddenInput.value;
    if (initialSpeedboatId) {
        const initialSpeedboat = speedboats.find(s => s.id == initialSpeedboatId);
        if (initialSpeedboat) {
            speedboatSearchInput.value = initialSpeedboat.display;
            selectedSpeedboat = initialSpeedboat;
            nameHiddenInput.value = initialSpeedboat.name;
            updateCapacityConstraints(initialSpeedboat);
        }
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        if (query.length < 1) {
            dropdown.classList.add('hidden');
            hiddenInput.value = '';
            selectedDestination = null;
            pricePreview.classList.add('hidden');
            validateForm();
            return;
        }

        const filteredDestinations = destinations.filter(destination => 
            destination.departure_location.toLowerCase().includes(query) || 
            destination.destination_location.toLowerCase().includes(query) ||
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
                destination.departure_location.toLowerCase().includes(query) || 
                destination.destination_location.toLowerCase().includes(query) ||
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

    // Speedboat search functionality
    speedboatSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        if (query.length < 1) {
            speedboatDropdown.classList.add('hidden');
            speedboatHiddenInput.value = '';
            selectedSpeedboat = null;
            nameHiddenInput.value = '';
            validateForm();
            return;
        }

        const filteredSpeedboats = speedboats.filter(speedboat => 
            speedboat.name.toLowerCase().includes(query) || 
            speedboat.code.toLowerCase().includes(query) ||
            speedboat.type.toLowerCase().includes(query) ||
            speedboat.display.toLowerCase().includes(query)
        );

        renderSpeedboatDropdown(filteredSpeedboats);
        validateForm();
    });

    speedboatSearchInput.addEventListener('focus', function() {
        if (this.value.length >= 1) {
            const query = this.value.toLowerCase();
            const filteredSpeedboats = speedboats.filter(speedboat => 
                speedboat.name.toLowerCase().includes(query) || 
                speedboat.code.toLowerCase().includes(query) ||
                speedboat.type.toLowerCase().includes(query) ||
                speedboat.display.toLowerCase().includes(query)
            );
            renderSpeedboatDropdown(filteredSpeedboats);
        }
    });

    speedboatSearchInput.addEventListener('blur', function() {
        // Delay hiding to allow click on dropdown
        setTimeout(() => {
            speedboatDropdown.classList.add('hidden');
        }, 200);
    });

    function renderSpeedboatDropdown(filteredSpeedboats) {
        if (filteredSpeedboats.length === 0) {
            speedboatDropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Tidak ada speedboat ditemukan</div>';
        } else {
            speedboatDropdown.innerHTML = filteredSpeedboats.map(speedboat => `
                <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0" 
                     onclick="selectSpeedboat(${speedboat.id}, '${speedboat.display}', '${speedboat.name}')">
                    <div class="flex items-center">
                        <span class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-2 py-1 rounded text-xs font-medium mr-2">
                            ${speedboat.code}
                        </span>
                        <span class="text-gray-900 dark:text-white font-medium">${speedboat.name}</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        ${speedboat.type} | Kapasitas: ${speedboat.capacity} penumpang
                    </div>
                </div>
            `).join('');
        }
        speedboatDropdown.classList.remove('hidden');
    }

    function selectSpeedboat(id, display, name) {
        speedboatSearchInput.value = display;
        speedboatHiddenInput.value = id;
        const speedboat = speedboats.find(s => s.id == id);
        selectedSpeedboat = speedboat;
        speedboatDropdown.classList.add('hidden');
        nameHiddenInput.value = name;
        
        // Update capacity constraints based on selected speedboat
        updateCapacityConstraints(speedboat);
        
        validateForm();
    }

    function renderDropdown(filteredDestinations) {
        if (filteredDestinations.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Tidak ada destinasi ditemukan</div>';
        } else {
            dropdown.innerHTML = filteredDestinations.map(destination => `
                <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0" 
                     onclick="selectDestination(${destination.id}, '${destination.display}', ${destination.adult_price}, ${destination.toddler_price})">
                    <div class="flex items-center">
                        <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mr-2">
                            ${destination.code}
                        </span>
                        <span class="text-gray-900 dark:text-white">${destination.departure_location} → ${destination.destination_location}</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Dewasa: Rp ${new Intl.NumberFormat('id-ID').format(destination.adult_price)} | 
                        Balita: Rp ${new Intl.NumberFormat('id-ID').format(destination.toddler_price)}
                    </div>
                </div>
            `).join('');
        }
        dropdown.classList.remove('hidden');
    }

    // Add event listeners for form validation
    document.getElementById('departure_time').addEventListener('input', validateForm);
    document.getElementById('capacity').addEventListener('input', function() {
        validateCapacity();
        validateForm();
        calculateTotalSeatsAndGenerateLayout();
    });

    // Add event listener for max capacity checkbox
    document.getElementById('use_max_capacity').addEventListener('change', function() {
        const capacityInput = document.getElementById('capacity');

        if (this.checked && selectedSpeedboat) {
            capacityInput.value = selectedSpeedboat.capacity;
            capacityInput.readOnly = true;
            capacityInput.classList.add('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
            // Add readonly styling
            capacityInput.style.pointerEvents = 'none';
            capacityInput.style.userSelect = 'none';
        } else {
            capacityInput.readOnly = false;
            capacityInput.classList.remove('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
            // Remove readonly styling
            capacityInput.style.pointerEvents = '';
            capacityInput.style.userSelect = '';
        }

        // Recalculate layout when capacity changes
        calculateTotalSeatsAndGenerateLayout();

        // Additional validation calls
        validateCapacity();
        validateForm();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
        if (!speedboatSearchInput.contains(e.target) && !speedboatDropdown.contains(e.target)) {
            speedboatDropdown.classList.add('hidden');
        }
    });

    // Initial form validation
    validateForm();

    // Generate initial seat layout after DOM is ready
    setTimeout(function() {
        if (typeof calculateTotalSeatsAndGenerateLayout === 'function') {
            calculateTotalSeatsAndGenerateLayout();
        }
    }, 100);
}); // End of DOMContentLoaded

// Global functions for onclick handlers
function selectDestination(id, display, adultPrice, toddlerPrice) {
    searchInput.value = display;
    hiddenInput.value = id;
    selectedDestination = { id, display, adult_price: adultPrice, toddler_price: toddlerPrice };
    dropdown.classList.add('hidden');

    updatePricePreview({ adult_price: adultPrice, toddler_price: toddlerPrice });
    validateForm();
}

function selectSpeedboat(id, display, name) {
    speedboatSearchInput.value = display;
    speedboatHiddenInput.value = id;
    const speedboat = speedboats.find(s => s.id == id);
    selectedSpeedboat = speedboat;
    speedboatDropdown.classList.add('hidden');
    nameHiddenInput.value = name;

    // Update capacity constraints based on selected speedboat
    updateCapacityConstraints(speedboat);

    // Auto-check "use max capacity" checkbox and set capacity
    if (speedboat) {
        const capacityInput = document.getElementById('capacity');
        const useMaxCapacityCheckbox = document.getElementById('use_max_capacity');

        // Set capacity to speedboat max
        capacityInput.value = speedboat.capacity;

        // Check the checkbox
        useMaxCapacityCheckbox.checked = true;

        // Apply readonly styling
        capacityInput.readOnly = true;
        capacityInput.classList.add('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
        capacityInput.style.pointerEvents = 'none';
        capacityInput.style.userSelect = 'none';

        // Calculate layout
        calculateTotalSeatsAndGenerateLayout();
    }

    validateForm();
}

function updatePricePreview(destination) {
    if (destination) {
        document.getElementById('adult-price').textContent = 'Rp ' +
            new Intl.NumberFormat('id-ID').format(destination.adult_price);
        document.getElementById('toddler-price').textContent = 'Rp ' +
            new Intl.NumberFormat('id-ID').format(destination.toddler_price);

        pricePreview.classList.remove('hidden');
    } else {
        pricePreview.classList.add('hidden');
    }
}

function updateCapacityConstraints(speedboat) {
    const capacityInput = document.getElementById('capacity');
    const maxCapacityOption = document.getElementById('maxCapacityOption');
    const speedboatMaxCapacity = document.getElementById('speedboat-max-capacity');
    const capacityInfo = document.getElementById('capacity-info');
    const maxCapacityDisplay = document.getElementById('max-capacity-display');

    if (speedboat) {
        // Set max attribute and show capacity info
        capacityInput.setAttribute('max', speedboat.capacity);
        speedboatMaxCapacity.textContent = speedboat.capacity;
        maxCapacityDisplay.textContent = speedboat.capacity;

        // Show max capacity option and info
        maxCapacityOption.style.display = 'block';
        capacityInfo.classList.remove('hidden');
    } else {
        // Hide max capacity option and info
        maxCapacityOption.style.display = 'none';
        capacityInfo.classList.add('hidden');
        capacityInput.removeAttribute('max');
    }

    // Validate current capacity value
    validateCapacity();
}

function validateCapacity() {
    const capacityInput = document.getElementById('capacity');
    const capacityError = document.getElementById('capacity-error');
    const useMaxCapacityCheckbox = document.getElementById('use_max_capacity');

    // Get current capacity value, force reread from input
    const currentCapacity = parseInt(capacityInput.value) || 0;

    // If checkbox is checked, capacity should always be valid since it's set to speedboat max
    if (useMaxCapacityCheckbox && useMaxCapacityCheckbox.checked && selectedSpeedboat) {
        capacityError.classList.add('hidden');
        capacityInput.classList.remove('border-red-500');
        capacityInput.classList.add('border-gray-300', 'dark:border-gray-600');
        return true;
    }

    if (selectedSpeedboat && currentCapacity > selectedSpeedboat.capacity) {
        capacityError.classList.remove('hidden');
        capacityInput.classList.add('border-red-500');
        capacityInput.classList.remove('border-gray-300', 'dark:border-gray-600');
        return false;
    } else {
        capacityError.classList.add('hidden');
        capacityInput.classList.remove('border-red-500');
        capacityInput.classList.add('border-gray-300', 'dark:border-gray-600');
        return true;
    }
}

function validateForm() {
    const destinationId = document.getElementById('destination_id').value;
    const speedboatId = document.getElementById('speedboat_id').value;
    const name = document.getElementById('name').value.trim();
    const departureTime = document.getElementById('departure_time').value;
    const capacityInput = document.getElementById('capacity');
    const capacity = capacityInput.value;
    const submitBtn = document.getElementById('submitBtn');

    // Validate capacity against speedboat limit
    const isCapacityValid = validateCapacity();

    // Check if all required fields are filled
    const isValid = destinationId !== '' &&
                   speedboatId !== '' &&
                   name !== '' &&
                   departureTime !== '' &&
                   capacity !== '' &&
                   parseInt(capacity) > 0 &&
                   isCapacityValid;

    if (isValid) {
        // Enable button and make it blue
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
        // Disable button and make it gray
        submitBtn.disabled = true;
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
    }
}

// Seat layout management
let seatNumbers = {};

function calculateTotalSeatsAndGenerateLayout() {
    const columnsInput = document.getElementById('columns');
    const rowsInput = document.getElementById('rows');
    const capacityInput = document.getElementById('capacity');
    const totalSeatsDisplay = document.getElementById('total_seats_display');
    const seatCapacityInfo = document.getElementById('seat_capacity_info');

    const columns = parseInt(columnsInput.value) || 0;
    const rows = parseInt(rowsInput.value) || 0;
    const maxCapacity = parseInt(capacityInput.value) || 0;

    // Calculate total seats from layout
    const totalSeats = rows * columns;

    // Update display
    totalSeatsDisplay.textContent = totalSeats;

    // Show info about capacity
    if (totalSeats > maxCapacity) {
        const excessSeats = totalSeats - maxCapacity;
        seatCapacityInfo.innerHTML = `<span class="text-yellow-600 dark:text-yellow-400">(${excessSeats} kursi akan kosong/disabled)</span>`;
    } else if (totalSeats === maxCapacity) {
        seatCapacityInfo.innerHTML = `<span class="text-green-600 dark:text-green-400">(Sesuai kapasitas maksimal)</span>`;
    } else {
        const remainingSeats = maxCapacity - totalSeats;
        seatCapacityInfo.innerHTML = `<span class="text-blue-600 dark:text-blue-400">(Masih bisa tambah ${remainingSeats} kursi)</span>`;
    }

    // Generate seat layout
    generateSeatLayout();
}

function generateSeatLayout() {
    const rowsInput = document.getElementById('rows');
    const columnsInput = document.getElementById('columns');
    const capacityInput = document.getElementById('capacity');

    const rows = parseInt(rowsInput.value) || 5;
    const columns = parseInt(columnsInput.value) || 4;
    const maxCapacity = parseInt(capacityInput.value) || 0;

    // Always regenerate seat numbers to match new layout
    seatNumbers = {};
    const seatLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    let seatCount = 0;
    for (let row = 1; row <= rows; row++) {
        for (let col = 0; col < columns; col++) {
            seatCount++;

            // Generate seat number for all positions
            const seatLabel = seatLabels[col % seatLabels.length];
            const seatNumber = seatLabel + row;
            const position = `${row}-${col}`;

            // Mark as enabled or disabled based on capacity
            if (seatCount <= maxCapacity) {
                seatNumbers[position] = {
                    number: seatNumber,
                    enabled: true
                };
            } else {
                seatNumbers[position] = {
                    number: seatNumber,
                    enabled: false
                };
            }
        }
    }

    // Generate preview
    renderSeatLayoutPreview(rows, columns, maxCapacity);

    // Update hidden input
    updateSeatNumbersInput();

    // Validate capacity
    validateCapacity();
    validateForm();
}

function renderSeatLayoutPreview(rows, columns, maxCapacity) {
    const preview = document.getElementById('seatLayoutPreview');
    const leftColumns = parseInt(document.getElementById('left_columns').value) || 0;
    const rightColumns = columns - leftColumns;

    let html = '<div class="inline-block">';

    let seatCount = 0;
    let enabledCount = 0;
    let disabledCount = 0;

    for (let row = 1; row <= rows; row++) {
        html += '<div class="flex justify-center mb-2">';

        // Left side seats
        html += '<div class="flex space-x-2">';
        for (let col = 0; col < leftColumns; col++) {
            const position = `${row}-${col}`;
            const seatData = seatNumbers[position];

            if (seatData) {
                seatCount++;

                if (seatData.enabled) {
                    // Active seat (within capacity)
                    enabledCount++;
                    html += `
                        <button type="button"
                                class="w-12 h-12 border-2 border-blue-300 dark:border-blue-600 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-lg text-blue-800 dark:text-blue-200 text-xs font-semibold transition-colors duration-200"
                                onclick="editSeatNumber('${position}')"
                                title="Klik untuk edit nomor kursi - Kursi Aktif">
                            ${seatData.number}
                        </button>
                    `;
                } else {
                    // Disabled seat (beyond capacity)
                    disabledCount++;
                    html += `
                        <div class="w-12 h-12 border-2 border-dashed border-gray-400 dark:border-gray-500 bg-gray-200 dark:bg-gray-700 rounded-lg text-gray-500 dark:text-gray-400 text-xs font-semibold flex items-center justify-center opacity-40"
                             title="Kursi Disabled - Melebihi kapasitas maksimal">
                            ${seatData.number}
                        </div>
                    `;
                }
            }
        }
        html += '</div>';

        // Aisle (lorong)
        if (leftColumns > 0 && rightColumns > 0) {
            html += '<div class="w-8 flex items-center justify-center"><div class="h-full w-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div></div>';
        }

        // Right side seats
        html += '<div class="flex space-x-2">';
        for (let col = leftColumns; col < columns; col++) {
            const position = `${row}-${col}`;
            const seatData = seatNumbers[position];

            if (seatData) {
                seatCount++;

                if (seatData.enabled) {
                    // Active seat (within capacity)
                    enabledCount++;
                    html += `
                        <button type="button"
                                class="w-12 h-12 border-2 border-blue-300 dark:border-blue-600 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-lg text-blue-800 dark:text-blue-200 text-xs font-semibold transition-colors duration-200"
                                onclick="editSeatNumber('${position}')"
                                title="Klik untuk edit nomor kursi - Kursi Aktif">
                            ${seatData.number}
                        </button>
                    `;
                } else {
                    // Disabled seat (beyond capacity)
                    disabledCount++;
                    html += `
                        <div class="w-12 h-12 border-2 border-dashed border-gray-400 dark:border-gray-500 bg-gray-200 dark:bg-gray-700 rounded-lg text-gray-500 dark:text-gray-400 text-xs font-semibold flex items-center justify-center opacity-40"
                             title="Kursi Disabled - Melebihi kapasitas maksimal">
                            ${seatData.number}
                        </div>
                    `;
                }
            }
        }
        html += '</div>';

        html += '</div>';
    }

    html += '</div>';
    html += `<div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
        <strong>Total Layout:</strong> ${seatCount} kursi (${rows} baris × ${columns} kolom)<br>
        <strong>Konfigurasi:</strong> ${leftColumns} kiri | lorong | ${rightColumns} kanan<br>
        <strong class="text-green-600 dark:text-green-400">Kursi Aktif:</strong> ${enabledCount} kursi |
        <strong class="text-gray-500 dark:text-gray-400">Kursi Disabled:</strong> ${disabledCount} kursi
    </div>`;

    preview.innerHTML = html;
}

let currentEditingPosition = null;

function editSeatNumber(position) {
    currentEditingPosition = position;
    const seatData = seatNumbers[position];
    const currentNumber = seatData ? seatData.number : '';

    // Show modal
    const modal = document.getElementById('editSeatModal');
    const input = document.getElementById('seatNumberInput');
    const errorMsg = document.getElementById('seatErrorMsg');

    input.value = currentNumber;
    errorMsg.classList.add('hidden');
    modal.classList.remove('hidden');

    // Focus input
    setTimeout(() => {
        input.focus();
        input.select();
    }, 100);
}

function closeSeatModal() {
    const modal = document.getElementById('editSeatModal');
    const errorMsg = document.getElementById('seatErrorMsg');

    modal.classList.add('hidden');
    errorMsg.classList.add('hidden');
    currentEditingPosition = null;
}

function saveSeatNumber() {
    const input = document.getElementById('seatNumberInput');
    const errorMsg = document.getElementById('seatErrorMsg');
    const newNumber = input.value.trim();

    if (newNumber === '') {
        errorMsg.textContent = 'Nomor kursi tidak boleh kosong!';
        errorMsg.classList.remove('hidden');
        return;
    }

    // Check if seat number already exists
    const existingPosition = Object.keys(seatNumbers).find(
        pos => pos !== currentEditingPosition && seatNumbers[pos].number === newNumber
    );

    if (existingPosition) {
        errorMsg.textContent = `Nomor kursi "${newNumber}" sudah digunakan!`;
        errorMsg.classList.remove('hidden');
        return;
    }

    // Save new seat number (keep enabled status)
    const currentSeatData = seatNumbers[currentEditingPosition];
    seatNumbers[currentEditingPosition] = {
        number: newNumber,
        enabled: currentSeatData.enabled
    };

    // Re-render preview
    const rows = parseInt(document.getElementById('rows').value);
    const columns = parseInt(document.getElementById('columns').value);
    const maxCapacity = parseInt(document.getElementById('capacity').value);
    renderSeatLayoutPreview(rows, columns, maxCapacity);

    // Update hidden input
    updateSeatNumbersInput();

    // Close modal
    closeSeatModal();
}

function updateSeatNumbersInput() {
    const hiddenInput = document.getElementById('seat_numbers');
    hiddenInput.value = JSON.stringify(seatNumbers);
}

// Handle Enter key in modal input
document.addEventListener('DOMContentLoaded', function() {
    const seatInput = document.getElementById('seatNumberInput');
    if (seatInput) {
        seatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveSeatNumber();
            }
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editSeatModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeSeatModal();
            }
        }
    });

    // Close modal when clicking outside
    const modal = document.getElementById('editSeatModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeSeatModal();
            }
        });
    }
});
</script>
@endpush
@endsection