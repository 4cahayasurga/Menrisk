<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Department Management — Settings']);
?>
  <!-- Sidebar Navigation Template -->
  <?php $this->load->view('templates/sidebar', ['active_menu' => 'department']); ?>

  <!-- Main Content Area -->
  <main>
    <header>
      <button class="menu" aria-label="Buka menu">☰</button>
      <div>
        <p class="eyebrow">SETTINGS · ADMINISTRATOR ACCESS ONLY</p>
        <h1>Department Management</h1>
        <p class="subtitle">Kelola struktur divisi dan master data department perusahaan</p>
      </div>
      <div class="header-actions">
        <button class="primary" onclick="openAddDeptModal()">+ Tambah Department</button>
        <div class="avatar" title="<?= htmlspecialchars($current_user['nama']) ?>">
          <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
        </div>
      </div>
    </header>

    <!-- Department Table Panel -->
    <section class="panel full">
      <div class="panel-head">
        <div>
          <h2>Daftar Department Perusahaan</h2>
          <p>Total <?= count($departments) ?> unit kerja terdaftar</p>
        </div>
        <input id="deptSearch" placeholder="Cari nama atau kode department..." style="max-width:240px;height:36px;border:1px solid var(--line);border-radius:6px;padding:0 10px;font-size:12px;" />
      </div>

      <div class="table-wrap">
        <table id="deptTable">
          <thead>
            <tr>
              <th style="width:45px">ID</th>
              <th style="width:100px">Kode</th>
              <th>Nama Department</th>
              <th>Deskripsi</th>
              <th>Total User Terkait</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($departments as $d): ?>
              <tr data-search="<?= strtolower($d['name'] . ' ' . $d['code'] . ' ' . $d['description']) ?>">
                <td><b><?= $d['id'] ?></b></td>
                <td><span class="tag" style="font-weight:800"><?= htmlspecialchars($d['code'] ?: '-') ?></span></td>
                <td><strong style="color:var(--navy)"><?= htmlspecialchars($d['name']) ?></strong></td>
                <td style="color:var(--muted);font-size:12px"><?= htmlspecialchars($d['description'] ?: '-') ?></td>
                <td>
                  <span class="pill" style="background:#eef4fc;color:var(--blue)">
                    <?= (int)$d['total_users'] ?> Pengguna
                  </span>
                </td>
                <td style="text-align:right">
                  <div class="action-btn-group" style="justify-content:flex-end">
                    <button class="btn-edit" onclick='openEditDeptModal(<?= json_encode($d) ?>)'>
                      Edit
                    </button>
                    <button class="btn-delete" onclick="deleteDept(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['name'])) ?>')">
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- MODAL: TAMBAH / EDIT DEPARTMENT -->
  <div class="modal-backdrop" id="deptFormModal">
    <div class="modal-box">
      <button class="close" onclick="closeDeptFormModal()">×</button>
      <p class="eyebrow" id="formDeptEyebrow">DATA MASTER</p>
      <h2 style="margin-bottom:16px" id="formDeptTitle">Tambah Department</h2>

      <form id="deptForm" onsubmit="saveDept(event)">
        <input type="hidden" name="id" id="deptId" />

        <div class="form-row">
          <label for="formDeptName">Nama Department</label>
          <input type="text" name="name" id="formDeptName" required placeholder="Contoh: Teknologi Informasi" />
        </div>

        <div class="form-row">
          <label for="formDeptCode">Kode Singkatan</label>
          <input type="text" name="code" id="formDeptCode" placeholder="Contoh: IT" style="text-transform:uppercase" />
        </div>

        <div class="form-row">
          <label for="formDeptDesc">Deskripsi Unit Kerja</label>
          <textarea name="description" id="formDeptDesc" placeholder="Penjelasan ruang lingkup divisi..."></textarea>
        </div>

        <div style="margin-top:22px;display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="ghost" onclick="closeDeptFormModal()">Batal</button>
          <button type="submit" class="primary" id="saveDeptBtn">Simpan Department</button>
        </div>
      </form>
    </div>
  </div>

  <script>

    // Search filter tabel
    document.getElementById('deptSearch').oninput = e => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#deptTable tbody tr').forEach(tr => {
        tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
      });
    };

    function openAddDeptModal() {
      document.getElementById('deptId').value = '';
      document.getElementById('formDeptName').value = '';
      document.getElementById('formDeptCode').value = '';
      document.getElementById('formDeptDesc').value = '';
      document.getElementById('formDeptEyebrow').textContent = 'DATA MASTER';
      document.getElementById('formDeptTitle').textContent = 'Tambah Department Baru';
      document.getElementById('deptFormModal').classList.add('show');
    }

    function openEditDeptModal(d) {
      document.getElementById('deptId').value = d.id;
      document.getElementById('formDeptName').value = d.name;
      document.getElementById('formDeptCode').value = d.code || '';
      document.getElementById('formDeptDesc').value = d.description || '';
      document.getElementById('formDeptEyebrow').textContent = 'EDIT MASTER';
      document.getElementById('formDeptTitle').textContent = 'Edit Data Department';
      document.getElementById('deptFormModal').classList.add('show');
    }

    function closeDeptFormModal() {
      document.getElementById('deptFormModal').classList.remove('show');
    }

    async function saveDept(e) {
      e.preventDefault();
      const form = document.getElementById('deptForm');
      const formData = new FormData(form);

      try {
        const res = await fetch('<?= site_url("settings/department_save") ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.status === 'success') {
          showToast(data.message);
          closeDeptFormModal();
          setTimeout(() => location.reload(), 800);
        } else {
          alert(data.message);
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    async function deleteDept(id, name) {
      if (!confirm(`Apakah Anda yakin ingin menghapus department "${name}"? Relasi user terkait juga akan disesuaikan.`)) return;

      try {
        const res = await fetch(`<?= site_url("settings/department_delete/") ?>${id}`, {
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
        alert('Gagal menghapus department.');
      }
    }
  </script>
<?php $this->load->view('templates/footer'); ?>
