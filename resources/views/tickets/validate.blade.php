@extends('layouts.app')

@section('title', 'Validasi Tiket QR Code')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Validasi Tiket Boarding</h2>
                <p class="text-gray-600 mt-1">Scan QR Code tiket penumpang untuk validasi boarding</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Scanner Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Scan QR Code</h3>
            </div>
            
            <div class="p-6">
                <!-- Camera Preview -->
                <div class="mb-4">
                    <div id="qr-reader" style="width: 100%;"></div>
                    <div id="qr-reader-results"></div>
                </div>

                <!-- Manual Input Alternative -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Atau Input Manual</h4>
                    <div class="flex space-x-2">
                        <input type="text" id="manual-qr-input" class="form-input flex-1" 
                               placeholder="Masukkan kode tiket atau scan hasil QR...">
                        <button onclick="validateManualInput()" class="btn btn-primary">
                            Validasi
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Format: TKT-123-001 atau paste hasil scan QR
                    </p>
                </div>

                <!-- Search Ticket -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Cari Tiket</h4>
                    <div class="flex space-x-2">
                        <input type="text" id="search-ticket-input" class="form-input flex-1" 
                               placeholder="Cari berdasarkan kode tiket...">
                        <button onclick="searchTicket()" class="btn btn-info">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Hasil Validasi</h3>
            </div>
            
            <div class="p-6">
                <!-- Loading State -->
                <div id="loading-state" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Memproses validasi...</p>
                </div>

                <!-- Initial State -->
                <div id="initial-state" class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Siap untuk Scan</h3>
                    <p class="text-gray-600">Arahkan kamera ke QR code tiket atau input manual</p>
                </div>

                <!-- Success State -->
                <div id="success-state" class="hidden">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">✅ VALID - Boleh Naik!</h3>
                        <p class="text-green-600" id="success-message"></p>
                    </div>
                    
                    <!-- Ticket Details -->
                    <div id="ticket-details" class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Error State -->
                <div id="error-state" class="hidden">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-red-800 mb-2">❌ TIDAK VALID</h3>
                        <p class="text-red-600" id="error-message"></p>
                    </div>
                    
                    <!-- Error Ticket Details (if available) -->
                    <div id="error-ticket-details" class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Reset Button -->
                <div class="text-center mt-4">
                    <button onclick="resetValidation()" class="btn btn-secondary">
                        Scan Tiket Lain
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Code Reader Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrcodeScanner = null;
let isScanning = false;

document.addEventListener('DOMContentLoaded', function() {
    initQRScanner();
});

function initQRScanner() {
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0
        },
        false
    );
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    isScanning = true;
}

function onScanSuccess(decodedText, decodedResult) {
    if (!isScanning) return;
    
    isScanning = false;
    html5QrcodeScanner.clear();
    
    validateQRCode(decodedText);
}

function onScanFailure(error) {
    // Handle scan failure, usually better to ignore
}

function validateQRCode(qrData) {
    showLoadingState();
    
    fetch('{{ route("tickets.validate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            qr_data: qrData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessState(data.message, data.ticket);
        } else {
            showErrorState(data.message, data.ticket);
        }
    })
    .catch(error => {
        showErrorState('Error: ' + error.message);
    });
}

function validateManualInput() {
    const input = document.getElementById('manual-qr-input').value.trim();
    if (!input) {
        alert('Masukkan kode tiket atau data QR');
        return;
    }
    
    validateQRCode(input);
}

function searchTicket() {
    const ticketCode = document.getElementById('search-ticket-input').value.trim();
    if (!ticketCode) {
        alert('Masukkan kode tiket');
        return;
    }
    
    showLoadingState();
    
    fetch('{{ route("tickets.search") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            ticket_code: ticketCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTicketInfo(data.ticket, 'info');
        } else {
            showErrorState(data.message);
        }
    })
    .catch(error => {
        showErrorState('Error: ' + error.message);
    });
}

function showLoadingState() {
    document.getElementById('initial-state').classList.add('hidden');
    document.getElementById('success-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('loading-state').classList.remove('hidden');
}

function showSuccessState(message, ticket) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('initial-state').classList.add('hidden');
    document.getElementById('success-state').classList.remove('hidden');
    
    document.getElementById('success-message').textContent = message;
    showTicketInfo(ticket, 'success');
}

function showErrorState(message, ticket = null) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('success-state').classList.add('hidden');
    document.getElementById('initial-state').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    
    document.getElementById('error-message').textContent = message;
    
    if (ticket) {
        showTicketInfo(ticket, 'error');
    } else {
        document.getElementById('error-ticket-details').style.display = 'none';
    }
}

function showTicketInfo(ticket, type) {
    const container = type === 'success' ? 'ticket-details' : 
                     type === 'error' ? 'error-ticket-details' : 'ticket-details';
    
    let statusClass = 'bg-gray-100 text-gray-800';
    let statusText = ticket.status;
    
    if (ticket.status === 'boarded') {
        statusClass = 'bg-green-100 text-green-800';
        statusText = 'Sudah Boarding';
    } else if (ticket.status === 'active') {
        statusClass = 'bg-blue-100 text-blue-800';
        statusText = 'Aktif';
    }
    
    const isValidated = ticket.validated_at ? true : false;
    const validatedInfo = isValidated ? 
        `<div class="text-sm text-gray-600">Divalidasi: ${new Date(ticket.validated_at).toLocaleString('id-ID')}</div>` : '';
    
    const html = `
        <div class="space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-semibold text-gray-900">${ticket.ticket_code}</h4>
                    <p class="text-sm text-gray-600">${ticket.passenger_name}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                    ${statusText}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Tujuan</div>
                    <div class="font-medium">${ticket.transaction.schedule.destination.name}</div>
                </div>
                <div>
                    <div class="text-gray-500">Tanggal</div>
                    <div class="font-medium">${new Date(ticket.transaction.schedule.departure_date).toLocaleDateString('id-ID')}</div>
                </div>
                <div>
                    <div class="text-gray-500">Jam</div>
                    <div class="font-medium">${ticket.transaction.schedule.departure_time}</div>
                </div>
                <div>
                    <div class="text-gray-500">Tipe</div>
                    <div class="font-medium">${ticket.passenger_type === 'adult' ? 'Dewasa' : 'Anak'}</div>
                </div>
            </div>
            
            <div class="text-sm">
                <div class="text-gray-500">Status Pembayaran</div>
                <div class="font-medium ${ticket.transaction.payment_status === 'paid' ? 'text-green-600' : 'text-red-600'}">
                    ${ticket.transaction.payment_status === 'paid' ? 'Lunas' : 'Belum Bayar'}
                </div>
            </div>
            
            ${validatedInfo}
        </div>
    `;
    
    document.getElementById(container).innerHTML = html;
    document.getElementById(container).style.display = 'block';
}

function resetValidation() {
    document.getElementById('success-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('initial-state').classList.remove('hidden');
    
    document.getElementById('manual-qr-input').value = '';
    document.getElementById('search-ticket-input').value = '';
    
    if (!isScanning) {
        initQRScanner();
    }
}
</script>
@endsection