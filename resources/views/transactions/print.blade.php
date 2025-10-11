<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Tiket - {{ $transaction->transaction_code }}</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        
        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
            .ticket {
                width: 80mm; /* Standard thermal printer width */
                margin: 0;
                padding: 8px;
                border: none;
                font-size: 9px;
            }
            .company-name {
                font-size: 12px;
            }
            .ticket-title {
                font-size: 10px;
            }
            .section-title {
                font-size: 8px;
            }
            .ticket-row {
                font-size: 8px;
            }
        }
        
        /* Portrait receipt format - narrow and long like Indomaret/Alfamart */
        .ticket {
            width: 300px;
            margin: 0 auto 30px;
            padding: 12px;
            border: 2px dashed #000;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        
        .ticket-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-tagline {
            font-size: 8px;
            margin-bottom: 4px;
        }
        
        .ticket-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ticket-body {
            font-size: 10px;
            margin: 8px 0;
            display: flex;
            flex-direction: column;
        }
        
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            word-wrap: break-word;
            align-items: flex-start;
        }
        
        .ticket-row .label {
            flex: 0 0 40%;
        }
        
        .ticket-row .value {
            flex: 1;
            text-align: right;
            font-weight: bold;
        }
        
        .ticket-row.center {
            justify-content: center;
            text-align: center;
        }
        
        .ticket-divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        
        .qr-code img {
            max-width: 80px;
            height: auto;
        }
        
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            margin: 8px 0 6px 0;
            text-transform: uppercase;
        }
        
        .section-divider {
            border-top: 1px dashed #ccc;
            margin: 6px 0 2px 0;
        }
        
        .ticket-footer {
            text-align: center;
            font-size: 7px;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            line-height: 1.2;
        }
        
        .important-notice {
            font-size: 8px;
            line-height: 1.3;
            margin: 8px 0;
            text-align: left;
        }
        
        .price-large {
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Controls for screen view */
        .print-controls {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .print-controls button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-print:hover {
            background: #0056b3;
        }
        
        .btn-back:hover {
            background: #545b62;
        }
        
        /* Status indicators */
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .large-text {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Print Controls (hidden when printing) -->
    <div class="print-controls no-print">
        <h2>Print Tiket - {{ $transaction->transaction_code }}</h2>
        <p>{{ $transaction->tickets->count() }} tiket akan dicetak</p>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Semua Tiket</button>
        <button onclick="window.history.back()" class="btn-back">← Kembali</button>
    </div>

    <!-- Generate ticket for each passenger -->
    @foreach($transaction->tickets as $index => $ticket)
    <div class="ticket">
        <!-- Header -->
        <div class="ticket-header">
            <div>══════════════════════════</div>
            <div class="company-name">SPEEDBOAT TICKETING</div>
            <div class="company-tagline">Fast • Safe • Reliable</div>
            <div class="ticket-title">TIKET SPEEDBOAT</div>
            <div>══════════════════════════</div>
        </div>
        
        <!-- Body -->
        <div class="ticket-body">
            <!-- Transaction Info -->
            <div class="section-title">INFORMASI TRANSAKSI</div>
            <div class="ticket-row">
                <span class="label">No. Transaksi:</span>
                <span class="value">{{ $transaction->transaction_code }}</span>
            </div>
            <div class="ticket-row">
                <span class="label">Kode Tiket:</span>
                <span class="value">{{ $ticket->ticket_code }}</span>
            </div>
            
            <div class="ticket-divider"></div>
            
            <!-- Passenger Info -->
            <div class="section-title">INFORMASI PENUMPANG</div>
            <div class="ticket-row">
                <span class="label">Nama:</span>
                <span class="value">{{ $ticket->passenger_name }}</span>
            </div>
            <div class="ticket-row">
                <span class="label">Tipe:</span>
                <span class="value">{{ $ticket->passenger_type === 'adult' ? 'DEWASA' : ($ticket->passenger_type === 'toddler' ? 'BALITA' : 'ANAK') }}</span>
            </div>
            @if($ticket->seat_number)
            <div class="ticket-row">
                <span class="label">No. Kursi:</span>
                <span class="value large-text">{{ $ticket->seat_number }}</span>
            </div>
            @endif
            
            <div class="ticket-divider"></div>
            
            <!-- Trip Info -->
            <div class="section-title">INFORMASI PERJALANAN</div>
            <div class="ticket-row" style="margin-bottom: 4px;">
                <span style="font-size: 9px;">KEBERANGKATAN:</span>
            </div>
            <div style="text-align: center; font-weight: bold; margin-bottom: 4px;">{{ $transaction->schedule->destination->departure_location }}</div>
            <div style="text-align: center; margin-bottom: 4px;">⬇</div>
            <div style="text-align: center; font-weight: bold; margin-bottom: 6px;">{{ $transaction->schedule->destination->destination_location }}</div>
            
            <div class="ticket-row">
                <span class="label">Jadwal:</span>
                <span class="value">{{ $transaction->schedule->name }}</span>
            </div>
            <div class="ticket-row">
                <span class="label">Jam Berangkat:</span>
                <span class="value">{{ $transaction->schedule->departure_time->format('H:i') }} WIB</span>
            </div>
            
            <div class="ticket-divider"></div>
            
            <!-- Payment Info -->
            <div class="section-title">INFORMASI PEMBAYARAN</div>
            <div class="ticket-row">
                <span class="label">Harga Tiket:</span>
                <span class="value price-large">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
            </div>
            <div class="ticket-row">
                <span class="label">Pembayaran:</span>
                <span class="value">{{ strtoupper($transaction->payment_method) }}</span>
            </div>
            <div class="ticket-row">
                <span class="label">Status:</span>
                <span class="value {{ $transaction->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                    {{ $transaction->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                </span>
            </div>
            @if($transaction->payment_reference)
            <div class="ticket-row">
                <span class="label">Referensi:</span>
                <span class="value">{{ $transaction->payment_reference }}</span>
            </div>
            @endif
            
            <!-- QR Code -->
            <div class="qr-code">
                <div style="margin-bottom: 6px; font-size: 8px; font-weight: bold;">SCAN UNTUK VALIDASI</div>
                {!! QrCode::size(80)->generate($ticket->qr_code) !!}
            </div>
            
            <div class="ticket-divider"></div>
            
            <!-- Important Notice -->
            <div class="section-title" style="color: #d63384;">⚠ PENTING!</div>
            <div class="important-notice">
                • Datang 30 menit sebelum keberangkatan<br>
                • Tunjukkan tiket kepada petugas boarding<br>
                • Tiket tidak dapat di-refund<br>
                • Simpan tiket hingga perjalanan selesai
            </div>
            
            @if($transaction->notes)
            <div class="section-divider"></div>
            <div class="section-title">CATATAN</div>
            <div style="font-size: 8px; text-align: left; margin: 4px 0;">
                {{ $transaction->notes }}
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="ticket-footer">
            <div class="ticket-divider"></div>
            <div>{{ now()->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ $transaction->creator->name ?? 'System' }}</div>
            <div>Terima kasih atas kepercayaan Anda!</div>
            <div>══════════════════════════</div>
        </div>
    </div>
    
    <!-- Page break except for last ticket -->
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach

    <script>
        // Auto print functionality
        function autoPrint() {
            // Small delay to ensure page is fully loaded
            setTimeout(() => {
                window.print();
                // Optionally redirect back after printing
                // window.history.back();
            }, 1000);
        }
        
        // Print event handlers
        window.addEventListener('afterprint', function() {
            console.log('Print dialog closed');
            // You can add logic here after printing is done
        });
        
        window.addEventListener('beforeprint', function() {
            console.log('Print dialog opened');
        });
        
        // Check if this page was opened for printing
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            autoPrint();
        }
    </script>
</body>
</html>