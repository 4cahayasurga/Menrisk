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
        $user_id   = (int)$this->session->userdata('user_id');
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
        $user_id   = (int)$this->session->userdata('user_id');
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
        $user_id   = (int)$this->session->userdata('user_id');
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
}
