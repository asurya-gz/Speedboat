@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . $transaction->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Transaksi</h2>
                <p class="text-gray-600 mt-1">{{ $transaction->transaction_code }}</p>
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
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Transaksi</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Kode Transaksi</span>
                    <span class="font-semibold">{{ $transaction->transaction_code }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Nama Penumpang</span>
                    <span class="font-semibold">{{ $transaction->passenger_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Jumlah Penumpang</span>
                    <span class="font-semibold">
                        {{ $transaction->adult_count }} Dewasa
                        @if($transaction->child_count > 0)
                            + {{ $transaction->child_count }} Anak
                        @endif
                        ({{ $transaction->adult_count + $transaction->child_count }} total)
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pembayaran</span>
                    <span class="font-semibold text-lg text-green-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-semibold">{{ strtoupper($transaction->payment_method) }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status Pembayaran</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                           ($transaction->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                </div>
                
                @if($transaction->payment_reference)
                <div class="flex justify-between">
                    <span class="text-gray-600">Referensi Pembayaran</span>
                    <span class="font-semibold">{{ $transaction->payment_reference }}</span>
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
                            <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Pembayaran
                            </label>
                            <input type="text" name="payment_reference" id="payment_reference" 
                                   class="form-input w-full" 
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
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Jadwal</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tujuan</span>
                    <span class="font-semibold">{{ $transaction->schedule->destination->name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kode Tujuan</span>
                    <span class="font-semibold">{{ $transaction->schedule->destination->code }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal Keberangkatan</span>
                    <span class="font-semibold">{{ $transaction->schedule->departure_date->format('d M Y') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Jam Keberangkatan</span>
                    <span class="font-semibold">{{ $transaction->schedule->departure_time->format('H:i') }} WIB</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kapasitas Speedboat</span>
                    <span class="font-semibold">{{ $transaction->schedule->capacity }} penumpang</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kursi Tersedia</span>
                    <span class="font-semibold">{{ $transaction->schedule->available_seats }} kursi</span>
                </div>
                
                @if($transaction->schedule->destination->description)
                <div class="pt-4 border-t border-gray-200">
                    <span class="text-gray-600 block mb-2">Deskripsi Tujuan</span>
                    <p class="text-gray-900">{{ $transaction->schedule->destination->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="bg-white rounded-lg shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Tiket ({{ $transaction->tickets->count() }})</h3>
            <a href="{{ route('transactions.print', $transaction) }}?autoprint=1" target="_blank" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Auto Print
            </a>
        </div>
        
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Tiket
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Penumpang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Harga
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validasi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->tickets as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $ticket->ticket_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->passenger_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $ticket->passenger_type === 'adult' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $ticket->passenger_type === 'adult' ? 'Dewasa' : 'Anak' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($ticket->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $ticket->status === 'active' ? 'bg-blue-100 text-blue-800' : 
                                       ($ticket->status === 'boarded' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($ticket->validated_at)
                                    <div class="text-xs">
                                        <div class="text-green-600 font-medium">✅ Tervalidasi</div>
                                        <div>{{ $ticket->validated_at->format('d M Y H:i') }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">Belum divalidasi</span>
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