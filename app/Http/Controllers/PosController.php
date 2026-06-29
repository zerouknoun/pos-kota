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
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

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
        // Eager load items and products to avoid N+1 queries
        $order->load('items.product');

        $base64Bytes = '';

        if (class_exists(\Mike42\Escpos\Printer::class)) {
            try {
                $connector = new DummyPrintConnector();
                $printer = new Printer($connector);

                // Format thermal print
                $printer->initialize();
                
                // Header (Double size & Bold)
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(2, 2);
                $printer->setEmphasis(true);
                $printer->text("KOPI TAKALAR\n");
                
                // Address & Divider (Regular size)
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                $printer->text("Jl. Poros Takalar\n");
                $printer->text("--------------------------------\n");

                // Info (Align Left)
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Kasir : " . (auth()->user() ? auth()->user()->name : '-') . "\n");
                $printer->text("Waktu : " . date('d/m/Y H:i') . "\n");
                $printer->text("Metode: " . $order->payment_method . "\n");
                $printer->text("--------------------------------\n");

                // Items list
                foreach ($order->items as $item) {
                    $qtyAndName = $item->qty . "x " . $item->product->name;
                    $priceStr = number_format($item->subtotal, 0, ',', '.');
                    
                    // Format 32 columns wide row
                    $line = $this->formatItemRow($qtyAndName, $priceStr, 32);
                    $printer->text($line . "\n");
                }
                $printer->text("--------------------------------\n");

                // Total (Bold)
                $printer->setEmphasis(true);
                $totalStr = "Rp " . number_format($order->total_price, 0, ',', '.');
                $line = $this->formatRow("TOTAL", $totalStr, 32);
                $printer->text($line . "\n");
                $printer->setEmphasis(false); // Reset to regular

                // Footer
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Terima Kasih\nSilakan Berkunjung Kembali\n");
                $printer->feed(4);
                $printer->cut();

                $rawBytes = $connector->getData();
                $base64Bytes = base64_encode($rawBytes);
                $printer->close();
            } catch (\Exception $e) {
                // Log warning or handle quietly
                logger()->error("Gagal membuat data ESC/POS dengan mike42/escpos-php: " . $e->getMessage());
            }
        }

        return view('pos.struk', compact('order', 'base64Bytes'));
    }

    /**
     * Kirim data cetak langsung ke printer fisik dari server menggunakan mike42/escpos-php.
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function printDirect(Order $order): \Illuminate\Http\JsonResponse
    {
        // Load relationships
        $order->load('items.product');

        $connection = env('PRINTER_CONNECTION', 'dummy');
        $name = env('PRINTER_NAME', 'POS-58');
        $ip = env('PRINTER_IP', '127.0.0.1');
        $port = (int) env('PRINTER_PORT', 9100);

        try {
            if (!class_exists(\Mike42\Escpos\Printer::class)) {
                throw new \Exception('Library mike42/escpos-php tidak terpasang.');
            }

            switch ($connection) {
                case 'windows':
                    $connector = new WindowsPrintConnector($name);
                    break;
                case 'network':
                    $connector = new NetworkPrintConnector($ip, $port);
                    break;
                case 'file':
                    $connector = new FilePrintConnector($name);
                    break;
                default:
                    $connector = new DummyPrintConnector();
                    break;
            }

            $printer = new Printer($connector);

            // Format thermal print
            $printer->initialize();
            
            // Header (Double size & Bold)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->setEmphasis(true);
            $printer->text("KOPI TAKALAR\n");
            
            // Address & Divider (Regular size)
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text("Jl. Poros Takalar\n");
            $printer->text("--------------------------------\n");

            // Info (Align Left)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Kasir : " . (auth()->user() ? auth()->user()->name : '-') . "\n");
            $printer->text("Waktu : " . date('d/m/Y H:i') . "\n");
            $printer->text("Metode: " . $order->payment_method . "\n");
            $printer->text("--------------------------------\n");

            // Items list
            foreach ($order->items as $item) {
                $qtyAndName = $item->qty . "x " . $item->product->name;
                $priceStr = number_format($item->subtotal, 0, ',', '.');
                
                // Format 32 columns wide row
                $line = $this->formatItemRow($qtyAndName, $priceStr, 32);
                $printer->text($line . "\n");
            }
            $printer->text("--------------------------------\n");

            // Total (Bold)
            $printer->setEmphasis(true);
            $totalStr = "Rp " . number_format($order->total_price, 0, ',', '.');
            $line = $this->formatRow("TOTAL", $totalStr, 32);
            $printer->text($line . "\n");
            $printer->setEmphasis(false); // Reset to regular

            // Footer
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih\nSilakan Berkunjung Kembali\n");
            $printer->feed(4);
            $printer->cut();
            $printer->close();

            return response()->json([
                'success' => true,
                'message' => $connection === 'dummy' 
                    ? 'Mode Dummy: Berhasil menyusun biner struk (printer tidak tersambung fisik).' 
                    : 'Struk berhasil dikirim ke printer.'
            ]);

        } catch (\Exception $e) {
            logger()->error("Gagal cetak langsung dari server: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memformat baris rata kiri-kanan dengan padding spasi.
     */
    private function formatRow(string $leftText, string $rightText, int $width = 32): string
    {
        $leftLen = strlen($leftText);
        $rightLen = strlen($rightText);
        $spacesNeeded = $width - $leftLen - $rightLen;
        if ($spacesNeeded > 0) {
            return $leftText . str_repeat(' ', $spacesNeeded) . $rightText;
        } else {
            $maxLeftLen = $width - $rightLen - 1;
            return substr($leftText, 0, $maxLeftLen) . ' ' . $rightText;
        }
    }

    /**
     * Memformat baris item dengan kuantitas dan harga.
     */
    private function formatItemRow(string $qtyAndName, string $priceStr, int $width = 32): string
    {
        if (strlen($qtyAndName) + strlen($priceStr) + 1 <= $width) {
            return $this->formatRow($qtyAndName, $priceStr, $width);
        } else {
            return $qtyAndName . "\n" . str_repeat(' ', $width - strlen($priceStr)) . $priceStr;
        }
    }
}
