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
  min-width: 1200px;
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
.cell-clamp {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: break-word;
  line-height: 1.45;
  max-height: 2.9em;
}
.cell-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: break-word;
  line-height: 1.45;
  max-height: 4.35em;
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
.btn-act-monitoring {
  background: #fdf4ff;
  color: #a21caf;
  border-color: #f0abfc;
}
.btn-act-monitoring:hover {
  background: #fae8ff;
}
.badge-score-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
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
            <th style="width: 220px; text-align: center;">Aksi</th>
            <th style="width: 220px; text-align: center;">Department</th>
            <th style="min-width: 190px;">Sasaran / Target</th>
            <th style="min-width: 180px;">Nama Risiko</th>
            <th style="min-width: 160px;">Sumber &amp; Faktor Risiko</th>
            <th style="min-width: 130px;">Efektifitas Kontrol</th>
            <th style="min-width: 130px;">Periode Terpapar</th>
            <th style="min-width: 130px; text-align: center;">Skala Inheren</th>
            <th style="min-width: 130px; text-align: center;">Skala Residual</th>
            <th style="min-width: 140px; text-align: center;">Skala Current</th>
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
                <td style="text-align: center;">
                  <div class="action-btn-group">
                    <a href="<?= site_url('menrisk/monitoring_profil_risiko/' . (int)$row->id) ?>" class="btn-act btn-act-monitoring" title="Progress Monitoring Profile Risiko">
                      <span>📊</span>
                    </a>
                    <a href="<?= site_url('menrisk/profil_risiko/' . (int)$row->id . '?mode=view') ?>" class="btn-act btn-act-view" title="Lihat Detail Formulir">
                      <span>👁</span>
                    </a>
                    <a href="<?= site_url('menrisk/profil_risiko/' . (int)$row->id) ?>" class="btn-act btn-act-edit" title="Edit Formulir">
                      <span>✎</span>
                    </a>
                  </div>
                </td>
                <td>
                  <strong><?= htmlspecialchars($row->department_name ?? '-') ?></strong>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->sasaran ?? '') ?>">
                    <strong><?= htmlspecialchars($row->sasaran ?? '-') ?></strong>
                  </div>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->nama_risiko ?? '') ?>">
                    <strong style="color: var(--navy, #0f172a);"><?= htmlspecialchars($row->nama_risiko ?? '-') ?></strong>
                  </div>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->sumber_risiko_nama ?? '') ?>" style="font-weight: 600; color: #0f172a;">
                    <?= htmlspecialchars($row->sumber_risiko_nama ?: '-') ?>
                  </div>
                  <?php if (!empty($row->faktor_sumber_nama)): ?>
                    <div class="cell-clamp" title="Faktor: <?= htmlspecialchars($row->faktor_sumber_nama) ?>" style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                      Faktor: <?= htmlspecialchars($row->faktor_sumber_nama) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($row->efektifitas_kontrol)): ?>
                    <div class="badge-efektif <?= $efClass ?>"><?= htmlspecialchars($row->efektifitas_kontrol) ?></div>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->periode_terpapar ?? '') ?>">
                    <?= htmlspecialchars($row->periode_terpapar ?: '-') ?>
                  </div>
                </td>
                <?php
                  // Helper level warna 1-25
                  $getLevelStyle = function($score) {
                      $s = (int)$score;
                      if ($s <= 0) return ['bg' => '#f1f5f9', 'text' => '#64748b', 'label' => '-'];
                      if ($s >= 21) return ['bg' => '#ff0000', 'text' => '#ffffff', 'label' => 'Hight'];
                      if ($s >= 16) return ['bg' => '#ffbf00', 'text' => '#0f172a', 'label' => 'Mod. to High'];
                      if ($s >= 12) return ['bg' => '#ffff00', 'text' => '#0f172a', 'label' => 'Moderate'];
                      if ($s >= 6)  return ['bg' => '#92d14f', 'text' => '#0f172a', 'label' => 'Low to Mod.'];
                      return ['bg' => '#00b04f', 'text' => '#ffffff', 'label' => 'Low'];
                  };
                  $inherenStyle = $getLevelStyle($row->skala_risiko_inheren);
                  $residualStyle = $getLevelStyle($row->skala_risiko_residual);
                  $currentStyle = $getLevelStyle($row->skala_risiko_current);
                ?>
                <td style="text-align: center;">
                  <?php if (!empty($row->skala_risiko_inheren)): ?>
                    <span class="badge-score-pill" style="background: <?= $inherenStyle['bg'] ?>; color: <?= $inherenStyle['text'] ?>;" title="Skala Risiko Inheren: <?= (int)$row->skala_risiko_inheren ?> (<?= $inherenStyle['label'] ?>)">
                      <span><?= (int)$row->skala_risiko_inheren ?></span>
                      <small style="opacity: 0.9; font-size: 10px;">· <?= $inherenStyle['label'] ?></small>
                    </span>
                  <?php else: ?>
                    <span style="color: #94a3b8;">-</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: center;">
                  <?php if (!empty($row->skala_risiko_residual)): ?>
                    <span class="badge-score-pill" style="background: <?= $residualStyle['bg'] ?>; color: <?= $residualStyle['text'] ?>;" title="Skala Risiko Residual (Target): <?= (int)$row->skala_risiko_residual ?> (<?= $residualStyle['label'] ?>)">
                      <span><?= (int)$row->skala_risiko_residual ?></span>
                      <small style="opacity: 0.9; font-size: 10px;">· <?= $residualStyle['label'] ?></small>
                    </span>
                  <?php else: ?>
                    <span style="color: #94a3b8;">-</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: center;">
                  <?php if (!empty($row->skala_risiko_current)): ?>
                    <span class="badge-score-pill" style="background: <?= $currentStyle['bg'] ?>; color: <?= $currentStyle['text'] ?>;" title="Skala Risiko Current (Terbaru dari Progress Monitoring): <?= (int)$row->skala_risiko_current ?> (<?= $currentStyle['label'] ?>) - <?= htmlspecialchars($row->periode_current ?? '') ?> <?= htmlspecialchars($row->tahun_current ?? '') ?>">
                      <span><?= (int)$row->skala_risiko_current ?></span>
                      <small style="opacity: 0.9; font-size: 10px;">· <?= $currentStyle['label'] ?></small>
                    </span>
                    <?php if (!empty($row->periode_current)): ?>
                      <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">
                        <?= htmlspecialchars($row->periode_current) ?> <?= htmlspecialchars($row->tahun_current ?? '') ?>
                      </div>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Belum ada progres</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyProfilRow">
              <td colspan="11" style="text-align: center; padding: 36px 16px; color: #64748b;">
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

