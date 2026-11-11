<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: FormulirRisiko_model
 * Mengelola penyimpanan, pembaruan, dan pengambilan data Formulir Profile Risiko
 * beserta relasi 4 Tab:
 * - dbo.MNRSK_FormulirRisiko (Master)
 * - dbo.MNRSK_IdentifikasiRisiko (Tab 1)
 * - dbo.MNRSK_KuantifikasiRisikoInheren (Tab 2)
 * - dbo.MNRSK_RencanaPerlakuanRisiko (Tab 3)
 * - dbo.MNRSK_KuantifikasiRisikoResidual (Tab 4)
 */
class FormulirRisiko_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Ambil seluruh daftar Formulir Profile Risiko (Master + Tab 1 Identifikasi Risiko)
     * untuk ditampilkan pada tabel menu Profile Risiko maupun Heatmap Executive Overview
     */
    public function get_all_formulir($allowed_dept_ids = null) {
        if (is_array($allowed_dept_ids) && empty($allowed_dept_ids)) {
            return [];
        }

        $where_dept = "";
        if (is_array($allowed_dept_ids) && !empty($allowed_dept_ids)) {
            $safe_ids = array_map('intval', $allowed_dept_ids);
            $where_dept = "WHERE (f.department_id IN (" . implode(',', $safe_ids) . ") OR f.department_id IS NULL)";
        }

        $sql = "
            SELECT 
                f.id,
                f.department_id,
                COALESCE(dept.d_3_name, '') AS department_name,
                COALESCE(dept.d_3_desc, '') AS department_code,
                f.sasaran,
                f.fungsi_kerja,
                f.created_at,
                f.updated_at,
                f.created_id,
                t1.id AS identifikasi_id,
                t1.nama_risiko,
                t1.kategori_risiko,
                CAST(kat.nama AS NVARCHAR(MAX)) AS kategori_risiko_nama,
                t1.peristiwa_risiko,
                t1.penyebab_risiko,
                t1.dampak_kualitatif,
                t1.sumber_risiko,
                sr.nama AS sumber_risiko_nama,
                t1.faktor_sumber,
                fr.nama AS faktor_sumber_nama,
                t1.kontrol_saat_ini,
                t1.efektifitas_kontrol,
                t1.periode_terpapar,
                t2.skala_risiko_inheren,
                t4.skala_risiko_residual,
                latest_mon.skala_risiko_realisasi AS skala_risiko_current,
                latest_mon.skala_dampak_realisasi_current AS skala_dampak_current,
                latest_mon.skala_probabilitas_realisasi_current AS skala_probabilitas_current,
                latest_mon.eksposur_kuantitatif_current,
                latest_mon.status_progres_tindak_lanjut,
                latest_mon.periode AS periode_current,
                latest_mon.tahun AS tahun_current
            FROM dbo.MNRSK_FormulirRisiko f
            LEFT JOIN dbo.organisation_d_3s dept ON dept.id = f.department_id
            LEFT JOIN dbo.MNRSK_IdentifikasiRisiko t1 ON t1.formulir_id = f.id
            LEFT JOIN dbo.MNRSK_KategoriRisiko_kat_perusahaan kat ON kat.code = t1.kategori_risiko
            LEFT JOIN dbo.MNRSK_SumberRisiko sr ON sr.id = t1.sumber_risiko
            LEFT JOIN dbo.MNRSK_FaktorRisiko fr ON fr.id = t1.faktor_sumber
            LEFT JOIN dbo.MNRSK_KuantifikasiRisikoInheren t2 ON t2.formulir_id = f.id
            LEFT JOIN dbo.MNRSK_KuantifikasiRisikoResidual t4 ON t4.formulir_id = f.id
            OUTER APPLY (
                SELECT TOP 1 
                    pm.skala_risiko_realisasi,
                    pm.skala_dampak_realisasi_current,
                    pm.skala_probabilitas_realisasi_current,
                    pm.eksposur_kuantitatif_current,
                    pm.status_progres_tindak_lanjut,
                    pm.periode,
                    pm.tahun
                FROM dbo.MNRSK_ProgressMonitoringProfileRisiko pm
                WHERE pm.formulir_id = f.id
                ORDER BY pm.tahun DESC, pm.id DESC
            ) latest_mon
            {$where_dept}
            ORDER BY f.id DESC
        ";
        $q = $this->db->query($sql);
        return ($q && is_object($q)) ? $q->result() : [];
    }

    /**
     * Ambil detail lengkap 1 Formulir Profile Risiko beserta 4 Tab-nya berdasarkan ID
     */
    public function get_formulir_by_id($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        $sql = "
            SELECT 
                f.id,
                f.department_id,
                COALESCE(dept.d_3_name, '') AS department_name,
                COALESCE(dept.d_3_desc, '') AS department_code,
                f.sasaran,
                f.fungsi_kerja,
                f.created_at,
                f.updated_at,
                f.created_id,

                -- Tab 1: Identifikasi Risiko
                t1.nama_risiko,
                t1.kategori_risiko,
                CAST(kat.nama AS NVARCHAR(MAX)) AS kategori_risiko_nama,
                t1.peristiwa_risiko,
                t1.penyebab_risiko,
                t1.dampak_kualitatif,
                t1.sumber_risiko,
                sr.nama AS sumber_risiko_nama,
                t1.faktor_sumber,
                fr.nama AS faktor_sumber_nama,
                t1.kontrol_saat_ini,
                t1.efektifitas_kontrol,
                t1.periode_terpapar,

                -- Tab 2: Kuantifikasi Risiko Inheren
                t2.eksposure_kuantitatif,
                t2.kategori_dampak,
                sdk.judul AS kategori_dampak_judul,
                CAST(sdk.risiko AS NVARCHAR(MAX)) AS kategori_dampak_risiko,
                t2.skala_dampak_inheren,
                t2.skala_probabilitas,
                t2.skala_risiko_inheren,

                -- Data Kualitatif 1-5 untuk Kategori Dampak
                CAST(sdk.skala_1 AS NVARCHAR(MAX)) AS kategori_dampak_skala_1,
                CAST(sdk.skala_2 AS NVARCHAR(MAX)) AS kategori_dampak_skala_2,
                CAST(sdk.skala_3 AS NVARCHAR(MAX)) AS kategori_dampak_skala_3,
                CAST(sdk.skala_4 AS NVARCHAR(MAX)) AS kategori_dampak_skala_4,
                CAST(sdk.skala_5 AS NVARCHAR(MAX)) AS kategori_dampak_skala_5,

                -- Tab 3: Rencana Perlakuan Risiko
                t3.operasi_perlakuan,
                t3.jenis_rencana_perlakuan,
                t3.kegiatan_mitigasi,
                t3.output_perlakuan,
                t3.target_waktu,
                t3.biaya_perlakuan,
                t3.program_rkap,
                t3.pic,

                -- Tab 4: Kuantifikasi Risiko Residual
                t4.eksposure_residual,
                t4.skala_dampak_residual,
                t4.skala_kemungkinan_residual,
                t4.skala_risiko_residual
            FROM dbo.MNRSK_FormulirRisiko f
            LEFT JOIN dbo.organisation_d_3s dept ON dept.id = f.department_id
            LEFT JOIN dbo.MNRSK_IdentifikasiRisiko t1 ON t1.formulir_id = f.id
            LEFT JOIN dbo.MNRSK_KategoriRisiko_kat_perusahaan kat ON kat.code = t1.kategori_risiko
            LEFT JOIN dbo.MNRSK_SumberRisiko sr ON sr.id = t1.sumber_risiko
            LEFT JOIN dbo.MNRSK_FaktorRisiko fr ON fr.id = t1.faktor_sumber
            LEFT JOIN dbo.MNRSK_KuantifikasiRisikoInheren t2 ON t2.formulir_id = f.id
            LEFT JOIN dbo.MNRSK_SkalaDampakKualitatif sdk ON sdk.id = t2.kategori_dampak
            LEFT JOIN dbo.MNRSK_RencanaPerlakuanRisiko t3 ON t3.formulir_id = f.id
            LEFT JOIN dbo.MNRSK_KuantifikasiRisikoResidual t4 ON t4.formulir_id = f.id
            WHERE f.id = ?
        ";

        $q = $this->db->query($sql, [$id]);
        return ($q && is_object($q) && $q->num_rows() > 0) ? $q->row() : null;
    }

    /**
     * Simpan data Formulir Profile Risiko baru beserta 4 tabel relasi Tab
     */
    public function save_formulir($payload) {
        $this->db->trans_begin();

        // 1. Insert Master Table (MNRSK_FormulirRisiko)
        $sqlMaster = "
            INSERT INTO dbo.MNRSK_FormulirRisiko (department_id, sasaran, fungsi_kerja, created_at, created_id)
            VALUES (?, ?, ?, GETDATE(), ?);
        ";
        $this->db->query($sqlMaster, [
            !empty($payload['master']['department_id']) ? (int)$payload['master']['department_id'] : null,
            $payload['master']['sasaran'],
            $payload['master']['fungsi_kerja'],
            $payload['master']['created_id'] ?? null
        ]);

        $qId = $this->db->query("SELECT CAST(SCOPE_IDENTITY() AS INT) AS new_id");
        $rowId = ($qId && is_object($qId)) ? $qId->row() : null;
        $formulir_id = $rowId ? (int)$rowId->new_id : 0;

        if ($formulir_id <= 0) {
            $this->db->trans_rollback();
            return false;
        }

        // 2. Insert Tab 1 (MNRSK_IdentifikasiRisiko)
        $t1 = $payload['tab1'];
        $sqlTab1 = "
            INSERT INTO dbo.MNRSK_IdentifikasiRisiko (
                formulir_id, nama_risiko, kategori_risiko, peristiwa_risiko, penyebab_risiko,
                dampak_kualitatif, sumber_risiko, faktor_sumber, kontrol_saat_ini,
                efektifitas_kontrol, periode_terpapar
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $this->db->query($sqlTab1, [
            $formulir_id,
            $t1['nama_risiko'],
            $t1['kategori_risiko'],
            $t1['peristiwa_risiko'],
            $t1['penyebab_risiko'],
            $t1['dampak_kualitatif'],
            $t1['sumber_risiko'],
            $t1['faktor_sumber'],
            $t1['kontrol_saat_ini'],
            $t1['efektifitas_kontrol'],
            $t1['periode_terpapar']
        ]);

        // 3. Insert Tab 2 (MNRSK_KuantifikasiRisikoInheren)
        $t2 = $payload['tab2'];
        $sqlTab2 = "
            INSERT INTO dbo.MNRSK_KuantifikasiRisikoInheren (
                formulir_id, eksposure_kuantitatif, kategori_dampak,
                skala_dampak_inheren, skala_probabilitas, skala_risiko_inheren
            ) VALUES (?, ?, ?, ?, ?, ?)
        ";
        $this->db->query($sqlTab2, [
            $formulir_id,
            $t2['eksposure_kuantitatif'],
            $t2['kategori_dampak'],
            $t2['skala_dampak_inheren'],
            $t2['skala_probabilitas'],
            $t2['skala_risiko_inheren']
        ]);

        // 4. Insert Tab 3 (MNRSK_RencanaPerlakuanRisiko)
        $t3 = $payload['tab3'];
        $sqlTab3 = "
            INSERT INTO dbo.MNRSK_RencanaPerlakuanRisiko (
                formulir_id, operasi_perlakuan, jenis_rencana_perlakuan,
                kegiatan_mitigasi, output_perlakuan, target_waktu,
                biaya_perlakuan, program_rkap, pic
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $this->db->query($sqlTab3, [
            $formulir_id,
            $t3['operasi_perlakuan'],
            $t3['jenis_rencana_perlakuan'],
            $t3['kegiatan_mitigasi'],
            $t3['output_perlakuan'],
            $t3['target_waktu'],
            $t3['biaya_perlakuan'],
            $t3['program_rkap'],
            $t3['pic']
        ]);

        // 5. Insert Tab 4 (MNRSK_KuantifikasiRisikoResidual)
        $t4 = $payload['tab4'];
        $sqlTab4 = "
            INSERT INTO dbo.MNRSK_KuantifikasiRisikoResidual (
                formulir_id, eksposure_residual, skala_dampak_residual,
                skala_kemungkinan_residual, skala_risiko_residual
            ) VALUES (?, ?, ?, ?, ?)
        ";
        $this->db->query($sqlTab4, [
            $formulir_id,
            $t4['eksposure_residual'],
            $t4['skala_dampak_residual'],
            $t4['skala_kemungkinan_residual'],
            $t4['skala_risiko_residual']
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return $formulir_id;
    }

    /**
     * Perbarui data Formulir Profile Risiko beserta 4 tabel relasi Tab
     */
    public function update_formulir($id, $payload) {
        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $this->db->trans_begin();

        // 1. Update Master Table
        $sqlMaster = "
            UPDATE dbo.MNRSK_FormulirRisiko
            SET department_id = ?, sasaran = ?, fungsi_kerja = ?, updated_at = GETDATE()
            WHERE id = ?
        ";
        $this->db->query($sqlMaster, [
            !empty($payload['master']['department_id']) ? (int)$payload['master']['department_id'] : null,
            $payload['master']['sasaran'],
            $payload['master']['fungsi_kerja'],
            $id
        ]);

        // 2. Update Tab 1 (MNRSK_IdentifikasiRisiko)
        $t1 = $payload['tab1'];
        $sqlTab1 = "
            UPDATE dbo.MNRSK_IdentifikasiRisiko
            SET nama_risiko = ?, kategori_risiko = ?, peristiwa_risiko = ?, penyebab_risiko = ?,
                dampak_kualitatif = ?, sumber_risiko = ?, faktor_sumber = ?, kontrol_saat_ini = ?,
                efektifitas_kontrol = ?, periode_terpapar = ?
            WHERE formulir_id = ?
        ";
        $this->db->query($sqlTab1, [
            $t1['nama_risiko'],
            $t1['kategori_risiko'],
            $t1['peristiwa_risiko'],
            $t1['penyebab_risiko'],
            $t1['dampak_kualitatif'],
            $t1['sumber_risiko'],
            $t1['faktor_sumber'],
            $t1['kontrol_saat_ini'],
            $t1['efektifitas_kontrol'],
            $t1['periode_terpapar'],
            $id
        ]);

        // 3. Update Tab 2 (MNRSK_KuantifikasiRisikoInheren)
        $t2 = $payload['tab2'];
        $sqlTab2 = "
            UPDATE dbo.MNRSK_KuantifikasiRisikoInheren
            SET eksposure_kuantitatif = ?, kategori_dampak = ?,
                skala_dampak_inheren = ?, skala_probabilitas = ?, skala_risiko_inheren = ?
            WHERE formulir_id = ?
        ";
        $this->db->query($sqlTab2, [
            $t2['eksposure_kuantitatif'],
            $t2['kategori_dampak'],
            $t2['skala_dampak_inheren'],
            $t2['skala_probabilitas'],
            $t2['skala_risiko_inheren'],
            $id
        ]);

        // 4. Update Tab 3 (MNRSK_RencanaPerlakuanRisiko)
        $t3 = $payload['tab3'];
        $sqlTab3 = "
            UPDATE dbo.MNRSK_RencanaPerlakuanRisiko
            SET operasi_perlakuan = ?, jenis_rencana_perlakuan = ?,
                kegiatan_mitigasi = ?, output_perlakuan = ?, target_waktu = ?,
                biaya_perlakuan = ?, program_rkap = ?, pic = ?
            WHERE formulir_id = ?
        ";
        $this->db->query($sqlTab3, [
            $t3['operasi_perlakuan'],
            $t3['jenis_rencana_perlakuan'],
            $t3['kegiatan_mitigasi'],
            $t3['output_perlakuan'],
            $t3['target_waktu'],
            $t3['biaya_perlakuan'],
            $t3['program_rkap'],
            $t3['pic'],
            $id
        ]);

        // 5. Update Tab 4 (MNRSK_KuantifikasiRisikoResidual)
        $t4 = $payload['tab4'];
        $sqlTab4 = "
            UPDATE dbo.MNRSK_KuantifikasiRisikoResidual
            SET eksposure_residual = ?, skala_dampak_residual = ?,
                skala_kemungkinan_residual = ?, skala_risiko_residual = ?
            WHERE formulir_id = ?
        ";
        $this->db->query($sqlTab4, [
            $t4['eksposure_residual'],
            $t4['skala_dampak_residual'],
            $t4['skala_kemungkinan_residual'],
            $t4['skala_risiko_residual'],
            $id
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Ambil seluruh riwayat progress monitoring untuk 1 Formulir Profile Risiko
     */
    public function get_monitoring_by_formulir_id($formulir_id) {
        $formulir_id = (int)$formulir_id;
        if ($formulir_id <= 0) {
            return [];
        }

        $sql = "
            SELECT 
                pm.*
            FROM dbo.MNRSK_ProgressMonitoringProfileRisiko pm
            WHERE pm.formulir_id = ?
            ORDER BY pm.tahun DESC, pm.id DESC
        ";
        $q = $this->db->query($sql, [$formulir_id]);
        return ($q && is_object($q)) ? $q->result() : [];
    }

    /**
     * Ambil 1 record progress monitoring berdasarkan ID
     */
    public function get_monitoring_by_id($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        $sql = "
            SELECT * FROM dbo.MNRSK_ProgressMonitoringProfileRisiko
            WHERE id = ?
        ";
        $q = $this->db->query($sql, [$id]);
        return ($q && is_object($q) && $q->num_rows() > 0) ? $q->row() : null;
    }

    /**
     * Simpan record progress monitoring baru
     */
    public function save_monitoring($data) {
        $sql = "
            INSERT INTO dbo.MNRSK_ProgressMonitoringProfileRisiko (
                formulir_id, periode_tipe, tahun, periode,
                status_progres_tindak_lanjut, realisasi_biaya,
                proses_tindak_lanjut_persen, persentase_per_risiko,
                eksposur_kuantitatif_current, skala_dampak_realisasi_current,
                skala_probabilitas_realisasi_current, skala_risiko_realisasi,
                kendala, action_plan, created_at, created_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?)
        ";

        $ok = $this->db->query($sql, [
            $data['formulir_id'],
            $data['periode_tipe'] ?? 'triwulan',
            $data['tahun'],
            $data['periode'],
            $data['status_progres_tindak_lanjut'] ?? null,
            $data['realisasi_biaya'] ?? null,
            $data['proses_tindak_lanjut_persen'] ?? null,
            $data['persentase_per_risiko'] ?? null,
            $data['eksposur_kuantitatif_current'] ?? null,
            $data['skala_dampak_realisasi_current'] ?? null,
            $data['skala_probabilitas_realisasi_current'] ?? null,
            $data['skala_risiko_realisasi'] ?? null,
            $data['kendala'] ?? null,
            $data['action_plan'] ?? null,
            $data['created_id'] ?? null
        ]);

        if ($ok) {
            $qId = $this->db->query("SELECT CAST(SCOPE_IDENTITY() AS INT) AS new_id");
            $rowId = ($qId && is_object($qId)) ? $qId->row() : null;
            return $rowId ? (int)$rowId->new_id : true;
        }

        return false;
    }

    /**
     * Perbarui record progress monitoring
     */
    public function update_monitoring($id, $data) {
        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $sql = "
            UPDATE dbo.MNRSK_ProgressMonitoringProfileRisiko
            SET 
                periode_tipe = ?,
                tahun = ?,
                periode = ?,
                status_progres_tindak_lanjut = ?,
                realisasi_biaya = ?,
                proses_tindak_lanjut_persen = ?,
                persentase_per_risiko = ?,
                eksposur_kuantitatif_current = ?,
                skala_dampak_realisasi_current = ?,
                skala_probabilitas_realisasi_current = ?,
                skala_risiko_realisasi = ?,
                kendala = ?,
                action_plan = ?,
                updated_at = GETDATE()
            WHERE id = ?
        ";

        return $this->db->query($sql, [
            $data['periode_tipe'] ?? 'triwulan',
            $data['tahun'],
            $data['periode'],
            $data['status_progres_tindak_lanjut'] ?? null,
            $data['realisasi_biaya'] ?? null,
            $data['proses_tindak_lanjut_persen'] ?? null,
            $data['persentase_per_risiko'] ?? null,
            $data['eksposur_kuantitatif_current'] ?? null,
            $data['skala_dampak_realisasi_current'] ?? null,
            $data['skala_probabilitas_realisasi_current'] ?? null,
            $data['skala_risiko_realisasi'] ?? null,
            $data['kendala'] ?? null,
            $data['action_plan'] ?? null,
            $id
        ]);
    }

    /**
     * Hapus record progress monitoring
     */
    public function delete_monitoring($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }
        return $this->db->query("DELETE FROM dbo.MNRSK_ProgressMonitoringProfileRisiko WHERE id = ?", [$id]);
    }
}

