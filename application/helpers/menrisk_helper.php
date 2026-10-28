<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper: menrisk_helper
 * Kumpulan fungsi formatting & display untuk modul Menrisk
 */

if (!function_exists('risk_score_color')) {
    function risk_score_color($score) {
        $s = (int)$score;
        if ($s >= 20) return '#d93b45';
        if ($s >= 15) return '#ed8351';
        if ($s >= 10) return '#f3a62a';
        if ($s >= 5)  return '#d5bd3e';
        return '#1c9b68';
    }
}

if (!function_exists('risk_appetite_badge')) {
    function risk_appetite_badge($appetite) {
        $app = strtolower(trim($appetite));
        if ($app === 'melebihi') {
            return '<span class="status critical">Melebihi</span>';
        } elseif ($app === 'mendekati') {
            return '<span class="status warning">Mendekati</span>';
        }
        return '<span class="status">Dalam</span>';
    }
}

if (!function_exists('kri_status_badge')) {
    function kri_status_badge($status) {
        $st = strtolower(trim($status));
        if ($st === 'kritis') {
            return '<span class="status critical">KRITIS</span>';
        } elseif ($st === 'waspada') {
            return '<span class="status warning">WASPADA</span>';
        }
        return '<span class="status">NORMAL</span>';
    }
}

if (!function_exists('progress_color')) {
    function progress_color($progress) {
        $p = (int)$progress;
        if ($p < 60) return '#d93b45';
        if ($p < 75) return '#f3a62a';
        return '#1c9b68';
    }
}

if (!function_exists('print_rr')) {
    function print_rr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "<pre>";
    }
}