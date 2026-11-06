<?php
// ============================================================
// KONEKSI DATABASE - UD Karya Abadi
// Edit credential sesuai hosting InfinityFree kamu (lihat panduan deploy)
// ============================================================

$DB_HOST = 'localhost';
$DB_USER = 'root';        // Ganti dengan user MySQL InfinityFree kamu
$DB_PASS = '';            // Ganti dengan password MySQL InfinityFree kamu
$DB_NAME = 'karya_abadi'; // Ganti dengan nama DB InfinityFree kamu

$koneksi = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Pastikan charset UTF-8 (biar emoji & karakter Indonesia tampil benar)
mysqli_set_charset($koneksi, 'utf8mb4');

// Timezone Indonesia
date_default_timezone_set('Asia/Jakarta');
