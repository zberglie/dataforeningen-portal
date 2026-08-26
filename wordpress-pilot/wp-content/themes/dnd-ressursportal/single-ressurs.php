<?php get_header(); the_post();
$type = get_post_meta(get_the_ID(), '_dnd_type', true) ?: 'PDF';
$tema = dnd_temaside_for(get_the_ID());
$omrade = $tema ? dnd_omrade_for($tema->ID) : null;
$er_form = ($type === 'Forms');
$beskrivelse = trim(wp_strip_all_tags(get_the_content()));
?>

<div class="shell">
  <div class="pagehead">
    <?php
    $sti = [['label' => 'Hjem', 'url' => home_url('/')]];
    if ($omrade) { $sti[] = ['label' => $omrade->post_title, 'url' => get_permalink($omrade)]; }
    if ($tema) { $sti[] = ['label' => $tema->post_title, 'url' => get_permalink($tema)]; }
    $sti[] = ['label' => get_the_title()];
    echo dnd_crumbs($sti);
    ?>
    <h1><?php the_title(); ?> <?php echo dnd_badge($type); ?></h1>
  </div>

  <div class="viewerbar">
    <?php if ($tema) : ?>
    <a class="btn btn-line" href="<?php echo esc_url(get_permalink($tema)); ?>">← Tilbake til <?php echo esc_html($tema->post_title); ?></a>
    <?php endif; ?>
    <?php if (!$er_form) : ?>
    <a class="btn btn-dark" href="<?php echo esc_url(dnd_proxy_url(get_the_ID())); ?>">Last ned original (<?php echo esc_html($type); ?>)</a>
    <?php endif; ?>
    <span class="note"><?php echo $er_form
        ? 'Skjemaet bygges inn i portalen (Microsoft Forms, KR-26) — brukeren mister ikke portalen.'
        : 'Forhåndsvisning i portalen. Originalen ligger i SharePoint og hentes serverside via dokumentproxyen — hovedmeny og tilbakeknapp beholdes. I piloten streamer proxyen en generert plassholder-PDF.'; ?></span>
  </div>

  <div class="stage">
    <?php if ($er_form) : ?>
    <div class="fakepage fakeform">
      <h2><?php the_title(); ?></h2>
      <p class="desc">Microsoft Forms — innebygd i portalen (KR-26).</p>
      <label>Navn</label><div class="field"></div>
      <label>E-post</label><div class="field"></div>
      <label>Hva vil du bidra med eller foreslå?</label><div class="field tall"></div>
      <div style="margin-top:1.5rem"><span class="btn btn-dark" aria-hidden="true">Send inn</span></div>
    </div>
    <?php else : ?>
    <div class="fakepage">
      <h2><?php the_title(); ?></h2>
      <?php if ($beskrivelse) : ?><p class="desc"><?php echo esc_html($beskrivelse); ?></p><?php endif; ?>
      <div class="line w95"></div><div class="line w88"></div><div class="line w95"></div><div class="line w75"></div>
      <div class="line w40" style="margin-top:1.6rem"></div>
      <div class="line w95"></div><div class="line w88"></div><div class="line w60"></div>
      <div class="line w40" style="margin-top:1.6rem"></div>
      <div class="line w88"></div><div class="line w95"></div><div class="line w75"></div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
