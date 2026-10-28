<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Auth
 * Menangani Otentikasi Login & Logout Pengguna
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $this->login();
    }

    /**
     * Halaman Login
     */
    public function login() {
        // Jika sudah login, langsung arahkan ke Dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('menrisk');
        }

        $data = [
            'title' => 'Login — IFPRO Risk Command Center',
            'error' => $this->session->flashdata('error')
        ];

        $this->load->view('auth/login_view', $data);
    }

    /**
     * Proses Login (Submit form atau AJAX)
     */
    public function process_login() {
        $username = trim($this->input->post('username', TRUE));
        $password = trim($this->input->post('password', TRUE));

        if (empty($username) || empty($password)) {
            $msg = 'Username dan password wajib diisi.';
            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $msg]));
            }
            $this->session->set_flashdata('error', $msg);
            redirect('auth/login');
        }

        $user = $this->User_model->get_by_username($username);

        // Verifikasi User & Password
        if ($user) {
            if ((int)$user['is_active'] !== 1) {
                $msg = 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.';
                if ($this->input->is_ajax_request()) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                }
                $this->session->set_flashdata('error', $msg);
                redirect('auth/login');
            }

            $password_valid = password_verify($password, $user['password']);

            // Fallback plain text / md5 jika migrasi data legacy
            if (!$password_valid && ($password === $user['password'] || md5($password) === $user['password'])) {
                $password_valid = TRUE;
            }

            if ($password_valid) {
                // Set Session
                $session_data = [
                    'user_id'   => (int)$user['id'],
                    'username'  => $user['username'],
                    'nama'      => $user['nama'],
                    'level'     => $user['level'], // 'admin' atau 'user'
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($session_data);

                if ($this->input->is_ajax_request()) {
                    return $this->output->set_content_type('application/json')->set_output(json_encode([
                        'status'   => 'success',
                        'message'  => 'Login berhasil!',
                        'redirect' => site_url('menrisk')
                    ]));
                }

                redirect('menrisk');
            }
        }

        $msg = 'Username atau password salah.';
        if ($this->input->is_ajax_request()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $msg]));
        }

        $this->session->set_flashdata('error', $msg);
        redirect('auth/login');
    }

    /**
     * Ganti Password (AJAX POST) — untuk user yang sedang login
     * Endpoint: auth/change_password
     * Body    : current_password, new_password, confirm_password
     * Response: JSON { status: 'success'|'error', message: string }
     */
    public function change_password() {
        $json = function ($status, $message, $code = 200) {
            return $this->output->set_status_header($code)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => $status, 'message' => $message]));
        };

        if (!$this->session->userdata('logged_in')) {
            return $json('error', 'Sesi Anda telah berakhir. Silakan login kembali.', 401);
        }

        if ($this->input->method() !== 'post') {
            return $json('error', 'Metode request tidak diizinkan.', 405);
        }

        // Sanitasi dibuat identik dengan process_login() (XSS filter + trim) agar hash selalu cocok saat login
        $current_password = trim((string)$this->input->post('current_password', TRUE));
        $new_password     = trim((string)$this->input->post('new_password', TRUE));
        $confirm_password = trim((string)$this->input->post('confirm_password', TRUE));

        if ($current_password === '' || $new_password === '' || $confirm_password === '') {
            return $json('error', 'Seluruh field password wajib diisi.');
        }

        if (strlen($new_password) < 6) {
            return $json('error', 'Password baru minimal 6 karakter.');
        }

        if ($new_password !== $confirm_password) {
            return $json('error', 'Konfirmasi password baru tidak sama.');
        }

        if ($new_password === $current_password) {
            return $json('error', 'Password baru harus berbeda dari password lama.');
        }

        $user_id = (int)$this->session->userdata('user_id');
        $user    = $this->User_model->get_by_id($user_id);
        if (!$user) {
            return $json('error', 'User tidak ditemukan.', 404);
        }

        // Verifikasi password lama (dukung hash bcrypt + fallback legacy plain/md5 seperti process_login)
        $valid = password_verify($current_password, $user['password'])
              || $current_password === $user['password']
              || md5($current_password) === $user['password'];

        if (!$valid) {
            return $json('error', 'Password lama yang Anda masukkan salah.');
        }

        // User_model::update() otomatis melakukan password_hash() (bcrypt)
        if (!$this->User_model->update($user_id, ['password' => $new_password])) {
            return $json('error', 'Gagal menyimpan password baru. Silakan coba lagi.', 500);
        }

        return $json('success', 'Password berhasil diperbarui.');
    }

    /**
     * Logout
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
