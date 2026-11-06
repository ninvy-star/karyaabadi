<?php
// admin/produk_edit.php - form edit produk
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'produk';
$judul = 'Edit Produk';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: produk.php'); exit; }

// Ambil produk existing
$stmt = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$produk) { header('Location: produk.php'); exit; }

// Ambil kategori & material
$kategoris = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);
$materials = mysqli_fetch_all(mysqli_query($koneksi, "SELECT * FROM material WHERE status = 'aktif' ORDER BY urutan ASC, nama ASC"), MYSQLI_ASSOC);

$error = '';

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

    if ($nama === '') {
        $error = 'Nama produk wajib diisi.';
    } else {
        $foto_lama = $produk['foto'];
        $foto_baru = $foto_lama;
        // Handle upload foto baru (opsional)
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $up = handle_upload('produk', $foto_lama);
            if (isset($up['error'])) {
                $error = $up['error'];
            } else {
                $foto_baru = $up;
            }
        }

        if (!$error) {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE produk SET nama=?, kategori_id=?, material_id=?, deskripsi=?, ukuran=?, harga=?, is_konsultasi=?, foto=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "siissiisi",
                $nama, $kategori_id, $material_id, $deskripsi, $ukuran, $harga, $is_konsultasi, $foto_baru, $id);
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Produk "' . $nama . '" berhasil diperbarui.');
                header('Location: produk.php');
                exit;
            } else {
                $error = 'Gagal memperbarui produk: ' . mysqli_error($koneksi);
            }
        }
    }
    // Refresh produk dengan input terbaru
    $produk['nama'] = $nama;
    $produk['kategori_id'] = $kategori_id;
    $produk['material_id'] = $material_id;
    $produk['deskripsi'] = $deskripsi;
    $produk['ukuran'] = $ukuran;
    $produk['is_konsultasi'] = $is_konsultasi;
    $produk['harga_input'] = $_POST['harga'] ?? '';
} else {
    $produk['harga_input'] = $produk['harga'] !== null ? number_format((int)$produk['harga'], 0, ',', '.') : '';
}

include __DIR__ . '/includes/header.php';

if ($error): ?>
  <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title">Edit Produk</div>
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
          <input type="text" id="nama" name="nama" required value="<?= e($produk['nama']) ?>">
        </div>
        <div class="form-group">
          <label for="kategori_id">Kategori</label>
          <select id="kategori_id" name="kategori_id">
            <option value="">Pilih Kategori</option>
            <?php foreach ($kategoris as $kat): ?>
              <option value="<?= (int)$kat['id'] ?>" <?= $produk['kategori_id'] == $kat['id'] ? 'selected' : '' ?>><?= e($kat['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="deskripsi">Deskripsi Produk</label>
          <textarea id="deskripsi" name="deskripsi"><?= e($produk['deskripsi']) ?></textarea>
        </div>
      </div>

      <div>
        <div class="form-group">
          <label for="material_id">Material</label>
          <select id="material_id" name="material_id">
            <option value="">Pilih Material</option>
            <?php foreach ($materials as $mat): ?>
              <option value="<?= (int)$mat['id'] ?>" <?= $produk['material_id'] == $mat['id'] ? 'selected' : '' ?>><?= e($mat['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="ukuran">Ukuran / Spesifikasi</label>
          <input type="text" id="ukuran" name="ukuran" value="<?= e($produk['ukuran']) ?>">
        </div>
        <div class="form-group">
          <label for="harga">Harga (Rp)</label>
          <input type="text" id="harga" name="harga" value="<?= e($produk['harga_input']) ?>" <?= $produk['is_konsultasi'] ? 'disabled' : '' ?>>
          <label style="margin-top: 10px; font-size: 13px; font-weight: 400;">
            <input type="checkbox" name="is_konsultasi" value="1" <?= $produk['is_konsultasi'] ? 'checked' : '' ?>
                   onclick="document.getElementById('harga').disabled = this.checked;">
            Harga oleh konsultasi
          </label>
        </div>
        <div class="form-group">
          <label>Foto Produk</label>
          <?php if (!empty($produk['foto'])): ?>
            <div class="foto-sekarang">
              <img src="../uploads/<?= e($produk['foto']) ?>" alt="">
            </div>
          <?php endif; ?>
          <div class="upload-box" onclick="document.getElementById('foto').click();" style="margin-top: 12px;">
            <div class="upload-box__icon">📷</div>
            <div class="upload-box__text"><?= !empty($produk['foto']) ? 'Ganti Foto' : 'Pilih foto atau seret ke sini' ?></div>
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
