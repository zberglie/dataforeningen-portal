<?php get_header(); the_post();
$omrade = dnd_omrade_for(get_the_ID());
$eier = get_post_meta(get_the_ID(), '_dnd_eier', true) ?: 'Administrasjonen';
?>

<div class="shell">
  <div class="pagehead">
    <?php
    $sti = [['label' => 'Hjem', 'url' => home_url('/')], ['label' => 'Ressursportal', 'url' => home_url('/')]];
    if ($omrade) { $sti[] = ['label' => $omrade->post_title, 'url' => get_permalink($omrade)]; }
    $sti[] = ['label' => get_the_title()];
    echo dnd_crumbs($sti);
    ?>
    <h1><?php the_title(); ?></h1>
    <div class="intro"><?php the_content(); ?></div>
    <p class="metaline">Sist oppdatert: <?php echo esc_html(get_the_modified_date('j. F Y')); ?>
      <span class="dot">·</span>Innholdseier: <?php echo esc_html($eier); ?>
      <span class="dot">·</span><a href="<?php echo esc_url(dnd_tips_url()); ?>">Gi tilbakemelding</a></p>
  </div>

  <h2 style="font-size:1.2rem;margin-top:1.75rem">Dokumenter og skjemaer</h2>
  <ul class="doclist">
    <?php foreach (dnd_ressurser(get_the_ID()) as $r) :
        $type = get_post_meta($r->ID, '_dnd_type', true); ?>
    <li><a class="docrow" href="<?php echo esc_url(get_permalink($r)); ?>">
      <?php echo dnd_badge($type); ?>
      <span class="title"><?php echo esc_html($r->post_title); ?></span>
      <span class="open">Åpnes i portalen →</span>
    </a></li>
    <?php endforeach; ?>
  </ul>

  <div class="feedback">
    <span class="chip"><?php echo dnd_smiley(); ?></span>
    <div><strong>Savner du noe på denne siden?</strong>
    <p><a href="<?php echo esc_url(dnd_tips_url()); ?>">Send oss et tips</a> — innholdet vedlikeholdes av
    <?php echo esc_html(mb_strtolower($eier)); ?> og oppdateres uten utvikler.</p></div>
  </div>

  <?php if ($omrade) : ?>
  <a class="backlink" href="<?php echo esc_url(get_permalink($omrade)); ?>">← Tilbake til <?php echo esc_html($omrade->post_title); ?></a>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
