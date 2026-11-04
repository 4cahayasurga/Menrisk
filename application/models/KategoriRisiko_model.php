<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: KategoriRisiko_model
 * Mengelola 4 level hierarki Taksonomi Kategori Risiko KBUMN:
 * - Table T1: dbo.MNRSK_KategoriRisiko_tema
 * - Table T2: dbo.MNRSK_KategoriRisiko_kat_kbumn
 * - Table T3: dbo.MNRSK_KategoriRisiko_kat_perusahaan
 * - Table T4: dbo.MNRSK_KategoriRisiko_per_perusahaan
 */
class KategoriRisiko_model extends CI_Model {

    protected $table_t1 = 'dbo.MNRSK_KategoriRisiko_tema';
    protected $table_t2 = 'dbo.MNRSK_KategoriRisiko_kat_kbumn';
    protected $table_t3 = 'dbo.MNRSK_KategoriRisiko_kat_perusahaan';
    protected $table_t4 = 'dbo.MNRSK_KategoriRisiko_per_perusahaan';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Dapatkan nama tabel fisik berdasarkan kode level (T1, T2, T3, T4)
     */
    public function get_table_by_level($level) {
        $lvl = strtoupper(trim((string)$level));
        switch ($lvl) {
            case 'T1':
            case '1':
                return $this->table_t1;
            case 'T2':
            case '2':
                return $this->table_t2;
            case 'T3':
            case '3':
                return $this->table_t3;
            case 'T4':
            case '4':
                return $this->table_t4;
            default:
                return NULL;
        }
    }

    /**
     * Ambil seluruh struktur hierarki 4 level lengkap dari database
     * Level 1 (T1) -> Level 2 (T2) -> Level 3 (T3) -> Level 4 (T4)
     */
    public function get_all_hierarchy() {
        // 1. Ambil seluruh data Level 4 (T4)
        $t4_rows = $this->db->order_by('code', 'ASC')->order_by('id', 'ASC')->get($this->table_t4)->result_array();
        $t4_by_parent = [];
        foreach ($t4_rows as $r4) {
            $parentId = (int)$r4['kat_perusahaan_id'];
            if (!isset($t4_by_parent[$parentId])) {
                $t4_by_parent[$parentId] = [];
            }
            $t4_by_parent[$parentId][] = [
                'id'       => 't4-' . $r4['id'],
                'db_id'    => (int)$r4['id'],
                'code'     => trim((string)$r4['code']),
                'nama'     => trim((string)$r4['nama']),
                'definisi' => trim((string)$r4['definisi']),
                'level'    => 'T4',
                'children' => []
            ];
        }

        // 2. Ambil seluruh data Level 3 (T3) dan tautkan anak T4
        $t3_rows = $this->db->order_by('code', 'ASC')->order_by('id', 'ASC')->get($this->table_t3)->result_array();
        $t3_by_parent = [];
        foreach ($t3_rows as $r3) {
            $parentId = (int)$r3['kat_kbumn_id'];
            $t3_id    = (int)$r3['id'];
            if (!isset($t3_by_parent[$parentId])) {
                $t3_by_parent[$parentId] = [];
            }
            $t3_by_parent[$parentId][] = [
                'id'       => 't3-' . $t3_id,
                'db_id'    => $t3_id,
                'code'     => trim((string)$r3['code']),
                'nama'     => trim((string)$r3['nama']),
                'definisi' => trim((string)$r3['definisi']),
                'level'    => 'T3',
                'children' => $t4_by_parent[$t3_id] ?? []
            ];
        }

        // 3. Ambil seluruh data Level 2 (T2) dan tautkan anak T3
        $t2_rows = $this->db->order_by('code', 'ASC')->order_by('id', 'ASC')->get($this->table_t2)->result_array();
        $t2_by_parent = [];
        foreach ($t2_rows as $r2) {
            $parentId = (int)$r2['tema_id'];
            $t2_id    = (int)$r2['id'];
            if (!isset($t2_by_parent[$parentId])) {
                $t2_by_parent[$parentId] = [];
            }
            $t2_by_parent[$parentId][] = [
                'id'       => 't2-' . $t2_id,
                'db_id'    => $t2_id,
                'code'     => trim((string)$r2['code']),
                'nama'     => trim((string)$r2['nama']),
                'definisi' => trim((string)$r2['definisi']),
                'level'    => 'T2',
                'children' => $t3_by_parent[$t2_id] ?? []
            ];
        }

        // 4. Ambil seluruh data Level 1 (T1) dan tautkan anak T2
        $t1_rows = $this->db->order_by('code', 'ASC')->order_by('id', 'ASC')->get($this->table_t1)->result_array();
        $result = [];
        foreach ($t1_rows as $r1) {
            $t1_id = (int)$r1['id'];
            $result[] = [
                'id'       => 't1-' . $t1_id,
                'db_id'    => $t1_id,
                'code'     => trim((string)$r1['code']),
                'nama'     => trim((string)$r1['nama']),
                'definisi' => trim((string)$r1['definisi']),
                'level'    => 'T1',
                'children' => $t2_by_parent[$t1_id] ?? []
            ];
        }

        return $result;
    }

    /**
     * Hitung total statistik item taksonomi dari database
     */
    public function get_stats() {
        $t1 = (int)$this->db->count_all($this->table_t1);
        $t2 = (int)$this->db->count_all($this->table_t2);
        $t3 = (int)$this->db->count_all($this->table_t3);
        $t4 = (int)$this->db->count_all($this->table_t4);

        return [
            't1'    => $t1,
            't2'    => $t2,
            't3'    => $t3,
            't4'    => $t4,
            'total' => $t1 + $t2 + $t3 + $t4
        ];
    }

    /**
     * Update data pada tabel sesuai level taksonomi (T1, T2, T3, T4)
     * @param string $level Kode level ('T1', 'T2', 'T3', 'T4')
     * @param int|string $id ID record (baik format 't1-1' maupun angka '1')
     * @param array $data Array data: code, nama, definisi
     * @return bool
     */
    public function update_item($level, $id, array $data) {
        $table = $this->get_table_by_level($level);
        if (!$table) {
            return FALSE;
        }

        // Ekstrak angka murni dari ID jika ada prefix seperti 't1-1'
        $clean_id = (int)preg_replace('/^[a-zA-Z0-9]+-/', '', (string)$id);
        if ($clean_id <= 0) {
            return FALSE;
        }

        $update_data = [];
        if (isset($data['code'])) {
            $update_data['code'] = trim((string)$data['code']);
        }
        if (isset($data['nama'])) {
            $update_data['nama'] = trim((string)$data['nama']);
        }
        if (isset($data['definisi'])) {
            $update_data['definisi'] = trim((string)$data['definisi']);
        }

        if (empty($update_data)) {
            return FALSE;
        }

        $this->db->where('id', $clean_id);
        return $this->db->update($table, $update_data);
    }

    /**
     * Tambah record baru ke tabel berdasarkan level (T1, T2, T3, T4)
     * @param string $level Kode level ('T1', 'T2', 'T3', 'T4')
     * @param array $data Array data: code, nama, definisi, parent_id, created_id
     * @return int|bool ID record yang berhasil di-insert atau FALSE jika gagal
     */
    public function insert_item($level, array $data) {
        $lvl = strtoupper(trim((string)$level));
        $table = $this->get_table_by_level($lvl);
        if (!$table) {
            return FALSE;
        }

        $code       = trim((string)($data['code'] ?? ''));
        $nama       = trim((string)($data['nama'] ?? ''));
        $definisi   = trim((string)($data['definisi'] ?? ''));
        $created_id = trim((string)($data['created_id'] ?? $this->session->userdata('user_id') ?? 'ADMIN'));
        $parent_id  = (int)preg_replace('/^[a-zA-Z0-9]+-/', '', (string)($data['parent_id'] ?? 0));

        $insert_data = [
            'code'       => $code,
            'nama'       => $nama,
            'definisi'   => $definisi,
            'created_at' => date('Y-m-d H:i:s'),
            'created_id' => $created_id
        ];

        switch ($lvl) {
            case 'T1':
            case '1':
                // T1 tidak memiliki foreign key
                break;
            case 'T2':
            case '2':
                if ($parent_id <= 0) return FALSE;
                $insert_data['tema_id'] = $parent_id;
                break;
            case 'T3':
            case '3':
                if ($parent_id <= 0) return FALSE;
                $insert_data['kat_kbumn_id'] = $parent_id;
                break;
            case 'T4':
            case '4':
                if ($parent_id <= 0) return FALSE;
                $insert_data['kat_perusahaan_id'] = $parent_id;
                break;
            default:
                return FALSE;
        }

        $success = $this->db->insert($table, $insert_data);
        if ($success) {
            $insert_id = (int)$this->db->insert_id();
            if ($insert_id <= 0) {
                $query = $this->db->query("SELECT SCOPE_IDENTITY() as id");
                $row = $query->row_array();
                $insert_id = (int)($row['id'] ?? 1);
            }
            return $insert_id > 0 ? $insert_id : TRUE;
        }
        return FALSE;
    }

    /**
     * Ambil daftar opsi Parent T1 (Tema Risiko)
     */
    public function get_t1_options() {
        return $this->db->select('id, code, nama')
                        ->order_by('code', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get($this->table_t1)
                        ->result_array();
    }

    /**
     * Ambil daftar opsi Parent T2 (Kategori Risiko KBUMN)
     */
    public function get_t2_options() {
        return $this->db->select('id, tema_id, code, nama')
                        ->order_by('code', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get($this->table_t2)
                        ->result_array();
    }

    /**
     * Ambil daftar opsi Parent T3 (Kategori Risiko Perusahaan)
     */
    public function get_t3_options() {
        return $this->db->select('id, kat_kbumn_id, code, nama')
                        ->order_by('code', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get($this->table_t3)
                        ->result_array();
    }

    /**
     * Ambil 1 record spesifik berdasarkan level dan ID
     */
    public function get_by_id($level, $id) {
        $table = $this->get_table_by_level($level);
        if (!$table) {
            return NULL;
        }

        $clean_id = (int)preg_replace('/^[a-zA-Z0-9]+-/', '', (string)$id);
        if ($clean_id <= 0) {
            return NULL;
        }

        return $this->db->where('id', $clean_id)->get($table)->row_array();
    }
}
