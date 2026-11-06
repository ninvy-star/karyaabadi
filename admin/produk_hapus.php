<?php
// admin/produk_hapus.php - hapus produk (POST + CSRF)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: produk.php');
    exit;
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID produk tidak valid.');
    header('Location: produk.php');
    exit;
}

// Ambil nama foto untuk dihapus file-nya
$stmt = mysqli_prepare($koneksi, "SELECT nama, foto FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    set_flash('error', 'Produk tidak ditemukan.');
    header('Location: produk.php');
    exit;
}

// Delete record
$stmt = mysqli_prepare($koneksi, "DELETE FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    // Hapus file foto
    if (!empty($row['foto'])) {
        $path = __DIR__ . '/../uploads/' . $row['foto'];
        if (file_exists($path)) @unlink($path);
    }
    set_flash('success', 'Produk "' . $row['nama'] . '" berhasil dihapus.');
} else {
    set_flash('error', 'Gagal menghapus produk: ' . mysqli_error($koneksi));
}
header('Location: produk.php');
exit;
