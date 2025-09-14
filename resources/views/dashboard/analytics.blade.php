@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Monitor performa dan statistik bisnis speedboat Anda</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Last updated:</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ now()->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($analytics['totalRevenue'], 0, ',', '.') }}</p>
                    <p class="text-sm {{ $analytics['revenueChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $analytics['revenueChange'] >= 0 ? '+' : '' }}{{ number_format($analytics['revenueChange'], 1) }}% dari bulan lalu
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Tickets Sold -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Tiket Terjual</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analytics['totalTicketsSold']) }}</p>
                    <p class="text-sm {{ $analytics['ticketsChange'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $analytics['ticketsChange'] >= 0 ? '+' : '' }}{{ number_format($analytics['ticketsChange'], 1) }}% dari bulan lalu
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Destinations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Destinasi</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['totalDestinations'] }}</p>
                    <p class="text-sm text-purple-600 dark:text-purple-400">Destinasi aktif</p>
                </div>
            </div>
        </div>

        <!-- Active Schedules -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Jadwal Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analytics['activeSchedules'] }}</p>
                    <p class="text-sm text-orange-600 dark:text-orange-400">15 hari ke depan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pendapatan Bulanan</h3>
                <select class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 dark:bg-gray-700 dark:text-white">
                    <option>6 bulan terakhir</option>
                    <option>3 bulan terakhir</option>
                    <option>12 bulan terakhir</option>
                </select>
            </div>
            <div class="relative">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Popular Destinations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Destinasi Populer</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Bulan ini</div>
            </div>
            <div class="relative">
                <canvas id="destinationChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Ticket Sales Trends -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Trend Penjualan Tiket</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-lg">7 Hari</button>
                <button class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">30 Hari</button>
                <button class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">90 Hari</button>
            </div>
        </div>
        <div class="relative">
            <canvas id="salesTrendChart" width="800" height="300"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Aktivitas Terbaru</h3>
        <div class="space-y-4">
            @forelse($analytics['recentActivities'] as $activity)
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">
                            Tiket terjual untuk {{ $activity->schedule->destination->name }} - 
                            {{ $activity->adult_count }} Dewasa
                            @if($activity->child_count > 0), {{ $activity->child_count }} Anak @endif
                            @if($activity->toddler_count > 0), {{ $activity->toddler_count }} Balita @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas terbaru</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart - using real data
    const monthlyRevenueData = @json($analytics['monthlyRevenue']);
    const revenueLabels = monthlyRevenueData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1];
    });
    const revenueValues = monthlyRevenueData.map(item => item.revenue / 1000000); // Convert to millions

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels.length > 0 ? revenueLabels : ['No Data'],
            datasets: [{
                label: 'Pendapatan (Juta Rp)',
                data: revenueValues.length > 0 ? revenueValues : [0],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                }
            }
        }
    });

    // Popular Destinations Chart - using real data
    const popularDestinationsData = @json($analytics['popularDestinations']);
    const destinationLabels = popularDestinationsData.map(item => item.name);
    const destinationValues = popularDestinationsData.map(item => item.transaction_count);

    const destinationCtx = document.getElementById('destinationChart').getContext('2d');
    new Chart(destinationCtx, {
        type: 'doughnut',
        data: {
            labels: destinationLabels.length > 0 ? destinationLabels : ['No Data'],
            datasets: [{
                data: destinationValues.length > 0 ? destinationValues : [1],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 101, 101, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Sales Trend Chart - using real data
    const dailySalesData = @json($analytics['dailySales']);
    
    // Create last 7 days labels
    const last7Days = [];
    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        last7Days.push({
            date: date.toISOString().split('T')[0],
            dayName: dayNames[date.getDay()]
        });
    }
    
    // Map sales data to days
    const salesLabels = last7Days.map(day => day.dayName);
    const salesValues = last7Days.map(day => {
        const found = dailySalesData.find(sale => sale.date === day.date);
        return found ? parseInt(found.tickets_sold) : 0;
    });

    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'bar',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Tiket Terjual',
                data: salesValues,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush