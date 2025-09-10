@extends('layouts.app')

@section('title', 'Edit Destinasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Destinasi: {{ $destination->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('destinations.update', $destination) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                    Nama Destinasi
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $destination->name) }}"
                                       placeholder="Contoh: Pulau Tidung"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">
                                    <i class="bi bi-tag text-primary me-1"></i>
                                    Kode Destinasi
                                </label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $destination->code) }}"
                                       placeholder="Contoh: PTD"
                                       style="text-transform: uppercase"
                                       maxlength="10"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maksimal 10 karakter, akan otomatis kapital</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="adult_price" class="form-label">
                                    <i class="bi bi-person text-success me-1"></i>
                                    Harga Dewasa
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('adult_price') is-invalid @enderror" 
                                           id="adult_price" 
                                           name="adult_price" 
                                           value="{{ old('adult_price', $destination->adult_price) }}"
                                           placeholder="50000"
                                           min="0"
                                           required>
                                </div>
                                @error('adult_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="child_price" class="form-label">
                                    <i class="bi bi-person-hearts text-info me-1"></i>
                                    Harga Anak
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('child_price') is-invalid @enderror" 
                                           id="child_price" 
                                           name="child_price" 
                                           value="{{ old('child_price', $destination->child_price) }}"
                                           placeholder="30000"
                                           min="0"
                                           required>
                                </div>
                                @error('child_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-text-paragraph text-primary me-1"></i>
                            Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Deskripsi destinasi (opsional)">{{ old('description', $destination->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $destination->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Destinasi aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>
                            Update Destinasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto uppercase code field
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush
@endsection