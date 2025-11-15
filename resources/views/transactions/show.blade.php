@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $transaction->transaction_code)
@section('description', 'Detail lengkap transaksi speedboat dengan kode ' . $transaction->transaction_code . ' - Informasi penumpang, jadwal keberangkatan, dan status pembayaran.')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Transaksi</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $transaction->transaction_code }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('transactions.print', $transaction) }}" target="_blank" class="btn btn-success">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Tiket
                </a>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaction Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Transaksi</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Kode Transaksi</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->transaction_code }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nama Penumpang</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->passenger_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Jumlah Penumpang</span>
                    <span class="font-semibold dark:text-white">
                        {{ $transaction->adult_count }} Dewasa
                        @if($transaction->child_count > 0)
                            + {{ $transaction->child_count }} Anak
                        @endif
                        @if($transaction->toddler_count > 0)
                            + {{ $transaction->toddler_count }} Balita
                        @endif
                        ({{ $transaction->adult_count + $transaction->child_count + ($transaction->toddler_count ?? 0) }} total)
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Pembayaran</span>
                    <span class="font-semibold text-lg text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                    <span class="font-semibold dark:text-white">{{ strtoupper($transaction->payment_method) }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Status Pembayaran</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                           ($transaction->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                </div>
                
                @if($transaction->payment_reference)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Referensi Pembayaran</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->payment_reference }}</span>
                </div>
                @endif
                
                @if($transaction->paid_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibayar Pada</span>
                    <span class="font-semibold">{{ $transaction->paid_at->format('d M Y H:i') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibuat Pada</span>
                    <span class="font-semibold">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kasir</span>
                    <span class="font-semibold">{{ $transaction->creator->name ?? 'System' }}</span>
                </div>
                
                @if($transaction->notes)
                <div class="pt-4 border-t border-gray-200">
                    <span class="text-gray-600 block mb-2">Catatan</span>
                    <p class="text-gray-900">{{ $transaction->notes }}</p>
                </div>
                @endif

                @if($transaction->payment_status !== 'paid')
                <div class="pt-4 border-t border-gray-200">
                    <form action="{{ route('transactions.confirm-payment', $transaction) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label for="payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Konfirmasi Pembayaran
                            </label>
                            <input type="text" name="payment_reference" id="payment_reference" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400" 
                                   placeholder="Masukkan referensi pembayaran..."
                                   value="{{ $transaction->payment_reference }}"
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-full">
                            Konfirmasi Pembayaran Lunas
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Schedule Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Jadwal</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tujuan</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->schedule->destination->departure_location }} → {{ $transaction->schedule->destination->destination_location }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Kode Tujuan</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->schedule->destination->code }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nama Jadwal</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->schedule->name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Jam Keberangkatan</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->schedule->departure_time->format('H:i') }} WIB</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Kapasitas Speedboat</span>
                    <span class="font-semibold dark:text-white">{{ $transaction->schedule->capacity }} penumpang</span>
                </div>
                
                @if($transaction->schedule->destination->description)
                <div class="pt-4 border-t border-gray-200">
                    <span class="text-gray-600 dark:text-gray-400 block mb-2">Deskripsi Tujuan</span>
                    <p class="text-gray-900 dark:text-white">{{ $transaction->schedule->destination->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- WooCommerce Sync Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 {{ $transaction->is_synced ? 'bg-green-50 dark:bg-green-900/20' : ($transaction->sync_error ? 'bg-red-50 dark:bg-red-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') }}">
            <h3 class="text-lg font-semibold flex items-center {{ $transaction->is_synced ? 'text-green-900 dark:text-green-100' : ($transaction->sync_error ? 'text-red-900 dark:text-red-100' : 'text-yellow-900 dark:text-yellow-100') }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                WooCommerce Sync Status
            </h3>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">Status Sync</span>
                @if($transaction->is_synced)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Synced to WooCommerce
                    </span>
                @elseif($transaction->sync_error)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Sync Failed
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Sync
                    </span>
                @endif
            </div>

            @if($transaction->woocommerce_order_id)
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">WooCommerce Order ID</span>
                <a href="https://naikspeed.com/wp-admin/post.php?post={{ $transaction->woocommerce_order_id }}&action=edit"
                   target="_blank"
                   class="inline-flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                    #{{ $transaction->woocommerce_order_id }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
            @endif

            @if($transaction->synced_at)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Synced At</span>
                <span class="font-semibold dark:text-white">
                    {{ $transaction->synced_at->format('d M Y H:i') }}
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $transaction->synced_at->diffForHumans() }})</span>
                </span>
            </div>
            @endif

            @if($transaction->sync_error)
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400 block mb-2">Error Message</span>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    <p class="text-sm text-red-800 dark:text-red-200">{{ $transaction->sync_error }}</p>
                </div>
            </div>
            @endif

            @if(!$transaction->is_synced && !$transaction->woocommerce_order_id)
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <svg class="w-4 h-4 inline text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Transaksi ini belum tersinkronisasi ke WooCommerce. Sync otomatis akan berjalan setiap 5 menit, atau Anda bisa melakukan sync manual.
                </p>
                @if(Auth::user()->isAdmin())
                <a href="{{ route('sync.status') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Go to Sync Dashboard
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Tickets List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Tiket ({{ $transaction->tickets->count() }})</h3>
            <a href="{{ route('transactions.print', $transaction) }}?autoprint=1" target="_blank" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Auto Print
            </a>
        </div>
        
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kode Tiket
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama Penumpang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tipe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Harga
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Validasi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($transaction->tickets as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $ticket->ticket_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ $ticket->passenger_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $ticket->passenger_type === 'adult' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       ($ticket->passenger_type === 'child' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                       ($ticket->passenger_type === 'toddler' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                    {{ $ticket->passenger_type === 'adult' ? 'Dewasa' :
                                       ($ticket->passenger_type === 'child' ? 'Anak' :
                                       ($ticket->passenger_type === 'toddler' ? 'Balita' : 'Unknown')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                Rp {{ number_format($ticket->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $ticket->status === 'active' ? 'bg-blue-100 text-blue-800' : 
                                       ($ticket->status === 'used' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                @if($ticket->validated_at)
                                    <div class="text-xs">
                                        <div class="text-green-600 font-medium">✅ Tervalidasi</div>
                                        <div class="dark:text-gray-300">{{ $ticket->validated_at->format('d M Y H:i') }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Belum divalidasi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection