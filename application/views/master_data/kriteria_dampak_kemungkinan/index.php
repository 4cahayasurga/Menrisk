<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Master Data — Kriteria Dampak & Kemungkinan']);
?>
<!-- Sidebar Navigation Template -->
<?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu]); ?>

<style>
/* ==========================================================================
   KRITERIA DAMPAK & KEMUNGKINAN CUSTOM STYLES
   ========================================================================== */
.kriteria-tabs-bar {
  display: flex;
  gap: 10px;
  border-bottom: 2px solid #e2e8f0;
  margin-bottom: 20px;
  overflow-x: auto;
}
.kriteria-tab-btn {
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  padding: 10px 18px;
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
.kriteria-tab-btn:hover {
  color: var(--navy, #0f172a);
}
.kriteria-tab-btn.active {
  color: var(--navy, #0f172a);
  border-bottom-color: var(--navy, #0f172a);
  font-weight: 700;
}
.tab-badge {
  display: inline-block;
  padding: 2px 7px;
  border-radius: 12px;
  font-size: 11px;
  background: #f1f5f9;
  color: #475569;
}
.kriteria-tab-btn.active .tab-badge {
  background: #e2e8f0;
  color: #0f172a;
}
.badge-skala {
  display: inline-block;
  /* min-width: 26px; */
  padding: auto;
  /* height: 26px; */
  line-height: 24px;
  text-align: center;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 700;
  border: 1px solid transparent;
  width: max-content;
  padding: 0 10px;
}
.badge-skala-1 { background: #e6f4ea; color: #137333; border-color: #ceead6; }
.badge-skala-2 { background: #e8f0fe; color: #1a73e8; border-color: #d2e3fc; }
.badge-skala-3 { background: #fef7e0; color: #b06000; border-color: #feefc3; }
.badge-skala-4 { background: #feefe3; color: #d93025; border-color: #fad2cf; }
.badge-skala-5 { background: #fce8e6; color: #c5221f; border-color: #f6aea9; }

.cell-truncate {
  max-width: 280px;
  white-space: normal;
  line-height: 1.45;
  font-size: 12.5px;
}
.scale-col-header {
  min-width: 170px;
  font-size: 11.5px;
  text-transform: uppercase;
  letter-spacing: .02em;
}
</style>

<!-- Main Content Area -->
<main>
    <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
        <p class="eyebrow">MASTER DATA · KRITERIA DAMPAK & KEMUNGKINAN</p>
        <h1>Kriteria Dampak & Kemungkinan</h1>
        <p class="subtitle">Pengaturan standar skala evaluasi risiko: Dampak Kuantitatif, Dampak Kualitatif, dan Probabilitas.</p>
    </div>
    <div class="header-actions">
        <button class="primary" id="btnMainAdd" onclick="openCurrentAddModal()">+ Tambah Data</button>
        <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
        </div>
    </div>
    </header>

    <section class="panel full">
        <!-- Tab Navigation Bar -->
        <div class="kriteria-tabs-bar">
            <button type="button" class="kriteria-tab-btn active" data-tab="kuantitatif" onclick="switchKriteriaTab('kuantitatif')">
                <span>1. Skala Dampak Kuantitatif</span>
                <span class="tab-badge" id="badge-count-kuantitatif">-</span>
            </button>
            <button type="button" class="kriteria-tab-btn" data-tab="kualitatif" onclick="switchKriteriaTab('kualitatif')">
                <span>2. Skala Dampak Kualitatif</span>
                <span class="tab-badge" id="badge-count-kualitatif">-</span>
            </button>
            <button type="button" class="kriteria-tab-btn" data-tab="probabilitas" onclick="switchKriteriaTab('probabilitas')">
                <span>3. Skala Probabilitas</span>
                <span class="tab-badge" id="badge-count-probabilitas">-</span>
            </button>
        </div>

        <!-- Panel Head & Search Toolbar -->
        <div class="panel-head" style="align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <div>
                <h2 id="tabContentTitle">Skala Dampak Kuantitatif</h2>
                <p id="tabContentSubtitle">Tabel parameter skala dampak berbasis nominal kerugian finansial kuantitatif.</p>
            </div>
            <!-- Minimalist Toolbar Controls -->
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-left: auto;">
                <input type="text" id="kriteriaSearchInput" placeholder="Cari data pada tabel..."
                    onkeyup="filterActiveTable(this.value)"
                    style="height: 35px; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; min-width: 260px; background: #fff;" 
                />
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 1: TABLE SKALA DAMPAK KUANTITATIF                             -->
        <!-- ================================================================= -->
        <div class="tab-pane-container" id="container-kuantitatif">
            <div class="table-wrap">
                <table id="table-kuantitatif" class="table" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="width: 70px; text-align: center;">No</th>
                            <th style="width: 100px; text-align: center;">Skala</th>
                            <th style="width: 220px;">Kriteria</th>
                            <th>Deskripsi / Batasan Kuantitatif</th>
                            <th style="width: 150px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted); padding: 32px;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 2: TABLE SKALA DAMPAK KUALITATIF                              -->
        <!-- ================================================================= -->
        <div class="tab-pane-container d-none" id="container-kualitatif">
            <div class="table-wrap">
                <table id="table-kualitatif" class="table" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="width: 60px; text-align: center;">No</th>
                            <th style="width: 180px;">Aspek / Judul</th>
                            <th style="width: 200px;">Risiko</th>
                            <th class="scale-col-header" style="background: #f4fbf6;">1 · Sangat Rendah</th>
                            <th class="scale-col-header" style="background: #f4f8fe;">2 · Rendah</th>
                            <th class="scale-col-header" style="background: #fefcf3;">3 · Moderat</th>
                            <th class="scale-col-header" style="background: #fef8f4;">4 · Tinggi</th>
                            <th class="scale-col-header" style="background: #fcf4f4;">5 · Sangat Tinggi</th>
                            <th style="width: 130px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--muted); padding: 32px;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 3: TABLE SKALA PROBABILITAS                                   -->
        <!-- ================================================================= -->
        <div class="tab-pane-container d-none" id="container-probabilitas">
            <div class="table-wrap">
                <table id="table-probabilitas" class="table" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="width: 60px; text-align: center;">No</th>
                            <th style="width: 220px;">Parameter Probabilitas</th>
                            <th class="scale-col-header" style="background: #f4fbf6;">1 · Sangat Jarang Terjadi</th>
                            <th class="scale-col-header" style="background: #f4f8fe;">2 · Jarang Terjadi</th>
                            <th class="scale-col-header" style="background: #fefcf3;">3 · Bisa Terjadi</th>
                            <th class="scale-col-header" style="background: #fef8f4;">4 · Sangat Mungkin Terjadi</th>
                            <th class="scale-col-header" style="background: #fcf4f4;">5 · Hampir Pasti Terjadi</th>
                            <th style="width: 130px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--muted); padding: 32px;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<!-- ========================================================================= -->
<!-- MODAL 1: FORM SKALA DAMPAK KUANTITATIF                                    -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="modalKuantitatif">
    <div class="modal-box" style="width: min(540px, 100%);">
        <button type="button" class="modal-close" onclick="closeModal('modalKuantitatif')" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="modalKuantitatifTitle">Tambah Skala Dampak Kuantitatif</h2>
            <p>Masukkan skala peringkat, label kriteria, dan deskripsi kuantitatif.</p>
        </div>

        <form id="formKuantitatif" onsubmit="submitKuantitatif(event)">
            <input type="hidden" id="kuantitatif_id" name="id" />

            <div class="form-row">
                <label for="kuantitatif_skala">Tingkat Skala (1 s/d 5) *</label>
                <select id="kuantitatif_skala" name="skala" required>
                    <option value="">-- Pilih Skala --</option>
                    <option value="1">Skala 1 - Sangat Rendah</option>
                    <option value="2">Skala 2 - Rendah</option>
                    <option value="3">Skala 3 - Moderat</option>
                    <option value="4">Skala 4 - Tinggi</option>
                    <option value="5">Skala 5 - Sangat Tinggi</option>
                </select>
            </div>

            <div class="form-row">
                <label for="range_kriteria">Range Dampak*</label>
                <input type="text" id="range_kriteria" name="range" required placeholder="Contoh: X ≤ 20% dari Batasan Risiko" />
            </div>

            <div class="form-row">
                <label for="kuantitatif_deskripsi">Deskripsi / Batasan Kuantitatif *</label>
                <textarea id="kuantitatif_deskripsi" name="deskripsi" required rows="3" placeholder="Contoh: Kerugian finansial ≤ Rp 50.000.000"></textarea>
            </div>

            <div class="modal-foot">
                <button type="button" class="ghost" onclick="closeModal('modalKuantitatif')">Batal</button>
                <button type="submit" class="primary" id="btnSaveKuantitatif" style="margin-left: 0;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: FORM SKALA DAMPAK KUALITATIF                                     -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="modalKualitatif">
    <div class="modal-box" style="width: min(780px, 100%); max-height: 90vh; overflow-y: auto;">
        <button type="button" class="modal-close" onclick="closeModal('modalKualitatif')" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="modalKualitatifTitle">Tambah Skala Dampak Kualitatif</h2>
            <p>Masukkan aspek/judul, risiko, serta rincian dampak kualitatif skala 1 sampai skala 5.</p>
        </div>

        <form id="formKualitatif" onsubmit="submitKualitatif(event)">
            <input type="hidden" id="kualitatif_id" name="id" />

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-row" style="margin-bottom: 0;">
                    <label for="kualitatif_judul">Aspek / Judul Kualitatif *</label>
                    <input type="text" id="kualitatif_judul" name="judul" required placeholder="Contoh: Operasional / Reputasi" />
                </div>
                <div class="form-row" style="margin-bottom: 0;">
                    <label for="kualitatif_risiko">Risiko / Area Dampak *</label>
                    <input type="text" id="kualitatif_risiko" name="risiko" required placeholder="Contoh: Gangguan layanan & operasional" />
                </div>
            </div>

            <div style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                <p style="font-size: 12px; font-weight: 700; color: var(--navy); margin-bottom: 10px; text-transform: uppercase; letter-spacing: .02em;">Deskripsi Dampak per Tingkat Skala</p>

                <div class="form-row">
                    <label for="kualitatif_skala_1" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-1">1</span> <b>Skala 1 (Sangat Rendah) *</b>
                    </label>
                    <textarea id="kualitatif_skala_1" name="skala_1" required rows="2" placeholder="Deskripsi dampak tingkat 1..."></textarea>
                </div>

                <div class="form-row">
                    <label for="kualitatif_skala_2" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-2">2</span> <b>Skala 2 (Rendah) *</b>
                    </label>
                    <textarea id="kualitatif_skala_2" name="skala_2" required rows="2" placeholder="Deskripsi dampak tingkat 2..."></textarea>
                </div>

                <div class="form-row">
                    <label for="kualitatif_skala_3" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-3">3</span> <b>Skala 3 (Moderat) *</b>
                    </label>
                    <textarea id="kualitatif_skala_3" name="skala_3" required rows="2" placeholder="Deskripsi dampak tingkat 3..."></textarea>
                </div>

                <div class="form-row">
                    <label for="kualitatif_skala_4" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-4">4</span> <b>Skala 4 (Tinggi) *</b>
                    </label>
                    <textarea id="kualitatif_skala_4" name="skala_4" required rows="2" placeholder="Deskripsi dampak tingkat 4..."></textarea>
                </div>

                <div class="form-row">
                    <label for="kualitatif_skala_5" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-5">5</span> <b>Skala 5 (Sangat Tinggi) *</b>
                    </label>
                    <textarea id="kualitatif_skala_5" name="skala_5" required rows="2" placeholder="Deskripsi dampak tingkat 5..."></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="ghost" onclick="closeModal('modalKualitatif')">Batal</button>
                <button type="submit" class="primary" id="btnSaveKualitatif" style="margin-left: 0;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: FORM SKALA PROBABILITAS                                          -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="modalProbabilitas">
    <div class="modal-box" style="width: min(780px, 100%); max-height: 90vh; overflow-y: auto;">
        <button type="button" class="modal-close" onclick="closeModal('modalProbabilitas')" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="modalProbabilitasTitle">Tambah Skala Probabilitas</h2>
            <p>Masukkan parameter probabilitas serta kriteria kemungkinan skala 1 sampai skala 5.</p>
        </div>

        <form id="formProbabilitas" onsubmit="submitProbabilitas(event)">
            <input type="hidden" id="probabilitas_id" name="id" />

            <div class="form-row">
                <label for="probabilitas_parameter">Parameter Probabilitas *</label>
                <input type="text" id="probabilitas_parameter" name="parameter" required placeholder="Contoh: Frekuensi Kejadian Tahunan / Persentase Kemungkinan" />
            </div>

            <div style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                <p style="font-size: 12px; font-weight: 700; color: var(--navy); margin-bottom: 10px; text-transform: uppercase; letter-spacing: .02em;">Deskripsi Probabilitas per Tingkat Skala</p>

                <div class="form-row">
                    <label for="probabilitas_skala_1" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-1">1</span> <b>Skala 1 (Sangat Jarang Terjadi) *</b>
                    </label>
                    <textarea id="probabilitas_skala_1" name="skala_1" required rows="2" placeholder="Contoh: Terjadi < 1 kali dalam 1 tahun..."></textarea>
                </div>

                <div class="form-row">
                    <label for="probabilitas_skala_2" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-2">2</span> <b>Skala 2 (Jarang Terjadi) *</b>
                    </label>
                    <textarea id="probabilitas_skala_2" name="skala_2" required rows="2" placeholder="Contoh: Terjadi 1 s/d 2 kali dalam 1 tahun..."></textarea>
                </div>

                <div class="form-row">
                    <label for="probabilitas_skala_3" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-3">3</span> <b>Skala 3 (Bisa Terjadi) *</b>
                    </label>
                    <textarea id="probabilitas_skala_3" name="skala_3" required rows="2" placeholder="Contoh: Terjadi 3 s/d 5 kali dalam 1 tahun..."></textarea>
                </div>

                <div class="form-row">
                    <label for="probabilitas_skala_4" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-4">4</span> <b>Skala 4 (Sangat Mungkin Terjadi) *</b>
                    </label>
                    <textarea id="probabilitas_skala_4" name="skala_4" required rows="2" placeholder="Contoh: Terjadi 6 s/d 12 kali dalam 1 tahun..."></textarea>
                </div>

                <div class="form-row">
                    <label for="probabilitas_skala_5" style="display: flex; align-items: center; gap: 6px;">
                        <span class="badge-skala badge-skala-5">5</span> <b>Skala 5 (Hampir Pasti Terjadi) *</b>
                    </label>
                    <textarea id="probabilitas_skala_5" name="skala_5" required rows="2" placeholder="Contoh: Terjadi > 12 kali dalam 1 tahun..."></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="ghost" onclick="closeModal('modalProbabilitas')">Batal</button>
                <button type="submit" class="primary" id="btnSaveProbabilitas" style="margin-left: 0;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: CLEAN AJAX TAB & CRUD LOGIC (JQUERY 3)                        -->
<!-- ========================================================================= -->
<script>
    // State tab aktif
    let currentTab = 'kuantitatif';

    // Metadata judul dan subjudul per tab
    const TAB_INFO = {
        kuantitatif: {
            title: 'Skala Dampak Kuantitatif',
            subtitle: 'Tabel parameter skala dampak berbasis nominal kerugian finansial kuantitatif.',
            btnAdd: '+ Tambah Dampak Kuantitatif'
        },
        kualitatif: {
            title: 'Skala Dampak Kualitatif',
            subtitle: 'Tabel parameter skala dampak berbasis kategori aspek kualitatif operasional & non-finansial.',
            btnAdd: '+ Tambah Dampak Kualitatif'
        },
        probabilitas: {
            title: 'Skala Probabilitas',
            subtitle: 'Tabel skala kemungkinan/frekuensi keterjadian risiko dari skala 1 sampai 5.',
            btnAdd: '+ Tambah Probabilitas'
        }
    };

    // Cache data per tab untuk pencarian cepat & edit modal prefill
    const cachedData = {
        kuantitatif: [],
        kualitatif: [],
        probabilitas: []
    };

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
     * Berpindah tab secara dinamis tanpa reload halaman
     */
    function switchKriteriaTab(tabName) {
        if (!TAB_INFO[tabName]) return;
        currentTab = tabName;

        // Update tombol tab aktif
        $('.kriteria-tab-btn').removeClass('active');
        $(`.kriteria-tab-btn[data-tab="${tabName}"]`).addClass('active');

        // Update header section teks
        $('#tabContentTitle').text(TAB_INFO[tabName].title);
        $('#tabContentSubtitle').text(TAB_INFO[tabName].subtitle);
        $('#btnMainAdd').text(TAB_INFO[tabName].btnAdd);

        // Tampilkan kontainer tabel yang sesuai
        $('.tab-pane-container').addClass('d-none');
        $(`#container-${tabName}`).removeClass('d-none');

        // Reset input search
        $('#kriteriaSearchInput').val('');

        // Ambil data terbaru melalui AJAX
        loadTabData(tabName);
    }

    /**
     * Memuat data tabel aktif melalui AJAX
     */
    function loadTabData(tabName) {
        const $tbody = $(`#table-${tabName} tbody`);
        const colSpan = tabName === 'kuantitatif' ? 5 : (tabName === 'kualitatif' ? 9 : 8);

        $tbody.html(`
            <tr>
                <td colspan="${colSpan}" style="text-align: center; color: var(--muted); padding: 32px;">
                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                        <span>Memuat data ${TAB_INFO[tabName].title}...</span>
                    </div>
                </td>
            </tr>
        `);

        $.ajax({
            url: '<?= site_url("MasterData/KriteriaDampakKemungkinan/get_data/") ?>' + tabName,
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 'success') {
                    cachedData[tabName] = res.data || [];
                    $(`#badge-count-${tabName}`).text(cachedData[tabName].length);
                    renderTable(tabName, cachedData[tabName]);
                } else {
                    $tbody.html(`
                        <tr>
                            <td colspan="${colSpan}" style="text-align: center; color: var(--red); padding: 24px;">
                                Gagal memuat data: ${escapeHtml(res.message || 'Terjadi kesalahan.')}
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $tbody.html(`
                    <tr>
                        <td colspan="${colSpan}" style="text-align: center; color: var(--red); padding: 24px;">
                            Terjadi kesalahan koneksi saat memuat data.
                        </td>
                    </tr>
                `);
            }
        });
    }

    /**
     * Render data ke HTML Table
     */
    function renderTable(tabName, items) {
        const $tbody = $(`#table-${tabName} tbody`);
        $tbody.empty();

        const colSpan = tabName === 'kuantitatif' ? 5 : (tabName === 'kualitatif' ? 9 : 8);

        if (!items || items.length === 0) {
            $tbody.html(`
                <tr class="row-empty">
                    <td colspan="${colSpan}" style="text-align: center; color: var(--muted); padding: 32px;">
                        Belum ada data ${escapeHtml(TAB_INFO[tabName].title)}. Klik tombol <b>+ Tambah Data</b> untuk menambahkan.
                    </td>
                </tr>
            `);
            return;
        }

        let rowsHtml = '';

        if (tabName === 'kuantitatif') {
            items.forEach((item, idx) => {
                const skalaClass = `badge-skala-${item.skala || 1}`;
                var skalaLevel = '';
                switch (item.skala) {
                    case 1:
                        skalaLevel = 'Sangat Rendah';
                        break;
                    case 2:
                        skalaLevel = 'Rendah';
                        break;
                    case 3:
                        skalaLevel = 'Moderat';
                        break;
                    case 4:
                        skalaLevel = 'Tinggi';
                        break;
                    case 5:
                        skalaLevel = 'Sangat Tinggi';
                        break;
                    default:
                        skalaLevel = '-';
                        break;
                }
                rowsHtml += `
                    <tr id="row-${tabName}-${item.id}" data-search="${escapeHtml((item.skala + ' ' + item.kriteria + ' ' + item.deskripsi).toLowerCase())}">
                        <td style="text-align: center;">${idx + 1}</td>
                        <td style="text-align: center;">
                            <span class="badge-skala ${skalaClass}">${escapeHtml(item.skala)} - ${skalaLevel}</span>
                        </td>
                        <td><b>${escapeHtml(item.range)}</b></td>
                        <td class="cell-truncate">${escapeHtml(item.deskripsi)}</td>
                        <td style="text-align: right;">
                            <button type="button" class="btn-edit" onclick="openEditModal('kuantitatif', ${item.id})">Edit</button>
                            <button type="button" class="btn-delete" onclick="deleteItem('kuantitatif', ${item.id}, 'Skala ${escapeHtml(item.skala)} - ${escapeHtml(item.kriteria)}')">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        } else if (tabName === 'kualitatif') {
            items.forEach((item, idx) => {
                const searchStr = (item.judul + ' ' + item.risiko + ' ' + item.skala_1 + ' ' + item.skala_2 + ' ' + item.skala_3 + ' ' + item.skala_4 + ' ' + item.skala_5).toLowerCase();
                rowsHtml += `
                    <tr id="row-${tabName}-${item.id}" data-search="${escapeHtml(searchStr)}">
                        <td style="text-align: center;">${idx + 1}</td>
                        <td><b>${escapeHtml(item.judul)}</b></td>
                        <td class="cell-truncate">${escapeHtml(item.risiko)}</td>
                        <td class="cell-truncate" style="background: #fafdfb;">${escapeHtml(item.skala_1)}</td>
                        <td class="cell-truncate" style="background: #fafcff;">${escapeHtml(item.skala_2)}</td>
                        <td class="cell-truncate" style="background: #fffdf9;">${escapeHtml(item.skala_3)}</td>
                        <td class="cell-truncate" style="background: #fffbfa;">${escapeHtml(item.skala_4)}</td>
                        <td class="cell-truncate" style="background: #fdf8f8;">${escapeHtml(item.skala_5)}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn-edit" onclick="openEditModal('kualitatif', ${item.id})">Edit</button>
                            <button type="button" class="btn-delete" onclick="deleteItem('kualitatif', ${item.id}, '${escapeHtml(item.judul)}')">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        } else if (tabName === 'probabilitas') {
            items.forEach((item, idx) => {
                const searchStr = (item.parameter + ' ' + item.skala_1 + ' ' + item.skala_2 + ' ' + item.skala_3 + ' ' + item.skala_4 + ' ' + item.skala_5).toLowerCase();
                rowsHtml += `
                    <tr id="row-${tabName}-${item.id}" data-search="${escapeHtml(searchStr)}">
                        <td style="text-align: center;">${idx + 1}</td>
                        <td><b>${escapeHtml(item.parameter)}</b></td>
                        <td class="cell-truncate" style="background: #fafdfb;">${escapeHtml(item.skala_1)}</td>
                        <td class="cell-truncate" style="background: #fafcff;">${escapeHtml(item.skala_2)}</td>
                        <td class="cell-truncate" style="background: #fffdf9;">${escapeHtml(item.skala_3)}</td>
                        <td class="cell-truncate" style="background: #fffbfa;">${escapeHtml(item.skala_4)}</td>
                        <td class="cell-truncate" style="background: #fdf8f8;">${escapeHtml(item.skala_5)}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn-edit" onclick="openEditModal('probabilitas', ${item.id})">Edit</button>
                            <button type="button" class="btn-delete" onclick="deleteItem('probabilitas', ${item.id}, '${escapeHtml(item.parameter)}')">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        }

        $tbody.html(rowsHtml);
    }

    /**
     * Filter pencarian real-time pada tabel aktif
     */
    function filterActiveTable(query) {
        const q = $.trim(query).toLowerCase();
        $(`#table-${currentTab} tbody tr`).each(function() {
            if ($(this).hasClass('row-empty')) return;
            const searchData = $(this).attr('data-search') || $(this).text().toLowerCase();
            if (searchData.indexOf(q) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    /**
     * Buka modal Tambah sesuai tab aktif
     */
    function openCurrentAddModal() {
        if (currentTab === 'kuantitatif') {
            $('#formKuantitatif')[0].reset();
            $('#kuantitatif_id').val('');
            $('#modalKuantitatifTitle').text('Tambah Skala Dampak Kuantitatif');
            openModal('modalKuantitatif');
        } else if (currentTab === 'kualitatif') {
            $('#formKualitatif')[0].reset();
            $('#kualitatif_id').val('');
            $('#modalKualitatifTitle').text('Tambah Skala Dampak Kualitatif');
            openModal('modalKualitatif');
        } else if (currentTab === 'probabilitas') {
            $('#formProbabilitas')[0].reset();
            $('#probabilitas_id').val('');
            $('#modalProbabilitasTitle').text('Tambah Skala Probabilitas');
            openModal('modalProbabilitas');
        }
    }

    /**
     * Buka modal Edit dan prefill data
     */
    function openEditModal(tabName, id) {
        const item = cachedData[tabName].find(x => parseInt(x.id, 10) === parseInt(id, 10));
        if (!item) {
            // Ambil dari server jika belum ada di cache
            $.getJSON('<?= site_url("MasterData/KriteriaDampakKemungkinan/get_item/") ?>' + tabName + '/' + id, function(res) {
                if (res.status === 'success' && res.data) {
                    fillAndOpenEdit(tabName, res.data);
                } else {
                    showToast('Gagal memuat data detail.');
                }
            });
            return;
        }

        fillAndOpenEdit(tabName, item);
    }

    function fillAndOpenEdit(tabName, item) {
        if (tabName === 'kuantitatif') {
            $('#kuantitatif_id').val(item.id);
            $('#kuantitatif_skala').val(item.skala);
            $('#kuantitatif_kriteria').val(item.kriteria);
            $('#kuantitatif_deskripsi').val(item.deskripsi);
            $('#modalKuantitatifTitle').text('Edit Skala Dampak Kuantitatif');
            openModal('modalKuantitatif');
        } else if (tabName === 'kualitatif') {
            $('#kualitatif_id').val(item.id);
            $('#kualitatif_judul').val(item.judul);
            $('#kualitatif_risiko').val(item.risiko);
            $('#kualitatif_skala_1').val(item.skala_1);
            $('#kualitatif_skala_2').val(item.skala_2);
            $('#kualitatif_skala_3').val(item.skala_3);
            $('#kualitatif_skala_4').val(item.skala_4);
            $('#kualitatif_skala_5').val(item.skala_5);
            $('#modalKualitatifTitle').text('Edit Skala Dampak Kualitatif');
            openModal('modalKualitatif');
        } else if (tabName === 'probabilitas') {
            $('#probabilitas_id').val(item.id);
            $('#probabilitas_parameter').val(item.parameter);
            $('#probabilitas_skala_1').val(item.skala_1);
            $('#probabilitas_skala_2').val(item.skala_2);
            $('#probabilitas_skala_3').val(item.skala_3);
            $('#probabilitas_skala_4').val(item.skala_4);
            $('#probabilitas_skala_5').val(item.skala_5);
            $('#modalProbabilitasTitle').text('Edit Skala Probabilitas');
            openModal('modalProbabilitas');
        }
    }

    function openModal(modalId) {
        $('#' + modalId).addClass('show');
    }

    function closeModal(modalId) {
        $('#' + modalId).removeClass('show');
    }

    /**
     * Submit Form: Skala Dampak Kuantitatif
     */
    function submitKuantitatif(e) {
        if (e && e.preventDefault) e.preventDefault();

        const id = $('#kuantitatif_id').val();
        const isEdit = !!id;
        const url = isEdit
            ? '<?= site_url("MasterData/KriteriaDampakKemungkinan/update/kuantitatif") ?>'
            : '<?= site_url("MasterData/KriteriaDampakKemungkinan/save/kuantitatif") ?>';

        const $btn = $('#btnSaveKuantitatif');
        $btn.prop('disabled', true).text('Menyimpan...');

        const formData = new FormData($('#formKuantitatif')[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('modalKuantitatif');
                    showToast(res.message);
                    loadTabData('kuantitatif');
                } else {
                    showToast(res.message || 'Gagal menyimpan data.');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan koneksi saat menyimpan data.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Simpan Data');
            }
        });
    }

    /**
     * Submit Form: Skala Dampak Kualitatif
     */
    function submitKualitatif(e) {
        if (e && e.preventDefault) e.preventDefault();

        const id = $('#kualitatif_id').val();
        const isEdit = !!id;
        const url = isEdit
            ? '<?= site_url("MasterData/KriteriaDampakKemungkinan/update/kualitatif") ?>'
            : '<?= site_url("MasterData/KriteriaDampakKemungkinan/save/kualitatif") ?>';

        const $btn = $('#btnSaveKualitatif');
        $btn.prop('disabled', true).text('Menyimpan...');

        const formData = new FormData($('#formKualitatif')[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('modalKualitatif');
                    showToast(res.message);
                    loadTabData('kualitatif');
                } else {
                    showToast(res.message || 'Gagal menyimpan data.');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan koneksi saat menyimpan data.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Simpan Data');
            }
        });
    }

    /**
     * Submit Form: Skala Probabilitas
     */
    function submitProbabilitas(e) {
        if (e && e.preventDefault) e.preventDefault();

        const id = $('#probabilitas_id').val();
        const isEdit = !!id;
        const url = isEdit
            ? '<?= site_url("MasterData/KriteriaDampakKemungkinan/update/probabilitas") ?>'
            : '<?= site_url("MasterData/KriteriaDampakKemungkinan/save/probabilitas") ?>';

        const $btn = $('#btnSaveProbabilitas');
        $btn.prop('disabled', true).text('Menyimpan...');

        const formData = new FormData($('#formProbabilitas')[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 'success') {
                    closeModal('modalProbabilitas');
                    showToast(res.message);
                    loadTabData('probabilitas');
                } else {
                    showToast(res.message || 'Gagal menyimpan data.');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan koneksi saat menyimpan data.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Simpan Data');
            }
        });
    }

    /**
     * Hapus Data via AJAX
     */
    function deleteItem(tabName, id, label) {
        if (!confirm(`Apakah Anda yakin ingin menghapus "${label}"?`)) return;

        $.ajax({
            url: '<?= site_url("MasterData/KriteriaDampakKemungkinan/delete/") ?>' + tabName + '/' + id,
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 'success') {
                    showToast(res.message);
                    $(`#row-${tabName}-${id}`).fadeOut(250, function() {
                        $(this).remove();
                        // Perbarui data cache & counter
                        loadTabData(tabName);
                    });
                } else {
                    showToast(res.message || 'Gagal menghapus data.');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan koneksi saat menghapus data.');
            }
        });
    }

    // Inisialisasi awal saat dokumen siap
    $(function() {
        // Muat tab awal
        switchKriteriaTab('kuantitatif');

        // Modal backdrop listener
        $('.modal-backdrop').on('click', function(e) {
            if (e.target === this) {
                $(this).removeClass('show');
            }
        });

        // Keyboard Escape listener
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('.modal-backdrop').removeClass('show');
            }
        });
    });
</script>

<?php $this->load->view('templates/footer'); ?>

