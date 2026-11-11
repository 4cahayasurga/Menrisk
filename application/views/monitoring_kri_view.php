<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * View: Progress Monitoring Key Risk Indicator (KRI)
 * IFPRO Risk Command Center
 */
$this->load->view('templates/header', ['title' => $title ?? 'Progress Monitoring KRI — IFPRO Risk Command Center']);
$this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'kri', 'is_dashboard' => false]);
?>
<style>
/* Card Styles */
.mon-card {
  background: #ffffff;
  border: 1px solid var(--line, #e2e8f0);
  border-radius: 12px;
  padding: 24px 28px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  margin-bottom: 24px;
}
.mon-card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--navy, #0f172a);
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}
.mon-card-desc {
  font-size: 13px;
  color: var(--muted, #64748b);
  margin-bottom: 18px;
}

/* Info KRI Header Banner */
.kri-summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 18px 20px;
}
.kri-summary-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.kri-summary-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: .05em;
}
.kri-summary-val {
  font-size: 13.5px;
  color: #0f172a;
  font-weight: 600;
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
  min-width: 950px;
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

/* Action Buttons */
.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 11.5px;
  font-weight: 600;
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
.btn-act-edit:hover {
  background: #dbeafe;
}
.btn-act-delete {
  background: #fef2f2;
  color: #b91c1c;
  border-color: #fecaca;
}
.btn-act-delete:hover {
  background: #fee2e2;
}

/* Form Styles */
.form-grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 18px;
}
.profile-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}
.profile-field-group label {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
}
.profile-field-group .helper-text {
  font-size: 11.5px;
  color: var(--muted, #64748b);
}
.profile-field-group input,
.profile-field-group select,
.profile-field-group textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 7px;
  font-size: 13.5px;
  color: #0f172a;
  background-color: #fff;
  transition: border-color .15s ease, box-shadow .15s ease;
  font-family: inherit;
}
.profile-field-group input:focus,
.profile-field-group select:focus,
.profile-field-group textarea:focus {
  outline: none;
  border-color: #29b6af;
  box-shadow: 0 0 0 3px rgba(41, 182, 175, 0.15);
}

/* Threshold Option Cards (Pilih Threshold dengan Klik Warna) */
.threshold-picker-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 12px;
}
@media (max-width: 768px) {
  .threshold-picker-grid {
    grid-template-columns: 1fr;
  }
}
.threshold-pick-card {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 14px 16px;
  cursor: pointer;
  transition: all .2s ease;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  gap: 6px;
  position: relative;
  user-select: none;
}
.threshold-pick-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.threshold-pick-card.selected.pick-aman {
  border-color: #00b04f !important;
  background: #f0fdf4 !important;
  box-shadow: 0 0 0 3px rgba(0, 176, 79, 0.25);
}
.threshold-pick-card.selected.pick-hati_hati {
  border-color: #ca8a04 !important;
  background: #fefce8 !important;
  box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.35);
}
.threshold-pick-card.selected.pick-bahaya {
  border-color: #ff0000 !important;
  background: #fef2f2 !important;
  box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.25);
}

.threshold-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.color-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}
.color-badge-pill.aman {
  background: #00b04f;
  color: #ffffff;
}
.color-badge-pill.hati_hati {
  background: #ffff00;
  color: #0f172a;
  border: 1px solid #eab308;
}
.color-badge-pill.bahaya {
  background: #ff0000;
  color: #ffffff;
}

.pick-check-mark {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  color: transparent;
}
.threshold-pick-card.selected .pick-check-mark {
  background: #0f172a;
  border-color: #0f172a;
  color: #ffffff;
}
.threshold-pick-card.selected.pick-aman .pick-check-mark {
  background: #00b04f;
  border-color: #00b04f;
}
.threshold-pick-card.selected.pick-hati_hati .pick-check-mark {
  background: #ca8a04;
  border-color: #ca8a04;
}
.threshold-pick-card.selected.pick-bahaya .pick-check-mark {
  background: #ff0000;
  border-color: #ff0000;
}

.threshold-ref-text {
  font-size: 12px;
  color: #475569;
  line-height: 1.4;
  margin-top: 4px;
}

/* Badge Hasil Monitoring pada Tabel */
.badge-result {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 9px;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-result.aman {
  background: #00b04f;
  color: #ffffff;
}
.badge-result.hati_hati {
  background: #ffff00;
  color: #0f172a;
  border: 1px solid #eab308;
}
.badge-result.bahaya {
  background: #ff0000;
  color: #ffffff;
}
</style>

<main>
  <!-- HEADER -->
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">MANAJEMEN RISIKO · MONITORING EVALUASI KRI</p>
      <h1>Progress Monitoring KRI</h1>
      <p class="subtitle">Evaluasi berkala (per 3 bulan atau per bulan) terhadap kondisi dan capaian ambang batas Key Risk Indicator.</p>
    </div>
    <div class="header-actions">
      <a href="<?= site_url('menrisk/daftar_kri') ?>" class="ghost" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
        <span>←</span> Kembali ke Daftar KRI
      </a>
      <a href="#sectionFormMonitoringKri" class="primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600;">
        <span>+</span> Tambah Evaluasi Baru
      </a>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <!-- CARD 1: INFORMASI KEY RISK INDICATOR (KRI) -->
  <div class="mon-card">
    <div class="mon-card-title">
      <span>◫</span> Informasi Key Risk Indicator
    </div>
    <p class="mon-card-desc">Parameter acuan metrik dan batas ambang toleransi yang dipantau.</p>

    <div class="kri-summary-grid">
      <div class="kri-summary-item" style="grid-column: span 2;">
        <span class="kri-summary-label">Nama Risiko (Profile Risiko)</span>
        <span class="kri-summary-val" style="font-size: 14.5px; color: var(--navy);">
          <?= htmlspecialchars($kri->nama_risiko ?: '-') ?>
        </span>
        <?php if (!empty($kri->sasaran)): ?>
          <span style="font-size: 11.5px; color: #64748b;">Sasaran: <?= htmlspecialchars($kri->sasaran) ?></span>
        <?php endif; ?>
      </div>

      <div class="kri-summary-item">
        <span class="kri-summary-label">Polaritas Capaian</span>
        <span class="kri-summary-val">
          <?php if (strtolower($kri->polaritas ?? '') === 'minimize'): ?>
            <span style="display:inline-flex; align-items:center; gap:4px; color:#475569; font-weight:700;">▼ Minimize (Makin Rendah Makin Baik)</span>
          <?php else: ?>
            <span style="display:inline-flex; align-items:center; gap:4px; color:#0369a1; font-weight:700;">▲ Maximize (Makin Tinggi Makin Baik)</span>
          <?php endif; ?>
        </span>
      </div>

      <div class="kri-summary-item" style="grid-column: span 2;">
        <span class="kri-summary-label">Penyebab Risiko</span>
        <span class="kri-summary-val" style="font-weight: normal; font-size: 13px;">
          <?= nl2br(htmlspecialchars($kri->penyebab_risiko ?: '-')) ?>
        </span>
      </div>

      <div class="kri-summary-item">
        <span class="kri-summary-label">Deskripsi KRI</span>
        <span class="kri-summary-val" style="font-weight: normal; font-size: 13px;">
          <?= nl2br(htmlspecialchars($kri->deskripsi ?: '-')) ?>
        </span>
      </div>

      <!-- Ambang Batas Threshold KRI -->
      <div class="kri-summary-item" style="grid-column: 1 / -1; border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 4px;">
        <span class="kri-summary-label">Ambang Batas Toleransi (Threshold) Acuan</span>
        <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-top: 6px;">
          <div style="display: flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 6px;">
            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #00b04f;"></span>
            <span style="font-size: 12px; font-weight: 700; color: #15803d;">Batas Aman: <?= htmlspecialchars($kri->ambang_batas_aman ?: '-') ?></span>
          </div>
          <div style="display: flex; align-items: center; gap: 6px; background: #fefce8; border: 1px solid #fef08a; padding: 4px 10px; border-radius: 6px;">
            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #ffff00; border: 1px solid #ca8a04;"></span>
            <span style="font-size: 12px; font-weight: 700; color: #854d0e;">Batas Hati-Hati: <?= htmlspecialchars($kri->ambang_batas_hati_hati ?: '-') ?></span>
          </div>
          <div style="display: flex; align-items: center; gap: 6px; background: #fef2f2; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 6px;">
            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #ff0000;"></span>
            <span style="font-size: 12px; font-weight: 700; color: #b91c1c;">Batas Bahaya: <?= htmlspecialchars($kri->ambang_batas_bahaya ?: '-') ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CARD 2: RIWAYAT EVALUASI PROGRESS MONITORING KRI -->
  <div class="mon-card">
    <div class="mon-card-title">
      <span>📋</span> Riwayat Evaluasi Progress Monitoring KRI
    </div>
    <p class="mon-card-desc">Histori seluruh pemantauan berkala metrik KRI, hasil threshold, dan tindak lanjut perbaikan.</p>

    <div class="table-scroll-wrap">
      <table class="mon-table" id="tableRiwayatMonKri">
        <thead>
          <tr>
            <th style="width: 44px; text-align: center;">No</th>
            <th style="min-width: 120px;">Periode Evaluasi</th>
            <th style="min-width: 220px;">1. Monitoring (Realisasi Metrik)</th>
            <th style="min-width: 200px;">2. Hasil Threshold &amp; Keterangan</th>
            <th style="min-width: 240px;">3. Tindak Lanjut</th>
            <th style="width: 120px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($monitoring_list)): ?>
            <?php foreach ($monitoring_list as $mIdx => $m): ?>
              <?php
                $thKey = strtolower($m->hasil_threshold ?? 'aman');
                $thLabel = ($thKey === 'aman') ? 'Aman' : (($thKey === 'hati_hati') ? 'Hati-Hati' : 'Bahaya');
              ?>
              <tr id="row-mon-kri-<?= $m->id ?>">
                <td style="text-align: center; font-weight: 700; color: #64748b;"><?= $mIdx + 1 ?></td>
                <td>
                  <span style="font-weight: 700; color: var(--navy);"><?= htmlspecialchars($m->periode) ?> <?= htmlspecialchars($m->tahun) ?></span>
                  <div style="font-size: 11px; color: var(--muted); text-transform: capitalize;"><?= htmlspecialchars($m->periode_tipe) ?></div>
                </td>
                <td>
                  <strong><?= nl2br(htmlspecialchars($m->monitoring ?: '-')) ?></strong>
                </td>
                <td>
                  <span class="badge-result <?= $thKey ?>">
                    ● <?= $thLabel ?>
                  </span>
                  <?php if (!empty($m->hasil_keterangan)): ?>
                    <div style="font-size: 12px; color: #334155; margin-top: 4px;">
                      <?= nl2br(htmlspecialchars($m->hasil_keterangan)) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <?= nl2br(htmlspecialchars($m->tindak_lanjut ?: '-')) ?>
                </td>
                <td style="text-align: center;">
                  <div style="display: inline-flex; gap: 6px;">
                    <button type="button" class="btn-act btn-act-edit" onclick="editMonKri(<?= (int)$m->id ?>)" title="Edit Evaluasi Monitoring">
                      <span>✎</span> Edit
                    </button>
                    <button type="button" class="btn-act btn-act-delete" onclick="deleteMonKri(<?= (int)$m->id ?>)" title="Hapus Evaluasi Monitoring">
                      <span>🗑</span> Hapus
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyMonKriRow">
              <td colspan="6" style="text-align: center; padding: 32px 16px; color: #64748b;">
                Belum ada data Progress Monitoring untuk KRI ini. Silakan lengkapi formulir di bawah ini untuk mencatat evaluasi berkala.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- CARD 3: FORMULIR EVALUASI PROGRESS MONITORING KRI -->
  <div class="mon-card" id="sectionFormMonitoringKri">
    <div class="mon-card-title">
      <span id="formMonActionIcon">📝</span>
      <span id="lblFormMonTitle">Formulir Pengisian Progress Monitoring KRI</span>
    </div>
    <p class="mon-card-desc">Lengkapi 3 poin evaluasi pemantauan berkala KRI: Monitoring, Hasil Threshold, dan Tindak Lanjut.</p>

    <form id="formMonitoringKri" onsubmit="submitMonitoringKriForm(event)">
      <input type="hidden" id="mon_kri_id" name="monitoring_id" value="" />
      <input type="hidden" id="kri_id" name="kri_id" value="<?= (int)$kri->id ?>" />
      <input type="hidden" id="hasil_threshold" name="hasil_threshold" value="aman" />

      <!-- PENGATURAN PERIODE EVALUASI (Per 3 Bulan / Per Bulan) -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
        <div style="font-size: 13.5px; font-weight: 700; color: var(--navy); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
          <span>🗓</span> Penetapan Periode Evaluasi Monitoring
        </div>
        <div class="form-grid-2">
          <!-- Tipe Frekuensi Periode -->
          <div class="profile-field-group">
            <label for="periode_tipe">Frekuensi Pemantauan <span style="color: var(--red);">*</span></label>
            <select id="periode_tipe" name="periode_tipe" onchange="onPeriodeTipeChange(this.value)">
              <option value="triwulan">Per 3 Bulan (Triwulan)</option>
              <option value="bulanan">Per Bulan (Bulanan)</option>
            </select>
            <span class="helper-text">Pilih opsi interval evaluasi pemantauan KRI.</span>
          </div>

          <!-- Periode & Tahun -->
          <div class="form-grid-2">
            <div class="profile-field-group">
              <label for="periode">Periode <span style="color: var(--red);">*</span></label>
              <select id="periode" name="periode" required>
                <!-- Opsi diisi otomatis via JavaScript -->
              </select>
            </div>
            <div class="profile-field-group">
              <label for="tahun">Tahun <span style="color: var(--red);">*</span></label>
              <select id="tahun" name="tahun" required>
                <?php
                  $currentYear = (int)date('Y');
                  for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++) {
                      $sel = ($y === $currentYear) ? 'selected' : '';
                      echo "<option value=\"{$y}\" {$sel}>{$y}</option>";
                  }
                ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- 1. Monitoring (Free text) -->
      <div class="profile-field-group">
        <label for="monitoring">1. Monitoring (Realisasi Metrik / Kondisi Terkini) <span style="color: var(--red);">*</span></label>
        <input type="text" id="monitoring" name="monitoring" required 
               placeholder="Contoh: Realisasi ketersediaan armada kapal penyeberangan bulan ini mencapai 98,2% atau Rp 4,5 Miliar" />
        <span class="helper-text">Keterangan realisasi angka metrik, kondisi capaian, atau status pemantauan KRI terkini (free text).</span>
      </div>

      <!-- 2. Hasil: Pilih Threshold dengan cara klik warna #00b04f, #ffff00, #ff0000 + Keterangan -->
      <div class="profile-field-group">
        <label>2. Hasil Threshold <span style="color: var(--red);">*</span></label>
        <span class="helper-text" style="margin-bottom: 8px;">
          Pilih status capaian dengan mengklik salah satu kartu warna threshold di bawah ini:
        </span>

        <div class="threshold-picker-grid">
          <!-- Opsi 1: Aman (#00b04f) -->
          <div class="threshold-pick-card pick-aman selected" id="cardPickAman" onclick="selectThreshold('aman')">
            <div class="threshold-card-top">
              <span class="color-badge-pill aman">
                <span>●</span> Batas Aman
              </span>
              <div class="pick-check-mark">✓</div>
            </div>
            <div class="threshold-ref-text">
              <strong>Acuan:</strong> <?= htmlspecialchars($kri->ambang_batas_aman ?: 'Kondisi aman / target tercapai') ?>
            </div>
          </div>

          <!-- Opsi 2: Hati-Hati (#ffff00) -->
          <div class="threshold-pick-card pick-hati_hati" id="cardPickHatiHati" onclick="selectThreshold('hati_hati')">
            <div class="threshold-card-top">
              <span class="color-badge-pill hati_hati">
                <span>●</span> Batas Hati-Hati
              </span>
              <div class="pick-check-mark">✓</div>
            </div>
            <div class="threshold-ref-text">
              <strong>Acuan:</strong> <?= htmlspecialchars($kri->ambang_batas_hati_hati ?: 'Kondisi waspada / perlu perhatian mitigasi') ?>
            </div>
          </div>

          <!-- Opsi 3: Bahaya (#ff0000) -->
          <div class="threshold-pick-card pick-bahaya" id="cardPickBahaya" onclick="selectThreshold('bahaya')">
            <div class="threshold-card-top">
              <span class="color-badge-pill bahaya">
                <span>●</span> Batas Bahaya
              </span>
              <div class="pick-check-mark">✓</div>
            </div>
            <div class="threshold-ref-text">
              <strong>Acuan:</strong> <?= htmlspecialchars($kri->ambang_batas_bahaya ?: 'Kondisi bahaya / melampaui toleransi risiko') ?>
            </div>
          </div>
        </div>

        <!-- Keterangan hasil inputannya -->
        <label for="hasil_keterangan" style="margin-top: 6px; font-size: 12.5px;">Keterangan Hasil Inputan Threshold:</label>
        <input type="text" id="hasil_keterangan" name="hasil_keterangan" 
               placeholder="Contoh: Capaian berada dalam batas aman dengan deviasi terkendali di bawah 1,5%..." />
        <span class="helper-text">Keterangan deskriptif atas hasil inputan nilai threshold yang dipilih.</span>
      </div>

      <!-- KETERANGAN ATURAN WAJIB TINDAK LANJUT LEVEL MERAH -->
      <div id="noticeTindakLanjut" style="margin-bottom: 14px;"></div>

      <!-- 3. Tindak Lanjut (Free text textarea) -->
      <div class="profile-field-group">
        <label for="tindak_lanjut" id="lblTindakLanjut">3. Tindak Lanjut <span style="color: #64748b; font-weight: normal; font-size: 12px;">(Opsional / Tidak perlu diisi untuk level Hijau &amp; Kuning)</span></label>
        <textarea id="tindak_lanjut" name="tindak_lanjut" rows="4" 
                  placeholder="Opsional: Catatan perbaikan tambahan jika diperlukan (tidak wajib diisi jika level Hijau / Kuning)..."></textarea>
        <span class="helper-text" id="helperTindakLanjut">
          Apabila Hasil KRI berada pada <b>level MERAH (Batas Bahaya)</b> maka <b>wajib mengisi</b> tindak lanjut. Jika masih pada level <b>hijau/kuning</b> tidak perlu diisi (opsional).
        </span>
      </div>

      <!-- FOOTER BUTTONS -->
      <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
        <button type="button" class="ghost" id="btnCancelEditMonKri" onclick="resetMonKriForm()" style="display: none; padding: 10px 18px; font-size: 13px; font-weight: 600;">
          Batal Edit
        </button>
        <button type="submit" class="primary btn-submit-mon-kri" id="btnSaveMonKri" style="padding: 10px 24px; font-size: 13.5px; font-weight: 600;">
          Simpan Progress Monitoring KRI
        </button>
      </div>
    </form>
  </div>
</main>

<script>
  let isMonKriSubmitting = false;

  const triwulanOptions = [
    { val: 'Triwulan I', text: 'Triwulan I (Januari - Maret)' },
    { val: 'Triwulan II', text: 'Triwulan II (April - Juni)' },
    { val: 'Triwulan III', text: 'Triwulan III (Juli - September)' },
    { val: 'Triwulan IV', text: 'Triwulan IV (Oktober - Desember)' }
  ];

  const bulanOptions = [
    { val: 'Januari', text: 'Januari' },
    { val: 'Februari', text: 'Februari' },
    { val: 'Maret', text: 'Maret' },
    { val: 'April', text: 'April' },
    { val: 'Mei', text: 'Mei' },
    { val: 'Juni', text: 'Juni' },
    { val: 'Juli', text: 'Juli' },
    { val: 'Agustus', text: 'Agustus' },
    { val: 'September', text: 'September' },
    { val: 'Oktober', text: 'Oktober' },
    { val: 'November', text: 'November' },
    { val: 'Desember', text: 'Desember' }
  ];

  function onPeriodeTipeChange(tipe) {
    const $p = $('#periode');
    $p.empty();
    const list = (tipe === 'bulanan') ? bulanOptions : triwulanOptions;
    list.forEach(item => {
      $p.append(`<option value="${item.val}">${item.text}</option>`);
    });
  }

  // Pilih Threshold dengan klik warna
  function selectThreshold(val) {
    $('#hasil_threshold').val(val);
    $('.threshold-pick-card').removeClass('selected');

    const $lbl = $('#lblTindakLanjut');
    const $txt = $('#tindak_lanjut');
    const $notice = $('#noticeTindakLanjut');

    if (val === 'bahaya') {
      $('#cardPickBahaya').addClass('selected');
      $lbl.html('3. Tindak Lanjut <span style="color: #dc2626; font-weight: 700;">* (Wajib Diisi karena Hasil KRI berada pada Level MERAH)</span>');
      $txt.prop('required', true)
          .css('border-color', '#ef4444')
          .attr('placeholder', 'Wajib diisi: Uraikan rencana aksi darurat, langkah perbaikan, atau eskalasi mitigasi penanganan level bahaya...');
      $notice.html(`
        <div style="display:flex;align-items:flex-start;gap:12px;background:#fef2f2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;font-size:13px;line-height:1.5;">
          <div>
            <div style="font-weight:700;margin-bottom:2px;">Perhatian: Level KRI Merah (Batas Bahaya)</div>
            <div>Karena hasil capaian KRI berada pada <strong>Level MERAH</strong>, maka pengisian kolom <strong>3. Tindak Lanjut wajib diisi</strong> untuk rencana mitigasi/eskalasi.</div>
          </div>
        </div>
      `);
    } else if (val === 'hati_hati') {
      $('#cardPickHatiHati').addClass('selected');
      $lbl.html('3. Tindak Lanjut <span style="color: #64748b; font-weight: normal; font-size: 12px;">(Opsional / Tidak perlu diisi untuk level Kuning)</span>');
      $txt.prop('required', false)
          .css('border-color', '#cbd5e1')
          .attr('placeholder', 'Opsional: Catatan pemantauan berkala jika diperlukan (tidak wajib diisi jika level Kuning)...');
      $notice.html(`
        <div style="display:flex;align-items:flex-start;gap:12px;background:#fefce8;border:1px solid #fde047;color:#854d0e;padding:10px 16px;border-radius:8px;font-size:12.5px;line-height:1.5;">
          <div>Hasil KRI berada pada <strong>Level KUNING (Batas Hati-Hati)</strong>. Kolom Tindak Lanjut <strong>tidak perlu diisi</strong> (opsional).</div>
        </div>
      `);
    } else {
      $('#cardPickAman').addClass('selected');
      $lbl.html('3. Tindak Lanjut <span style="color: #64748b; font-weight: normal; font-size: 12px;">(Opsional / Tidak perlu diisi untuk level Hijau)</span>');
      $txt.prop('required', false)
          .css('border-color', '#cbd5e1')
          .attr('placeholder', 'Opsional: Catatan tambahan jika diperlukan (tidak wajib diisi jika level Hijau)...');
      $notice.html(`
        <div style="display:flex;align-items:flex-start;gap:12px;background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:8px;font-size:12.5px;line-height:1.5;">
          <div>Hasil KRI berada pada <strong>Level HIJAU (Batas Aman)</strong>. Kondisi operasional aman dan terkendali, kolom Tindak Lanjut <strong>tidak perlu diisi</strong>.</div>
        </div>
      `);
    }
  }

  // Reset Formulir Monitoring
  function resetMonKriForm() {
    $('#formMonitoringKri')[0].reset();
    $('#mon_kri_id').val('');
    $('#lblFormMonTitle').text('Formulir Pengisian Progress Monitoring KRI');
    $('#btnSaveMonKri').text('Simpan Progress Monitoring KRI');
    $('#btnCancelEditMonKri').hide();
    onPeriodeTipeChange('triwulan');
    selectThreshold('aman');
  }

  // Edit Progress Monitoring
  function editMonKri(id) {
    $.ajax({
      url: '<?= site_url("menrisk/detail_monitoring_kri/") ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success' && res.data) {
          const d = res.data;
          $('#mon_kri_id').val(d.id);
          $('#periode_tipe').val(d.periode_tipe || 'triwulan');
          onPeriodeTipeChange(d.periode_tipe || 'triwulan');
          $('#periode').val(d.periode);
          $('#tahun').val(d.tahun);
          $('#monitoring').val(d.monitoring || '');
          selectThreshold(d.hasil_threshold || 'aman');
          $('#hasil_keterangan').val(d.hasil_keterangan || '');
          $('#tindak_lanjut').val(d.tindak_lanjut || '');

          $('#lblFormMonTitle').text('Edit Progress Monitoring KRI (' + d.periode + ' ' + d.tahun + ')');
          $('#btnSaveMonKri').text('Perbarui Progress Monitoring KRI');
          $('#btnCancelEditMonKri').show();

          // Smooth scroll ke form
          $('html, body').animate({
            scrollTop: $('#sectionFormMonitoringKri').offset().top - 80
          }, 400);
        } else {
          showToast(res.message || 'Gagal memuat detail monitoring.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat memuat data.');
      }
    });
  }

  // Hapus Progress Monitoring
  function deleteMonKri(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data progress monitoring KRI ini?')) {
      return;
    }

    $.ajax({
      url: '<?= site_url("menrisk/delete_monitoring_kri/") ?>' + id,
      type: 'POST',
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Data monitoring KRI berhasil dihapus.');
          $('#row-mon-kri-' + id).fadeOut(300, function() {
            $(this).remove();
            if ($('#tableRiwayatMonKri tbody tr').length === 0) {
              window.location.reload();
            }
          });
        } else {
          showToast(res.message || 'Gagal menghapus data monitoring.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menghapus data.');
      }
    });
  }

  // Submit Monitoring KRI Handler dengan disable button & proteksi duplikasi
  function submitMonitoringKriForm(e) {
    if (e && e.preventDefault) e.preventDefault();

    if (isMonKriSubmitting) return false;

    const monitoring = $.trim($('#monitoring').val());
    const tindak     = $.trim($('#tindak_lanjut').val());
    const threshold  = $.trim($('#hasil_threshold').val());

    if (!monitoring) {
      showToast('Mohon isi kolom 1. Monitoring.');
      $('#monitoring').focus();
      return;
    }
    if (!threshold) {
      showToast('Mohon pilih 2. Hasil Threshold (Aman / Hati-Hati / Bahaya).');
      return;
    }
    if (threshold === 'bahaya' && !tindak) {
      showToast('Hasil KRI berada pada Level MERAH (Batas Bahaya), maka kolom 3. Tindak Lanjut wajib diisi!');
      $('#tindak_lanjut').focus();
      return;
    }

    // Lock & disable button
    isMonKriSubmitting = true;
    const $btn = $('#btnSaveMonKri');
    $btn.prop('disabled', true).addClass('disabled').css('pointer-events', 'none').text('Menyimpan data...');

    const formData = new FormData($('#formMonitoringKri')[0]);

    $.ajax({
      url: '<?= site_url("menrisk/save_monitoring_kri") ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Progress Monitoring KRI berhasil disimpan.');
          $btn.text('Tersimpan, memuat ulang...');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast(res.message || 'Gagal menyimpan progress monitoring KRI.');
          isMonKriSubmitting = false;
          $btn.prop('disabled', false).removeClass('disabled').css('pointer-events', '').text('Simpan Progress Monitoring KRI');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menyimpan progress monitoring.');
        isMonKriSubmitting = false;
        $btn.prop('disabled', false).removeClass('disabled').css('pointer-events', '').text('Simpan Progress Monitoring KRI');
      }
    });
  }

  $(function() {
    onPeriodeTipeChange('triwulan');
    selectThreshold('aman');
  });
</script>

<?php $this->load->view('templates/footer'); ?>