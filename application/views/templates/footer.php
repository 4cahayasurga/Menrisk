<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template: Footer
 * IFPRO Risk Command Center
 * 
 * Reusable Footer, Toast, dan Global Scripts untuk seluruh view
 */
?>
  <!-- Toast Notification Container -->
  <div id="toast" class="toast"><?= htmlspecialchars($toast_message ?? 'Perubahan berhasil disimpan') ?></div>

  <!-- Common Responsive & Utility Scripts (jQuery 3) -->
  <script>
    $(function() {
      const $sidebar = $('.sidebar');
      const $menuBtn = $('.menu');
      const $toggleBtn = $('#sidebarToggleBtn');
      const $body = $('body');

      // 1. Inisialisasi state sidebar collapse dari localStorage
      const isCollapsed = localStorage.getItem('ifpro_sidebar_collapsed') === 'true';
      if (isCollapsed && $(window).width() > 760) {
        $body.addClass('sidebar-collapsed');
        document.documentElement.classList.add('sidebar-is-collapsed');
        if ($toggleBtn.length) {
          $toggleBtn.find('span').text('»');
          $toggleBtn.attr('title', 'Expand Sidebar');
        }
      } else {
        $body.removeClass('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-is-collapsed');
      }

      // 2. Tombol toggle collapse/expand desktop
      if ($toggleBtn.length) {
        $toggleBtn.on('click', function(e) {
          e.stopPropagation();
          $body.toggleClass('sidebar-collapsed');
          const collapsedNow = $body.hasClass('sidebar-collapsed');
          if (collapsedNow) {
            document.documentElement.classList.add('sidebar-is-collapsed');
          } else {
            document.documentElement.classList.remove('sidebar-is-collapsed');
          }
          localStorage.setItem('ifpro_sidebar_collapsed', collapsedNow);
          $toggleBtn.find('span').text(collapsedNow ? '»' : '«');
          $toggleBtn.attr('title', collapsedNow ? 'Expand Sidebar' : 'Collapse Sidebar');
        });
      }

      // 3. Responsive mobile toggle (tombol hamburger)
      if ($menuBtn.length && $sidebar.length) {
        $menuBtn.on('click', function(e) {
          e.stopPropagation();
          $sidebar.toggleClass('open');
        });
        // Auto-close sidebar jika klik di luar area sidebar pada mobile screen
        $(document).on('click', function(e) {
          if ($(window).width() <= 760 && !$sidebar.is(e.target) && !$sidebar.has(e.target).length && !$menuBtn.is(e.target) && !$menuBtn.has(e.target).length && $sidebar.hasClass('open')) {
            $sidebar.removeClass('open');
          }
        });
      }
    });

    // Global Toast Notification Helper
    let toastTimeout = null;
    function showToast(msg) {
      const $t = $('#toast');
      if ($t.length) {
        if (msg) $t.text(msg);
        $t.addClass('show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(function() {
          $t.removeClass('show');
        }, 2600);
      }
    }
  </script>

  <?php if (!empty($extra_js)): ?>
    <?= $extra_js ?>
  <?php endif; ?>

  <?php $ci =& get_instance(); if ($ci->session->userdata('logged_in')): ?>
  <!-- MODAL: GANTI PASSWORD (Dibuka dari dropdown profil -> Change Password) -->
  <div class="modal-backdrop" id="changePasswordModal">
    <div class="modal-box" style="width:min(440px,100%)">
      <button type="button" class="close" onclick="closeChangePasswordModal()" aria-label="Tutup">×</button>
      <p class="eyebrow">KEAMANAN AKUN</p>
      <h2 style="margin-bottom:16px">Ganti Password</h2>

      <form id="changePasswordForm" onsubmit="submitChangePassword(event)" autocomplete="off">
        <div class="form-row">
          <label for="cpCurrent">Password Lama</label>
          <input type="password" name="current_password" id="cpCurrent" required autocomplete="current-password" />
        </div>
        <div class="form-row">
          <label for="cpNew">Password Baru</label>
          <input type="password" name="new_password" id="cpNew" required minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter" />
        </div>
        <div class="form-row">
          <label for="cpConfirm">Konfirmasi Password Baru</label>
          <input type="password" name="confirm_password" id="cpConfirm" required minlength="6" autocomplete="new-password" />
        </div>

        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);cursor:pointer">
          <input type="checkbox" id="cpShow" onchange="toggleChangePasswordVisibility(this.checked)" /> Tampilkan password
        </label>

        <div id="cpError" style="display:none;margin-top:12px;background:#fdebed;color:var(--red);border-radius:7px;padding:9px 12px;font-size:12px;font-weight:600"></div>

        <div style="margin-top:22px;display:flex;gap:10px;justify-content:flex-end;">
          <button type="button" class="ghost" onclick="closeChangePasswordModal()">Batal</button>
          <button type="submit" class="primary" id="cpSubmitBtn" style="margin-left:0">Simpan Password</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // =========================================================================
    // DROPDOWN PROFIL (jQuery 3)
    // =========================================================================
    function toggleProfileMenu(e) {
      if (e && e.stopPropagation) e.stopPropagation();
      const $menu = $('#profileMenu');
      if (!$menu.length) return;
      const isOpen = $menu.toggleClass('open').hasClass('open');
      $('#profileMenuBtn').attr('aria-expanded', isOpen ? 'true' : 'false');
    }

    function closeProfileMenu() {
      const $menu = $('#profileMenu');
      if (!$menu.length) return;
      $menu.removeClass('open');
      $('#profileMenuBtn').attr('aria-expanded', 'false');
    }

    // Klik di luar dropdown menutup menu
    $(document).on('click', function(e) {
      const $menu = $('#profileMenu');
      if ($menu.length && !$menu.is(e.target) && !$menu.has(e.target).length) {
        closeProfileMenu();
      }
    });

    // =========================================================================
    // MODAL GANTI PASSWORD (jQuery 3)
    // =========================================================================
    function openChangePasswordModal() {
      $('#changePasswordForm')[0].reset();
      toggleChangePasswordVisibility(false);
      showChangePasswordError('');
      $('#changePasswordModal').addClass('show');
      setTimeout(function() { $('#cpCurrent').trigger('focus'); }, 50);
    }

    function closeChangePasswordModal() {
      $('#changePasswordModal').removeClass('show');
    }

    function toggleChangePasswordVisibility(show) {
      $('#cpCurrent, #cpNew, #cpConfirm').attr('type', show ? 'text' : 'password');
      $('#cpShow').prop('checked', show);
    }

    function showChangePasswordError(msg) {
      const $el = $('#cpError');
      if (msg) {
        $el.text(msg).show();
      } else {
        $el.hide().text('');
      }
    }

    function submitChangePassword(e) {
      if (e && e.preventDefault) e.preventDefault();

      const current = $.trim($('#cpCurrent').val());
      const newPwd  = $.trim($('#new_password, #cpNew').val());
      const confirm = $.trim($('#confirm_password, #cpConfirm').val());

      if (newPwd.length < 6)   return showChangePasswordError('Password baru minimal 6 karakter.');
      if (newPwd !== confirm)  return showChangePasswordError('Konfirmasi password baru tidak sama.');
      if (newPwd === current)  return showChangePasswordError('Password baru harus berbeda dari password lama.');
      showChangePasswordError('');

      const $btn = $('#cpSubmitBtn');
      $btn.prop('disabled', true).text('Menyimpan...');

      const formData = new FormData($('#changePasswordForm')[0]);

      $.ajax({
        url: '<?= site_url("auth/change_password") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
          if (data.status === 'success') {
            closeChangePasswordModal();
            showToast(data.message);
          } else {
            showChangePasswordError(data.message || 'Gagal mengganti password.');
          }
        },
        error: function() {
          showChangePasswordError('Terjadi kesalahan koneksi.');
        },
        complete: function() {
          $btn.prop('disabled', false).text('Simpan Password');
        }
      });
    }

    $(function() {
      // Form submit binding
      $('#changePasswordForm').on('submit', submitChangePassword);

      // Tutup modal dengan klik backdrop
      $('#changePasswordModal').on('click', function(e) {
        if (e.target === this) closeChangePasswordModal();
      });

      // Escape key listener
      $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          closeChangePasswordModal();
          closeProfileMenu();
        }
      });
    });
  </script>

    
  <?php endif; ?>

  <!-- Bootstrap 5 Bundle JS -->
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  
</body>
</html>