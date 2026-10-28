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

  <!-- Common Responsive & Utility Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Responsive sidebar toggle
      const menuBtn = document.querySelector('.menu');
      const sidebarEl = document.querySelector('.sidebar');
      if (menuBtn && sidebarEl) {
        menuBtn.onclick = (e) => {
          e.stopPropagation();
          sidebarEl.classList.toggle('open');
        };
        // Auto-close sidebar jika klik di luar area sidebar pada mobile screen
        document.addEventListener('click', (e) => {
          if (window.innerWidth <= 760 && !sidebarEl.contains(e.target) && !menuBtn.contains(e.target) && sidebarEl.classList.contains('open')) {
            sidebarEl.classList.remove('open');
          }
        });
      }
    });

    // Global Toast Notification Helper
    function showToast(msg) {
      const t = document.getElementById('toast');
      if (t) {
        if (msg) t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2600);
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
    // DROPDOWN PROFIL (klik avatar -> tampil menu "Change Password")
    // =========================================================================
    function toggleProfileMenu(e) {
      e.stopPropagation();
      const menu = document.getElementById('profileMenu');
      if (!menu) return;
      const open = menu.classList.toggle('open');
      document.getElementById('profileMenuBtn').setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function closeProfileMenu() {
      const menu = document.getElementById('profileMenu');
      if (!menu) return;
      menu.classList.remove('open');
      document.getElementById('profileMenuBtn').setAttribute('aria-expanded', 'false');
    }

    // Klik di luar dropdown menutup menu
    document.addEventListener('click', e => {
      const menu = document.getElementById('profileMenu');
      if (menu && !menu.contains(e.target)) closeProfileMenu();
    });

    // =========================================================================
    // MODAL GANTI PASSWORD
    // =========================================================================
    function openChangePasswordModal() {
      document.getElementById('changePasswordForm').reset();
      toggleChangePasswordVisibility(false);
      showChangePasswordError('');
      document.getElementById('changePasswordModal').classList.add('show');
      setTimeout(() => document.getElementById('cpCurrent').focus(), 50);
    }

    function closeChangePasswordModal() {
      document.getElementById('changePasswordModal').classList.remove('show');
    }

    function toggleChangePasswordVisibility(show) {
      ['cpCurrent', 'cpNew', 'cpConfirm'].forEach(id => {
        document.getElementById(id).type = show ? 'text' : 'password';
      });
      document.getElementById('cpShow').checked = show;
    }

    function showChangePasswordError(msg) {
      const el = document.getElementById('cpError');
      el.textContent = msg;
      el.style.display = msg ? 'block' : 'none';
    }

    async function submitChangePassword(e) {
      e.preventDefault();
      const current = document.getElementById('cpCurrent').value.trim();
      const newPwd  = document.getElementById('cpNew').value.trim();
      const confirm = document.getElementById('cpConfirm').value.trim();

      if (newPwd.length < 6)   return showChangePasswordError('Password baru minimal 6 karakter.');
      if (newPwd !== confirm)  return showChangePasswordError('Konfirmasi password baru tidak sama.');
      if (newPwd === current)  return showChangePasswordError('Password baru harus berbeda dari password lama.');
      showChangePasswordError('');

      const btn = document.getElementById('cpSubmitBtn');
      btn.disabled = true;
      btn.textContent = 'Menyimpan...';

      try {
        const res = await fetch('<?= site_url("auth/change_password") ?>', {
          method: 'POST',
          body: new FormData(document.getElementById('changePasswordForm')),
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.status === 'success') {
          closeChangePasswordModal();
          showToast(data.message);
        } else {
          showChangePasswordError(data.message || 'Gagal mengganti password.');
        }
      } catch (err) {
        showChangePasswordError('Terjadi kesalahan koneksi.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan Password';
      }
    }

    // Tutup modal dengan klik backdrop; Escape menutup modal & dropdown
    document.getElementById('changePasswordModal').addEventListener('click', e => {
      if (e.target.id === 'changePasswordModal') closeChangePasswordModal();
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        closeChangePasswordModal();
        closeProfileMenu();
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>