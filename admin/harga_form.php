<?php
// admin/harga_form.php - form tambah/edit data harga
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'material';

$id = (int)($_GET['id'] ?? 0);
$is_edit = $id > 0;
$judul = $is_edit ? 'Edit Data Harga' : 'Tambah Data Harga';

$harga = ['id'=>0, 'produk_id'=>'', 'ukuran'=>'', 'harga_jati_super'=>'', 'harga_jati_lokal'=>'', 'harga_kayu_rimba'=>''];
$error = '';

if ($is_edit) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM harga WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { set_flash('error', 'Data harga tidak ditemukan.'); header('Location: material.php'); exit; }
    $harga = $row;
}

// Ambil semua produk untuk dropdown
$produks = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id, nama FROM produk ORDER BY nama ASC"), MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $produk_id = (int)($_POST['produk_id'] ?? 0);
    $ukuran = trim($_POST['ukuran'] ?? '');
    $h_js = parse_rupiah($_POST['harga_jati_super'] ?? '');
    $h_jl = parse_rupiah($_POST['harga_jati_lokal'] ?? '');
    $h_kr = parse_rupiah($_POST['harga_kayu_rimba'] ?? '');

    if ($produk_id <= 0) {
        $error = 'Pilih produk terlebih dahulu.';
    } else {
        if ($is_edit) {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE harga SET produk_id=?, ukuran=?, harga_jati_super=?, harga_jati_lokal=?, harga_kayu_rimba=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "isiiii", $produk_id, $ukuran, $h_js, $h_jl, $h_kr, $id);
        } else {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO harga (produk_id, ukuran, harga_jati_super, harga_jati_lokal, harga_kayu_rimba) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isiii", $produk_id, $ukuran, $h_js, $h_jl, $h_kr);
        }
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Data harga berhasil ' . ($is_edit ? 'diperbarui.' : 'ditambahkan.'));
            header('Location: material.php');
            exit;
        } else {
            $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
    $harga['produk_id'] = $produk_id;
    $harga['ukuran'] = $ukuran;
    $harga['harga_jati_super'] = $_POST['harga_jati_super'] ?? '';
    $harga['harga_jati_lokal'] = $_POST['harga_jati_lokal'] ?? '';
    $harga['harga_kayu_rimba'] = $_POST['harga_kayu_rimba'] ?? '';
}

include __DIR__ . '/includes/header.php';

if ($error): ?>
  <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title"><?= e($judul) ?></div>
    <div class="page-head__subtitle">Atur harga produk berdasarkan jenis material dan ukuran.</div>
  </div>
  <div class="page-head__actions">
    <a href="material.php" class="btn btn--back">← Kembali</a>
  </div>
</div>

<form method="post" action="">
  <?= csrf_field() ?>
  <div class="form-card" style="max-width: 700px;">
    <div class="form-group">
      <label for="produk_id">Jenis Produk</label>
      <select id="produk_id" name="produk_id" required>
        <option value="">Pilih Produk</option>
        <?php foreach ($produks as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= $harga['produk_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="ukuran">Ukuran</label>
      <input type="text" id="ukuran" name="ukuran" required value="<?= e($harga['ukuran']) ?>"
             placeholder="Contoh: 120 x 60 x 75 cm">
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="harga_jati_super">Harga Jati Super</label>
        <input type="text" id="harga_jati_super" name="harga_jati_super"
               value="<?= $harga['harga_jati_super'] !== null && $harga['harga_jati_super'] !== '' ? number_format((int)$harga['harga_jati_super'], 0, ',', '.') : '' ?>"
               placeholder="Rp">
      </div>
      <div class="form-group">
        <label for="harga_jati_lokal">Harga Jati Lokal</label>
        <input type="text" id="harga_jati_lokal" name="harga_jati_lokal"
               value="<?= $harga['harga_jati_lokal'] !== null && $harga['harga_jati_lokal'] !== '' ? number_format((int)$harga['harga_jati_lokal'], 0, ',', '.') : '' ?>"
               placeholder="Rp">
      </div>
    </div>
    <div class="form-group">
      <label for="harga_kayu_rimba">Harga Kayu Rimba</label>
      <input type="text" id="harga_kayu_rimba" name="harga_kayu_rimba"
             value="<?= $harga['harga_kayu_rimba'] !== null && $harga['harga_kayu_rimba'] !== '' ? number_format((int)$harga['harga_kayu_rimba'], 0, ',', '.') : '' ?>"
             placeholder="Rp">
    </div>

    <div class="form-actions">
      <a href="material.php" class="btn btn--outline">Batal</a>
      <button type="submit" class="btn btn--brown">Simpan Harga</button>
    </div>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
