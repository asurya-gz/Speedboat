@extends('layouts.app')

@section('title', 'Detail Speedboat')

@section('header-actions')
    <div class="flex space-x-3">
        @if(Auth::user()->isAdmin())
            <a href="{{ route('speedboats.edit', $speedboat) }}" class="btn btn-warning">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Speedboat
            </a>
        @endif
        <a href="{{ route('speedboats.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M8 21l4-7 4 7M3 4h18M4 4h16v4a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Detail Speedboat: {{ $speedboat->name }}
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Utama -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">
                        Informasi Utama
                    </h4>
                    
                    <!-- Kode Speedboat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Kode Speedboat
                        </label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                {{ $speedboat->code }}
                            </span>
                        </div>
                    </div>

                    <!-- Nama Speedboat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Nama Speedboat
                        </label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $speedboat->name }}
                        </p>
                    </div>

                    <!-- Kapasitas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Kapasitas Penumpang
                        </label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-lg font-semibold text-green-600 dark:text-green-300">
                                {{ $speedboat->capacity }} orang
                            </span>
                        </div>
                    </div>

                    <!-- Tipe Speedboat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Tipe Speedboat
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            @if($speedboat->type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    {{ $speedboat->type }}
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 italic">-</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Status & Informasi Tambahan -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2">
                        Status & Informasi
                    </h4>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Status
                        </label>
                        <div class="flex items-center">
                            @if($speedboat->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-200 text-green-800 dark:text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tanggal Dibuat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Tanggal Dibuat
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $speedboat->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <!-- Tanggal Update -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Terakhir Diupdate
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $speedboat->updated_at->format('d F Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            @if($speedboat->description)
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">
                        Deskripsi
                    </h4>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $speedboat->description }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Statistik -->
            <div class="mt-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">
                    Statistik
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Jadwal -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Jadwal</p>
                                <p class="text-2xl font-semibold text-blue-900 dark:text-blue-300">
                                    {{ $speedboat->schedules->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Aktif -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">Jadwal Aktif</p>
                                <p class="text-2xl font-semibold text-green-900 dark:text-green-300">
                                    {{ $speedboat->schedules->where('is_active', true)->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Efisiensi -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Efisiensi</p>
                                <p class="text-2xl font-semibold text-purple-900 dark:text-purple-300">
                                    @php
                                        $totalSchedules = $speedboat->schedules->count();
                                        $activeSchedules = $speedboat->schedules->where('is_active', true)->count();
                                        $efficiency = $totalSchedules > 0 ? round(($activeSchedules / $totalSchedules) * 100) : 0;
                                    @endphp
                                    {{ $efficiency }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->isAdmin())
                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 flex items-center justify-end space-x-4">
                    <form action="{{ route('speedboats.toggle-status', $speedboat) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-200 
                                       {{ $speedboat->is_active ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
                            @if($speedboat->is_active)
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                Nonaktifkan
                            @else
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Aktifkan
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('speedboats.destroy', $speedboat) }}" 
                          method="POST" 
                          class="inline-block delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 delete-btn"
                                data-speedboat-name="{{ $speedboat->name }}">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Speedboat
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden z-50 transition-opacity duration-300">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="modalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Hapus</h3>
                    </div>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-300 text-sm">
                    Apakah Anda yakin ingin menghapus speedboat
                    <span class="font-semibold text-gray-900 dark:text-white" id="speedboatName"></span>?
                </p>
                <p class="text-red-600 dark:text-red-400 text-xs mt-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" 
                        id="cancelBtn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Batal
                </button>
                <button type="button" 
                        id="confirmBtn"
                        style="background-color: #dc2626; color: white;"
                        class="px-4 py-2 text-sm font-medium border border-transparent rounded-lg 
                               hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 
                               transition-colors duration-200 shadow-lg hover:shadow-xl">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('modalContent');
    const speedboatNameSpan = document.getElementById('speedboatName');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    let currentForm = null;

    // Show modal with animation
    function showModal() {
        deleteModal.classList.remove('hidden');
        setTimeout(() => {
            deleteModal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    // Hide modal with animation
    function hideModal() {
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        deleteModal.classList.add('opacity-0');
        setTimeout(() => {
            deleteModal.classList.add('hidden');
            currentForm = null;
        }, 300);
    }

    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentForm = this.closest('.delete-form');
            const speedboatName = this.getAttribute('data-speedboat-name');
            speedboatNameSpan.textContent = speedboatName;
            showModal();
        });
    });

    // Handle cancel button
    cancelBtn.addEventListener('click', hideModal);

    // Handle confirm button
    confirmBtn.addEventListener('click', function() {
        if (currentForm) {
            // Add loading state
            this.innerHTML = `
                <svg class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menghapus...
            `;
            this.disabled = true;
            
            // Submit the form
            currentForm.submit();
        }
    });

    // Handle click outside modal
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            hideModal();
        }
    });

    // Handle ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            hideModal();
        }
    });
});
</script>

<style>
/* Custom styles for smooth animations */
#deleteModal {
    transition: opacity 300ms ease-in-out;
}

#deleteModal.hidden {
    opacity: 0;
}

#modalContent {
    transition: transform 300ms ease-in-out;
}

#modalContent.scale-95 {
    transform: scale(0.95);
}

#modalContent.scale-100 {
    transform: scale(1);
}

/* Backdrop blur effect */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection