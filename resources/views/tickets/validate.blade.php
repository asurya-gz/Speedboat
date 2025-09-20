@extends('layouts.app')

@section('title', 'Validasi Tiket QR Code')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Validasi Tiket Boarding</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Scan QR Code tiket penumpang untuk validasi boarding</p>
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scan QR Code</h3>
            </div>
            
            <div class="p-6">
                <!-- Scan Button -->
                <div class="text-center mb-4" id="scan-button-container">
                    <button onclick="startCamera()" id="start-scan-btn" class="btn btn-primary btn-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Mulai Scan QR Code
                    </button>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Klik untuk mengaktifkan kamera dan mulai scan tiket</p>
                </div>
                
                <!-- Camera Preview -->
                <div class="mb-4" id="camera-container" style="display: none;">
                    <div class="text-center mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            📷 Kamera Aktif - Arahkan ke QR Code
                        </span>
                    </div>
                    <div id="qr-reader" style="width: 100%; min-height: 300px; border: 2px dashed #e5e7eb; border-radius: 8px; background: #f9fafb;"></div>
                    <div id="qr-reader-results"></div>
                    <div class="text-center mt-3">
                        <button onclick="stopCamera()" class="btn btn-secondary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            Hentikan Scan
                        </button>
                    </div>
                </div>

                <!-- Manual Input Alternative -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Atau Input Manual</h4>
                    <div class="flex space-x-2">
                        <input type="text" id="manual-qr-input" class="form-input flex-1 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" 
                               placeholder="Masukkan kode tiket atau scan hasil QR...">
                        <button onclick="validateManualInput()" class="btn btn-primary">
                            Validasi
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Format: TKT-123-001 atau paste hasil scan QR
                    </p>
                    
                    <!-- Debug Info -->
                    <div class="mt-3 p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs" id="debug-info">
                        <strong>Status:</strong> <span id="debug-status">Memuat...</span><br>
                        <strong>Library:</strong> <span id="debug-library">Checking...</span><br>
                        <strong>Camera:</strong> <span id="debug-camera">Checking...</span><br>
                        <strong>Protocol:</strong> <span id="debug-protocol"><?= isset($_SERVER['HTTPS']) ? 'HTTPS' : 'HTTP' ?></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hasil Validasi</h3>
            </div>
            
            <div class="p-6">
                <!-- Loading State -->
                <div id="loading-state" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-300">Memproses validasi...</p>
                </div>

                <!-- Initial State -->
                <div id="initial-state" class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Siap untuk Scan</h3>
                    <p class="text-gray-600 dark:text-gray-300">Arahkan kamera ke QR code tiket atau input manual</p>
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
                    <div id="ticket-details" class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
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
                    <div id="error-ticket-details" class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
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
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>

<script>
let html5QrcodeScanner = null;
let isScanning = false;

document.addEventListener('DOMContentLoaded', function() {
    // Check if HTML5QRCode library is loaded
    checkLibraryAndCamera();
});

function checkLibraryAndCamera() {
    // Check if HTML5QRCode library is loaded
    if (typeof Html5QrcodeScanner === 'undefined') {
        console.error('HTML5QRCode library not loaded');
        updateDebugInfo('library', '❌ Belum dimuat');
        updateDebugInfo('status', 'Menunggu library...');
        setTimeout(checkLibraryAndCamera, 100); // Retry after 100ms
        return;
    }
    
    console.log('HTML5QRCode library loaded successfully');
    updateDebugInfo('library', '✅ Berhasil dimuat');
    updateDebugInfo('status', 'Library siap');
    checkCameraAvailability();
}

function checkCameraAvailability() {
    // Check if getUserMedia is available
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateDebugInfo('camera', '❌ Tidak tersedia');
        updateDebugInfo('status', 'Kamera tidak didukung browser');
        
        document.getElementById('start-scan-btn').disabled = true;
        document.getElementById('start-scan-btn').innerHTML = `
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Kamera Tidak Tersedia
        `;
        document.getElementById('start-scan-btn').className = 'btn btn-secondary btn-lg';
        return;
    }
    
    // Check if HTTPS or localhost (required for camera access)  
    const isSecureContext = location.protocol === 'https:' || 
                            location.hostname === 'localhost' || 
                            location.hostname === '127.0.0.1' ||
                            location.hostname === '0.0.0.0';
    
    if (!isSecureContext) {
        console.warn('Camera access requires HTTPS or localhost');
        updateDebugInfo('camera', '⚠️ Butuh HTTPS/localhost');
        updateDebugInfo('status', 'Akses via localhost:8000 untuk kamera');
        
        document.getElementById('start-scan-btn').innerHTML = `
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Akses via localhost:8000
        `;
        document.getElementById('start-scan-btn').className = 'btn btn-warning btn-lg';
        document.getElementById('start-scan-btn').disabled = false;
        return;
    }
    
    // Test camera availability with actual permission request
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            // Camera is available, stop the test stream
            stream.getTracks().forEach(track => track.stop());
            updateDebugInfo('camera', '✅ Siap digunakan');
            updateDebugInfo('status', 'Siap untuk scan');
        })
        .catch(function(error) {
            console.error('Camera test failed:', error);
            updateDebugInfo('camera', '❌ ' + error.name);
            updateDebugInfo('status', 'Kamera error: ' + error.message);
            
            if (error.name === 'NotAllowedError') {
                document.getElementById('start-scan-btn').innerHTML = `
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Izinkan Akses Kamera
                `;
            } else if (error.name === 'NotFoundError') {
                document.getElementById('start-scan-btn').innerHTML = `
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Kamera Tidak Ditemukan
                `;
                document.getElementById('start-scan-btn').disabled = true;
            }
        });
}

function startCamera() {
    // Check if library is still available
    if (typeof Html5QrcodeScanner === 'undefined') {
        updateDebugInfo('status', 'Library hilang - refresh halaman');
        showCameraError('Library QR Scanner belum dimuat. Silakan refresh halaman.');
        return;
    }
    
    updateDebugInfo('status', 'Meminta akses kamera...');
    
    document.getElementById('scan-button-container').style.display = 'none';
    document.getElementById('camera-container').style.display = 'block';
    
    // Show loading state
    document.getElementById('qr-reader').innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; color: #6b7280; background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 8px;">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
            <p style="font-weight: 500; margin-bottom: 8px;">Meminta akses kamera...</p>
            <small style="text-align: center; color: #9ca3af;">
                Pastikan memberikan izin akses kamera pada browser<br>
                Klik "Allow" atau "Izinkan" ketika diminta
            </small>
        </div>
    `;
    
    try {
        // Create scanner with improved configuration
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { 
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                showZoomSliderIfSupported: true,
                defaultZoomValueIfSupported: 1,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                verbose: false
            },
            false
        );
        
        // Add timeout for camera initialization
        const cameraTimeout = setTimeout(() => {
            if (!isScanning) {
                showCameraError('Gagal mengakses kamera. Pastikan kamera tidak digunakan aplikasi lain dan browser memiliki izin akses kamera.');
            }
        }, 10000); // 10 second timeout
        
        html5QrcodeScanner.render(
            (decodedText, decodedResult) => {
                clearTimeout(cameraTimeout);
                updateDebugInfo('status', 'QR Code berhasil di-scan!');
                onScanSuccess(decodedText, decodedResult);
            },
            (error) => {
                onScanFailure(error);
            }
        );
        
        isScanning = true;
        updateDebugInfo('status', 'Kamera aktif - siap scan QR');
        
    } catch (error) {
        console.error('Error starting camera:', error);
        showCameraError('Gagal memulai kamera: ' + error.message);
    }
}

function showCameraError(message) {
    document.getElementById('qr-reader').innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; color: #ef4444;">
            <svg style="width: 48px; height: 48px; margin-bottom: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h4 style="font-weight: 600; margin-bottom: 8px;">Gagal Mengakses Kamera</h4>
            <p style="text-align: center; margin-bottom: 16px;">${message}</p>
            <small style="text-align: center; color: #6b7280;">
                Pastikan:<br>
                • Browser memiliki akses kamera<br>
                • Kamera tidak digunakan aplikasi lain<br>
                • Gunakan HTTPS atau localhost
            </small>
        </div>
    `;
    
    // Show the start button again
    document.getElementById('camera-container').style.display = 'none';
    document.getElementById('scan-button-container').style.display = 'block';
}

function stopCamera() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
        html5QrcodeScanner = null;
    }
    isScanning = false;
    
    document.getElementById('camera-container').style.display = 'none';
    document.getElementById('scan-button-container').style.display = 'block';
}

function initQRScanner() {
    // This function is now replaced by startCamera()
}

function onScanSuccess(decodedText, decodedResult) {
    if (!isScanning) return;
    
    isScanning = false;
    html5QrcodeScanner.clear();
    
    validateQRCode(decodedText);
}

function onScanFailure(error) {
    // Handle scan failure
    console.log('Scan failed:', error);
    
    // Check for camera permission errors
    if (error && typeof error === 'string' && (
        error.includes('Permission') || 
        error.includes('NotAllowed') || 
        error.includes('NotFoundError') ||
        error.includes('NotReadableError') ||
        error.includes('camera') ||
        error.includes('getUserMedia')
    )) {
        isScanning = false;
        showCameraError('Akses kamera ditolak atau tidak dapat mengakses kamera. Pastikan memberikan izin akses kamera pada browser dan kamera tidak sedang digunakan aplikasi lain.');
        return;
    }
    
    // For other errors, we can ignore as they're usually just "no QR code found"
    // Just continue scanning without showing error to user
}

function validateQRCode(qrData) {
    showLoadingState();
    updateDebugInfo('status', 'Memvalidasi: ' + qrData);
    
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
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            updateDebugInfo('status', 'Validasi berhasil!');
            showSuccessState(data.message, data.ticket);
        } else {
            updateDebugInfo('status', 'Validasi gagal: ' + data.message);
            showErrorState(data.message, data.ticket);
        }
    })
    .catch(error => {
        console.error('Validation error:', error);
        updateDebugInfo('status', 'Error: ' + error.message);
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
    
    let statusClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
    let statusText = ticket.status;
    
    if (ticket.status === 'used') {
        statusClass = 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200';
        statusText = 'Sudah Boarding';
    } else if (ticket.status === 'active') {
        statusClass = 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200';
        statusText = 'Aktif';
    }
    
    const isValidated = ticket.validated_at ? true : false;
    const validatedInfo = isValidated ? 
        `<div class="text-sm text-gray-600 dark:text-gray-400">Divalidasi: ${new Date(ticket.validated_at).toLocaleString('id-ID')}</div>` : '';
    
    const html = `
        <div class="space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">${ticket.ticket_code}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">${ticket.passenger_name}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                    ${statusText}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Tujuan</div>
                    <div class="font-medium dark:text-white">${ticket.transaction.schedule.destination.name}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Tanggal</div>
                    <div class="font-medium dark:text-white">${new Date(ticket.transaction.schedule.departure_date).toLocaleDateString('id-ID')}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Jam</div>
                    <div class="font-medium dark:text-white">${ticket.transaction.schedule.departure_time}</div>
                </div>
                <div>
                    <div class="text-gray-500 dark:text-gray-400">Tipe</div>
                    <div class="font-medium dark:text-white">${ticket.passenger_type === 'adult' ? 'Dewasa' : (ticket.passenger_type === 'child' ? 'Anak' : 'Balita')}</div>
                </div>
            </div>
            
            <div class="text-sm">
                <div class="text-gray-500 dark:text-gray-400">Status Pembayaran</div>
                <div class="font-medium ${ticket.transaction.payment_status === 'paid' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
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
    
    // Stop camera and show start button again
    if (isScanning && html5QrcodeScanner) {
        stopCamera();
    }
}

function updateDebugInfo(type, message) {
    const debugElement = document.getElementById('debug-' + type);
    if (debugElement) {
        debugElement.textContent = message;
    }
}
</script>
@endsection