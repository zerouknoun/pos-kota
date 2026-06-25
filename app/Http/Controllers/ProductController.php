<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar menu produk.
     *
     * @return View
     */
    public function index(): View
    {
        $products = Product::orderBy('category')->orderBy('name')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Tampilkan formulir tambah menu baru.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Menyimpan menu baru ke database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:A. ICE,B. HOT,C. Gelas'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        Product::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'price' => (int) $request->input('price'),
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit menu.
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Memperbarui data menu di database.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:A. ICE,B. HOT,C. Gelas'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $product->update([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'price' => (int) $request->input('price'),
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus menu dari database.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
            return redirect()
                ->route('products.index')
                ->with('success', 'Menu berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Menu ini tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }
    }
}
