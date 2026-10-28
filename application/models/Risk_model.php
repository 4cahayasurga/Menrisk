<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Risk_model
 * Menangani transaksi tabel dbo.risks di Microsoft SQL Server
 */
class Risk_model extends CI_Model {

    protected $table = 'dbo.risks';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil data resiko dengan kriteria filter
     * 
     * @param array $filters ['period', 'dir', 'cat', 'search']
     * @return array
     */
    public function get_all($filters = []) {
        $period           = $filters['period'] ?? 'September 2026';
        $dir              = $filters['dir'] ?? ($filters['department'] ?? 'all');
        $cat              = $filters['cat'] ?? 'all';
        $search           = $filters['search'] ?? '';
        $allowed_dept_ids = $filters['allowed_dept_ids'] ?? null;

        $data = [];

        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->select('r.id, r.title, r.owner, r.department_id, COALESCE(d.name, r.dir) AS dir, d.code AS department_code, r.cat, r.inherent, r.residual, r.appetite, r.progress, r.due, r.decision, r.mitigation, r.period');
                $this->db->from($this->table . ' r');
                $this->db->join('dbo.departments d', 'r.department_id = d.id', 'left');

                if (!empty($period) && $period !== 'all') {
                    $this->db->where('r.period', $period);
                }

                // Filter hak akses department jika user bukan admin
                if ($allowed_dept_ids !== null) {
                    if (empty($allowed_dept_ids)) {
                        $this->db->where('1=0');
                    } else {
                        $this->db->where_in('r.department_id', $allowed_dept_ids);
                    }
                }

                if (!empty($dir) && $dir !== 'all') {
                    if (is_numeric($dir)) {
                        $this->db->where('r.department_id', (int)$dir);
                    } else {
                        $this->db->group_start();
                        $this->db->where('d.name', $dir);
                        $this->db->or_where('r.dir', $dir);
                        $this->db->group_end();
                    }
                }
                if (!empty($cat) && $cat !== 'all') {
                    $this->db->where('r.cat', $cat);
                }
                if (!empty($search)) {
                    $this->db->group_start();
                    $this->db->like('r.id', $search);
                    $this->db->or_like('r.title', $search);
                    $this->db->or_like('r.owner', $search);
                    $this->db->or_like('d.name', $search);
                    $this->db->group_end();
                }

                $query = $this->db->get();
                if ($query) {
                    $data = $query->result_array();
                }
            }
        } catch (Exception $e) {
            log_message('error', 'SQL Server Risk_model::get_all error: ' . $e->getMessage());
        }

        // Gunakan fallback jika query kosong atau DB belum aktif
        if (empty($data)) {
            $data = $this->get_fallback_data();

            if ($allowed_dept_ids !== null) {
                if (empty($allowed_dept_ids)) {
                    $data = [];
                } else {
                    $data = array_values(array_filter($data, function($r) use ($allowed_dept_ids) {
                        return isset($r['department_id']) && in_array((int)$r['department_id'], $allowed_dept_ids);
                    }));
                }
            }

            if (!empty($dir) && $dir !== 'all') {
                $data = array_values(array_filter($data, function($r) use ($dir) {
                    return ($r['dir'] === $dir || (isset($r['department_id']) && (string)$r['department_id'] === (string)$dir));
                }));
            }
            if (!empty($cat) && $cat !== 'all') {
                $data = array_values(array_filter($data, function($r) use ($cat) { return $r['cat'] === $cat; }));
            }
            if (!empty($search)) {
                $s = strtolower($search);
                $data = array_values(array_filter($data, function($r) use ($s) {
                    return strpos(strtolower($r['id'] . $r['title'] . $r['owner'] . ($r['dir'] ?? '')), $s) !== FALSE;
                }));
            }
        }

        // Pastikan tipe data int
        foreach ($data as &$r) {
            $r['inherent'] = (int)$r['inherent'];
            $r['residual'] = (int)$r['residual'];
            $r['progress'] = (int)$r['progress'];
            if (isset($r['department_id'])) {
                $r['department_id'] = $r['department_id'] !== NULL ? (int)$r['department_id'] : NULL;
            }
        }

        return $data;
    }

    /**
     * Ambil 1 resiko berdasarkan ID
     */
    public function get_by_id($id) {
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('r.id, r.title, r.owner, r.department_id, COALESCE(d.name, r.dir) AS dir, d.code AS department_code, r.cat, r.inherent, r.residual, r.appetite, r.progress, r.due, r.decision, r.mitigation, r.period')
                                  ->from($this->table . ' r')
                                  ->join('dbo.departments d', 'r.department_id = d.id', 'left')
                                  ->where('r.id', $id)
                                  ->get();
                if ($query && $query->num_rows() > 0) {
                    $row = $query->row_array();
                    $row['inherent'] = (int)$row['inherent'];
                    $row['residual'] = (int)$row['residual'];
                    $row['progress'] = (int)$row['progress'];
                    if (isset($row['department_id'])) {
                        $row['department_id'] = $row['department_id'] !== NULL ? (int)$row['department_id'] : NULL;
                    }
                    return $row;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'SQL Server Risk_model::get_by_id error: ' . $e->getMessage());
        }

        $fallback = $this->get_fallback_data();
        foreach ($fallback as $r) {
            if ($r['id'] === $id) return $r;
        }

        return NULL;
    }


    /**
     * Ambil Top N risks berdasarkan residual score
     */
    public function get_top_risks($limit = 5, $period = 'September 2026') {
        $all = $this->get_all(['period' => $period]);
        usort($all, function($a, $b) {
            return $b['residual'] - $a['residual'];
        });
        return array_slice($all, 0, $limit);
    }

    /**
     * Ambil daftar periode unik yang ada di tabel
     */
    public function get_distinct_periods() {
        try {
            if ($this->db && $this->db->conn_id) {
                $this->db->distinct();
                $this->db->select('period');
                $query = $this->db->get($this->table);
                if ($query && $query->num_rows() > 0) {
                    return array_column($query->result_array(), 'period');
                }
            }
        } catch (Exception $e) {
            log_message('error', 'SQL Server get_distinct_periods error: ' . $e->getMessage());
        }

        return ['September 2026', 'Agustus 2026', 'Juli 2026'];
    }

    /**
     * Generate ID risiko berikutnya (Format: R-001, R-002, dst)
     */
    public function generate_next_id() {
        $max_num = 0;
        try {
            if ($this->db && $this->db->conn_id) {
                $query = $this->db->select('id')->from($this->table)->get();
                if ($query) {
                    foreach ($query->result_array() as $row) {
                        if (preg_match('/^R-(\d+)$/i', $row['id'], $m)) {
                            $num = (int)$m[1];
                            if ($num > $max_num) {
                                $max_num = $num;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'generate_next_id error: ' . $e->getMessage());
        }

        if ($max_num === 0) {
            $fallback = $this->get_fallback_data();
            foreach ($fallback as $row) {
                if (preg_match('/^R-(\d+)$/i', $row['id'], $m)) {
                    $num = (int)$m[1];
                    if ($num > $max_num) {
                        $max_num = $num;
                    }
                }
            }
        }

        return 'R-' . str_pad($max_num + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Insert data resiko baru
     */
    public function insert($data) {
        if ($this->db && $this->db->conn_id) {
            return $this->db->insert($this->table, $data);
        }
        return FALSE;
    }

    /**
     * Update data resiko
     */
    public function update($id, $data) {
        if ($this->db && $this->db->conn_id) {
            return $this->db->where('id', $id)->update($this->table, $data);
        }
        return FALSE;
    }

    /**
     * Delete data resiko
     */
    public function delete($id) {
        if ($this->db && $this->db->conn_id) {
            return $this->db->where('id', $id)->delete($this->table);
        }
        return FALSE;
    }

    /**
     * Fallback dataset saat DB SQL Server belum terisi
     */
    public function get_fallback_data() {
        return [
            [
                'id' => 'R-001',
                'title' => 'Gangguan layanan sistem kritikal dan serangan siber',
                'owner' => 'Manager IT',
                'department_id' => 1,
                'dir' => 'Teknologi Informasi',
                'cat' => 'Teknologi',
                'inherent' => 25,
                'residual' => 20,
                'appetite' => 'Melebihi',
                'progress' => 42,
                'due' => '30 Sep 2026',
                'decision' => 'Persetujuan percepatan implementasi DR site dan managed SOC.',
                'mitigation' => 'Implementasi DR site, EDR endpoint, SOC 24/7 dan simulasi incident response.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-002',
                'title' => 'Target okupansi Sosoro Mall tidak tercapai',
                'owner' => 'VP Komersial',
                'department_id' => 3,
                'dir' => 'Komersial',
                'cat' => 'Strategis',
                'inherent' => 20,
                'residual' => 16,
                'appetite' => 'Melebihi',
                'progress' => 55,
                'due' => '15 Okt 2026',
                'decision' => 'Persetujuan skema commercial incentive untuk anchor tenant.',
                'mitigation' => 'Repricing tenant mix, akuisisi anchor tenant, dan digital campaign berbasis conversion.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-003',
                'title' => 'Piutang usaha melewati jatuh tempo',
                'owner' => 'VP Keuangan',
                'department_id' => 4,
                'dir' => 'Keuangan',
                'cat' => 'Keuangan',
                'inherent' => 20,
                'residual' => 15,
                'appetite' => 'Melebihi',
                'progress' => 68,
                'due' => '25 Sep 2026',
                'decision' => 'Mandat eskalasi collection dan restrukturisasi selektif.',
                'mitigation' => 'Aging control mingguan, payment reminder otomatis, dan collection task force.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-004',
                'title' => 'Ketergangtungan pendapatan pada pelanggan utama',
                'owner' => 'Direktur Komersial',
                'department_id' => 3,
                'dir' => 'Komersial',
                'cat' => 'Strategis',
                'inherent' => 16,
                'residual' => 12,
                'appetite' => 'Mendekati',
                'progress' => 35,
                'due' => '30 Nov 2026',
                'decision' => '',
                'mitigation' => 'Diversifikasi portofolio dan target kontribusi revenue eksternal.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-005',
                'title' => 'Kegagalan SLA layanan kebersihan dan keamanan',
                'owner' => 'VP Operasional',
                'department_id' => 2,
                'dir' => 'Operasional',
                'cat' => 'Operasional',
                'inherent' => 16,
                'residual' => 12,
                'appetite' => 'Mendekati',
                'progress' => 76,
                'due' => '10 Okt 2026',
                'decision' => '',
                'mitigation' => 'Digital SLA monitoring, vendor scorecard, dan corrective action.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-006',
                'title' => 'Ketidakpatuhan perizinan dan regulasi properti',
                'owner' => 'Corporate Secretary',
                'department_id' => 2,
                'dir' => 'Operasional',
                'cat' => 'Kepatuhan',
                'inherent' => 15,
                'residual' => 10,
                'appetite' => 'Dalam',
                'progress' => 82,
                'due' => '20 Okt 2026',
                'decision' => '',
                'mitigation' => 'Compliance calendar dan legal document repository.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-007',
                'title' => 'Keterlambatan proyek revitalisasi aset',
                'owner' => 'VP Property',
                'department_id' => 2,
                'dir' => 'Operasional',
                'cat' => 'Operasional',
                'inherent' => 15,
                'residual' => 9,
                'appetite' => 'Dalam',
                'progress' => 64,
                'due' => '15 Des 2026',
                'decision' => '',
                'mitigation' => 'Stage gate proyek dan weekly project control.',
                'period' => 'September 2026'
            ],
            [
                'id' => 'R-008',
                'title' => 'Kesenjangan kompetensi digital tenaga kerja',
                'owner' => 'VP Human Capital',
                'department_id' => 5,
                'dir' => 'Human Capital',
                'cat' => 'Strategis',
                'inherent' => 12,
                'residual' => 8,
                'appetite' => 'Dalam',
                'progress' => 60,
                'due' => '31 Des 2026',
                'decision' => '',
                'mitigation' => 'Digital capability mapping dan targeted upskilling.',
                'period' => 'September 2026'
            ]
        ];
    }
}
