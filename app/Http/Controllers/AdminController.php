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
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

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

    /**
     * Tampilkan halaman pengetesan printer.
     */
    public function testPrinterPage(): View
    {
        $printer_config = [
            'connection' => env('PRINTER_CONNECTION', 'dummy'),
            'name' => env('PRINTER_NAME', 'POS-58'),
            'ip' => env('PRINTER_IP', '127.0.0.1'),
            'port' => env('PRINTER_PORT', 9100),
        ];

        return view('admin.test-printer', compact('printer_config'));
    }

    /**
     * Jalankan test print menggunakan escpos-php.
     */
    public function runTestPrint(Request $request): \Illuminate\Http\JsonResponse
    {
        $connection = $request->input('connection_type', 'dummy');
        $name = $request->input('printer_name', 'POS-58');
        $ip = $request->input('printer_ip', '127.0.0.1');
        $port = (int) $request->input('printer_port', 9100);
        $test_type = $request->input('test_type', 'text');

        try {
            if (!class_exists(\Mike42\Escpos\Printer::class)) {
                throw new \Exception('Library mike42/escpos-php tidak terpasang.');
            }

            if ($connection === 'rawbt') {
                $connector = new DummyPrintConnector();
            } else {
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
            }

            $printer = new Printer($connector);
            $printer->initialize();

            if ($test_type === 'cut') {
                $printer->text("=== TEST POTONG KERTAS ===\n");
                $printer->feed(4);
                $printer->cut();
            } elseif ($test_type === 'alignment') {
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Ini Rata Kiri (Left Alignment)\n");
                
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Ini Rata Tengah (Center Alignment)\n");
                
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text("Ini Rata Kanan (Right Alignment)\n");
                
                $printer->feed(2);
                $printer->cut();
            } elseif ($test_type === 'styles') {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("=== TEST GAYA TEKS ===\n\n");
                
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Teks Normal\n");
                
                $printer->setEmphasis(true);
                $printer->text("Teks Bold (Tebal)\n");
                $printer->setEmphasis(false);
                
                $printer->setTextSize(2, 1);
                $printer->text("Teks Lebar Ganda (Double Width)\n");
                
                $printer->setTextSize(1, 2);
                $printer->text("Teks Tinggi Ganda (Double Height)\n");
                
                $printer->setTextSize(2, 2);
                $printer->text("Teks Ukuran Ganda\n");
                
                $printer->setTextSize(1, 1);
                $printer->feed(2);
                $printer->cut();
            } else {
                // Default: Standard receipt test
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(2, 2);
                $printer->setEmphasis(true);
                $printer->text("KOPI TAKALAR\n");
                
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                $printer->text("Jl. Poros Takalar\n");
                $printer->text("--------------------------------\n");
                $printer->text("PRINTER TEST CONNECTION\n");
                $printer->text("Status: KONEKSI BERHASIL!\n");
                $printer->text("--------------------------------\n");
                $printer->text("Waktu: " . date('d-m-Y H:i:s') . "\n");
                $printer->text("Tipe: " . strtoupper($connection) . "\n");
                $printer->text("Target: " . ($connection === 'network' ? "$ip:$port" : $name) . "\n");
                $printer->text("--------------------------------\n");
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Terima Kasih\n");
                $printer->feed(4);
                $printer->cut();
            }

            if ($connection === 'rawbt') {
                $rawBytes = $connector->getData();
                $base64Bytes = base64_encode($rawBytes);
                $printer->close();
                return response()->json([
                    'success' => true,
                    'message' => 'Format cetak RawBt berhasil disusun. Mengirim ke aplikasi RawBt...',
                    'base64' => $base64Bytes
                ]);
            }

            $printer->close();

            return response()->json([
                'success' => true,
                'message' => 'Test print berhasil dikirim ke printer!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan test print: ' . $e->getMessage()
            ], 500);
        }
    }
}