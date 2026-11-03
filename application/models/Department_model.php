<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Department_model
 * Mengelola data master department pada dbo.organisation_d_3s
 * dan relasi pengguna pada dbo.MNRSK_user_departments
 */
class Department_model extends CI_Model {

    protected $table       = 'dbo.organisation_d_3s';
    protected $table_pivot = 'dbo.MNRSK_user_departments';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil seluruh department beserta jumlah user yang terasosiasi
     */
    public function get_all() {
        $departments = [];
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('d.id, d.d_3_name AS name, d.d_3_desc AS code, d.d_3_desc AS description, d.d_3_name, d.d_3_desc, d.created_at, COUNT(ud.user_id) as total_users')
                                  ->from($this->table . ' d')
                                  ->join($this->table_pivot . ' ud', 'd.id = ud.department_id', 'left')
                                  ->group_by('d.id, d.d_3_name, d.d_3_desc, d.created_at')
                                  ->order_by('d.id', 'ASC')
                                  ->get();
                if ($query) {
                    foreach ($query->result_array() as $row) {
                        $row['id'] = (int)$row['id'];
                        $row['total_users'] = (int)$row['total_users'];
                        $departments[] = $row;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::get_all error: ' . $e->getMessage());
        }

        return $departments;
    }

    /**
     * Ambil 1 department berdasarkan ID
     */
    public function get_by_id($id) {
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('d.id, d.d_3_name AS name, d.d_3_desc AS code, d.d_3_desc AS description, d.d_3_name, d.d_3_desc, d.created_at, d.updated_at')
                                  ->from($this->table . ' d')
                                  ->where('d.id', (int)$id)
                                  ->get();
                if ($query && $query->num_rows() > 0) {
                    $row = $query->row_array();
                    $row['id'] = (int)$row['id'];
                    return $row;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::get_by_id error: ' . $e->getMessage());
        }
        return NULL;
    }

    /**
     * Tambah department baru ke dbo.organisation_d_3s
     */
    public function insert($data) {
        try {
            if ($this->db && $this->db->conn_id) {
                // Generate Next ID karena kolom id dbo.organisation_d_3s bukan IDENTITY
                $max_query = $this->db->select_max('id')->get($this->table);
                $next_id = 1;
                if ($max_query && $max_query->num_rows() > 0) {
                    $max_row = $max_query->row_array();
                    $next_id = ((int)($max_row['id'] ?? 0)) + 1;
                }

                $name = trim((string)($data['d_3_name'] ?? $data['name'] ?? ''));
                $desc = trim((string)($data['d_3_desc'] ?? $data['description'] ?? $data['code'] ?? ''));

                $insert_data = [
                    'id'         => $next_id,
                    'd_id'       => 1,
                    'd_1_id'     => 1,
                    'd_2_id'     => 1,
                    'd_3_name'   => $name,
                    'd_3_desc'   => $desc !== '' ? $desc : $name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $ok = $this->db->insert($this->table, $insert_data);
                return $ok ? $next_id : FALSE;
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::insert error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Update department pada dbo.organisation_d_3s
     */
    public function update($id, $data) {
        $id = (int)$id;
        if ($id <= 0) {
            return FALSE;
        }

        try {
            if ($this->db && $this->db->conn_id) {
                $name = trim((string)($data['d_3_name'] ?? $data['name'] ?? ''));
                $desc = trim((string)($data['d_3_desc'] ?? $data['description'] ?? $data['code'] ?? ''));

                $update_data = [
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                if ($name !== '') {
                    $update_data['d_3_name'] = $name;
                }
                if ($desc !== '') {
                    $update_data['d_3_desc'] = $desc;
                }

                return $this->db->where('id', $id)->update($this->table, $update_data);
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::update error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Hapus department dari dbo.organisation_d_3s
     */
    public function delete($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return FALSE;
        }

        try {
            if ($this->db && $this->db->conn_id) {
                // Hapus relasi pivot terlebih dahulu
                $this->db->where('department_id', $id)->delete($this->table_pivot);
                // Null-kan foreign key pada risks dan kri jika ada
                $this->db->where('department_id', $id)->update('dbo.MNRSK_risks', ['department_id' => NULL]);
                $this->db->where('department_id', $id)->update('dbo.MNRSK_kri', ['department_id' => NULL]);

                return $this->db->where('id', $id)->delete($this->table);
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::delete error: ' . $e->getMessage());
        }
        return FALSE;
    }
}
