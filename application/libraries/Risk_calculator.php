<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library: Risk_calculator
 * Logika perhitungan matematis & analitik risiko ERM (Enterprise Risk Management)
 */
class Risk_calculator {

    /**
     * Hitung agregasi metrik KPI Dashboard
     */
    public function compute_kpis(array $risks) {
        $total = count($risks);
        $critical = 0;
        $appetite_counts = [
            'Dalam'     => 0,
            'Mendekati' => 0,
            'Melebihi'  => 0
        ];
        $mitigation_stats = [
            'selesai'     => 0,
            'on_track'    => 0,
            'terlambat'   => 0,
            'belum_mulai' => 0
        ];

        foreach ($risks as $r) {
            $residual = (int)$r['residual'];
            if ($residual >= 15) {
                $critical++;
            }

            $appetite = $r['appetite'] ?? 'Dalam';
            if (isset($appetite_counts[$appetite])) {
                $appetite_counts[$appetite]++;
            }

            $progress = (int)$r['progress'];
            if ($progress >= 100) {
                $mitigation_stats['selesai']++;
            } elseif ($progress >= 60) {
                $mitigation_stats['on_track']++;
            } elseif ($progress > 0) {
                $mitigation_stats['terlambat']++;
            } else {
                $mitigation_stats['belum_mulai']++;
            }
        }

        // Hitung persentase appetite
        $dalam_pct = $total > 0 ? round(($appetite_counts['Dalam'] / $total) * 100) : 71;
        $ontime_pct = $total > 0 ? round((($mitigation_stats['selesai'] + $mitigation_stats['on_track']) / $total) * 100) : 78;

        return [
            'total_risks'       => $total,
            'critical_risks'    => $critical,
            'residual_exposure' => 'Rp18,6 M',
            'ontime_mitigation' => $ontime_pct . '%',
            'appetite'          => $appetite_counts,
            'appetite_percent'  => $dalam_pct,
            'mitigation_stats'  => $mitigation_stats
        ];
    }

    /**
     * Hitung agregasi metrik KPI Dashboard berdasarkan data Daftar Profile Risiko
     */
    public function compute_profile_kpis(array $profil_list) {
        $total = count($profil_list);
        $monitored_count = 0;
        $critical = 0;
        $high_count = 0;
        $mod_high_count = 0;

        $appetite_counts = [
            'Dalam'     => 0,
            'Mendekati' => 0,
            'Melebihi'  => 0
        ];
        $mitigation_stats = [
            'selesai'     => 0,
            'on_track'    => 0,
            'terlambat'   => 0,
            'belum_mulai' => 0
        ];

        $total_inherent_rp = 0;
        $total_current_rp  = 0;
        $sum_progress      = 0;

        foreach ($profil_list as $p) {
            $cur = isset($p->skala_risiko_current) ? (int)$p->skala_risiko_current : 0;
            $res = isset($p->skala_risiko_residual) ? (int)$p->skala_risiko_residual : 0;
            $inh = isset($p->skala_risiko_inheren) ? (int)$p->skala_risiko_inheren : 0;

            if ($cur > 0) {
                $monitored_count++;
            }

            $eff_score = $cur > 0 ? $cur : ($res > 0 ? $res : ($inh > 0 ? $inh : 1));

            if ($eff_score >= 20) {
                $critical++;
                $high_count++;
            } elseif ($eff_score >= 16) {
                $critical++;
                $mod_high_count++;
            }

            if ($eff_score >= 16) {
                $appetite_counts['Melebihi']++;
            } elseif ($eff_score >= 12) {
                $appetite_counts['Mendekati']++;
            } else {
                $appetite_counts['Dalam']++;
            }

            // Eksposur Kuantitatif (parse angka dari string format titik ribuan)
            $inh_rp = (float)preg_replace('/[^0-9]/', '', (string)($p->eksposure_kuantitatif ?? '0'));
            $res_rp = (float)preg_replace('/[^0-9]/', '', (string)($p->eksposure_residual ?? '0'));
            $cur_rp_raw = (string)($p->eksposur_kuantitatif_current ?? '');
            $cur_rp = ($cur_rp_raw !== '') ? (float)preg_replace('/[^0-9]/', '', $cur_rp_raw) : ($res_rp > 0 ? $res_rp : $inh_rp);

            $total_inherent_rp += $inh_rp;
            $total_current_rp  += $cur_rp;

            // Progres Mitigasi
            $pct_raw = (string)($p->proses_tindak_lanjut_persen ?? $p->persentase_per_risiko ?? '');
            $pct_val = (float)preg_replace('/[^0-9.]/', '', $pct_raw);
            $pct_val = max(0, min(100, $pct_val));
            $sum_progress += $pct_val;

            $status_txt = strtolower((string)($p->status_progres_tindak_lanjut ?? ''));
            if ($pct_val >= 100 || strpos($status_txt, 'selesai') !== false) {
                $mitigation_stats['selesai']++;
            } elseif ($pct_val >= 50) {
                $mitigation_stats['on_track']++;
            } elseif ($pct_val > 0 || $cur > 0) {
                $mitigation_stats['terlambat']++;
            } else {
                $mitigation_stats['belum_mulai']++;
            }
        }

        $dalam_pct = $total > 0 ? (int)round(($appetite_counts['Dalam'] / $total) * 100) : 100;
        $ontime_pct = $total > 0 ? (int)round((($mitigation_stats['selesai'] + $mitigation_stats['on_track']) / $total) * 100) : 0;
        $avg_progress = $total > 0 ? (int)round($sum_progress / $total) : 0;

        $exposure_diff_pct = 0;
        if ($total_inherent_rp > 0 && $total_current_rp <= $total_inherent_rp) {
            $exposure_diff_pct = round((($total_inherent_rp - $total_current_rp) / $total_inherent_rp) * 100, 1);
        }

        return [
            'total_risks'        => $total,
            'monitored_risks'    => $monitored_count,
            'critical_risks'     => $critical,
            'high_risks'         => $high_count,
            'mod_high_risks'     => $mod_high_count,
            'residual_exposure'  => $this->format_rupiah_short($total_current_rp),
            'inherent_exposure'  => $this->format_rupiah_short($total_inherent_rp),
            'exposure_diff_pct'  => $exposure_diff_pct,
            'ontime_mitigation'  => $avg_progress . '%',
            'ontime_pct_num'     => $avg_progress,
            'ontime_action_Ratio'=> $ontime_pct,
            'appetite'           => $appetite_counts,
            'appetite_percent'   => $dalam_pct,
            'mitigation_stats'   => $mitigation_stats
        ];
    }

    /**
     * Format nominal Rupiah ke bentuk ringkas (M / Jt / Rb)
     */
    public function format_rupiah_short($num) {
        $num = (float)$num;
        if ($num <= 0) {
            return 'Rp 0';
        }
        if ($num >= 1000000000000) {
            return 'Rp ' . str_replace('.', ',', (string)round($num / 1000000000000, 2)) . ' T';
        }
        if ($num >= 1000000000) {
            return 'Rp ' . str_replace('.', ',', (string)round($num / 1000000000, 2)) . ' M';
        }
        if ($num >= 1000000) {
            return 'Rp ' . str_replace('.', ',', (string)round($num / 1000000, 1)) . ' Jt';
        }
        return 'Rp ' . number_format($num, 0, ',', '.');
    }

    /**
     * Hitung matriks heatmap 5x5
     * Impact (1-5) x Likelihood (1-5)
     */
    public function compute_heatmap_matrix(array $risks) {
        $matrix = [];
        for ($imp = 1; $imp <= 5; $imp++) {
            for ($lik = 1; $lik <= 5; $lik++) {
                $score = $imp * $lik;
                $matching = array_filter($risks, function($r) use ($score) {
                    return (int)$r['inherent'] === $score;
                });
                $matrix[] = [
                    'impact'     => $imp,
                    'likelihood' => $lik,
                    'score'      => $score,
                    'count'      => count($matching),
                    'severity'   => $this->get_score_severity($score)
                ];
            }
        }
        return $matrix;
    }

    /**
     * Klasifikasi skor
     */
    public function get_score_severity($score) {
        if ($score >= 15) return 'extreme';
        if ($score >= 10) return 'high';
        if ($score >= 5)  return 'moderate';
        return 'low';
    }

    /**
     * Warna representasi skor
     */
    public function get_score_color($score) {
        if ($score >= 20) return '#d93b45'; // Extreme
        if ($score >= 15) return '#ed8351'; // High
        if ($score >= 10) return '#f3a62a'; // Moderate
        if ($score >= 5)  return '#d5bd3e'; // Low-mod
        return '#1c9b68';                    // Low (Green)
    }
}
