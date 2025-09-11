@extends('layouts.app')

@section('title', 'Daftar Destinasi')

@section('header-actions')
    <a href="{{ route('destinations.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tambah Destinasi
    </a>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Daftar Destinasi
            </h3>
        </div>
        
        <div class="p-6">
            @if($destinations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Destinasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Dewasa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Anak</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($destinations as $destination)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $destination->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $destination->name }}</div>
                                    @if($destination->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($destination->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">
                                        Rp {{ number_format($destination->adult_price, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-cyan-600">
                                        Rp {{ number_format($destination->child_price, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($destination->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('destinations.show', $destination) }}" 
                                           class="text-cyan-600 hover:text-cyan-900"
                                           title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('destinations.edit', $destination) }}" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('destinations.destroy', $destination) }}" 
                                              method="POST" 
                                              class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 delete-btn"
                                                    title="Hapus"
                                                    data-destination-name="{{ $destination->name }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada destinasi</h3>
                    <p class="mt-2 text-sm text-gray-500">Tambahkan destinasi speedboat pertama Anda</p>
                    <div class="mt-6">
                        <a href="{{ route('destinations.create') }}" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Destinasi
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden z-50 transition-opacity duration-300">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="modalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    </div>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <p class="text-gray-600 text-sm">
                    Apakah Anda yakin ingin menghapus destinasi
                    <span class="font-semibold text-gray-900" id="destinationName"></span>?
                </p>
                <p class="text-red-600 text-xs mt-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" 
                        id="cancelBtn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
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
    const destinationNameSpan = document.getElementById('destinationName');
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
            const destinationName = this.getAttribute('data-destination-name');
            destinationNameSpan.textContent = destinationName;
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