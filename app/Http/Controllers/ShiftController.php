<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShiftController extends Controller
{
    /**
     * Memulai shift baru untuk kasir yang sedang login.
     * Dipanggil setelah kasir Login.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function startShift(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        // Cek jika kasir sudah memiliki shift aktif yang belum selesai
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if ($activeShift) {
            return redirect()
                ->route('pos.index')
                ->with('info', 'Anda sudah berada di dalam shift aktif.');
        }

        $initialCup = $request->has('initial_cup') ? (int) $request->input('initial_cup') : 30;

        Shift::create([
            'user_id' => $userId,
            'start_time' => Carbon::now(),
            'initial_cup' => $initialCup,
        ]);

        return redirect()
            ->route('pos.index')
            ->with('success', 'Shift baru berhasil dimulai.');
    }

    /**
     * Mengakhiri shift aktif dan menampilkan rekapan transaksi.
     * Dipanggil saat klik "Akhiri Shift".
     *
     * @return View|RedirectResponse
     */
    public function endShift(): View|RedirectResponse
    {
        $userId = Auth::id();

        // Ambil shift kasir yang aktif
        $shift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (!$shift) {
            return redirect()
                ->route('pos.index')
                ->with('error', 'Tidak ada shift aktif yang ditemukan.');
        }

        // Ambil ID semua order pada shift ini
        $orderIds = Order::where('shift_id', $shift->id)->pluck('id');

        // Hitung total pendapatan berdasarkan metode pembayaran menggunakan database aggregasi
        $paymentTotals = Order::where('shift_id', $shift->id)
            ->selectRaw('payment_method, SUM(total_price) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $totalCash = (int) $paymentTotals->get('CASH', 0);
        $totalQris = (int) $paymentTotals->get('QRIS', 0);
        $totalKasbon = (int) $paymentTotals->get('KASBON', 0);
        $totalRevenue = $totalCash + $totalQris + $totalKasbon;

        // Hitung cup terpakai dari order items (Kategori ICE dan HOT saja, bukan Gelas)
        $usedCup = (int) OrderItem::whereIn('order_id', $orderIds)
            ->whereHas('product', function ($query) {
                $query->whereIn('category', ['A. ICE', 'B. HOT']);
            })
            ->sum('qty');

        // Update data shift dengan rekapan
        $shift->update([
            'end_time' => Carbon::now(),
            'final_cup' => $shift->initial_cup - $usedCup,
            'total_cash' => $totalCash,
            'total_qris' => $totalQris,
            'total_kasbon' => $totalKasbon,
            'total_revenue' => $totalRevenue,
        ]);

        // Ambil rincian produk yang terjual untuk ditampilkan di rekapan
        $soldItems = OrderItem::whereIn('order_id', $orderIds)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.category, products.name, SUM(order_items.qty) as total_qty')
            ->groupBy('products.category', 'products.name')
            ->get()
            ->groupBy('category');

        return view('pos.rekapan', [
            'shift' => $shift,
            'sold_items' => $soldItems,
            'used_cup' => $usedCup,
        ]);
    }
}

