<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactions;
    
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }
    
    public function collection()
    {
        return $this->transactions;
    }
    
    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Nama Penumpang',
            'Destinasi',
            'Keberangkatan',
            'Tujuan',
            'Jadwal',
            'Dewasa',
            'Anak',
            'Balita',
            'Total Penumpang',
            'Total Harga',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Tanggal Transaksi',
            'Dibuat Oleh'
        ];
    }
    
    public function map($transaction): array
    {
        return [
            $transaction->transaction_code,
            $transaction->passenger_name,
            $transaction->schedule->destination->name ?? '-',
            $transaction->schedule->destination->departure_location ?? '-',
            $transaction->schedule->destination->destination_location ?? '-',
            $transaction->schedule->departure_time->format('d/m/Y H:i') ?? '-',
            $transaction->adult_count,
            $transaction->child_count,
            $transaction->toddler_count,
            $transaction->adult_count + $transaction->child_count + $transaction->toddler_count,
            'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
            strtoupper($transaction->payment_method),
            ucfirst($transaction->payment_status),
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->creator->name ?? '-'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}