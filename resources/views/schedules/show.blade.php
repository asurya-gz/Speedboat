@extends('layouts.app')

@section('title', 'Detail Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Detail Jadwal: {{ $schedule->destination->name }}
            </h3>
        </div>
        
        <div class="p-6">
            <div class="space-y-6">
                <!-- Informasi Destinasi -->
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Informasi Destinasi
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Destinasi</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                {{ $schedule->destination->code }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Destinasi</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $schedule->destination->name }}</p>
                        </div>
                        @if($schedule->destination->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $schedule->destination->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Jadwal / Speedboat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Nama Jadwal
                        </label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 cursor-not-allowed"
                               value="{{ $schedule->name }}"
                               disabled
                               readonly>
                    </div>

                    <!-- Waktu Keberangkatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Waktu Keberangkatan
                        </label>
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-cyan-100 dark:bg-cyan-900 text-cyan-800 dark:text-cyan-300">
                            {{ $schedule->departure_time->format('H:i') }} WIB
                        </span>
                    </div>
                </div>

                <!-- Speedboat Info -->
                @if($schedule->speedboat)
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        Informasi Speedboat
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Speedboat</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $schedule->speedboat->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">
                                {{ $schedule->speedboat->code }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $schedule->speedboat->type }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kapasitas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Kapasitas Total
                        </label>
                        <input type="text"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 cursor-not-allowed"
                               value="{{ $schedule->capacity }} penumpang"
                               disabled
                               readonly>
                    </div>

                    <!-- Total Transaksi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Total Transaksi
                        </label>
                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">
                            {{ $schedule->transactions->count() }} transaksi
                        </span>
                    </div>
                </div>

                <!-- Layout Kursi -->
                @if($schedule->seat_numbers)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-blue-900 dark:text-blue-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                        </svg>
                        Layout Kursi Speedboat
                    </h4>

                    <div class="bg-white dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div id="seatLayoutDisplay" class="inline-block">
                            <!-- Seat layout will be rendered here -->
                        </div>
                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Konfigurasi:</strong> {{ $schedule->rows }} baris × {{ $schedule->columns }} kolom = {{ $schedule->capacity }} kursi
                        </div>
                    </div>
                </div>
                @endif

                <!-- Informasi Harga -->
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Informasi Harga Tiket
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Harga Dewasa</div>
                                <div class="text-lg font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($schedule->destination->adult_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Harga Anak</div>
                                <div class="text-lg font-semibold text-cyan-600 dark:text-cyan-400">Rp {{ number_format($schedule->destination->child_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-3">Status Jadwal:</label>
                        @if($schedule->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-200 text-green-800 dark:text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                Nonaktif
                            </span>
                        @endif
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
                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if($schedule->seat_numbers)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatNumbers = @json($schedule->seat_numbers);
    const rows = {{ $schedule->rows }};
    const columns = {{ $schedule->columns }};
    const capacity = {{ $schedule->capacity }};

    renderSeatLayout(seatNumbers, rows, columns, capacity);
});

function renderSeatLayout(seatNumbers, rows, columns, capacity) {
    const container = document.getElementById('seatLayoutDisplay');
    if (!container) return;

    let html = '<div class="inline-block">';

    let seatCount = 0;
    for (let row = 1; row <= rows; row++) {
        html += '<div class="flex justify-center mb-2 space-x-2">';

        for (let col = 0; col < columns; col++) {
            const position = `${row}-${col}`;
            const seatNumber = seatNumbers[position];

            if (seatNumber) {
                // Seat exists (within capacity)
                html += `
                    <div class="w-12 h-12 border-2 border-blue-300 dark:border-blue-600 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-800 dark:text-blue-200 text-xs font-semibold flex items-center justify-center">
                        ${seatNumber}
                    </div>
                `;
                seatCount++;
            } else {
                // Empty space (beyond capacity)
                html += `
                    <div class="w-12 h-12 border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 rounded-lg opacity-30"></div>
                `;
            }
        }

        html += '</div>';
    }

    html += '</div>';

    container.innerHTML = html;
}
</script>
@endif
@endpush
@endsection