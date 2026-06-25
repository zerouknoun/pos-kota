<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Tampilkan daftar karyawan (user).
     *
     * @return View
     */
    public function index(): View
    {
        $employees = User::orderBy('role')->orderBy('name')->get();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Tampilkan formulir tambah karyawan baru.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.employees.create');
    }

    /**
     * Menyimpan akun karyawan baru ke database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,kasir'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Akun karyawan baru berhasil didaftarkan.');
    }

    /**
     * Tampilkan formulir edit karyawan.
     *
     * @param User $employee
     * @return View
     */
    public function edit(User $employee): View
    {
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Memperbarui data karyawan di database.
     *
     * @param Request $request
     * @param User $employee
     * @return RedirectResponse
     */
    public function update(Request $request, User $employee): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'role' => ['required', 'in:admin,kasir'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Menghapus karyawan dari database.
     *
     * @param User $employee
     * @return RedirectResponse
     */
    public function destroy(User $employee): RedirectResponse
    {
        // Proteksi: Tidak boleh menghapus diri sendiri yang sedang login
        if ($employee->id === Auth::id()) {
            return redirect()
                ->route('employees.index')
                ->with('error', 'Keamanan Sistem: Anda tidak dapat menghapus akun admin Anda sendiri yang sedang aktif.');
        }

        try {
            $employee->delete();
            return redirect()
                ->route('employees.index')
                ->with('success', 'Akun karyawan berhasil dinonaktifkan/dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('employees.index')
                ->with('error', 'Akun ini tidak dapat dihapus karena memiliki riwayat data shift kerja.');
        }
    }
}
