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
    <div>
      <strong>IFPRO</strong>
      <small>Risk Command Center</small>
    </div>
  </div>
  
  <nav>
    <?php if ($is_dashboard): ?>
      <button class="nav <?= ($active_menu === 'overview') ? 'active' : '' ?>" data-view="overview">
        <span>◫</span> Executive Overview
      </button>
      <button class="nav <?= ($active_menu === 'register') ? 'active' : '' ?>" data-view="register">
        <span>≡</span> Risk Register
      </button>
      <button class="nav <?= ($active_menu === 'mitigation') ? 'active' : '' ?>" data-view="mitigation">
        <span>✓</span> Mitigation Monitor
      </button>
      <button class="nav <?= ($active_menu === 'kri') ? 'active' : '' ?>" data-view="kri">
        <span>⌁</span> Key Risk Indicators
      </button>
    <?php else: ?>
      <a href="<?= site_url('menrisk') ?>" class="nav" style="text-decoration:none">
        <span>◫</span> Executive Overview
      </a>
      <a href="<?= site_url('menrisk') ?>#register" class="nav" style="text-decoration:none">
        <span>≡</span> Risk Register
      </a>
      <a href="<?= site_url('menrisk') ?>#mitigation" class="nav" style="text-decoration:none">
        <span>✓</span> Mitigation Monitor
      </a>
      <a href="<?= site_url('menrisk') ?>#kri" class="nav" style="text-decoration:none">
        <span>⌁</span> Key Risk Indicators
      </a>
    <?php endif; ?>

    <!-- MENU SETTINGS (Hanya Tampil untuk Administrator) -->
    <?php if ($user_level === 'admin'): ?>
      <div class="sidebar-section">SETTINGS</div>
      <a href="<?= site_url('settings/user') ?>" class="nav <?= ($active_menu === 'user') ? 'active' : '' ?>" style="text-decoration:none">
        <span>⌁</span> User
      </a>
      <a href="<?= site_url('settings/department') ?>" class="nav <?= ($active_menu === 'department') ? 'active' : '' ?>" style="text-decoration:none">
        <span>⌁</span> Department
      </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-foot">
    <small>LOGGED IN USER</small>
    <strong><?= htmlspecialchars($user_nama) ?></strong>
    <span style="font-size:11px;color:#29b6af;text-transform:uppercase">Role: <?= htmlspecialchars($user_level) ?></span>
    <a href="<?= site_url('auth/logout') ?>" style="color:#ff8d94;font-size:12px;margin-top:6px;text-decoration:none;font-weight:700">⎋ Keluar / Logout</a>
  </div>
</aside>
