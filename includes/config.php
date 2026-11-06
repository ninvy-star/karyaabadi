<?php
// ============================================================
// CONFIG UTAMA - UD Karya Abadi
// Helper functions, site settings, navigation menu
// ============================================================

// Site settings (GANTI sesuai data asli UD Karya Abadi)
$site = [
    'nama'      => 'UD Karya Abadi',
    'nama_pendek' => 'Karya Abadi',
    'tagline'   => 'Furniture Homemade & Custom',
    'deskripsi' => 'Furniture homemade dan custom dari pengrajin lokal untuk kebutuhan rumah serta ruang usaha.',
    // WA number format: 62 (kode negara) + nomor tanpa 0 di depan
    // GANTI dengan nomor WhatsApp asli UD Karya Abadi
    'wa'        => '6281234567890',
    'wa_display'=> '+62 812 3456 7890',
    'email'     => 'halo@karyaabadi.id',
    'jam'       => 'Senin–Sabtu, 08.00–17.00',
    'alamat'    => 'Dusun Partelon RT 01 / RW 01, Desa Silo, Kec. Silo, Jember, Jawa Timur',
    'instagram' => '#',
    'facebook'  => '#',
    'tiktok'    => '#',
    'tahun_berdiri' => 2005,
    'estimasi_pesanan' => 120,
];

// ------------------------------------------------------------
// MENU PUBLIC (urutan navbar)
// ------------------------------------------------------------
$menu = [
    ['label' => 'Beranda',       'url' => 'index.php',         'ready' => true],
    ['label' => 'Tentang Kami',  'url' => 'tentang.php',       'ready' => true],
    ['label' => 'Produk',        'url' => 'produk.php',        'ready' => true],
    ['label' => 'Material',      'url' => 'material.php',      'ready' => true],
    ['label' => 'Kontak',        'url' => 'kontak.php',        'ready' => true],
];

// ------------------------------------------------------------
// HELPER FUNCTIONS
// ------------------------------------------------------------

/** Escape output untuk safety XSS */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Bangun URL gambar produk/material dengan fallback */
function img($foto, $tipe = 'produk') {
    if (!$foto) {
        // Fallback ke SVG placeholder sesuai tipe
        $fallbacks = [
            'produk'    => 'assets/images/produk-meja.svg',
            'material'  => 'assets/images/material-jati-super.svg',
            'portofolio'=> 'assets/images/galeri-1.svg',
        ];
        return $fallbacks[$tipe] ?? $fallbacks['produk'];
    }
    // Kalau path foto udah ada prefix uploads/ langsung, pakai apa adanya
    if (strpos($foto, 'uploads/') === 0 || strpos($foto, 'assets/') === 0) {
        return $foto;
    }
    return 'uploads/' . $foto;
}

/** Bangun link WhatsApp dengan pesan opsional */
function wa_link($pesan = '') {
    global $site;
    $pesan = trim($pesan);
    if ($pesan) {
        return 'https://wa.me/' . $site['wa'] . '?text=' . rawurlencode($pesan);
    }
    return 'https://wa.me/' . $site['wa'];
}

/** Format angka ke rupiah: 2500000 -> "Rp 2.500.000" */
function rupiah($angka) {
    if ($angka === null || $angka === '') return 'Konsultasi';
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

/** Format angka ke rupiah tanpa "Rp" prefix (untuk input form) */
function rupiah_input($angka) {
    if ($angka === null || $angka === '') return '';
    return number_format((int)$angka, 0, ',', '.');
}

/** Parse input rupiah user "Rp 2.500.000" -> 2500000 (int) */
function parse_rupiah($str) {
    if ($str === '' || $str === null) return null;
    // Buang semua kecuali digit
    $clean = preg_replace('/[^0-9]/', '', $str);
    return $clean === '' ? null : (int)$clean;
}

// ------------------------------------------------------------
// CSRF PROTECTION
// ------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Generate / ambil CSRF token untuk form */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verifikasi CSRF token dari POST form */
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Akses ditolak: CSRF token tidak valid. Silakan kembali dan coba lagi.');
    }
}

/** Hidden input field untuk CSRF token (taruh di setiap form) */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/** Random filename untuk upload gambar (anti-collision) */
function generate_filename($prefix = 'upload') {
    return $prefix . '_' . bin2hex(random_bytes(8)) . '.' . pathinfo($_FILES['foto']['name'] ?? 'x.jpg', PATHINFO_EXTENSION);
}

/** Upload gambar foto ke folder uploads/. Return filename baru atau null kalau gagal. */
function handle_upload($prefix = 'produk', $old_file = null) {
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['error' => 'Format file tidak didukung. Hanya JPG, JPEG, PNG, WEBP.'];
    }
    // Maks 5MB
    if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        return ['error' => 'Ukuran file terlalu besar. Maks 5MB.'];
    }
    // Verifikasi MIME via getimagesize
    $info = @getimagesize($_FILES['foto']['tmp_name']);
    if ($info === false) {
        return ['error' => 'File bukan gambar valid.'];
    }
    $nama_baru = generate_filename($prefix);
    $dest = __DIR__ . '/../uploads/' . $nama_baru;
    if (!is_dir(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
        return ['error' => 'Gagal menyimpan file upload. Cek permission folder uploads/.'];
    }
    // Hapus foto lama kalau ada
    if ($old_file) {
        $old_path = __DIR__ . '/../uploads/' . $old_file;
        if (file_exists($old_path)) {
            @unlink($old_path);
        }
    }
    return $nama_baru;
}

/** Flash message (1× pakai, lalu hilang) */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
