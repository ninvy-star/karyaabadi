<?php
// admin/portofolio_form.php - form tambah/edit portofolio
include __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/koneksi.php';

$menu_aktif = 'portofolio';

$id = (int)($_GET['id'] ?? 0);
$is_edit = $id > 0;
$judul = $is_edit ? 'Edit Portofolio' : 'Tambah Portofolio';

$porto = ['id'=>0, 'judul'=>'', 'kategori'=>'', 'deskripsi'=>'', 'foto'=>'', 'status_tampil'=>1];
$error = '';

if ($is_edit) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM portofolio WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { set_flash('error', 'Portofolio tidak ditemukan.'); header('Location: portofolio.php'); exit; }
    $porto = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $judul_p = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_tampil = isset($_POST['status_tampil']) ? 1 : 0;
    $foto_lama = $porto['foto'];
    $foto_baru = $foto_lama;

    if ($judul_p === '') {
        $error = 'Judul portofolio wajib diisi.';
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $up = handle_upload('portofolio', $foto_lama);
            if (isset($up['error'])) {
                $error = $up['error'];
            } else {
                $foto_baru = $up;
            }
        }

        if (!$error) {
            if ($is_edit) {
                $stmt = mysqli_prepare($koneksi,
                    "UPDATE portofolio SET judul=?, kategori=?, deskripsi=?, foto=?, status_tampil=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssssii", $judul_p, $kategori, $deskripsi, $foto_baru, $status_tampil, $id);
            } else {
                $stmt = mysqli_prepare($koneksi,
                    "INSERT INTO portofolio (judul, kategori, deskripsi, foto, status_tampil) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssssi", $judul_p, $kategori, $deskripsi, $foto_baru, $status_tampil);
            }
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Portofolio "' . $judul_p . '" berhasil ' . ($is_edit ? 'diperbarui.' : 'ditambahkan.'));
                header('Location: portofolio.php');
                exit;
            } else {
                $error = 'Gagal menyimpan portofolio: ' . mysqli_error($koneksi);
            }
        }
    }
    $porto['judul'] = $judul_p;
    $porto['kategori'] = $kategori;
    $porto['deskripsi'] = $deskripsi;
    $porto['status_tampil'] = $status_tampil;
}

include __DIR__ . '/includes/header.php';

if ($error): ?>
  <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="page-head">
  <div>
    <div class="page-head__title"><?= e($judul) ?></div>
  </div>
  <div class="page-head__actions">
    <a href="portofolio.php" class="btn btn--back">← Kembali</a>
  </div>
</div>

<form method="post" action="" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-card">
    <div class="form-grid">
      <!-- Left: foto -->
      <div>
        <div class="form-group">
          <label>Foto Portofolio</label>
          <?php if (!empty($porto['foto'])): ?>
            <div class="foto-sekarang" style="aspect-ratio: 4/3;">
              <img src="../uploads/<?= e($porto['foto']) ?>" alt="">
            </div>
          <?php endif; ?>
          <button type="button" class="btn btn--brown btn--block" style="margin-top: 12px;"
                  onclick="document.getElementById('foto').click();">
            📷 Ganti Foto
          </button>
          <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" style="display:none;">
          <span class="help" style="margin-top: 8px;">Format JPG, PNG. Maks. 5MB.</span>
        </div>
      </div>

      <!-- Right: form fields -->
      <div>
        <div class="form-group">
          <label for="judul">Judul Portofolio</label>
          <input type="text" id="judul" name="judul" required value="<?= e($porto['judul']) ?>"
                 placeholder="Contoh: Meja Makan Ukuran Panjang">
        </div>

        <div class="form-group">
          <label for="kategori">Kategori</label>
          <input type="text" id="kategori" name="kategori" value="<?= e($porto['kategori']) ?>"
                 placeholder="Contoh: Meja Makan" list="kategori-list">
          <datalist id="kategori-list">
            <?php
            // Ambil kategori yang sudah dipakai untuk autocomplete
            $res_kat = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM portofolio WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
            while ($row = mysqli_fetch_assoc($res_kat)): ?>
              <option value="<?= e($row['kategori']) ?>">
            <?php endwhile; ?>
          </datalist>
        </div>

        <div class="form-group">
          <label for="deskripsi">Deskripsi</label>
          <textarea id="deskripsi" name="deskripsi" style="min-height: 180px;"><?= e($porto['deskripsi']) ?></textarea>
        </div>

        <div class="form-group">
          <label>Status Tampilan</label>
          <div class="toggle-wrap">
            <input type="checkbox" id="status_tampil" name="status_tampil" value="1"
                   <?= $porto['status_tampil'] ? 'checked' : '' ?>
                   style="width: auto; margin-right: 8px;"
                   onchange="document.getElementById('status-label').textContent = this.checked ? 'Tampilkan di website' : 'Sembunyikan dari website';">
            <span id="status-label" class="toggle-label">
              <?= $porto['status_tampil'] ? 'Tampilkan di website' : 'Sembunyikan dari website' ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="portofolio.php" class="btn btn--outline">Batal</a>
      <button type="submit" class="btn btn--brown">Simpan Perubahan</button>
    </div>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
