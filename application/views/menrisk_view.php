<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * View: menrisk_view.php
 * IFPRO Risk Command Center
 * 
 * Data dipasok langsung dari Controller Menrisk.php via SQL Server
 */
$this->load->view('templates/header', ['title' => $title ?? 'IFPRO Risk Command Center']);
?>
  <!-- Sidebar Navigation Template -->
  <?php $this->load->view('templates/sidebar'); ?>

  <!-- Main Content Area -->
  <main>
    <!-- Top Header -->
    <header>
      <button class="menu" aria-label="Buka menu">☰</button>
      <div>
        <p class="eyebrow">BOARD OF DIRECTORS · CONFIDENTIAL</p>
        <h1>Enterprise Risk Overview</h1>
        <p class="subtitle">Posisi risiko perusahaan dan prioritas keputusan manajemen</p>
      </div>
      <div class="header-actions">
        <div class="updated">
          <span class="live"></span>
          <div>
            <small>Data diperbarui</small>
            <strong><?= date('d M Y · H:i') ?></strong>
          </div>
        </div>
        <button class="icon-btn" title="Notifikasi">♢<b><?= count($kri) ?></b></button>
        <button type="button" class="avatar profile-trigger" id="profileMenuBtn"
                title="<?= htmlspecialchars($current_user['nama'] ?? 'User') ?>"
                aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown"
                onclick="toggleProfileMenu(event)">
          <?= strtoupper(substr($current_user['nama'] ?? 'US', 0, 2)) ?>
        </button>
        <button type="button" class="ghost" id="menuChangePassword" role="menuitem"
                  onclick="closeProfileMenu(); openChangePasswordModal();" style="display: flex; align-items: center; gap: 2px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="7.5" cy="15.5" r="5.5"></circle>
              <path d="m21 2-9.6 9.6"></path>
              <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
            </svg>
            Change Password
        </button>
        <a href="<?= site_url('auth/logout') ?>" class="ghost" style="padding:0 10px;text-decoration:none;height:36px;font-size:12px;display: flex; align-items: center; gap: 2px;" title="Keluar dari sistem">
          ⎋ Logout
        </a>
      </div>
    </header>


    <!-- Filters Bar -->
    <section class="filters">
      <label>Periode
        <select id="period">
          <?php foreach ($filter_options['periods'] as $p): ?>
            <option value="<?= $p ?>" <?= ($selected_period === $p) ? 'selected' : '' ?>><?= $p ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Department
        <select id="department">
          <?php 
          $dept_options = isset($filter_options['departments']) ? $filter_options['departments'] : $filter_options['directorates'];
          $current_selected_dept = isset($selected_department) ? $selected_department : $selected_directorate;
          foreach ($dept_options as $label => $val): 
          ?>
            <option value="<?= $val ?>" <?= ($current_selected_dept === $val) ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Kategori
        <select id="category">
          <?php foreach ($filter_options['categories'] as $label => $val): ?>
            <option value="<?= $val ?>" <?= ($selected_category === $val) ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <button class="ghost" id="reset">↻ Reset</button>
      <button class="primary" id="export">⇩ Executive Brief</button>
    </section>

    <!-- VIEW 1: EXECUTIVE OVERVIEW -->
    <div id="overview" class="view active-view">
      <!-- High Priority Alert -->
      <section class="alert">
        <div class="alert-icon">!</div>
        <div>
          <strong><span id="alertCount"><?= $kpis['critical_risks'] ?></span> risiko memerlukan keputusan BOD</strong>
          <p>Eksposur kritis teridentifikasi dan memerlukan penanganan segera.</p>
        </div>
        <button id="showCritical">Tinjau sekarang →</button>
      </section>

      <!-- KPI Metrics Cards -->
      <section class="kpis">
        <article class="kpi">
          <div class="kpi-top"><span>Total Risiko Aktif</span><i class="blue">◎</i></div>
          <strong id="totalRisk"><?= $kpis['total_risks'] ?></strong>
          <p><b class="down">↓ 4</b> dari bulan lalu</p>
          <div class="spark blue-spark"></div>
        </article>

        <article class="kpi">
          <div class="kpi-top"><span>Risiko Kritis</span><i class="red">!</i></div>
          <strong id="criticalRisk"><?= $kpis['critical_risks'] ?></strong>
          <p><b class="up">↑ 2</b> perlu perhatian</p>
          <div class="spark red-spark"></div>
        </article>

        <article class="kpi">
          <div class="kpi-top"><span>Residual Exposure</span><i class="amber">◆</i></div>
          <strong><?= $kpis['residual_exposure'] ?></strong>
          <p><b class="down">↓ 8,4%</b> pasca mitigasi</p>
          <div class="spark amber-spark"></div>
        </article>

        <article class="kpi">
          <div class="kpi-top"><span>Mitigasi Tepat Waktu</span><i class="green">✓</i></div>
          <strong id="kpiMitigation"><?= $kpis['ontime_mitigation'] ?></strong>
          <p><b class="up">11 aksi</b> melewati target</p>
          <div class="progress"><span style="width:<?= (int)$kpis['ontime_mitigation'] ?>%"></span></div>
        </article>
      </section>

      <!-- Grid Main: Heatmap & Appetite -->
      <section class="grid-main">
        <article class="panel heatmap-panel">
          <div class="panel-head">
            <div>
              <h2>Profile Risiko Heatmap (Current)</h2>
              <p>Distribusi skala rasio (current) dari Progress Monitoring Daftar Profile Risiko · klik sel untuk melihat detail</p>
            </div>
            <span class="tag" id="tagProfileCurrentCount">0 RISIKO CURRENT</span>
          </div>
          <div class="heatmap-wrap">
            <!-- <div class="axis-y">DAMPAK →</div> -->
            <div class="profile-heatmap-grid" id="profileCurrentHeatmap"></div>
            <!-- <div class="axis-x">KEMUNGKINAN →</div> -->
          </div>
          <div class="legend" style="flex-wrap: wrap; gap: 10px 14px;">
            <span><i class="legend-i" style="background:#548235"></i>Low (1–5)</span>
            <span><i class="legend-i" style="background:#a9d08e"></i>Low to Mod. (6–11)</span>
            <span><i class="legend-i" style="background:#ffff00;border:1px solid #cbd5e1"></i>Moderate (12–15)</span>
            <span><i class="legend-i" style="background:#f4b042"></i>Mod. to High (16–20)</span>
            <span><i class="legend-i" style="background:#e52e20"></i>High (21–25)</span>
          </div>
        </article>

        <article class="panel appetite">
          <div class="panel-head">
            <div>
              <h2>Risk Appetite Status</h2>
              <p>Eksposur terhadap toleransi BOD</p>
            </div>
            <button class="dots">•••</button>
          </div>
          <div class="donut-wrap">
            <div class="donut">
              <div>
                <strong>71%</strong>
                <span>Dalam appetite</span>
              </div>
            </div>
          </div>
          <div class="appetite-list">
            <div><span><i class="dot green-bg"></i>Dalam batas</span><b id="appetiteDalam"><?= $kpis['appetite']['Dalam'] ?></b></div>
            <div><span><i class="dot amber-bg"></i>Mendekati batas</span><b id="appetiteMendekati"><?= $kpis['appetite']['Mendekati'] ?></b></div>
            <div><span><i class="dot red-bg"></i>Melebihi batas</span><b id="appetiteMelebihi"><?= $kpis['appetite']['Melebihi'] ?></b></div>
          </div>
          <div class="callout">
            <span>⚑</span>
            <p><strong><?= $kpis['appetite']['Melebihi'] ?> risiko melampaui appetite</strong><br>Wajib memiliki treatment plan disetujui BOD.</p>
          </div>
        </article>
      </section>

      <!-- Grid Secondary: Top Risks & Trend -->
      <section class="grid-secondary">
        <article class="panel">
          <div class="panel-head">
            <div>
              <h2>Top Enterprise Risks</h2>
              <p>Urutan berdasarkan residual score</p>
            </div>
            <button class="link" data-jump="register">Lihat semua →</button>
          </div>
          <div class="risk-list" id="topRisks"></div>
        </article>

        <article class="panel">
          <div class="panel-head">
            <div>
              <h2>Exposure Trend</h2>
              <p>Pergerakan inherent vs residual score</p>
            </div>
            <span class="tag">6 BULAN</span>
          </div>
          <div class="chart-legend">
            <span><i class="line navy"></i>Inherent</span>
            <span><i class="line teal"></i>Residual</span>
            <span><i class="dash"></i>Appetite</span>
          </div>
          <div class="linechart" id="trendChart"></div>
        </article>
      </section>

      <!-- Grid Bottom: Mitigation & KRI Status -->
      <section class="grid-secondary bottom">
        <article class="panel">
          <div class="panel-head">
            <div>
              <h2>Mitigation Performance</h2>
              <p>Status progres penanganan risiko</p>
            </div>
          </div>
          <div class="bar-row"><span>Selesai</span><div><i style="width:46%" class="green-bg"></i></div><b>23</b></div>
          <div class="bar-row"><span>On track</span><div><i style="width:32%" class="blue-bg"></i></div><b>16</b></div>
          <div class="bar-row"><span>Terlambat</span><div><i style="width:18%" class="amber-bg"></i></div><b>9</b></div>
          <div class="bar-row"><span>Belum mulai</span><div><i style="width:4%" class="red-bg"></i></div><b>2</b></div>
        </article>

        <article class="panel">
          <div class="panel-head">
            <div>
              <h2>Key Risk Indicators</h2>
              <p>Indikator termonitor dari database</p>
            </div>
            <span class="tag"><?= count($kri) ?> INDIKATOR</span>
          </div>
          <div id="kriOverviewList">
            <?php foreach (array_slice($kri, 0, 3) as $row): ?>
              <div class="kri-row">
                <div>
                  <strong><?= htmlspecialchars($row['indicator']) ?></strong>
                  <small><?= htmlspecialchars($row['directorate']) ?></small>
                </div>
                <b><?= htmlspecialchars($row['value']) ?></b>
                <span class="status <?= $row['status'] === 'Kritis' ? 'critical' : ($row['status'] === 'Waspada' ? 'warning' : '') ?>">
                  <?= strtoupper(htmlspecialchars($row['status'])) ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        </article>
      </section>
    </div>

    <!-- VIEW 2: RISK REGISTER -->
    <div id="register" class="view">
      <section class="panel full">
        <div class="panel-head">
          <div>
            <h2>Enterprise Risk Register</h2>
            <p>Daftar risiko lintas department dan status tindak lanjut</p>
          </div>
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <input id="search" placeholder="Cari risiko, ID, atau owner..." value="<?= htmlspecialchars($search_keyword) ?>" />
            <a href="<?= site_url('menrisk/profil_risiko') ?>" id="btnOpenProfilRisiko" style="display:inline-flex;align-items:center;gap:6px;padding:9px 15px;background:#fff;color:var(--navy);border:1px solid #cbd5e1;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;text-decoration:none;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:all .15s ease;">
              <span style="font-size:15px;line-height:1">📋</span> Formulir Profile Risiko
            </a>
            <button id="btnOpenAddRisk" class="btn-primary" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:linear-gradient(135deg, var(--teal) 0%, #00877e 100%);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;white-space:nowrap;box-shadow:0 3px 8px rgba(0,168,157,0.3);transition:transform .15s, filter .15s;">
              <span style="font-size:16px;line-height:1">+</span> Tambah Risiko
            </button>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Pernyataan Risiko</th>
                <th>Owner</th>
                <th>Kategori</th>
                <th>Inherent</th>
                <th>Residual</th>
                <th>Appetite</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="riskTable">
              <!-- Rendered via JS Hydration dari DB -->
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- VIEW 3: MITIGATION MONITOR -->
    <div id="mitigation" class="view">
      <section class="panel full">
        <div class="panel-head">
          <div>
            <h2>Mitigation Monitor</h2>
            <p>Progres treatment plan dan akuntabilitas risk owner</p>
          </div>
        </div>
        <div class="mitigation-grid" id="mitigationCards">
          <!-- Rendered via JS Hydration dari DB -->
        </div>
      </section>
    </div>

    <!-- VIEW 4: KEY RISK INDICATORS -->
    <div id="kri" class="view">
      <section class="panel full">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div>
            <h2>Key Risk Indicators (KRI)</h2>
            <p>Early warning metrics dan ambang batas toleransi risiko</p>
          </div>
          <div style="display:flex; gap:10px;">
            <a href="<?= site_url('menrisk/daftar_kri') ?>" class="ghost" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; padding:7px 14px; font-size:12.5px; font-weight:600;">
              <span>📋</span> Buka Daftar KRI
            </a>
            <a href="<?= site_url('menrisk/tambah_kri') ?>" class="primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; padding:7px 14px; font-size:12.5px; font-weight:600;">
              <span>+</span> Tambah KRI Baru
            </a>
          </div>
        </div>
        <div class="kri-board" id="kriBoard">
          <!-- Rendered via JS Hydration dari DB -->
        </div>
      </section>
    </div>
  </main>

  <!-- Risk Detail Modal -->
  <div class="modal" id="modal">
    <div class="modal-card" id="modalCard">
      <button class="close" id="closeModal">×</button>
      <p class="eyebrow" id="modalEyebrow">RISK DETAIL</p>
      <h2 id="modalTitle"></h2>
      <div id="modalBody"></div>
    </div>
  </div>

  <!-- Modal Tambah Risiko Baru -->
  <div class="modal" id="modalAddRisk">
    <div class="modal-card modal-card-large">
      <button class="close" id="closeAddRiskModal">×</button>
      <p class="eyebrow">ENTERPRISE RISK MANAGEMENT</p>
      <h2>Tambah Risiko Baru</h2>
      <p style="color:var(--muted);font-size:12px;margin-top:2px;">Daftarkan identifikasi risiko baru ke dalam sistem dan database SQL Server</p>

      <form id="formAddRisk">
        <div class="form-grid">
          <!-- Row 1: ID & Periode -->
          <div class="form-group">
            <label for="new_risk_id">ID Risiko <small style="color:var(--teal);font-weight:normal">Auto Generated</small></label>
            <input type="text" id="new_risk_id" name="id" value="<?= htmlspecialchars($next_risk_id ?? 'R-009') ?>" required />
          </div>
          <div class="form-group">
            <label for="new_period">Periode Pelaporan</label>
            <select id="new_period" name="period" required>
              <?php foreach ($filter_options['periods'] as $p): ?>
                <option value="<?= $p ?>" <?= ($p === $selected_period) ? 'selected' : '' ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Row 2: Pernyataan Risiko -->
          <div class="form-group full" style="min-height:0">
            <label for="new_title">Pernyataan Risiko (Risk Event) <span style="color:#d93b45">*</span></label>
            <textarea id="new_title" name="title" rows="2" placeholder="Uraikan peristiwa risiko yang berpotensi terjadi dan mengganggu sasaran..." required></textarea>
          </div>

          <!-- Row 3: Owner & Department -->
          <div class="form-group">
            <label for="new_owner">Risk Owner <span style="color:#d93b45">*</span></label>
            <input type="text" id="new_owner" name="owner" placeholder="Contoh: Manager IT / VP Operasional" required />
          </div>
          <div class="form-group">
            <label for="new_department_id">Department Terkait <span style="color:#d93b45">*</span></label>
            <select id="new_department_id" name="department_id" required>
              <option value="">-- Pilih Department --</option>
              <?php if (!empty($available_depts)): ?>
                <?php foreach ($available_depts as $dept): ?>
                  <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?><?= (!empty($dept['code']) && $dept['code'] !== $dept['name']) ? ' (' . htmlspecialchars($dept['code']) . ')' : '' ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Row 4: Kategori & Appetite -->
          <div class="form-group">
            <label for="new_cat">Kategori Risiko <span style="color:#d93b45">*</span></label>
            <select id="new_cat" name="cat" required>
              <option value="Strategis">Strategis</option>
              <option value="Operasional" selected>Operasional</option>
              <option value="Keuangan">Keuangan</option>
              <option value="Kepatuhan">Kepatuhan</option>
              <option value="Teknologi">Teknologi</option>
            </select>
          </div>
          <div class="form-group">
            <label for="new_appetite">Status Appetite</label>
            <select id="new_appetite" name="appetite" required>
              <option value="Dalam">Dalam Batas Appetite (Skor Rendah)</option>
              <option value="Mendekati" selected>Mendekati Batas Appetite (Skor Sedang)</option>
              <option value="Melebihi">Melebihi Batas Appetite (Skor Tinggi/Kritis)</option>
            </select>
          </div>

          <!-- Row 5: Inherent Risk & Residual Risk (Side by Side) -->
          <div class="form-group" style="background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;">
            <label>Inherent Risk <span id="lblInherentBadge" class="score-badge" style="background:#f3a62a">Skor: 12</span></label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px;">
              <div>
                <small style="display:block;color:#64748b;font-size:11px;margin-bottom:4px;">Kemungkinan (1-5)</small>
                <select id="new_inh_l" style="width:100%">
                  <option value="1">1 - Sangat Rendah</option>
                  <option value="2">2 - Rendah</option>
                  <option value="3">3 - Sedang</option>
                  <option value="4" selected>4 - Tinggi</option>
                  <option value="5">5 - Sangat Tinggi</option>
                </select>
              </div>
              <div>
                <small style="display:block;color:#64748b;font-size:11px;margin-bottom:4px;">Dampak (1-5)</small>
                <select id="new_inh_i" style="width:100%">
                  <option value="1">1 - Sangat Rendah</option>
                  <option value="2">2 - Rendah</option>
                  <option value="3" selected>3 - Sedang</option>
                  <option value="4">4 - Signifikan</option>
                  <option value="5">5 - Katastropik</option>
                </select>
              </div>
            </div>
            <input type="hidden" id="new_inherent" name="inherent" value="12" />
          </div>

          <div class="form-group" style="background:#f8fafc;padding:12px 14px;border-radius:8px;border:1px solid #e2e8f0;">
            <label>Residual Risk <span id="lblResidualBadge" class="score-badge" style="background:#1c9b68">Skor: 6</span></label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px;">
              <div>
                <small style="display:block;color:#64748b;font-size:11px;margin-bottom:4px;">Kemungkinan (1-5)</small>
                <select id="new_res_l" style="width:100%">
                  <option value="1">1 - Sangat Rendah</option>
                  <option value="2" selected>2 - Rendah</option>
                  <option value="3">3 - Sedang</option>
                  <option value="4">4 - Tinggi</option>
                  <option value="5">5 - Sangat Tinggi</option>
                </select>
              </div>
              <div>
                <small style="display:block;color:#64748b;font-size:11px;margin-bottom:4px;">Dampak (1-5)</small>
                <select id="new_res_i" style="width:100%">
                  <option value="1">1 - Sangat Rendah</option>
                  <option value="2">2 - Rendah</option>
                  <option value="3" selected>3 - Sedang</option>
                  <option value="4">4 - Signifikan</option>
                  <option value="5">5 - Katastropik</option>
                </select>
              </div>
            </div>
            <input type="hidden" id="new_residual" name="residual" value="6" />
          </div>

          <!-- Row 6: Progress & Due Date -->
          <div class="form-group">
            <label for="new_progress">Progres Mitigasi <span id="lblProgress" style="color:var(--teal);font-weight:800">0%</span></label>
            <div style="display:flex;align-items:center;height:38px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:7px;padding:0 12px;">
              <input type="range" id="new_progress" name="progress" min="0" max="100" value="0" style="width:100%;padding:0;border:0;background:transparent;accent-color:var(--teal);cursor:pointer;" />
            </div>
          </div>
          <div class="form-group">
            <label for="new_due">Target Selesai (Due Date)</label>
            <input type="text" id="new_due" name="due" placeholder="Contoh: 31 Des 2026 / 15 Nov 2026" />
          </div>

          <!-- Row 7: Treatment Plan -->
          <div class="form-group full" style="min-height:0">
            <label for="new_mitigation">Rencana Mitigasi (Action Plan)</label>
            <textarea id="new_mitigation" name="mitigation" rows="2" placeholder="Uraikan strategi penanganan dan rencana perlakuan risiko..."></textarea>
          </div>

          <!-- Row 8: Decision BOD -->
          <div class="form-group full" style="min-height:0">
            <label for="new_decision">Keputusan yang Dibutuhkan dari BOD <small style="color:#64748b;font-weight:normal">(Opsional)</small></label>
            <textarea id="new_decision" name="decision" rows="2" placeholder="Uraikan arahan kebijakan, alokasi anggaran, atau otorisasi khusus BOD..."></textarea>
          </div>
        </div>

        <div class="modal-foot">
          <button type="button" class="btn-secondary" id="btnCancelAddRisk">Batal</button>
          <button type="submit" class="btn-submit" id="btnSubmitRisk">Simpan Risiko</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast Notification -->

  <!-- Client-side Hydration & Interactions -->
  <script>
    // Data diinjeksikan langsung dari Controller CI3 & SQL Server
    let allRisks        = <?= json_encode($risks, JSON_UNESCAPED_UNICODE) ?>;
    let allKri          = <?= json_encode($kri, JSON_UNESCAPED_UNICODE) ?>;
    let kpis            = <?= json_encode($kpis, JSON_UNESCAPED_UNICODE) ?>;
    let allProfilRisiko = <?= json_encode($profil_risiko_list ?? [], JSON_UNESCAPED_UNICODE) ?>;

    const colors = s => s >= 20 ? '#d93b45' : s >= 15 ? '#ed8351' : s >= 10 ? '#f3a62a' : s >= 5 ? '#d5bd3e' : '#1c9b68';
    const riskList = document.getElementById('topRisks');

    function renderTop(data = allRisks) {
      if (!riskList) return;
      riskList.innerHTML = data.slice().sort((a, b) => b.residual - a.residual).slice(0, 5).map((r, i) => `
        <div class="risk-item" data-id="${r.id}">
          <span class="rank">${i + 1}</span>
          <div>
            <strong>${r.title}</strong>
            <small>${r.id} · ${r.owner}</small>
          </div>
          <span class="score" style="background:${colors(r.residual)}">${r.residual}</span>
        </div>
      `).join('');
      riskList.querySelectorAll('.risk-item').forEach(x => x.onclick = () => openRisk(x.dataset.id));
    }

    function renderTable(data = allRisks) {
      const tableBody = document.getElementById('riskTable');
      if (!tableBody) return;
      tableBody.innerHTML = data.map(r => `
        <tr>
          <td><b>${r.id}</b></td>
          <td>${r.title}</td>
          <td>${r.owner}</td>
          <td><span class="pill">${r.cat}</span></td>
          <td><span class="pill score-pill" style="background:${colors(r.inherent)}">${r.inherent}</span></td>
          <td><span class="pill score-pill" style="background:${colors(r.residual)}">${r.residual}</span></td>
          <td><span class="status ${r.appetite === 'Melebihi' ? 'critical' : r.appetite === 'Mendekati' ? 'warning' : ''}">${r.appetite}</span></td>
          <td><button class="table-btn" data-id="${r.id}">Detail</button></td>
        </tr>
      `).join('');
      tableBody.querySelectorAll('.table-btn').forEach(x => x.onclick = () => openRisk(x.dataset.id));
    }

    function renderMitigations(data = allRisks) {
      const el = document.getElementById('mitigationCards');
      if (!el) return;
      el.innerHTML = data.map(r => `
        <article class="mit-card">
          <span class="status ${r.progress < 60 ? 'critical' : r.progress < 75 ? 'warning' : ''}">${r.id}</span>
          <h3>${r.title}</h3>
          <p>${r.mitigation}</p>
          <div class="mit-meta"><span>${r.owner}</span><b>${r.progress}%</b></div>
          <div class="progress">
            <span style="width:${r.progress}%;background:${r.progress < 60 ? '#d93b45' : r.progress < 75 ? '#f3a62a' : '#1c9b68'}"></span>
          </div>
          <div class="mit-meta"><small>Target</small><strong>${r.due}</strong></div>
        </article>
      `).join('');
    }

    function renderKri(kriData = allKri) {
      const el = document.getElementById('kriBoard');
      if (!el) return;
      el.innerHTML = kriData.map(x => `
        <article class="kri-card">
          <span class="status ${x.status === 'Kritis' ? 'critical' : x.status === 'Waspada' ? 'warning' : ''}">${x.status.toUpperCase()}</span>
          <h3>${x.indicator}</h3>
          <strong style="font-size:25px;display:block;margin:8px 0">${x.value}</strong>
          <p>Threshold: ${x.threshold} · ${x.directorate}</p>
        </article>
      `).join('');
    }

    function heatmap() {
      const h = document.getElementById('heatmap');
      if (!h) return;
      let html = '';
      for (let imp = 1; imp <= 5; imp++) {
        for (let lik = 1; lik <= 5; lik++) {
          const score = imp * lik,
                count = allRisks.filter(r => r.inherent === score).length,
                cl = score >= 15 ? 'extreme' : score >= 10 ? 'high' : score >= 5 ? 'moderate' : 'low';
          html += `<div class="cell ${cl}" data-score="${score}" data-imp="${imp}" data-lik="${lik}" title="Skor ${score} (Dampak ${imp} × Kemungkinan ${lik})">${count ? `<em>${count}</em>` : ''}</div>`;
        }
      }
      h.innerHTML = html;
      h.querySelectorAll('.cell').forEach(c => c.onclick = () => {
        const score = parseInt(c.dataset.score, 10);
        const imp   = parseInt(c.dataset.imp, 10);
        const lik   = parseInt(c.dataset.lik, 10);
        const found = allRisks.filter(r => parseInt(r.inherent, 10) === score);
        openEnterpriseHeatmapModal(score, imp, lik, found);
      });
    }

    function openEnterpriseHeatmapModal(score, imp, lik, items) {
      const modalCard = document.getElementById('modalCard');
      const modalEyebrow = document.getElementById('modalEyebrow');
      if (modalCard) modalCard.classList.add('modal-card-wide');
      if (modalEyebrow) modalEyebrow.textContent = 'ENTERPRISE RISK HEATMAP · INHERENT RISK';

      const levelLabel = score >= 15 ? 'Ekstrem' : score >= 10 ? 'Tinggi' : score >= 5 ? 'Moderat' : 'Rendah';
      const levelBg    = score >= 15 ? '#eb7478' : score >= 10 ? '#f5bc72' : score >= 5 ? '#fbe9ac' : '#cce9db';
      const levelText  = score >= 15 ? '#ffffff' : '#17243a';

      document.getElementById('modalTitle').textContent = 'Detail Distribusi Enterprise Risk';

      const summaryHtml = `
        <div class="hm-modal-summary">
          <div class="hm-score-box" style="background:${levelBg};color:${levelText};">
            ${score}
          </div>
          <div class="hm-summary-meta">
            <strong>Tingkat Risiko: ${levelLabel} (Skor Inherent ${score})</strong>
            <span>Matriks: Dampak Skala ${imp} × Kemungkinan Skala ${lik}</span>
          </div>
          <div class="hm-summary-count">
            <b>${items ? items.length : 0}</b>
            <small>Risiko</small>
          </div>
        </div>
      `;

      if (!items || items.length === 0) {
        document.getElementById('modalBody').innerHTML = `
          ${summaryHtml}
          <div style="text-align:center;padding:24px 16px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;">
            <p style="color:var(--muted);font-size:13px;">Belum ada risiko aktif dengan skor inherent <strong>${score}</strong> pada sel ini.</p>
          </div>
        `;
      } else {
        const listHtml = items.map((r, idx) => `
          <div class="hm-risk-card">
            <div class="hm-risk-head">
              <div class="hm-risk-title-wrap">
                <span class="hm-risk-num">${idx + 1}</span>
                <div>
                  <div class="hm-risk-title">${r.title}</div>
                  <div class="hm-risk-subchips">
                    <span class="hm-chip"><b>${r.id}</b></span>
                    <span class="hm-chip dept">🏢 ${r.dir || '-'}</span>
                    <span class="hm-chip">🏷️ ${r.cat || '-'}</span>
                    <span class="hm-chip">👤 ${r.owner || '-'}</span>
                  </div>
                </div>
              </div>
              <span class="status ${r.appetite === 'Melebihi' ? 'critical' : r.appetite === 'Mendekati' ? 'warning' : ''}" style="font-size:10px;padding:4px 8px;">
                ${r.appetite || 'Dalam'} Appetite
              </span>
            </div>

            <div class="hm-info-grid">
              <div class="hm-info-item">
                <small>Skor Inherent → Residual</small>
                <p>
                  <span class="hm-mini-score" style="background:${colors(r.inherent)};color:#fff;">Inherent: ${r.inherent}</span>
                  <span style="margin:0 4px;color:#64748b;">→</span>
                  <span class="hm-mini-score" style="background:${colors(r.residual)};color:#fff;">Residual: ${r.residual}</span>
                </p>
              </div>
              <div class="hm-info-item">
                <small>Progres Mitigasi &amp; Target</small>
                <div style="display:flex;align-items:center;gap:8px;margin-top:3px;">
                  <div class="progress" style="flex:1;margin-top:0 !important;height:6px !important;">
                    <span style="width:${r.progress}%;background:${r.progress < 60 ? '#d93b45' : r.progress < 75 ? '#f3a62a' : '#1c9b68'} !important;"></span>
                  </div>
                  <strong style="font-size:11px;color:var(--navy);">${r.progress}%</strong>
                </div>
                <span style="display:block;font-size:11px;color:#64748b;margin-top:3px;">Target Selesai: <b>${r.due || '-'}</b></span>
              </div>
              <div class="hm-info-item" style="grid-column: 1 / -1;">
                <small>Rencana Mitigasi (Treatment Plan)</small>
                <p>${r.mitigation || '-'}</p>
              </div>
            </div>

            ${r.decision ? `
              <div class="hm-note-box" style="background:#fff3f2;border-color:#fecaca;color:#991b1b;">
                <strong>⚑ Keputusan yang dibutuhkan dari BOD:</strong> ${r.decision}
              </div>
            ` : ''}
          </div>
        `).join('');

        document.getElementById('modalBody').innerHTML = `
          ${summaryHtml}
          <div class="hm-risk-list-scroll">${listHtml}</div>
        `;
      }

      document.getElementById('modal').classList.add('show');
    }

    /**
     * Render Heatmap Baru: Profile Risiko Skala Rasio (Current) 5x5 (Skor 1 s/d 25)
     * Sesuai urutan & warna matriks pada contoh-heatmap.png
     */
    function profileCurrentHeatmap(data = allProfilRisiko) {
      const gridEl = document.getElementById('profileCurrentHeatmap');
      if (!gridEl) return;

      // Urutan angka matriks 5 baris x 5 kolom sesuai contoh-heatmap.png (dari baris atas ke bawah)
      // const matrixRows = [
      //   [11, 16, 20, 23, 25],
      //   [ 7, 12, 17, 21, 24],
      //   [ 4,  8, 13, 18, 22],
      //   [ 2,  5,  9, 14, 19],
      //   [ 1,  3,  6, 10, 15]
      // ];
      const matrixRows = [
        [11, 16, 20, 23, 25],
        [ 7, 12, 17, 21, 24],
        [ 4,  8, 13, 18, 22],
        [ 2,  6,  9, 14, 19],
        [ 1,  3,  5, 10, 15]
      ];

      // Hitung total Profile Risiko yang telah memiliki data Progress Monitoring Current
      const validCurrentList = (data || []).filter(p => parseInt(p.skala_risiko_current, 10) >= 1 && parseInt(p.skala_risiko_current, 10) <= 25);
      const tagCountEl = document.getElementById('tagProfileCurrentCount');
      if (tagCountEl) {
        tagCountEl.textContent = validCurrentList.length + ' RISIKO CURRENT';
      }

      let html = '';
      matrixRows.forEach(row => {
        row.forEach(score => {
          const matched = validCurrentList.filter(p => parseInt(p.skala_risiko_current, 10) === score);
          const count = matched.length;

          let colorClass = 'pcell-low';
          let levelLabel = 'Low';
          if (score >= 21) {
            colorClass = 'pcell-high';
            levelLabel = 'High';
          } else if (score >= 16) {
            colorClass = 'pcell-mod-high';
            levelLabel = 'Moderate to High';
          } else if (score >= 12) {
            colorClass = 'pcell-moderate';
            levelLabel = 'Moderate';
          } else if (score >= 6) {
            colorClass = 'pcell-low-mod';
            levelLabel = 'Low to Moderate';
          }

          const hasDataClass = count > 0 ? 'has-data' : '';
          const tooltipText = count > 0
            ? `Skala ${score} (${levelLabel}) — ${count} Profile Risiko Current: ` + matched.map(m => m.nama_risiko || '-').join(', ')
            : `Skala ${score} (${levelLabel}) — Belum ada risiko current`;

          html += `
            <div class="pcell ${colorClass} ${hasDataClass}" data-score="${score}" title="${tooltipText.replace(/"/g, '&quot;')}">
              <span class="pcell-score">${score}</span>
              ${count > 0 ? `<em>${count}</em>` : ''}
            </div>
          `;
        });
      });

      gridEl.innerHTML = html;

      gridEl.querySelectorAll('.pcell').forEach(cell => {
        cell.onclick = () => {
          const score = parseInt(cell.dataset.score, 10);
          const matched = validCurrentList.filter(p => parseInt(p.skala_risiko_current, 10) === score);
          openProfilCurrentModal(score, matched);
        };
      });
    }

    function getProfileScaleMeta(score) {
      const s = parseInt(score, 10) || 0;
      if (s >= 21) return { label: 'High', bg: '#e52e20', text: '#ffffff', desc: 'Risiko sangat tinggi / kritis — memerlukan tindakan mitigasi prioritas segera.' };
      if (s >= 16) return { label: 'Moderate to High', bg: '#f4b042', text: '#17243a', desc: 'Risiko menengah ke tinggi — memerlukan pemantauan intensif dan pengendalian.' };
      if (s >= 12) return { label: 'Moderate', bg: '#ffff00', text: '#17243a', desc: 'Risiko moderat — perlu tindak lanjut sesuai rencana mitigasi berkala.' };
      if (s >= 6)  return { label: 'Low to Moderate', bg: '#a9d08e', text: '#17243a', desc: 'Risiko rendah ke moderat — kondisi cukup terkendali dengan pengawasan rutin.' };
      if (s >= 1)  return { label: 'Low', bg: '#548235', text: '#ffffff', desc: 'Risiko rendah — berada dalam batas aman operasional.' };
      return { label: '-', bg: '#e2e8f0', text: '#64748b', desc: '' };
    }

    function openProfilCurrentModal(score, items) {
      const modalCard = document.getElementById('modalCard');
      const modalEyebrow = document.getElementById('modalEyebrow');
      if (modalCard) modalCard.classList.add('modal-card-wide');
      if (modalEyebrow) modalEyebrow.textContent = 'PROFILE RISIKO · PROGRESS MONITORING CURRENT';

      // Cari koordinat Dampak & Kemungkinan pada matriks 5x5 sesuai contoh-heatmap.png
      const matrixRows = [
        [11, 16, 20, 23, 25],
        [ 7, 12, 17, 21, 24],
        [ 4,  8, 13, 18, 22],
        [ 2,  5,  9, 14, 19],
        [ 1,  3,  6, 10, 15]
      ];
      let coordDampak = '-', coordProb = '-';
      matrixRows.forEach((row, rIdx) => {
        const cIdx = row.indexOf(score);
        if (cIdx !== -1) {
          coordDampak = 5 - rIdx;
          coordProb   = cIdx + 1;
        }
      });

      const levelInfo = getProfileScaleMeta(score);

      document.getElementById('modalTitle').textContent = 'Detail Distribusi Profile Risiko (Current)';

      const summaryHtml = `
        <div class="hm-modal-summary">
          <div class="hm-score-box" style="background:${levelInfo.bg};color:${levelInfo.text};">
            ${score}
          </div>
          <div class="hm-summary-meta">
            <strong>Level Risiko: ${levelInfo.label} (Skala ${score} / 25)</strong>
            <span>Matriks: Skala Dampak ${coordDampak} × Skala Kemungkinan ${coordProb} · ${levelInfo.desc}</span>
          </div>
          <div class="hm-summary-count">
            <b>${items ? items.length : 0}</b>
            <small>Risiko</small>
          </div>
        </div>
      `;

      if (!items || items.length === 0) {
        document.getElementById('modalBody').innerHTML = `
          ${summaryHtml}
          <div style="text-align:center;padding:24px 16px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;">
            <p style="color:var(--muted);font-size:13px;margin-bottom:12px;">
              Belum ada Daftar Profile Risiko dengan nilai Progress Monitoring Skala Rasio (Current) pada angka <strong>${score} (${levelInfo.label})</strong>.
            </p>
            <a href="<?= site_url('menrisk/daftar_profil_risiko') ?>" class="ghost" style="text-decoration:none;height:34px;font-size:12px;">
              📋 Buka Daftar Profile Risiko →
            </a>
          </div>
        `;
      } else {
        const listHtml = items.map((item, idx) => {
          const inhMeta = getProfileScaleMeta(item.skala_risiko_inheren);
          const resMeta = getProfileScaleMeta(item.skala_risiko_residual);
          const curMeta = getProfileScaleMeta(item.skala_risiko_current);

          return `
            <div class="hm-risk-card">
              <!-- Card Header -->
              <div class="hm-risk-head">
                <div class="hm-risk-title-wrap">
                  <span class="hm-risk-num">${idx + 1}</span>
                  <div>
                    <div class="hm-risk-title">${item.nama_risiko || '-'}</div>
                    <div class="hm-risk-subchips">
                      <span class="hm-chip dept">🏢 ${item.department_name || 'Tanpa Department'}</span>
                      ${item.kategori_risiko ? `<span class="hm-chip">🏷️ ${item.kategori_risiko}${item.kategori_risiko_nama ? ' - ' + item.kategori_risiko_nama : ''}</span>` : ''}
                      ${item.fungsi_kerja ? `<span class="hm-chip">⚙️ ${item.fungsi_kerja}</span>` : ''}
                    </div>
                  </div>
                </div>
                <span class="hm-chip period" title="Periode Progress Monitoring Terakhir">
                  📅 ${(item.periode_current || '-') + ' ' + (item.tahun_current || '')}
                </span>
              </div>

              <!-- 3-Column Score Progression Strip -->
              <div class="hm-score-strip" style="border-bottom: none;">
                <div class="hm-score-step">
                  <small>1. Risiko Inheren</small>
                  <div class="hm-score-badge-row">
                    <span class="hm-mini-score" style="background:${inhMeta.bg};color:${inhMeta.text};">
                      ${item.skala_risiko_inheren || '-'} · ${inhMeta.label}
                    </span>
                  </div>
                  <span class="hm-exp-val">Eksposur: Rp ${item.eksposure_kuantitatif || '0'}</span>
                </div>

                <div class="hm-score-step">
                  <small>2. Target Residual</small>
                  <div class="hm-score-badge-row">
                    <span class="hm-mini-score" style="background:${resMeta.bg};color:${resMeta.text};">
                      ${item.skala_risiko_residual || '-'} · ${resMeta.label}
                    </span>
                  </div>
                  <span class="hm-exp-val">Eksposur: Rp ${item.eksposure_residual || '0'}</span>
                </div>

                <div class="hm-score-step active-step">
                  <small>3. Realisasi Current</small>
                  <div class="hm-score-badge-row">
                    <span class="hm-mini-score" style="background:${curMeta.bg};color:${curMeta.text};">
                      ${item.skala_risiko_current} · ${curMeta.label}
                    </span>
                  </div>
                  <span class="hm-exp-val" style="color:#0f172a;">Eksposur: Rp ${item.eksposur_kuantitatif_current || '0'}</span>
                </div>
              </div>

              <!-- Card Footer -->
              <div class="hm-card-foot" style="justify-content: flex-end;">
                <a href="<?= site_url('menrisk/profil_risiko/') ?>${item.id}?mode=view" class="ghost" style="height:31px;padding:0 11px;font-size:11.5px;text-decoration:none;">
                  👁 Detail Formulir
                </a>
                <a href="<?= site_url('menrisk/monitoring_profil_risiko/') ?>${item.id}" class="primary" style="height:31px;padding:0 12px;font-size:11.5px;text-decoration:none;margin-left:0;">
                  📊 Progress Monitoring
                </a>
              </div>
            </div>
          `;
        }).join('');

        document.getElementById('modalBody').innerHTML = `
          ${summaryHtml}
          <div class="hm-risk-list-scroll">${listHtml}</div>
        `;
      }

      document.getElementById('modal').classList.add('show');
    }

    function trend() {
      const trendEl = document.getElementById('trendChart');
      if (!trendEl) return;
      const months = ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
            a = [82, 86, 81, 88, 91, 94],
            b = [58, 61, 56, 53, 57, 54];
      const points = v => v.map((n, i) => `${25 + i * 66},${170 - (n - 40) * 2}`).join(' ');
      trendEl.innerHTML = `
        <svg viewBox="0 0 390 185" role="img" aria-label="Tren skor eksposur">
          <g stroke="#e7ebf0" stroke-width="1">${[30, 70, 110, 150].map(y => `<line x1="24" y1="${y}" x2="370" y2="${y}"/>`).join('')}</g>
          <line x1="24" y1="120" x2="370" y2="120" stroke="#d93b45" stroke-dasharray="5 5"/>
          <polyline fill="none" stroke="#0a2342" stroke-width="3" points="${points(a)}"/>
          <polyline fill="none" stroke="#00a89d" stroke-width="3" points="${points(b)}"/>
          ${months.map((m, i) => `<text x="${25 + i * 66}" y="182" text-anchor="middle" font-size="9" fill="#7b8797">${m}</text>`).join('')}
          ${b.map((n, i) => `<circle cx="${25 + i * 66}" cy="${170 - (n - 40) * 2}" r="3" fill="#00a89d"/>`).join('')}
        </svg>
      `;
    }

    function openRisk(id) {
      const r = allRisks.find(x => x.id === id);
      if (!r) return;
      const modalCard = document.getElementById('modalCard');
      const modalEyebrow = document.getElementById('modalEyebrow');
      if (modalCard) modalCard.classList.remove('modal-card-wide');
      if (modalEyebrow) modalEyebrow.textContent = 'RISK DETAIL';

      document.getElementById('modalTitle').textContent = r.title;
      document.getElementById('modalBody').innerHTML = `
        <p>${r.mitigation}</p>
        <div class="detail-grid">
          <div><small>Risk owner</small><strong>${r.owner}</strong></div>
          <div><small>Department</small><strong>${r.dir}</strong></div>
          <div><small>Inherent score</small><strong>${r.inherent} · Ekstrem</strong></div>
          <div><small>Residual score</small><strong>${r.residual}</strong></div>
          <div><small>Progres mitigasi</small><strong>${r.progress}%</strong></div>
          <div><small>Target selesai</small><strong>${r.due}</strong></div>
        </div>
        ${r.decision ? `<div class="decision"><strong>Keputusan yang dibutuhkan dari BOD</strong><br>${r.decision}</div>` : ''}
      `;
      document.getElementById('modal').classList.add('show');
    }

    function changeView(id) {
      document.querySelectorAll('.view').forEach(v => v.classList.remove('active-view'));
      const target = document.getElementById(id);
      if (target) target.classList.add('active-view');
      document.querySelectorAll('.nav').forEach(n => n.classList.toggle('active', n.dataset.view === id));
      document.querySelector('.sidebar').classList.remove('open');
    }

    // Event Bindings
    document.querySelectorAll('.nav[data-view]').forEach(n => n.onclick = () => changeView(n.dataset.view));
    document.querySelectorAll('[data-jump]').forEach(n => n.onclick = () => changeView(n.dataset.jump));
    document.querySelector('.menu').onclick = () => document.querySelector('.sidebar').classList.toggle('open');
    document.getElementById('closeModal').onclick = () => document.getElementById('modal').classList.remove('show');
    document.getElementById('modal').onclick = e => {
      if (e.target.id === 'modal') e.currentTarget.classList.remove('show');
    };
    document.getElementById('showCritical').onclick = () => openRisk('R-001');

    // Modal Tambah Risiko Bindings
    const modalAddRisk = document.getElementById('modalAddRisk');
    const btnOpenAddRisk = document.getElementById('btnOpenAddRisk');
    const closeAddRiskModal = document.getElementById('closeAddRiskModal');
    const btnCancelAddRisk = document.getElementById('btnCancelAddRisk');
    const formAddRisk = document.getElementById('formAddRisk');

    if (btnOpenAddRisk) {
      btnOpenAddRisk.onclick = () => {
        // Ambil ID risiko terbaru
        fetch('<?= site_url("menrisk/next_id") ?>')
          .then(r => r.json())
          .then(res => {
            if (res && res.status === 'success' && res.next_id) {
              document.getElementById('new_risk_id').value = res.next_id;
            }
          })
          .catch(() => {});
        modalAddRisk.classList.add('show');
      };
    }

    if (closeAddRiskModal) closeAddRiskModal.onclick = () => modalAddRisk.classList.remove('show');
    if (btnCancelAddRisk) btnCancelAddRisk.onclick = () => modalAddRisk.classList.remove('show');
    if (modalAddRisk) {
      modalAddRisk.onclick = e => {
        if (e.target.id === 'modalAddRisk') modalAddRisk.classList.remove('show');
      };
    }

    // Kalkulasi Skor Inherent Realtime
    function updateInherentScore() {
      const l = parseInt(document.getElementById('new_inh_l').value) || 1,
            i = parseInt(document.getElementById('new_inh_i').value) || 1,
            score = l * i;
      document.getElementById('new_inherent').value = score;
      const badge = document.getElementById('lblInherentBadge');
      badge.textContent = 'Skor: ' + score;
      badge.style.background = colors(score);
    }
    document.getElementById('new_inh_l').onchange = updateInherentScore;
    document.getElementById('new_inh_i').onchange = updateInherentScore;

    // Kalkulasi Skor Residual Realtime & Rekomendasi Appetite
    function updateResidualScore() {
      const l = parseInt(document.getElementById('new_res_l').value) || 1,
            i = parseInt(document.getElementById('new_res_i').value) || 1,
            score = l * i;
      document.getElementById('new_residual').value = score;
      const badge = document.getElementById('lblResidualBadge');
      badge.textContent = 'Skor: ' + score;
      badge.style.background = colors(score);

      // Rekomendasi Appetite otomatis
      const appSelect = document.getElementById('new_appetite');
      if (score >= 15) {
        appSelect.value = 'Melebihi';
      } else if (score >= 10) {
        appSelect.value = 'Mendekati';
      } else {
        appSelect.value = 'Dalam';
      }
    }
    document.getElementById('new_res_l').onchange = updateResidualScore;
    document.getElementById('new_res_i').onchange = updateResidualScore;

    // Range Slider Progress
    document.getElementById('new_progress').oninput = e => {
      document.getElementById('lblProgress').textContent = e.target.value + '%';
    };

    // Submit Form Tambah Risiko via AJAX
    if (formAddRisk) {
      formAddRisk.onsubmit = function(e) {
        e.preventDefault();
        const btnSubmit = document.getElementById('btnSubmitRisk');
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Menyimpan...';

        const formData = new FormData(this);

        fetch('<?= site_url("menrisk/store_risk") ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(res => {
          btnSubmit.disabled = false;
          btnSubmit.textContent = 'Simpan Risiko';

          if (res.status === 'success') {
            modalAddRisk.classList.remove('show');
            formAddRisk.reset();
            document.getElementById('lblProgress').textContent = '0%';
            updateInherentScore();
            updateResidualScore();

            // Tampilkan Toast
            const t = document.getElementById('toast');
            t.textContent = res.message || 'Risiko baru berhasil ditambahkan!';
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3500);

            // Refresh dataset dashboard via AJAX
            fetch('<?= site_url("menrisk?ajax=1") ?>')
              .then(r => r.json())
              .then(dashData => {
                if (dashData && dashData.status === 'success') {
                  allRisks = dashData.data.risks;
                  allKri   = dashData.data.kri;
                  kpis     = dashData.data.kpis;
                  if (dashData.data.profil_risiko) {
                    allProfilRisiko = dashData.data.profil_risiko;
                  }

                  // Update KPI UI
                  const elTotal = document.getElementById('totalRisk');
                  if (elTotal) elTotal.textContent = kpis.total_risks;
                  const elCrit = document.getElementById('criticalRisk');
                  if (elCrit) elCrit.textContent = kpis.critical_risks;
                  const elAlert = document.getElementById('alertCount');
                  if (elAlert) elAlert.textContent = kpis.critical_risks;

                  applyFilter();
                  heatmap();
                  profileCurrentHeatmap();
                  changeView('register');
                }
              });
          } else {
            alert(res.message || 'Gagal menyimpan data risiko.');
          }
        })
        .catch(err => {
          btnSubmit.disabled = false;
          btnSubmit.textContent = 'Simpan Risiko';
          alert('Terjadi kesalahan jaringan: ' + err.message);
        });
      };
    }

    document.getElementById('search').oninput = e => {
      const q = e.target.value.toLowerCase();
      renderTable(allRisks.filter(r => (r.title + r.owner + r.id).toLowerCase().includes(q)));
    };

    function applyFilter() {
      const deptEl = document.getElementById('department') || document.getElementById('directorate');
      const d = deptEl ? deptEl.value : 'all',
            c = document.getElementById('category').value;
      const f = allRisks.filter(r => (d === 'all' || r.dir === d || String(r.department_id) === String(d)) && (c === 'all' || r.cat === c));
      renderTop(f);
      renderTable(f);
      renderMitigations(f);
      document.getElementById('totalRisk').textContent = f.length;
      document.getElementById('criticalRisk').textContent = f.filter(r => r.residual >= 15).length;

      const fkri = allKri.filter(k => (d === 'all' || k.directorate === d || String(k.department_id) === String(d)));
      renderKri(fkri);

      const fProfil = (allProfilRisiko || []).filter(p => (d === 'all' || p.department_name === d || String(p.department_id) === String(d)));
      profileCurrentHeatmap(fProfil);
    }

    const deptFilter = document.getElementById('department') || document.getElementById('directorate');
    if (deptFilter) deptFilter.onchange = applyFilter;
    document.getElementById('category').onchange = applyFilter;
    document.getElementById('reset').onclick = () => {
      if (deptFilter) deptFilter.value = 'all';
      document.getElementById('category').value = 'all';
      document.getElementById('search').value = '';
      applyFilter();
    };

    document.getElementById('export').onclick = () => {
      const t = document.getElementById('toast');
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2600);
      // Panggil endpoint export_brief jika diinginkan
      // window.open('<?= site_url("menrisk/export_brief") ?>', '_blank');
    };

    // Inisialisasi awal
    heatmap();
    profileCurrentHeatmap();
    trend();
    renderTop();
    renderTable();
    renderMitigations();
    renderKri();

    // Auto switch view jika ada hash pada URL (misal #register dari halaman lain)
    const hash = window.location.hash.replace('#', '');
    if (hash && ['overview', 'register', 'mitigation', 'kri'].includes(hash)) {
      changeView(hash);
    }
  </script>
<?php $this->load->view('templates/footer'); ?>
