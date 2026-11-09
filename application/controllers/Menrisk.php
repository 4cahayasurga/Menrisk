<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Menrisk
 * IFPRO Risk Command Center - Enterprise Risk Management
 * 
 * Terintegrasi penuh dengan CodeIgniter 3 MVC:
 * - Model: Risk_model, Kri_model
 * - Library: Risk_calculator
 * - Helper: menrisk_helper
 * - Database: Microsoft SQL Server (tabel dbo.risks & dbo.kri)
 */
class Menrisk extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'risk_calculator']);
        $this->load->model(['Risk_model', 'Kri_model', 'User_model', 'Department_model']);
        $this->load->helper(['url', 'html', 'menrisk']);


        // Wajib Login untuk mengakses dashboard
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }


    /**
     * Dashboard Utama Command Center
     */
    public function index() {
        // Tangkap parameter filter (GET)
        $selected_period      = $this->input->get('period', TRUE) ?: 'September 2026';
        $selected_department  = $this->input->get('department', TRUE) ?: $this->input->get('directorate', TRUE) ?: 'all';
        $selected_category    = $this->input->get('category', TRUE) ?: 'all';
        $search_keyword       = $this->input->get('search', TRUE) ?: '';

        // Identifikasi hak akses pengguna yang sedang login
        $user_id   = trim((string)$this->session->userdata('user_id'));
        $user_role = $this->session->userdata('level'); // 'admin' atau 'user'

        $allowed_dept_ids = null;
        $available_depts  = [];

        if ($user_role === 'admin') {
            // Admin memiliki akses ke seluruh department
            $available_depts  = $this->Department_model->get_all();
            $allowed_dept_ids = null;
        } else {
            // User hanya memiliki akses ke department yang di-assign padanya
            $user_depts       = $this->User_model->get_departments_by_user_id($user_id);
            $available_depts  = $user_depts;
            $allowed_dept_ids = array_map('intval', array_column($user_depts, 'id'));
        }

        // 1. Ambil data risks dari Model (SQL Server dbo.risks) sesuai hak akses
        $filters = [
            'period'           => $selected_period,
            'dir'              => $selected_department,
            'cat'              => $selected_category,
            'search'           => $search_keyword,
            'allowed_dept_ids' => $allowed_dept_ids
        ];
        $risks = $this->Risk_model->get_all($filters);

        // 2. Ambil data Key Risk Indicators (KRI) dari Model (SQL Server dbo.kri) sesuai hak akses
        $kri = $this->Kri_model->get_all($selected_period, $allowed_dept_ids, $selected_department);

        // 3. Hitung KPI dan Agregasi Heatmap menggunakan Library Risk_calculator
        $kpis = $this->risk_calculator->compute_kpis($risks);
        $heatmap_matrix = $this->risk_calculator->compute_heatmap_matrix($risks);

        // 4. Opsi list dropdown filter Department sesuai hak akses
        $department_options = ['Semua Department' => 'all'];
        foreach ($available_depts as $dept) {
            $department_options[$dept['name']] = $dept['name'];
        }

        $filter_options = [
            'periods'      => $this->Risk_model->get_distinct_periods(),
            'departments'  => $department_options,
            'directorates' => $department_options, // alias untuk kompatibilitas
            'categories'   => [
                'Semua Kategori' => 'all',
                'Strategis'      => 'Strategis',
                'Operasional'    => 'Operasional',
                'Keuangan'       => 'Keuangan',
                'Kepatuhan'      => 'Kepatuhan',
                'Teknologi'      => 'Teknologi'
            ]
        ];


        // 5. Response AJAX (Jika dipanggil via fetch/XMLHttpRequest)
        if ($this->input->is_ajax_request() || $this->input->get('ajax') == '1') {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'data'   => [
                        'risks'   => $risks,
                        'kri'     => $kri,
                        'kpis'    => $kpis,
                        'heatmap' => $heatmap_matrix
                    ]
                ]));
        }

        // 6. Siapkan data payload ke View
        $data = [
            'title'                => 'IFPRO Risk Command Center',
            'current_user'         => [
                'id'       => $this->session->userdata('user_id'),
                'username' => $this->session->userdata('username'),
                'nama'     => $this->session->userdata('nama'),
                'level'    => $this->session->userdata('level') // 'admin' atau 'user'
            ],
            'selected_period'      => $selected_period,
            'selected_department'  => $selected_department,
            'selected_directorate' => $selected_department, // alias untuk kompatibilitas
            'selected_category'    => $selected_category,
            'search_keyword'       => $search_keyword,
            'filter_options'       => $filter_options,
            'available_depts'      => $available_depts,
            'next_risk_id'         => $this->Risk_model->generate_next_id(),
            'risks'                => $risks,
            'kri'                  => $kri,
            'kpis'                 => $kpis,
            'heatmap_matrix'       => $heatmap_matrix
        ];


        // Render 1 view utama
        $this->load->view('menrisk_view', $data);
    }

    /**
     * Endpoint Generate ID risiko berikutnya
     * GET /menrisk/next_id
     */
    public function next_id() {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'next_id' => $this->Risk_model->generate_next_id()
            ]));
    }

    /**
     * Endpoint Simpan Risiko Baru
     * POST /menrisk/store_risk
     */
    public function store_risk() {
        $user_id   = trim((string)$this->session->userdata('user_id'));
        $user_role = $this->session->userdata('level');

        $allowed_dept_ids = null;
        if ($user_role !== 'admin') {
            $user_depts       = $this->User_model->get_departments_by_user_id($user_id);
            $allowed_dept_ids = array_map('intval', array_column($user_depts, 'id'));
        }

        $id            = trim($this->input->post('id', TRUE));
        $title         = trim($this->input->post('title', TRUE));
        $owner         = trim($this->input->post('owner', TRUE));
        $department_id = $this->input->post('department_id', TRUE);
        $cat           = trim($this->input->post('cat', TRUE));
        $inherent      = (int)$this->input->post('inherent', TRUE);
        $residual      = (int)$this->input->post('residual', TRUE);
        $appetite      = trim($this->input->post('appetite', TRUE));
        $progress      = (int)$this->input->post('progress', TRUE);
        $due           = trim($this->input->post('due', TRUE));
        $mitigation    = trim($this->input->post('mitigation', TRUE));
        $decision      = trim($this->input->post('decision', TRUE));
        $period        = trim($this->input->post('period', TRUE)) ?: 'September 2026';

        // Validasi input mandatory
        if (empty($title) || empty($owner) || empty($cat)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Pernyataan risiko, owner, dan kategori wajib diisi.']));
        }

        // Validasi hak akses department
        if ($department_id !== '' && $department_id !== null) {
            $department_id = (int)$department_id;
            if ($allowed_dept_ids !== null && !in_array($department_id, $allowed_dept_ids)) {
                return $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk department yang dipilih.']));
            }
            $dept_row = $this->Department_model->get_by_id($department_id);
            $dir_name = $dept_row ? $dept_row['name'] : '';
        } else {
            $department_id = null;
            $dir_name = '';
        }

        // Tentukan ID
        if (empty($id)) {
            $id = $this->Risk_model->generate_next_id();
        }

        // Cek duplikasi ID
        $existing = $this->Risk_model->get_by_id($id);
        if ($existing) {
            $id = $this->Risk_model->generate_next_id();
        }

        if (empty($appetite)) {
            $appetite = ($residual >= 15) ? 'Melebihi' : (($residual >= 10) ? 'Mendekati' : 'Dalam');
        }

        $insert_data = [
            'id'            => $id,
            'title'         => $title,
            'owner'         => $owner,
            'department_id' => $department_id,
            'dir'           => $dir_name,
            'cat'           => $cat,
            'inherent'      => max(1, min(25, $inherent)),
            'residual'      => max(1, min(25, $residual)),
            'appetite'      => $appetite,
            'progress'      => max(0, min(100, $progress)),
            'due'           => $due,
            'mitigation'    => $mitigation,
            'decision'      => $decision,
            'period'        => $period,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $inserted = $this->Risk_model->insert($insert_data);

        if (!$inserted) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data risiko ke database.']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Data risiko ' . $id . ' berhasil ditambahkan.',
                'data'    => $insert_data
            ]));
    }

    /**
     * Endpoint detail resiko (untuk modal detail)
     * GET /menrisk/detail/{id}
     */
    public function detail($id = NULL) {
        if (!$id) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Risk ID required']));
        }

        $risk = $this->Risk_model->get_by_id($id);

        if (!$risk) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Risk not found']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'data' => $risk]));
    }

    /**
     * Endpoint Export Executive Brief
     * GET /menrisk/export_brief
     */
    public function export_brief() {
        $period    = $this->input->get('period') ?: 'September 2026';
        $user_id   = trim((string)$this->session->userdata('user_id'));
        $user_role = $this->session->userdata('level');

        $allowed_dept_ids = null;
        if ($user_role !== 'admin') {
            $user_depts       = $this->User_model->get_departments_by_user_id($user_id);
            $allowed_dept_ids = array_map('intval', array_column($user_depts, 'id'));
        }

        $risks  = $this->Risk_model->get_all(['period' => $period, 'allowed_dept_ids' => $allowed_dept_ids]);
        $kpis   = $this->risk_calculator->compute_kpis($risks);

        $brief = [
            'report_name'    => 'IFPRO BOD Executive Risk Brief',
            'period'         => $period,
            'generated_at'   => date('Y-m-d H:i:s'),
            'summary'        => $kpis,
            'critical_items' => array_values(array_filter($risks, function($r) {
                return (int)$r['residual'] >= 15;
            }))
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($brief, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Halaman Formulir Profile Risiko (Create / Edit / View)
     * Diakses dari menu Risk Register atau menu Profile Risiko
     */
    public function profil_risiko($id = null) {
        $this->load->model('SumberRisiko_model');
        $this->load->model('FormulirRisiko_model');

        // 1. Kategori Risiko dari MNRSK_KategoriRisiko_kat_perusahaan (code dan nama)
        $q1 = $this->db->query("SELECT id, code, CAST(nama AS NVARCHAR(MAX)) AS nama FROM dbo.MNRSK_KategoriRisiko_kat_perusahaan ORDER BY code ASC");
        $kategori_perusahaan = ($q1 && is_object($q1)) ? $q1->result() : [];
        if (!empty($kategori_perusahaan)) {
            usort($kategori_perusahaan, function($a, $b) {
                $codeA = $a->code ?? '';
                $codeB = $b->code ?? '';
                $cmp = version_compare($codeA, $codeB);
                return ($cmp !== 0) ? $cmp : strnatcasecmp($codeA, $codeB);
            });
        }

        // 2. Sumber Risiko dari MNRSK_SumberRisiko (id, nama)
        $sumber_risiko = $this->SumberRisiko_model->get_all_sumber_risiko() ?: [];

        // 3. Faktor Sumber dari MNRSK_FaktorRisiko (id, sumber_risiko_id, nama)
        $q3 = $this->db->query("SELECT id, sumber_risiko_id, nama FROM dbo.MNRSK_FaktorRisiko ORDER BY nama ASC");
        $faktor_risiko = ($q3 && is_object($q3)) ? $q3->result() : [];

        // 4. Skala Dampak Kualitatif untuk Tab 2 (id, judul, risiko, skala_1 s/d skala_5)
        $q4 = $this->db->query("SELECT id, judul, CAST(risiko AS NVARCHAR(MAX)) AS risiko, CAST(skala_1 AS NVARCHAR(MAX)) AS skala_1, CAST(skala_2 AS NVARCHAR(MAX)) AS skala_2, CAST(skala_3 AS NVARCHAR(MAX)) AS skala_3, CAST(skala_4 AS NVARCHAR(MAX)) AS skala_4, CAST(skala_5 AS NVARCHAR(MAX)) AS skala_5 FROM dbo.MNRSK_SkalaDampakKualitatif ORDER BY judul ASC");
        $skala_kualitatif = ($q4 && is_object($q4)) ? $q4->result() : [];

        // 5. Skala Dampak Kuantitatif untuk Tab 2 (id, skala, [range], deskripsi)
        // Catatan: [range] adalah reserved keyword di SQL Server, gunakan direct query agar tidak terdistorsi CI Query Builder
        $q5 = $this->db->query("SELECT id, skala, [range], CAST(deskripsi AS NVARCHAR(MAX)) AS deskripsi FROM dbo.MNRSK_SkalaDampakKuantitatif ORDER BY skala ASC");
        $skala_kuantitatif = ($q5 && is_object($q5)) ? $q5->result() : [];

        // 6. Skala Probabilitas untuk Tab 2 (id, parameter, skala_1 s/d skala_5)
        $q6 = $this->db->query("SELECT id, CAST(parameter AS NVARCHAR(MAX)) AS parameter, CAST(skala_1 AS NVARCHAR(MAX)) AS skala_1, CAST(skala_2 AS NVARCHAR(MAX)) AS skala_2, CAST(skala_3 AS NVARCHAR(MAX)) AS skala_3, CAST(skala_4 AS NVARCHAR(MAX)) AS skala_4, CAST(skala_5 AS NVARCHAR(MAX)) AS skala_5 FROM dbo.MNRSK_SkalaProbabilitas ORDER BY id ASC");
        $skala_probabilitas = ($q6 && is_object($q6)) ? $q6->result() : [];

        $formulir_detail = null;
        $form_mode       = 'create';
        if (!empty($id) && is_numeric($id)) {
            $formulir_detail = $this->FormulirRisiko_model->get_formulir_by_id((int)$id);
            if ($formulir_detail) {
                $req_mode  = strtolower((string)$this->input->get('mode', TRUE));
                $form_mode = ($req_mode === 'view') ? 'view' : 'edit';
            }
        }

        $data = [
            'title'               => ($form_mode === 'edit' ? 'Edit ' : ($form_mode === 'view' ? 'Detail ' : '')) . 'Formulir Profile Risiko — IFPRO Risk Command Center',
            'current_user'        => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu'         => ($form_mode === 'create') ? 'register' : 'daftar_profil_risiko',
            'is_dashboard'        => false,
            'kategori_perusahaan' => $kategori_perusahaan,
            'sumber_risiko'       => $sumber_risiko,
            'faktor_risiko'       => $faktor_risiko,
            'skala_kualitatif'    => $skala_kualitatif,
            'skala_kuantitatif'   => $skala_kuantitatif,
            'skala_probabilitas'  => $skala_probabilitas,
            'formulir_detail'     => $formulir_detail,
            'form_mode'           => $form_mode
        ];

        $this->load->view('profil_risiko_view', $data);
    }

    /**
     * Halaman Daftar / Tabel Hasil Input Formulir Profile Risiko
     */
    public function daftar_profil_risiko() {
        $this->load->model('FormulirRisiko_model');

        $formulir_list = $this->FormulirRisiko_model->get_all_formulir();

        $data = [
            'title'         => 'Daftar Profile Risiko — IFPRO Risk Command Center',
            'current_user'  => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu'   => 'daftar_profil_risiko',
            'is_dashboard'  => false,
            'formulir_list' => $formulir_list
        ];

        $this->load->view('daftar_profil_risiko_view', $data);
    }

    /**
     * Endpoint JSON Detail Formulir Profile Risiko (untuk modal Lihat)
     */
    public function detail_profil_risiko($id = null) {
        $this->load->model('FormulirRisiko_model');

        $detail = $this->FormulirRisiko_model->get_formulir_by_id((int)$id);
        if (!$detail) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Data Formulir Profile Risiko tidak ditemukan.'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $detail
            ]));
    }

    /**
     * Simpan / Perbarui Formulir Profile Risiko ke Database
     */
    public function save_profil_risiko() {
        $this->load->model('FormulirRisiko_model');

        $formulir_id         = (int)$this->input->post('formulir_id', TRUE);
        $sasaran             = trim((string)$this->input->post('sasaran', TRUE));
        $fungsi_kerja        = trim((string)$this->input->post('fungsi_kerja', TRUE));
        $nama_risiko         = trim((string)$this->input->post('nama_risiko', TRUE));

        if (empty($sasaran) || empty($fungsi_kerja) || empty($nama_risiko)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Sasaran/Target, Fungsi Kerja, dan Nama Risiko wajib diisi.'
                 ]));
            return;
        }

        $sumber_risiko        = $this->input->post('sumber_risiko', TRUE);
        $faktor_sumber        = $this->input->post('faktor_sumber', TRUE);
        $kategori_dampak      = $this->input->post('kategori_dampak', TRUE);
        $skala_dampak_inheren = $this->input->post('skala_dampak_inheren', TRUE);
        $skala_probabilitas   = $this->input->post('skala_probabilitas', TRUE);
        $skala_risiko_inheren = $this->input->post('skala_risiko_inheren', TRUE);

        $skala_dampak_residual      = $this->input->post('skala_dampak_residual', TRUE);
        $skala_kemungkinan_residual = $this->input->post('skala_kemungkinan_residual', TRUE);
        $skala_risiko_residual      = $this->input->post('skala_risiko_residual', TRUE);

        $payload = [
            'master' => [
                'sasaran'      => $sasaran,
                'fungsi_kerja' => $fungsi_kerja,
                'created_id'   => (string)($this->session->userdata('username') ?? $this->session->userdata('nama') ?? 'system')
            ],
            'tab1' => [
                'nama_risiko'         => $nama_risiko,
                'kategori_risiko'     => trim((string)$this->input->post('kategori_risiko', TRUE)) ?: null,
                'peristiwa_risiko'    => trim((string)$this->input->post('peristiwa_risiko', TRUE)) ?: null,
                'penyebab_risiko'     => trim((string)$this->input->post('penyebab_risiko', TRUE)) ?: null,
                'dampak_kualitatif'   => trim((string)$this->input->post('dampak_kualitatif', TRUE)) ?: null,
                'sumber_risiko'       => ($sumber_risiko !== null && $sumber_risiko !== '') ? (int)$sumber_risiko : null,
                'faktor_sumber'       => ($faktor_sumber !== null && $faktor_sumber !== '') ? (int)$faktor_sumber : null,
                'kontrol_saat_ini'    => trim((string)$this->input->post('kontrol_saat_ini', TRUE)) ?: null,
                'efektifitas_kontrol' => trim((string)$this->input->post('efektifitas_kontrol', TRUE)) ?: null,
                'periode_terpapar'    => trim((string)$this->input->post('periode_terpapar', TRUE)) ?: null,
            ],
            'tab2' => [
                'eksposure_kuantitatif' => trim((string)$this->input->post('eksposure_kuantitatif', TRUE)) ?: null,
                'kategori_dampak'       => ($kategori_dampak !== null && $kategori_dampak !== '') ? (int)$kategori_dampak : null,
                'skala_dampak_inheren'  => ($skala_dampak_inheren !== null && $skala_dampak_inheren !== '') ? (int)$skala_dampak_inheren : null,
                'skala_probabilitas'    => ($skala_probabilitas !== null && $skala_probabilitas !== '') ? (int)$skala_probabilitas : null,
                'skala_risiko_inheren'  => ($skala_risiko_inheren !== null && $skala_risiko_inheren !== '') ? (int)$skala_risiko_inheren : 1,
            ],
            'tab3' => [
                'operasi_perlakuan'       => trim((string)$this->input->post('operasi_perlakuan', TRUE)) ?: null,
                'jenis_rencana_perlakuan' => trim((string)$this->input->post('jenis_rencana_perlakuan', TRUE)) ?: null,
                'kegiatan_mitigasi'       => trim((string)$this->input->post('kegiatan_mitigasi', TRUE)) ?: null,
                'output_perlakuan'        => trim((string)$this->input->post('output_perlakuan', TRUE)) ?: null,
                'target_waktu'            => trim((string)$this->input->post('target_waktu', TRUE)) ?: null,
                'biaya_perlakuan'         => trim((string)$this->input->post('biaya_perlakuan', TRUE)) ?: null,
                'program_rkap'            => trim((string)$this->input->post('program_rkap', TRUE)) ?: null,
                'pic'                     => trim((string)$this->input->post('pic', TRUE)) ?: null,
            ],
            'tab4' => [
                'eksposure_residual'         => trim((string)$this->input->post('eksposure_residual', TRUE)) ?: null,
                'skala_dampak_residual'      => ($skala_dampak_residual !== null && $skala_dampak_residual !== '') ? (int)$skala_dampak_residual : null,
                'skala_kemungkinan_residual' => ($skala_kemungkinan_residual !== null && $skala_kemungkinan_residual !== '') ? (int)$skala_kemungkinan_residual : null,
                'skala_risiko_residual'      => ($skala_risiko_residual !== null && $skala_risiko_residual !== '') ? (int)$skala_risiko_residual : 1,
            ]
        ];

        if ($formulir_id > 0) {
            $ok = $this->FormulirRisiko_model->update_formulir($formulir_id, $payload);
            $msg = 'Data Formulir Profile Risiko berhasil diperbarui.';
        } else {
            $new_id = $this->FormulirRisiko_model->save_formulir($payload);
            $ok = ($new_id !== false && $new_id > 0);
            $msg = 'Data Formulir Profile Risiko berhasil disimpan.';
        }

        if (!$ok) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Gagal menyimpan data Formulir Profile Risiko ke database.'
                 ]));
            return;
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status'       => 'success',
                 'message'      => $msg,
                 'redirect_url' => site_url('menrisk/daftar_profil_risiko')
             ]));
    }

    /**
     * Halaman Progress Monitoring Profile Risiko (Tabel Riwayat Monitoring + Formulir Pengisian)
     */
    public function monitoring_profil_risiko($formulir_id) {
        $this->load->model('FormulirRisiko_model');

        $formulir_id = (int)$formulir_id;
        $formulir = $this->FormulirRisiko_model->get_formulir_by_id($formulir_id);

        if (!$formulir) {
            show_404();
            return;
        }

        $monitoring_list = $this->FormulirRisiko_model->get_monitoring_by_formulir_id($formulir_id);

        // Skala Probabilitas untuk Pilihan Skala Probabilitas Realisasi Current
        $qProb = $this->db->query("SELECT id, CAST(parameter AS NVARCHAR(MAX)) AS parameter, CAST(skala_1 AS NVARCHAR(MAX)) AS skala_1, CAST(skala_2 AS NVARCHAR(MAX)) AS skala_2, CAST(skala_3 AS NVARCHAR(MAX)) AS skala_3, CAST(skala_4 AS NVARCHAR(MAX)) AS skala_4, CAST(skala_5 AS NVARCHAR(MAX)) AS skala_5 FROM dbo.MNRSK_SkalaProbabilitas ORDER BY id ASC");
        $skala_probabilitas = ($qProb && is_object($qProb)) ? $qProb->result() : [];

        $data = [
            'title'              => 'Progress Monitoring Profile Risiko — IFPRO Command Center',
            'current_user'       => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu'        => 'daftar_profil_risiko',
            'is_dashboard'       => false,
            'formulir'           => $formulir,
            'monitoring_list'    => $monitoring_list,
            'skala_probabilitas' => $skala_probabilitas
        ];

        $this->load->view('monitoring_profil_risiko_view', $data);
    }

    /**
     * Endpoint AJAX untuk mengambil data 1 record monitoring (untuk Edit modal)
     */
    public function detail_monitoring_profil_risiko($id) {
        $this->load->model('FormulirRisiko_model');
        $item = $this->FormulirRisiko_model->get_monitoring_by_id((int)$id);

        if (!$item) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Data progress monitoring tidak ditemukan.'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => $item
            ]));
    }

    /**
     * Endpoint AJAX untuk menyimpan / memperbarui data Progress Monitoring Profile Risiko
     */
    public function save_monitoring_profil_risiko() {
        $this->load->model('FormulirRisiko_model');

        $monitoring_id = (int)$this->input->post('monitoring_id', TRUE);
        $formulir_id   = (int)$this->input->post('formulir_id', TRUE);
        $tahun         = (int)$this->input->post('tahun', TRUE);
        $periode       = trim((string)$this->input->post('periode', TRUE));
        $periode_tipe  = trim((string)$this->input->post('periode_tipe', TRUE)) ?: 'triwulan';

        if ($formulir_id <= 0 || empty($tahun) || empty($periode)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Parameter Formulir Risiko, Tahun, dan Periode wajib diisi.'
                ]));
        }

        $data = [
            'formulir_id'                    => $formulir_id,
            'periode_tipe'                   => $periode_tipe,
            'tahun'                          => $tahun,
            'periode'                        => $periode,
            'status_progres_tindak_lanjut'   => trim((string)$this->input->post('status_progres_tindak_lanjut', TRUE)) ?: null,
            'realisasi_biaya'                => trim((string)$this->input->post('realisasi_biaya', TRUE)) ?: null,
            'proses_tindak_lanjut_persen'    => trim((string)$this->input->post('proses_tindak_lanjut_persen', TRUE)) ?: null,
            'persentase_per_risiko'          => trim((string)$this->input->post('persentase_per_risiko', TRUE)) ?: null,
            'eksposur_kuantitatif_current'        => trim((string)$this->input->post('eksposur_kuantitatif_current', TRUE)) ?: null,
            'skala_dampak_realisasi_current'      => ($this->input->post('skala_dampak_realisasi_current', TRUE) !== null && $this->input->post('skala_dampak_realisasi_current', TRUE) !== '') ? (int)$this->input->post('skala_dampak_realisasi_current', TRUE) : null,
            'skala_probabilitas_realisasi_current'=> ($this->input->post('skala_probabilitas_realisasi_current', TRUE) !== null && $this->input->post('skala_probabilitas_realisasi_current', TRUE) !== '') ? (int)$this->input->post('skala_probabilitas_realisasi_current', TRUE) : null,
            'skala_risiko_realisasi'              => ($this->input->post('skala_risiko_realisasi', TRUE) !== null && $this->input->post('skala_risiko_realisasi', TRUE) !== '') ? (int)$this->input->post('skala_risiko_realisasi', TRUE) : 1,
            'kendala'                        => trim((string)$this->input->post('kendala', TRUE)) ?: null,
            'action_plan'                    => trim((string)$this->input->post('action_plan', TRUE)) ?: null,
            'created_id'                     => (string)($this->session->userdata('username') ?? $this->session->userdata('nama') ?? 'system')
        ];

        if ($monitoring_id > 0) {
            $ok = $this->FormulirRisiko_model->update_monitoring($monitoring_id, $data);
            $msg = 'Data Progress Monitoring berhasil diperbarui.';
        } else {
            $ok = $this->FormulirRisiko_model->save_monitoring($data);
            $msg = 'Data Progress Monitoring berhasil disimpan.';
        }

        if (!$ok) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan data Progress Monitoring ke database.'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => $msg
            ]));
    }

    /**
     * Endpoint AJAX untuk menghapus 1 data Progress Monitoring
     */
    public function delete_monitoring_profil_risiko($id) {
        $this->load->model('FormulirRisiko_model');
        $ok = $this->FormulirRisiko_model->delete_monitoring((int)$id);

        if (!$ok) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Gagal menghapus data progress monitoring.'
                ]));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'message' => 'Data Progress Monitoring berhasil dihapus.'
            ]));
    }
}
