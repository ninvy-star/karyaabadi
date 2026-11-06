<?php
// admin/produk.php - daftar produk (Katalog Produk)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'produk';
$judul = 'Katalog Produk';

// Filter
$q_search = trim($_GET['q'] ?? '');
$q_kat = trim($_GET['kategori'] ?? '');
$q_mat = trim($_GET['material'] ?? '');

$where = "WHERE 1=1";
$params = []; $types = "";
if ($q_search !== '') { $where .= " AND p.nama LIKE ?"; $params[] = "%" . $q_search . "%"; $types .= "s"; }
if ($q_kat !== '') { $where .= " AND k.id = ?"; $params[] = (int)$q_kat; $types .= "i"; }
if ($q_mat !== '') { $where .= " AND m.id = ?"; $params[] = (int)$q_mat; $types .= "i"; }

$sql = "SELECT p.*, k.nama AS kategori, m.nama AS material
        FROM produk p
        LEFT JOIN kategori k ON p.kategori_id = k.id
        LEFT JOIN material m ON p.material_id = m.id
        $where ORDER BY p.id DESC";
$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$produk_list = [];
while ($row = mysqli_fetch_assoc($res)) $produk_list[] = $row;

// Kategori & material untuk dropdown
$kategoris = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);
$materials = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM material ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';

$flash = get_flash();
if ($flash): ?>
  <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title">Katalog Produk</div>
    <div class="page-head__subtitle">Kelola seluruh produk yang tampil di website</div>
  </div>
  <div class="page-head__actions">
    <a href="produk_tambah.php" class="btn btn--brown">+ Tambah Produk Baru</a>
  </div>
</div>

<!-- Filter -->
<form method="get" action="" class="toolbar-row">
  <div class="search-form">
    <span class="icon">🔍</span>
    <input type="text" name="q" value="<?= e($q_search) ?>" placeholder="Cari Produk ...">
  </div>
  <select name="kategori" class="filter-select">
    <option value="">Semua Kategori</option>
    <?php foreach ($kategoris as $kat): ?>
      <option value="<?= (int)$kat['id'] ?>" <?= $q_kat == $kat['id'] ? 'selected' : '' ?>><?= e($kat['nama']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="material" class="filter-select">
    <option value="">Semua Material</option>
    <?php foreach ($materials as $mat): ?>
      <option value="<?= (int)$mat['id'] ?>" <?= $q_mat == $mat['id'] ? 'selected' : '' ?>><?= e($mat['nama']) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn--outline btn--sm">Filter</button>
  <a href="produk.php" class="btn btn--outline btn--sm">Reset</a>
</form>

<!-- Tabel produk -->
<h2 class="section-heading">Produk</h2>
<div class="table-card">
  <table class="tabel">
    <thead>
      <tr>
        <th>Foto</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Material</th>
        <th>Harga</th>
        <th style="text-align: right;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($produk_list)): ?>
        <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada produk. Klik "Tambah Produk Baru" untuk menambah.</td></tr>
      <?php else: foreach ($produk_list as $p): ?>
        <tr>
          <td>
            <?php if (!empty($p['foto'])): ?>
              <span class="thumb"><img src="../uploads/<?= e($p['foto']) ?>" alt=""></span>
            <?php else: ?>
              <span class="thumb-kosong">—</span>
            <?php endif; ?>
          </td>
          <td class="nama-cell"><?= e($p['nama']) ?></td>
          <td><?= e($p['kategori'] ?? '-') ?></td>
          <td><?= e($p['material'] ?? '-') ?></td>
          <td>
            <?php if ($p['is_konsultasi']): ?>
              <span class="badge">Konsultasi</span>
            <?php else: ?>
              <?= rupiah($p['harga']) ?>
            <?php endif; ?>
          </td>
          <td>
            <div class="aksi" style="justify-content: flex-end;">
              <a href="produk_edit.php?id=<?= (int)$p['id'] ?>" class="link-edit" title="Edit">✎</a>
              <form method="post" action="produk_hapus.php" style="display: inline;"
                    onsubmit="return confirm('Yakin ingin menghapus produk <?= e($p['nama']) ?>?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button type="submit" class="link-hapus" title="Hapus">🗑</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="6">
        <div class="tabel-foot">
          <span>Menampilkan <?= count($produk_list) ?> produk</span>
          <span>Harga untuk informasi publik website</span>
        </div>
      </td></tr>
    </tfoot>
  </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
