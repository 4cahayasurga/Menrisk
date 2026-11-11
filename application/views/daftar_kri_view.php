<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * View: Daftar Key Risk Indicators (KRI)
 * IFPRO Risk Command Center
 */
$this->load->view('templates/header', ['title' => $title ?? 'Daftar KRI — IFPRO Risk Command Center']);
$this->load->view('templates/sidebar', ['active_menu' => $active_menu ?? 'kri', 'is_dashboard' => false]);
?>
<style>
.list-card {
  background: #ffffff;
  border: 1px solid var(--line, #e2e8f0);
  border-radius: 12px;
  padding: 24px 28px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  margin-bottom: 24px;
}
.list-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}
.search-input-box {
  width: 280px;
  max-width: 100%;
  padding: 9px 13px;
  font-size: 13px;
  border: 1px solid #cbd5e1;
  border-radius: 7px;
  color: #0f172a;
}
.search-input-box:focus {
  outline: none;
  border-color: #29b6af;
  box-shadow: 0 0 0 3px rgba(41, 182, 175, 0.15);
}

/* Tabel KRI */
.table-scroll-wrap {
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}
.kri-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
  min-width: 1100px;
}
.kri-table thead th {
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
.kri-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
  color: #1e293b;
  line-height: 1.45;
}
.kri-table tbody tr:hover {
  background: #f8fafc;
}

/* Cell Clamp */
.cell-clamp {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  max-height: 2.9em;
  line-height: 1.45;
}

/* Action Buttons */
.action-btn-group {
  display: inline-flex;
  gap: 6px;
  align-items: center;
}
.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all .15s ease;
  white-space: nowrap;
}
.btn-act-monitoring {
  background: #fdf4ff;
  color: #a21caf;
  border-color: #f0abfc;
}
.btn-act-monitoring:hover {
  background: #fae8ff;
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

/* Polaritas Badge */
.badge-polaritas {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 700;
}
.badge-polaritas.maximize {
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
}
.badge-polaritas.minimize {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}

/* Threshold Pills */
.threshold-pill-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.threshold-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 2px 7px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  line-height: 1.3;
}
.threshold-pill.aman {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}
.threshold-pill.hati-hati {
  background: #fefce8;
  color: #854d0e;
  border: 1px solid #fef08a;
}
.threshold-pill.bahaya {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}
.dot-threshold {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.dot-aman { background-color: #00b04f; }
.dot-hati-hati { background-color: #ffff00; border: 1px solid #ca8a04; }
.dot-bahaya { background-color: #ff0000; }

/* Status Monitoring Badge */
.badge-status-mon {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 9px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-status-mon.aman {
  background: #00b04f;
  color: #ffffff;
}
.badge-status-mon.hati_hati {
  background: #ffff00;
  color: #0f172a;
  border: 1px solid #eab308;
}
.badge-status-mon.bahaya {
  background: #ff0000;
  color: #ffffff;
}
</style>

<main>
  <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
      <p class="eyebrow">MANAJEMEN RISIKO · KEY RISK INDICATORS</p>
      <h1>Daftar Key Risk Indicators (KRI)</h1>
      <p class="subtitle">Daftar metrik indikator peringatan dini (early warning metrics) dan evaluasi ambang batas toleransi.</p>
    </div>
    <div class="header-actions">
      <a href="<?= site_url('menrisk/tambah_kri') ?>" class="primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; font-size: 13px; font-weight: 600;">
        <span>+</span> Buat Data KRI Baru
      </a>
      <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
      </div>
    </div>
  </header>

  <div class="list-card">
    <div class="list-toolbar">
      <div>
        <strong style="font-size: 15px; color: var(--navy, #0f172a);">Tabel Key Risk Indicators</strong>
        <p style="font-size: 12px; color: var(--muted, #64748b); margin: 2px 0 0 0;">
          Kelola metrik indikator risiko, pantau status ambang batas, dan perbarui progres evaluasi berkala.
        </p>
      </div>
      <div>
        <input type="text" id="searchKriInput" class="search-input-box" placeholder="Cari risiko, deskripsi, metrik KRI..." />
      </div>
    </div>

    <div class="table-scroll-wrap">
      <table class="kri-table" id="tableDaftarKri">
        <thead>
          <tr>
            <th style="width: 44px; text-align: center;">No</th>
            <th style="width: 220px; text-align: center;">Aksi</th>
            <th style="min-width: 180px;">Nama Risiko</th>
            <th style="min-width: 170px;">Penyebab Risiko</th>
            <th style="min-width: 180px;">Deskripsi KRI</th>
            <th style="width: 110px; text-align: center;">Polaritas</th>
            <th style="min-width: 210px;">Ambang Batas (Threshold)</th>
            <th style="min-width: 160px; text-align: center;">Monitoring Terakhir</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($kri_list)): ?>
            <?php foreach ($kri_list as $idx => $row): ?>
              <?php
                $searchContent = strtolower(
                  ($row->nama_risiko ?? '') . ' ' .
                  ($row->penyebab_risiko ?? '') . ' ' .
                  ($row->deskripsi ?? '') . ' ' .
                  ($row->polaritas ?? '') . ' ' .
                  ($row->ambang_batas_aman ?? '') . ' ' .
                  ($row->ambang_batas_hati_hati ?? '') . ' ' .
                  ($row->ambang_batas_bahaya ?? '')
                );
              ?>
              <tr class="kri-row-item" data-search="<?= htmlspecialchars($searchContent) ?>" id="row-kri-<?= $row->id ?>">
                <td style="text-align: center; font-weight: 700; color: #64748b;"><?= $idx + 1 ?></td>
                <td style="text-align: center;">
                  <div class="action-btn-group">
                    <a href="<?= site_url('menrisk/monitoring_kri/' . (int)$row->id) ?>" class="btn-act btn-act-monitoring" title="Buka Progres Monitoring KRI">
                      <span>◫</span> Monitoring
                    </a>
                    <a href="<?= site_url('menrisk/kri_form/' . (int)$row->id) ?>" class="btn-act btn-act-edit" title="Edit Data KRI">
                      <span>✎</span> Edit
                    </a>
                    <button type="button" class="btn-act btn-act-delete" onclick="confirmDeleteKri(<?= (int)$row->id ?>, '<?= htmlspecialchars(addslashes($row->nama_risiko ?? '')) ?>')" title="Hapus Data KRI">
                      <span>🗑</span> Hapus
                    </button>
                  </div>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->nama_risiko ?? '') ?>">
                    <strong><?= htmlspecialchars($row->nama_risiko ?: '-') ?></strong>
                  </div>
                  <?php if (!empty($row->sasaran)): ?>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                      Sasaran: <?= htmlspecialchars($row->sasaran) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->penyebab_risiko ?? '') ?>">
                    <?= nl2br(htmlspecialchars($row->penyebab_risiko ?: '-')) ?>
                  </div>
                </td>
                <td>
                  <div class="cell-clamp" title="<?= htmlspecialchars($row->deskripsi ?? '') ?>">
                    <?= nl2br(htmlspecialchars($row->deskripsi ?: '-')) ?>
                  </div>
                </td>
                <td style="text-align: center;">
                  <?php if (strtolower($row->polaritas ?? '') === 'minimize'): ?>
                    <span class="badge-polaritas minimize" title="Minimize: Semakin kecil semakin baik">
                      <span>▼</span> Minimize
                    </span>
                  <?php else: ?>
                    <span class="badge-polaritas maximize" title="Maximize: Semakin tinggi semakin baik">
                      <span>▲</span> Maximize
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="threshold-pill-group">
                    <div class="threshold-pill aman" title="Batas Aman (#00b04f)">
                      <span class="dot-threshold dot-aman"></span>
                      <span>Aman: <?= htmlspecialchars($row->ambang_batas_aman ?: '-') ?></span>
                    </div>
                    <div class="threshold-pill hati-hati" title="Batas Hati-Hati (#ffff00)">
                      <span class="dot-threshold dot-hati-hati"></span>
                      <span>Hati-Hati: <?= htmlspecialchars($row->ambang_batas_hati_hati ?: '-') ?></span>
                    </div>
                    <div class="threshold-pill bahaya" title="Batas Bahaya (#ff0000)">
                      <span class="dot-threshold dot-bahaya"></span>
                      <span>Bahaya: <?= htmlspecialchars($row->ambang_batas_bahaya ?: '-') ?></span>
                    </div>
                  </div>
                </td>
                <td style="text-align: center;">
                  <?php if (!empty($row->last_hasil_threshold)): ?>
                    <?php 
                      $thKey = strtolower($row->last_hasil_threshold);
                      $thLabel = ($thKey === 'aman') ? 'Aman' : (($thKey === 'hati_hati') ? 'Hati-Hati' : 'Bahaya');
                    ?>
                    <span class="badge-status-mon <?= $thKey ?>">
                      ● <?= $thLabel ?>
                    </span>
                    <div style="font-size: 11px; font-weight: 600; color: #1e293b; margin-top: 4px;">
                      <?= htmlspecialchars($row->last_monitoring_val ?? '-') ?>
                    </div>
                    <div style="font-size: 10px; color: #64748b;">
                      <?= htmlspecialchars($row->last_periode ?? '') ?> <?= htmlspecialchars($row->last_tahun ?? '') ?>
                    </div>
                  <?php else: ?>
                    <span style="color: #94a3b8; font-size: 11.5px;">Belum dimonitor</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyRowKri">
              <td colspan="8" style="text-align: center; padding: 36px 16px; color: #64748b;">
                Belum ada data Key Risk Indicator. Klik tombol <b>+ Buat Data KRI Baru</b> untuk menambahkan indikator risiko.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
  // Filter pencarian tabel KRI
  $('#searchKriInput').on('input', function() {
    const val = $.trim($(this).val()).toLowerCase();
    let visibleCount = 0;
    $('.kri-row-item').each(function() {
      const search = $(this).data('search') || '';
      if (!val || search.indexOf(val) !== -1) {
        $(this).show();
        visibleCount++;
      } else {
        $(this).hide();
      }
    });

    if (visibleCount === 0 && $('.kri-row-item').length > 0) {
      if (!$('#noMatchKriRow').length) {
        $('#tableDaftarKri tbody').append('<tr id="noMatchKriRow"><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">Tidak ada data KRI yang cocok dengan kata kunci pencarian.</td></tr>');
      }
    } else {
      $('#noMatchKriRow').remove();
    }
  });

  // Hapus Data KRI
  function confirmDeleteKri(id, nama) {
    if (!confirm('Apakah Anda yakin ingin menghapus data KRI "' + nama + '" beserta seluruh riwayat monitoringnya?')) {
      return;
    }

    $.ajax({
      url: '<?= site_url("menrisk/delete_kri/") ?>' + id,
      type: 'POST',
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(res) {
        if (res.status === 'success') {
          showToast(res.message || 'Data KRI berhasil dihapus.');
          $('#row-kri-' + id).fadeOut(300, function() {
            $(this).remove();
            if ($('.kri-row-item').length === 0) {
              window.location.reload();
            }
          });
        } else {
          showToast(res.message || 'Gagal menghapus data KRI.');
        }
      },
      error: function() {
        showToast('Terjadi kesalahan koneksi saat menghapus data KRI.');
      }
    });
  }
</script>

<?php $this->load->view('templates/footer'); ?>

