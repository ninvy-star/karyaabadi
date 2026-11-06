<?php
// admin/produk_tambah.php - form tambah produk
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'produk';
$judul = 'Tambah Produk Baru';

// Ambil kategori & material untuk dropdown
$kategoris = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);
$materials = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM material WHERE status = 'aktif' ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);

$error = '';
$old = ['nama'=>'', 'kategori_id'=>'', 'material_id'=>'', 'harga'=>'', 'deskripsi'=>'', 'ukuran'=>'', 'is_konsultasi'=>0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nama = trim($_POST['nama'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0) ?: null;
    $material_id = (int)($_POST['material_id'] ?? 0) ?: null;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $ukuran = trim($_POST['ukuran'] ?? '');
    $harga = parse_rupiah($_POST['harga'] ?? '');
    $is_konsultasi = isset($_POST['is_konsultasi']) ? 1 : 0;
    if ($is_konsultasi) $harga = null;

    $old = compact('nama','kategori_id','material_id','deskripsi','ukuran','harga','is_konsultasi') + ['harga'=>$_POST['harga'] ?? ''];

    if ($nama === '') {
        $error = 'Nama produk wajib diisi.';
    } else {
        // Handle upload foto
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $up = handle_upload('produk');
            if (isset($up['error'])) {
                $error = $up['error'];
            } else {
                $foto = $up;
            }
        }

        if (!$error) {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO produk (nama, kategori_id, material_id, deskripsi, ukuran, harga, is_konsultasi, foto)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "siissiis",
                $nama, $kategori_id, $material_id, $deskripsi, $ukuran, $harga, $is_konsultasi, $foto);
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Produk "' . $nama . '" berhasil ditambahkan.');
                header('Location: produk.php');
                exit;
            } else {
                $error = 'Gagal menyimpan produk: ' . mysqli_error($koneksi);
            }
        }
    }
}

include __DIR__ . '/includes/header.php';

if ($error): ?>
  <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title">Tambah Produk Baru</div>
  </div>
  <div class="page-head__actions">
    <a href="produk.php" class="btn btn--back">← Kembali</a>
  </div>
</div>

<form method="post" action="" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-card">
    <div class="form-grid">
      <div>
        <div class="form-group">
          <label for="nama">Nama Produk</label>
          <input type="text" id="nama" name="nama" placeholder="Contoh: Meja Makan Jati"
                 value="<?= e($old['nama']) ?>" required>
        </div>
        <div class="form-group">
          <label for="kategori_id">Kategori</label>
          <select id="kategori_id" name="kategori_id">
            <option value="">Pilih Kategori</option>
            <?php foreach ($kategoris as $kat): ?>
              <option value="<?= (int)$kat['id'] ?>" <?= ($old['kategori_id'] ?? 0) == $kat['id'] ? 'selected' : '' ?>><?= e($kat['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="deskripsi">Deskripsi Produk</label>
          <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan Deskripsi Produk..."><?= e($old['deskripsi']) ?></textarea>
        </div>
      </div>

      <div>
        <div class="form-group">
          <label for="material_id">Material</label>
          <select id="material_id" name="material_id">
            <option value="">Pilih Material</option>
            <?php foreach ($materials as $mat): ?>
              <option value="<?= (int)$mat['id'] ?>" <?= ($old['material_id'] ?? 0) == $mat['id'] ? 'selected' : '' ?>><?= e($mat['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="ukuran">Ukuran / Spesifikasi</label>
          <input type="text" id="ukuran" name="ukuran" placeholder="Contoh: 120 x 60 x 75 cm"
                 value="<?= e($old['ukuran']) ?>">
        </div>
        <div class="form-group">
          <label for="harga">Harga (Rp)</label>
          <input type="text" id="harga" name="harga" placeholder="Rp"
                 value="<?= e($old['harga'] ?? '') ?>" <?= ($old['is_konsultasi'] ?? 0) ? 'disabled' : '' ?>>
          <label style="margin-top: 10px; font-size: 13px; font-weight: 400;">
            <input type="checkbox" name="is_konsultasi" value="1" <?= ($old['is_konsultasi'] ?? 0) ? 'checked' : '' ?>
                   onclick="document.getElementById('harga').disabled = this.checked;">
            Harga oleh konsultasi (sembunyikan harga)
          </label>
        </div>
        <div class="form-group">
          <label>Foto Produk</label>
          <div class="upload-box" onclick="document.getElementById('foto').click();">
            <div class="upload-box__icon">📷</div>
            <div class="upload-box__text">Pilih foto atau seret ke sini</div>
            <div class="upload-box__help">Ukuran maks. 5MB, format JPG, PNG</div>
          </div>
          <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none;">
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="produk.php" class="btn btn--outline">Batal</a>
      <button type="submit" class="btn btn--brown">Simpan Produk</button>
    </div>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
