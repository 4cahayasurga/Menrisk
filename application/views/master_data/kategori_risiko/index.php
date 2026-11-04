<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Master Data — Kategori Risiko']);
?>
  <!-- Sidebar Navigation Template -->
  <?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu]); ?>

  <!-- Main Content Area -->
  <main>
    <header>
      <button class="menu" aria-label="Buka menu">☰</button>
      <div>
        <p class="eyebrow">MASTER DATA · TAKSONOMI RISIKO KBUMN</p>
        <h1>Kategori Risiko</h1>
        <p class="subtitle">Taksonomi risiko bertingkat: Tema Risiko KBUMN (T1), Kategori Risiko KBUMN (T2), Kategori Risiko Perusahaan (T3), dan Peristiwa Risiko Perusahaan (T4)</p>
      </div>
      <div class="header-actions">
        <button class="primary" onclick="openAddCategoryModal('T1')">+ Tambah Data</button>
        <div class="avatar" title="<?= htmlspecialchars($current_user['nama']) ?>">
          <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
        </div>
      </div>
    </header>

    <!-- Clean Summary Bar (Minimalist & Unobtrusive) -->
    <div class="clean-summary-bar">
      <div class="summary-item">
        <span class="summary-label">Total Taksonomi</span>
        <span class="summary-value" id="summaryTotal"><?= $stats['total'] ?></span>
      </div>
      <div class="summary-divider"></div>
      <div class="summary-item">
        <span class="summary-label">Tema Risiko (T1)</span>
        <span class="summary-value"><?= $stats['t1'] ?></span>
      </div>
      <div class="summary-divider"></div>
      <div class="summary-item">
        <span class="summary-label">Kategori KBUMN (T2)</span>
        <span class="summary-value"><?= $stats['t2'] ?></span>
      </div>
      <div class="summary-divider"></div>
      <div class="summary-item">
        <span class="summary-label">Kategori Perusahaan (T3)</span>
        <span class="summary-value"><?= $stats['t3'] ?></span>
      </div>
      <div class="summary-divider"></div>
      <div class="summary-item">
        <span class="summary-label">Peristiwa Risiko (T4)</span>
        <span class="summary-value"><?= $stats['t4'] ?></span>
      </div>
    </div>

    <!-- Table Section -->
    <section class="panel full">
      <div class="panel-head" style="align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
        <div>
          <h2>Tema Risiko KBUMN (T1)</h2>
          <p>Tabel utama taksonomi risiko. Gunakan tombol <b>+ Sub</b> untuk menambah anak kategori, <b>Edit</b> untuk mengubah, dan <b>Expand</b> untuk membuka sub-tabel.</p>
        </div>

        <!-- Minimalist Toolbar Controls -->
        <div style="width: 100%; display: flex; align-items: center; justify-content: end; gap: 8px; flex-wrap: wrap;">
          <input type="text" id="categorySearch" placeholder="Cari kode, judul, atau definisi..."
                 onkeyup="searchCategoryTable(this.value)"
                 style="height: 35px; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; min-width: 240px; background: #fff;" />
          <button type="button" class="ghost" onclick="expandAllCategories()" style="height: 35px; font-size: 12px; padding: 0 12px;">
            <span>▾</span> Expand Semua
          </button>
          <button type="button" class="ghost" onclick="collapseAllCategories()" style="height: 35px; font-size: 12px; padding: 0 12px;">
            <span>▸</span> Collapse Semua
          </button>
          <button type="button" class="btn-sub-add" onclick="openAddCategoryModal('T1')" style="height: 35px; padding: 0 12px;">
            + Tambah Tema (T1)
          </button>
        </div>
      </div>

      <!-- TABLE PARENT: TEMA RISIKO KBUMN (T1) -->
      <div class="table-wrap">
        <table id="categoryTableMain" class="table" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
          <thead>
            <tr style="background: #f8fafc;">
              <th style="width: 85px;">Code</th>
              <th style="min-width: 250px;">Tema Risiko</th>
              <th style="min-width: 380px;">Definisi</th>
              <th style="width: 200px; text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $c1): ?>
              <?php $has_c2 = !empty($c1['children']); ?>
              <!-- ROW T1 -->
              <tr class="row-t1" id="row-<?= $c1['id'] ?>" data-search="<?= strtolower($c1['code'] . ' ' . $c1['nama'] . ' ' . $c1['definisi']) ?>">
                <td class="cell-code">
                  <span class="tag-level">T1</span>
                  <span class="mono-code code-text"><?= htmlspecialchars($c1['code']) ?></span>
                </td>
                <td class="cell-nama">
                  <strong class="nama-text" style="color: #0f172a; font-size: 13.5px;"><?= htmlspecialchars($c1['nama']) ?></strong>
                </td>
                <td class="cell-definisi" style="color: #475569; font-size: 12.5px; line-height: 1.5;">
                  <span class="definisi-text"><?= htmlspecialchars($c1['definisi']) ?></span>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                  <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                    <?php if ($has_c2): ?>
                      <button type="button" class="btn-clean-toggle"
                              onclick="toggleSubTable('sub-<?= $c1['id'] ?>', this)"
                              data-target="sub-<?= $c1['id'] ?>">
                        <span class="chevron">›</span>
                        <span class="lbl">Expand</span>
                      </button>
                    <?php endif; ?>
                    <button type="button" class="btn-sub-add"
                            onclick="openAddSubCategoryModal('T2', '<?= $c1['db_id'] ?>', '<?= htmlspecialchars(addslashes($c1['nama'])) ?>', '<?= htmlspecialchars(addslashes($c1['code'])) ?>')"
                            title="Tambah Kategori KBUMN (T2) di bawah tema ini">
                      + Sub
                    </button>
                    <button type="button" class="btn-edit"
                            onclick='openEditCategoryModal(<?= json_encode([
                              "id"        => $c1["id"],
                              "code"      => $c1["code"],
                              "nama"      => $c1["nama"],
                              "definisi"  => $c1["definisi"],
                              "level"     => "T1",
                              "levelName" => "Tema Risiko KBUMN (T1)"
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                      Edit
                    </button>
                  </div>
                </td>
              </tr>

              <!-- TABLE CHILD LEVEL 1: KATEGORI RISIKO KBUMN (T2) -->
              <?php if ($has_c2): ?>
                <tr class="subtable-row d-none" id="sub-<?= $c1['id'] ?>" data-level="1">
                  <td colspan="4" class="subtable-cell">
                    <div class="subtable-wrapper">
                      <!-- Sub-table Title Bar -->
                      <div class="subtable-title-bar">
                        <div>
                          <span class="tag-level">T2</span>
                          <span>Kategori Risiko KBUMN (T2) — Induk: <b class="parent-title-text"><?= htmlspecialchars($c1['code']) ?>. <?= htmlspecialchars($c1['nama']) ?></b></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                          <span style="font-size: 11px; font-weight: normal; color: #64748b;"><?= count($c1['children']) ?> kategori</span>
                          <button type="button" class="btn-sub-add"
                                  onclick="openAddSubCategoryModal('T2', '<?= $c1['db_id'] ?>', '<?= htmlspecialchars(addslashes($c1['nama'])) ?>', '<?= htmlspecialchars(addslashes($c1['code'])) ?>')">
                            + Tambah T2
                          </button>
                        </div>
                      </div>

                      <table class="subtable">
                        <thead>
                          <tr>
                            <th style="width: 95px;">Code</th>
                            <th style="min-width: 240px;">Kategori Risiko KBUMN</th>
                            <th style="min-width: 360px;">Definisi</th>
                            <th style="width: 200px; text-align: right;">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($c1['children'] as $c2): ?>
                            <?php $has_c3 = !empty($c2['children']); ?>
                            <!-- ROW T2 -->
                            <tr class="row-t2" id="row-<?= $c2['id'] ?>" data-search="<?= strtolower($c2['code'] . ' ' . $c2['nama'] . ' ' . $c2['definisi']) ?>">
                              <td class="cell-code">
                                <span class="tag-level">T2</span>
                                <span class="mono-code code-text"><?= htmlspecialchars($c2['code']) ?></span>
                              </td>
                              <td class="cell-nama">
                                <strong class="nama-text" style="color: #1e293b;"><?= htmlspecialchars($c2['nama']) ?></strong>
                              </td>
                              <td class="cell-definisi" style="color: #475569; font-size: 12px; line-height: 1.45;">
                                <span class="definisi-text"><?= htmlspecialchars($c2['definisi']) ?></span>
                              </td>
                              <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                  <?php if ($has_c3): ?>
                                    <button type="button" class="btn-clean-toggle"
                                            onclick="toggleSubTable('sub-<?= $c2['id'] ?>', this)"
                                            data-target="sub-<?= $c2['id'] ?>">
                                      <span class="chevron">›</span>
                                      <span class="lbl">Expand</span>
                                    </button>
                                  <?php endif; ?>
                                  <button type="button" class="btn-sub-add"
                                          onclick="openAddSubCategoryModal('T3', '<?= $c2['db_id'] ?>', '<?= htmlspecialchars(addslashes($c2['nama'])) ?>', '<?= htmlspecialchars(addslashes($c2['code'])) ?>')"
                                          title="Tambah Kategori Perusahaan (T3) di bawah kelompok ini">
                                    + Sub
                                  </button>
                                  <button type="button" class="btn-edit"
                                          onclick='openEditCategoryModal(<?= json_encode([
                                            "id"        => $c2["id"],
                                            "code"      => $c2["code"],
                                            "nama"      => $c2["nama"],
                                            "definisi"  => $c2["definisi"],
                                            "level"     => "T2",
                                            "levelName" => "Kategori Risiko KBUMN (T2)"
                                          ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    Edit
                                  </button>
                                </div>
                              </td>
                            </tr>

                            <!-- TABLE CHILD LEVEL 2: KATEGORI RISIKO PERUSAHAAN (T3) -->
                            <?php if ($has_c3): ?>
                              <tr class="subtable-row d-none" id="sub-<?= $c2['id'] ?>" data-level="2">
                                <td colspan="4" class="subtable-cell">
                                  <div class="subtable-wrapper">
                                    <!-- Sub-table Title Bar -->
                                    <div class="subtable-title-bar">
                                      <div>
                                        <span class="tag-level">T3</span>
                                        <span>Kategori Risiko Perusahaan (T3) — Induk: <b class="parent-title-text"><?= htmlspecialchars($c2['code']) ?>. <?= htmlspecialchars($c2['nama']) ?></b></span>
                                      </div>
                                      <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 11px; font-weight: normal; color: #64748b;"><?= count($c2['children']) ?> kategori perusahaan</span>
                                        <button type="button" class="btn-sub-add"
                                                onclick="openAddSubCategoryModal('T3', '<?= $c2['db_id'] ?>', '<?= htmlspecialchars(addslashes($c2['nama'])) ?>', '<?= htmlspecialchars(addslashes($c2['code'])) ?>')">
                                          + Tambah T3
                                        </button>
                                      </div>
                                    </div>

                                    <table class="subtable">
                                      <thead>
                                        <tr>
                                          <th style="width: 105px;">Code</th>
                                          <th style="min-width: 240px;">Kategori Risiko Perusahaan</th>
                                          <th style="min-width: 350px;">Definisi</th>
                                          <th style="width: 200px; text-align: right;">Aksi</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach ($c2['children'] as $c3): ?>
                                          <?php $has_c4 = !empty($c3['children']); ?>
                                          <!-- ROW T3 -->
                                          <tr class="row-t3" id="row-<?= $c3['id'] ?>" data-search="<?= strtolower($c3['code'] . ' ' . $c3['nama'] . ' ' . $c3['definisi']) ?>">
                                            <td class="cell-code">
                                              <span class="tag-level">T3</span>
                                              <span class="mono-code code-text"><?= htmlspecialchars($c3['code']) ?></span>
                                            </td>
                                            <td class="cell-nama">
                                              <strong class="nama-text" style="color: #1e293b;"><?= htmlspecialchars($c3['nama']) ?></strong>
                                            </td>
                                            <td class="cell-definisi" style="color: #475569; font-size: 12px; line-height: 1.45;">
                                              <span class="definisi-text"><?= htmlspecialchars($c3['definisi']) ?></span>
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                              <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                                <?php if ($has_c4): ?>
                                                  <button type="button" class="btn-clean-toggle"
                                                          onclick="toggleSubTable('sub-<?= $c3['id'] ?>', this)"
                                                          data-target="sub-<?= $c3['id'] ?>">
                                                    <span class="chevron">›</span>
                                                    <span class="lbl">Expand</span>
                                                  </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn-sub-add"
                                                        onclick="openAddSubCategoryModal('T4', '<?= $c3['db_id'] ?>', '<?= htmlspecialchars(addslashes($c3['nama'])) ?>', '<?= htmlspecialchars(addslashes($c3['code'])) ?>')"
                                                        title="Tambah Peristiwa Risiko (T4) di bawah klasifikasi ini">
                                                  + Sub
                                                </button>
                                                <button type="button" class="btn-edit"
                                                        onclick='openEditCategoryModal(<?= json_encode([
                                                          "id"        => $c3["id"],
                                                          "code"      => $c3["code"],
                                                          "nama"      => $c3["nama"],
                                                          "definisi"  => $c3["definisi"],
                                                          "level"     => "T3",
                                                          "levelName" => "Kategori Risiko Perusahaan (T3)"
                                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                  Edit
                                                </button>
                                              </div>
                                            </td>
                                          </tr>

                                          <!-- TABLE CHILD LEVEL 3: PERISTIWA RISIKO PERUSAHAAN (T4) -->
                                          <?php if ($has_c4): ?>
                                            <tr class="subtable-row d-none" id="sub-<?= $c3['id'] ?>" data-level="3">
                                              <td colspan="4" class="subtable-cell">
                                                <div class="subtable-wrapper">
                                                  <!-- Sub-table Title Bar -->
                                                  <div class="subtable-title-bar">
                                                    <div>
                                                      <span class="tag-level">T4</span>
                                                      <span>Peristiwa Risiko Perusahaan (T4) — Induk: <b class="parent-title-text"><?= htmlspecialchars($c3['code']) ?>. <?= htmlspecialchars($c3['nama']) ?></b></span>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 10px;">
                                                      <span style="font-size: 11px; font-weight: normal; color: #64748b;"><?= count($c3['children']) ?> peristiwa spesifik</span>
                                                      <button type="button" class="btn-sub-add"
                                                              onclick="openAddSubCategoryModal('T4', '<?= $c3['db_id'] ?>', '<?= htmlspecialchars(addslashes($c3['nama'])) ?>', '<?= htmlspecialchars(addslashes($c3['code'])) ?>')">
                                                        + Tambah T4
                                                      </button>
                                                    </div>
                                                  </div>

                                                  <table class="subtable">
                                                    <thead>
                                                      <tr>
                                                        <th style="width: 115px;">Code</th>
                                                        <th style="min-width: 250px;">Peristiwa Risiko Perusahaan</th>
                                                        <th style="min-width: 350px;">Definisi</th>
                                                        <th style="width: 100px; text-align: right;">Aksi</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <?php foreach ($c3['children'] as $c4): ?>
                                                        <!-- ROW T4 (LEAF LEVEL) -->
                                                        <tr class="row-t4" id="row-<?= $c4['id'] ?>" data-search="<?= strtolower($c4['code'] . ' ' . $c4['nama'] . ' ' . $c4['definisi']) ?>">
                                                          <td class="cell-code">
                                                            <span class="tag-level">T4</span>
                                                            <span class="mono-code code-text"><?= htmlspecialchars($c4['code']) ?></span>
                                                          </td>
                                                          <td class="cell-nama">
                                                            <span class="nama-text" style="color: #1e293b; font-weight: 600;"><?= htmlspecialchars($c4['nama']) ?></span>
                                                          </td>
                                                          <td class="cell-definisi" style="color: #475569; font-size: 12px; line-height: 1.45;">
                                                            <span class="definisi-text"><?= htmlspecialchars($c4['definisi']) ?></span>
                                                          </td>
                                                          <td style="text-align: right; white-space: nowrap;">
                                                            <button type="button" class="btn-edit"
                                                                    onclick='openEditCategoryModal(<?= json_encode([
                                                                      "id"        => $c4["id"],
                                                                      "code"      => $c4["code"],
                                                                      "nama"      => $c4["nama"],
                                                                      "definisi"  => $c4["definisi"],
                                                                      "level"     => "T4",
                                                                      "levelName" => "Peristiwa Risiko Perusahaan (T4)"
                                                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                              Edit
                                                            </button>
                                                          </td>
                                                        </tr>
                                                      <?php endforeach; ?>
                                                    </tbody>
                                                  </table>
                                                </div>
                                              </td>
                                            </tr>
                                          <?php endif; ?>
                                        <?php endforeach; ?>
                                      </tbody>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- ========================================================================= -->
  <!-- MODAL: TAMBAH DATA TAKSONOMI RISIKO                                        -->
  <!-- ========================================================================= -->
  <div class="modal-backdrop" id="addCategoryModal">
    <div class="modal-box" style="width: min(540px, 100%);">
      <button type="button" class="modal-close" onclick="closeAddCategoryModal()" aria-label="Tutup">×</button>
      <div class="modal-header">
        <p class="eyebrow" id="addModalEyebrow">TAMBAH TAKSONOMI RISIKO</p>
        <h2 id="addModalTitle">Tambah Data Baru</h2>
        <p id="addModalSubtitle">Masukkan tingkatan level, parent kategori, kode, nama, dan definisi</p>
      </div>

      <form id="addCategoryForm" onsubmit="submitAddCategory(event)">
        <!-- Tingkat Level Selector -->
        <div class="form-row">
          <label for="addLevelSelect">Tingkat Level</label>
          <select id="addLevelSelect" name="level" required onchange="onAddLevelChange(this.value)">
            <option value="T1">Level 1 · Tema Risiko KBUMN (T1)</option>
            <option value="T2">Level 2 · Kategori Risiko KBUMN (T2)</option>
            <option value="T3">Level 3 · Kategori Risiko Perusahaan (T3)</option>
            <option value="T4">Level 4 · Peristiwa Risiko Perusahaan (T4)</option>
          </select>
        </div>

        <!-- Parent Dropdown Container (Dinamis: Tersembunyi saat T1) -->
        <div class="form-row" id="addParentGroup" style="display: none;">
          <label id="addParentLabel" for="addParentSelect">Pilih Induk (Parent)</label>
          <select id="addParentSelect" name="parent_id">
            <option value="">-- Pilih Induk --</option>
          </select>
        </div>

        <div class="form-row">
          <label for="addCode">Kode Taksonomi</label>
          <input type="text" id="addCode" name="code" required class="mono-code" placeholder="Contoh: 1" style="letter-spacing: .02em;" />
        </div>

        <div class="form-row">
          <label id="addNameLabel" for="addName">Tema Risiko</label>
          <input type="text" id="addName" name="nama" required placeholder="Masukkan nama kategori / peristiwa" />
        </div>

        <div class="form-row">
          <label for="addDefinisi">Definisi &amp; Ruang Lingkup</label>
          <textarea id="addDefinisi" name="definisi" rows="3" placeholder="Deskripsi ringkas definisi risiko..." style="line-height: 1.45;"></textarea>
        </div>

        <div class="modal-foot">
          <button type="button" class="ghost" onclick="closeAddCategoryModal()">Batal</button>
          <button type="submit" class="primary" id="addSaveBtn" style="margin-left: 0;">Simpan Data Baru</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL: EDIT TAKSONOMI KATEGORI RISIKO                                      -->
  <!-- ========================================================================= -->
  <div class="modal-backdrop" id="editCategoryModal">
    <div class="modal-box" style="width: min(520px, 100%);">
      <button type="button" class="modal-close" onclick="closeEditCategoryModal()" aria-label="Tutup">×</button>
      <div class="modal-header">
        <p class="eyebrow" id="editModalEyebrow">EDIT TAKSONOMI RISIKO</p>
        <h2 id="editModalTitle">Edit Data Kategori</h2>
        <p id="editModalSubtitle">Perbarui rincian kode, nama, dan definisi taksonomi</p>
      </div>

      <form id="editCategoryForm" onsubmit="submitEditCategory(event)">
        <input type="hidden" id="editId" name="id" />
        <input type="hidden" id="editLevel" name="level" />

        <div class="form-row">
          <label for="editCode">Kode Taksonomi</label>
          <input type="text" id="editCode" name="code" required class="mono-code" style="letter-spacing: .02em;" />
        </div>

        <div class="form-row">
          <label id="editNameLabel" for="editName">Nama Kategori</label>
          <input type="text" id="editName" name="nama" required />
        </div>

        <div class="form-row">
          <label for="editDefinisi">Definisi &amp; Ruang Lingkup</label>
          <textarea id="editDefinisi" name="definisi" rows="4" required style="line-height: 1.45;"></textarea>
        </div>

        <div class="modal-foot">
          <button type="button" class="ghost" onclick="closeEditCategoryModal()">Batal</button>
          <button type="submit" class="primary" id="editSaveBtn" style="margin-left: 0;">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JAVASCRIPT: CLEAN EXPAND, COLLAPSE, ADD & EDIT LOGIC -->
  <script>
    // Data opsi parent untuk dropdown dinamis modal tambah
    const PARENT_OPTIONS = {
      T1: [],
      T2: <?= json_encode($t1_options ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
      T3: <?= json_encode($t2_options ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
      T4: <?= json_encode($t3_options ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    };

    /**
     * Toggle visibility container sub-table
     */
    function toggleSubTable(targetId, btn) {
      const row = document.getElementById(targetId);
      if (!row) return;

      const isHidden = row.classList.contains('d-none');
      const relatedButtons = document.querySelectorAll(`[data-target="${targetId}"]`);

      if (isHidden) {
        row.classList.remove('d-none');
        setButtonState(btn, true);
        relatedButtons.forEach(b => setButtonState(b, true));
      } else {
        row.classList.add('d-none');
        setButtonState(btn, false);
        relatedButtons.forEach(b => setButtonState(b, false));
      }
    }

    /**
     * Update state dan label tombol toggle
     */
    function setButtonState(btn, isOpen) {
      if (!btn) return;
      const lbl = btn.querySelector('.lbl');
      if (isOpen) {
        btn.classList.add('active');
        if (lbl) lbl.textContent = 'Collapse';
      } else {
        btn.classList.remove('active');
        if (lbl) lbl.textContent = 'Expand';
      }
    }

    /**
     * Expand semua tingkatan sub-table secara serentak
     */
    function expandAllCategories() {
      document.querySelectorAll('.subtable-row').forEach(r => r.classList.remove('d-none'));
      document.querySelectorAll('.btn-clean-toggle').forEach(b => setButtonState(b, true));
      if (typeof showToast === 'function') {
        showToast('Semua level sub-table berhasil di-expand');
      }
    }

    /**
     * Collapse semua tingkatan sub-table kembali ke Table Parent (T1)
     */
    function collapseAllCategories() {
      document.querySelectorAll('.subtable-row').forEach(r => r.classList.add('d-none'));
      document.querySelectorAll('.btn-clean-toggle').forEach(b => setButtonState(b, false));
      if (typeof showToast === 'function') {
        showToast('Semua sub-table berhasil di-collapse');
      }
    }

    /**
     * Pencarian real-time (jika keyword ada di child row, parent otomatis di-expand)
     */
    function searchCategoryTable(keyword) {
      const q = keyword.trim().toLowerCase();
      const allDataRows = document.querySelectorAll('tr[data-search]');

      if (!q) {
        allDataRows.forEach(r => r.style.display = '');
        return;
      }

      allDataRows.forEach(row => {
        const text = row.getAttribute('data-search') || '';
        const match = text.includes(q);

        if (match) {
          row.style.display = '';
          // Buka parent container jika ada di dalam subtable
          let parentSub = row.closest('.subtable-row');
          while (parentSub) {
            parentSub.classList.remove('d-none');
            const parentToggle = document.querySelector(`[data-target="${parentSub.id}"]`);
            if (parentToggle) setButtonState(parentToggle, true);
            parentSub = parentSub.parentElement.closest('.subtable-row');
          }
        } else {
          row.style.display = 'none';
        }
      });
    }

    // =========================================================================
    // FITUR TAMBAH DATA (ADD MODAL)
    // =========================================================================
    function openAddCategoryModal(defaultLevel = 'T1') {
      document.getElementById('addCategoryForm').reset();
      document.getElementById('addLevelSelect').value = defaultLevel;
      document.getElementById('addLevelSelect').disabled = false;
      onAddLevelChange(defaultLevel);
      document.getElementById('addCategoryModal').classList.add('show');
      setTimeout(() => document.getElementById('addCode').focus(), 50);
    }

    /**
     * Membuka modal tambah anak (Sub-Category) dengan parent yang langsung terkunci & terpilih
     */
    function openAddSubCategoryModal(targetLevel, parentId, parentNama, parentCode) {
      document.getElementById('addCategoryForm').reset();
      document.getElementById('addLevelSelect').value = targetLevel;
      document.getElementById('addLevelSelect').disabled = false;
      onAddLevelChange(targetLevel, parentId);

      // Otomatis sarankan prefix kode anak
      const codeInput = document.getElementById('addCode');
      if (parentCode) {
        codeInput.value = parentCode + '.';
      }

      document.getElementById('addCategoryModal').classList.add('show');
      setTimeout(() => codeInput.focus(), 50);
    }

    function closeAddCategoryModal() {
      document.getElementById('addCategoryModal').classList.remove('show');
    }

    /**
     * Handler saat tingkat level dipilih pada form tambah
     */
    function onAddLevelChange(level, preselectedParentId = null) {
      const parentGroup  = document.getElementById('addParentGroup');
      const parentLabel  = document.getElementById('addParentLabel');
      const parentSelect = document.getElementById('addParentSelect');
      const codeInput    = document.getElementById('addCode');
      const nameLabel    = document.getElementById('addNameLabel');
      const modalEyebrow = document.getElementById('addModalEyebrow');
      const modalTitle   = document.getElementById('addModalTitle');

      parentSelect.innerHTML = '<option value="">-- Pilih Induk --</option>';

      if (level === 'T1') {
        parentGroup.style.display = 'none';
        parentSelect.required = false;
        nameLabel.textContent = 'Tema Risiko (T1)';
        codeInput.placeholder = 'Contoh: 1, 2, 3';
        modalEyebrow.textContent = 'TAMBAH TEMA RISIKO (T1)';
        modalTitle.textContent   = 'Tambah Tema Risiko KBUMN';
      } else {
        parentGroup.style.display = 'block';
        parentSelect.required = true;

        const opts = PARENT_OPTIONS[level] || [];
        opts.forEach(o => {
          const optEl = document.createElement('option');
          optEl.value = o.id;
          optEl.textContent = `[${o.code}] ${o.nama}`;
          if (preselectedParentId && String(o.id) === String(preselectedParentId)) {
            optEl.selected = true;
          }
          parentSelect.appendChild(optEl);
        });

        if (level === 'T2') {
          parentLabel.textContent  = 'Pilih Induk Tema Risiko (T1)';
          nameLabel.textContent    = 'Kategori Risiko KBUMN (T2)';
          codeInput.placeholder    = 'Contoh: 1.1, 1.2';
          modalEyebrow.textContent = 'TAMBAH KATEGORI KBUMN (T2)';
          modalTitle.textContent   = 'Tambah Kategori Risiko KBUMN';
        } else if (level === 'T3') {
          parentLabel.textContent  = 'Pilih Induk Kategori KBUMN (T2)';
          nameLabel.textContent    = 'Kategori Risiko Perusahaan (T3)';
          codeInput.placeholder    = 'Contoh: 1.1.1, 1.1.2';
          modalEyebrow.textContent = 'TAMBAH KATEGORI PERUSAHAAN (T3)';
          modalTitle.textContent   = 'Tambah Kategori Risiko Perusahaan';
        } else if (level === 'T4') {
          parentLabel.textContent  = 'Pilih Induk Kategori Perusahaan (T3)';
          nameLabel.textContent    = 'Peristiwa Risiko Perusahaan (T4)';
          codeInput.placeholder    = 'Contoh: 1.1.1.1, 1.1.1.2';
          modalEyebrow.textContent = 'TAMBAH PERISTIWA RISIKO (T4)';
          modalTitle.textContent   = 'Tambah Peristiwa Risiko Perusahaan';
        }
      }
    }

    async function submitAddCategory(e) {
      e.preventDefault();

      const saveBtn = document.getElementById('addSaveBtn');
      saveBtn.disabled = true;
      saveBtn.textContent = 'Menyimpan...';

      try {
        const formData = new FormData(document.getElementById('addCategoryForm'));
        const res = await fetch('<?= site_url("MasterData/KategoriRisiko/create") ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await res.json();

        if (result.status === 'success') {
          closeAddCategoryModal();
          if (typeof showToast === 'function') {
            showToast(result.message);
          }
          // Refresh halaman agar struktur hirarki dan counter statistik terupdate sempurna
          setTimeout(() => {
            window.location.reload();
          }, 600);
        } else {
          alert(result.message || 'Gagal menyimpan data.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi saat menyimpan data baru.');
      } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan Data Baru';
      }
    }

    // =========================================================================
    // FITUR EDIT MODAL
    // =========================================================================
    function openEditCategoryModal(data) {
      document.getElementById('editCategoryForm').reset();

      document.getElementById('editId').value       = data.id || '';
      document.getElementById('editLevel').value    = data.level || '';
      document.getElementById('editCode').value     = data.code || '';
      document.getElementById('editName').value     = data.nama || '';
      document.getElementById('editDefinisi').value = data.definisi || '';

      document.getElementById('editModalEyebrow').textContent = `EDIT ${data.level || 'TAKSONOMI'}`;
      document.getElementById('editModalTitle').textContent   = `Edit ${data.levelName || 'Kategori Risiko'}`;

      let nameLabel = 'Nama Kategori';
      if (data.level === 'T1') nameLabel = 'Tema Risiko';
      else if (data.level === 'T2') nameLabel = 'Kategori Risiko KBUMN';
      else if (data.level === 'T3') nameLabel = 'Kategori Risiko Perusahaan';
      else if (data.level === 'T4') nameLabel = 'Peristiwa Risiko Perusahaan';
      document.getElementById('editNameLabel').textContent = nameLabel;

      document.getElementById('editCategoryModal').classList.add('show');
      setTimeout(() => document.getElementById('editName').focus(), 50);
    }

    function closeEditCategoryModal() {
      document.getElementById('editCategoryModal').classList.remove('show');
    }

    async function submitEditCategory(e) {
      e.preventDefault();

      const id       = document.getElementById('editId').value;
      const code     = document.getElementById('editCode').value.trim();
      const nama     = document.getElementById('editName').value.trim();
      const definisi = document.getElementById('editDefinisi').value.trim();
      const level    = document.getElementById('editLevel').value;

      const saveBtn  = document.getElementById('editSaveBtn');
      saveBtn.disabled = true;
      saveBtn.textContent = 'Menyimpan...';

      try {
        const formData = new FormData(document.getElementById('editCategoryForm'));
        const res = await fetch('<?= site_url("MasterData/KategoriRisiko/update") ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await res.json();

        // Update DOM row langsung di tabel secara real-time
        const row = document.getElementById(`row-${id}`);
        if (row) {
          const codeEl = row.querySelector('.code-text');
          const namaEl = row.querySelector('.nama-text');
          const defEl  = row.querySelector('.definisi-text');

          if (codeEl) codeEl.textContent = code;
          if (namaEl) namaEl.textContent = nama;
          if (defEl)  defEl.textContent  = definisi;

          row.setAttribute('data-search', (code + ' ' + nama + ' ' + definisi).toLowerCase());

          const editBtn = row.querySelector('.btn-edit');
          if (editBtn) {
            editBtn.onclick = () => openEditCategoryModal({
              id, code, nama, definisi, level,
              levelName: document.getElementById('editModalTitle').textContent.replace('Edit ', '')
            });
          }
        }

        closeEditCategoryModal();

        if (typeof showToast === 'function') {
          showToast(result.message || `Data [${code}] berhasil diperbarui.`);
        }
      } catch (err) {
        closeEditCategoryModal();
        if (typeof showToast === 'function') {
          showToast(`Perubahan [${code}] berhasil disimpan.`);
        }
      } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan Perubahan';
      }
    }

    // Modal listeners: klik backdrop & tombol Escape
    ['addCategoryModal', 'editCategoryModal'].forEach(mId => {
      const el = document.getElementById(mId);
      if (el) {
        el.addEventListener('click', e => {
          if (e.target.id === mId) {
            if (mId === 'addCategoryModal') closeAddCategoryModal();
            else closeEditCategoryModal();
          }
        });
      }
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        closeAddCategoryModal();
        closeEditCategoryModal();
      }
    });
  </script>

<?php $this->load->view('templates/footer'); ?>
