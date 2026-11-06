<?php
// admin/portofolio_toggle.php - toggle status_tampil (Tampil/Sembunyi)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: portofolio.php');
    exit;
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID portofolio tidak valid.');
    header('Location: portofolio.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "UPDATE portofolio SET status_tampil = NOT status_tampil WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    set_flash('success', 'Status tampil portofolio berhasil diubah.');
} else {
    set_flash('error', 'Gagal mengubah status: ' . mysqli_error($koneksi));
}
header('Location: portofolio.php');
exit;
