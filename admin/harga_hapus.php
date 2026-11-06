<?php
// admin/harga_hapus.php - hapus data harga (POST + CSRF)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: material.php');
    exit;
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID data harga tidak valid.');
    header('Location: material.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM harga WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    set_flash('success', 'Data harga berhasil dihapus.');
} else {
    set_flash('error', 'Gagal menghapus data harga: ' . mysqli_error($koneksi));
}
header('Location: material.php');
exit;
