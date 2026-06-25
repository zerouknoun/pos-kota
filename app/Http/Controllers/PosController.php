<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Tampilkan halaman utama POS.
     * Jika tidak ada shift aktif, kasir diarahkan untuk memulai shift.
     *
     * @return View
     */
    public function index(): View
    {
        $userId = Auth::id();

        // Cari shift kasir yang sedang aktif
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        // Ambil semua produk
        $products = Product::all();

        return view('pos.index', compact('activeShift', 'products'));
    }

    /**
     * Memproses pesanan/transaksi baru dari kasir.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function checkout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        // Pastikan ada shift aktif
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (!$activeShift) {
            return redirect()
                ->route('pos.index')
                ->with('error', 'Silakan mulai shift Anda terlebih dahulu.');
        }

        // Validasi input checkout
        $request->validate([
            'payment_method' => ['required', 'in:CASH,QRIS,KASBON'],
            'cart' => ['required', 'array'],
            'cart.*.id' => ['required', 'exists:products,id'],
            'cart.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->input('cart');
        $paymentMethod = $request->input('payment_method');

        try {
            $order = DB::transaction(function () use ($activeShift, $cart, $paymentMethod) {
                $totalPrice = 0;
                $orderItemsData = [];

                // Cari semua detail produk di keranjang dalam satu query untuk efisiensi
                $productIds = collect($cart)->pluck('id')->toArray();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($cart as $item) {
                    $product = $products->get($item['id']);
                    if (!$product) {
                        throw new \Exception('Produk tidak ditemukan.');
                    }

                    $qty = (int) $item['qty'];
                    $subtotal = $product->price * $qty;
                    $totalPrice += $subtotal;

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'subtotal' => $subtotal,
                    ];
                }

                // Buat Order baru
                $order = Order::create([
                    'shift_id' => $activeShift->id,
                    'total_price' => $totalPrice,
                    'payment_method' => $paymentMethod,
                ]);

                // Simpan detail Order Items
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            return redirect()
                ->route('pos.struk', ['order' => $order->id])
                ->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()
                ->route('pos.index')
                ->with('error', 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan struk/receipt pembayaran untuk dicetak.
     *
     * @param Order $order
     * @return View
     */
    public function struk(Order $order): View
    {
        // Eager load items and products to avoid N+1 queries in view
        $order->load('items.product');

        return view('pos.struk', compact('order'));
    }
}
