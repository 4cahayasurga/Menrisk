<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Kri_model
 * Menangani transaksi tabel dbo.kri di Microsoft SQL Server
 */
class Kri_model extends CI_Model {

    protected $table = 'dbo.kri';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil data KRI berdasarkan periode
     */
    public function get_all($period = 'September 2026', $allowed_dept_ids = null, $dept = 'all') {
        $kri = [];

        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->select('k.id, k.indicator, k.value, k.threshold, k.department_id, COALESCE(d.name, k.directorate) AS directorate, d.code AS department_code, k.status, k.period');
                $this->db->from($this->table . ' k');
                $this->db->join('dbo.departments d', 'k.department_id = d.id', 'left');

                if (!empty($period) && $period !== 'all') {
                    $this->db->where('k.period', $period);
                }

                // Filter hak akses department jika user bukan admin
                if ($allowed_dept_ids !== null) {
                    if (empty($allowed_dept_ids)) {
                        $this->db->where('1=0');
                    } else {
                        $this->db->where_in('k.department_id', $allowed_dept_ids);
                    }
                }

                if (!empty($dept) && $dept !== 'all') {
                    if (is_numeric($dept)) {
                        $this->db->where('k.department_id', (int)$dept);
                    } else {
                        $this->db->group_start();
                        $this->db->where('d.name', $dept);
                        $this->db->or_where('k.directorate', $dept);
                        $this->db->group_end();
                    }
                }

                $query = $this->db->get();
                if ($query && $query->num_rows() > 0) {
                    $kri = $query->result_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'SQL Server Kri_model::get_all error: ' . $e->getMessage());
        }

        if (empty($kri)) {
            $kri = $this->get_fallback_data();

            if ($allowed_dept_ids !== null) {
                if (empty($allowed_dept_ids)) {
                    $kri = [];
                } else {
                    $kri = array_values(array_filter($kri, function($item) use ($allowed_dept_ids) {
                        return isset($item['department_id']) && in_array((int)$item['department_id'], $allowed_dept_ids);
                    }));
                }
            }

            if (!empty($dept) && $dept !== 'all') {
                $kri = array_values(array_filter($kri, function($item) use ($dept) {
                    return (isset($item['directorate']) && $item['directorate'] === $dept) || (isset($item['department_id']) && (string)$item['department_id'] === (string)$dept);
                }));
            }
        }

        return $kri;
    }


    /**
     * Ambil KRI yang berstatus 'Kritis' atau 'Waspada'
     */
    public function get_breaches($period = 'September 2026', $allowed_dept_ids = null) {
        $all = $this->get_all($period, $allowed_dept_ids);
        return array_values(array_filter($all, function($k) {
            return in_array($k['status'], ['Kritis', 'Waspada']);
        }));
    }

    /**
     * Fallback data KRI
     */
    public function get_fallback_data() {
        return [
            ['id' => 1, 'indicator' => 'System availability', 'value' => '98,2%', 'threshold' => '≥ 99,5%', 'department_id' => 1, 'directorate' => 'Teknologi Informasi', 'status' => 'Kritis', 'period' => 'September 2026'],
            ['id' => 2, 'indicator' => 'Occupancy rate', 'value' => '61%', 'threshold' => '≥ 75%', 'department_id' => 3, 'directorate' => 'Komersial', 'status' => 'Waspada', 'period' => 'September 2026'],
            ['id' => 3, 'indicator' => 'Overdue receivables', 'value' => 'Rp8,7 M', 'threshold' => '≤ Rp5 M', 'department_id' => 4, 'directorate' => 'Keuangan', 'status' => 'Kritis', 'period' => 'September 2026'],
            ['id' => 4, 'indicator' => 'SLA vendor achievement', 'value' => '91%', 'threshold' => '≥ 95%', 'department_id' => 2, 'directorate' => 'Operasional', 'status' => 'Waspada', 'period' => 'September 2026'],
            ['id' => 5, 'indicator' => 'Employee turnover', 'value' => '4,1%', 'threshold' => '≤ 8%', 'department_id' => 5, 'directorate' => 'Human Capital', 'status' => 'Normal', 'period' => 'September 2026'],
            ['id' => 6, 'indicator' => 'Project delay index', 'value' => '7,5%', 'threshold' => '≤ 10%', 'department_id' => 2, 'directorate' => 'Operasional', 'status' => 'Normal', 'period' => 'September 2026']
        ];
    }
}
