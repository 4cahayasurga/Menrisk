<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: User_model
 * Mengelola data pengguna (dbo.users) dan relasi many-to-many ke dbo.departments
 */
class User_model extends CI_Model {

    protected $table = 'dbo.users';
    protected $table_pivot = 'dbo.user_departments';
    protected $table_dept = 'dbo.departments';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil seluruh user beserta relasi department-nya
     */
    public function get_all() {
        $users = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('id, username, nama, level, is_active, created_at, updated_at')
                                  ->from($this->table)
                                  ->order_by('id', 'ASC')
                                  ->get();
                if ($query) {
                    $users = $query->result_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_all error: ' . $e->getMessage());
        }

        // Ambil relasi department untuk setiap user
        foreach ($users as &$u) {
            $u['departments'] = $this->get_departments_by_user_id($u['id']);
        }

        return $users;
    }

    /**
     * Ambil detail 1 user berdasarkan ID
     */
    public function get_by_id($id) {
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->where('id', (int)$id)->get($this->table);
                if ($query && $query->num_rows() > 0) {
                    $user = $query->row_array();
                    $user['departments'] = $this->get_departments_by_user_id($user['id']);
                    $user['department_ids'] = array_column($user['departments'], 'id');
                    return $user;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_by_id error: ' . $e->getMessage());
        }
        return NULL;
    }

    /**
     * Cari user berdasarkan username (untuk keperluan otentikasi login)
     */
    public function get_by_username($username) {
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->where('username', trim($username))->get($this->table);
                if ($query && $query->num_rows() > 0) {
                    return $query->row_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::get_by_username error: ' . $e->getMessage());
        }
        return NULL;
    }

    /**
     * Ambil department yang di-assign ke user_id tertentu
     */
    public function get_departments_by_user_id($user_id) {
        $departments = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('d.id, d.name, d.code')
                                  ->from($this->table_dept . ' d')
                                  ->join($this->table_pivot . ' ud', 'd.id = ud.department_id')
                                  ->where('ud.user_id', (int)$user_id)
                                  ->order_by('d.name', 'ASC')
                                  ->get();
                if ($query) {
                    $departments = $query->result_array();
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
                $query = $this->db->select('id, name, code, description')
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

        $assigned_ids = array_column($this->get_departments_by_user_id($user_id), 'id');

        foreach ($all_depts as &$d) {
            $d['is_assigned'] = in_array((int)$d['id'], array_map('intval', $assigned_ids));
        }

        return $all_depts;
    }

    /**
     * Toggle relasi user ke department (Diaktifkan/Dinonaktifkan via Switch)
     */
    public function toggle_department($user_id, $department_id, $enable) {
        $user_id = (int)$user_id;
        $department_id = (int)$department_id;

        try {
            if ($this->db && $this->db->conn_id) {
                if ($enable) {
                    // Cek apakah relasi sudah ada
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
                    // Hapus relasi
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
     * Insert User baru
     */
    public function insert($data) {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->insert($this->table, $data);
                return $this->db->insert_id();
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::insert error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Update User
     */
    public function update($id, $data) {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        try {
            if ($this->db && $this->db->conn_id) {
                return $this->db->where('id', (int)$id)->update($this->table, $data);
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::update error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Hapus User
     */
    public function delete($id) {
        try {
            if ($this->db && $this->db->conn_id) {
                // Relasi di user_departments terhapus otomatis via CASCADE di SQL Server
                return $this->db->where('id', (int)$id)->delete($this->table);
            }
        } catch (Exception $e) {
            log_message('error', 'User_model::delete error: ' . $e->getMessage());
        }
        return FALSE;
    }
}
