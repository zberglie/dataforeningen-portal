<?php
/**
 * DND Ressursportal — pilottema.
 */

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'style', 'script']);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'dnd-ressursportal',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );
});

/** Smilefjes-ikonet fra designskissen. */
function dnd_smiley() {
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="9" cy="10" r="1.2" fill="currentColor"/><circle cx="15" cy="10" r="1.2" fill="currentColor"/><path d="M8.5 14c1 1.3 2.2 2 3.5 2s2.5-.7 3.5-2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

/** Typemerke for en ressurs (KR-18). */
function dnd_badge($type) {
    $map = [
        'PDF' => 't-pdf', 'Word' => 't-word', 'Excel' => 't-excel',
        'PowerPoint' => 't-ppt', 'Forms' => 't-forms', 'Lenke' => 't-lenke',
    ];
    $cls = isset($map[$type]) ? $map[$type] : 't-lenke';
    return '<span class="typebadge ' . esc_attr($cls) . '">' . esc_html($type ?: 'Lenke') . '</span>';
}

/** Brødsmulesti (KR-21). $items: [['label' =>, 'url' => (valgfri)], …] */
function dnd_crumbs(array $items) {
    $ut = '<nav class="crumbs" aria-label="Brødsmulesti">';
    $siste = count($items) - 1;
    foreach ($items as $i => $it) {
        if ($i) { $ut .= '<span class="sep">/</span>'; }
        if ($i === $siste || empty($it['url'])) {
            $ut .= '<span class="here">' . esc_html($it['label']) . '</span>';
        } else {
            $ut .= '<a href="' . esc_url($it['url']) . '">' . esc_html($it['label']) . '</a>';
        }
    }
    return $ut . '</nav>';
}

/** Lenke til side opprettet i seeden (arrangementer, faggrupper, …). */
function dnd_side_url($slug) {
    $p = get_page_by_path($slug);
    return $p ? get_permalink($p) : home_url('/');
}

/** Første temaside for «tips til innhold»-lenker. */
function dnd_tips_url() {
    $p = get_page_by_path('foresla-forbedringer', OBJECT, 'temaside');
    return $p ? get_permalink($p) : home_url('/');
}
