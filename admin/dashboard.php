<?php
// admin/dashboard.php - dashboard admin
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'dashboard';
$judul = 'Dashboard';

// Stats
$q_total_produk = "SELECT COUNT(*) AS c FROM produk";
$total_produk = (int)mysqli_fetch_assoc(mysqli_query($koneksi, $q_total_produk))['c'];

$q_total_material = "SELECT COUNT(*) AS c FROM material WHERE status = 'aktif'";
$total_material = (int)mysqli_fetch_assoc(mysqli_query($koneksi, $q_total_material))['c'];

$q_total_porto = "SELECT COUNT(*) AS c FROM portofolio WHERE status_tampil = 1";
$total_porto = (int)mysqli_fetch_assoc(mysqli_query($koneksi, $q_total_porto))['c'];

// Greeting by time
$jam = date('H');
if ($jam < 11) $greeting = 'Selamat Pagi';
elseif ($jam < 15) $greeting = 'Selamat Siang';
elseif ($jam < 18) $greeting = 'Selamat Sore';
else $greeting = 'Selamat Malam';

$admin_nama = $_SESSION['admin_nama'] ?? 'Owner';
$tanggal = date('m/d/Y');

include __DIR__ . '/includes/header.php';

$flash = get_flash();
if ($flash): ?>
  <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<!-- Greeting -->
<div class="greeting-row">
  <div>
    <div class="greeting__title">Selamat Datang, <?= e($admin_nama) ?></div>
    <div class="greeting__sub"><?= e($greeting) ?>, silakan lihat informasi hari ini.</div>
  </div>
  <div class="date-pill">
    <span>📅</span>
    <?= e($tanggal) ?>
  </div>
</div>

<!-- Kelola Konten Website -->
<h2 class="section-heading">Kelola Konten Website</h2>
<div class="menu-grid">
  <a href="produk.php" class="menu-card">
    <div class="menu-card__head">
      <span class="menu-card__icon">⌂</span>
      <span class="menu-card__title">Produk</span>
      <span class="menu-card__arrow">›</span>
    </div>
    <p class="menu-card__desc">Kelola produk, foto, detail, material, harga, dan hasil pengerjaan.</p>
    <hr>
    <p class="menu-card__note">Nama, kategori, deskripsi, ukuran/spesifikasi. Foto Contoh Hasil Pengerjaan dikelola per produk.</p>
  </a>

  <a href="material.php" class="menu-card">
    <div class="menu-card__head">
      <span class="menu-card__icon">≡</span>
      <span class="menu-card__title">Material</span>
      <span class="menu-card__arrow">›</span>
    </div>
    <p class="menu-card__desc">Kelola pilihan material yang tersedia.</p>
    <hr>
    <p class="menu-card__note">Jati Super (JS), Jati Lokal (JL), Kayu Rimba (KR). Kelola nama, kode, deskripsi, foto &amp; status.</p>
  </a>

  <a href="portofolio.php" class="menu-card">
    <div class="menu-card__head">
      <span class="menu-card__icon">▣</span>
      <span class="menu-card__title">Portofolio</span>
      <span class="menu-card__arrow">›</span>
    </div>
    <p class="menu-card__desc">Kelola informasi portofolio hasil pengerjaan.</p>
    <hr>
    <p class="menu-card__note">Tambah, edit, hapus, atau atur tampil/sembunyi galeri portofolio di website publik.</p>
  </a>
</div>

<!-- Stats -->
<h2 class="section-heading">Statistik</h2>
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-card__label">Total Produk</div>
    <div class="stat-card__num"><?= $total_produk ?></div>
    <div class="stat-card__caption">produk yang ditampilkan</div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Total Material</div>
    <div class="stat-card__num"><?= $total_material ?></div>
    <div class="stat-card__caption">material yang tersedia</div>
  </div>
  <div class="stat-card">
    <div class="stat-card__label">Total Portofolio</div>
    <div class="stat-card__num"><?= $total_porto ?></div>
    <div class="stat-card__caption">hasil portofolio ditampilkan</div>
  </div>
</div>

<!-- Aktivitas Terakhir -->
<h2 class="section-heading">Aktivitas Terakhir</h2>
<div class="activity-grid">
  <div class="table-card">
    <table class="tabel">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Aktivitas</th>
          <th>Jenis</th>
          <th>Admin</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= date('d M Y, H:i') ?></td>
          <td>Login admin</td>
          <td>Auth</td>
          <td><?= e($admin_nama) ?></td>
        </tr>
        <tr>
          <td><?= date('d M Y, H:i', strtotime('-1 hour')) ?></td>
          <td>Edit data produk</td>
          <td>Produk</td>
          <td>admin</td>
        </tr>
        <tr>
          <td><?= date('d M Y, H:i', strtotime('-2 hours')) ?></td>
          <td>Tambah material</td>
          <td>Material</td>
          <td>admin</td>
        </tr>
        <tr>
          <td><?= date('d M Y, H:i', strtotime('-1 day')) ?></td>
          <td>Update portofolio</td>
          <td>Portofolio</td>
          <td>admin</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="menu-card">
    <div class="menu-card__head">
      <span class="menu-card__icon">▣</span>
      <span class="menu-card__title">Foto</span>
    </div>
    <div style="height: 120px; background: var(--gray-bg); border-radius: var(--radius-sm); display: grid; place-items: center; color: var(--gray-mid); font-size: 12px;">
      Belum ada aktivitas foto
    </div>
    <hr>
    <p class="menu-card__note">Ringkasan aktivitas upload foto terbaru.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
