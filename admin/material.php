<?php
// admin/material.php - halaman material: Daftar Material + Data Tabel Harga
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'material';
$judul = 'Daftar Material';

$q_search = trim($_GET['q'] ?? '');

$where = "WHERE 1=1";
$params = []; $types = "";
if ($q_search !== '') {
    $where .= " AND nama LIKE ?";
    $params[] = "%" . $q_search . "%";
    $types .= "s";
}

$sql = "SELECT * FROM material $where ORDER BY urutan ASC, id ASC";
$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$material_list = [];
while ($row = mysqli_fetch_assoc($res)) $material_list[] = $row;

// Ambil data harga join produk
$sql_h = "SELECT h.*, p.nama AS produk_nama
          FROM harga h
          LEFT JOIN produk p ON h.produk_id = p.id
          ORDER BY h.id ASC";
$res_h = mysqli_query($koneksi, $sql_h);
$harga_list = [];
while ($row = mysqli_fetch_assoc($res_h)) $harga_list[] = $row;

include __DIR__ . '/includes/header.php';

$flash = get_flash();
if ($flash): ?>
  <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title">Daftar Material</div>
    <div class="page-head__subtitle">Kelola jenis material kayu yang tersedia</div>
  </div>
  <div class="page-head__actions">
    <a href="material_form.php" class="btn btn--brown">+ Tambah Material</a>
  </div>
</div>

<!-- Search -->
<form method="get" action="" class="toolbar-row">
  <div class="search-form">
    <span class="icon">🔍</span>
    <input type="text" name="q" value="<?= e($q_search) ?>" placeholder="Cari material...">
  </div>
  <button type="submit" class="btn btn--outline btn--sm">Cari</button>
  <a href="material.php" class="btn btn--outline btn--sm">Reset</a>
</form>

<!-- TABEL 1: Daftar Material -->
<h2 class="section-heading">Daftar Material</h2>
<div class="table-card">
  <table class="tabel">
    <thead>
      <tr>
        <th>Foto</th>
        <th>Nama Material</th>
        <th>Deskripsi Singkat</th>
        <th>Kode</th>
        <th>Status</th>
        <th style="text-align: right;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($material_list)): ?>
        <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada material.</td></tr>
      <?php else: foreach ($material_list as $m): ?>
        <tr>
          <td>
            <?php if (!empty($m['foto'])): ?>
              <span class="thumb"><img src="../uploads/<?= e($m['foto']) ?>" alt=""></span>
            <?php else: ?>
              <span class="thumb-kosong">—</span>
            <?php endif; ?>
          </td>
          <td class="nama-cell"><?= e($m['nama']) ?></td>
          <td style="max-width: 360px; color: var(--text-muted);"><?= e($m['deskripsi']) ?></td>
          <td><?= e($m['kode'] ?? '-') ?></td>
          <td>
            <?php if ($m['status'] === 'aktif'): ?>
              <span class="badge" style="background: #D4EDDA; color: #155724;">Aktif</span>
            <?php else: ?>
              <span class="badge" style="background: #F8D7DA; color: #721C24;">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="aksi" style="justify-content: flex-end;">
              <a href="material_form.php?id=<?= (int)$m['id'] ?>" class="link-edit" title="Edit">✎</a>
              <form method="post" action="material_hapus.php" style="display: inline;"
                    onsubmit="return confirm('Yakin hapus material <?= e($m['nama']) ?>? Produk yang memakai material ini akan kehilangan referensi material.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <button type="submit" class="link-hapus" title="Hapus">🗑</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- TABEL 2: Data Tabel Harga -->
<div class="page-head" style="margin-top: 40px;">
  <div>
    <div class="page-head__title" style="font-size: 24px;">Data Tabel Harga</div>
    <div class="page-head__subtitle">Harga produk per material &amp; ukuran</div>
  </div>
  <div class="page-head__actions">
    <a href="harga_form.php" class="btn btn--brown">+ Tambah Data</a>
  </div>
</div>

<div class="table-card">
  <table class="tabel">
    <thead>
      <tr>
        <th>Jenis Produk</th>
        <th>Ukuran</th>
        <th>Jati Super</th>
        <th>Jati Lokal</th>
        <th>Kayu Rimba</th>
        <th style="text-align: right;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($harga_list)): ?>
        <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada data harga.</td></tr>
      <?php else: foreach ($harga_list as $h): ?>
        <tr>
          <td class="nama-cell"><?= e($h['produk_nama'] ?? '(produk terhapus)') ?></td>
          <td><?= e($h['ukuran'] ?? '-') ?></td>
          <td><?= $h['harga_jati_super'] !== null ? rupiah($h['harga_jati_super']) : '-' ?></td>
          <td><?= $h['harga_jati_lokal'] !== null ? rupiah($h['harga_jati_lokal']) : '-' ?></td>
          <td><?= $h['harga_kayu_rimba'] !== null ? rupiah($h['harga_kayu_rimba']) : '-' ?></td>
          <td>
            <div class="aksi" style="justify-content: flex-end;">
              <a href="harga_form.php?id=<?= (int)$h['id'] ?>" class="link-edit" title="Edit">✎</a>
              <form method="post" action="harga_hapus.php" style="display: inline;"
                    onsubmit="return confirm('Yakin hapus data harga ini?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$h['id'] ?>">
                <button type="submit" class="link-hapus" title="Hapus">🗑</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
