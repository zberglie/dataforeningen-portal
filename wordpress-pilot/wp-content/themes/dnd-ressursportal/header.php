<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skiplink" href="#innhold">Hopp til innhold</a>

<div class="ribbon">Pilotmiljø · innhold er plassholder · innlogging og dokumenter er simulert ·
<a href="<?php echo esc_url(dnd_side_url('om-piloten')); ?>">Om piloten</a></div>

<header class="top">
  <div class="topbar">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
      <span class="name">Dataforeningen</span><span class="tag">Ressursportal</span>
    </a>
    <nav class="mainnav" aria-label="Hovedmeny">
      <a href="<?php echo esc_url(home_url('/')); ?>"<?php if (is_front_page()) { echo ' aria-current="page"'; } ?>>Hjem</a>
      <a href="<?php echo esc_url(home_url('/')); ?>">Ressursportal</a>
      <a href="<?php echo esc_url(dnd_side_url('arrangementer')); ?>"<?php if (is_page('arrangementer')) { echo ' aria-current="page"'; } ?>>Arrangementer</a>
      <a href="<?php echo esc_url(dnd_side_url('faggrupper')); ?>"<?php if (is_page('faggrupper')) { echo ' aria-current="page"'; } ?>>Faggrupper</a>
    </nav>
    <form class="headsearch" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="search" name="s" placeholder="Søk" aria-label="Søk i ressursportalen"
             value="<?php echo esc_attr(get_search_query()); ?>">
    </form>
    <?php if (is_user_logged_in()) : $u = wp_get_current_user(); ?>
      <span class="userchip"><?php echo esc_html($u->display_name); ?> ·
        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Logg ut</a></span>
    <?php endif; ?>
    <a class="btn btn-dark" href="<?php echo esc_url(dnd_side_url('mitt-medlemskap')); ?>">Mitt medlemskap</a>
  </div>
</header>

<main id="innhold">
