<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * View: Form Tambah & Edit Key Risk Indicator (KRI)
 * IFPRO Risk Command Center
 */
$this->load->view('templates/header', ['title' => $title ?? 'Formulir KRI — IFPRO Risk Command Center']);
$this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'kri', 'is_dashboard' => false]);
$form_mode = $form_mode ?? 'create';
$is_edit   = ($form_mode === 'edit' && !empty($kri->id));
?>
<style>
.kri-form-card {
  background: #ffffff;
  border: 1px solid var(--line, #e2e8f0);
  border-radius: 12px;
  padding: 24px 28px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  margin-bottom: 24px;
}
.kri-form-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--navy, #0f172a);
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}
.kri-form-desc {
  font-size: 13px;
  color: var(--muted, #64748b);
  margin-bottom: 20px;
}

/* Field Groups */
.profile-field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 18px;
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

/* Threshold Card Grid */
.threshold-container {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 20px;
  margin-top: 10px;
}
.threshold-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 16px;
  margin-top: 12px;
}
.threshold-card {
  background: #ffffff;
  border-radius: 8px;
  padding: 14px;
  border: 2px solid transparent;
  transition: transform .15s ease, box-shadow .15s ease;
}
.threshold-card.aman {
  border-color: #00b04f;
  background: #f0fdf4;
}
.threshold-card.hati-hati {
  border-color: #eab308;
  background: #fefce8;
}
.threshold-card.bahaya {
  border-color: #ff0000;
  background: #fef2f2;
}
.threshold-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 8px;
}
.threshold-badge.aman {
  background-color: #00b04f;
  color: #ffffff;
}
.threshold-badge.hati-hati {
  background-color: #ffff00;
  color: #0f172a;
  border: 1px solid #eab308;
}
.threshold-badge.bahaya {
  background-color: #ff0000;
  color: #ffffff;
}

.kri-footer-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
  margin-top: 10px;
}
</style>

<main>
  <!-- TOP HEADER -->
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">MANAJEMEN RISIKO · KEY RISK INDICATOR (KRI)</p>
      <h1><?= $is_edit ? 'Edit Data KRI' : 'Tambah Data KRI' ?></h1>
      <p class="subtitle">Tetapkan indikator metrik peringatan dini (early warning) dan ambang batas toleransi (threshold).</p>
    </div>
    <div class="header-actions">
      <a href="<?= site_url('menrisk/daftar_kri') ?>" class="ghost" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; font-weight: 600;">
        <span>←</span> Kembali ke Daftar KRI
      </a>
      <button type="button" class="primary btn-submit-kri" onclick="submitKriForm()" id="btnTopSaveKri">
        <?= $is_edit ? 'Perbarui Data KRI' : 'Simpan Data KRI' ?>
      </button>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <form id="formKri" onsubmit="submitKriForm(event)">
    <input type="hidden" id="kri_id" name="kri_id" value="<?= !empty($kri->id) ? (int)$kri->id : '' ?>" />
    <input type="hidden" id="department_id" name="department_id" value="<?= !empty($kri->department_id) ? (int)$kri->department_id : '' ?>" />
    <input type="hidden" id="nama_risiko_hidden" name="nama_risiko" value="<?= htmlspecialchars($kri->nama_risiko ?? '') ?>" />

    <div class="kri-form-card">
      <div class="kri-form-title">
        <span>◫</span> Formulir Identifikasi Key Risk Indicator
      </div>
      <p class="kri-form-desc">Lengkapi data metrik pengukuran risiko dan tentukan polaritas serta nilai ambang batas toleransi.</p>

      <!-- 1. Nama Risiko (Select Option dari Profile Risiko) -->
      <div class="profile-field-group">
        <label for="formulir_id">1. Nama Risiko <span style="color: var(--red);">*</span></label>
        <select id="formulir_id" name="formulir_id" required onchange="onNamaRisikoChange(this)">
          <option value="">-- Pilih Nama Risiko dari Profile Risiko --</option>
          <?php if (!empty($profile_risiko_options)): ?>
            <?php foreach ($profile_risiko_options as $pr): ?>
              <?php
                $isSel = (!empty($kri->formulir_id) && (int)$kri->formulir_id === (int)$pr->formulir_id) 
                         || (!empty($kri->nama_risiko) && trim($kri->nama_risiko) === trim($pr->nama_risiko));
              ?>
              <option value="<?= (int)$pr->formulir_id ?>" 
                      data-nama="<?= htmlspecialchars($pr->nama_risiko) ?>"
                      data-penyebab="<?= htmlspecialchars($pr->penyebab_risiko ?? '') ?>"
                      data-dept="<?= (int)($pr->department_id ?? 0) ?>"
                      <?= $isSel ? 'selected' : '' ?>>
                <?= htmlspecialchars($pr->nama_risiko) ?> (<?= htmlspecialchars($pr->sasaran ?: ($pr->department_name ?: 'Profile #' . $pr->formulir_id)) ?>)
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
        <span class="helper-text">Memilih nama risiko hasil dari profile risiko yang telah dibuat.</span>
      </div>

      <!-- 2. Penyebab Risiko (Free Text Textarea, mengacu ke Profile Risiko) -->
      <div class="profile-field-group">
        <label for="penyebab_risiko">2. Penyebab Risiko <span style="color: var(--red);">*</span></label>
        <textarea id="penyebab_risiko" name="penyebab_risiko" rows="4" required placeholder="Penyebab risiko akan terisi otomatis saat memilih Nama Risiko di atas, dan dapat disesuaikan/diedit secara bebas..."><?= htmlspecialchars($kri->penyebab_risiko ?? '') ?></textarea>
        <span class="helper-text">Mengacu kepada Penyebab Risiko yang dipilih pada profile risiko, dapat diedit secara bebas (free text).</span>
      </div>

      <!-- 3. Deskripsi KRI (Free text) -->
      <div class="profile-field-group">
        <label for="deskripsi">3. Deskripsi KRI <span style="color: var(--red);">*</span></label>
        <textarea id="deskripsi" name="deskripsi" rows="3" required placeholder="Contoh: Ketersediaan armada operasional kapal penyeberangan (availability rate) lintas strategis..."><?= htmlspecialchars($kri->deskripsi ?? '') ?></textarea>
        <span class="helper-text">Deskripsi penjelasan detail mengenai indikator risiko, metode perhitungan, atau metrik pengujian.</span>
      </div>

      <!-- 4. Polaritas (Maximize / Minimize) -->
      <div class="profile-field-group">
        <label for="polaritas">4. Polaritas <span style="color: var(--red);">*</span></label>
        <select id="polaritas" name="polaritas" required>
          <?php $pol = $kri->polaritas ?? 'Maximize'; ?>
          <option value="Maximize" <?= ($pol === 'Maximize') ? 'selected' : '' ?>>Maximize</option>
          <option value="Minimize" <?= ($pol === 'Minimize') ? 'selected' : '' ?>>Minimize</option>
        </select>
        <span class="helper-text">Arah polaritas capaian: Maximize (e.g. ketersediaan sistem, pendapatan) atau Minimize (e.g. downtime, komplain, kecelakaan).</span>
      </div>

      <!-- 5. Ambang Batas (Threshold) 3 Inputan: Batas Aman (#00b04f), Batas Hati-Hati (#ffff00), Batas Bahaya (#ff0000) -->
      <div class="threshold-container">
        <div style="font-size: 14px; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: 8px;">
          5. Ambang Batas (Threshold)
        </div>
        <p style="font-size: 12.5px; color: var(--muted); margin: 4px 0 0 0;">
          Tentukan parameter batasan toleransi untuk kategori Aman, Hati-Hati, dan Bahaya.
        </p>

        <div class="threshold-grid">
          <!-- 1. Batas Aman (#00b04f) -->
          <div class="threshold-card aman">
            <div class="threshold-badge aman">
              <span>●</span> Batas Aman
            </div>
            <div class="profile-field-group" style="margin-bottom: 0;">
              <label for="ambang_batas_aman" style="font-size: 12px; color: #15803d;">Kriteria Batas Aman <span style="color:var(--red)">*</span></label>
              <input type="text" id="ambang_batas_aman" name="ambang_batas_aman" required 
                     value="<?= htmlspecialchars($kri->ambang_batas_aman ?? '') ?>"
                     placeholder="Contoh: ≥ 98,5% atau ≤ Rp 50 Juta" />
              <span class="helper-text" style="color: #166534;">Kondisi normal / terkendali dengan baik.</span>
            </div>
          </div>

          <!-- 2. Batas Hati-Hati (#ffff00) -->
          <div class="threshold-card hati-hati">
            <div class="threshold-badge hati-hati">
              <span>●</span> Batas Hati-Hati
            </div>
            <div class="profile-field-group" style="margin-bottom: 0;">
              <label for="ambang_batas_hati_hati" style="font-size: 12px; color: #854d0e;">Kriteria Batas Hati-Hati <span style="color:var(--red)">*</span></label>
              <input type="text" id="ambang_batas_hati_hati" name="ambang_batas_hati_hati" required 
                     value="<?= htmlspecialchars($kri->ambang_batas_hati_hati ?? '') ?>"
                     placeholder="Contoh: 95% - 98,4% atau Rp 50 - 100 Juta" />
              <span class="helper-text" style="color: #854d0e;">Kondisi waspada / perlu perhatian mitigasi.</span>
            </div>
          </div>

          <!-- 3. Batas Bahaya (#ff0000) -->
          <div class="threshold-card bahaya">
            <div class="threshold-badge bahaya">
              <span>●</span> Batas Bahaya
            </div>
            <div class="profile-field-group" style="margin-bottom: 0;">
              <label for="ambang_batas_bahaya" style="font-size: 12px; color: #b91c1c;">Kriteria Batas Bahaya <span style="color:var(--red)">*</span></label>
              <input type="text" id="ambang_batas_bahaya" name="ambang_batas_bahaya" required 
                     value="<?= htmlspecialchars($kri->ambang_batas_bahaya ?? '') ?>"
                     placeholder="Contoh: < 95% atau > Rp 100 Juta" />
              <span class="helper-text" style="color: #991b1b;">Kondisi kritis / melampaui toleransi risiko.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- FOOTER ACTIONS -->
      <div class="kri-footer-actions">
        <a href="<?= site_url('menrisk/daftar_kri') ?>" class="ghost" style="text-decoration: none; padding: 10px 18px; font-size: 13.5px; font-weight: 600;">
          Batal
        </a>
        <button type="submit" class="primary btn-submit-kri" id="btnBottomSaveKri">
          <?= $is_edit ? 'Perbarui Data KRI' : 'Simpan Data KRI' ?>
        </button>
      </div>
    </div>
  </form>
</main>

<script>
  let isKriSubmitting = false;

  // Event Change: Nama Risiko dipilih -> Auto-fill Penyebab Risiko & Department
  function onNamaRisikoChange(selectEl) {
    const $opt = $(selectEl).find(':selected');
    const nama     = $opt.data('nama') || '';
    const penyebab = $opt.data('penyebab') || '';
    const deptId   = $opt.data('dept') || '';

    $('#nama_risiko_hidden').val(nama);
    if (deptId) {
      $('#department_id').val(deptId);
    }
    
    // Isi otomatis textarea penyebab risiko jika ada
    if (penyebab) {
      $('#penyebab_risiko').val(penyebab);
    }
  }

  // Submit Handler dengan proteksi cegah klik ganda & disable button
  function submitKriForm(e) {
    if (e && e.preventDefault) e.preventDefault();

    if (isKriSubmitting) return false;

    const formulirId = $.trim($('#formulir_id').val());
    let namaRisiko   = $.trim($('#nama_risiko_hidden').val());
    if (!namaRisiko && formulirId) {
      namaRisiko = $.trim($('#formulir_id').find(':selected').data('nama'));
      $('#nama_risiko_hidden').val(namaRisiko);
    }
    const penyebabRisiko = $.trim($('#penyebab_risiko').val());
    const deskripsi      = $.trim($('#deskripsi').val());
    const polaritas      = $.trim($('#polaritas').val());
    const batasAman      = $.trim($('#ambang_batas_aman').val());
    const batasHatiHati  = $.trim($('#ambang_batas_hati_hati').val());
    const batasBahaya    = $.trim($('#ambang_batas_bahaya').val());

    // Validasi Field
    if (!formulirId || !namaRisiko) {
      showToast('Mohon pilih Nama Risiko dari Profile Risiko.');
      $('#formulir_id').focus();
      return;
    }
    if (!penyebabRisiko) {
      showToast('Mohon isi kolom Penyebab Risiko.');
      $('#penyebab_risiko').focus();
      return;
    }
    if (!deskripsi) {
      showToast('Mohon isi kolom Deskripsi KRI.');
      $('#deskripsi').focus();
      return;
    }
    if (!polaritas) {
      showToast('Mohon pilih Polaritas (Maximize / Minimize).');
      $('#polaritas').focus();
      return;
    }
    if (!batasAman) {
      showToast('Mohon isi Batas Aman (#00b04f).');
      $('#ambang_batas_aman').focus();
      return;
    }
    if (!batasHatiHati) {
      showToast('Mohon isi Batas Hati-Hati (#ffff00).');
      $('#ambang_batas_hati_hati').focus();
      return;
    }
    if (!batasBahaya) {
      showToast('Mohon isi Batas Bahaya (#ff0000).');
      $('#ambang_batas_bahaya').focus();
      return;
    }

    // Aktifkan lock & disable tombol submit
    isKriSubmitting = true;
    const $btns = $('.btn-submit-kri, #btnTopSaveKri, #btnBottomSaveKri');
    $btns.prop('disabled', true).addClass('disabled').css('pointer-events', 'none').text('Menyimpan data...');

    const formData = new FormData($('#formKri')[0]);

    $.ajax({
      url: '<?= site_url("menrisk/save_kri") ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Data Key Risk Indicator berhasil disimpan.');
          $btns.text('Tersimpan, mengalihkan...');
          setTimeout(() => {
            window.location.href = res.redirect_url || '<?= site_url("menrisk/daftar_kri") ?>';
          }, 1000);
        } else {
          showToast(res.message || 'Gagal menyimpan data KRI.');
          isKriSubmitting = false;
          $btns.prop('disabled', false).removeClass('disabled').css('pointer-events', '').text('<?= $is_edit ? "Perbarui Data KRI" : "Simpan Data KRI" ?>');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menyimpan data KRI.');
        isKriSubmitting = false;
        $btns.prop('disabled', false).removeClass('disabled').css('pointer-events', '').text('<?= $is_edit ? "Perbarui Data KRI" : "Simpan Data KRI" ?>');
      }
    });
  }
</script>

<?php $this->load->view('templates/footer'); ?>

