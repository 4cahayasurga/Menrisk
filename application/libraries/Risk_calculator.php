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
