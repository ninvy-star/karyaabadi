<?php
// admin/includes/header.php - <head> + opening body + sidebar
// Variabel: $judul (page title), $menu_aktif
$judul = $judul ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($judul) ?> - Admin <?= e($site['nama_pendek'] ?? 'Karya Abadi') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
<?php include __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">
