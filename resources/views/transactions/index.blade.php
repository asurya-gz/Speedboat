@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h2>
                <p class="text-gray-600 mt-1">Kelola semua transaksi penjualan tiket</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Transaksi Baru
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

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    Transaksi Terbaru ({{ $transactions->total() }} total)
                </h3>
                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">Tampilkan:</label>
                    <select id="perPage" 
                            class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium min-w-[60px]"
                            onchange="changePerPage(this.value)">
                        <option value="3" {{ $perPage == 3 ? 'selected' : '' }}>3</option>
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    </select>
                    <span class="text-sm text-gray-700 dark:text-gray-300">data per halaman</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Penumpang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Jadwal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->transaction_code }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->passenger_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->adult_count }} Dewasa
                                    @if($transaction->child_count > 0)
                                        + {{ $transaction->child_count }} Anak
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->schedule->destination->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->schedule->departure_date->format('d M') }} - 
                                    {{ $transaction->schedule->departure_time->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $transaction->adult_count + $transaction->child_count }} tiket
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ strtoupper($transaction->payment_method) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaction->payment_status === 'paid' ? 'bg-green-100 dark:bg-green-200 text-green-800 dark:text-green-800' : 
                                       ($transaction->payment_status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-200 text-yellow-800 dark:text-yellow-800' : 'bg-red-100 dark:bg-red-200 text-red-800 dark:text-red-800') }}">
                                    {{ ucfirst($transaction->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('transactions.show', $transaction) }}" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                    Detail
                                </a>
                                <a href="{{ route('transactions.print', $transaction) }}" target="_blank"
                                   class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                    Print
                                </a>
                                @if($transaction->payment_status !== 'paid')
                                <form action="{{ route('transactions.confirm-payment', $transaction) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="payment_reference" value="Manual Confirmation">
                                    <button type="submit" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                            onclick="return confirm('Konfirmasi pembayaran lunas?')">
                                        Konfirmasi
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada transaksi</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat transaksi baru</p>
                                    <div class="mt-6">
                                        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Buat Transaksi Pertama
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-4 border-t border-gray-200 dark:border-gray-600 sm:px-6">
            <div class="flex justify-end">
                @if($transactions->hasPages())
                    {{ $transactions->appends(['per_page' => $perPage])->links() }}
                @else
                    <!-- Custom pagination for single page with arrows -->
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1">
                        <!-- Previous Button (disabled) -->
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 cursor-not-allowed rounded-l-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                        
                        <!-- Current Page -->
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default">
                            1
                        </span>
                        
                        <!-- Next Button (disabled) -->
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 cursor-not-allowed rounded-r-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </nav>
                @endif
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

<script>
function changePerPage(perPage) {
    // Get current URL
    const url = new URL(window.location);
    
    // Update per_page parameter
    url.searchParams.set('per_page', perPage);
    
    // Remove page parameter to go back to first page
    url.searchParams.delete('page');
    
    // Redirect to new URL
    window.location.href = url.toString();
}
</script>
@endsection