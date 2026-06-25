<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Exports\LaporanPenjualanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Menghandle request ekspor laporan penjualan.
     */
    public function download(Request $request)
    {
        $request->validate([
            'period' => ['required', 'in:weekly,monthly,3_months,yearly'],
            'format' => ['required', 'in:pdf,excel'],
        ]);

        $period = $request->query('period');
        $format = $request->query('format');

        // Tentukan tanggal mulai berdasarkan periode pilihan
        $now = now();
        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->subDays(7)->startOfDay();
                $periodName = 'Mingguan (7 Hari Terakhir)';
                $periodCode = 'mingguan';
                break;
            case 'monthly':
                $startDate = $now->copy()->subDays(30)->startOfDay();
                $periodName = 'Bulanan (30 Hari Terakhir)';
                $periodCode = 'bulanan';
                break;
            case '3_months':
                $startDate = $now->copy()->subDays(90)->startOfDay();
                $periodName = '3 Bulan Terakhir';
                $periodCode = '3_bulanan';
                break;
            case 'yearly':
                $startDate = $now->copy()->subDays(365)->startOfDay();
                $periodName = 'Tahunan (365 Hari Terakhir)';
                $periodCode = 'tahunan';
                break;
            default:
                $startDate = $now->copy()->subDays(30)->startOfDay();
                $periodName = 'Bulanan (30 Hari Terakhir)';
                $periodCode = 'bulanan';
        }

        // Ambil data transaksi dalam rentang tanggal
        $orders = Order::where('created_at', '>=', $startDate)
            ->with(['shift.user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $title = "Laporan Penjualan - " . $periodName;

        // Jika Format EXCEL
        if ($format === 'excel') {
            return Excel::download(
                new LaporanPenjualanExport($orders, $title), 
                "laporan_penjualan_{$periodCode}_" . date('Ymd_His') . ".xlsx"
            );
        }

        // Jika Format PDF: Lakukan perhitungan ringkasan data
        $total_orders = $orders->count();
        $total_pendapatan = (int) $orders->sum('total_price');

        // Detail Pendapatan berdasarkan Metode Pembayaran
        $revenue_cash = (int) $orders->where('payment_method', 'CASH')->sum('total_price');
        $revenue_qris = (int) $orders->where('payment_method', 'QRIS')->sum('total_price');
        $revenue_kasbon = (int) $orders->where('payment_method', 'KASBON')->sum('total_price');

        // Jumlah Cup Terjual (kategori ICE + HOT)
        $orderIds = $orders->pluck('id')->toArray();
        $total_cup_terjual = 0;
        if (!empty($orderIds)) {
            $total_cup_terjual = (int) OrderItem::whereIn('order_id', $orderIds)
                ->whereHas('product', function ($q) {
                    $q->whereIn('category', ['A. ICE', 'B. HOT']);
                })->sum('qty');
        }

        // Top 5 Produk Terlaris
        $top_products = [];
        if (!empty($orderIds)) {
            $top_products = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereIn('orders.id', $orderIds)
                ->select(
                    'products.name as product_name',
                    'products.category as product_category',
                    DB::raw('SUM(order_items.qty) as total_qty'),
                    DB::raw('SUM(order_items.subtotal) as total_subtotal')
                )
                ->groupBy('products.id', 'products.name', 'products.category')
                ->orderBy('total_qty', 'desc')
                ->limit(5)
                ->get();
        }

        // Generate PDF
        $period_name = $periodName;
        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'orders',
            'title',
            'period_name',
            'periodName',
            'total_orders',
            'total_pendapatan',
            'revenue_cash',
            'revenue_qris',
            'revenue_kasbon',
            'total_cup_terjual',
            'top_products'
        ));

        // Mengatur ukuran A4 portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("laporan_penjualan_{$periodCode}_" . date('Ymd_His') . ".pdf");
    }
}
