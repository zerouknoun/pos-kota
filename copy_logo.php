<?php

$source = __DIR__ . '/Screenshot_2026-06-24-22-27-39-265-edit_com.whatsapp.jpg';
$target = __DIR__ . '/public/logo.png';

if (file_exists($source)) {
    if (copy($source, $target)) {
        echo "Sukses! Logo berhasil diganti dengan file screenshot Anda di public/logo.png\n";
    } else {
        echo "Gagal menyalin file secara internal.\n";
    }
} else {
    echo "Gagal: File " . $source . " tidak ditemukan di root folder proyek Anda.\n";
}
