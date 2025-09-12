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
            
            <!-- Pilih Jadwal -->
            <div class="mb-6">
                <label for="schedule_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Jadwal Keberangkatan *
                </label>
                <select name="schedule_id" id="schedule_id" 
                        class="form-select w-full" 
                        style="background-color: white !important; color: #1f2937 !important;" 
                        required onchange="updatePricing()">
                    <option value="">-- Pilih Jadwal --</option>
                    @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" 
                            data-adult-price="{{ $schedule->destination->adult_price }}"
                            data-child-price="{{ $schedule->destination->child_price }}"
                            data-available-seats="{{ $schedule->available_seats }}">
                        {{ $schedule->destination->name }} - {{ $schedule->departure_date->format('d M Y') }} pukul {{ $schedule->departure_time->format('H:i') }}
                        ({{ $schedule->available_seats }} kursi tersedia)
                    </option>
                    @endforeach
                </select>
                @error('schedule_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Harga -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/50 rounded-lg" id="priceInfo" style="display: none;">
                <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">Informasi Harga</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600 dark:text-blue-400">Dewasa:</span>
                        <span class="font-semibold text-blue-800 dark:text-blue-300" id="adultPrice">-</span>
                    </div>
                    <div>
                        <span class="text-blue-600 dark:text-blue-400">Anak:</span>
                        <span class="font-semibold text-blue-800 dark:text-blue-300" id="childPrice">-</span>
                    </div>
                </div>
            </div>

            <!-- Data Penumpang -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="passenger_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Penumpang Utama *
                    </label>
                    <input type="text" name="passenger_name" id="passenger_name" 
                           class="form-input w-full" 
                           style="background-color: white !important; color: #1f2937 !important;"
                           value="{{ old('passenger_name') }}" 
                           placeholder="Masukkan nama penumpang utama"
                           required>
                    @error('passenger_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jumlah Penumpang *
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="adult_count" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Dewasa</label>
                            <input type="number" name="adult_count" id="adult_count" 
                                   class="form-input w-full" 
                                   style="background-color: white !important; color: #1f2937 !important;"
                                   value="{{ old('adult_count', 1) }}" 
                                   min="1" max="10" required onchange="calculateTotal()">
                        </div>
                        <div>
                            <label for="child_count" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Anak</label>
                            <input type="number" name="child_count" id="child_count" 
                                   class="form-input w-full" 
                                   style="background-color: white !important; color: #1f2937 !important;"
                                   value="{{ old('child_count', 0) }}" 
                                   min="0" max="10" onchange="calculateTotal()">
                        </div>
                    </div>
                    @error('adult_count')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Total Harga -->
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 rounded-lg" id="totalSection" style="display: none;">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-green-800 dark:text-green-300">Total Pembayaran</h4>
                        <p class="text-sm text-green-600 dark:text-green-400" id="breakdown">-</p>
                    </div>
                    <div class="text-2xl font-bold text-green-800 dark:text-green-300" id="totalAmount">Rp 0</div>
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
                       style="background-color: white !important; color: #1f2937 !important;"
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
                          style="background-color: white !important; color: #1f2937 !important;"
                          placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    Buat Tiket & Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePricing() {
    const select = document.getElementById('schedule_id');
    const option = select.options[select.selectedIndex];
    const priceInfo = document.getElementById('priceInfo');
    
    if (option.value) {
        const adultPrice = parseInt(option.dataset.adultPrice);
        const childPrice = parseInt(option.dataset.childPrice);
        
        document.getElementById('adultPrice').textContent = 'Rp ' + adultPrice.toLocaleString('id-ID');
        document.getElementById('childPrice').textContent = 'Rp ' + childPrice.toLocaleString('id-ID');
        priceInfo.style.display = 'block';
        
        calculateTotal();
    } else {
        priceInfo.style.display = 'none';
        document.getElementById('totalSection').style.display = 'none';
    }
}

function calculateTotal() {
    const select = document.getElementById('schedule_id');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) return;
    
    const adultPrice = parseInt(option.dataset.adultPrice);
    const childPrice = parseInt(option.dataset.childPrice);
    const adultCount = parseInt(document.getElementById('adult_count').value) || 0;
    const childCount = parseInt(document.getElementById('child_count').value) || 0;
    const availableSeats = parseInt(option.dataset.availableSeats);
    
    const totalPassengers = adultCount + childCount;
    
    // Check seat availability
    if (totalPassengers > availableSeats) {
        alert(`Maaf, hanya tersedia ${availableSeats} kursi untuk jadwal ini.`);
        return;
    }
    
    const totalAmount = (adultPrice * adultCount) + (childPrice * childCount);
    
    document.getElementById('totalAmount').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
    document.getElementById('breakdown').textContent = `${adultCount} Dewasa + ${childCount} Anak = ${totalPassengers} penumpang`;
    document.getElementById('totalSection').style.display = 'block';
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
    
    // Initialize payment reference field visibility
    togglePaymentFields();
});
</script>
@endsection