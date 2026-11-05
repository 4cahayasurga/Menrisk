<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SumberRisiko_model extends CI_Model {

    protected $table1 = 'dbo.MNRSK_SumberRisiko';
    protected $table2 = 'dbo.MNRSK_FaktorRisiko';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_sumber_risiko()
    {
        return $this->db->select('id, nama')->order_by('nama', 'asc')->get($this->table1)->result();
    }

    public function get_all_faktor_risiko()
    {
        $this->db->select('f.id, f.sumber_risiko_id, f.nama AS nama_faktor, s.nama AS nama_sumber, f.created_at');
        $this->db->from($this->table2 . ' f');
        $this->db->join($this->table1 . ' s', 's.id = f.sumber_risiko_id', 'left');
        $this->db->order_by('s.nama', 'asc');
        $this->db->order_by('f.nama', 'asc');
        return $this->db->get()->result();
    }

    public function get_item($id)
    {
        return $this->db->where('id', (int) $id)->get($this->table2)->row();
    }

    /**
     * Cek apakah kombinasi sumber_risiko_id dan nama faktor risiko sudah ada (mencegah duplikat).
     *
     * @param int|string $sumberId
     * @param string $faktor
     * @param int|string|null $excludeId
     * @return bool
     */
    public function is_duplicate_faktor($sumberId, $faktor, $excludeId = null)
    {
        $sumberId = (int) $sumberId;
        $faktor   = trim((string) $faktor);

        if ($sumberId <= 0 || $faktor === '') {
            return false;
        }

        $sql = "SELECT COUNT(1) AS cnt 
                FROM dbo.MNRSK_FaktorRisiko 
                WHERE sumber_risiko_id = ? 
                  AND LOWER(LTRIM(RTRIM(nama))) = LOWER(LTRIM(RTRIM(?)))";
        $bindings = [$sumberId, $faktor];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $bindings[] = (int) $excludeId;
        }

        $query = $this->db->query($sql, $bindings);
        $row = $query ? $query->row() : null;
        return ($row && (int)$row->cnt > 0);
    }

    public function create_item($sumberId, $faktor, $createdId = null)
    {
        if ($this->is_duplicate_faktor($sumberId, $faktor)) {
            return false;
        }

        $data = [
            'sumber_risiko_id' => (int) $sumberId,
            'nama'             => trim((string) $faktor),
            'created_at'       => date('Y-m-d H:i:s'),
            'created_id'       => $createdId
        ];
        return $this->db->insert($this->table2, $data);
    }

    public function update_item($id, $sumberId, $faktor)
    {
        $id       = (int) $id;
        $sumberId = (int) $sumberId;
        $faktor   = trim((string) $faktor);

        if ($this->is_duplicate_faktor($sumberId, $faktor, $id)) {
            return false;
        }

        $data = [
            'sumber_risiko_id' => $sumberId,
            'nama'             => $faktor
        ];

        return $this->db->where('id', $id)->update($this->table2, $data);
    }

    public function delete_item($id)
    {
        return $this->db->where('id', (int) $id)->delete($this->table2);
    }
}
