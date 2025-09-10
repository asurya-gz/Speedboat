@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-600 text-white">
            <h3 class="text-lg font-semibold flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Jadwal Keberangkatan
            </h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('schedules.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Destinasi -->
                <div>
                    <label for="destination_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Destinasi
                    </label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('destination_id') border-red-500 @enderror" 
                            id="destination_id" 
                            name="destination_id" 
                            required>
                        <option value="">Pilih Destinasi...</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" 
                                    {{ old('destination_id') == $destination->id ? 'selected' : '' }}
                                    data-adult-price="{{ $destination->adult_price }}"
                                    data-child-price="{{ $destination->child_price }}">
                                {{ $destination->code }} - {{ $destination->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('destination_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Keberangkatan -->
                    <div>
                        <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Tanggal Keberangkatan
                        </label>
                        <input type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('departure_date') border-red-500 @enderror" 
                               id="departure_date" 
                               name="departure_date" 
                               value="{{ old('departure_date') }}"
                               min="{{ date('Y-m-d') }}"
                               required>
                        @error('departure_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Waktu Keberangkatan -->
                    <div>
                        <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Waktu Keberangkatan
                        </label>
                        <input type="time" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('departure_time') border-red-500 @enderror" 
                               id="departure_time" 
                               name="departure_time" 
                               value="{{ old('departure_time') }}"
                               required>
                        @error('departure_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kapasitas -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Kapasitas Penumpang
                    </label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror" 
                           id="capacity" 
                           name="capacity" 
                           value="{{ old('capacity', 50) }}"
                           placeholder="50"
                           min="1"
                           max="200"
                           required>
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Maksimal 200 penumpang per jadwal</p>
                </div>

                <!-- Status Aktif -->
                <div>
                    <div class="flex items-center">
                        <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="ml-2 text-sm font-medium text-gray-700" for="is_active">
                            <svg class="w-4 h-4 inline text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Jadwal aktif
                        </label>
                    </div>
                </div>

                <!-- Preview harga -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hidden" id="price-preview">
                    <h6 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Preview Harga Tiket
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Dewasa</div>
                                <div class="text-sm text-green-600 font-semibold" id="adult-price">-</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-cyan-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Anak</div>
                                <div class="text-sm text-cyan-600 font-semibold" id="child-price">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('destination_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const pricePreview = document.getElementById('price-preview');
        
        if (selected.value) {
            const adultPrice = selected.getAttribute('data-adult-price');
            const childPrice = selected.getAttribute('data-child-price');
            
            document.getElementById('adult-price').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(adultPrice);
            document.getElementById('child-price').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(childPrice);
            
            pricePreview.classList.remove('hidden');
        } else {
            pricePreview.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection