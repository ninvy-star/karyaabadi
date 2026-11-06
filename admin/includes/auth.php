<?php
// admin/includes/auth.php - session guard untuk semua halaman admin
// Include ini di awal setiap file admin (kecuali login.php & logout.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Include config utama biar helper functions (e, img, wa_link, csrf_*) bisa dipake di admin
require __DIR__ . '/../../includes/config.php';
