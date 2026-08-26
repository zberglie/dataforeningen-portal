<?php
/**
 * Plugin Name: DND Dokumentproxy
 * Description: Proxy-mønsteret for dokumentvisning (KR-16–19): portalen henter dokumentet serverside og streamer det til innlogget bruker. I piloten genereres en plassholder-PDF; i produksjon byttes dnd_proxy_hent_dokument() til Microsoft Graph (applikasjonstillatelser, ?format=pdf for Office-formater).
 * Version: 0.1.0
 * Author: Dataforeningen (internt)
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Nedlastings-URL for en ressurs. Brukes av temaet.
 */
function dnd_proxy_url($ressurs_id) {
    return home_url('/?dnd_dokument=' . (int) $ressurs_id);
}

/**
 * PUNKTET SOM BYTTES UT I PRODUKSJON.
 * Skal da: slå opp SharePoint-URL (_dnd_url), hente fila via Graph med
 * app-tillatelser (Office-formater konverteres med ?format=pdf for
 * forhåndsvisning; original streames for nedlasting), og returnere
 * [innhold, mime, filnavn]. Nå: generert plassholder-PDF.
 */
function dnd_proxy_hent_dokument($ressurs) {
    $type = get_post_meta($ressurs->ID, '_dnd_type', true) ?: 'PDF';
    $tema = function_exists('dnd_temaside_for') ? dnd_temaside_for($ressurs->ID) : null;
    $linjer = [
        'Dette er en plassholder generert av pilotens dokumentproxy.',
        '',
        'I produksjon henter proxyen originalen (' . $type . ') fra SharePoint',
        'via Microsoft Graph med applikasjonstillatelser, og streamer den',
        'til innlogget medlem - uten iframe, og uten at brukeren mister portalen.',
        '',
        'Ressurs: ' . $ressurs->post_title,
        'Temaside: ' . ($tema ? $tema->post_title : '-'),
        'Generert: ' . wp_date('d.m.Y H:i'),
    ];
    $pdf = dnd_lag_pdf($ressurs->post_title, $linjer);
    return [$pdf, 'application/pdf', sanitize_title($ressurs->post_title) . '-plassholder.pdf'];
}

/** Streamer dokumentet til innlogget bruker. Kjøres etter innloggingsporten. */
add_action('template_redirect', function () {
    if (empty($_GET['dnd_dokument'])) { return; }
    if (!is_user_logged_in()) { auth_redirect(); }
    $ressurs = get_post((int) $_GET['dnd_dokument']);
    if (!$ressurs || $ressurs->post_type !== 'ressurs' || $ressurs->post_status !== 'publish') {
        wp_die('Fant ikke dokumentet.', 'Dokumentproxy', ['response' => 404]);
    }
    list($innhold, $mime, $filnavn) = dnd_proxy_hent_dokument($ressurs);
    nocache_headers();
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $filnavn . '"');
    header('Content-Length: ' . strlen($innhold));
    echo $innhold;
    exit;
}, 20);

/**
 * Minimal, gyldig én-sides PDF (A4, Helvetica, WinAnsi for æøå).
 * Bevisst uten biblioteker — dette er bare pilotens plassholder.
 */
function dnd_lag_pdf($tittel, array $linjer) {
    $esc = function ($s) {
        $s = @iconv('UTF-8', 'CP1252//TRANSLIT', $s);
        if ($s === false) { $s = '?'; }
        return strtr($s, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
    };
    $strom = "BT /F1 16 Tf 56 780 Td (" . $esc($tittel) . ") Tj ET\n";
    $strom .= "BT /F1 11 Tf 56 744 Td 16 TL\n";
    foreach ($linjer as $l) {
        $strom .= "(" . $esc($l) . ") Tj T*\n";
    }
    $strom .= "ET\n";

    $objekter = [
        "<< /Type /Catalog /Pages 2 0 R >>",
        "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
        "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] "
            . "/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
        "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>",
        "<< /Length " . strlen($strom) . " >>\nstream\n" . $strom . "endstream",
    ];

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    foreach ($objekter as $i => $obj) {
        $offsets[$i + 1] = strlen($pdf);
        $pdf .= ($i + 1) . " 0 obj\n" . $obj . "\nendobj\n";
    }
    $xref = strlen($pdf);
    $n = count($objekter) + 1;
    $pdf .= "xref\n0 " . $n . "\n0000000000 65535 f \n";
    for ($i = 1; $i < $n; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size " . $n . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
    return $pdf;
}
