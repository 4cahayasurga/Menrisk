<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: KriteriaDampakKemungkinan_model
 * Master Data — Kriteria Dampak & Kemungkinan
 * Mengelola:
 * 1. Skala Dampak Kuantitatif (dbo.MNRSK_SkalaDampakKuantitatif)
 * 2. Skala Dampak Kualitatif (dbo.MNRSK_SkalaDampakKualitatif)
 * 3. Skala Probabilitas (dbo.MNRSK_SkalaProbabilitas)
 */
class KriteriaDampakKemungkinan_model extends CI_Model {

    protected $table_kuantitatif  = 'dbo.MNRSK_SkalaDampakKuantitatif';
    protected $table_kualitatif   = 'dbo.MNRSK_SkalaDampakKualitatif';
    protected $table_probabilitas = 'dbo.MNRSK_SkalaProbabilitas';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================================
    // 1. SKALA DAMPAK KUANTITATIF
    // =========================================================================

    public function get_all_kuantitatif() {
        return $this->db->order_by('skala', 'ASC')->get($this->table_kuantitatif)->result();
    }

    public function get_kuantitatif($id) {
        return $this->db->where('id', (int)$id)->get($this->table_kuantitatif)->row();
    }

    public function is_duplicate_kuantitatif($skala, $excludeId = null) {
        $sql = "SELECT COUNT(1) AS cnt FROM {$this->table_kuantitatif} WHERE skala = ?";
        $params = [(int)$skala];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = (int)$excludeId;
        }
        $query = $this->db->query($sql, $params);
        $row = $query ? $query->row() : null;
        return ($row && (int)$row->cnt > 0);
    }

    public function create_kuantitatif($data) {
        return $this->db->insert($this->table_kuantitatif, [
            'skala'     => (int)$data['skala'],
            'range'  => trim((string)$data['range']),
            'deskripsi' => trim((string)$data['deskripsi'])
        ]);
    }

    public function update_kuantitatif($id, $data) {
        return $this->db->where('id', (int)$id)->update($this->table_kuantitatif, [
            'skala'     => (int)$data['skala'],
            'range'  => trim((string)$data['range']),
            'deskripsi' => trim((string)$data['deskripsi'])
        ]);
    }

    public function delete_kuantitatif($id) {
        return $this->db->where('id', (int)$id)->delete($this->table_kuantitatif);
    }

    // =========================================================================
    // 2. SKALA DAMPAK KUALITATIF
    // =========================================================================

    public function get_all_kualitatif() {
        return $this->db->order_by('id', 'ASC')->get($this->table_kualitatif)->result();
    }

    public function get_kualitatif($id) {
        return $this->db->where('id', (int)$id)->get($this->table_kualitatif)->row();
    }

    public function is_duplicate_kualitatif($judul, $excludeId = null) {
        $sql = "SELECT COUNT(1) AS cnt FROM {$this->table_kualitatif} WHERE LOWER(LTRIM(RTRIM(judul))) = LOWER(LTRIM(RTRIM(?)))";
        $params = [trim((string)$judul)];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = (int)$excludeId;
        }
        $query = $this->db->query($sql, $params);
        $row = $query ? $query->row() : null;
        return ($row && (int)$row->cnt > 0);
    }

    public function create_kualitatif($data) {
        return $this->db->insert($this->table_kualitatif, [
            'judul'   => trim((string)$data['judul']),
            'risiko'  => trim((string)$data['risiko']),
            'skala_1' => trim((string)$data['skala_1']),
            'skala_2' => trim((string)$data['skala_2']),
            'skala_3' => trim((string)$data['skala_3']),
            'skala_4' => trim((string)$data['skala_4']),
            'skala_5' => trim((string)$data['skala_5'])
        ]);
    }

    public function update_kualitatif($id, $data) {
        return $this->db->where('id', (int)$id)->update($this->table_kualitatif, [
            'judul'   => trim((string)$data['judul']),
            'risiko'  => trim((string)$data['risiko']),
            'skala_1' => trim((string)$data['skala_1']),
            'skala_2' => trim((string)$data['skala_2']),
            'skala_3' => trim((string)$data['skala_3']),
            'skala_4' => trim((string)$data['skala_4']),
            'skala_5' => trim((string)$data['skala_5'])
        ]);
    }

    public function delete_kualitatif($id) {
        return $this->db->where('id', (int)$id)->delete($this->table_kualitatif);
    }

    // =========================================================================
    // 3. SKALA PROBABILITAS
    // =========================================================================

    public function get_all_probabilitas() {
        return $this->db->order_by('id', 'ASC')->get($this->table_probabilitas)->result();
    }

    public function get_probabilitas($id) {
        return $this->db->where('id', (int)$id)->get($this->table_probabilitas)->row();
    }

    public function is_duplicate_probabilitas($parameter, $excludeId = null) {
        $sql = "SELECT COUNT(1) AS cnt FROM {$this->table_probabilitas} WHERE LOWER(LTRIM(RTRIM(CAST(parameter AS NVARCHAR(MAX))))) = LOWER(LTRIM(RTRIM(?)))";
        $params = [trim((string)$parameter)];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = (int)$excludeId;
        }
        $query = $this->db->query($sql, $params);
        $row = $query ? $query->row() : null;
        return ($row && (int)$row->cnt > 0);
    }

    public function create_probabilitas($data) {
        return $this->db->insert($this->table_probabilitas, [
            'parameter' => trim((string)$data['parameter']),
            'skala_1'   => trim((string)$data['skala_1']),
            'skala_2'   => trim((string)$data['skala_2']),
            'skala_3'   => trim((string)$data['skala_3']),
            'skala_4'   => trim((string)$data['skala_4']),
            'skala_5'   => trim((string)$data['skala_5'])
        ]);
    }

    public function update_probabilitas($id, $data) {
        return $this->db->where('id', (int)$id)->update($this->table_probabilitas, [
            'parameter' => trim((string)$data['parameter']),
            'skala_1'   => trim((string)$data['skala_1']),
            'skala_2'   => trim((string)$data['skala_2']),
            'skala_3'   => trim((string)$data['skala_3']),
            'skala_4'   => trim((string)$data['skala_4']),
            'skala_5'   => trim((string)$data['skala_5'])
        ]);
    }

    public function delete_probabilitas($id) {
        return $this->db->where('id', (int)$id)->delete($this->table_probabilitas);
    }
}
