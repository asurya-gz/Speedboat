@extends('layouts.app')

@section('title', 'Riwayat Validasi QR Code')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Riwayat Validasi QR Code</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Daftar tiket yang telah divalidasi untuk boarding</p>
            </div>
            <a href="{{ route('tickets.validate.form') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                </svg>
                Validasi Tiket Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Validasi Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $validatedTickets->where('validated_at', '>=', today())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Validasi</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $validatedTickets->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Penumpang Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $validatedTickets->where('validated_at', '>=', today())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6 shadow-sm">
        <form method="GET" action="{{ route('tickets.validate.history') }}" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pencarian & Filter</h3>
                <button type="button" onclick="resetFilters()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Reset Filter
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search by Ticket Code -->
                <div>
                    <label for="ticket_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kode Tiket
                    </label>
                    <input type="text" id="ticket_code" name="ticket_code" 
                           value="{{ request('ticket_code') }}"
                           class="form-input w-full"
                           placeholder="Cari kode tiket...">
                </div>

                <!-- Search by Passenger Name -->
                <div>
                    <label for="passenger_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Penumpang
                    </label>
                    <input type="text" id="passenger_name" name="passenger_name" 
                           value="{{ request('passenger_name') }}"
                           class="form-input w-full"
                           placeholder="Cari nama penumpang...">
                </div>

                <!-- Filter by Destination -->
                <div>
                    <label for="destination_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tujuan
                    </label>
                    <select id="destination_id" name="destination_id" class="form-select w-full">
                        <option value="">Semua Tujuan</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ request('destination_id') == $destination->id ? 'selected' : '' }}>
                                {{ $destination->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dari Tanggal
                    </label>
                    <input type="date" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}"
                           class="form-input w-full">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sampai Tanggal
                    </label>
                    <input type="date" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}"
                           class="form-input w-full">
                </div>

                <!-- Filter by Validator -->
                <div>
                    <label for="validator_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Divalidasi Oleh
                    </label>
                    <select id="validator_id" name="validator_id" class="form-select w-full">
                        <option value="">Semua Validator</option>
                        @foreach($validators as $validator)
                            <option value="{{ $validator->id }}" {{ request('validator_id') == $validator->id ? 'selected' : '' }}>
                                {{ $validator->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Search Buttons -->
            <div class="flex items-center space-x-3 pt-4">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>
                <a href="{{ route('tickets.validate.history') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </a>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ $validatedTickets->count() }} dari {{ $validatedTickets->total() }} hasil
                </div>
                @if($validatedTickets->count() > 0)
                    <a href="{{ route('tickets.validate.history.export', request()->query()) }}" 
                       class="btn btn-success btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Validation History Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Validasi Tiket</h3>
                @if(request()->hasAny(['ticket_code', 'passenger_name', 'destination_id', 'date_from', 'date_to', 'validator_id']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter Aktif
                    </span>
                @endif
            </div>
        </div>

        @if($validatedTickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Kode Tiket
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Penumpang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tujuan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Jadwal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Waktu Validasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Divalidasi Oleh
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($validatedTickets as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_code }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($ticket->passenger_type) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->passenger_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->transaction && $ticket->transaction->schedule && $ticket->transaction->schedule->destination)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $ticket->transaction->schedule->destination->departure_location }} → {{ $ticket->transaction->schedule->destination->destination_location }}</div>
                                    @else
                                        <div class="text-sm text-gray-500 dark:text-gray-400">N/A</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->transaction && $ticket->transaction->schedule)
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $ticket->transaction->schedule->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $ticket->transaction->schedule->departure_time->format('H:i') }} WIB
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 dark:text-gray-400">N/A</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $ticket->validated_at->format('d M Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $ticket->validated_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $ticket->validator->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Sudah Boarding
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $validatedTickets->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Validasi</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada tiket yang divalidasi. Mulai validasi tiket untuk melihat riwayat disini.</p>
                <a href="{{ route('tickets.validate.form') }}" class="btn btn-primary">
                    Mulai Validasi Tiket
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function resetFilters() {
    // Reset all form fields
    document.getElementById('ticket_code').value = '';
    document.getElementById('passenger_name').value = '';
    document.getElementById('destination_id').value = '';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    document.getElementById('validator_id').value = '';
}

// Auto-submit form on date change for better UX
document.getElementById('date_from').addEventListener('change', function() {
    if (this.value && document.getElementById('date_to').value) {
        document.querySelector('form').submit();
    }
});

document.getElementById('date_to').addEventListener('change', function() {
    if (this.value && document.getElementById('date_from').value) {
        document.querySelector('form').submit();
    }
});

// Auto-submit on dropdown change
document.getElementById('destination_id').addEventListener('change', function() {
    if (this.value !== '') {
        document.querySelector('form').submit();
    }
});

document.getElementById('validator_id').addEventListener('change', function() {
    if (this.value !== '') {
        document.querySelector('form').submit();
    }
});

// Add search on Enter key
document.getElementById('ticket_code').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.querySelector('form').submit();
    }
});

document.getElementById('passenger_name').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.querySelector('form').submit();
    }
});
</script>
@endsection