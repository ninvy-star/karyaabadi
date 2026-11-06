<?php
// admin/includes/sidebar.php - shared sidebar untuk semua halaman admin
// Variabel yang dibutuhkan: $menu_aktif (string: 'dashboard'|'produk'|'material'|'portofolio'|'website')
$menu_aktif = $menu_aktif ?? '';
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar__brand">
    <span class="sidebar__logo">KA</span>
    <span class="sidebar__brand-text">
      <strong>KARYA ABADI</strong>
      <small>ADMIN</small>
    </span>
  </div>

  <nav class="sidebar__menu">
    <a href="dashboard.php" class="sidebar__item <?= $menu_aktif === 'dashboard' ? 'is-active' : '' ?>">
      <span class="icon">▦</span>
      <span>Dashboard</span>
      <span class="chevron">›</span>
    </a>
    <a href="produk.php" class="sidebar__item <?= $menu_aktif === 'produk' ? 'is-active' : '' ?>">
      <span class="icon">⌂</span>
      <span>Produk</span>
      <span class="chevron">›</span>
    </a>
    <a href="material.php" class="sidebar__item <?= $menu_aktif === 'material' ? 'is-active' : '' ?>">
      <span class="icon">≡</span>
      <span>Material</span>
      <span class="chevron">›</span>
    </a>
    <a href="portofolio.php" class="sidebar__item <?= $menu_aktif === 'portofolio' ? 'is-active' : '' ?>">
      <span class="icon">▣</span>
      <span>Portofolio</span>
      <span class="chevron">›</span>
    </a>
    <a href="../index.php" target="_blank" class="sidebar__item <?= $menu_aktif === 'website' ? 'is-active' : '' ?>">
      <span class="icon">⊙</span>
      <span>Website</span>
      <span class="chevron">›</span>
    </a>
  </nav>

  <div class="sidebar__divider"></div>

  <a href="logout.php" class="sidebar__logout"
     onclick="return confirm('Yakin ingin keluar dari halaman admin?')">
    <span class="icon">⏻</span>
    <span>Logout</span>
  </a>
</aside>
