<?php
/**
 * Plugin Name: DND Portal
 * Description: Innholdsmodell for Dataforeningens ressursportal: ressursområder, temasider og ressurser, med søk på tvers og innloggingsport. Pilot, jf. kravspec ressursportal v1.2.
 * Version: 0.1.0
 * Author: Dataforeningen (internt)
 */

if (!defined('ABSPATH')) { exit; }

/* -------------------------------------------------------------------------
 * Innholdstyper (KR-10–15: DND administrerer struktur uten kodeendring:
 * nytt område = nytt innlegg, skjul = kladd, arkiver = papirkurv,
 * rekkefølge = "Order"-feltet)
 * ---------------------------------------------------------------------- */

add_action('init', function () {
    register_post_type('omrade', [
        'labels' => [
            'name' => 'Ressursområder', 'singular_name' => 'Ressursområde',
            'add_new_item' => 'Nytt ressursområde', 'edit_item' => 'Rediger ressursområde',
        ],
        'public' => true, 'has_archive' => false, 'menu_icon' => 'dashicons-category',
        'menu_position' => 21, 'rewrite' => ['slug' => 'omrade'],
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);
    register_post_type('temaside', [
        'labels' => [
            'name' => 'Temasider', 'singular_name' => 'Temaside',
            'add_new_item' => 'Ny temaside', 'edit_item' => 'Rediger temaside',
        ],
        'public' => true, 'has_archive' => false, 'menu_icon' => 'dashicons-welcome-widgets-menus',
        'menu_position' => 22, 'rewrite' => ['slug' => 'tema'],
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);
    register_post_type('ressurs', [
        'labels' => [
            'name' => 'Ressurser', 'singular_name' => 'Ressurs',
            'add_new_item' => 'Ny ressurs', 'edit_item' => 'Rediger ressurs',
        ],
        'public' => true, 'has_archive' => false, 'menu_icon' => 'dashicons-media-document',
        'menu_position' => 23, 'rewrite' => ['slug' => 'ressurs'],
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);
});

const DND_RESSURSTYPER = ['PDF', 'Word', 'Excel', 'PowerPoint', 'Forms', 'Lenke'];

/* -------------------------------------------------------------------------
 * Metabokser: relasjoner og felter
 * ---------------------------------------------------------------------- */

add_action('add_meta_boxes', function () {
    add_meta_box('dnd_temaside_meta', 'Plassering og forvaltning', 'dnd_temaside_metabox', 'temaside', 'side');
    add_meta_box('dnd_ressurs_meta', 'Ressursdetaljer', 'dnd_ressurs_metabox', 'ressurs', 'side');
});

function dnd_velger($name, $post_type, $valgt) {
    $poster = get_posts(['post_type' => $post_type, 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC', 'post_status' => ['publish', 'draft']]);
    $html = '<select name="' . esc_attr($name) . '" style="width:100%">';
    $html .= '<option value="">— velg —</option>';
    foreach ($poster as $p) {
        $html .= '<option value="' . $p->ID . '"' . selected($valgt, $p->ID, false) . '>' . esc_html($p->post_title) . '</option>';
    }
    return $html . '</select>';
}

function dnd_temaside_metabox($post) {
    wp_nonce_field('dnd_meta', 'dnd_meta_nonce');
    echo '<p><label><strong>Ressursområde</strong></label><br>'
        . dnd_velger('dnd_omrade', 'omrade', (int) get_post_meta($post->ID, '_dnd_omrade', true)) . '</p>';
    echo '<p><label><strong>Innholdseier</strong></label><br><input type="text" name="dnd_eier" style="width:100%" value="'
        . esc_attr(get_post_meta($post->ID, '_dnd_eier', true)) . '" placeholder="f.eks. Administrasjonen"></p>';
}

function dnd_ressurs_metabox($post) {
    wp_nonce_field('dnd_meta', 'dnd_meta_nonce');
    echo '<p><label><strong>Temaside</strong></label><br>'
        . dnd_velger('dnd_temaside', 'temaside', (int) get_post_meta($post->ID, '_dnd_temaside', true)) . '</p>';
    $type = get_post_meta($post->ID, '_dnd_type', true) ?: 'PDF';
    echo '<p><label><strong>Type</strong></label><br><select name="dnd_type" style="width:100%">';
    foreach (DND_RESSURSTYPER as $t) {
        echo '<option value="' . esc_attr($t) . '"' . selected($type, $t, false) . '>' . esc_html($t) . '</option>';
    }
    echo '</select></p>';
    echo '<p><label><strong>SharePoint-/ekstern URL</strong></label><br><input type="url" name="dnd_url" style="width:100%" value="'
        . esc_attr(get_post_meta($post->ID, '_dnd_url', true)) . '" placeholder="https://…sharepoint.com/…"></p>';
    echo '<p><label><strong>Søkeord</strong> (kommaseparert, jf. KR-25)</label><br><input type="text" name="dnd_sokeord" style="width:100%" value="'
        . esc_attr(get_post_meta($post->ID, '_dnd_sokeord', true)) . '" placeholder="budsjett, økonomi"></p>';
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['dnd_meta_nonce']) || !wp_verify_nonce($_POST['dnd_meta_nonce'], 'dnd_meta')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }
    $felter = [
        'dnd_omrade' => '_dnd_omrade', 'dnd_eier' => '_dnd_eier', 'dnd_temaside' => '_dnd_temaside',
        'dnd_type' => '_dnd_type', 'dnd_url' => '_dnd_url', 'dnd_sokeord' => '_dnd_sokeord',
    ];
    foreach ($felter as $inn => $meta) {
        if (isset($_POST[$inn])) {
            update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$inn])));
        }
    }
    // Søkeord speiles inn i utdraget slik at kjernesøket treffer dem (KR-25)
    if (isset($_POST['dnd_sokeord']) && get_post_type($post_id) === 'ressurs') {
        remove_action('save_post', __FUNCTION__);
        wp_update_post(['ID' => $post_id, 'post_excerpt' => sanitize_text_field(wp_unslash($_POST['dnd_sokeord']))]);
    }
}, 10);

/* -------------------------------------------------------------------------
 * Hjelpefunksjoner for temaet
 * ---------------------------------------------------------------------- */

function dnd_temasider($omrade_id) {
    return get_posts([
        'post_type' => 'temaside', 'numberposts' => -1,
        'orderby' => 'menu_order title', 'order' => 'ASC',
        'meta_key' => '_dnd_omrade', 'meta_value' => (int) $omrade_id,
    ]);
}

function dnd_ressurser($temaside_id) {
    return get_posts([
        'post_type' => 'ressurs', 'numberposts' => -1,
        'orderby' => 'menu_order title', 'order' => 'ASC',
        'meta_key' => '_dnd_temaside', 'meta_value' => (int) $temaside_id,
    ]);
}

function dnd_omrade_for($temaside_id) {
    $id = (int) get_post_meta($temaside_id, '_dnd_omrade', true);
    return $id ? get_post($id) : null;
}

function dnd_temaside_for($ressurs_id) {
    $id = (int) get_post_meta($ressurs_id, '_dnd_temaside', true);
    return $id ? get_post($id) : null;
}

/* -------------------------------------------------------------------------
 * Søk: dekk område + temaside + ressurs (KR-23/24); titler, tekst og
 * søkeord (via utdrag). Sider holdes utenfor.
 * ---------------------------------------------------------------------- */

add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && $q->is_search()) {
        $q->set('post_type', ['omrade', 'temaside', 'ressurs']);
        $q->set('posts_per_page', 50);
    }
});

/* -------------------------------------------------------------------------
 * Innloggingsport (KR-03/04-plassholder): hele portalen krever innlogging.
 * Byttes til OIDC (StyreWeb-SSO eller Entra External ID) etter
 * StyreWeb-avklaringen — resten av portalen er uavhengig av valget.
 * ---------------------------------------------------------------------- */

add_action('template_redirect', function () {
    if (is_user_logged_in()) { return; }
    if (defined('DOING_AJAX') && DOING_AJAX) { return; }
    auth_redirect();
});

add_filter('login_message', function ($msg) {
    return $msg . '<p class="message">Pilot: lokal innlogging er plassholder for '
        . 'StyreWeb-SSO/Entra External ID (KR-01–03). Kun aktive medlemmer får tilgang '
        . 'i den ekte løsningen (KR-04).</p>';
});

/* -------------------------------------------------------------------------
 * Adminkolonner så redaksjonen ser relasjonene
 * ---------------------------------------------------------------------- */

add_filter('manage_temaside_posts_columns', function ($cols) {
    $cols['dnd_omrade'] = 'Ressursområde';
    return $cols;
});
add_action('manage_temaside_posts_custom_column', function ($col, $post_id) {
    if ($col === 'dnd_omrade') {
        $o = dnd_omrade_for($post_id);
        echo $o ? esc_html($o->post_title) : '—';
    }
}, 10, 2);

add_filter('manage_ressurs_posts_columns', function ($cols) {
    $cols['dnd_temaside'] = 'Temaside';
    $cols['dnd_type'] = 'Type';
    return $cols;
});
add_action('manage_ressurs_posts_custom_column', function ($col, $post_id) {
    if ($col === 'dnd_temaside') {
        $t = dnd_temaside_for($post_id);
        echo $t ? esc_html($t->post_title) : '—';
    }
    if ($col === 'dnd_type') {
        echo esc_html(get_post_meta($post_id, '_dnd_type', true));
    }
}, 10, 2);
