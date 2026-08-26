<?php get_header(); ?>

<section class="hero">
  <h1>Dataforeningen Ressursportal</h1>
  <p>Din komplette verktøykasse for faggrupper, styrer og ildsjeler.</p>
</section>

<div class="photostrip" aria-hidden="true"><div class="ph1"></div><div class="ph2"></div><div class="ph3"></div><div class="ph4"></div></div>

<div class="shell">
  <form class="bigsearch" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" name="s" placeholder="Søk etter maler, veiledninger og dokumenter …"
           aria-label="Søk i ressursportalen" value="<?php echo esc_attr(get_search_query()); ?>">
    <button class="btn btn-dark" type="submit">Søk</button>
  </form>
</div>

<section class="section">
  <div class="shell">
    <h2>Verktøykasse fra A til Å</h2>
    <p class="sub">Alt du trenger til styrearbeid, markedsføring og gjennomføring av konferanser og
    nettverksmøter. Områdene administreres av Dataforeningen — uten utvikler.</p>
    <div class="cardgrid">
      <?php
      $omrader = get_posts(['post_type' => 'omrade', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC']);
      foreach ($omrader as $o) :
          $temaer = dnd_temasider($o->ID);
      ?>
      <article class="card">
        <div class="cardhead">
          <span class="chip"><?php echo dnd_smiley(); ?></span>
          <h3><a href="<?php echo esc_url(get_permalink($o)); ?>"><?php echo esc_html($o->post_title); ?></a></h3>
        </div>
        <ul class="linklist">
          <?php foreach (array_slice($temaer, 0, 5) as $t) : ?>
          <li><a href="<?php echo esc_url(get_permalink($t)); ?>"><span class="arrow">→</span><?php echo esc_html($t->post_title); ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="more">
          <a href="<?php echo esc_url(get_permalink($o)); ?>">Åpne området →</a>
          <?php if (count($temaer) > 5) : ?><span>(<?php echo count($temaer); ?> temasider)</span><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="statement alt section">
  <h2>Teknologi utvikler vi best sammen</h2>
  <p>Siden 1953</p>
</section>

<section class="section">
  <div class="shell ctagrid">
    <div class="cta"><span class="chip"><?php echo dnd_smiley(); ?></span>
      <div><strong>Vi deler for å skape verdi</strong>
      <p>Sammen bygger vi kunnskap, nettverk og muligheter.</p></div></div>
    <div class="cta"><span class="chip"><?php echo dnd_smiley(); ?></span>
      <div><strong>Har du tips til innhold eller savner noe?</strong>
      <p><a href="<?php echo esc_url(dnd_tips_url()); ?>">Ta kontakt</a> — sammen gjør vi ressursportalen enda bedre!</p></div></div>
  </div>
</section>

<?php get_footer(); ?>
