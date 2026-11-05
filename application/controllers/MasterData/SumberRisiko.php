<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: SumberRisiko
 * Master Data — Sumber Risiko
 */
class SumberRisiko extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('SumberRisiko_model');
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
            'title'        => 'Master Data — Sumber Risiko',
            'current_user' => [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'sumber_risiko' => $this->SumberRisiko_model->get_all_sumber_risiko(),
            'faktor_risiko' => $this->SumberRisiko_model->get_all_faktor_risiko(),
            'active_menu'   => 'm_sumber_risiko'
        ];

        $this->load->view('master_data/sumber_risiko/index', $data);
    }

    public function save()
    {
        $sumberId = trim((string)$this->input->post('sumber_id', TRUE));
        $faktor   = trim((string)$this->input->post('faktor', TRUE));

        if (empty($sumberId) || empty($faktor)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Sumber risiko dan faktor risiko wajib diisi.'
                 ]));
            return;
        }

        // Validasi duplikasi: kombinasi sumber_risiko_id dan nama faktor risiko tidak boleh ada yang kembar
        if ($this->SumberRisiko_model->is_duplicate_faktor($sumberId, $faktor)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Faktor risiko tersebut sudah terdaftar pada sumber risiko yang dipilih.'
                 ]));
            return;
        }

        $userId = $this->session->userdata('user_id') ?? 'ADMIN';
        $success = $this->SumberRisiko_model->create_item($sumberId, $faktor, $userId);

        if ($success) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'success',
                    'message' => 'Berhasil menambah faktor risiko baru.'
                ]));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ]));
        return;
    }

    public function update()
    {
        $id       = (int) $this->input->post('id', TRUE);
        $sumberId = trim((string)$this->input->post('sumber_id', TRUE));
        $faktor   = trim((string)$this->input->post('faktor', TRUE));

        if (empty($id) || empty($sumberId) || empty($faktor)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Semua kolom wajib diisi.'
                 ]));
            return;
        }

        $existing = $this->SumberRisiko_model->get_item($id);
        if (!$existing) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Data faktor risiko tidak ditemukan.'
                 ]));
            return;
        }

        // Validasi duplikasi (kecualikan id yang sedang diedit)
        if ($this->SumberRisiko_model->is_duplicate_faktor($sumberId, $faktor, $id)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Faktor risiko tersebut sudah terdaftar pada sumber risiko yang dipilih.'
                 ]));
            return;
        }

        $success = $this->SumberRisiko_model->update_item($id, $sumberId, $faktor);

        if ($success) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'success',
                     'message' => 'Data faktor risiko berhasil diperbarui.'
                 ]));
            return;
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status'  => 'error',
                 'message' => 'Terjadi kesalahan saat memperbarui data.'
             ]));
        return;
    }

    public function delete($id = null)
    {
        $id = $id ?? $this->input->post('id', TRUE);
        $id = (int) $id;

        if (empty($id)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'ID faktor risiko tidak valid.'
                 ]));
            return;
        }

        $existing = $this->SumberRisiko_model->get_item($id);
        if (!$existing) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'error',
                     'message' => 'Data tidak ditemukan atau sudah dihapus.'
                 ]));
            return;
        }

        $success = $this->SumberRisiko_model->delete_item($id);

        if ($success) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status'  => 'success',
                     'message' => 'Data faktor risiko berhasil dihapus.'
                 ]));
            return;
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status'  => 'error',
                 'message' => 'Gagal menghapus data faktor risiko.'
             ]));
        return;
    }
}
