@extends('layouts.app')

@section('title', 'Detail Destinasi')

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('destinations.edit', $destination) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>
            Edit
        </a>
        <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Detail Destinasi
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Destinasi</label>
                            <h4 class="text-primary">{{ $destination->name }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Kode Destinasi</label>
                            <h4>
                                <span class="badge bg-primary fs-6">{{ $destination->code }}</span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Harga Dewasa</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-success me-2 fs-4"></i>
                                <h4 class="text-success mb-0">
                                    Rp {{ number_format($destination->adult_price, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Harga Anak</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-hearts text-info me-2 fs-4"></i>
                                <h4 class="text-info mb-0">
                                    Rp {{ number_format($destination->child_price, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                @if($destination->description)
                <div class="mb-3">
                    <label class="form-label text-muted">Deskripsi</label>
                    <div class="border p-3 rounded bg-light">
                        {{ $destination->description }}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                @if($destination->is_active)
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Dibuat</label>
                            <div class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                {{ $destination->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calendar-event me-1"></i>
                    Jadwal Aktif
                </h6>
            </div>
            <div class="card-body">
                @if($destination->schedules->where('is_active', true)->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($destination->schedules->where('is_active', true)->take(5) as $schedule)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $schedule->departure_date->format('d M Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $schedule->departure_time->format('H:i') }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Tersedia</small>
                                    <br>
                                    <span class="badge bg-info">{{ $schedule->available_seats }}/{{ $schedule->capacity }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($destination->schedules->where('is_active', true)->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('schedules.index') }}?destination={{ $destination->id }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Jadwal
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                        <p>Belum ada jadwal</p>
                        <a href="{{ route('schedules.create') }}?destination={{ $destination->id }}" class="btn btn-sm btn-primary">
                            Tambah Jadwal
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-1"></i>
                    Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $destination->schedules->count() }}</h4>
                            <small class="text-muted">Total Jadwal</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $destination->schedules->where('is_active', true)->count() }}</h4>
                        <small class="text-muted">Jadwal Aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection