<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: KriteriaDampakKemungkinan
 * Master Data — Kriteria Dampak & Kemungkinan
 * 
 * Mengelola 3 sub-kriteria:
 * 1. Skala Dampak Kuantitatif
 * 2. Skala Dampak Kualitatif
 * 3. Skala Probabilitas
 */
class KriteriaDampakKemungkinan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KriteriaDampakKemungkinan_model', 'kriteria_model');
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
        $data = [
            'title'        => 'Master Data — Kriteria Dampak & Kemungkinan',
            'current_user' => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu'  => 'm_kriteria_dampak_kemungkinan'
        ];

        $this->load->view('master_data/kriteria_dampak_kemungkinan/index', $data);
    }

    /**
     * AJAX Endpoint: Mengambil data untuk tab tertentu
     */
    public function get_data($tab = 'kuantitatif') {
        $valid_tabs = ['kuantitatif', 'kualitatif', 'probabilitas'];
        if (!in_array($tab, $valid_tabs, true)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Tab tidak valid.']));
            return;
        }

        switch ($tab) {
            case 'kuantitatif':
                $items = $this->kriteria_model->get_all_kuantitatif();
                break;
            case 'kualitatif':
                $items = $this->kriteria_model->get_all_kualitatif();
                break;
            case 'probabilitas':
                $items = $this->kriteria_model->get_all_probabilitas();
                break;
            default:
                $items = [];
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status' => 'success',
                 'tab'    => $tab,
                 'data'   => $items
             ]));
    }

    /**
     * AJAX Endpoint: Mengambil 1 item untuk modal edit
     */
    public function get_item($tab, $id) {
        $id = (int)$id;
        $item = null;

        if ($tab === 'kuantitatif') {
            $item = $this->kriteria_model->get_kuantitatif($id);
        } elseif ($tab === 'kualitatif') {
            $item = $this->kriteria_model->get_kualitatif($id);
        } elseif ($tab === 'probabilitas') {
            $item = $this->kriteria_model->get_probabilitas($id);
        }

        if (!$item) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']));
            return;
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status' => 'success',
                 'data'   => $item
             ]));
    }

    /**
     * AJAX Endpoint: Tambah data baru
     */
    public function save($tab) {
        if ($tab === 'kuantitatif') {
            $skala     = $this->input->post('skala', TRUE);
            $range  = trim((string)$this->input->post('range', FALSE));
            $deskripsi = trim((string)$this->input->post('deskripsi', TRUE));

            if ($skala === '' || $skala === null || empty($range) || empty($deskripsi)) {
                $this->_send_error('Semua field (Skala, Range, dan Deskripsi) wajib diisi.');
                return;
            }

            if ($this->kriteria_model->is_duplicate_kuantitatif($skala)) {
                $this->_send_error("Skala Dampak Kuantitatif tingkat {$skala} sudah terdaftar.");
                return;
            }

            $success = $this->kriteria_model->create_kuantitatif([
                'skala'     => $skala,
                'range'  => $range,
                'deskripsi' => $deskripsi
            ]);

            if ($success) {
                $this->_send_success('Data Skala Dampak Kuantitatif berhasil ditambahkan.');
            } else {
                $this->_send_error('Gagal menambahkan data Skala Dampak Kuantitatif.');
            }

        } elseif ($tab === 'kualitatif') {
            $judul   = trim((string)$this->input->post('judul', TRUE));
            $risiko  = trim((string)$this->input->post('risiko', TRUE));
            $skala_1 = trim((string)$this->input->post('skala_1', TRUE));
            $skala_2 = trim((string)$this->input->post('skala_2', TRUE));
            $skala_3 = trim((string)$this->input->post('skala_3', TRUE));
            $skala_4 = trim((string)$this->input->post('skala_4', TRUE));
            $skala_5 = trim((string)$this->input->post('skala_5', TRUE));

            if (empty($judul) || empty($risiko) || empty($skala_1) || empty($skala_2) || empty($skala_3) || empty($skala_4) || empty($skala_5)) {
                $this->_send_error('Seluruh kolom (Judul, Risiko, dan Skala 1 s/d 5) wajib diisi.');
                return;
            }

            // if ($this->kriteria_model->is_duplicate_kualitatif($judul)) {
            //     $this->_send_error("Aspek Kualitatif dengan judul '{$judul}' sudah terdaftar.");
            //     return;
            // }

            $success = $this->kriteria_model->create_kualitatif([
                'judul'   => $judul,
                'risiko'  => $risiko,
                'skala_1' => $skala_1,
                'skala_2' => $skala_2,
                'skala_3' => $skala_3,
                'skala_4' => $skala_4,
                'skala_5' => $skala_5
            ]);

            if ($success) {
                $this->_send_success('Data Skala Dampak Kualitatif berhasil ditambahkan.');
            } else {
                $this->_send_error('Gagal menambahkan data Skala Dampak Kualitatif.');
            }

        } elseif ($tab === 'probabilitas') {
            $parameter = trim((string)$this->input->post('parameter', FALSE));
            $skala_1   = trim((string)$this->input->post('skala_1', FALSE));
            $skala_2   = trim((string)$this->input->post('skala_2', FALSE));
            $skala_3   = trim((string)$this->input->post('skala_3', FALSE));
            $skala_4   = trim((string)$this->input->post('skala_4', FALSE));
            $skala_5   = trim((string)$this->input->post('skala_5', FALSE));

            if (empty($parameter) || empty($skala_1) || empty($skala_2) || empty($skala_3) || empty($skala_4) || empty($skala_5)) {
                $this->_send_error('Seluruh kolom (Parameter dan Skala 1 s/d 5) wajib diisi.');
                return;
            }

            if ($this->kriteria_model->is_duplicate_probabilitas($parameter)) {
                $this->_send_error("Parameter Probabilitas '{$parameter}' sudah terdaftar.");
                return;
            }

            $success = $this->kriteria_model->create_probabilitas([
                'parameter' => $parameter,
                'skala_1'   => $skala_1,
                'skala_2'   => $skala_2,
                'skala_3'   => $skala_3,
                'skala_4'   => $skala_4,
                'skala_5'   => $skala_5
            ]);

            if ($success) {
                $this->_send_success('Data Skala Probabilitas berhasil ditambahkan.');
            } else {
                $this->_send_error('Gagal menambahkan data Skala Probabilitas.');
            }

        } else {
            $this->_send_error('Tab tidak valid.');
        }
    }

    /**
     * AJAX Endpoint: Simpan pembaruan data (Edit)
     */
    public function update($tab) {
        $id = (int)$this->input->post('id', TRUE);
        if ($id <= 0) {
            $this->_send_error('ID data tidak valid.');
            return;
        }

        if ($tab === 'kuantitatif') {
            $existing = $this->kriteria_model->get_kuantitatif($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan.');
                return;
            }

            $skala     = $this->input->post('skala', TRUE);
            $range  = trim((string)$this->input->post('range', FALSE));
            $deskripsi = trim((string)$this->input->post('deskripsi', FALSE));

            if ($skala === '' || $skala === null || empty($range) || empty($deskripsi)) {
                $this->_send_error('Semua field (Skala, Range, dan Deskripsi) wajib diisi.');
                return;
            }

            if ($this->kriteria_model->is_duplicate_kuantitatif($skala, $id)) {
                $this->_send_error("Skala Dampak Kuantitatif tingkat {$skala} sudah terdaftar pada data lain.");
                return;
            }

            $success = $this->kriteria_model->update_kuantitatif($id, [
                'skala'     => $skala,
                'range'  => $range,
                'deskripsi' => $deskripsi
            ]);

            if ($success) {
                $this->_send_success('Data Skala Dampak Kuantitatif berhasil diperbarui.');
            } else {
                $this->_send_error('Gagal memperbarui data Skala Dampak Kuantitatif.');
            }

        } elseif ($tab === 'kualitatif') {
            $existing = $this->kriteria_model->get_kualitatif($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan.');
                return;
            }

            $judul   = trim((string)$this->input->post('judul', TRUE));
            $risiko  = trim((string)$this->input->post('risiko', TRUE));
            $skala_1 = trim((string)$this->input->post('skala_1', TRUE));
            $skala_2 = trim((string)$this->input->post('skala_2', TRUE));
            $skala_3 = trim((string)$this->input->post('skala_3', TRUE));
            $skala_4 = trim((string)$this->input->post('skala_4', TRUE));
            $skala_5 = trim((string)$this->input->post('skala_5', TRUE));

            if (empty($judul) || empty($risiko) || empty($skala_1) || empty($skala_2) || empty($skala_3) || empty($skala_4) || empty($skala_5)) {
                $this->_send_error('Seluruh kolom wajib diisi.');
                return;
            }

            if ($this->kriteria_model->is_duplicate_kualitatif($judul, $id)) {
                $this->_send_error("Aspek Kualitatif dengan judul '{$judul}' sudah terdaftar pada data lain.");
                return;
            }

            $success = $this->kriteria_model->update_kualitatif($id, [
                'judul'   => $judul,
                'risiko'  => $risiko,
                'skala_1' => $skala_1,
                'skala_2' => $skala_2,
                'skala_3' => $skala_3,
                'skala_4' => $skala_4,
                'skala_5' => $skala_5
            ]);

            if ($success) {
                $this->_send_success('Data Skala Dampak Kualitatif berhasil diperbarui.');
            } else {
                $this->_send_error('Gagal memperbarui data Skala Dampak Kualitatif.');
            }

        } elseif ($tab === 'probabilitas') {
            $existing = $this->kriteria_model->get_probabilitas($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan.');
                return;
            }

            $parameter = trim((string)$this->input->post('parameter', FALSE));
            $skala_1   = trim((string)$this->input->post('skala_1', FALSE));
            $skala_2   = trim((string)$this->input->post('skala_2', FALSE));
            $skala_3   = trim((string)$this->input->post('skala_3', FALSE));
            $skala_4   = trim((string)$this->input->post('skala_4', FALSE));
            $skala_5   = trim((string)$this->input->post('skala_5', FALSE));

            if (empty($parameter) || empty($skala_1) || empty($skala_2) || empty($skala_3) || empty($skala_4) || empty($skala_5)) {
                $this->_send_error('Seluruh kolom wajib diisi.');
                return;
            }

            if ($this->kriteria_model->is_duplicate_probabilitas($parameter, $id)) {
                $this->_send_error("Parameter Probabilitas '{$parameter}' sudah terdaftar pada data lain.");
                return;
            }

            $success = $this->kriteria_model->update_probabilitas($id, [
                'parameter' => $parameter,
                'skala_1'   => $skala_1,
                'skala_2'   => $skala_2,
                'skala_3'   => $skala_3,
                'skala_4'   => $skala_4,
                'skala_5'   => $skala_5
            ]);

            if ($success) {
                $this->_send_success('Data Skala Probabilitas berhasil diperbarui.');
            } else {
                $this->_send_error('Gagal memperbarui data Skala Probabilitas.');
            }

        } else {
            $this->_send_error('Tab tidak valid.');
        }
    }

    /**
     * AJAX Endpoint: Hapus data
     */
    public function delete($tab, $id = null) {
        $id = $id ?? $this->input->post('id', TRUE);
        $id = (int)$id;

        if ($id <= 0) {
            $this->_send_error('ID data tidak valid.');
            return;
        }

        $success = false;

        if ($tab === 'kuantitatif') {
            $existing = $this->kriteria_model->get_kuantitatif($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan atau sudah dihapus.');
                return;
            }
            $success = $this->kriteria_model->delete_kuantitatif($id);

        } elseif ($tab === 'kualitatif') {
            $existing = $this->kriteria_model->get_kualitatif($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan atau sudah dihapus.');
                return;
            }
            $success = $this->kriteria_model->delete_kualitatif($id);

        } elseif ($tab === 'probabilitas') {
            $existing = $this->kriteria_model->get_probabilitas($id);
            if (!$existing) {
                $this->_send_error('Data tidak ditemukan atau sudah dihapus.');
                return;
            }
            $success = $this->kriteria_model->delete_probabilitas($id);

        } else {
            $this->_send_error('Tab tidak valid.');
            return;
        }

        if ($success) {
            $this->_send_success('Data berhasil dihapus.');
        } else {
            $this->_send_error('Gagal menghapus data.');
        }
    }

    // Helper Respon JSON
    private function _send_success($message, $extra = []) {
        $response = array_merge(['status' => 'success', 'message' => $message], $extra);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
    }

    private function _send_error($message) {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => $message]));
    }
}

