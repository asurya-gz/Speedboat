@extends('layouts.app')

@section('title', 'Jual Tiket Baru')

@section('content')
<style>
/* Fix untuk input fields agar teks terlihat */
.form-input, .form-select {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

html.dark .form-input, html.dark .form-select {
    background-color: #374151 !important;
    color: #f9fafb !important;
    border: 1px solid #4b5563 !important;
}

.form-input:focus, .form-select:focus {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 1px #3b82f6 !important;
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

html.dark .form-input:focus, html.dark .form-select:focus {
    background-color: #374151 !important;
    color: #f9fafb !important;
}

.form-input::placeholder {
    color: #9ca3af !important;
}

html.dark .form-input::placeholder {
    color: #6b7280 !important;
}

/* Pastikan textarea juga terlihat */
textarea.form-input {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    resize: vertical;
}

html.dark textarea.form-input {
    background-color: #374151 !important;
    color: #f9fafb !important;
}

/* Fix untuk select options */
.form-select option {
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

html.dark .form-select option {
    background-color: #374151 !important;
    color: #f9fafb !important;
}

/* Fix untuk radio buttons */
input[type="radio"] {
    accent-color: #3b82f6;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Payment option styling */
.payment-option {
    transition: all 0.2s ease-in-out;
    border: 2px solid #e5e7eb;
}

html.dark .payment-option {
    border: 2px solid #4b5563;
}

.payment-option:hover {
    border-color: #bfdbfe !important;
    background-color: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

html.dark .payment-option:hover {
    border-color: #3b82f6 !important;
    background-color: #1f2937 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.payment-option.selected {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
    box-shadow: 0 0 0 1px #3b82f6, 0 4px 6px rgba(59, 130, 246, 0.1);
}

html.dark .payment-option.selected {
    background-color: #1e3a8a !important;
    box-shadow: 0 0 0 1px #3b82f6, 0 4px 6px rgba(59, 130, 246, 0.3);
}

.payment-option.selected::before {
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

.payment-option {
    position: relative;
}

/* Input number styling */
input[type="number"] {
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

html.dark input[type="number"] {
    background-color: #374151 !important;
    color: #f9fafb !important;
}

/* Ensure all form elements are visible */
input, select, textarea {
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

html.dark input, html.dark select, html.dark textarea {
    background-color: #374151 !important;
    color: #f9fafb !important;
}
</style>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Jual Tiket Baru</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Pilih jadwal dan input data penumpang</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('transactions.index') }}" class="btn btn-info">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Riwayat Transaksi
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Penjualan Tiket -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Penjualan Tiket</h3>
        </div>
        
        <form action="{{ route('transactions.store') }}" method="POST" class="p-6" id="ticketForm">
            @csrf
            
            <!-- Filter Speedboat -->
            <div class="mb-6">
                <label for="speedboat_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Speedboat *
                </label>
                <select name="speedboat_id" id="speedboat_id" 
                        class="form-select w-full" 
                        required onchange="loadFilteredSchedules()">
                    <option value="" selected>-- Pilih Speedboat --</option>
                    @foreach($speedboats as $speedboat)
                    <option value="{{ $speedboat->id }}" {{ old('speedboat_id') == $speedboat->id ? 'selected' : '' }}>
                        {{ $speedboat->name }} ({{ $speedboat->type }})
                    </option>
                    @endforeach
                </select>
                @error('speedboat_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Filter Rute/Destinasi -->
            <div class="mb-6">
                <label for="destination_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Rute Perjalanan *
                </label>
                <select name="destination_id" id="destination_id" 
                        class="form-select w-full" 
                        required onchange="loadFilteredSchedules()">
                    <option value="" selected>-- Pilih Rute --</option>
                    @foreach($destinations as $destination)
                    <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>
                        {{ $destination->departure_location }} → {{ $destination->destination_location }}
                    </option>
                    @endforeach
                </select>
                @error('destination_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Keberangkatan -->
            <div class="mb-6">
                <label for="departure_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tanggal Keberangkatan *
                </label>
                <input type="date" name="departure_date" id="departure_date" 
                       class="form-input w-full" 
                       value="{{ old('departure_date', now()->format('Y-m-d')) }}"
                       min="{{ now()->format('Y-m-d') }}"
                       required onchange="loadFilteredSchedules()">
                @error('departure_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Jadwal -->
            <div class="mb-6">
                <label for="schedule_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Jadwal Keberangkatan *
                </label>
                <select name="schedule_id" id="schedule_id" 
                        class="form-select w-full" 
                        required onchange="updatePricing()" disabled>
                    <option value="">-- Pilih Speedboat, Rute, dan Tanggal terlebih dahulu --</option>
                </select>
                @error('schedule_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Harga -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg" id="priceInfo" style="display: none;">
                <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Informasi Harga</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600 dark:text-blue-300">Dewasa:</span>
                        <span class="font-semibold text-blue-800 dark:text-white" id="adultPrice">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600 dark:text-blue-300">Balita:</span>
                        <span class="font-semibold text-blue-800 dark:text-white" id="toddlerPrice">-</span>
                    </div>
                </div>
            </div>

            <!-- Jumlah Penumpang -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Jumlah Penumpang *
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="adult_count" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Dewasa</label>
                        <input type="number" name="adult_count" id="adult_count" 
                               class="form-input w-full" 
                               value="{{ old('adult_count', 1) }}" 
                               min="1" required onchange="updatePassengerCount()">
                    </div>
                    <div>
                        <label for="toddler_count" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Balita</label>
                        <input type="number" name="toddler_count" id="toddler_count" 
                               class="form-input w-full" 
                               value="{{ old('toddler_count', 0) }}" 
                               min="0" onchange="updatePassengerCount()">
                    </div>
                </div>
                <input type="hidden" name="child_count" value="0">
                <input type="hidden" name="selected_seats" id="selected_seats" value="">
                @error('adult_count')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data Penumpang -->
            <div id="passenger_fields" class="mb-6">
                <!-- Field nama penumpang akan di-generate secara dinamis -->
            </div>

            <!-- Seat Selection -->
            <div class="mb-6" id="seatSelectionSection" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Kursi untuk Setiap Penumpang *
                </label>
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <!-- Passenger list for seat assignment -->
                    <div id="passengerSeatAssignment" class="mb-4">
                        <!-- Will be generated dynamically -->
                    </div>
                    
                    <div id="seatMap" class="text-center">
                        <!-- Seat map will be generated here -->
                    </div>
                    <div class="mt-4 flex justify-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">Tersedia</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">Terisi</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">Dipilih</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">Sedang Dipilih</span>
                        </div>
                    </div>
                </div>
                <div id="seatAssignmentStatus" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div>
            </div>

            <!-- Total Harga -->
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg" id="totalSection" style="display: none;">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-green-800 dark:text-green-200">Total Pembayaran</h4>
                        <p class="text-sm text-green-600 dark:text-green-300" id="breakdown">-</p>
                    </div>
                    <div class="text-2xl font-bold text-green-800 dark:text-white" id="totalAmount">Rp 0</div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Metode Pembayaran *
                </label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="payment-option flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="payment_method" value="cash" class="mr-3" 
                               {{ old('payment_method') == 'cash' ? 'checked' : '' }} onchange="togglePaymentFields()">
                        <div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">Tunai</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Bayar langsung</div>
                        </div>
                    </label>
                    
                    <label class="payment-option flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="payment_method" value="transfer" class="mr-3"
                               {{ old('payment_method') == 'transfer' ? 'checked' : '' }} onchange="togglePaymentFields()">
                        <div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">Transfer</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Bank Transfer</div>
                        </div>
                    </label>
                    
                    <label class="payment-option flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="radio" name="payment_method" value="qris" class="mr-3"
                               {{ old('payment_method') == 'qris' ? 'checked' : '' }} onchange="togglePaymentFields()">
                        <div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">QRIS</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Scan QR</div>
                        </div>
                    </label>
                </div>
                @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Reference (for non-cash) -->
            <div class="mb-6" id="paymentRefSection" style="display: none;">
                <label for="payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Referensi Pembayaran
                </label>
                <input type="text" name="payment_reference" id="payment_reference" 
                       class="form-input w-full" 
                       value="{{ old('payment_reference') }}"
                       placeholder="No. Transaksi / ID Pembayaran">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsional untuk non-tunai</p>
            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          class="form-input w-full" 
                          placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    Buat Tiket & Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Embed schedule data for JavaScript -->
<script>
// All schedules data from server
const allSchedules = @json($allSchedules);

// Seat selection state
let passengerSeatAssignments = {}; // {passengerIndex: seatNumber}
let passengers = []; // Array of passenger objects
let currentSelectingPassenger = null;
let totalPassengers = 0;

function loadFilteredSchedules() {
    const speedboatId = document.getElementById('speedboat_id').value;
    const destinationId = document.getElementById('destination_id').value;
    const departureDate = document.getElementById('departure_date').value;
    const scheduleSelect = document.getElementById('schedule_id');
    
    // Reset schedule select
    scheduleSelect.innerHTML = '<option value="">-- Loading... --</option>';
    scheduleSelect.disabled = true;
    document.getElementById('priceInfo').style.display = 'none';
    document.getElementById('totalSection').style.display = 'none';
    
    if (speedboatId && destinationId && departureDate) {
        // Fetch real-time schedules from server API using jQuery AJAX
        $.ajax({
            url: '/transactions/filtered-schedules',
            method: 'GET',
            data: {
                speedboat_id: speedboatId,
                destination_id: destinationId,
                departure_date: departureDate
            },
            dataType: 'json',
            success: function(filteredSchedules) {
            scheduleSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
            
            if (filteredSchedules.length === 0) {
                scheduleSelect.innerHTML = '<option value="">-- Tidak ada jadwal tersedia --</option>';
            } else {
                filteredSchedules.forEach(schedule => {
                const option = document.createElement('option');
                option.value = schedule.id;
                option.dataset.adultPrice = schedule.destination.adult_price;
                option.dataset.childPrice = schedule.destination.child_price;
                option.dataset.toddlerPrice = schedule.destination.toddler_price || 0;
                option.dataset.capacity = schedule.capacity;
                option.dataset.availableSeats = schedule.available_seats || schedule.capacity;
                option.dataset.bookedSeats = schedule.booked_seats || 0;
                option.dataset.status = schedule.status || 'available';
                
                const departureTime = new Date(`2000-01-01T${schedule.departure_time}`);
                const timeString = departureTime.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    hour12: false 
                });
                
                const availableSeats = schedule.available_seats || schedule.capacity;
                const bookedSeats = schedule.booked_seats || 0;
                const totalCapacity = schedule.capacity;
                
                // Create status indicator
                let statusText = '';
                let statusClass = '';
                if (schedule.status === 'full') {
                    statusText = ' - PENUH';
                    statusClass = 'text-red-600';
                    option.disabled = true;
                } else if (schedule.status === 'limited') {
                    statusText = ' - TERBATAS';
                    statusClass = 'text-orange-600';
                } else {
                    statusText = '';
                    statusClass = 'text-green-600';
                }
                
                option.textContent = `${schedule.name} pukul ${timeString} (${availableSeats}/${totalCapacity} kursi tersedia)${statusText}`;
                
                // Add visual styling for status
                if (schedule.status === 'full') {
                    option.style.color = '#dc2626';
                    option.style.fontStyle = 'italic';
                } else if (schedule.status === 'limited') {
                    option.style.color = '#ea580c';
                }
                
                scheduleSelect.appendChild(option);
            });
            }
            
            scheduleSelect.disabled = false;
            },
            error: function(xhr, status, error) {
                console.error('Error loading schedules:', error, xhr.responseText);
                console.error('XHR Status:', xhr.status);
                console.error('XHR Response:', xhr.responseJSON || xhr.responseText);
                
                if (xhr.status === 401) {
                    scheduleSelect.innerHTML = '<option value="">-- Session expired, please refresh page --</option>';
                    alert('Session expired. Please refresh the page and login again.');
                } else if (xhr.status === 403) {
                    scheduleSelect.innerHTML = '<option value="">-- Access denied --</option>';
                } else {
                    scheduleSelect.innerHTML = '<option value="">-- Error loading schedules --</option>';
                }
                scheduleSelect.disabled = false;
            }
        });
    } else {
        scheduleSelect.innerHTML = '<option value="">-- Pilih Speedboat, Rute, dan Tanggal terlebih dahulu --</option>';
        scheduleSelect.disabled = true;
    }
    
    validateForm();
}

function updatePricing() {
    const select = document.getElementById('schedule_id');
    const option = select.options[select.selectedIndex];
    const priceInfo = document.getElementById('priceInfo');
    
    if (option.value) {
        const adultPrice = parseInt(option.dataset.adultPrice);
        const childPrice = parseInt(option.dataset.childPrice);
        const toddlerPrice = parseInt(option.dataset.toddlerPrice || 0);
        
        document.getElementById('adultPrice').textContent = 'Rp ' + adultPrice.toLocaleString('id-ID');
        document.getElementById('toddlerPrice').textContent = 'Rp ' + toddlerPrice.toLocaleString('id-ID');
        priceInfo.style.display = 'block';
        
        // Load seat map when schedule is selected
        loadSeatMap();
        
        calculateTotal();
    } else {
        priceInfo.style.display = 'none';
        document.getElementById('totalSection').style.display = 'none';
        document.getElementById('seatSelectionSection').style.display = 'none';
    }
    validateForm();
}

function loadSeatMap() {
    const scheduleId = document.getElementById('schedule_id').value;
    const departureDate = document.getElementById('departure_date').value;
    
    if (!scheduleId || !departureDate) return;
    
    // Show loading state
    const seatMap = document.getElementById('seatMap');
    seatMap.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="sr-only">Loading seat map...</span></div><p>Memuat peta kursi...</p></div>';
    
    // Fetch seat map from server using jQuery AJAX
    console.log('Loading seat map for schedule:', scheduleId, 'date:', departureDate);
    $.ajax({
        url: '/transactions/seat-map',
        method: 'GET',
        data: {
            schedule_id: scheduleId,
            departure_date: departureDate
        },
        dataType: 'json',
        success: function(data) {
            console.log('Seat map data received:', data);
            if (data.seat_layout) {
                generateSeatMapFromServer(data.seat_layout);
                document.getElementById('seatSelectionSection').style.display = 'block';
            } else {
                console.error('No seat_layout in response');
                seatMap.innerHTML = '<div class="text-red-500">Error loading seat map - no layout data</div>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading seat map:', error, xhr.responseText);
            console.log('Falling back to client-side seat generation');
            
            // Fallback to client-side generation
            const select = document.getElementById('schedule_id');
            const option = select.options[select.selectedIndex];
            const capacity = parseInt(option.dataset.capacity);
            
            if (capacity) {
                generateClientSideSeatMap(capacity);
                document.getElementById('seatSelectionSection').style.display = 'block';
            } else {
                seatMap.innerHTML = '<div class="text-red-500">Error loading seat map: ' + error + '</div>';
            }
        }
    });
}

function generateSeatMapFromServer(seatLayout) {
    const seatMap = document.getElementById('seatMap');

    let html = '<div class="mb-4"><h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Layout Kursi Speedboat</h4></div>';
    html += '<div class="max-w-md mx-auto">';

    seatLayout.forEach(row => {
        html += '<div class="flex justify-center mb-2 space-x-2">';

        row.forEach(seat => {
            // Handle empty seats (for layout alignment)
            if (seat.is_empty || !seat.seat_number) {
                html += `<div class="w-10 h-10 border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 rounded-lg opacity-30"></div>`;
                return;
            }

            const seatNumber = seat.seat_number;
            const isAvailable = seat.is_available;

            let buttonClass = 'seat-btn w-10 h-10 border-2 rounded-lg text-white text-xs font-semibold transition-colors duration-200';
            let clickHandler = '';
            let title = `Kursi ${seatNumber}`;

            if (isAvailable) {
                buttonClass += ' border-gray-300 bg-green-500 hover:bg-green-600';
                clickHandler = `onclick="toggleSeat('${seatNumber}')"`;
                title += ' - Tersedia';
            } else {
                buttonClass += ' border-red-300 bg-red-500 cursor-not-allowed';
                title += ' - Sudah dipesan';
            }

            html += `<button type="button"
                     class="${buttonClass}"
                     data-seat="${seatNumber}"
                     ${clickHandler}
                     title="${title}"
                     ${!isAvailable ? 'disabled' : ''}>
                     ${seatNumber}
                     </button>`;
        });

        html += '</div>';
    });

    html += '</div>';

    // Add legend
    html += '<div class="mt-4 flex justify-center space-x-4 text-sm">';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-green-500 rounded mr-2"></div>Tersedia</div>';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-red-500 rounded mr-2"></div>Sudah dipesan</div>';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>Dipilih</div>';
    html += '</div>';

    seatMap.innerHTML = html;
}

function generateClientSideSeatMap(capacity) {
    const seatMap = document.getElementById('seatMap');
    const seatsPerRow = 4;
    const totalRows = Math.ceil(capacity / seatsPerRow);
    const seatLabels = ['A', 'B', 'C', 'D'];
    
    let html = '<div class="mb-4"><h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Layout Kursi Speedboat</h4>';
    html += '<p class="text-sm text-yellow-600">⚠️ Menampilkan semua kursi sebagai tersedia - silakan coba refresh jika ada masalah</p></div>';
    html += '<div class="max-w-md mx-auto">';
    
    for (let row = 1; row <= totalRows; row++) {
        html += '<div class="flex justify-center mb-2 space-x-2">';
        
        for (let seatIndex = 0; seatIndex < seatsPerRow; seatIndex++) {
            const seatNumber = seatLabels[seatIndex] + row;
            const seatCount = (row - 1) * seatsPerRow + seatIndex + 1;
            
            if (seatCount <= capacity) {
                html += `<button type="button" 
                         class="seat-btn w-10 h-10 border-2 border-gray-300 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-semibold transition-colors duration-200" 
                         data-seat="${seatNumber}" 
                         onclick="toggleSeat('${seatNumber}')"
                         title="Kursi ${seatNumber} - Tersedia">
                         ${seatNumber}
                         </button>`;
            }
        }
        
        html += '</div>';
    }
    
    html += '</div>';
    
    // Add legend
    html += '<div class="mt-4 flex justify-center space-x-4 text-sm">';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-green-500 rounded mr-2"></div>Tersedia</div>';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-red-500 rounded mr-2"></div>Sudah dipesan</div>';
    html += '<div class="flex items-center"><div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>Dipilih</div>';
    html += '</div>';
    
    seatMap.innerHTML = html;
}

function toggleSeat(seatNumber) {
    if (currentSelectingPassenger === null) {
        alert('Silakan pilih penumpang terlebih dahulu sebelum memilih kursi.');
        return;
    }
    
    const seatBtn = document.querySelector(`[data-seat="${seatNumber}"]`);
    
    // Check if seat is already occupied by someone else
    const seatOccupiedBy = Object.keys(passengerSeatAssignments).find(
        passengerIndex => passengerSeatAssignments[passengerIndex] === seatNumber
    );
    
    if (seatOccupiedBy && parseInt(seatOccupiedBy) !== currentSelectingPassenger) {
        const occupyingPassenger = passengers[seatOccupiedBy];
        alert(`Kursi ${seatNumber} sudah dipilih oleh ${occupyingPassenger.name}`);
        return;
    }
    
    // Check if current passenger already has a seat assigned
    const currentAssignment = passengerSeatAssignments[currentSelectingPassenger];
    if (currentAssignment) {
        // Remove previous assignment
        const oldSeatBtn = document.querySelector(`[data-seat="${currentAssignment}"]`);
        if (oldSeatBtn) {
            oldSeatBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            oldSeatBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            oldSeatBtn.innerHTML = currentAssignment; // Remove passenger name
        }
    }
    
    // Assign new seat
    passengerSeatAssignments[currentSelectingPassenger] = seatNumber;
    seatBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
    seatBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
    
    // Add passenger name to seat button
    const passenger = passengers[currentSelectingPassenger];
    seatBtn.innerHTML = `${seatNumber}<br><span class="text-xs">${passenger.name}</span>`;
    
    // Update passenger list display
    generatePassengerSeatList();
    
    // Update status
    const statusDiv = document.getElementById('seatAssignmentStatus');
    statusDiv.textContent = `${passenger.name} berhasil ditempatkan di kursi ${seatNumber}`;
    
    // Clear current selection after successful assignment
    currentSelectingPassenger = null;
    
    // Update form data
    updateSeatAssignmentData();
    validateForm();
}

function updateSeatAssignmentData() {
    const selectedSeatsInput = document.getElementById('selected_seats');
    
    // Create an array of seat assignments with passenger info
    const seatAssignments = [];
    Object.keys(passengerSeatAssignments).forEach(passengerIndex => {
        const passenger = passengers[passengerIndex];
        const seatNumber = passengerSeatAssignments[passengerIndex];
        seatAssignments.push({
            passengerIndex: parseInt(passengerIndex),
            passengerName: passenger.name,
            passengerType: passenger.type,
            seatNumber: seatNumber
        });
    });
    
    selectedSeatsInput.value = JSON.stringify(seatAssignments);
    
    // Update status display
    const statusDiv = document.getElementById('seatAssignmentStatus');
    const assignedCount = Object.keys(passengerSeatAssignments).length;
    if (assignedCount === 0) {
        statusDiv.textContent = 'Belum ada kursi yang dipilih';
    } else if (assignedCount < totalPassengers) {
        statusDiv.textContent = `${assignedCount}/${totalPassengers} penumpang sudah memilih kursi`;
    } else {
        statusDiv.textContent = `Semua penumpang sudah memiliki kursi! ✅`;
    }
}

function updatePassengerCount() {
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const toddlerCount = parseInt(document.getElementById('toddler_count').value) || 0;
    totalPassengers = adultCount + toddlerCount;
    
    // Reset seat selection if passenger count changes
    selectedSeats = [];
    document.querySelectorAll('.seat-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        btn.classList.add('bg-green-500', 'hover:bg-green-600');
    });
    
    updateSelectedSeatsInfo();
    generatePassengerFields();
}

function calculateTotal() {
    const select = document.getElementById('schedule_id');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) return;
    
    const adultPrice = parseInt(option.dataset.adultPrice);
    const childPrice = parseInt(option.dataset.childPrice);
    const toddlerPrice = parseInt(option.dataset.toddlerPrice || 0);
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const childCount = parseInt(document.getElementById('child_count').value) || 0;
    const toddlerCount = parseInt(document.getElementById('toddler_count').value) || 0;
    const availableSeats = parseInt(option.dataset.availableSeats);
    
    const totalPassengers = adultCount + childCount + toddlerCount;
    
    // Check seat availability
    if (totalPassengers > availableSeats) {
        alert(`Maaf, hanya tersedia ${availableSeats} kursi untuk jadwal ini.`);
        return;
    }
    
    const totalAmount = (adultPrice * adultCount) + (childPrice * childCount) + (toddlerPrice * toddlerCount);
    
    document.getElementById('totalAmount').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
    document.getElementById('breakdown').textContent = `${adultCount} Dewasa + ${toddlerCount} Balita = ${totalPassengers} penumpang`;
    document.getElementById('totalSection').style.display = 'block';
    
    validateForm();
}

function togglePaymentFields() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
    const paymentRefSection = document.getElementById('paymentRefSection');
    
    // Update payment reference field visibility
    if (paymentMethod === 'transfer' || paymentMethod === 'qris') {
        paymentRefSection.style.display = 'block';
    } else {
        paymentRefSection.style.display = 'none';
    }
    
    // Update visual selection
    updatePaymentSelection();
    validateForm();
}

// Form validation function
function generatePassengerFields() {
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const toddlerCount = parseInt(document.getElementById('toddler_count').value) || 0;
    totalPassengers = adultCount + toddlerCount;
    
    const container = document.getElementById('passenger_fields');
    let html = '';
    
    // Reset passengers array
    passengers = [];
    
    if (totalPassengers > 0) {
        html += '<h4 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Data Penumpang</h4>';
        html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        
        // Generate adult passenger fields
        for (let i = 1; i <= adultCount; i++) {
            const passengerIndex = passengers.length;
            passengers.push({ type: 'adult', index: i, globalIndex: passengerIndex });
            
            html += `
                <div>
                    <label for="adult_name_${i}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Penumpang Dewasa ${i} *
                    </label>
                    <input type="text" name="adult_names[]" id="adult_name_${i}" 
                           class="form-input w-full" 
                           placeholder="Masukkan nama lengkap"
                           onchange="updatePassengerData(${passengerIndex})"
                           required>
                </div>
            `;
        }
        
        // Generate toddler passenger fields
        for (let i = 1; i <= toddlerCount; i++) {
            const passengerIndex = passengers.length;
            passengers.push({ type: 'toddler', index: i, globalIndex: passengerIndex });
            
            html += `
                <div>
                    <label for="toddler_name_${i}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Penumpang Balita ${i} *
                    </label>
                    <input type="text" name="toddler_names[]" id="toddler_name_${i}" 
                           class="form-input w-full" 
                           placeholder="Masukkan nama lengkap"
                           onchange="updatePassengerData(${passengerIndex})"
                           required>
                </div>
            `;
        }
        
        html += '</div>';
    }
    
    container.innerHTML = html;
    
    // Reset seat assignments
    passengerSeatAssignments = {};
    currentSelectingPassenger = null;
    
    // Check if we should show seat selection
    checkShowSeatSelection();
    
    calculateTotal();
    validateForm();
}

function updatePassengerData(passengerIndex) {
    const passenger = passengers[passengerIndex];
    if (!passenger) return;
    
    let nameInput;
    if (passenger.type === 'adult') {
        nameInput = document.getElementById(`adult_name_${passenger.index}`);
    } else {
        nameInput = document.getElementById(`toddler_name_${passenger.index}`);
    }
    
    passenger.name = nameInput.value;
    
    // Update seat assignment display if exists
    updatePassengerSeatList();
    
    // Check if we should show seat selection
    checkShowSeatSelection();
}

function checkShowSeatSelection() {
    // Show seat selection only if:
    // 1. Schedule is selected
    // 2. All passenger names are filled
    const scheduleSelected = document.getElementById('schedule_id').value !== '';
    const allNamesFilled = passengers.every(p => p.name && p.name.trim() !== '');
    
    if (scheduleSelected && allNamesFilled && passengers.length > 0) {
        loadSeatMapWithPassengers();
        document.getElementById('seatSelectionSection').style.display = 'block';
    } else {
        document.getElementById('seatSelectionSection').style.display = 'none';
    }
}

function loadSeatMapWithPassengers() {
    generatePassengerSeatList();
    loadSeatMap();
}

function generatePassengerSeatList() {
    const container = document.getElementById('passengerSeatAssignment');
    let html = '<h5 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Klik nama penumpang, lalu pilih kursi:</h5>';
    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">';
    
    passengers.forEach((passenger, index) => {
        const assignedSeat = passengerSeatAssignments[index] || '';
        const isSelected = currentSelectingPassenger === index;
        const hasAssignment = assignedSeat !== '';
        
        html += `
            <button type="button" 
                    class="passenger-btn p-3 border-2 rounded-lg text-left transition-all ${
                        isSelected ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 
                        hasAssignment ? 'border-green-500 bg-green-50 dark:bg-green-900/20' :
                        'border-gray-300 hover:border-blue-400'
                    }"
                    onclick="selectPassengerForSeat(${index})">
                <div class="font-medium text-gray-700 dark:text-gray-300">
                    ${passenger.name || `${passenger.type === 'adult' ? 'Dewasa' : 'Balita'} ${passenger.index}`}
                </div>
                <div class="text-sm ${hasAssignment ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'}">
                    ${hasAssignment ? `Kursi: ${assignedSeat}` : 'Belum pilih kursi'}
                </div>
            </button>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function selectPassengerForSeat(passengerIndex) {
    currentSelectingPassenger = passengerIndex;
    generatePassengerSeatList(); // Refresh to show selection
    
    // Update status message
    const passenger = passengers[passengerIndex];
    const statusDiv = document.getElementById('seatAssignmentStatus');
    statusDiv.textContent = `Sedang memilih kursi untuk: ${passenger.name}`;
}

function updatePassengerSeatList() {
    if (document.getElementById('seatSelectionSection').style.display !== 'none') {
        generatePassengerSeatList();
    }
}

function validateForm() {
    const speedboatId = document.getElementById('speedboat_id').value;
    const destinationId = document.getElementById('destination_id').value;
    const departureDate = document.getElementById('departure_date').value;
    const scheduleId = document.getElementById('schedule_id').value;
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const toddlerCount = parseInt(document.getElementById('toddler_count').value) || 0;
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
    const submitBtn = document.getElementById('submitBtn');
    
    // Check if all passenger name fields are filled
    let allNamesFilled = true;
    const adultNameInputs = document.querySelectorAll('input[name="adult_names[]"]');
    const toddlerNameInputs = document.querySelectorAll('input[name="toddler_names[]"]');
    
    adultNameInputs.forEach(input => {
        if (!input.value.trim()) allNamesFilled = false;
    });
    
    toddlerNameInputs.forEach(input => {
        if (!input.value.trim()) allNamesFilled = false;
    });
    
    // Check if all passengers have seat assignments
    const allPassengersHaveSeats = Object.keys(passengerSeatAssignments).length === totalPassengers;
    
    // Check if all required fields are filled
    const isValid = speedboatId !== '' && 
                   destinationId !== '' && 
                   departureDate !== '' &&
                   scheduleId !== '' && 
                   adultCount >= 1 && 
                   allPassengersHaveSeats &&
                   allNamesFilled && 
                   paymentMethod !== undefined;
    
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

function updatePaymentSelection() {
    const allOptions = document.querySelectorAll('.payment-option');
    const selectedRadio = document.querySelector('input[name="payment_method"]:checked');
    
    // Remove selected class from all options
    allOptions.forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to the chosen option
    if (selectedRadio) {
        const selectedOption = selectedRadio.closest('.payment-option');
        if (selectedOption) {
            selectedOption.classList.add('selected');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Setup jQuery AJAX defaults
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        xhrFields: {
            withCredentials: true
        }
    });
    // Reset all form fields to default state
    document.getElementById('speedboat_id').value = '';
    document.getElementById('destination_id').value = '';
    document.getElementById('schedule_id').innerHTML = '<option value="">-- Pilih Speedboat, Rute, dan Tanggal terlebih dahulu --</option>';
    document.getElementById('schedule_id').value = '';
    document.getElementById('schedule_id').disabled = true;
    
    // Initialize passenger count
    totalPassengers = parseInt(document.getElementById('adult_count').value) + parseInt(document.getElementById('toddler_count').value);
    
    updatePricing();
    
    // Initialize payment method selection visual
    updatePaymentSelection();
    
    // Add event listeners to all payment radio buttons
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            togglePaymentFields();
        });
    });
    
    // Function to remove leading zeros from number inputs
    function normalizeNumberInput(input) {
        let value = input.value;
        if (value && value.length > 1 && value.charAt(0) === '0') {
            // Remove leading zeros, but keep single '0' if that's the only digit
            value = parseInt(value, 10).toString();
            input.value = value;
        }
    }
    
    // Add event listeners for form validation
    document.getElementById('speedboat_id').addEventListener('change', validateForm);
    document.getElementById('destination_id').addEventListener('change', validateForm);
    document.getElementById('departure_date').addEventListener('change', validateForm);
    document.getElementById('schedule_id').addEventListener('change', validateForm);
    
    // Generate initial passenger fields
    generatePassengerFields();
    
    // Adult count input
    document.getElementById('adult_count').addEventListener('input', function() {
        normalizeNumberInput(this);
        generatePassengerFields();
    });
    document.getElementById('adult_count').addEventListener('blur', function() {
        normalizeNumberInput(this);
    });
    
    // Toddler count input
    document.getElementById('toddler_count').addEventListener('input', function() {
        normalizeNumberInput(this);
        generatePassengerFields();
    });
    document.getElementById('toddler_count').addEventListener('blur', function() {
        normalizeNumberInput(this);
    });
    
    // Initialize payment reference field visibility
    togglePaymentFields();
    
    // Initial form validation
    validateForm();
});
</script>
@endsection