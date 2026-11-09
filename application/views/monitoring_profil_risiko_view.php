<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Progress Monitoring Profile Risiko — IFPRO Risk Command Center']);
?>
<!-- Sidebar Navigation Template -->
<?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'daftar_profil_risiko', 'is_dashboard' => false]); ?>

<style>
/* ==========================================================================
   PROGRESS MONITORING PROFILE RISIKO STYLES
   ========================================================================== */
.mon-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 22px 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mon-card-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--navy, #0f172a);
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.mon-card-desc {
  font-size: 12.5px;
  color: var(--muted, #64748b);
  margin-bottom: 16px;
}

/* Info Summary Box Profile Risiko */
.profile-meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 8px;
}
.profile-meta-item small {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-bottom: 3px;
}
.profile-meta-item strong {
  font-size: 13.5px;
  color: #0f172a;
  line-height: 1.4;
}

/* Table Riwayat Monitoring */
.table-scroll-wrap {
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin-top: 14px;
}
.mon-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
  min-width: 1350px;
}
.mon-table thead th {
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
.mon-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
  color: #1e293b;
  line-height: 1.45;
}
.mon-table tbody tr:hover {
  background: #f8fafc;
}

/* Form Styles */
.form-grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 18px;
}
.form-grid-full {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.profile-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.profile-field-group label {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
}
.profile-field-group .helper-text {
  font-size: 11.5px;
  color: var(--muted, #64748b);
  line-height: 1.4;
}
.profile-field-group input,
.profile-field-group select,
.profile-field-group textarea {
  width: 100%;
  padding: 10px 14px;
  font-size: 13px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #ffffff;
  color: #0f172a;
  outline: none;
  transition: border-color .15s ease, box-shadow .15s ease;
  font-family: inherit;
}
.profile-field-group input {
  padding: 9.5px 14px;
}
.profile-field-group input:focus,
.profile-field-group select:focus,
.profile-field-group textarea:focus {
  border-color: var(--teal, #00a89d);
  box-shadow: 0 0 0 3px rgba(0, 168, 157, 0.15);
}
.profile-field-group textarea {
  resize: vertical;
  min-height: 68px;
}

/* Radio Scale Cards */
.radio-scale-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 10px;
}
.scale-card-option {
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 12px 14px;
  cursor: pointer;
  transition: all .15s ease;
  margin: 0;
}
.scale-card-option:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}
.scale-card-option input[type="radio"] {
  width: auto;
  margin-top: 3px;
  accent-color: var(--teal, #00a89d);
  cursor: pointer;
}
.scale-card-option.selected {
  border-color: var(--teal, #00a89d);
  background: #f0fdfa;
  box-shadow: 0 0 0 1px var(--teal, #00a89d);
}

/* Badge Skala 1 - 5 */
.badge-skala {
  display: inline-block;
  min-width: 24px;
  height: 24px;
  line-height: 22px;
  text-align: center;
  border-radius: 50%;
  font-size: 11.5px;
  font-weight: 800;
  border: 1px solid transparent;
}
.badge-skala-1 { background: #e6f4ea; color: #137333; border-color: #ceead6; }
.badge-skala-2 { background: #e8f0fe; color: #1a73e8; border-color: #d2e3fc; }
.badge-skala-3 { background: #fef7e0; color: #b06000; border-color: #feefc3; }
.badge-skala-4 { background: #feefe3; color: #d93025; border-color: #fad2cf; }
.badge-skala-5 { background: #fce8e6; color: #c5221f; border-color: #f6aea9; }

/* Badge Level Score */
.badge-level-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}

/* Action buttons */
.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all .15s ease;
  white-space: nowrap;
}
.btn-act-edit {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}
.btn-act-delete {
  background: #fef2f2;
  color: #b91c1c;
  border-color: #fecaca;
}
</style>

<main>
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">RISK REGISTER · PROGRESS MONITORING</p>
      <h1>Progress Monitoring Profile Risiko</h1>
      <p class="subtitle">Pencatatan dan pemantauan realisasi berkala progres mitigasi serta profil risiko current.</p>
    </div>
    <div class="header-actions">
      <a href="<?= site_url('menrisk/daftar_profil_risiko') ?>" class="ghost" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
        <span>←</span> Kembali ke Daftar Profile Risiko
      </a>
      <a href="#sectionFormMonitoring" class="primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;" onclick="resetMonitoringForm()">
        <span>+</span> Isi Progress Monitoring Baru
      </a>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <!-- CARD 1: INFORMASI PROFIL RISIKO ACUAN -->
  <div class="mon-card">
    <div class="mon-card-title">
      <span>🎯</span> Profil Risiko yang Dimonitor
    </div>
    <p class="mon-card-desc">Informasi profil risiko dasar yang menjadi acuan pengisian progress monitoring berkala.</p>

    <div class="profile-meta-grid">
      <div class="profile-meta-item">
        <small>Sasaran / Target</small>
        <strong><?= htmlspecialchars($formulir->sasaran ?? '-') ?></strong>
      </div>
      <div class="profile-meta-item">
        <small>Fungsi Kerja</small>
        <strong><?= htmlspecialchars($formulir->fungsi_kerja ?? '-') ?></strong>
      </div>
      <div class="profile-meta-item">
        <small>Nama Risiko</small>
        <strong style="color: var(--teal, #00a89d);"><?= htmlspecialchars($formulir->nama_risiko ?? '-') ?></strong>
      </div>
      <div class="profile-meta-item">
        <small>Kategori Risiko</small>
        <strong>
          <?= htmlspecialchars($formulir->kategori_risiko ? $formulir->kategori_risiko . ' - ' . ($formulir->kategori_risiko_nama ?? '') : '-') ?>
        </strong>
      </div>
      <div class="profile-meta-item">
        <small>Eksposure Inheren (Tab 1/Tab 2)</small>
        <strong>Rp <?= htmlspecialchars($formulir->eksposure_kuantitatif ?: '0') ?></strong>
      </div>
      <div class="profile-meta-item">
        <small>Kategori Dampak Kualitatif (Tab 2)</small>
        <strong><?= htmlspecialchars($formulir->kategori_dampak_judul ? ($formulir->kategori_dampak_risiko ?: $formulir->kategori_dampak_judul) : 'Belum Dipilih') ?></strong>
      </div>
      <div class="profile-meta-item">
        <small>Skor Risiko Inheren</small>
        <strong>Nilai <?= htmlspecialchars($formulir->skala_risiko_inheren ?: '1') ?> (Inheren)</strong>
      </div>
      <div class="profile-meta-item">
        <small>Skor Risiko Residual (Target)</small>
        <strong>Nilai <?= htmlspecialchars($formulir->skala_risiko_residual ?: '1') ?> (Residual)</strong>
      </div>
    </div>
  </div>

  <!-- CARD 2: TABEL RIWAYAT PROGRESS MONITORING -->
  <div class="mon-card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 6px;">
      <div>
        <div class="mon-card-title">
          <span>📋</span> Riwayat Progress Monitoring Profile Risiko
        </div>
        <p class="mon-card-desc" style="margin-bottom: 0;">Catatan progres berkala per 3 bulan (Triwulan) / per bulan yang telah diinputkan untuk profil risiko ini.</p>
      </div>
    </div>

    <div class="table-scroll-wrap">
      <table class="mon-table" id="tableRiwayatMonitoring">
        <thead>
          <tr>
            <th style="width: 44px; text-align: center;">No</th>
            <th style="min-width: 110px;">Periode</th>
            <th style="min-width: 220px;">Status Progres Tindak Lanjut</th>
            <th style="min-width: 140px;">Realisasi Biaya</th>
            <th style="min-width: 120px;">Proses (%)</th>
            <th style="min-width: 120px;">% per Risiko</th>
            <th style="min-width: 150px;">Eksposur Current (Rp)</th>
            <th style="min-width: 130px;">Skala Dampak</th>
            <th style="min-width: 140px;">Skala Probabilitas</th>
            <th style="min-width: 130px;">Skala Risiko Realisasi</th>
            <th style="min-width: 180px;">Kendala</th>
            <th style="min-width: 180px;">Action Plan</th>
            <th style="width: 130px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($monitoring_list)): ?>
            <?php foreach ($monitoring_list as $mIdx => $m): ?>
              <?php
                $mScore = (int)($m->skala_risiko_realisasi ?? 1);
                $bgLvl = '#00b04f';
                $lblLvl = 'Low';
                $txtLvl = '#fff';
                if ($mScore >= 21) {
                    $bgLvl = '#ff0000'; $lblLvl = 'Hight'; $txtLvl = '#fff';
                } elseif ($mScore >= 16) {
                    $bgLvl = '#ffbf00'; $lblLvl = 'Moderate to Hight'; $txtLvl = '#0f172a';
                } elseif ($mScore >= 12) {
                    $bgLvl = '#ffff00'; $lblLvl = 'Moderate'; $txtLvl = '#0f172a';
                } elseif ($mScore >= 6) {
                    $bgLvl = '#92d14f'; $lblLvl = 'Low to Moderate'; $txtLvl = '#0f172a';
                }
              ?>
              <tr id="row-mon-<?= $m->id ?>">
                <td style="text-align: center; font-weight: 700; color: #64748b;"><?= $mIdx + 1 ?></td>
                <td>
                  <span style="font-weight: 700; color: var(--navy);"><?= htmlspecialchars($m->periode) ?> <?= htmlspecialchars($m->tahun) ?></span>
                  <div style="font-size: 11px; color: var(--muted); text-transform: capitalize;"><?= htmlspecialchars($m->periode_tipe) ?></div>
                </td>
                <td><?= nl2br(htmlspecialchars($m->status_progres_tindak_lanjut ?: '-')) ?></td>
                <td><?= nl2br(htmlspecialchars($m->realisasi_biaya ?: '-')) ?></td>
                <td><?= nl2br(htmlspecialchars($m->proses_tindak_lanjut_persen ?: '-')) ?></td>
                <td><?= htmlspecialchars($m->persentase_per_risiko ?: '-') ?></td>
                <td><strong><?= htmlspecialchars($m->eksposur_kuantitatif_current ? 'Rp ' . $m->eksposur_kuantitatif_current : '-') ?></strong></td>
                <td>
                  <?php if (!empty($m->skala_dampak_realisasi_current)): ?>
                    <span class="badge-skala badge-skala-<?= $m->skala_dampak_realisasi_current ?>">
                      <?= $m->skala_dampak_realisasi_current ?>
                    </span>
                    <span style="font-size: 12px; font-weight: 600; margin-left: 4px;">Skala <?= $m->skala_dampak_realisasi_current ?></span>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($m->skala_probabilitas_realisasi_current)): ?>
                    <span class="badge-skala badge-skala-<?= $m->skala_probabilitas_realisasi_current ?>">
                      <?= $m->skala_probabilitas_realisasi_current ?>
                    </span>
                    <span style="font-size: 12px; font-weight: 600; margin-left: 4px;">Skala <?= $m->skala_probabilitas_realisasi_current ?></span>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge-level-pill" style="background-color: <?= $bgLvl ?>; color: <?= $txtLvl ?>;">
                    Nilai: <?= $mScore ?> · <?= $lblLvl ?>
                  </span>
                </td>
                <td><?= nl2br(htmlspecialchars($m->kendala ?: '-')) ?></td>
                <td><?= nl2br(htmlspecialchars($m->action_plan ?: '-')) ?></td>
                <td style="text-align: center;">
                  <div style="display: inline-flex; gap: 6px;">
                    <button type="button" class="btn-act btn-act-edit" onclick="editMonitoring(<?= $m->id ?>)" title="Edit Progress Monitoring">
                      <span>✎</span> Edit
                    </button>
                    <button type="button" class="btn-act btn-act-delete" onclick="deleteMonitoring(<?= $m->id ?>)" title="Hapus Progress Monitoring">
                      <span>🗑</span> Hapus
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyMonRow">
              <td colspan="12" style="text-align: center; padding: 32px 16px; color: #64748b;">
                Belum ada data Progress Monitoring untuk profile risiko ini. Silakan isi form di bawah ini untuk menambahkan progres baru.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- CARD 3: FORMULIR PENGISIAN PROGRESS MONITORING -->
  <div class="mon-card" id="sectionFormMonitoring">
    <div class="mon-card-title">
      <span id="formActionIcon">📝</span>
      <span id="lblFormTitle">Formulir Pengisian Progress Monitoring</span>
    </div>
    <p class="mon-card-desc">Lengkapi 9 poin evaluasi perkembangan mitigasi dan kondisi current risiko.</p>

    <form id="formMonitoring" onsubmit="submitMonitoringForm(event)">
      <input type="hidden" id="monitoring_id" name="monitoring_id" value="" />
      <input type="hidden" id="formulir_id" name="formulir_id" value="<?= (int)$formulir->id ?>" />

      <!-- PENGATURAN PERIODE MONITORING (Per 3 Bulan / Per Bulan) -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
        <div style="font-size: 13.5px; font-weight: 700; color: var(--navy); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
          <span>🗓</span> Penetapan Periode Evaluasi Monitoring
        </div>
        <div class="form-grid-2">
          <!-- Tipe Frekuensi Periode -->
          <div class="profile-field-group">
            <label for="periode_tipe">Frekuensi Pemantauan <span style="color: var(--red);">*</span></label>
            <select id="periode_tipe" name="periode_tipe" onchange="onPeriodeTipeChange(this.value)">
              <option value="triwulan" selected>Per 3 Bulan (Triwulan)</option>
              <option value="bulanan">Per Bulan (Bulanan)</option>
            </select>
            <span class="helper-text">Pilih metode siklus pemantauan progres (default: Triwulan / 3 bulanan).</span>
          </div>

          <!-- Periode & Tahun -->
          <div style="display: grid; grid-template-columns: 1fr 120px; gap: 10px;">
            <div class="profile-field-group">
              <label for="periode">Pilihan Periode <span style="color: var(--red);">*</span></label>
              <select id="periode" name="periode" required>
                <!-- Populated dynamically via JS -->
              </select>
              <span class="helper-text">Pilih kuartal / bulan evaluasi.</span>
            </div>
            <div class="profile-field-group">
              <label for="tahun">Tahun <span style="color: var(--red);">*</span></label>
              <input type="number" id="tahun" name="tahun" value="<?= date('Y') ?>" min="2020" max="2040" required />
              <span class="helper-text">Tahun berjalan.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 9 POIN FORMULIR PROGRESS MONITORING -->
      <div class="form-grid-full">
        <!-- 1. Status Progres Tindak Lanjut -->
        <div class="profile-field-group">
          <label for="status_progres_tindak_lanjut">1. Status Progres Tindak Lanjut</label>
          <textarea id="status_progres_tindak_lanjut" name="status_progres_tindak_lanjut" rows="2" placeholder="Uraikan status keterlaksanaan rencana tindak lanjut mitigasi risiko pada periode ini..."></textarea>
          <span class="helper-text">Keterangan deskriptif pelaksanaan tindak lanjut (misal: Dalam proses pengadaan, terlaksana 100%, dll).</span>
        </div>

        <!-- 2. Realisasi Biaya -->
        <div class="profile-field-group">
          <label for="realisasi_biaya">2. Realisasi Biaya</label>
          <textarea id="realisasi_biaya" name="realisasi_biaya" rows="2" placeholder="Uraikan realisasi penyerapan anggaran biaya perlakuan risiko hingga periode ini..."></textarea>
          <span class="helper-text">Catatan realisasi anggaran atau nominal pembiayaan yang telah dikeluarkan.</span>
        </div>

        <!-- 3. Proses Setiap Tindak Lanjut dalam bentuk % -->
        <div class="profile-field-group">
          <label for="proses_tindak_lanjut_persen">3. Proses Setiap Tindak Lanjut dalam bentuk %</label>
          <textarea id="proses_tindak_lanjut_persen" name="proses_tindak_lanjut_persen" rows="2" placeholder="Contoh: Aksi 1 (SOP): 100%, Aksi 2 (Pelatihan): 75%, Aksi 3 (Pengadaan Sistem): 40%..."></textarea>
          <span class="helper-text">Rincian persentase capaian progres masing-masing rencana aksi mitigasi.</span>
        </div>

        <!-- 4. Persentase per Resiko -->
        <div class="profile-field-group">
          <label for="persentase_per_risiko">4. Persentase per Resiko</label>
          <input type="text" id="persentase_per_risiko" name="persentase_per_risiko" placeholder="Contoh: 80% atau Akumulasi Progres Total: 75%" />
          <span class="helper-text">Persentase agregat progres mitigasi per keseluruhan risiko.</span>
        </div>

        <!-- 5. Eksposur Risiko Kuantitatif Current (dalam Rupiah) -->
        <div class="profile-field-group">
          <label for="eksposur_kuantitatif_current">5. Eksposur Risiko Kuantitatif Current (dalam Rupiah)</label>
          <div style="position: relative; display: flex; align-items: center;">
            <span style="position: absolute; left: 14px; font-weight: 700; color: #64748b; font-size: 13px; pointer-events: none;">Rp</span>
            <input type="text" id="eksposur_kuantitatif_current" name="eksposur_kuantitatif_current" 
                   value="<?= htmlspecialchars($formulir->eksposure_kuantitatif ?: '') ?>"
                   placeholder="Contoh: 150.000.000" 
                   oninput="formatRupiahInput(this)"
                   style="padding-left: 42px; font-weight: 600; font-size: 13.5px;" />
          </div>
          <span class="helper-text">Default terisi dari nilai Eksposure Inheren (Tab 1/Tab 2: Rp <?= htmlspecialchars($formulir->eksposure_kuantitatif ?: '0') ?>), dapat disesuaikan dengan kondisi nilai kerugian kuantitatif saat ini.</span>
        </div>

        <!-- 6. Skala Dampak Realisasi Current (Berdasarkan Kategori Dampak Tab 2) -->
        <div class="profile-field-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; flex-wrap: wrap; gap: 6px;">
            <label style="margin-bottom: 0;">6. Skala Dampak Realisasi Current (Skala 1 s/d 5)</label>
            <span style="font-size: 12px; color: var(--teal); font-weight: 700;">
              Kategori Acuan: <?= htmlspecialchars($formulir->kategori_dampak_judul ? ($formulir->kategori_dampak_risiko ?: $formulir->kategori_dampak_judul) : 'Kategori Dampak Inheren') ?>
            </span>
          </div>
          <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat skala dampak realisasi current mengacu pada rincian kriteria kategori dampak yang telah ditetapkan:</span>

          <div class="radio-scale-grid" id="gridSkalaDampakReal">
            <?php
              $labelsMap = [
                1 => ['label' => 'Sangat Rendah', 'text' => $formulir->kategori_dampak_skala_1 ?? 'Tingkat dampak sangat rendah / minimal.'],
                2 => ['label' => 'Rendah',        'text' => $formulir->kategori_dampak_skala_2 ?? 'Tingkat dampak rendah.'],
                3 => ['label' => 'Moderat',       'text' => $formulir->kategori_dampak_skala_3 ?? 'Tingkat dampak sedang / moderat.'],
                4 => ['label' => 'Tinggi',        'text' => $formulir->kategori_dampak_skala_4 ?? 'Tingkat dampak signifikan / tinggi.'],
                5 => ['label' => 'Sangat Tinggi', 'text' => $formulir->kategori_dampak_skala_5 ?? 'Tingkat dampak kritis / sangat tinggi.']
              ];
              for ($lvl = 1; $lvl <= 5; $lvl++):
            ?>
              <label class="scale-card-option" id="card-dampak-real-<?= $lvl ?>">
                <input type="radio" name="skala_dampak_realisasi_current" value="<?= $lvl ?>" onchange="onSkalaDampakRealChange(this.value)" />
                <div style="flex: 1;">
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                    <span class="badge-skala badge-skala-<?= $lvl ?>"><?= $lvl ?></span>
                    <strong style="font-size: 13px; color: var(--navy);">
                      Skala <?= $lvl ?> · <?= $labelsMap[$lvl]['label'] ?>
                    </strong>
                  </div>
                  <p style="font-size: 12px; color: #475569; margin: 0; line-height: 1.45;">
                    <?= htmlspecialchars($labelsMap[$lvl]['text']) ?>
                  </p>
                </div>
              </label>
            <?php endfor; ?>
          </div>
        </div>

        <!-- 6b. Skala Probabilitas Realisasi Current (Pilihan sama seperti pada Tab 2) -->
        <div class="profile-field-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
            <label style="margin-bottom: 0;">Skala Probabilitas Realisasi Current (Skala 1 s/d 5)</label>
            <?php if (!empty($skala_probabilitas[0]->parameter)): ?>
              <span style="font-size: 11.5px; color: var(--muted); font-style: italic;">
                Parameter Acuan: <?= htmlspecialchars($skala_probabilitas[0]->parameter) ?>
              </span>
            <?php endif; ?>
          </div>
          <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat skala kemungkinan/probabilitas keterjadian realisasi current:</span>

          <div class="radio-scale-grid" id="gridSkalaProbReal">
            <?php
              $probRef = !empty($skala_probabilitas) ? $skala_probabilitas[0] : null;
              $probLevels = [
                1 => ['label' => 'Sangat Jarang Terjadi', 'desc' => $probRef ? $probRef->skala_1 : 'Paling banyak satu kali dalam setahun.'],
                2 => ['label' => 'Jarang Terjadi',        'desc' => $probRef ? $probRef->skala_2 : 'Hanya sekali dalam 6 bulan.'],
                3 => ['label' => 'Bisa Terjadi',          'desc' => $probRef ? $probRef->skala_3 : 'Pernah terjadi, sekali dalam 4 bulan.'],
                4 => ['label' => 'Sangat Mungkin Terjadi','desc' => $probRef ? $probRef->skala_4 : 'Pernah terjadi sekali dalam 2 bulan.'],
                5 => ['label' => 'Hampir Pasti Terjadi',  'desc' => $probRef ? $probRef->skala_5 : 'Pernah terjadi sekali dalam 1 bulan.']
              ];
              foreach ($probLevels as $lvl => $info):
            ?>
              <label class="scale-card-option" id="card-prob-real-<?= $lvl ?>">
                <input type="radio" name="skala_probabilitas_realisasi_current" value="<?= $lvl ?>" onchange="onSkalaProbRealChange(this.value)" />
                <div style="flex: 1;">
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                    <span class="badge-skala badge-skala-<?= $lvl ?>"><?= $lvl ?></span>
                    <strong style="font-size: 13px; color: var(--navy);"><?= $info['label'] ?></strong>
                  </div>
                  <p style="font-size: 12px; color: #475569; margin: 0; line-height: 1.45;">
                    <?= htmlspecialchars($info['desc']) ?>
                  </p>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- 7. Skala Risiko Realisasi (Nilai 1 - 25 dengan slider level progres mitigasi) -->
        <div class="profile-field-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
            <label for="skala_risiko_realisasi" style="margin-bottom: 0;">
              7. Skala Risiko Realisasi (Nilai 1 - 25) <span style="color: var(--red);">*</span>
            </label>
            <!-- Dynamic Level Badge -->
            <div id="badgeRiskLevelReal" style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; border-radius: 20px; font-weight: 800; font-size: 13px; background: #00b04f; color: #ffffff; transition: all .2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
              <span id="lblScoreReal">Nilai: 1</span>
              <span>·</span>
              <span id="lblLevelReal">Low</span>
            </div>
          </div>

          <!-- Slider Bar -->
          <div style="display: flex; align-items: center; gap: 14px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 16px;">
            <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 14px;">1</span>
            <input type="range" id="skala_risiko_realisasi" name="skala_risiko_realisasi" min="1" max="25" value="1" 
                   oninput="updateRealLevel(this.value)"
                   style="width: 100%; padding: 0; border: 0; background: transparent; accent-color: #00b04f; cursor: pointer;" />
            <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 20px;">25</span>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
            <span class="helper-text">
              Geser slider (1 s/d 25) untuk menentukan tingkat skala risiko realisasi current.
            </span>
            <!-- Keterangan Legend Level -->
            <div style="display: inline-flex; gap: 6px; font-size: 10.5px; font-weight: 700; flex-wrap: wrap;">
              <span style="background: #00b04f; color: #fff; padding: 2px 6px; border-radius: 4px;">1-5: Low</span>
              <span style="background: #92d14f; color: #0f172a; padding: 2px 6px; border-radius: 4px;">6-11: Low to Moderate</span>
              <span style="background: #ffff00; color: #0f172a; padding: 2px 6px; border-radius: 4px; border: 1px solid #eab308;">12-15: Moderate</span>
              <span style="background: #ffbf00; color: #0f172a; padding: 2px 6px; border-radius: 4px;">16-20: Moderate to Hight</span>
              <span style="background: #ff0000; color: #fff; padding: 2px 6px; border-radius: 4px;">21-25: Hight</span>
            </div>
          </div>
        </div>

        <!-- 8. Kendala -->
        <div class="profile-field-group">
          <label for="kendala">8. Kendala</label>
          <textarea id="kendala" name="kendala" rows="3" placeholder="Uraikan kendala, hambatan internal/eksternal, atau isu operasional yang dihadapi dalam pelaksanaan mitigasi..."></textarea>
          <span class="helper-text">Permasalahan atau hambatan yang mempengaruhi pencapaian target mitigasi.</span>
        </div>

        <!-- 9. Action Plan -->
        <div class="profile-field-group">
          <label for="action_plan">9. Action Plan</label>
          <textarea id="action_plan" name="action_plan" rows="3" placeholder="Uraikan rencana aksi pemulihan (recovery plan), langkah perbaikan lanjutan, atau penyesuaian strategi berikutnya..."></textarea>
          <span class="helper-text">Rencana tindak lanjut korektif untuk mengatasi kendala yang dihadapi.</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
        <button type="button" class="ghost" onclick="resetMonitoringForm()">
          Batal / Reset Form
        </button>
        <button type="submit" class="primary" id="btnSaveMonitoring">
          Simpan Progress Monitoring
        </button>
      </div>
    </form>
  </div>
</main>

<script>
  const defaultEksposureInheren = <?= json_encode($formulir->eksposure_kuantitatif ?? '') ?>;

  function formatRupiahInput(el) {
    let raw = el.value.replace(/[^0-9]/g, '');
    if (raw) {
      el.value = new Intl.NumberFormat('id-ID').format(raw);
    } else {
      el.value = '';
    }
  }

  function onPeriodeTipeChange(tipe, preselectedPeriode = null) {
    const $select = $('#periode');
    $select.empty();

    if (tipe === 'bulanan') {
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      months.forEach((m, idx) => {
        const val = m;
        const isSel = (preselectedPeriode === val || (!preselectedPeriode && idx === new Date().getMonth())) ? 'selected' : '';
        $select.append(`<option value="${val}" ${isSel}>${val}</option>`);
      });
    } else {
      // Triwulan (3 bulanan)
      const quarters = [
        { val: 'Triwulan I (Q1)', desc: 'Triwulan I (Januari - Maret)' },
        { val: 'Triwulan II (Q2)', desc: 'Triwulan II (April - Juni)' },
        { val: 'Triwulan III (Q3)', desc: 'Triwulan III (Juli - September)' },
        { val: 'Triwulan IV (Q4)', desc: 'Triwulan IV (Oktober - Desember)' }
      ];
      const curQuarterIdx = Math.floor(new Date().getMonth() / 3);
      quarters.forEach((q, idx) => {
        const isSel = (preselectedPeriode === q.val || (!preselectedPeriode && idx === curQuarterIdx)) ? 'selected' : '';
        $select.append(`<option value="${q.val}" ${isSel}>${q.desc}</option>`);
      });
    }
  }

  function onSkalaDampakRealChange(val) {
    $('.scale-card-option[id^="card-dampak-real-"]').removeClass('selected');
    $(`#card-dampak-real-${val}`).addClass('selected');
    recalculateRealScore();
  }

  function onSkalaProbRealChange(val) {
    $('.scale-card-option[id^="card-prob-real-"]').removeClass('selected');
    $(`#card-prob-real-${val}`).addClass('selected');
    recalculateRealScore();
  }

  function recalculateRealScore() {
    const dampak = parseInt($('input[name="skala_dampak_realisasi_current"]:checked').val(), 10);
    const prob   = parseInt($('input[name="skala_probabilitas_realisasi_current"]:checked').val(), 10);

    if (dampak && prob) {
      const calcScore = dampak * prob; // Range 1 - 25
      $('#skala_risiko_realisasi').val(calcScore);
      updateRealLevel(calcScore);
    }
  }

  function getRealLevelInfo(score) {
    score = parseInt(score, 10) || 1;
    if (score >= 21) {
      return { level: 'Hight', bg: '#ff0000', text: '#ffffff' };
    } else if (score >= 16) {
      return { level: 'Moderate to Hight', bg: '#ffbf00', text: '#0f172a' };
    } else if (score >= 12) {
      return { level: 'Moderate', bg: '#ffff00', text: '#0f172a' };
    } else if (score >= 6) {
      return { level: 'Low to Moderate', bg: '#92d14f', text: '#0f172a' };
    } else {
      return { level: 'Low', bg: '#00b04f', text: '#ffffff' };
    }
  }

  function updateRealLevel(val) {
    const score = parseInt(val, 10) || 1;
    const info  = getRealLevelInfo(score);

    $('#lblScoreReal').text('Nilai: ' + score);
    $('#lblLevelReal').text(info.level);

    $('#badgeRiskLevelReal').css({
      'background-color': info.bg,
      'color': info.text
    });

    $('#skala_risiko_realisasi').css('accent-color', info.bg);
  }

  function resetMonitoringForm() {
    $('#monitoring_id').val('');
    $('#lblFormTitle').text('Formulir Pengisian Progress Monitoring');
    $('#btnSaveMonitoring').text('Simpan Progress Monitoring');

    $('#periode_tipe').val('triwulan');
    onPeriodeTipeChange('triwulan');
    $('#tahun').val(new Date().getFullYear());

    $('#status_progres_tindak_lanjut').val('');
    $('#realisasi_biaya').val('');
    $('#proses_tindak_lanjut_persen').val('');
    $('#persentase_per_risiko').val('');
    $('#eksposur_kuantitatif_current').val(defaultEksposureInheren || '');

    $('input[name="skala_dampak_realisasi_current"]').prop('checked', false);
    $('.scale-card-option[id^="card-dampak-real-"]').removeClass('selected');

    $('input[name="skala_probabilitas_realisasi_current"]').prop('checked', false);
    $('.scale-card-option[id^="card-prob-real-"]').removeClass('selected');

    $('#skala_risiko_realisasi').val(1);
    updateRealLevel(1);

    $('#kendala').val('');
    $('#action_plan').val('');
  }

  function editMonitoring(id) {
    $.ajax({
      url: '<?= site_url("menrisk/detail_monitoring_profil_risiko/") ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success' && res.data) {
          const d = res.data;
          $('#monitoring_id').val(d.id);
          $('#lblFormTitle').text('Edit Progress Monitoring (' + (d.periode || '') + ' ' + (d.tahun || '') + ')');
          $('#btnSaveMonitoring').text('Perbarui Progress Monitoring');

          $('#periode_tipe').val(d.periode_tipe || 'triwulan');
          onPeriodeTipeChange(d.periode_tipe || 'triwulan', d.periode);
          $('#tahun').val(d.tahun || new Date().getFullYear());

          $('#status_progres_tindak_lanjut').val(d.status_progres_tindak_lanjut || '');
          $('#realisasi_biaya').val(d.realisasi_biaya || '');
          $('#proses_tindak_lanjut_persen').val(d.proses_tindak_lanjut_persen || '');
          $('#persentase_per_risiko').val(d.persentase_per_risiko || '');
          $('#eksposur_kuantitatif_current').val(d.eksposur_kuantitatif_current || '');

          if (d.skala_dampak_realisasi_current) {
            $(`input[name="skala_dampak_realisasi_current"][value="${d.skala_dampak_realisasi_current}"]`).prop('checked', true);
            $('.scale-card-option[id^="card-dampak-real-"]').removeClass('selected');
            $(`#card-dampak-real-${d.skala_dampak_realisasi_current}`).addClass('selected');
          } else {
            $('input[name="skala_dampak_realisasi_current"]').prop('checked', false);
            $('.scale-card-option[id^="card-dampak-real-"]').removeClass('selected');
          }

          if (d.skala_probabilitas_realisasi_current) {
            $(`input[name="skala_probabilitas_realisasi_current"][value="${d.skala_probabilitas_realisasi_current}"]`).prop('checked', true);
            $('.scale-card-option[id^="card-prob-real-"]').removeClass('selected');
            $(`#card-prob-real-${d.skala_probabilitas_realisasi_current}`).addClass('selected');
          } else {
            $('input[name="skala_probabilitas_realisasi_current"]').prop('checked', false);
            $('.scale-card-option[id^="card-prob-real-"]').removeClass('selected');
          }

          const score = parseInt(d.skala_risiko_realisasi, 10) || 1;
          $('#skala_risiko_realisasi').val(score);
          updateRealLevel(score);

          $('#kendala').val(d.kendala || '');
          $('#action_plan').val(d.action_plan || '');

          // Scroll halus ke form
          $('html, body').animate({
            scrollTop: $('#sectionFormMonitoring').offset().top - 80
          }, 200);
        } else {
          showToast(res.message || 'Gagal mengambil data monitoring.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi.');
      }
    });
  }

  function deleteMonitoring(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data progress monitoring periode ini?')) {
      return;
    }

    $.ajax({
      url: '<?= site_url("menrisk/delete_monitoring_profil_risiko/") ?>' + id,
      type: 'POST',
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Data berhasil dihapus.');
          $('#row-mon-' + id).fadeOut(250, function() {
            $(this).remove();
            if ($('#tableRiwayatMonitoring tbody tr').length === 0) {
              window.location.reload();
            }
          });
        } else {
          showToast(res.message || 'Gagal menghapus data.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan saat menghapus data.');
      }
    });
  }

  function submitMonitoringForm(e) {
    if (e && e.preventDefault) e.preventDefault();

    const $btn = $('#btnSaveMonitoring');
    $btn.prop('disabled', true).text('Menyimpan...');

    const formData = new FormData($('#formMonitoring')[0]);

    $.ajax({
      url: '<?= site_url("menrisk/save_monitoring_profil_risiko") ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Progress Monitoring berhasil disimpan.');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast(res.message || 'Gagal menyimpan progress monitoring.');
          $btn.prop('disabled', false).text('Simpan Progress Monitoring');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menyimpan progress monitoring.');
        $btn.prop('disabled', false).text('Simpan Progress Monitoring');
      }
    });
  }

  $(function() {
    onPeriodeTipeChange('triwulan');
    updateRealLevel($('#skala_risiko_realisasi').val() || 1);
  });
</script>

<?php $this->load->view('templates/footer'); ?>

