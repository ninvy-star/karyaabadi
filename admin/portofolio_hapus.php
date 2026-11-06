<?php
// admin/portofolio_hapus.php - hapus portofolio (POST + CSRF)
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

$stmt = mysqli_prepare($koneksi, "SELECT judul, foto FROM portofolio WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    set_flash('error', 'Portofolio tidak ditemukan.');
    header('Location: portofolio.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM portofolio WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    if (!empty($row['foto'])) {
        $path = __DIR__ . '/../uploads/' . $row['foto'];
        if (file_exists($path)) @unlink($path);
    }
    set_flash('success', 'Portofolio "' . $row['judul'] . '" berhasil dihapus.');
} else {
    set_flash('error', 'Gagal menghapus portofolio: ' . mysqli_error($koneksi));
}
header('Location: portofolio.php');
exit;
