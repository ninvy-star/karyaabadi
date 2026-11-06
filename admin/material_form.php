<?php
// admin/material_form.php - form tambah/edit material (mode ditentukan oleh ?id=)
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'material';

$id = (int)($_GET['id'] ?? 0);
$is_edit = $id > 0;
$judul = $is_edit ? 'Edit Material' : 'Tambah Material';

// State form
$material = ['id'=>0, 'nama'=>'', 'kode'=>'', 'deskripsi'=>'', 'foto'=>'', 'status'=>'aktif'];
$error = '';

if ($is_edit) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM material WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { set_flash('error', 'Material tidak ditemukan.'); header('Location: material.php'); exit; }
    $material = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nama = trim($_POST['nama'] ?? '');
    $kode = trim($_POST['kode'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = $_POST['status'] ?? 'aktif';
    $foto_lama = $material['foto'];
    $foto_baru = $foto_lama;

    if ($nama === '') {
        $error = 'Nama material wajib diisi.';
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $up = handle_upload('material', $foto_lama);
            if (isset($up['error'])) {
                $error = $up['error'];
            } else {
                $foto_baru = $up;
            }
        }

        if (!$error) {
            if ($is_edit) {
                $stmt = mysqli_prepare($koneksi,
                    "UPDATE material SET nama=?, kode=?, deskripsi=?, foto=?, status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssssi", $nama, $kode, $deskripsi, $foto_baru, $status, $id);
            } else {
                $stmt = mysqli_prepare($koneksi,
                    "INSERT INTO material (nama, kode, deskripsi, foto, status) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssss", $nama, $kode, $deskripsi, $foto_baru, $status);
            }
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Material "' . $nama . '" berhasil ' . ($is_edit ? 'diperbarui.' : 'ditambahkan.'));
                header('Location: material.php');
                exit;
            } else {
                $error = 'Gagal menyimpan material: ' . mysqli_error($koneksi);
            }
        }
    }
    $material['nama'] = $nama;
    $material['kode'] = $kode;
    $material['deskripsi'] = $deskripsi;
    $material['status'] = $status;
}

include __DIR__ . '/includes/header.php';

if ($error): ?>
  <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title"><?= e($judul) ?></div>
    <div class="page-head__subtitle">Atur material yang ditampilkan pada halaman katalog.</div>
  </div>
  <div class="page-head__actions">
    <a href="material.php" class="btn btn--back">← Kembali</a>
  </div>
</div>

<form method="post" action="" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-card" style="max-width: 800px;">
    <div class="form-group">
      <label for="nama">Nama Material</label>
      <input type="text" id="nama" name="nama" required value="<?= e($material['nama']) ?>"
             placeholder="Contoh: Kayu Jati">
    </div>

    <div class="form-group">
      <label for="kode">Kode Material</label>
      <input type="text" id="kode" name="kode" value="<?= e($material['kode']) ?>" maxlength="10"
             placeholder="Contoh: JS, JL, KR" style="max-width: 200px;">
      <span class="help">Kode singkat untuk identifikasi internal (maks 10 karakter).</span>
    </div>

    <div class="form-group">
      <label for="deskripsi">Deskripsi Material</label>
      <textarea id="deskripsi" name="deskripsi" maxlength="500"
                oninput="document.getElementById('counter').textContent = this.value.length + '/500';"><?= e($material['deskripsi']) ?></textarea>
      <div class="char-counter"><span id="counter"><?= strlen($material['deskripsi']) ?>/500</span></div>
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="aktif" <?= $material['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $material['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
      </select>
      <span class="help">Material nonaktif tidak ditampilkan di website publik.</span>
    </div>

    <div class="form-group">
      <label>Foto Material</label>
      <?php if (!empty($material['foto'])): ?>
        <div class="foto-sekarang">
          <img src="../uploads/<?= e($material['foto']) ?>" alt="">
        </div>
      <?php endif; ?>
      <div class="upload-box" onclick="document.getElementById('foto').click();" style="margin-top: 12px;">
        <div class="upload-box__icon">📷</div>
        <div class="upload-box__text"><?= !empty($material['foto']) ? 'Ubah Foto' : 'Pilih foto atau seret ke sini' ?></div>
        <div class="upload-box__help">Format JPG, PNG. Maks. 2MB</div>
      </div>
      <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none;">
    </div>

    <div class="form-actions">
      <a href="material.php" class="btn btn--outline">Batal</a>
      <button type="submit" class="btn btn--brown">Simpan Material</button>
    </div>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
