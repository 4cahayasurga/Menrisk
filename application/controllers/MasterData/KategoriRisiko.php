<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: KategoriRisiko
 * Master Data — Taksonomi & Kategori Risiko KBUMN
 * Mengambil data langsung dari tabel database via KategoriRisiko_model:
 * - Table Parent (T1): dbo.MNRSK_KategoriRisiko_tema
 * - Table Child L1 (T2): dbo.MNRSK_KategoriRisiko_kat_kbumn
 * - Table Child L2 (T3): dbo.MNRSK_KategoriRisiko_kat_perusahaan
 * - Table Child L3 (T4): dbo.MNRSK_KategoriRisiko_per_perusahaan
 */
class KategoriRisiko extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KategoriRisiko_model');
        $this->load->library('session');
        $this->load->helper(['url', 'html', 'form']);

        // Proteksi Otorisasi: Wajib Login & Role Harus 'admin'
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        if ($this->session->userdata('level') !== 'admin') {
            show_error('Akses Ditolak: Seluruh menu di dalam Master Data hanya dapat diakses oleh Administrator (role == admin).', 403, '403 Forbidden - Unauthorized');
        }
    }

    public function index() {
        $categories = $this->KategoriRisiko_model->get_all_hierarchy();
        $stats      = $this->KategoriRisiko_model->get_stats();

        $data = [
            'title'        => 'Master Data — Kategori Risiko',
            'categories'   => $categories,
            'stats'        => $stats,
            't1_options'   => $this->KategoriRisiko_model->get_t1_options(),
            't2_options'   => $this->KategoriRisiko_model->get_t2_options(),
            't3_options'   => $this->KategoriRisiko_model->get_t3_options(),
            'current_user' => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu'  => 'm_kategori_risiko'
        ];

        $this->load->view('master_data/kategori_risiko/index', $data);
    }

    /**
     * Endpoint AJAX untuk menyimpan penambahan data kategori risiko baru ke database
     */
    public function create() {
        $level     = $this->input->post('level', TRUE);
        $code      = $this->input->post('code', TRUE);
        $nama      = $this->input->post('nama', TRUE);
        $definisi  = $this->input->post('definisi', TRUE);
        $parent_id = $this->input->post('parent_id', TRUE);

        if (empty($level) || empty($code) || empty($nama)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Tingkat Level, Kode, dan Nama wajib diisi.'
                 ]));
            return;
        }

        if (in_array($level, ['T2', 'T3', 'T4']) && empty($parent_id)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Parent kategori wajib dipilih untuk tingkatan ini.'
                 ]));
            return;
        }

        // Validasi duplikasi code atau nama
        $clean_parent_id = (int)preg_replace('/^[a-zA-Z0-9]+-/', '', (string)$parent_id);
        if ($this->KategoriRisiko_model->is_duplicate($level, $code, $nama, $clean_parent_id)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Kode atau nama kategori risiko tersebut sudah terdaftar pada tingkatan ini.'
                 ]));
            return;
        }

        $new_id = $this->KategoriRisiko_model->insert_item($level, [
            'code'       => $code,
            'nama'       => $nama,
            'definisi'   => $definisi,
            'parent_id'  => $parent_id,
            'created_id' => $this->session->userdata('user_id') ?? 'ADMIN'
        ]);

        if ($new_id) {
            $response = [
                'status'  => 'success',
                'message' => "Data baru [{$code}] {$nama} berhasil ditambahkan.",
                'new_id'  => $new_id
            ];
        } else {
            $response = [
                'status'  => 'error',
                'message' => "Gagal menambahkan data baru ke database. Silakan periksa kembali."
            ];
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
    }

    /**
     * Endpoint AJAX untuk menyimpan pembaruan data kategori risiko langsung ke tabel database
     */
    public function update() {
        $id       = $this->input->post('id', TRUE);
        $code     = $this->input->post('code', TRUE);
        $nama     = $this->input->post('nama', TRUE);
        $definisi = $this->input->post('definisi', TRUE);
        $level    = $this->input->post('level', TRUE);

        if (empty($id) || empty($level)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Parameter ID dan Level wajib diisi.'
                 ]));
            return;
        }

        $success = $this->KategoriRisiko_model->update_item($level, $id, [
            'code'     => $code,
            'nama'     => $nama,
            'definisi' => $definisi
        ]);

        if ($success) {
            $response = [
                'status'  => 'success',
                'message' => "Data [{$code}] {$nama} berhasil diperbarui di database.",
                'data'    => [
                    'id'       => $id,
                    'code'     => $code,
                    'nama'     => $nama,
                    'definisi' => $definisi,
                    'level'    => $level
                ]
            ];
        } else {
            $response = [
                'status'  => 'error',
                'message' => "Gagal memperbarui data [{$code}]. Silakan periksa kembali."
            ];
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
    }
}
