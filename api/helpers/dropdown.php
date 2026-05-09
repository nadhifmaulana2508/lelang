<?php
// File: api/helpers/dropdown.php

function renderDropdownCabang($selected = '') {
    $data_cabang = [
        '' => 'Semua Cabang (Konsolidasi)',
        
        // --- KORWIL SEMARANG ---
        'KORWIL_SMG' => '📍 KORWIL SEMARANG (Semua)',
        '001' => '&nbsp;&nbsp;&nbsp;001 - Kc. Utama',
        '002' => '&nbsp;&nbsp;&nbsp;002 - Kc. Rembang',
        '003' => '&nbsp;&nbsp;&nbsp;003 - Kc. Pati',
        '004' => '&nbsp;&nbsp;&nbsp;004 - Kc. Demak',
        '005' => '&nbsp;&nbsp;&nbsp;005 - Kc. Kendal',
        '006' => '&nbsp;&nbsp;&nbsp;006 - Kc. Salatiga',
        '007' => '&nbsp;&nbsp;&nbsp;007 - Kc. Kab. Semarang',

        // --- KORWIL SOLO ---
        'KORWIL_SLO' => '📍 KORWIL SOLO (Semua)',
        '008' => '&nbsp;&nbsp;&nbsp;008 - Kc. Wonogiri',
        '009' => '&nbsp;&nbsp;&nbsp;009 - Kc. Kota Surakarta',
        '010' => '&nbsp;&nbsp;&nbsp;010 - Kc. Karanganyar',
        '011' => '&nbsp;&nbsp;&nbsp;011 - Kc. Sukoharjo',
        '012' => '&nbsp;&nbsp;&nbsp;012 - Kc. Sragen',
        '013' => '&nbsp;&nbsp;&nbsp;013 - Kc. Boyolali',
        '014' => '&nbsp;&nbsp;&nbsp;014 - Kc. Magelang',

        // --- KORWIL BANYUMAS ---
        'KORWIL_BMS' => '📍 KORWIL BANYUMAS (Semua)',
        '015' => '&nbsp;&nbsp;&nbsp;015 - Kc. Wonosobo',
        '016' => '&nbsp;&nbsp;&nbsp;016 - Kc. Purworejo',
        '017' => '&nbsp;&nbsp;&nbsp;017 - Kc. Kebumen',
        '018' => '&nbsp;&nbsp;&nbsp;018 - Kc. Banjarnegara',
        '019' => '&nbsp;&nbsp;&nbsp;019 - Kc. Purbalingga',
        '020' => '&nbsp;&nbsp;&nbsp;020 - Kc. Banyumas',
        '021' => '&nbsp;&nbsp;&nbsp;021 - Kc. Cilacap',

        // --- KORWIL PEKALONGAN ---
        'KORWIL_PKL' => '📍 KORWIL PEKALONGAN (Semua)',
        '022' => '&nbsp;&nbsp;&nbsp;022 - Kc. Kab. Tegal',
        '023' => '&nbsp;&nbsp;&nbsp;023 - Kc. Brebes',
        '024' => '&nbsp;&nbsp;&nbsp;024 - Kc. Kota Tegal',
        '025' => '&nbsp;&nbsp;&nbsp;025 - Kc. Pemalang',
        '026' => '&nbsp;&nbsp;&nbsp;026 - Kc. Kota Pekalongan',
        '027' => '&nbsp;&nbsp;&nbsp;027 - Kc. Kab. Pekalongan',
        '028' => '&nbsp;&nbsp;&nbsp;028 - Kc. Batang',
    ];

    $html = '';
    foreach ($data_cabang as $val => $label) {
        $sel = ($selected === (string)$val) ? 'selected' : '';
        
        // Kasih styling beda kalau itu opsi Korwil
        if (strpos($val, 'KORWIL') !== false) {
            $html .= "<option value=\"$val\" $sel class=\"font-bold bg-slate-100 text-blue-700\">$label</option>";
        } else {
            $html .= "<option value=\"$val\" $sel>$label</option>";
        }
    }
    return $html;
}
?>