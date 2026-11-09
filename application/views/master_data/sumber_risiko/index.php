<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('templates/header', ['title' => $title ?? 'Master Data — Sumber & Faktor Risiko']);
?>
<!-- Sidebar Navigation Template -->
<?php $this->load->view('templates/sidebar', ['active_menu' => $active_menu]); ?>
<!-- Main Content Area -->
<main>
    <header>
    <button class="menu" aria-label="Buka menu">☰</button>
    <div>
        <p class="eyebrow">MASTER DATA · SUMBER & FAKTOR RISIKO</p>
        <h1>Sumber Risiko</h1>
        <p class="subtitle"></p>
    </div>
    <div class="header-actions">
        <button class="primary" onclick="openAddFormRisk()">+ Tambah Data 2</button>
        <button class="primary" onclick="openAddCategoryModal()">+ Tambah Data</button>
        <div class="avatar" title="<?= htmlspecialchars($current_user['nama'] ?? 'Admin') ?>">
        <?= strtoupper(substr($current_user['nama'] ?? 'AD', 0, 2)) ?>
        </div>
    </div>
    </header>

    <section class="panel full">
        <div class="panel-head" style="align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <div>
                <h2>Sumber & Faktor Risiko</h2>
                <p>Tabel daftar sumber dan faktor risiko. Gunakan tombol <b>+ Tambah Data</b> untuk menambahkan faktor risiko baru, <b>Edit</b> untuk mengubah, atau <b>Hapus</b> untuk menghapus data.</p>
            </div>
            <!-- Minimalist Toolbar Controls -->
            <div style="width: 100%; display: flex; align-items: center; justify-content: end; gap: 8px; flex-wrap: wrap;">
                <input type="text" id="categorySearch" placeholder="Cari sumber atau faktor risiko..."
                    onkeyup="searchCategoryTable(this.value)"
                    style="height: 35px; padding: 0 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 12.5px; min-width: 240px; background: #fff;" 
                />
            </div>
        </div>
        
        <!-- TABLE WRAP -->
        <div class="table-wrap">
            <table id="categoryTableMain" class="table" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="width: 85px;">No</th>
                        <th style="min-width: 250px;">Sumber Risiko</th>
                        <th style="min-width: 380px;">Faktor Risiko</th>
                        <th style="width: 200px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($faktor_risiko)): ?>
                        <?php $no = 1; foreach ($faktor_risiko as $row): ?>
                            <tr id="row-<?= $row->id ?>">
                                <td><?= $no++ ?></td>
                                <td class="col-sumber"><?= htmlspecialchars($row->nama_sumber ?? '-') ?></td>
                                <td class="col-faktor"><?= htmlspecialchars($row->nama_faktor ?? '-') ?></td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-edit" 
                                        onclick="openEditCategoryModal(<?= $row->id ?>, <?= (int)$row->sumber_risiko_id ?>, '<?= htmlspecialchars(addslashes($row->nama_faktor ?? ''), ENT_QUOTES) ?>')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-delete" 
                                        onclick="deleteCategory(<?= $row->id ?>, '<?= htmlspecialchars(addslashes($row->nama_faktor ?? ''), ENT_QUOTES) ?>')">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="row-empty">
                            <td colspan="4" style="text-align: center; color: var(--muted); padding: 24px;">Belum ada data faktor risiko.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- ========================================================================= -->
<!-- MODAL: TAMBAH DATA SUMBER & FAKTOR RISIKO                                 -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="addCategoryModal">
    <div class="modal-box" style="width: min(540px, 100%);">
        <button type="button" class="modal-close" onclick="closeAddCategoryModal()" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="addModalTitle">Tambah Data Baru</h2>
            <p id="addModalSubtitle">Pilih sumber risiko & masukkan faktor risiko</p>
        </div>

        <form id="addCategoryForm" onsubmit="submitAddCategory(event)">
        <div class="form-row">
            <label id="addParentLabel" for="addParentSelect">Pilih Sumber Risiko</label>
            <select id="addParentSelect" name="sumber_id" required>
                <option value="">-- Pilih Sumber --</option>
                <?php if (!empty($sumber_risiko)): ?>
                    <?php foreach($sumber_risiko as $item): ?>
                        <option value="<?= $item->id ?>"><?= htmlspecialchars($item->nama) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="addFaktor">Faktor Risiko</label>
            <input type="text" id="addFaktor" name="faktor" required class="mono-code" placeholder="Contoh: SDM" style="letter-spacing: .02em;" />
        </div>

        <div class="modal-foot">
            <button type="button" class="ghost" onclick="closeAddCategoryModal()">Batal</button>
            <button type="submit" class="primary" id="addSaveBtn" style="margin-left: 0;">Simpan Data Baru</button>
        </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT DATA SUMBER & FAKTOR RISIKO                                   -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="editCategoryModal">
    <div class="modal-box" style="width: min(540px, 100%);">
        <button type="button" class="modal-close" onclick="closeEditCategoryModal()" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="editModalTitle">Edit Faktor Risiko</h2>
            <p id="editModalSubtitle">Perbarui sumber risiko & nama faktor risiko</p>
        </div>

        <form id="editCategoryForm" onsubmit="submitEditCategory(event)">
        <input type="hidden" id="editId" name="id" />

        <div class="form-row">
            <label for="editParentSelect">Pilih Sumber Risiko</label>
            <select id="editParentSelect" name="sumber_id" required>
                <option value="">-- Pilih Sumber --</option>
                <?php if (!empty($sumber_risiko)): ?>
                    <?php foreach($sumber_risiko as $item): ?>
                        <option value="<?= $item->id ?>"><?= htmlspecialchars($item->nama) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="editFaktor">Faktor Risiko</label>
            <input type="text" id="editFaktor" name="faktor" required class="mono-code" placeholder="Contoh: SDM" style="letter-spacing: .02em;" />
        </div>

        <div class="modal-foot">
            <button type="button" class="ghost" onclick="closeEditCategoryModal()">Batal</button>
            <button type="submit" class="primary" id="editSaveBtn" style="margin-left: 0;">Simpan Perubahan</button>
        </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TESTING ADD FORM PROFIL RISIKO                                   -->
<!-- ========================================================================= -->
 <div class="modal-backdrop" id="addProfileRisikoModal">
    <div class="modal-box" style="width: min(940px, 100%);">
        <button type="button" class="modal-close" onclick="closeEditCategoryModal()" aria-label="Tutup">×</button>
        <div class="modal-header">
            <h2 id="editModalTitle">Edit Faktor Risiko</h2>
            <p id="editModalSubtitle">Perbarui sumber risiko & nama faktor risiko</p>
        </div>

    </div>
 </div>




<script>
    // =========================================================================
    // TESTING FITUR TAMBAH PROFIL RISIKO (ADD MODAL)
    // =========================================================================
    function openAddFormRisk() {
        console.log('asd')
        $('#addProfileRisikoModal').addClass('show');
    }

    // =========================================================================
    // FITUR TAMBAH DATA (ADD MODAL)
    // =========================================================================
    function openAddCategoryModal() {
        $('#addCategoryForm')[0].reset();
        $('#addCategoryModal').addClass('show');
        $('#addParentSelect').focus();
    }

    function closeAddCategoryModal() {
        $('#addCategoryModal').removeClass('show');
    }

    function searchCategoryTable(keyword) {
        const q = $.trim(keyword).toLowerCase();
        $('#categoryTableMain tbody tr').each(function() {
            if ($(this).attr('id') === 'row-empty') return;
            const text = $(this).text().toLowerCase();
            if (text.indexOf(q) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function submitAddCategory(e) {
        if (e && e.preventDefault) e.preventDefault();

        const $saveBtn = $('#addSaveBtn');
        const sumberId = $.trim($('#addParentSelect').val());
        const faktor   = $.trim($('#addFaktor').val());

        if (!sumberId) {
            if (typeof showToast === 'function') {
                showToast('Silakan pilih sumber risiko terlebih dahulu.');
            } else {
                alert('Silakan pilih sumber risiko terlebih dahulu.');
            }
            $('#addParentSelect').focus();
            return;
        }

        if (!faktor) {
            if (typeof showToast === 'function') {
                showToast('Faktor risiko wajib diisi.');
            } else {
                alert('Faktor risiko wajib diisi.');
            }
            $('#addFaktor').focus();
            return;
        }

        $saveBtn.prop('disabled', true).text('Menyimpan...');

        const formData = new FormData($('#addCategoryForm')[0]);

        $.ajax({
            url: '<?= site_url("MasterData/SumberRisiko/save") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(result) {
                if (result.status === 'success') {
                    closeAddCategoryModal();
                    if (typeof showToast === 'function') {
                        showToast(result.message);
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    if (typeof showToast === 'function') {
                        showToast(result.message || 'Gagal menyimpan data.');
                    } else {
                        alert(result.message || 'Gagal menyimpan data.');
                    }
                }
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Terjadi kesalahan koneksi saat menyimpan data baru.');
                } else {
                    alert('Terjadi kesalahan koneksi saat menyimpan data baru.');
                }
            },
            complete: function() {
                $saveBtn.prop('disabled', false).text('Simpan Data Baru');
            }
        });
    }

    // =========================================================================
    // FITUR EDIT DATA (EDIT MODAL)
    // =========================================================================
    function openEditCategoryModal(id, sumberId, faktor) {
        $('#editId').val(id);
        $('#editParentSelect').val(sumberId);
        $('#editFaktor').val(faktor);
        $('#editCategoryModal').addClass('show');
        setTimeout(() => $('#editFaktor').trigger('focus'), 50);
    }

    function closeEditCategoryModal() {
        $('#editCategoryModal').removeClass('show');
    }

    function submitEditCategory(e) {
        if (e && e.preventDefault) e.preventDefault();

        const $saveBtn = $('#editSaveBtn');
        const id       = $('#editId').val();
        const sumberId = $.trim($('#editParentSelect').val());
        const faktor   = $.trim($('#editFaktor').val());

        if (!sumberId) {
            if (typeof showToast === 'function') {
                showToast('Silakan pilih sumber risiko terlebih dahulu.');
            } else {
                alert('Silakan pilih sumber risiko terlebih dahulu.');
            }
            $('#editParentSelect').focus();
            return;
        }

        if (!faktor) {
            if (typeof showToast === 'function') {
                showToast('Faktor risiko wajib diisi.');
            } else {
                alert('Faktor risiko wajib diisi.');
            }
            $('#editFaktor').focus();
            return;
        }

        $saveBtn.prop('disabled', true).text('Menyimpan...');

        const formData = new FormData($('#editCategoryForm')[0]);

        $.ajax({
            url: '<?= site_url("MasterData/SumberRisiko/update") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(result) {
                if (result.status === 'success') {
                    closeEditCategoryModal();
                    if (typeof showToast === 'function') {
                        showToast(result.message);
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    if (typeof showToast === 'function') {
                        showToast(result.message || 'Gagal memperbarui data.');
                    } else {
                        alert(result.message || 'Gagal memperbarui data.');
                    }
                }
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Terjadi kesalahan koneksi saat memperbarui data.');
                } else {
                    alert('Terjadi kesalahan koneksi saat memperbarui data.');
                }
            },
            complete: function() {
                $saveBtn.prop('disabled', false).text('Simpan Perubahan');
            }
        });
    }

    // =========================================================================
    // FITUR HAPUS DATA
    // =========================================================================
    function deleteCategory(id, nama) {
        if (!confirm(`Apakah Anda yakin ingin menghapus faktor risiko "${nama}"?`)) return;

        $.ajax({
            url: '<?= site_url("MasterData/SumberRisiko/delete") ?>/' + id,
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(result) {
                if (result.status === 'success') {
                    if (typeof showToast === 'function') {
                        showToast(result.message);
                    }
                    $('#row-' + id).fadeOut(300, function() {
                        $(this).remove();
                        if ($('#categoryTableMain tbody tr').length === 0) {
                            window.location.reload();
                        }
                    });
                } else {
                    if (typeof showToast === 'function') {
                        showToast(result.message || 'Gagal menghapus data.');
                    } else {
                        alert(result.message || 'Gagal menghapus data.');
                    }
                }
            },
            error: function() {
                if (typeof showToast === 'function') {
                    showToast('Terjadi kesalahan koneksi saat menghapus data.');
                } else {
                    alert('Terjadi kesalahan koneksi saat menghapus data.');
                }
            }
        });
    }

    $(function() {
        // Modal backdrop click listener
        $('#addCategoryModal, #editCategoryModal').on('click', function(e) {
            if (e.target === this) {
                closeAddCategoryModal();
                closeEditCategoryModal();
            }
        });

        // Keyboard Escape listener
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                closeAddCategoryModal();
                closeEditCategoryModal();
            }
        });
    });
</script>

<?php $this->load->view('templates/footer'); ?>
