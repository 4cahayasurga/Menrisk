<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template: Topbar Header
 * IFPRO Risk Command Center
 * 
 * Reusable top header bar di dalam <main>
 */
$ci =& get_instance();
$user_nama = $current_user['nama'] ?? $ci->session->userdata('nama') ?? 'Pengguna';
$user_initials = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $user_nama) ?: 'US', 0, 2));
?>
<!-- Top Header Bar -->
<header>
  <button class="menu" aria-label="Buka menu">☰</button>
  <div>
    <?php if (!empty($eyebrow)): ?>
      <p class="eyebrow"><?= htmlspecialchars($eyebrow) ?></p>
    <?php endif; ?>
    <h1><?= htmlspecialchars($page_heading ?? $title ?? 'IFPRO Risk Command Center') ?></h1>
    <?php if (!empty($subtitle)): ?>
      <p class="subtitle"><?= htmlspecialchars($subtitle) ?></p>
    <?php endif; ?>
  </div>
  <div class="header-actions">
    <?= $header_actions ?? '' ?>
    <?php if (!isset($show_avatar) || $show_avatar !== false): ?>
      <div class="avatar" title="<?= htmlspecialchars($user_nama) ?>">
        <?= $user_initials ?>
      </div>
    <?php endif; ?>
  </div>
</header>
