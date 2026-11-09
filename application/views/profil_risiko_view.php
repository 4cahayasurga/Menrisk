<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Formulir Profile Risiko — IFPRO Risk Command Center']);
?>
<!-- Sidebar Navigation Template -->
<?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'register', 'is_dashboard' => false]); ?>

<style>
/* ==========================================================================
   FORMULIR PROFILE RISIKO STYLES
   ========================================================================== */
.form-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.form-card-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--navy, #0f172a);
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.form-card-desc {
  font-size: 12.5px;
  color: var(--muted, #64748b);
  margin-bottom: 18px;
}

/* Tabs Bar */
.profile-tabs-bar {
  display: flex;
  gap: 8px;
  border-bottom: 2px solid #e2e8f0;
  margin-bottom: 24px;
  overflow-x: auto;
}
.profile-tab-btn {
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  padding: 12px 20px;
  font-size: 13.5px;
  font-weight: 600;
  color: var(--muted, #64748b);
  cursor: pointer;
  transition: all .2s ease;
  margin-bottom: -2px;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.profile-tab-btn:hover {
  color: var(--navy, #0f172a);
}
.profile-tab-btn.active {
  color: var(--navy, #0f172a);
  border-bottom-color: var(--teal, #00a89d);
  font-weight: 700;
}
.tab-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  font-size: 11.5px;
  background: #f1f5f9;
  color: #475569;
}
.profile-tab-btn.active .tab-num {
  background: var(--teal, #00a89d);
  color: #ffffff;
}

/* Grid Layouts */
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
  min-height: 72px;
}

/* Radio Scale Cards */
.radio-scale-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
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

/* Kualitatif Preview Cards */
.kualitatif-scale-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  cursor: pointer;
  transition: all .15s ease;
}
.kualitatif-scale-card:hover {
  border-color: #00a89d;
  transform: translateY(-1px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.kualitatif-scale-card.active-scale {
  border-color: #00a89d;
  background: #f0fdfa;
  box-shadow: 0 0 0 1px #00a89d;
}

/* Empty State / Placeholder for Tab 3 & 4 */
.tab-placeholder-box {
  background: #f8fafc;
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  padding: 48px 24px;
  text-align: center;
  color: #64748b;
  margin: 16px 0;
}
.tab-placeholder-box .icon {
  font-size: 38px;
  margin-bottom: 12px;
  display: block;
  opacity: 0.7;
}
.tab-placeholder-box h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--navy, #0f172a);
  margin-bottom: 6px;
}
.tab-placeholder-box p {
  font-size: 13px;
  max-width: 480px;
  margin: 0 auto;
  line-height: 1.5;
}

/* Bottom Actions */
.profile-footer-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
  margin-top: 24px;
}
</style>

<!-- Main Content Area -->
<main>
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">RISK REGISTER · FORMULIR PROFILE RISIKO</p>
      <h1>
        <?php if (!empty($form_mode) && $form_mode === 'edit'): ?>
          Edit Formulir Profile Risiko
        <?php elseif (!empty($form_mode) && $form_mode === 'view'): ?>
          Detail Formulir Profile Risiko
        <?php else: ?>
          Formulir Profile Risiko
        <?php endif; ?>
      </h1>
      <p class="subtitle">Formulir penyusunan profil risiko komprehensif terintegrasi IFPRO Command Center.</p>
    </div>
    <div class="header-actions">
      <?php if (!empty($form_mode) && ($form_mode === 'edit' || $form_mode === 'view')): ?>
        <a href="<?= site_url('menrisk/daftar_profil_risiko') ?>" class="ghost" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
          <span>←</span> Kembali ke Daftar Profile Risiko
        </a>
      <?php else: ?>
        <a href="<?= site_url('menrisk') ?>#register" class="ghost" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
          <span>←</span> Kembali ke Risk Register
        </a>
      <?php endif; ?>
      <?php if (!empty($form_mode) && $form_mode === 'view' && !empty($formulir_detail->id)): ?>
        <a href="<?= site_url('menrisk/profil_risiko/' . (int)$formulir_detail->id) ?>" class="primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
          <span>✎</span> Edit Profile Risiko
        </a>
      <?php else: ?>
        <button type="button" class="primary" onclick="submitProfilRisikoForm()" id="btnTopSave">
          Simpan Profile Risiko
        </button>
      <?php endif; ?>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <form id="formProfilRisiko" onsubmit="submitProfilRisikoForm(event)">
    <input type="hidden" id="formulir_id" name="formulir_id" value="<?= !empty($formulir_detail->id) ? (int)$formulir_detail->id : '' ?>" />

    <!-- SECTION ATAS: SASARAN/TARGET & FUNGSI KERJA -->
    <div class="form-card">
      <div class="form-card-title">
        <span>🎯</span> Informasi Sasaran & Fungsi Kerja
      </div>
      <p class="form-card-desc">Tentukan sasaran/target yang terancam serta unit/fungsi kerja yang bertanggung jawab mengelola profil risiko ini.</p>

      <div class="form-grid-2">
        <!-- 1. Sasaran / Target -->
        <div class="profile-field-group">
          <label for="sasaran">1. Sasaran / Target <span style="color: var(--red);">*</span></label>
          <input type="text" id="sasaran" name="sasaran" required placeholder="Contoh: Menjaga keandalan layanan penyeberangan dan zero accident armada kapal" />
          <span class="helper-text">Sasaran kinerja, KPI, atau target strategis yang ingin dicapai.</span>
        </div>

        <!-- 2. Fungsi Kerja -->
        <div class="profile-field-group">
          <label for="fungsi_kerja">2. Fungsi Kerja <span style="color: var(--red);">*</span></label>
          <input type="text" id="fungsi_kerja" name="fungsi_kerja" required placeholder="Contoh: Divisi Operasi & Pemeliharaan Armada Kapal" />
          <span class="helper-text">Divisi, fungsi operasional, atau direktorat pelaksana.</span>
        </div>
      </div>
    </div>

    <!-- SECTION TAB: 4 TINGKATAN PROFIL RISIKO -->
    <div class="form-card" style="padding: 24px;">
      <!-- Tab Navigation -->
      <div class="profile-tabs-bar">
        <button type="button" class="profile-tab-btn active" data-tab="tab1" onclick="switchProfileTab('tab1')">
          <span class="tab-num">1</span>
          <span>Identifikasi Risiko</span>
        </button>
        <button type="button" class="profile-tab-btn" data-tab="tab2" onclick="switchProfileTab('tab2')">
          <span class="tab-num">2</span>
          <span>Kuantifikasi Risiko Inheren</span>
        </button>
        <button type="button" class="profile-tab-btn" data-tab="tab3" onclick="switchProfileTab('tab3')">
          <span class="tab-num">3</span>
          <span>Rencana Perlakuan Risiko</span>
        </button>
        <button type="button" class="profile-tab-btn" data-tab="tab4" onclick="switchProfileTab('tab4')">
          <span class="tab-num">4</span>
          <span>Kuantifikasi Risiko Residual</span>
        </button>
      </div>

      <!-- =================================================================== -->
      <!-- TAB 1: IDENTIFIKASI RISIKO                                          -->
      <!-- =================================================================== -->
      <div class="profile-tab-content" id="pane-tab1">
        <div style="margin-bottom: 20px;">
          <h3 style="font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 4px;">Tahap 1: Identifikasi Risiko</h3>
          <p style="font-size: 13px; color: var(--muted); margin: 0;">Lengkapi atribut identifikasi risiko, taksonomi kategori perusahaan, sumber pemicu, dan evaluasi efektivitas kontrol saat ini.</p>
        </div>

        <div class="form-grid-full">
          <!-- 1. Nama Risiko -->
          <div class="profile-field-group">
            <label for="nama_risiko">1. Nama Risiko <span style="color: var(--red);">*</span></label>
            <input type="text" id="nama_risiko" name="nama_risiko" required placeholder="Contoh: Risiko Kegagalan Sistem Kendali & Mesin Kapal Penyeberangan" />
            <span class="helper-text">Pernyataan singkat mengenai risiko yang dapat mengganggu pencapaian sasaran.</span>
          </div>

          <!-- 2 Kolom: Kategori Risiko & Periode Terpapar -->
          <div class="form-grid-2">
            <!-- 2. Kategori Risiko (MNRSK_KategoriRisiko_kat_perusahaan) -->
            <div class="profile-field-group">
              <label for="kategori_risiko">2. Kategori Risiko <span style="color: var(--red);">*</span></label>
              <select id="kategori_risiko" name="kategori_risiko" required>
                <option value="">-- Pilih Kategori Risiko Perusahaan --</option>
                <?php if (!empty($kategori_perusahaan)): ?>
                  <?php foreach ($kategori_perusahaan as $kat): ?>
                    <option value="<?= htmlspecialchars($kat->code) ?>">
                      [<?= htmlspecialchars($kat->code) ?>] <?= htmlspecialchars($kat->nama) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <span class="helper-text">Klasifikasi kategori risiko perusahaan (T3).</span>
            </div>

            <!-- 10. Perkiraan Waktu Terpapar Risiko -->
            <div class="profile-field-group">
              <label for="periode_terpapar">10. Perkiraan Waktu Terpapar Risiko</label>
              <input type="text" id="periode_terpapar" name="periode_terpapar" placeholder="Contoh: April 2026 s/d Desember 2026" />
              <span class="helper-text">Rentang periode atau batas waktu perkiraan risiko dapat terealisasi.</span>
            </div>
          </div>

          <!-- 3. Peristiwa Risiko -->
          <div class="profile-field-group">
            <label for="peristiwa_risiko">3. Peristiwa Risiko</label>
            <textarea id="peristiwa_risiko" name="peristiwa_risiko" rows="2" placeholder="Uraikan peristiwa, kondisi, atau insiden nyata yang terjadi saat risiko terealisasi..."></textarea>
            <span class="helper-text">Kejadian atau peristiwa spesifik yang terjadi saat risiko muncul.</span>
          </div>

          <!-- 4. Penyebab Risiko -->
          <div class="profile-field-group">
            <label for="penyebab_risiko">4. Penyebab Risiko</label>
            <textarea id="penyebab_risiko" name="penyebab_risiko" rows="2" placeholder="Uraikan faktor penyebab, pemicu utama, atau akar permasalahan timbulnya risiko..."></textarea>
            <span class="helper-text">Akar pemicu (internal/eksternal) yang memicu timbulnya peristiwa risiko.</span>
          </div>

          <!-- 5. Dampak Risiko Kualitatif Inheren -->
          <div class="profile-field-group">
            <label for="dampak_kualitatif">5. Dampak Risiko Kualitatif Inheren</label>
            <textarea id="dampak_kualitatif" name="dampak_kualitatif" rows="2" placeholder="Uraikan konsekuensi kualitatif (operasional, keselamatan, reputasi, atau legalitas) jika risiko terjadi sebelum mitigasi..."></textarea>
            <span class="helper-text">Konsekuensi kualitatif terhadap operasional, reputasi, legal, atau keselamatan sebelum perlakuan tambahan.</span>
          </div>

          <!-- 2 Kolom: Sumber Risiko & Faktor Sumber (Cascading Dropdown) -->
          <div class="form-grid-2">
            <!-- 6. Sumber Risiko (MNRSK_SumberRisiko) -->
            <div class="profile-field-group">
              <label for="sumber_risiko">6. Sumber Risiko <span style="color: var(--red);">*</span></label>
              <select id="sumber_risiko" name="sumber_risiko" onchange="onSumberRisikoChange(this.value)" required>
                <option value="">-- Pilih Sumber Risiko --</option>
                <?php if (!empty($sumber_risiko)): ?>
                  <?php foreach ($sumber_risiko as $sumber): ?>
                    <option value="<?= (int)$sumber->id ?>">
                      <?= htmlspecialchars($sumber->nama) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <span class="helper-text">Asal muasal timbulnya risiko (Internal atau Eksternal).</span>
            </div>

            <!-- 7. Faktor Sumber (MNRSK_FaktorRisiko - Dependent Dropdown) -->
            <div class="profile-field-group">
              <label for="faktor_sumber">7. Faktor Sumber <span style="color: var(--red);">*</span></label>
              <select id="faktor_sumber" name="faktor_sumber" disabled required>
                <option value="">-- Pilih Sumber Risiko Terlebih Dahulu --</option>
              </select>
              <span class="helper-text">Faktor spesifik berdasarkan sumber risiko yang dipilih.</span>
            </div>
          </div>

          <!-- 8. Kontrol Saat Ini -->
          <div class="profile-field-group">
            <label for="kontrol_saat_ini">8. Kontrol Saat Ini</label>
            <textarea id="kontrol_saat_ini" name="kontrol_saat_ini" rows="2" placeholder="Uraikan kontrol internal, SOP, inspeksi berkala, atau sistem proteksi yang telah berjalan saat ini..."></textarea>
            <span class="helper-text">Prosedur, sistem kontrol eksisting, atau mekanisme mitigasi yang sudah diimplementasikan.</span>
          </div>

          <!-- 9. Penilaian Efektifitas Kontrol -->
          <div class="profile-field-group">
            <label for="efektifitas_kontrol">9. Penilaian Efektifitas Kontrol <span style="color: var(--red);">*</span></label>
            <select id="efektifitas_kontrol" name="efektifitas_kontrol" required>
              <option value="">-- Pilih Penilaian Efektifitas Kontrol --</option>
              <option value="Cukup dan Efektif">Cukup dan Efektif</option>
              <option value="Cukup dan Efektif Sebagian">Cukup dan Efektif Sebagian</option>
              <option value="Cukup dan Tidak Efektif">Cukup dan Tidak Efektif</option>
              <option value="Tidak Cukup dan Efektif Sebagian">Tidak Cukup dan Efektif Sebagian</option>
              <option value="Tidak Cukup dan Tidak Efektif">Tidak Cukup dan Tidak Efektif</option>
            </select>
            <span class="helper-text">Evaluasi kecukupan dan efektivitas desain serta implementasi kontrol eksisting.</span>
          </div>
        </div>

        <!-- Footer Actions Tab 1 -->
        <div class="profile-footer-actions">
          <a href="<?= site_url('menrisk') ?>#register" class="ghost" style="text-decoration: none;">
            Batal & Kembali
          </a>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="primary" onclick="switchProfileTab('tab2')">
              Lanjut ke Kuantifikasi Inheren →
            </button>
          </div>
        </div>
      </div>

      <!-- =================================================================== -->
      <!-- TAB 2: KUANTIFIKASI RISIKO INHEREN                                  -->
      <!-- =================================================================== -->
      <div class="profile-tab-content d-none" id="pane-tab2">
        <div style="margin-bottom: 20px;">
          <h3 style="font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 4px;">Tahap 2: Kuantifikasi Risiko Inheren</h3>
          <p style="font-size: 13px; color: var(--muted); margin: 0;">Pengukuran nilai eksposure kuantitatif, pemilihan kategori dampak kualitatif, skala dampak, skala probabilitas, dan penetapan skala risiko inheren.</p>
        </div>

        <div class="form-grid-full">
          <!-- 1. EKSPOSURE DAMPAK RISIKO KUANTITATIF INHEREN (dalam rupiah) -->
          <div class="profile-field-group">
            <label for="eksposure_kuantitatif">
              1. Eksposure Dampak Risiko Kuantitatif Inheren (dalam Rupiah)
            </label>
            <div style="position: relative; display: flex; align-items: center;">
              <span style="position: absolute; left: 14px; font-weight: 700; color: #64748b; font-size: 13px; pointer-events: none;">Rp</span>
              <input type="text" id="eksposure_kuantitatif" name="eksposure_kuantitatif" 
                     placeholder="Contoh: 500.000.000 atau 1.250.000.000" 
                     oninput="formatRupiahInput(this)"
                     style="padding-left: 42px; font-weight: 600; font-size: 13.5px;" />
            </div>
            <span class="helper-text">Estimasi eksposure nilai kerugian kuantitatif dalam mata uang Rupiah. Masukkan angka bebas, sistem otomatis memformat tanda titik ribuan.</span>
          </div>

          <!-- 2. Kategori Dampak (MNRSK_SkalaDampakKualitatif) -->
          <div class="profile-field-group">
            <label for="kategori_dampak">
              2. Kategori Dampak (Skala Dampak Kualitatif)
            </label>
            <select id="kategori_dampak" name="kategori_dampak" onchange="onKategoriDampakChange(this.value)">
              <option value="">-- Pilih Kategori Dampak --</option>
              <?php if (!empty($skala_kualitatif)): ?>
                <?php foreach ($skala_kualitatif as $item): ?>
                  <option value="<?= $item->id ?>">
                    <?= htmlspecialchars($item->risiko) ?> (<?= htmlspecialchars($item->judul) ?>)
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <span class="helper-text">Pilih kategori dampak kualitatif. Setelah dipilih, isi/kriteria skala dampak 1 s/d 5 pada pilihan di bawah akan otomatis terisi dan diperbarui sesuai kategori yang dipilih.</span>
          </div>

          <!-- 3. Skala Dampak Inheren (Otomatis berdasarkan Kategori Dampak terpilih) -->
          <div class="profile-field-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; flex-wrap: wrap; gap: 6px;">
              <label style="margin-bottom: 0;">3. Skala Dampak Inheren (Skala 1 s/d 5)</label>
              <span id="lblSelectedKualitatifJudul" style="font-size: 11.5px; color: var(--teal); font-weight: 700;"></span>
            </div>
            <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat skala dampak inheren berdasarkan kriteria dampak kualitatif dari kategori yang dipilih:</span>
            
            <div class="radio-scale-grid" id="gridSkalaDampak">
              <?php
                $defaultLabels = [
                  1 => 'Sangat Rendah',
                  2 => 'Rendah',
                  3 => 'Moderat',
                  4 => 'Tinggi',
                  5 => 'Sangat Tinggi'
                ];
                for ($lvl = 1; $lvl <= 5; $lvl++):
              ?>
                <label class="scale-card-option" id="card-dampak-<?= $lvl ?>">
                  <input type="radio" name="skala_dampak_inheren" value="<?= $lvl ?>" onchange="onSkalaDampakChange(this.value)" />
                  <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                      <span class="badge-skala badge-skala-<?= $lvl ?>"><?= $lvl ?></span>
                      <strong style="font-size: 13px; color: var(--navy);" id="lbl-title-dampak-<?= $lvl ?>">
                        Skala <?= $lvl ?> · <?= $defaultLabels[$lvl] ?>
                      </strong>
                    </div>
                    <p style="font-size: 12px; color: #475569; margin: 0; line-height: 1.45;" id="desc-dampak-<?= $lvl ?>">
                      Silakan pilih Kategori Dampak di atas untuk memuat kriteria Skala <?= $lvl ?>.
                    </p>
                  </div>
                </label>
              <?php endfor; ?>
            </div>
          </div>

          <!-- 4. Skala Probabilitas (MNRSK_SkalaProbabilitas) -->
          <div class="profile-field-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <label style="margin-bottom: 0;">4. Skala Probabilitas</label>
              <?php if (!empty($skala_probabilitas[0]->parameter)): ?>
                <span style="font-size: 11.5px; color: var(--muted); font-style: italic;">
                  Parameter Acuan: <?= htmlspecialchars($skala_probabilitas[0]->parameter) ?>
                </span>
              <?php endif; ?>
            </div>
            <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat kemungkinan/frekuensi keterjadian risiko:</span>

            <div class="radio-scale-grid" id="gridSkalaProbabilitas">
              <?php
                $probRef = !empty($skala_probabilitas) ? $skala_probabilitas[0] : null;
                $probLevels = [
                  1 => ['label' => 'Sangat Jarang Terjadi', 'desc' => $probRef ? $probRef->skala_1 : 'Paling banyak satu kali dalam setahun.'],
                  2 => ['label' => 'Jarang Terjadi', 'desc' => $probRef ? $probRef->skala_2 : 'Hanya sekali dalam 6 bulan.'],
                  3 => ['label' => 'Bisa Terjadi', 'desc' => $probRef ? $probRef->skala_3 : 'Pernah terjadi, sekali dalam 4 bulan.'],
                  4 => ['label' => 'Sangat Mungkin Terjadi', 'desc' => $probRef ? $probRef->skala_4 : 'Pernah terjadi sekali dalam 2 bulan.'],
                  5 => ['label' => 'Hampir Pasti Terjadi', 'desc' => $probRef ? $probRef->skala_5 : 'Pernah terjadi sekali dalam 1 bulan.']
                ];
                foreach ($probLevels as $lvl => $info):
              ?>
                <label class="scale-card-option" id="card-prob-<?= $lvl ?>">
                  <input type="radio" name="skala_probabilitas" value="<?= $lvl ?>" onchange="onSkalaProbabilitasChange(this.value)" />
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

          <!-- 5. Skala Risiko Inheren (Nilai 1 - 25, Slider seperti Progres Mitigasi) -->
          <div class="profile-field-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
              <label for="skala_risiko_inheren" style="margin-bottom: 0;">
                5. Skala Risiko Inheren (Nilai 1 - 25) <span style="color: var(--red);">*</span>
              </label>
              <!-- Dynamic Level Badge -->
              <div id="badgeRiskLevel" style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; border-radius: 20px; font-weight: 800; font-size: 13px; background: #00b04f; color: #ffffff; transition: all .2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <span id="lblScoreInheren">Nilai: 1</span>
                <span>·</span>
                <span id="lblLevelInheren">Low</span>
              </div>
            </div>

            <!-- Slider Bar (mirip slider progres mitigasi) -->
            <div style="display: flex; align-items: center; gap: 14px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 16px;">
              <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 14px;">1</span>
              <input type="range" id="skala_risiko_inheren" name="skala_risiko_inheren" min="1" max="25" value="1" 
                     oninput="updateInherenLevel(this.value)"
                     style="width: 100%; padding: 0; border: 0; background: transparent; accent-color: #00b04f; cursor: pointer;" />
              <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 20px;">25</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
              <span class="helper-text">
                Geser slider (1 s/d 25) untuk menentukan tingkat risiko, atau otomatis terhitung saat memilih <b>Skala Dampak</b> dan <b>Skala Probabilitas</b> di atas (Dampak × Probabilitas).
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
        </div>

        <!-- Footer Actions Tab 2 -->
        <div class="profile-footer-actions">
          <button type="button" class="ghost" onclick="switchProfileTab('tab1')">
            ← Kembali ke Identifikasi Risiko
          </button>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="ghost" onclick="switchProfileTab('tab3')">
              Lanjut ke Rencana Perlakuan →
            </button>
            <button type="submit" class="primary">
              Simpan Profile Risiko
            </button>
          </div>
        </div>
      </div>

      <!-- =================================================================== -->
      <!-- TAB 3: RENCANA PERLAKUAN RISIKO                                     -->
      <!-- =================================================================== -->
      <div class="profile-tab-content d-none" id="pane-tab3">
        <div style="margin-bottom: 20px;">
          <h3 style="font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 4px;">Tahap 3: Rencana Perlakuan Risiko</h3>
          <p style="font-size: 13px; color: var(--muted); margin: 0;">Perumusan strategi operasi perlakuan risiko, rincian tindakan mitigasi, target waktu, estimasi biaya, keterkaitan program RKAP, dan unit penanggung jawab (PIC).</p>
        </div>

        <div class="form-grid-full">
          <!-- 1. Operasi Perlakuan Risiko -->
          <div class="profile-field-group">
            <label for="operasi_perlakuan">1. Operasi Perlakuan Risiko</label>
            <select id="operasi_perlakuan" name="operasi_perlakuan">
              <option value="">-- Pilih Operasi Perlakuan Risiko --</option>
              <option value="Accept/Monitor">Accept/Monitor</option>
              <option value="Reduce/Mitigate">Reduce/Mitigate</option>
              <option value="Transfer/Sharing">Transfer/Sharing</option>
              <option value="Hindari/Avoid">Hindari/Avoid</option>
            </select>
            <span class="helper-text">Pilih opsi respon atau strategi operasi perlakuan risiko yang akan diterapkan.</span>
          </div>

          <!-- 2. Jenis Rencana Perlakuan Risiko -->
          <div class="profile-field-group">
            <label for="jenis_rencana_perlakuan">2. Jenis Rencana Perlakuan Risiko</label>
            <select id="jenis_rencana_perlakuan" name="jenis_rencana_perlakuan">
              <option value="">-- Pilih Jenis Rencana Perlakuan Risiko --</option>
              <option value="Peningkatan Kecukupan Desain Control">Peningkatan Kecukupan Desain Control</option>
              <option value="Peningkatan Efektivitas Pelaksanaan Control">Peningkatan Efektivitas Pelaksanaan Control</option>
              <option value="Perbaikan Melalui Breakthrough Project">Perbaikan Melalui Breakthrough Project</option>
              <option value="Lainnya">Lainnya</option>
            </select>
            <span class="helper-text">Karakteristik atau pendekatan rencana aksi perlakuan risiko.</span>
          </div>

          <!-- 3. Kegiatan Rencana Perlakuan Risiko (Mitigasi) -->
          <div class="profile-field-group">
            <label for="kegiatan_mitigasi">3. Kegiatan Rencana Perlakuan Risiko (Mitigasi)</label>
            <textarea id="kegiatan_mitigasi" name="kegiatan_mitigasi" rows="3" placeholder="Uraikan uraian langkah-langkah, tindakan konkret, atau kegiatan mitigasi risiko yang akan dilaksanakan..."></textarea>
            <span class="helper-text">Rincian aksi konkret mitigasi untuk menurunkan kemungkinan atau dampak risiko.</span>
          </div>

          <!-- 4. Output Perlakuan Risiko -->
          <div class="profile-field-group">
            <label for="output_perlakuan">4. Output Perlakuan Risiko</label>
            <textarea id="output_perlakuan" name="output_perlakuan" rows="2" placeholder="Uraikan target output, bukti deliverable, dokumen, atau hasil yang diharapkan dari pelaksanaan perlakuan..."></textarea>
            <span class="helper-text">Keluaran terukur yang dihasilkan setelah perlakuan risiko terlaksana.</span>
          </div>

          <!-- 5. Target Waktu Pelaksanaan Rencana Tindak Lanjut (Mulai & Selesai) -->
          <div class="profile-field-group">
            <label for="target_waktu">5. Target Waktu Pelaksanaan Rencana Tindak Lanjut (Mulai & Selesai)</label>
            <textarea id="target_waktu" name="target_waktu" rows="2" placeholder="Contoh: Mulai: Januari 2027 s/d Selesai: Juni 2027 (Jadwal tahapan pelaksanaan tindak lanjut)..."></textarea>
            <span class="helper-text">Jadwal atau target waktu dimulainya dan diselesaikannya rencana tindak lanjut mitigasi.</span>
          </div>

          <!-- 6. Biaya Perlakuan Risiko -->
          <div class="profile-field-group">
            <label for="biaya_perlakuan">6. Biaya Perlakuan Risiko</label>
            <textarea id="biaya_perlakuan" name="biaya_perlakuan" rows="2" placeholder="Uraikan estimasi anggaran, rincian biaya yang dibutuhkan, atau sumber pembiayaan untuk perlakuan risiko..."></textarea>
            <span class="helper-text">Estimasi alokasi anggaran atau rincian biaya yang diperlukan dalam menjalankan perlakuan risiko.</span>
          </div>

          <!-- 2 Kolom: Jenis Program Dalam RKAP & PIC -->
          <div class="form-grid-2">
            <!-- 7. Jenis Program Dalam RKAP -->
            <div class="profile-field-group">
              <label for="program_rkap">7. Jenis Program Dalam RKAP</label>
              <input type="text" id="program_rkap" name="program_rkap" placeholder="Contoh: Program Investasi Strategis / Program OPEX Rutin RKAP" />
              <span class="helper-text">Keterkaitan program kerja ke dalam Rencana Kerja dan Anggaran Perusahaan (RKAP).</span>
            </div>

            <!-- 8. PIC -->
            <div class="profile-field-group">
              <label for="pic">8. PIC (Person in Charge / Unit Penanggung Jawab)</label>
              <input type="text" id="pic" name="pic" placeholder="Contoh: Manager Operasi Pelayaran / Tim Keandalan Armada" />
              <span class="helper-text">Unit kerja, divisi, atau pejabat penanggung jawab eksekusi rencana aksi.</span>
            </div>
          </div>
        </div>

        <!-- Footer Actions Tab 3 -->
        <div class="profile-footer-actions">
          <button type="button" class="ghost" onclick="switchProfileTab('tab2')">
            ← Kembali ke Kuantifikasi Inheren
          </button>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="ghost" onclick="switchProfileTab('tab4')">
              Lanjut ke Kuantifikasi Residual →
            </button>
            <button type="submit" class="primary">
              Simpan Profile Risiko
            </button>
          </div>
        </div>
      </div>

      <!-- =================================================================== -->
      <!-- TAB 4: KUANTIFIKASI RISIKO RESIDUAL                                 -->
      <!-- =================================================================== -->
      <div class="profile-tab-content d-none" id="pane-tab4">
        <div style="margin-bottom: 20px;">
          <h3 style="font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 4px;">Tahap 4: Kuantifikasi Risiko Residual</h3>
          <p style="font-size: 13px; color: var(--muted); margin: 0;">Pengukuran nilai eksposur kuantitatif residual, penetapan skala dampak residual, skala kemungkinan residual, dan penentuan skala tingkat risiko residual pasca perlakuan.</p>
        </div>

        <div class="form-grid-full">
          <!-- 1. Eksposur Dampak Risiko Kuantitatif Residual (dalam rupiah) -->
          <div class="profile-field-group">
            <label for="eksposure_residual">
              1. Eksposur Dampak Risiko Kuantitatif Residual (dalam Rupiah)
            </label>
            <div style="position: relative; display: flex; align-items: center;">
              <span style="position: absolute; left: 14px; font-weight: 700; color: #64748b; font-size: 13px; pointer-events: none;">Rp</span>
              <input type="text" id="eksposure_residual" name="eksposure_residual" 
                     placeholder="Contoh: 150.000.000 atau 300.000.000" 
                     oninput="formatRupiahInput(this)"
                     style="padding-left: 42px; font-weight: 600; font-size: 13.5px;" />
            </div>
            <span class="helper-text">Estimasi sisa eksposur nilai kerugian kuantitatif dalam mata uang Rupiah setelah rencana mitigasi/perlakuan dijalankan. Sistem otomatis memformat tanda titik ribuan.</span>
          </div>

          <!-- 2. Skala Dampak Residual (Pemilihan Skala 1 - 5) -->
          <div class="profile-field-group">
            <label>2. Skala Dampak Residual (Skala 1 s/d 5)</label>
            <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat skala dampak residual pasca perlakuan risiko:</span>

            <div class="radio-scale-grid" id="gridSkalaDampakResidual">
              <?php
                $probRef = !empty($skala_probabilitas) ? $skala_probabilitas[0] : null;
                $dampakResidualLevels = [
                  1 => ['label' => 'Sangat Jarang', 'desc' => $probRef ? $probRef->skala_1 : 'Paling banyak satu kali dalam setahun.'],
                  2 => ['label' => 'Jarang',        'desc' => $probRef ? $probRef->skala_2 : 'Hanya sekali dalam 6 bulan.'],
                  3 => ['label' => 'Kadang-kadang', 'desc' => $probRef ? $probRef->skala_3 : 'Pernah terjadi, sekali dalam 4 bulan.'],
                  4 => ['label' => 'Sering',        'desc' => $probRef ? $probRef->skala_4 : 'Pernah terjadi sekali dalam 2 bulan.'],
                  5 => ['label' => 'Sangat Sering', 'desc' => $probRef ? $probRef->skala_5 : 'Pernah terjadi sekali dalam 1 bulan.']
                ];
                foreach ($dampakResidualLevels as $lvl => $info):
              ?>
                <label class="scale-card-option" id="card-dampak-res-<?= $lvl ?>">
                  <input type="radio" name="skala_dampak_residual" value="<?= $lvl ?>" onchange="onSkalaDampakResidualChange(this.value)" />
                  <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                      <span class="badge-skala badge-skala-<?= $lvl ?>"><?= $lvl ?></span>
                      <strong style="font-size: 13px; color: var(--navy);">Skala <?= $lvl ?> · <?= $info['label'] ?></strong>
                    </div>
                    <p style="font-size: 12px; color: #475569; margin: 0; line-height: 1.45;">
                      <?= htmlspecialchars($info['desc']) ?>
                    </p>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- 3. Skala Kemungkinan Residual (Pemilihan Skala 1 - 5 mengacu kepada MNRSK_SkalaProbabilitas) -->
          <div class="profile-field-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <label style="margin-bottom: 0;">3. Skala Kemungkinan Residual (Skala 1 s/d 5)</label>
              <?php if (!empty($skala_probabilitas[0]->parameter)): ?>
                <span style="font-size: 11.5px; color: var(--muted); font-style: italic;">
                  Parameter Acuan: <?= htmlspecialchars($skala_probabilitas[0]->parameter) ?>
                </span>
              <?php endif; ?>
            </div>
            <span class="helper-text" style="margin-bottom: 6px;">Pilih tingkat skala kemungkinan/probabilitas residual keterjadian risiko setelah mitigasi (mengacu ke tabel parameter probabilitas):</span>

            <div class="radio-scale-grid" id="gridSkalaProbabilitasResidual">
              <?php
                $probRef = !empty($skala_probabilitas) ? $skala_probabilitas[0] : null;
                $probLevelsRes = [
                  1 => ['label' => 'Sangat Jarang', 'desc' => $probRef ? $probRef->skala_1 : 'Paling banyak satu kali dalam setahun.'],
                  2 => ['label' => 'Jarang',        'desc' => $probRef ? $probRef->skala_2 : 'Hanya sekali dalam 6 bulan.'],
                  3 => ['label' => 'Kadang-kadang', 'desc' => $probRef ? $probRef->skala_3 : 'Pernah terjadi, sekali dalam 4 bulan.'],
                  4 => ['label' => 'Sering',        'desc' => $probRef ? $probRef->skala_4 : 'Pernah terjadi sekali dalam 2 bulan.'],
                  5 => ['label' => 'Sangat Sering', 'desc' => $probRef ? $probRef->skala_5 : 'Pernah terjadi sekali dalam 1 bulan.']
                ];
                foreach ($probLevelsRes as $lvl => $info):
              ?>
                <label class="scale-card-option" id="card-prob-res-<?= $lvl ?>">
                  <input type="radio" name="skala_kemungkinan_residual" value="<?= $lvl ?>" onchange="onSkalaProbabilitasResidualChange(this.value)" />
                  <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                      <span class="badge-skala badge-skala-<?= $lvl ?>"><?= $lvl ?></span>
                      <strong style="font-size: 13px; color: var(--navy);">Skala <?= $lvl ?> · <?= $info['label'] ?></strong>
                    </div>
                    <p style="font-size: 12px; color: #475569; margin: 0; line-height: 1.45;">
                      <?= htmlspecialchars($info['desc']) ?>
                    </p>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- 4. Skala Risiko Residual (Nilai 1 - 25, Slider seperti Skala Risiko Inheren) -->
          <div class="profile-field-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
              <label for="skala_risiko_residual" style="margin-bottom: 0;">
                4. Skala Risiko Residual (Nilai 1 - 25) <span style="color: var(--red);">*</span>
              </label>
              <!-- Dynamic Level Badge for Residual -->
              <div id="badgeRiskLevelResidual" style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; border-radius: 20px; font-weight: 800; font-size: 13px; background: #00b04f; color: #ffffff; transition: all .2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <span id="lblScoreResidual">Nilai: 1</span>
                <span>·</span>
                <span id="lblLevelResidual">Low</span>
              </div>
            </div>

            <!-- Slider Bar (mirip skala risiko inheren) -->
            <div style="display: flex; align-items: center; gap: 14px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 16px;">
              <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 14px;">1</span>
              <input type="range" id="skala_risiko_residual" name="skala_risiko_residual" min="1" max="25" value="1" 
                     oninput="updateResidualLevel(this.value)"
                     style="width: 100%; padding: 0; border: 0; background: transparent; accent-color: #00b04f; cursor: pointer;" />
              <span style="font-size: 12px; font-weight: 800; color: var(--muted); min-width: 20px;">25</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
              <span class="helper-text">
                Geser slider (1 s/d 25) untuk menentukan tingkat risiko residual, atau otomatis terhitung saat memilih <b>Skala Dampak Residual</b> dan <b>Skala Kemungkinan Residual</b> di atas (Dampak × Kemungkinan).
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
        </div>

        <!-- Footer Actions Tab 4 -->
        <div class="profile-footer-actions">
          <button type="button" class="ghost" onclick="switchProfileTab('tab3')">
            ← Kembali ke Rencana Perlakuan
          </button>
          <button type="submit" class="primary">
            Simpan Profile Risiko
          </button>
        </div>
      </div>
    </div>
  </form>
</main>

<!-- JAVASCRIPT: TAB, DEPENDENT DROPDOWN & TAB 2 CALCULATION LOGIC -->
<script>
  // Cache data dari controller
  const faktorRisikoList    = <?= json_encode($faktor_risiko ?? []) ?>;
  const skalaKualitatifList = <?= json_encode($skala_kualitatif ?? []) ?>;
  const formulirDetail      = <?= json_encode($formulir_detail ?? null) ?>;
  const formMode            = <?= json_encode($form_mode ?? 'create') ?>;

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  /**
   * Berpindah antar Tab Profil Risiko
   */
  function switchProfileTab(tabId) {
    $('.profile-tab-btn').removeClass('active');
    $(`.profile-tab-btn[data-tab="${tabId}"]`).addClass('active');

    $('.profile-tab-content').addClass('d-none');
    $(`#pane-${tabId}`).removeClass('d-none');

    // Scroll halus ke awal tab card
    $('html, body').animate({
      scrollTop: $('.profile-tabs-bar').offset().top - 80
    }, 200);
  }

  /**
   * Filter Dependent Dropdown: Faktor Sumber berdasarkan Sumber Risiko (Tab 1)
   */
  function onSumberRisikoChange(sumberId) {
    const $faktorSelect = $('#faktor_sumber');
    $faktorSelect.empty().append('<option value="">-- Pilih Faktor Sumber --</option>');

    if (!sumberId) {
      $faktorSelect.prop('disabled', true);
      return;
    }

    const filtered = faktorRisikoList.filter(f => parseInt(f.sumber_risiko_id, 10) === parseInt(sumberId, 10));

    if (filtered.length > 0) {
      filtered.forEach(item => {
        $faktorSelect.append(`<option value="${item.id}">${escapeHtml(item.nama)}</option>`);
      });
      $faktorSelect.prop('disabled', false);
    } else {
      $faktorSelect.append('<option value="" disabled>Belum ada data faktor risiko untuk sumber ini</option>');
      $faktorSelect.prop('disabled', false);
    }
  }

  /**
   * Format Rupiah Input (Tab 2 Field 1)
   */
  function formatRupiahInput(el) {
    let raw = el.value.replace(/[^0-9]/g, '');
    if (raw) {
      el.value = new Intl.NumberFormat('id-ID').format(raw);
    } else {
      el.value = '';
    }
  }

  /**
   * Kategori Dampak Change: Perbarui teks & kriteria Skala Dampak Inheren (Skala 1 s/d 5)
   */
  function onKategoriDampakChange(kategoriId) {
    const $judul = $('#lblSelectedKualitatifJudul');

    const defaultLabels = {
      1: 'Sangat Rendah',
      2: 'Rendah',
      3: 'Moderat',
      4: 'Tinggi',
      5: 'Sangat Tinggi'
    };

    if (!kategoriId) {
      $judul.text('');
      for (let lvl = 1; lvl <= 5; lvl++) {
        $(`#lbl-title-dampak-${lvl}`).text(`Skala ${lvl} · ${defaultLabels[lvl]}`);
        $(`#desc-dampak-${lvl}`).text(`Silakan pilih Kategori Dampak di atas untuk memuat kriteria Skala ${lvl}.`);
      }
      return;
    }

    const selectedItem = skalaKualitatifList.find(x => parseInt(x.id, 10) === parseInt(kategoriId, 10));
    if (!selectedItem) {
      $judul.text('');
      return;
    }

    $judul.text(selectedItem.judul + (selectedItem.risiko ? ' · ' + selectedItem.risiko : ''));

    const scales = [
      { lvl: 1, label: 'Sangat Rendah', text: selectedItem.skala_1 },
      { lvl: 2, label: 'Rendah',        text: selectedItem.skala_2 },
      { lvl: 3, label: 'Moderat',        text: selectedItem.skala_3 },
      { lvl: 4, label: 'Tinggi',        text: selectedItem.skala_4 },
      { lvl: 5, label: 'Sangat Tinggi', text: selectedItem.skala_5 }
    ];

    scales.forEach(s => {
      $(`#lbl-title-dampak-${s.lvl}`).text(`Skala ${s.lvl} · ${s.label}`);
      $(`#desc-dampak-${s.lvl}`).text(s.text || '-');
    });
  }

  /**
   * Event Change: Skala Dampak Inheren (Tab 2 Field 3)
   */
  function onSkalaDampakChange(val) {
    $('.scale-card-option[id^="card-dampak-"]').removeClass('selected');
    $(`#card-dampak-${val}`).addClass('selected');
    recalculateInherenScore();
  }

  /**
   * Event Change: Skala Probabilitas (Tab 2 Field 4)
   */
  function onSkalaProbabilitasChange(val) {
    $('.scale-card-option[id^="card-prob-"]').removeClass('selected');
    $(`#card-prob-${val}`).addClass('selected');
    recalculateInherenScore();
  }

  /**
   * Hitung Otomatis Skor Risiko Inheren (Dampak × Probabilitas = 1 s/d 25)
   */
  function recalculateInherenScore() {
    const dampak = parseInt($('input[name="skala_dampak_inheren"]:checked').val(), 10);
    const prob   = parseInt($('input[name="skala_probabilitas"]:checked').val(), 10);

    if (dampak && prob) {
      const calcScore = dampak * prob; // Range 1 - 25
      $('#skala_risiko_inheren').val(calcScore);
      updateInherenLevel(calcScore);
    }
  }

  /**
   * Menentukan Label & Warna Level berdasarkan Nilai Skala Risiko Inheren (1 - 25)
   * Aturan:
   * 1 - 5   = Low (#00b04f)
   * 6 - 11  = Low to Moderate (#92d14f)
   * 12 - 15 = Moderate (#ffff00)
   * 16 - 20 = Moderate to Hight (#ffbf00)
   * 21 - 25 = Hight (#ff0000)
   */
  function getInherenLevelInfo(score) {
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

  function updateInherenLevel(val) {
    const score = parseInt(val, 10) || 1;
    const info  = getInherenLevelInfo(score);

    $('#lblScoreInheren').text('Nilai: ' + score);
    $('#lblLevelInheren').text(info.level);

    $('#badgeRiskLevel').css({
      'background-color': info.bg,
      'color': info.text
    });

    // Update accent color slider
    $('#skala_risiko_inheren').css('accent-color', info.bg);
  }

  /**
   * Submit / Simpan Formulir Profile Risiko
   */
  function submitProfilRisikoForm(e) {
    if (e && e.preventDefault) e.preventDefault();

    const sasaran     = $.trim($('#sasaran').val());
    const fungsiKerja = $.trim($('#fungsi_kerja').val());
    const namaRisiko  = $.trim($('#nama_risiko').val());
    const katRisiko   = $.trim($('#kategori_risiko').val());
    const sumber      = $.trim($('#sumber_risiko').val());
    const faktor      = $.trim($('#faktor_sumber').val());
    const efektifitas = $.trim($('#efektifitas_kontrol').val());

    // Validasi field pokok
    if (!sasaran) {
      showToast('Mohon isi kolom Sasaran / Target terlebih dahulu.');
      $('#sasaran').focus();
      return;
    }
    if (!fungsiKerja) {
      showToast('Mohon isi kolom Fungsi Kerja terlebih dahulu.');
      $('#fungsi_kerja').focus();
      return;
    }
    if (!namaRisiko) {
      switchProfileTab('tab1');
      showToast('Mohon isi kolom Nama Risiko.');
      $('#nama_risiko').focus();
      return;
    }
    if (!katRisiko) {
      switchProfileTab('tab1');
      showToast('Mohon pilih Kategori Risiko.');
      $('#kategori_risiko').focus();
      return;
    }
    if (!sumber) {
      switchProfileTab('tab1');
      showToast('Mohon pilih Sumber Risiko.');
      $('#sumber_risiko').focus();
      return;
    }
    if (!faktor) {
      switchProfileTab('tab1');
      showToast('Mohon pilih Faktor Sumber.');
      $('#faktor_sumber').focus();
      return;
    }
    if (!efektifitas) {
      switchProfileTab('tab1');
      showToast('Mohon pilih Penilaian Efektifitas Kontrol.');
      $('#efektifitas_kontrol').focus();
      return;
    }

    const $btnTop = $('#btnTopSave');
    const $btnBottom = $('#btnBottomSave');
    $btnTop.prop('disabled', true).text('Menyimpan...');
    $btnBottom.prop('disabled', true).text('Menyimpan...');

    const formData = new FormData($('#formProfilRisiko')[0]);

    $.ajax({
      url: '<?= site_url("menrisk/save_profil_risiko") ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Formulir Profile Risiko berhasil disimpan.');
          setTimeout(() => {
            window.location.href = res.redirect_url || '<?= site_url("menrisk/daftar_profil_risiko") ?>';
          }, 1200);
        } else {
          showToast(res.message || 'Gagal menyimpan Formulir Profile Risiko.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menyimpan profil risiko.');
      },
      complete: function() {
        $btnTop.prop('disabled', false).text('Simpan Profile Risiko');
        $btnBottom.prop('disabled', false).text('Simpan Profile Risiko');
      }
    });
  }

  /**
   * Event Change: Skala Dampak Residual (Tab 4 Field 2)
   */
  function onSkalaDampakResidualChange(val) {
    $('.scale-card-option[id^="card-dampak-res-"]').removeClass('selected');
    $(`#card-dampak-res-${val}`).addClass('selected');
    recalculateResidualScore();
  }

  /**
   * Event Change: Skala Kemungkinan Residual (Tab 4 Field 3)
   */
  function onSkalaProbabilitasResidualChange(val) {
    $('.scale-card-option[id^="card-prob-res-"]').removeClass('selected');
    $(`#card-prob-res-${val}`).addClass('selected');
    recalculateResidualScore();
  }

  /**
   * Hitung Otomatis Skor Risiko Residual (Dampak × Kemungkinan = 1 s/d 25)
   */
  function recalculateResidualScore() {
    const dampak = parseInt($('input[name="skala_dampak_residual"]:checked').val(), 10);
    const prob   = parseInt($('input[name="skala_kemungkinan_residual"]:checked').val(), 10);

    if (dampak && prob) {
      const calcScore = dampak * prob; // Range 1 - 25
      $('#skala_risiko_residual').val(calcScore);
      updateResidualLevel(calcScore);
    }
  }

  /**
   * Menentukan Label & Warna Level berdasarkan Nilai Skala Risiko Residual (1 - 25)
   */
  function updateResidualLevel(val) {
    const score = parseInt(val, 10) || 1;
    const info  = getInherenLevelInfo(score);

    $('#lblScoreResidual').text('Nilai: ' + score);
    $('#lblLevelResidual').text(info.level);

    $('#badgeRiskLevelResidual').css({
      'background-color': info.bg,
      'color': info.text
    });

    // Update accent color slider
    $('#skala_risiko_residual').css('accent-color', info.bg);
  }

  // Inisialisasi awal slider level & prefill data jika mode Edit / View
  $(function() {
    if (formulirDetail) {
      // Master
      $('#sasaran').val(formulirDetail.sasaran || '');
      $('#fungsi_kerja').val(formulirDetail.fungsi_kerja || '');

      // Tab 1: Identifikasi Risiko
      $('#nama_risiko').val(formulirDetail.nama_risiko || '');
      $('#kategori_risiko').val(formulirDetail.kategori_risiko || '');
      $('#peristiwa_risiko').val(formulirDetail.peristiwa_risiko || '');
      $('#penyebab_risiko').val(formulirDetail.penyebab_risiko || '');
      $('#dampak_kualitatif').val(formulirDetail.dampak_kualitatif || '');
      if (formulirDetail.sumber_risiko) {
        $('#sumber_risiko').val(formulirDetail.sumber_risiko);
        onSumberRisikoChange(formulirDetail.sumber_risiko);
        if (formulirDetail.faktor_sumber) {
          $('#faktor_sumber').val(formulirDetail.faktor_sumber);
        }
      }
      $('#kontrol_saat_ini').val(formulirDetail.kontrol_saat_ini || '');
      $('#efektifitas_kontrol').val(formulirDetail.efektifitas_kontrol || '');
      $('#periode_terpapar').val(formulirDetail.periode_terpapar || '');

      // Tab 2: Kuantifikasi Risiko Inheren
      $('#eksposure_kuantitatif').val(formulirDetail.eksposure_kuantitatif || '');
      if (formulirDetail.kategori_dampak) {
        $('#kategori_dampak').val(formulirDetail.kategori_dampak);
        onKategoriDampakChange(formulirDetail.kategori_dampak);
      }
      if (formulirDetail.skala_dampak_inheren) {
        const $rDampak = $(`input[name="skala_dampak_inheren"][value="${formulirDetail.skala_dampak_inheren}"]`);
        $rDampak.prop('checked', true);
        $('.scale-card-option[id^="card-dampak-"]').removeClass('selected');
        $(`#card-dampak-${formulirDetail.skala_dampak_inheren}`).addClass('selected');
      }
      if (formulirDetail.skala_probabilitas) {
        const $rProb = $(`input[name="skala_probabilitas"][value="${formulirDetail.skala_probabilitas}"]`);
        $rProb.prop('checked', true);
        $('.scale-card-option[id^="card-prob-"]').removeClass('selected');
        $(`#card-prob-${formulirDetail.skala_probabilitas}`).addClass('selected');
      }
      if (formulirDetail.skala_risiko_inheren) {
        $('#skala_risiko_inheren').val(formulirDetail.skala_risiko_inheren);
      }

      // Tab 3: Rencana Perlakuan Risiko
      $('#operasi_perlakuan').val(formulirDetail.operasi_perlakuan || '');
      $('#jenis_rencana_perlakuan').val(formulirDetail.jenis_rencana_perlakuan || '');
      $('#kegiatan_mitigasi').val(formulirDetail.kegiatan_mitigasi || '');
      $('#output_perlakuan').val(formulirDetail.output_perlakuan || '');
      $('#target_waktu').val(formulirDetail.target_waktu || '');
      $('#biaya_perlakuan').val(formulirDetail.biaya_perlakuan || '');
      $('#program_rkap').val(formulirDetail.program_rkap || '');
      $('#pic').val(formulirDetail.pic || '');

      // Tab 4: Kuantifikasi Risiko Residual
      $('#eksposure_residual').val(formulirDetail.eksposure_residual || '');
      if (formulirDetail.skala_dampak_residual) {
        const $rDampakRes = $(`input[name="skala_dampak_residual"][value="${formulirDetail.skala_dampak_residual}"]`);
        $rDampakRes.prop('checked', true);
        $('.scale-card-option[id^="card-dampak-res-"]').removeClass('selected');
        $(`#card-dampak-res-${formulirDetail.skala_dampak_residual}`).addClass('selected');
      }
      if (formulirDetail.skala_kemungkinan_residual) {
        const $rProbRes = $(`input[name="skala_kemungkinan_residual"][value="${formulirDetail.skala_kemungkinan_residual}"]`);
        $rProbRes.prop('checked', true);
        $('.scale-card-option[id^="card-prob-res-"]').removeClass('selected');
        $(`#card-prob-res-${formulirDetail.skala_kemungkinan_residual}`).addClass('selected');
      }
      if (formulirDetail.skala_risiko_residual) {
        $('#skala_risiko_residual').val(formulirDetail.skala_risiko_residual);
      }

      // Mode View Only: kunci seluruh input dan sembunyikan tombol submit di dalam form
      if (formMode === 'view') {
        $('#formProfilRisiko').find('input, select, textarea').prop('disabled', true);
        $('#formProfilRisiko').find('button[type="submit"]').hide();
        $('.kualitatif-scale-card').css('pointer-events', 'none');
      }
    }

    updateInherenLevel($('#skala_risiko_inheren').val() || 1);
    updateResidualLevel($('#skala_risiko_residual').val() || 1);
  });
</script>

<?php $this->load->view('templates/footer'); ?>
