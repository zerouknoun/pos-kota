<?php

$id = "1ZRy95H5s7v1H_PvgOu1jR983iM-X0ez6";
$url = "https://docs.google.com/uc?export=download&id=" . $id;
$target = __DIR__ . '/public/logo.png';

echo "Mengunduh logo Kopi Takalar...\n";

// Mengatur User Agent agar Google Drive memperbolehkan pengunduhan
$options = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);
$content = @file_get_contents($url, false, $context);

if ($content !== false && strlen($content) > 1000) {
    if (!is_dir(dirname($target))) {
        mkdir(dirname($target), 0755, true);
    }
    file_put_contents($target, $content);
    echo "Sukses! Logo berhasil disimpan di: " . $target . "\n";
} else {
    echo "Gagal mengunduh secara otomatis via Google Drive API.\n";
    echo "Silakan unduh secara manual dari link berikut:\n";
    echo "https://drive.google.com/file/d/" . $id . "/view\n";
    echo "Dan simpan ke dalam folder proyek Anda di: public/logo.png\n";
}
