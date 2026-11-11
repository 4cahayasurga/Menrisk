<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: KeyRiskIndicator_model
 * Mengelola data Master KRI (Key Risk Indicator) dan Riwayat Progress Monitoring KRI
 * Tabel:
 * - dbo.MNRSK_KeyRiskIndicator
 * - dbo.MNRSK_KRI_ProgressMonitoring
 */
class KeyRiskIndicator_model extends CI_Model {

    protected $table_kri = 'dbo.MNRSK_KeyRiskIndicator';
    protected $table_mon = 'dbo.MNRSK_KRI_ProgressMonitoring';
    protected $table_profil = 'dbo.MNRSK_FormulirRisiko';
    protected $table_ident = 'dbo.MNRSK_IdentifikasiRisiko';
    protected $table_dept = 'dbo.organisation_d_3s';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil seluruh daftar Master KRI beserta status monitoring terakhir
     */
    public function get_all_kri() {
        $sql = "
            SELECT 
                k.id,
                k.formulir_id,
                k.department_id,
                COALESCE(d.d_3_name, '') AS department_name,
                COALESCE(d.d_3_desc, '') AS department_code,
                k.nama_risiko,
                k.penyebab_risiko,
                k.deskripsi,
                k.polaritas,
                k.ambang_batas_aman,
                k.ambang_batas_hati_hati,
                k.ambang_batas_bahaya,
                k.created_at,
                k.updated_at,
                k.created_id,
                f.sasaran,
                f.fungsi_kerja,
                latest_mon.id AS last_monitoring_id,
                latest_mon.periode_tipe AS last_periode_tipe,
                latest_mon.tahun AS last_tahun,
                latest_mon.periode AS last_periode,
                latest_mon.monitoring AS last_monitoring_val,
                latest_mon.hasil_threshold AS last_hasil_threshold,
                latest_mon.hasil_keterangan AS last_hasil_keterangan,
                latest_mon.tindak_lanjut AS last_tindak_lanjut,
                latest_mon.created_at AS last_monitoring_date
            FROM {$this->table_kri} k
            LEFT JOIN {$this->table_profil} f ON f.id = k.formulir_id
            LEFT JOIN {$this->table_dept} d ON d.id = COALESCE(k.department_id, f.department_id)
            OUTER APPLY (
                SELECT TOP 1 
                    pm.id, pm.periode_tipe, pm.tahun, pm.periode, 
                    pm.monitoring, pm.hasil_threshold, pm.hasil_keterangan, 
                    pm.tindak_lanjut, pm.created_at
                FROM {$this->table_mon} pm
                WHERE pm.kri_id = k.id
                ORDER BY pm.tahun DESC, pm.id DESC
            ) latest_mon
            ORDER BY k.id DESC
        ";

        $q = $this->db->query($sql);
        return ($q && is_object($q)) ? $q->result() : [];
    }

    /**
     * Ambil 1 data KRI berdasarkan ID
     */
    public function get_kri_by_id($id) {
        $id = (int)$id;
        if ($id <= 0) return null;

        $sql = "
            SELECT 
                k.id,
                k.formulir_id,
                k.department_id,
                COALESCE(d.d_3_name, '') AS department_name,
                COALESCE(d.d_3_desc, '') AS department_code,
                k.nama_risiko,
                k.penyebab_risiko,
                k.deskripsi,
                k.polaritas,
                k.ambang_batas_aman,
                k.ambang_batas_hati_hati,
                k.ambang_batas_bahaya,
                k.created_at,
                k.updated_at,
                k.created_id,
                f.sasaran,
                f.fungsi_kerja
            FROM {$this->table_kri} k
            LEFT JOIN {$this->table_profil} f ON f.id = k.formulir_id
            LEFT JOIN {$this->table_dept} d ON d.id = COALESCE(k.department_id, f.department_id)
            WHERE k.id = ?
        ";

        $q = $this->db->query($sql, [$id]);
        return ($q && is_object($q) && $q->num_rows() > 0) ? $q->row() : null;
    }

    /**
     * Simpan Master KRI baru
     */
    public function save_kri($data) {
        $sql = "
            INSERT INTO {$this->table_kri} (
                formulir_id, department_id, nama_risiko, penyebab_risiko,
                deskripsi, polaritas, ambang_batas_aman, ambang_batas_hati_hati,
                ambang_batas_bahaya, created_at, created_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?);
        ";

        $this->db->query($sql, [
            !empty($data['formulir_id']) ? (int)$data['formulir_id'] : null,
            !empty($data['department_id']) ? (int)$data['department_id'] : null,
            $data['nama_risiko'],
            $data['penyebab_risiko'] ?? null,
            $data['deskripsi'] ?? null,
            $data['polaritas'] ?? 'Maximize',
            $data['ambang_batas_aman'] ?? null,
            $data['ambang_batas_hati_hati'] ?? null,
            $data['ambang_batas_bahaya'] ?? null,
            $data['created_id'] ?? null
        ]);

        $qId = $this->db->query("SELECT CAST(SCOPE_IDENTITY() AS INT) AS new_id");
        $rowId = ($qId && is_object($qId)) ? $qId->row() : null;
        return $rowId ? (int)$rowId->new_id : false;
    }

    /**
     * Update Master KRI
     */
    public function update_kri($id, $data) {
        $id = (int)$id;
        if ($id <= 0) return false;

        $sql = "
            UPDATE {$this->table_kri}
            SET formulir_id = ?, department_id = ?, nama_risiko = ?, penyebab_risiko = ?,
                deskripsi = ?, polaritas = ?, ambang_batas_aman = ?, ambang_batas_hati_hati = ?,
                ambang_batas_bahaya = ?, updated_at = GETDATE()
            WHERE id = ?
        ";

        return $this->db->query($sql, [
            !empty($data['formulir_id']) ? (int)$data['formulir_id'] : null,
            !empty($data['department_id']) ? (int)$data['department_id'] : null,
            $data['nama_risiko'],
            $data['penyebab_risiko'] ?? null,
            $data['deskripsi'] ?? null,
            $data['polaritas'] ?? 'Maximize',
            $data['ambang_batas_aman'] ?? null,
            $data['ambang_batas_hati_hati'] ?? null,
            $data['ambang_batas_bahaya'] ?? null,
            $id
        ]);
    }

    /**
     * Hapus Master KRI beserta riwayat monitoringnya
     */
    public function delete_kri($id) {
        $id = (int)$id;
        if ($id <= 0) return false;

        $this->db->trans_begin();
        $this->db->query("DELETE FROM {$this->table_mon} WHERE kri_id = ?", [$id]);
        $this->db->query("DELETE FROM {$this->table_kri} WHERE id = ?", [$id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Ambil seluruh opsi Profile Risiko untuk dropdown Nama Risiko
     */
    public function get_all_profile_risiko_options() {
        $sql = "
            SELECT 
                f.id AS formulir_id,
                f.department_id,
                COALESCE(dept.d_3_name, '') AS department_name,
                f.sasaran,
                f.fungsi_kerja,
                t1.nama_risiko,
                t1.penyebab_risiko
            FROM {$this->table_profil} f
            JOIN {$this->table_ident} t1 ON t1.formulir_id = f.id
            LEFT JOIN {$this->table_dept} dept ON dept.id = f.department_id
            WHERE t1.nama_risiko IS NOT NULL AND RTRIM(LTRIM(t1.nama_risiko)) <> ''
            ORDER BY f.id DESC
        ";

        $q = $this->db->query($sql);
        return ($q && is_object($q)) ? $q->result() : [];
    }

    /**
     * Ambil riwayat Progress Monitoring berdasarkan KRI ID
     */
    public function get_monitoring_by_kri_id($kri_id) {
        $kri_id = (int)$kri_id;
        if ($kri_id <= 0) return [];

        $sql = "
            SELECT 
                id, kri_id, periode_tipe, tahun, periode,
                monitoring, hasil_threshold, hasil_keterangan, tindak_lanjut,
                created_at, updated_at, created_id
            FROM {$this->table_mon}
            WHERE kri_id = ?
            ORDER BY tahun DESC, id DESC
        ";

        $q = $this->db->query($sql, [$kri_id]);
        return ($q && is_object($q)) ? $q->result() : [];
    }

    /**
     * Ambil detail 1 Progress Monitoring berdasarkan ID
     */
    public function get_monitoring_by_id($id) {
        $id = (int)$id;
        if ($id <= 0) return null;

        $sql = "SELECT * FROM {$this->table_mon} WHERE id = ?";
        $q = $this->db->query($sql, [$id]);
        return ($q && is_object($q) && $q->num_rows() > 0) ? $q->row() : null;
    }

    /**
     * Simpan data Progress Monitoring KRI baru
     */
    public function save_monitoring($data) {
        $sql = "
            INSERT INTO {$this->table_mon} (
                kri_id, periode_tipe, tahun, periode,
                monitoring, hasil_threshold, hasil_keterangan, tindak_lanjut,
                created_at, created_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?);
        ";

        return $this->db->query($sql, [
            (int)$data['kri_id'],
            $data['periode_tipe'] ?? 'triwulan',
            (int)$data['tahun'],
            $data['periode'],
            $data['monitoring'] ?? null,
            $data['hasil_threshold'] ?? null,
            $data['hasil_keterangan'] ?? null,
            $data['tindak_lanjut'] ?? null,
            $data['created_id'] ?? null
        ]);
    }

    /**
     * Update data Progress Monitoring KRI
     */
    public function update_monitoring($id, $data) {
        $id = (int)$id;
        if ($id <= 0) return false;

        $sql = "
            UPDATE {$this->table_mon}
            SET periode_tipe = ?, tahun = ?, periode = ?,
                monitoring = ?, hasil_threshold = ?, hasil_keterangan = ?, tindak_lanjut = ?,
                updated_at = GETDATE()
            WHERE id = ?
        ";

        return $this->db->query($sql, [
            $data['periode_tipe'] ?? 'triwulan',
            (int)$data['tahun'],
            $data['periode'],
            $data['monitoring'] ?? null,
            $data['hasil_threshold'] ?? null,
            $data['hasil_keterangan'] ?? null,
            $data['tindak_lanjut'] ?? null,
            $id
        ]);
    }

    /**
     * Hapus data Progress Monitoring KRI
     */
    public function delete_monitoring($id) {
        $id = (int)$id;
        if ($id <= 0) return false;

        return $this->db->query("DELETE FROM {$this->table_mon} WHERE id = ?", [$id]);
    }
}

