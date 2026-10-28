<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template: Header
 * IFPRO Risk Command Center
 * 
 * Reusable HTML Head & Layout Header untuk seluruh view
 */
$ci =& get_instance();
$page_title = $title ?? 'IFPRO Risk Command Center';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="description" content="<?= htmlspecialchars($meta_description ?? 'IFPRO Risk Command Center — dashboard risiko eksekutif BOD') ?>" />
  <title><?= htmlspecialchars($page_title) ?></title>
  
  <!-- CSS Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('styles.css') ?>" />

  <?php if (!empty($extra_css)): ?>
    <?= $extra_css ?>
  <?php endif; ?>
  <?php if (!empty($extra_head)): ?>
    <?= $extra_head ?>
  <?php endif; ?>
</head>
<body>
<?php
if (!empty($include_sidebar)) {
    $this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? null]);
}
?>
