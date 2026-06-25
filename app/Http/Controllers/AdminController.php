<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan data penjualan real-time.
     *
     * @return View
     */
    public function index(): View
    {
        // Mengambil data ringkasan karyawan & menu
        $total_karyawan = User::where('role', 'kasir')->count();
        $total_menu = Product::count();

        // Pendapatan real-time dari seluruh order (termasuk shift yang sedang berjalan)
        $total_pendapatan = (int) Order::sum('total_price');
        $total_orders = Order::count();

        // Detail Pendapatan berdasarkan Metode Pembayaran
        $revenue_cash = (int) Order::where('payment_method', 'CASH')->sum('total_price');
        $revenue_qris = (int) Order::where('payment_method', 'QRIS')->sum('total_price');
        $revenue_kasbon = (int) Order::where('payment_method', 'KASBON')->sum('total_price');

        // Jumlah Cup Terjual (kategori ICE + HOT)
        $total_cup_terjual = (int) OrderItem::whereHas('product', function ($q) {
            $q->whereIn('category', ['A. ICE', 'B. HOT']);
        })->sum('qty');

        // Shift kasir yang sedang aktif bertugas sekarang
        $active_shifts = Shift::whereNull('end_time')
            ->with('user')
            ->orderBy('start_time', 'desc')
            ->get();

        // 5 Transaksi terakhir real-time
        $recent_orders = Order::with('shift.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'total_karyawan',
            'total_menu',
            'total_pendapatan',
            'total_orders',
            'revenue_cash',
            'revenue_qris',
            'revenue_kasbon',
            'total_cup_terjual',
            'active_shifts',
            'recent_orders'
        ));
    }
}