<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Department_model
 * Mengelola data master department (dbo.departments)
 */
class Department_model extends CI_Model {

    protected $table = 'dbo.departments';
    protected $table_pivot = 'dbo.user_departments';

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
                $query = $this->db->select('d.id, d.name, d.code, d.description, d.created_at, COUNT(ud.user_id) as total_users')
                                  ->from($this->table . ' d')
                                  ->join($this->table_pivot . ' ud', 'd.id = ud.department_id', 'left')
                                  ->group_by('d.id, d.name, d.code, d.description, d.created_at')
                                  ->order_by('d.id', 'ASC')
                                  ->get();
                if ($query) {
                    $departments = $query->result_array();
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
                $query = $this->db->where('id', (int)$id)->get($this->table);
                if ($query && $query->num_rows() > 0) {
                    return $query->row_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::get_by_id error: ' . $e->getMessage());
        }
        return NULL;
    }

    /**
     * Tambah department baru
     */
    public function insert($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->insert($this->table, $data);
                return $this->db->insert_id();
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::insert error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Update department
     */
    public function update($id, $data) {
        try {
            if ($this->db && $this->db->conn_id) {
                return $this->db->where('id', (int)$id)->update($this->table, $data);
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::update error: ' . $e->getMessage());
        }
        return FALSE;
    }

    /**
     * Hapus department
     */
    public function delete($id) {
        try {
            if ($this->db && $this->db->conn_id) {
                return $this->db->where('id', (int)$id)->delete($this->table);
            }
        } catch (Exception $e) {
            log_message('error', 'Department_model::delete error: ' . $e->getMessage());
        }
        return FALSE;
    }
}
