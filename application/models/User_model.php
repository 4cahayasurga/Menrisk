<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: User_model
 * Mengelola data pengguna (dbo.Login) dan relasi many-to-many ke dbo.organisation_d_3s
 * melalui tabel pivot dbo.MNRSK_user_departments
 */
class User_model extends CI_Model {

    protected $table       = 'dbo.Login';
    protected $table_pivot = 'dbo.MNRSK_user_departments';
    protected $table_dept  = 'dbo.organisation_d_3s';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Tentukan role berdasarkan UserID:
     * Hanya UserID 'admin' yang memiliki role 'admin', selebihnya 'user'
     */
    public function resolve_level($user_id) {
        return (strtolower(trim((string)$user_id)) === 'admin') ? 'admin' : 'user';
    }

    /**
     * Normalisasi baris dari dbo.Login agar seragam dengan struktur aplikasi
     */
    protected function normalize_user_row($row) {
        if (!$row) {
            return NULL;
        }
        $user_id   = trim((string)($row['UserID'] ?? $row['id'] ?? $row['username'] ?? ''));
        $user_name = trim((string)($row['UserName'] ?? $row['nama'] ?? $user_id));
        $active    = isset($row['Active']) ? trim((string)$row['Active']) : '1';
        $is_active = ($active === '0' || strtolower($active) === 'n') ? 0 : 1;
        $pass_ci   = (string)($row['PasswordCI'] ?? $row['password'] ?? '');

        return [
            'id'         => $user_id,
            'user_id'    => $user_id,
            'UserID'     => $user_id,
            'username'   => $user_id,
            'nama'       => $user_name !== '' ? $user_name : $user_id,
            'UserName'   => $user_name !== '' ? $user_name : $user_id,
            'password'   => $pass_ci,
            'PasswordCI' => $pass_ci,
            'level'      => $this->resolve_level($user_id),
            'is_active'  => $is_active,
            'Active'     => $is_active ? '1' : '0',
            'created_at' => $row['TimeEntry'] ?? null,
            'updated_at' => $row['LastLogin'] ?? null
        ];
    }

    /**
     * Ambil seluruh user dari dbo.Login beserta relasi department-nya
     */
    public function get_all() {
        $users = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('UserID, UserName, PasswordCI, Active, TimeEntry, LastLogin')
                                  ->from($this->table)
                                  ->order_by("CASE WHEN LOWER(LTRIM(RTRIM(UserID))) = 'admin' THEN 0 ELSE 1 END", 'ASC', FALSE)
                                  ->order_by('UserID', 'ASC')
                                  ->get();
                if ($query) {
                    foreach ($query->result_array() as $row) {
                        $u = $this->normalize_user_row($row);
                        if ($u && $u['id'] !== '') {
                            $u['departments'] = [];
                            $users[] = $u;
                        }
                    }
                }

                // Ambil seluruh relasi department sekaligus (menghindari N+1 query)
                $dept_map = [];
                $rel_query = $this->db->select('ud.user_id, d.id, d.d_3_name AS name, d.d_3_desc AS code, d.d_3_desc AS description, d.d_3_name, d.d_3_desc')
                                      ->from($this->table_pivot . ' ud')
                                      ->join($this->table_dept . ' d', 'd.id = ud.department_id', 'inner')
                                      ->order_by('d.d_3_name', 'ASC')
                                      ->get();
                if ($rel_query) {
                    foreach ($rel_query->result_array() as $rel) {
                        $key = strtolower(trim((string)$rel['user_id']));
                        $dept_map[$key][] = [
                            'id'          => (int)$rel['id'],
                            'name'        => $rel['name'],
                            'code'        => $rel['code'],
                            'description' => $rel['description'],
                            'd_3_name'    => $rel['d_3_name'],
                            'd_3_desc'    => $rel['d_3_desc']
                        ];
                    }
                }

                foreach ($users as &$u) {
                    $key = strtolower($u['id']);
                    $u['departments'] = $dept_map[$key] ?? [];
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_all error: ' . $e->getMessage());
        }

        return $users;
    }

    /**
     * Ambil detail 1 user berdasarkan UserID
     */
    public function get_by_id($id) {
        $user_id = trim((string)$id);
        if ($user_id === '') {
            return NULL;
        }
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->where('UserID', $user_id)->get($this->table);
                if ($query && $query->num_rows() > 0) {
                    $user = $this->normalize_user_row($query->row_array());
                    $user['departments'] = $this->get_departments_by_user_id($user['id']);
                    $user['department_ids'] = array_map('intval', array_column($user['departments'], 'id'));
                    return $user;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_by_id error: ' . $e->getMessage());
        }
        return NULL;
    }

    /**
     * Cari user berdasarkan UserID (untuk keperluan otentikasi login)
     */
    public function get_by_username($username) {
        return $this->get_by_id($username);
    }

    /**
     * Ambil department (dbo.organisation_d_3s) yang di-assign ke UserID tertentu
     */
    public function get_departments_by_user_id($user_id) {
        $user_id = trim((string)$user_id);
        if ($user_id === '') {
            return [];
        }

        $departments = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('d.id, d.d_3_name AS name, d.d_3_desc AS code, d.d_3_desc AS description, d.d_3_name, d.d_3_desc')
                                  ->from($this->table_dept . ' d')
                                  ->join($this->table_pivot . ' ud', 'd.id = ud.department_id')
                                  ->where('ud.user_id', $user_id)
                                  ->order_by('d.d_3_name', 'ASC')
                                  ->get();
                if ($query) {
                    foreach ($query->result_array() as $row) {
                        $row['id'] = (int)$row['id'];
                        $departments[] = $row;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_departments_by_user_id error: ' . $e->getMessage());
        }
        return $departments;
    }

    /**
     * Ambil semua department dengan status apakah aktif (is_assigned) untuk modal switch
     */
    public function get_department_matrix_for_user($user_id) {
        $all_depts = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('id, d_3_name AS name, d_3_desc AS code, d_3_desc AS description, d_3_name, d_3_desc')
                                  ->from($this->table_dept)
                                  ->order_by('id', 'ASC')
                                  ->get();
                if ($query) {
                    $all_depts = $query->result_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_department_matrix_for_user error: ' . $e->getMessage());
        }

        $assigned_ids = array_map('intval', array_column($this->get_departments_by_user_id($user_id), 'id'));

        foreach ($all_depts as &$d) {
            $d['id'] = (int)$d['id'];
            $d['is_assigned'] = in_array($d['id'], $assigned_ids, TRUE);
        }

        return $all_depts;
    }

    /**
     * Toggle relasi user (UserID) ke department (id organisation_d_3s)
     */
    public function toggle_department($user_id, $department_id, $enable) {
        $user_id       = trim((string)$user_id);
        $department_id = (int)$department_id;

        if ($user_id === '' || $department_id <= 0) {
            return FALSE;
        }

        try {
            if ($this->db && $this->db->conn_id) {
                if ($enable) {
                    $exists = $this->db->where('user_id', $user_id)
                                       ->where('department_id', $department_id)
                                       ->count_all_results($this->table_pivot);
                    if ($exists == 0) {
                        return $this->db->insert($this->table_pivot, [
                            'user_id'       => $user_id,
                            'department_id' => $department_id,
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                    return TRUE;
                } else {
                    return $this->db->where('user_id', $user_id)
                                    ->where('department_id', $department_id)
                                    ->delete($this->table_pivot);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::toggle_department error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Insert User baru ke dbo.Login
     */
    public function insert($data) {
        $user_id   = trim((string)($data['UserID'] ?? $data['username'] ?? $data['id'] ?? ''));
        $user_name = trim((string)($data['UserName'] ?? $data['nama'] ?? $user_id));
        $raw_pass  = (string)($data['PasswordCI'] ?? $data['password'] ?? '');
        $hashed    = $raw_pass !== '' ? password_hash($raw_pass, PASSWORD_BCRYPT) : '';
        $is_active = isset($data['is_active']) ? ((int)$data['is_active'] === 1 ? '1' : '0') : '1';

        $insert_row = [
            'UserID'     => substr($user_id, 0, 30),
            'UserName'   => substr($user_name, 0, 30),
            'Password'   => $hashed,
            'PasswordCI' => $hashed,
            'Active'     => $is_active,
            'TimeEntry'  => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->db && $this->db->conn_id) {
                $ok = $this->db->insert($this->table, $insert_row);
                return $ok ? $user_id : FALSE;
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::insert error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Update User pada dbo.Login (berdasarkan UserID)
     */
    public function update($id, $data) {
        $user_id = trim((string)$id);
        if ($user_id === '') {
            return FALSE;
        }

        $update_row = [];
        if (isset($data['nama']) || isset($data['UserName'])) {
            $update_row['UserName'] = substr(trim((string)($data['UserName'] ?? $data['nama'])), 0, 30);
        }
        if (isset($data['is_active']) || isset($data['Active'])) {
            $active_val = $data['Active'] ?? $data['is_active'];
            $update_row['Active'] = ((string)$active_val === '0') ? '0' : '1';
        }
        if (!empty($data['password']) || !empty($data['PasswordCI'])) {
            $raw_pass = (string)($data['PasswordCI'] ?? $data['password']);
            $hashed   = password_hash($raw_pass, PASSWORD_BCRYPT);
            $update_row['PasswordCI'] = $hashed;
        }

        if (empty($update_row)) {
            return TRUE;
        }

        try {
            if ($this->db && $this->db->conn_id) {
                return $this->db->where('UserID', $user_id)->update($this->table, $update_row);
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::update error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Hapus User dari dbo.Login berdasarkan UserID
     */
    public function delete($id) {
        $user_id = trim((string)$id);
        if ($user_id === '' || strtolower($user_id) === 'admin') {
            return FALSE;
        }

        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->where('user_id', $user_id)->delete($this->table_pivot);
                return $this->db->where('UserID', $user_id)->delete($this->table);
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::delete error: ' . $e->getMessage());
        }
        return FALSE;
    }
}
