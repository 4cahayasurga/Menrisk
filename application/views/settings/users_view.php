<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'User Management — Settings']);
?>
  <!-- Sidebar Navigation Template -->
  <?php $this->load->view('templates/sidebar', ['active_menu' => 'user']); ?>

  <!-- Main Content Area -->
  <main>
    <header>
      <button class="menu" aria-label="Buka menu">☰</button>
      <div>
        <p class="eyebrow">SETTINGS · ADMINISTRATOR ACCESS ONLY</p>
        <h1>User Management</h1>
        <p class="subtitle">Kelola akun pengguna, level akses (admin / user), dan relasi ke department</p>
      </div>
      <div class="header-actions">
        <button class="primary" onclick="openAddUserModal()">+ Tambah User Baru</button>
        <div class="avatar" title="<?= htmlspecialchars($current_user['nama']) ?>">
          <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
        </div>
      </div>
    </header>

    <!-- User Table Panel -->
    <section class="panel full">
      <div class="panel-head">
        <div>
          <h2>Daftar Pengguna Sistem</h2>
          <p>Total <?= count($users) ?> akun terdaftar dalam sistem ERM</p>
        </div>
        <input id="userSearch" placeholder="Cari nama atau username..." style="max-width:240px;height:36px;border:1px solid var(--line);border-radius:6px;padding:0 10px;font-size:12px;" />
      </div>

      <div class="table-wrap">
        <table id="userTable">
          <thead>
            <tr>
              <th style="width:45px">ID</th>
              <th>Username</th>
              <th>Nama Lengkap</th>
              <th>Level / Role</th>
              <th>Akses Department (Many-to-Many)</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr data-search="<?= strtolower($u['username'] . ' ' . $u['nama'] . ' ' . $u['level']) ?>">
                <td><b><?= $u['id'] ?></b></td>
                <td><strong style="color:var(--navy)"><?= htmlspecialchars($u['username']) ?></strong></td>
                <td><?= htmlspecialchars($u['nama']) ?></td>
                <td>
                  <span class="badge-level <?= $u['level'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                    <?= htmlspecialchars($u['level']) ?>
                  </span>
                </td>
                <td>
                  <?php if (!empty($u['departments'])): ?>
                    <?php foreach ($u['departments'] as $d): ?>
                      <span class="dept-chip" title="<?= htmlspecialchars($d['name']) ?>">
                        <?= htmlspecialchars($d['code'] ?: $d['name']) ?>
                      </span>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <span class="dept-empty">Belum ada department</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="status <?= $u['is_active'] ? '' : 'critical' ?>">
                    <?= $u['is_active'] ? 'AKTIF' : 'NONAKTIF' ?>
                  </span>
                </td>
                <td style="text-align:right">
                  <div class="action-btn-group" style="justify-content:flex-end">
                    <!-- Tombol Action untuk Mengatur Relasi User ke Department Sesuai Instruksi -->
                    <button class="btn-dept" onclick="openDeptModal(<?= $u['id'] ?>)" title="Atur Akses Department">
                      <span>⚙</span> Atur Department
                    </button>
                    <button class="btn-edit" onclick='openEditUserModal(<?= json_encode($u) ?>)'>
                      Edit
                    </button>
                    <?php if ((int)$u['id'] !== (int)$this->session->userdata('user_id')): ?>
                      <button class="btn-delete" onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')">
                        Hapus
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- MODAL 1: ATUR AKSES DEPARTMENT (TABEL DENGAN SEARCH & SWITCH TOGGLE) -->
  <div class="modal-backdrop" id="deptModal">
    <div class="modal-box">
      <button class="close" onclick="closeDeptModal()">×</button>
      <h2 style="margin-bottom:12px">Atur Akses Department</h2>
      
      <div class="user-info-header" id="deptUserHeader">
        <div class="avatar" id="deptUserAvatar">U</div>
        <div>
          <strong id="deptUserName">-</strong>
          <small id="deptUserMeta">-</small>
        </div>
      </div>

      <!-- Fitur Search Department di dalam Modal -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:12px;flex-wrap:wrap">
        <div style="position:relative;flex:1;max-width:320px;">
          <input 
            type="text" 
            id="deptModalSearch" 
            placeholder="🔍 Cari nama atau kode department..." 
            style="width:100%;height:36px;border:1px solid var(--line);border-radius:7px;padding:0 12px;font-size:12px;background:#fff;" 
          />
        </div>
        <small id="deptModalCount" style="color:var(--muted);font-size:12px;font-weight:700;">Memuat...</small>
      </div>

      <!-- Tabel Daftar Department & Switch -->
      <div id="deptTableWrap" style="max-height:340px;overflow-y:auto;border:1px solid var(--line);border-radius:8px;">
        <table id="deptModalTable" style="width:100%;border-collapse:collapse;font-size:12px;">
          <thead>
            <tr style="background:#f8fafc;position:sticky;top:0;z-index:2;box-shadow:0 1px 0 var(--line);">
              <th style="width:40px;padding:10px;">ID</th>
              <th style="width:80px;padding:10px;">Kode</th>
              <th style="padding:10px;">Nama Department</th>
              <th style="padding:10px;">Deskripsi</th>
              <th style="width:120px;text-align:center;padding:10px;">Akses (Switch)</th>
            </tr>
          </thead>
          <tbody id="deptTableBody">
            <!-- Dimuat secara dinamis via AJAX -->
          </tbody>
        </table>
      </div>

      <div style="margin-top:18px;display:flex;justify-content:space-between;align-items:center">
        <button class="primary" onclick="closeDeptModal()">Selesai & Tutup</button>
      </div>
    </div>
  </div>


  <!-- MODAL 2: TAMBAH / EDIT USER -->
  <div class="modal-backdrop" id="userFormModal">
    <div class="modal-box">
      <button class="close" onclick="closeUserFormModal()">×</button>
      <p class="eyebrow" id="formModalEyebrow">PENGGUNA BARU</p>
      <h2 style="margin-bottom:16px" id="formModalTitle">Tambah User</h2>

      <form id="userForm" onsubmit="saveUser(event)">
        <input type="hidden" name="id" id="userId" />

        <div class="form-row">
          <label for="formUsername">Username</label>
          <input type="text" name="username" id="formUsername" required placeholder="Contoh: risk_lead" />
        </div>

        <div class="form-row">
          <label for="formNama">Nama Lengkap</label>
          <input type="text" name="nama" id="formNama" required placeholder="Contoh: John Doe" />
        </div>

        <div class="form-row">
          <label for="formPassword">Password</label>
          <input type="password" name="password" id="formPassword" placeholder="Masukkan password (kosongkan jika tidak diubah)" />
          <small id="pwdHelp" style="color:var(--muted);font-size:10px;display:none">Kosongkan jika tidak ingin mengubah password.</small>
        </div>

        <div class="form-row">
          <label for="formLevel">Level / Role</label>
          <select name="level" id="formLevel" required>
            <option value="user">User (Risk Officer)</option>
            <option value="admin">Admin (Administrator)</option>
          </select>
        </div>

        <div class="form-row">
          <label for="formStatus">Status Akun</label>
          <select name="is_active" id="formStatus">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>

        <div style="margin-top:22px;display:flex;gap:10px;justify-content:flex-end;">
          <button type="button" class="ghost" onclick="closeUserFormModal()">Batal</button>
          <button type="submit" class="primary" id="saveUserBtn">Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let activeUserIdForDept = null;

    // Search filter tabel
    document.getElementById('userSearch').oninput = e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#userTable tbody tr').forEach(tr => {
        tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
      });
    };

    // Modal Tambah User
    function openAddUserModal() {
      document.getElementById('userId').value = '';
      document.getElementById('formUsername').value = '';
      document.getElementById('formNama').value = '';
      document.getElementById('formPassword').value = '';
      document.getElementById('formPassword').required = true;
      document.getElementById('pwdHelp').style.display = 'none';
      document.getElementById('formLevel').value = 'user';
      document.getElementById('formStatus').value = '1';
      document.getElementById('formModalEyebrow').textContent = 'PENGGUNA BARU';
      document.getElementById('formModalTitle').textContent = 'Tambah User Baru';
      document.getElementById('userFormModal').classList.add('show');
    }

    // Modal Edit User
    function openEditUserModal(u) {
      document.getElementById('userId').value = u.id;
      document.getElementById('formUsername').value = u.username;
      document.getElementById('formNama').value = u.nama;
      document.getElementById('formPassword').value = '';
      document.getElementById('formPassword').required = false;
      document.getElementById('pwdHelp').style.display = 'block';
      document.getElementById('formLevel').value = u.level;
      document.getElementById('formStatus').value = u.is_active;
      document.getElementById('formModalEyebrow').textContent = 'EDIT PENGGUNA';
      document.getElementById('formModalTitle').textContent = 'Edit Data User';
      document.getElementById('userFormModal').classList.add('show');
    }

    function closeUserFormModal() {
      document.getElementById('userFormModal').classList.remove('show');
    }

    // Simpan User (Tambah / Edit)
    async function saveUser(e) {
      e.preventDefault();
      const form = document.getElementById('userForm');
      const formData = new FormData(form);

      try {
        const res = await fetch('<?= site_url("settings/user_save") ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.status === 'success') {
          showToast(data.message);
          closeUserFormModal();
          setTimeout(() => location.reload(), 800);
        } else {
          alert(data.message);
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    // Hapus User
    async function deleteUser(id, username) {
      if (!confirm(`Apakah Anda yakin ingin menghapus user "${username}"?`)) return;

      try {
        const res = await fetch(`<?= site_url("settings/user_delete/") ?>${id}`, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.status === 'success') {
          showToast(data.message);
          setTimeout(() => location.reload(), 800);
        } else {
          alert(data.message);
        }
      } catch (err) {
        alert('Gagal menghapus user.');
      }
    }

    // =========================================================================
    // FITUR UTAMA: ATUR AKSES DEPARTMENT (TABEL DENGAN LIVE SEARCH & SWITCH)
    // =========================================================================
    let currentDeptList = [];
    let currentDeptUser = null;

    function renderDeptTable(deptList) {
      const tbody = document.getElementById('deptTableBody');
      if (!tbody) return;

      if (!deptList || deptList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" style="text-align:center;padding:26px;color:var(--muted)">
              Tidak ada department yang cocok dengan pencarian.
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = deptList.map(d => `
        <tr data-id="${d.id}">
          <td style="padding:11px 10px;color:var(--muted);font-weight:700;">${d.id}</td>
          <td style="padding:11px 10px;"><span class="tag" style="font-weight:800">${d.code || '-'}</span></td>
          <td style="padding:11px 10px;">
            <strong style="color:var(--navy);font-size:13px;display:block;">${d.name}</strong>
          </td>
          <td style="padding:11px 10px;color:var(--muted);font-size:11px;max-width:240px;">
            ${d.description || '-'}
          </td>
          <td style="padding:11px 10px;text-align:center;">
            <div style="display:inline-flex;flex-direction:column;align-items:center;gap:3px;">
              <label class="switch">
                <input 
                  type="checkbox" 
                  ${d.is_assigned ? 'checked' : ''} 
                  onchange="toggleDept(${currentDeptUser.id}, ${d.id}, this)" 
                />
                <span class="slider"></span>
              </label>
              <span class="switch-badge" style="font-size:9px;font-weight:800;letter-spacing:.04em;color:${d.is_assigned ? 'var(--green)' : '#8a9ba8'}">
                ${d.is_assigned ? 'AKTIF' : 'NONAKTIF'}
              </span>
            </div>
          </td>
        </tr>
      `).join('');
    }

    async function openDeptModal(userId) {
      activeUserIdForDept = userId;
      const tbody = document.getElementById('deptTableBody');
      tbody.innerHTML = '<tr><td colspan="5" style="padding:26px;text-align:center;color:var(--muted)">Memuat data department...</td></tr>';
      document.getElementById('deptModalSearch').value = '';
      document.getElementById('deptModalCount').textContent = 'Memuat...';
      document.getElementById('deptModal').classList.add('show');

      try {
        const res = await fetch(`<?= site_url("settings/user_departments/") ?>${userId}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const resData = await res.json();
        if (resData.status === 'success') {
          currentDeptUser = resData.user;
          currentDeptList = resData.departments || [];

          document.getElementById('deptUserName').textContent = currentDeptUser.nama;
          document.getElementById('deptUserMeta').textContent = `@${currentDeptUser.username} · Role: ${currentDeptUser.level.toUpperCase()}`;
          document.getElementById('deptUserAvatar').textContent = currentDeptUser.nama.substring(0, 2).toUpperCase();

          document.getElementById('deptModalCount').textContent = `Total ${currentDeptList.length} department`;
          renderDeptTable(currentDeptList);
        } else {
          tbody.innerHTML = `<tr><td colspan="5" style="color:var(--red);padding:20px;text-align:center">${resData.message}</td></tr>`;
        }
      } catch (err) {
        tbody.innerHTML = '<tr><td colspan="5" style="color:var(--red);padding:20px;text-align:center">Gagal memuat relasi department.</td></tr>';
      }
    }

    // Live search filter department di dalam modal
    document.getElementById('deptModalSearch').oninput = e => {
      const q = e.target.value.toLowerCase().trim();
      const filtered = currentDeptList.filter(d => 
        (d.name + ' ' + (d.code || '') + ' ' + (d.description || '')).toLowerCase().includes(q)
      );
      renderDeptTable(filtered);
      document.getElementById('deptModalCount').textContent = `${filtered.length} dari ${currentDeptList.length} department`;
    };

    async function toggleDept(userId, deptId, inputEl) {
      const enabled = inputEl.checked;
      const labelEl = inputEl.closest('td').querySelector('.switch-badge');

      // Feedback visual instan
      if (labelEl) {
        labelEl.textContent = enabled ? 'AKTIF' : 'NONAKTIF';
        labelEl.style.color = enabled ? 'var(--green)' : '#8a9ba8';
      }

      // Update state di memory
      const item = currentDeptList.find(x => x.id == deptId);
      if (item) item.is_assigned = enabled;

      const formData = new FormData();
      formData.append('user_id', userId);
      formData.append('department_id', deptId);
      formData.append('enabled', enabled ? 1 : 0);

      try {
        const res = await fetch('<?= site_url("settings/toggle_user_department") ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.status === 'success') {
          showToast(data.message);
        } else {
          alert(data.message);
          // Revert jika gagal
          inputEl.checked = !enabled;
          if (labelEl) {
            labelEl.textContent = !enabled ? 'AKTIF' : 'NONAKTIF';
            labelEl.style.color = !enabled ? 'var(--green)' : '#8a9ba8';
          }
          if (item) item.is_assigned = !enabled;
        }
      } catch (err) {
        alert('Gagal memperbarui status relasi department.');
        inputEl.checked = !enabled;
        if (labelEl) {
          labelEl.textContent = !enabled ? 'AKTIF' : 'NONAKTIF';
          labelEl.style.color = !enabled ? 'var(--green)' : '#8a9ba8';
        }
      }
    }

    function closeDeptModal() {
      document.getElementById('deptModal').classList.remove('show');
      location.reload(); // Refresh untuk memperbarui daftar tag department di tabel utama
    }

  </script>
<?php $this->load->view('templates/footer'); ?>
