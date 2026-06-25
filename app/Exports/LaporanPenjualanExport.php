<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $orders;
    protected $title;

    /**
     * Konstruktor untuk menerima data order dan judul sheet.
     */
    public function __construct($orders, $title)
    {
        $this->orders = $orders;
        $this->title = $title;
    }

    /**
     * Mengembalikan koleksi data yang diekspor.
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * Mengatur nama sheet Excel.
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Mengatur judul header kolom Excel.
     */
    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Nama Kasir',
            'Metode Pembayaran',
            'Total Harga (Rp)',
            'Tanggal & Waktu Transaksi'
        ];
    }

    /**
     * Memetakan data dari setiap baris order.
     */
    public function map($order): array
    {
        return [
            $order->id,
            $order->shift->user->name ?? 'N/A',
            $order->payment_method,
            $order->total_price,
            $order->created_at->format('d-m-Y H:i:s')
        ];
    }
}
