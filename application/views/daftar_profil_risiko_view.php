<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Daftar Profile Risiko — IFPRO Risk Command Center']);
?>
<!-- Sidebar Navigation Template -->
<?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'daftar_profil_risiko', 'is_dashboard' => false]); ?>

<style>
.list-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 20px 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.list-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}
.search-input-box {
  min-width: 280px;
  padding: 9px 14px;
  font-size: 13px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  outline: none;
}
.search-input-box:focus {
  border-color: var(--teal, #00a89d);
  box-shadow: 0 0 0 3px rgba(0, 168, 157, 0.15);
}
.table-scroll-wrap {
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}
.profil-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
  min-width: 1550px;
}
.profil-table thead th {
  background: #f8fafc;
  color: #334155;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 0.4px;
  padding: 12px 14px;
  border-bottom: 2px solid #e2e8f0;
  text-align: left;
  white-space: nowrap;
}
.profil-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
  color: #1e293b;
  line-height: 1.45;
}
.profil-table tbody tr:hover {
  background: #f8fafc;
}
.badge-efektif {
  display: inline-block;
  padding: 3px 9px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-efektif.efektif {
  background: #dcfce7;
  color: #166534;
}
.badge-efektif.kurang {
  background: #fef9c3;
  color: #854d0e;
}
.badge-efektif.tidak {
  background: #fee2e2;
  color: #991b1b;
}
.action-btn-group {
  display: inline-flex;
  gap: 6px;
  align-items: center;
}
.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 6px 11px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all .15s ease;
  white-space: nowrap;
}
.btn-act-view {
  background: #f0fdfa;
  color: #0f766e;
  border-color: #99f6e4;
}
.btn-act-view:hover {
  background: #ccfbf1;
}
.btn-act-edit {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}
.btn-act-edit:hover {
  background: #dbeafe;
}
</style>

<main>
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">MANAJEMEN RISIKO · DAFTAR PROFILE RISIKO</p>
      <h1>Daftar Profile Risiko</h1>
      <p class="subtitle">Daftar hasil input Formulir Profile Risiko (Sasaran/Target, Fungsi Kerja &amp; Identifikasi Risiko).</p>
    </div>
    <div class="header-actions">
      <a href="<?= site_url('menrisk/profil_risiko') ?>" class="primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; font-size: 13px; font-weight: 600;">
        <span>+</span> Buat Profile Risiko Baru
      </a>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <div class="list-card">
    <div class="list-toolbar">
      <div>
        <strong style="font-size: 15px; color: var(--navy, #0f172a);">Tabel Hasil Input Formulir Profile Risiko</strong>
        <p style="font-size: 12px; color: var(--muted, #64748b); margin: 2px 0 0 0;">
          Menampilkan informasi Sasaran/Target, Fungsi Kerja, serta data Identifikasi Risiko (Tab 1).
        </p>
      </div>
      <div style="display: flex; align-items: center; gap: 10px;">
        <input type="text" id="searchProfilInput" class="search-input-box" placeholder="Cari sasaran, fungsi kerja, nama risiko..." />
      </div>
    </div>

    <div class="table-scroll-wrap">
      <table class="profil-table" id="tableDaftarProfil">
        <thead>
          <tr>
            <th style="width: 44px; text-align: center;">No</th>
            <th style="min-width: 190px;">Sasaran / Target</th>
            <th style="min-width: 150px;">Fungsi Kerja</th>
            <th style="min-width: 180px;">Nama Risiko</th>
            <th style="min-width: 150px;">Kategori Risiko</th>
            <th style="min-width: 200px;">Peristiwa Risiko</th>
            <th style="min-width: 200px;">Penyebab Risiko</th>
            <th style="min-width: 190px;">Dampak Kualitatif</th>
            <th style="min-width: 160px;">Sumber &amp; Faktor Risiko</th>
            <th style="min-width: 180px;">Kontrol Saat Ini</th>
            <th style="min-width: 130px;">Efektifitas Kontrol</th>
            <th style="min-width: 130px;">Periode Terpapar</th>
            <th style="width: 140px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($formulir_list)): ?>
            <?php foreach ($formulir_list as $idx => $row): ?>
              <?php
                $efVal = strtolower((string)($row->efektifitas_kontrol ?? ''));
                $efClass = 'kurang';
                if (strpos($efVal, 'tidak') !== false) {
                    $efClass = 'tidak';
                } elseif (strpos($efVal, 'kurang') !== false) {
                    $efClass = 'kurang';
                } elseif (strpos($efVal, 'efektif') !== false) {
                    $efClass = 'efektif';
                }
              ?>
              <tr class="profil-row">
                <td style="text-align: center; font-weight: 700; color: #64748b;"><?= $idx + 1 ?></td>
                <td><strong><?= htmlspecialchars($row->sasaran ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($row->fungsi_kerja ?? '-') ?></td>
                <td><strong style="color: var(--navy, #0f172a);"><?= htmlspecialchars($row->nama_risiko ?? '-') ?></strong></td>
                <td>
                  <?php if (!empty($row->kategori_risiko)): ?>
                    <span style="display:inline-block; background:#f1f5f9; color:#0f172a; font-weight:700; padding:2px 7px; border-radius:4px; font-size:11px; margin-bottom:3px;">
                      <?= htmlspecialchars($row->kategori_risiko) ?>
                    </span>
                    <div style="font-size:12px; color:#475569;"><?= htmlspecialchars($row->kategori_risiko_nama ?? '') ?></div>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td><?= nl2br(htmlspecialchars($row->peristiwa_risiko ?: '-')) ?></td>
                <td><?= nl2br(htmlspecialchars($row->penyebab_risiko ?: '-')) ?></td>
                <td><?= nl2br(htmlspecialchars($row->dampak_kualitatif ?: '-')) ?></td>
                <td>
                  <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($row->sumber_risiko_nama ?: '-') ?></div>
                  <?php if (!empty($row->faktor_sumber_nama)): ?>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">Faktor: <?= htmlspecialchars($row->faktor_sumber_nama) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= nl2br(htmlspecialchars($row->kontrol_saat_ini ?: '-')) ?></td>
                <td>
                  <?php if (!empty($row->efektifitas_kontrol)): ?>
                    <span class="badge-efektif <?= $efClass ?>"><?= htmlspecialchars($row->efektifitas_kontrol) ?></span>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row->periode_terpapar ?: '-') ?></td>
                <td style="text-align: center;">
                  <div class="action-btn-group">
                    <a href="<?= site_url('menrisk/profil_risiko/' . (int)$row->id . '?mode=view') ?>" class="btn-act btn-act-view" title="Lihat Detail Formulir">
                      <span>👁</span> Lihat
                    </a>
                    <a href="<?= site_url('menrisk/profil_risiko/' . (int)$row->id) ?>" class="btn-act btn-act-edit" title="Edit Formulir">
                      <span>✎</span> Edit
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyProfilRow">
              <td colspan="13" style="text-align: center; padding: 36px 16px; color: #64748b;">
                Belum ada data Formulir Profile Risiko yang disimpan. Klik tombol <b>+ Buat Profile Risiko Baru</b> di kanan atas untuk menambahkan data.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
  $(function() {
    $('#searchProfilInput').on('input', function() {
      const q = $.trim($(this).val()).toLowerCase();
      $('#tableDaftarProfil tbody tr.profil-row').each(function() {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(q) !== -1);
      });
    });
  });
</script>

<?php $this->load->view('templates/footer'); ?>

