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
        }
        
        /* Horizontal ticket layout - wider format */
        .ticket {
            width: 600px;
            margin: 0 auto 30px;
            padding: 15px;
            border: 2px dashed #000;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        
        .ticket-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-tagline {
            font-size: 10px;
            margin-bottom: 4px;
        }
        
        .ticket-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ticket-body {
            font-size: 11px;
            margin: 8px 0;
            display: flex;
            flex-direction: row;
            gap: 20px;
        }
        
        .ticket-left {
            flex: 1;
            padding-right: 15px;
        }
        
        .ticket-center {
            flex: 0 0 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-left: 1px dashed #000;
            border-right: 1px dashed #000;
            padding: 0 15px;
        }
        
        .ticket-right {
            flex: 1;
            padding-left: 15px;
        }
        
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            word-wrap: break-word;
        }
        
        .ticket-row.center {
            justify-content: center;
            text-align: center;
        }
        
        .ticket-row strong {
            font-weight: bold;
        }
        
        .ticket-divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .qr-code {
            text-align: center;
            margin: 8px 0;
        }
        
        .qr-code img {
            max-width: 90px;
            height: auto;
        }
        
        .section-divider {
            border-bottom: 1px dashed #ccc;
            margin: 8px 0;
            padding-bottom: 4px;
        }
        
        .ticket-footer {
            text-align: center;
            font-size: 8px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #000;
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
            <div class="company-name">SPEEDBOAT TICKETING</div>
            <div class="company-tagline">Fast • Safe • Reliable</div>
            <div class="ticket-title">TIKET SPEEDBOAT</div>
        </div>
        
        <!-- Body -->
        <div class="ticket-body">
            <!-- Left Section -->
            <div class="ticket-left">
                <!-- Transaction Info -->
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px;">INFORMASI TRANSAKSI</div>
                </div>
                <div class="ticket-row">
                    <span>No. Transaksi:</span>
                    <strong>{{ $transaction->transaction_code }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Kode Tiket:</span>
                    <strong>{{ $ticket->ticket_code }}</strong>
                </div>
                
                <!-- Passenger Info -->
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px;">INFORMASI PENUMPANG</div>
                </div>
                <div class="ticket-row">
                    <span>Nama:</span>
                    <strong>{{ $ticket->passenger_name }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Tipe:</span>
                    <strong>{{ $ticket->passenger_type === 'adult' ? 'DEWASA' : ($ticket->passenger_type === 'toddler' ? 'BALITA' : 'ANAK') }}</strong>
                </div>
                
                <!-- Trip Info -->
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px;">INFORMASI PERJALANAN</div>
                </div>
                <div class="ticket-row">
                    <span>Tujuan:</span>
                    <strong>{{ $transaction->schedule->destination->departure_location }} → {{ $transaction->schedule->destination->destination_location }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Jadwal:</span>
                    <strong>{{ $transaction->schedule->name }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Jam:</span>
                    <strong>{{ $transaction->schedule->departure_time->format('H:i') }} WIB</strong>
                </div>
            </div>
            
            <!-- Center Section - QR Code -->
            <div class="ticket-center">
                <div class="qr-code">
                    <div style="margin-bottom: 6px; font-size: 10px; font-weight: bold;">SCAN UNTUK VALIDASI</div>
                    {!! QrCode::size(90)->generate($ticket->qr_code) !!}
                </div>
            </div>
            
            <!-- Right Section -->
            <div class="ticket-right">
                <!-- Payment Info -->
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px;">INFORMASI PEMBAYARAN</div>
                </div>
                <div class="ticket-row">
                    <span>Harga:</span>
                    <strong>Rp {{ number_format($ticket->price, 0, ',', '.') }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Pembayaran:</span>
                    <strong>{{ strtoupper($transaction->payment_method) }}</strong>
                </div>
                <div class="ticket-row">
                    <span>Status:</span>
                    <strong class="{{ $transaction->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ $transaction->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                    </strong>
                </div>
                @if($transaction->payment_reference)
                <div class="ticket-row">
                    <span>Ref:</span>
                    <strong>{{ $transaction->payment_reference }}</strong>
                </div>
                @endif
                
                <!-- Important Notice -->
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px; color: #d63384;">PENTING!</div>
                </div>
                <div style="font-size: 9px; line-height: 1.3;">
                    • Harap datang 30 menit sebelum keberangkatan<br>
                    • Tunjukkan tiket ini kepada petugas boarding<br>
                    • Tiket tidak dapat di-refund<br>
                    • Simpan tiket hingga perjalanan selesai
                </div>
                
                @if($transaction->notes)
                <div class="section-divider">
                    <div style="font-weight: bold; margin-bottom: 6px;">CATATAN</div>
                </div>
                <div style="font-size: 9px;">
                    {{ $transaction->notes }}
                </div>
                @endif
            </div>
        </div>
        
        <!-- Footer -->
        <div class="ticket-footer">
            <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ $transaction->creator->name ?? 'System' }}</div>
            <div>Terima kasih atas kepercayaan Anda!</div>
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