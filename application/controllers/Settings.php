<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Settings
 * Mengelola Menu Settings -> User & Settings -> Department
 * Khusus role == 'admin'
 */
class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Department_model']);
        $this->load->library('session');
        $this->load->helper(['url', 'html', 'form']);

        // Proteksi Otorisasi: Wajib Login & Role Harus 'admin'
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        if ($this->session->userdata('level') !== 'admin') {
            show_error('Akses Ditolak: Seluruh menu di dalam Settings hanya dapat diakses oleh Administrator (role == admin).', 403, '403 Forbidden - Unauthorized');
        }
    }

    public function index() {
        redirect('settings/users');
    }

    // =========================================================================
    // 1. SETTINGS -> USER MANAGEMENT
    // =========================================================================

    /**
     * Tampilan daftar User & pengelolaan relasi department
     */
    public function users() {
        $data = [
            'title'       => 'User Management — Settings',
            'users'       => $this->User_model->get_all(),
            'departments' => $this->Department_model->get_all(),
            'current_user'=> [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu' => 'settings_user'
        ];

        $this->load->view('settings/users_view', $data);
    }

    /**
     * Simpan / Update User (AJAX / POST)
     */
    public function user_save() {
        $id       = $this->input->post('id', TRUE);
        $username = trim($this->input->post('username', TRUE));
        $nama     = trim($this->input->post('nama', TRUE));
        $password = trim($this->input->post('password', TRUE));
        $level    = trim($this->input->post('level', TRUE)) ?: 'user';
        $is_active= $this->input->post('is_active') !== NULL ? (int)$this->input->post('is_active') : 1;

        if (empty($username) || empty($nama)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Username dan Nama lengkap wajib diisi!'
            ]));
        }

        // Cek username unik jika tambah baru
        if (empty($id)) {
            if (empty($password)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Password wajib diisi untuk user baru!'
                ]));
            }
            $existing = $this->User_model->get_by_username($username);
            if ($existing) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Username "' . htmlspecialchars($username) . '" sudah digunakan!'
                ]));
            }

            $payload = [
                'username'  => $username,
                'password'  => $password,
                'nama'      => $nama,
                'level'     => $level,
                'is_active' => $is_active
            ];
            $saved_id = $this->User_model->insert($payload);
            $msg = 'User baru berhasil ditambahkan.';
        } else {
            // Mode Update
            $payload = [
                'username'  => $username,
                'nama'      => $nama,
                'level'     => $level,
                'is_active' => $is_active
            ];
            if (!empty($password)) {
                $payload['password'] = $password;
            }
            $this->User_model->update($id, $payload);
            $msg = 'Data user berhasil diperbarui.';
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'success',
            'message' => $msg
        ]));
    }

    /**
     * Hapus User
     */
    public function user_delete($id) {
        $id = (int)$id;
        // Cegah menghapus akun sendiri yang sedang login
        if ($id === (int)$this->session->userdata('user_id')) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login!'
            ]));
        }

        $this->User_model->delete($id);
        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'success',
            'message' => 'User berhasil dihapus.'
        ]));
    }

    /**
     * Ambil data department dan status aktif untuk user tertentu (Untuk Modal Switch)
     */
    public function user_departments($user_id) {
        $user_id = (int)$user_id;
        $user = $this->User_model->get_by_id($user_id);
        if (!$user) {
            return $this->output->set_status_header(404)->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'User tidak ditemukan'
            ]));
        }

        $matrix = $this->User_model->get_department_matrix_for_user($user_id);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'success',
            'user'   => [
                'id'       => $user['id'],
                'nama'     => $user['nama'],
                'username' => $user['username'],
                'level'    => $user['level']
            ],
            'departments' => $matrix
        ]));
    }

    /**
     * Action Switch: Toggle relasi user ke department
     * Sesuai instruksi: "diaktifkan melalui switch"
     */
    public function toggle_user_department() {
        $user_id       = (int)$this->input->post('user_id');
        $department_id = (int)$this->input->post('department_id');
        $enabled       = (bool)$this->input->post('enabled');

        if (!$user_id || !$department_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Parameter user_id dan department_id diperlukan.'
            ]));
        }

        $result = $this->User_model->toggle_department($user_id, $department_id, $enabled);

        if ($result) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'success',
                'message' => $enabled ? 'Akses department diaktifkan.' : 'Akses department dinonaktifkan.'
            ]));
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'error',
            'message' => 'Gagal mengubah relasi department.'
        ]));
    }

    // =========================================================================
    // 2. SETTINGS -> DEPARTMENT MANAGEMENT
    // =========================================================================

    /**
     * Tampilan daftar Department
     */
    public function departments() {
        $data = [
            'title'       => 'Department Management — Settings',
            'departments' => $this->Department_model->get_all(),
            'current_user'=> [
                'nama'  => $this->session->userdata('nama'),
                'level' => $this->session->userdata('level')
            ],
            'active_menu' => 'settings_department'
        ];

        $this->load->view('settings/departments_view', $data);
    }

    /**
     * Simpan / Update Department
     */
    public function department_save() {
        $id          = $this->input->post('id', TRUE);
        $name        = trim($this->input->post('name', TRUE));
        $code        = trim($this->input->post('code', TRUE));
        $description = trim($this->input->post('description', TRUE));

        if (empty($name)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Nama Department wajib diisi!'
            ]));
        }

        $payload = [
            'name'        => $name,
            'code'        => strtoupper($code),
            'description' => $description
        ];

        if (empty($id)) {
            $this->Department_model->insert($payload);
            $msg = 'Department baru berhasil ditambahkan.';
        } else {
            $this->Department_model->update($id, $payload);
            $msg = 'Department berhasil diperbarui.';
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'success',
            'message' => $msg
        ]));
    }

    /**
     * Hapus Department
     */
    public function department_delete($id) {
        $id = (int)$id;
        $this->Department_model->delete($id);
        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Department berhasil dihapus.'
        ]));
    }
}
