<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel Users (Admin & Kasir)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('kasir'); // 'admin' atau 'kasir'
        });

        // Tabel Menu Produk
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // 'A. ICE', 'B. HOT', 'C. Gelas'
            $table->integer('price');
            $table->timestamps();
        });

        // Tabel Rekap Shift
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->integer('initial_cup')->default(30); // Bisa disesuaikan
            $table->integer('final_cup')->nullable();
            $table->integer('total_cash')->default(0);
            $table->integer('total_qris')->default(0);
            $table->integer('total_kasbon')->default(0);
            $table->integer('total_revenue')->default(0);
            $table->timestamps();
        });

        // Tabel Transaksi
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts');
            $table->integer('total_price');
            $table->string('payment_method'); // 'CASH', 'QRIS', 'KASBON'
            $table->timestamps();
        });

        // Tabel Detail Transaksi
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('qty');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('products');
    }
};