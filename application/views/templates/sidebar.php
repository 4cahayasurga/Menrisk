<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template: Sidebar Navigation
 * IFPRO Risk Command Center
 * 
 * Reusable navigation sidebar untuk seluruh view (Dashboard, Settings User, Settings Department)
 */
$ci =& get_instance();
$current_class  = strtolower($ci->router->fetch_class());
$current_method = strtolower($ci->router->fetch_method());

$is_dashboard   = ($current_class === 'menrisk' && $current_method === 'index');
$active_menu    = $active_menu ?? ($is_dashboard ? 'overview' : $current_method);

$user_nama  = $current_user['nama'] ?? $ci->session->userdata('nama') ?? 'Pengguna';
$user_level = $current_user['level'] ?? $ci->session->userdata('level') ?? 'user';
?>
<!-- Sidebar Navigation -->
<aside class="sidebar">
  <div class="brand">
    <span class="brandmark">IF</span>
    <div class="brand-text">
      <strong>IFPRO</strong>
      <small>Risk Command Center</small>
    </div>
    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
      <span>«</span>
    </button>
  </div>
  
  <nav>
    <!-- <?php if ($is_dashboard): ?>
      <button class="nav <?= ($active_menu === 'overview') ? 'active' : '' ?>" data-view="overview" title="Executive Overview">
        <span class="nav-icon">◫</span> <span class="nav-label">Executive Overview</span>
      </button>
      <button class="nav <?= ($active_menu === 'register') ? 'active' : '' ?>" data-view="register" title="Risk Register">
        <span class="nav-icon">≡</span> <span class="nav-label">Risk Register</span>
      </button>
      <button class="nav <?= ($active_menu === 'mitigation') ? 'active' : '' ?>" data-view="mitigation" title="Mitigation Monitor">
        <span class="nav-icon">✓</span> <span class="nav-label">Mitigation Monitor</span>
      </button>
      <button class="nav <?= ($active_menu === 'kri') ? 'active' : '' ?>" data-view="kri" title="Key Risk Indicators">
        <span class="nav-icon">⌁</span> <span class="nav-label">Key Risk Indicators</span>
      </button>
    <?php else: ?>
      <a href="<?= site_url('menrisk') ?>" class="nav" style="text-decoration:none" title="Executive Overview">
        <span class="nav-icon">◫</span> <span class="nav-label">Executive Overview</span>
      </a>
      <a href="<?= site_url('menrisk') ?>#register" class="nav" style="text-decoration:none" title="Risk Register">
        <span class="nav-icon">≡</span> <span class="nav-label">Risk Register</span>
      </a>
      <a href="<?= site_url('menrisk') ?>#mitigation" class="nav" style="text-decoration:none" title="Mitigation Monitor">
        <span class="nav-icon">✓</span> <span class="nav-label">Mitigation Monitor</span>
      </a>
      <a href="<?= site_url('menrisk/daftar_kri') ?>" class="nav <?= ($active_menu === 'kri' || $active_menu === 'daftar_kri') ? 'active' : '' ?>" style="text-decoration:none" title="Key Risk Indicators">
        <span class="nav-icon">⌁</span> <span class="nav-label">Key Risk Indicators</span>
      </a>
    <?php endif; ?> -->

    <a href="<?= site_url('menrisk') ?>" class="nav" style="text-decoration:none" title="Executive Overview">
      <span class="nav-icon">◫</span> <span class="nav-label">Executive Overview</span>
    </a>
    <a href="<?= site_url('menrisk/daftar_profil_risiko') ?>" class="nav <?= ($active_menu === 'daftar_profil_risiko') ? 'active' : '' ?>" style="text-decoration:none" title="Profile Risiko">
      <span class="nav-icon">≣</span> <span class="nav-label">Profile Risiko</span>
    </a>
    <a href="<?= site_url('menrisk/daftar_kri') ?>" class="nav <?= ($active_menu === 'kri' || $active_menu === 'daftar_kri') ? 'active' : '' ?>" style="text-decoration:none" title="Key Risk Indicators">
      <span class="nav-icon">⌁</span> <span class="nav-label">Key Risk Indicators</span>
    </a>

    <!-- MENU MASTER DATA -->
    <div class="sidebar-section">MASTER DATA</div>
    <a href="<?= site_url('MasterData/KategoriRisiko') ?>" class="nav <?= ($active_menu === 'm_kategori_risiko') ? 'active' : '' ?>" style="text-decoration:none" title="Kategori Risiko">
        <span class="nav-icon">≡</span> <span class="nav-label">Kategori Risiko</span>
    </a>
    <a href="<?= site_url('MasterData/SumberRisiko') ?>" class="nav <?= ($active_menu === 'm_sumber_risiko') ? 'active' : '' ?>" style="text-decoration:none" title="Sumber Risiko">
        <span class="nav-icon">≡</span> <span class="nav-label">Sumber Risiko</span>
    </a>
    <a href="<?= site_url('MasterData/KriteriaDampakKemungkinan') ?>" class="nav <?= ($active_menu === 'm_kriteria_dampak_kemungkinan') ? 'active' : '' ?>" style="text-decoration:none" title="Kriteria Dampak & Kemungkinan">
        <span class="nav-icon">≡</span> <span class="nav-label">Kriteria Dampak & Kemungkinan</span>
    </a>

    <!-- MENU SETTINGS (Hanya Tampil untuk Administrator) -->
    <?php if ($user_level === 'admin'): ?>
      <div class="sidebar-section">SETTINGS</div>
      <a href="<?= site_url('settings/user') ?>" class="nav <?= ($active_menu === 'user') ? 'active' : '' ?>" style="text-decoration:none" title="User Settings">
        <span class="nav-icon">⌁</span> <span class="nav-label">User</span>
      </a>
      <a href="<?= site_url('settings/department') ?>" class="nav <?= ($active_menu === 'department') ? 'active' : '' ?>" style="text-decoration:none" title="Department Settings">
        <span class="nav-icon">⌁</span> <span class="nav-label">Department</span>
      </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-foot">
    <div class="foot-user-info">
      <small>LOGGED IN USER</small>
      <strong><?= htmlspecialchars($user_nama) ?></strong>
      <span style="font-size:11px;color:#29b6af;text-transform:uppercase">Role: <?= htmlspecialchars($user_level) ?></span>
    </div>
    <a href="<?= site_url('auth/logout') ?>" class="btn-logout" title="Keluar / Logout" style="color:#ff8d94;font-size:12px;margin-top:6px;text-decoration:none;font-weight:700">
      <span class="logout-icon">⎋</span>
      <span class="logout-text">Keluar / Logout</span>
    </a>
  </div>
</aside>