<?php
// admin/material_hapus.php - hapus material (POST + CSRF) + cek referential integrity
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: material.php');
    exit;
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID material tidak valid.');
    header('Location: material.php');
    exit;
}

// Cek apakah ada produk yang memakai material ini
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS c FROM produk WHERE material_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$jml = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['c'];

if ($jml > 0) {
    set_flash('error', "Tidak bisa hapus material: masih ada $jml produk yang memakai material ini. Ubah material produk tersebut dulu.");
    header('Location: material.php');
    exit;
}

// Ambil foto untuk dihapus
$stmt = mysqli_prepare($koneksi, "SELECT nama, foto FROM material WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    set_flash('error', 'Material tidak ditemukan.');
    header('Location: material.php');
    exit;
}

// Delete
$stmt = mysqli_prepare($koneksi, "DELETE FROM material WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    if (!empty($row['foto'])) {
        $path = __DIR__ . '/../uploads/' . $row['foto'];
        if (file_exists($path)) @unlink($path);
    }
    set_flash('success', 'Material "' . $row['nama'] . '" berhasil dihapus.');
} else {
    set_flash('error', 'Gagal menghapus material: ' . mysqli_error($koneksi));
}
header('Location: material.php');
exit;
