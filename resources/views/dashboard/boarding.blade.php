@extends('layouts.app')

@section('title', 'Dashboard Boarding')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Banner -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ $user->name }}!</h2>
                <p class="text-gray-600 mt-1">Dashboard Boarding - Kelola boarding dan validasi tiket</p>
            </div>
            <div class="flex-shrink-0">
                <svg class="w-16 h-16 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Today's Departures -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Keberangkatan Hari Ini</p>
                    @php
                        $todayDepartures = \App\Models\Schedule::where('departure_date', today())
                            ->where('is_active', true)
                            ->count();
                    @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ $todayDepartures }}</p>
                </div>
            </div>
        </div>

        <!-- Tickets Validated Today -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tiket Tervalidasi</p>
                    @php
                        $validatedTickets = \App\Models\Ticket::whereDate('validated_at', today())->count();
                    @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ $validatedTickets }}</p>
                </div>
            </div>
        </div>

        <!-- Passengers Boarded -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Penumpang Naik</p>
                    @php
                        $boardedPassengers = \App\Models\Transaction::whereHas('tickets', function($q) {
                            $q->whereDate('validated_at', today());
                        })->sum('adult_count') + \App\Models\Transaction::whereHas('tickets', function($q) {
                            $q->whereDate('validated_at', today());
                        })->sum('child_count');
                    @endphp
                    <p class="text-2xl font-bold text-gray-900">{{ $boardedPassengers }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Validasi QR Code</h3>
            <p class="text-gray-600 mb-4 text-sm">Scan QR code tiket untuk validasi boarding</p>
            <a href="{{ route('tickets.validate.form') }}" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                </svg>
                Scan QR Code
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cek Jadwal</h3>
            <p class="text-gray-600 mb-4 text-sm">Lihat jadwal keberangkatan hari ini</p>
            <a href="{{ route('schedules.index') }}" class="btn btn-info">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Lihat Jadwal
            </a>
        </div>
    </div>

    <!-- Today's Schedule Status -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Status Jadwal Hari Ini</h3>
        </div>
        <div class="p-6">
            @php
                $todaySchedules = \App\Models\Schedule::with(['destination', 'tickets'])
                    ->where('departure_date', today())
                    ->where('is_active', true)
                    ->orderBy('departure_time')
                    ->get();
            @endphp
            
            @if($todaySchedules->count() > 0)
                <div class="space-y-4">
                    @foreach($todaySchedules as $schedule)
                    @php
                        $now = now();
                        $departureDateTime = $schedule->departure_date->setTimeFromTimeString($schedule->departure_time->format('H:i:s'));
                        $isUpcoming = $departureDateTime > $now;
                        $isPast = $departureDateTime < $now->subMinutes(30);
                        $isBoarding = !$isUpcoming && !$isPast;
                        
                        $validatedTickets = $schedule->tickets()->whereNotNull('validated_at')->count();
                        $totalTickets = $schedule->tickets()->count();
                    @endphp
                    
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $schedule->destination->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $schedule->departure_time->format('H:i') }} - Kapasitas: {{ $schedule->capacity }}</p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                @if($isUpcoming)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Menunggu
                                    </span>
                                @elseif($isBoarding)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Boarding
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Total Tiket Terjual</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $totalTickets }}</div>
                            </div>
                            
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Tiket Tervalidasi</div>
                                <div class="text-2xl font-bold text-green-600">{{ $validatedTickets }}</div>
                            </div>
                            
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500">Sisa Kursi</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $schedule->available_seats }}</div>
                            </div>
                        </div>
                        
                        @if($totalTickets > 0)
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress Validasi</span>
                                <span>{{ $validatedTickets }}/{{ $totalTickets }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalTickets > 0 ? ($validatedTickets / $totalTickets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        @endif
                        
                        @if($isBoarding)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('tickets.validate.form') }}" class="btn btn-primary btn-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                                </svg>
                                Mulai Validasi
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal hari ini</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada keberangkatan yang dijadwalkan untuk hari ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection