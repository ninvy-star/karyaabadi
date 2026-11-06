<?php
// admin/portofolio.php - daftar portofolio (galeri hasil pengerjaan)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'portofolio';
$judul = 'Kelola Portofolio';

$q_search = trim($_GET['q'] ?? '');
$q_kat = trim($_GET['kategori'] ?? '');

$where = "WHERE 1=1";
$params = []; $types = "";
if ($q_search !== '') { $where .= " AND (judul LIKE ? OR deskripsi LIKE ?)"; $params[] = "%" . $q_search . "%"; $params[] = "%" . $q_search . "%"; $types .= "ss"; }
if ($q_kat !== '') { $where .= " AND kategori = ?"; $params[] = $q_kat; $types .= "s"; }

$sql = "SELECT * FROM portofolio $where ORDER BY urutan ASC, id DESC";
$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$portofolio_list = [];
while ($row = mysqli_fetch_assoc($res)) $portofolio_list[] = $row;

// Ambil kategori unik untuk dropdown
$q_kat_unique = "SELECT DISTINCT kategori FROM portofolio WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
$res_kat_unique = mysqli_query($koneksi, $q_kat_unique);
$kategori_unik = [];
while ($row = mysqli_fetch_assoc($res_kat_unique)) $kategori_unik[] = $row['kategori'];

include __DIR__ . '/includes/header.php';

$flash = get_flash();
if ($flash): ?>
  <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title">Kelola Portofolio</div>
    <div class="page-head__subtitle">Kelola galeri hasil pengerjaan yang tampil di website</div>
  </div>
  <div class="page-head__actions">
    <a href="portofolio_form.php" class="btn btn--brown">+ Tambah Portofolio</a>
  </div>
</div>

<!-- Search + filter -->
<form method="get" action="" class="toolbar-row">
  <div class="search-form">
    <span class="icon">🔍</span>
    <input type="text" name="q" value="<?= e($q_search) ?>" placeholder="Cari portofolio...">
  </div>
  <select name="kategori" class="filter-select">
    <option value="">Semua Kategori</option>
    <?php foreach ($kategori_unik as $kat): ?>
      <option value="<?= e($kat) ?>" <?= $q_kat === $kat ? 'selected' : '' ?>><?= e($kat) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn--outline btn--sm">Filter</button>
  <a href="portofolio.php" class="btn btn--outline btn--sm">Reset</a>
</form>

<!-- Tabel portofolio -->
<div class="table-card">
  <table class="tabel">
    <thead>
      <tr>
        <th>Foto</th>
        <th>Judul / Deskripsi</th>
        <th>Kategori</th>
        <th>Status</th>
        <th style="text-align: right;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($portofolio_list)): ?>
        <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada portofolio. Klik "Tambah Portofolio" untuk menambah.</td></tr>
      <?php else: foreach ($portofolio_list as $porto): ?>
        <tr>
          <td>
            <?php if (!empty($porto['foto'])): ?>
              <span class="thumb"><img src="../uploads/<?= e($porto['foto']) ?>" alt=""></span>
            <?php else: ?>
              <span class="thumb-kosong">—</span>
            <?php endif; ?>
          </td>
          <td style="max-width: 380px;">
            <div class="nama-cell"><?= e($porto['judul']) ?></div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;"><?= e($porto['deskripsi']) ?></div>
          </td>
          <td>
            <?php if (!empty($porto['kategori'])): ?>
              <span class="badge"><?= e($porto['kategori']) ?></span>
            <?php else: ?>
              <span style="color: var(--gray-mid);">-</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="post" action="portofolio_toggle.php" style="display: inline;"
                  onchange="this.submit();">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$porto['id'] ?>">
              <div class="toggle-wrap">
                <button type="button" class="toggle <?= $porto['status_tampil'] ? 'is-on' : '' ?>"
                        onclick="this.form.submit();"></button>
                <span class="toggle-label"><?= $porto['status_tampil'] ? 'Tampil' : 'Sembunyi' ?></span>
              </div>
            </form>
          </td>
          <td>
            <div class="aksi" style="justify-content: flex-end;">
              <a href="portofolio_form.php?id=<?= (int)$porto['id'] ?>" class="link-edit" title="Edit">✎</a>
              <form method="post" action="portofolio_hapus.php" style="display: inline;"
                    onsubmit="return confirm('Yakin hapus portofolio <?= e($porto['judul']) ?>?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$porto['id'] ?>">
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
