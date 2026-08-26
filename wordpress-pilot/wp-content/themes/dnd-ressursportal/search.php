<?php get_header();
global $wp_query;
$grupper = ['omrade' => [], 'temaside' => [], 'ressurs' => []];
foreach ($wp_query->posts as $p) {
    if (isset($grupper[$p->post_type])) { $grupper[$p->post_type][] = $p; }
}
$etiketter = ['omrade' => 'Ressursområder', 'temaside' => 'Temasider', 'ressurs' => 'Dokumenter og skjemaer'];
?>

<div class="shell">
  <div class="pagehead">
    <?php echo dnd_crumbs([['label' => 'Hjem', 'url' => home_url('/')], ['label' => 'Søk']]); ?>
    <h1>Søk: «<?php echo esc_html(get_search_query()); ?>»</h1>
    <p class="intro"><?php echo (int) $wp_query->found_posts; ?> treff i ressursområder, temasider og
    dokumenttitler med søkeord. (Søket leser ikke tekst <em>inne i</em> dokumentene i MVP — jf. KR-24.)</p>
  </div>

  <form class="bigsearch" style="margin:1.5rem 0 0" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" name="s" aria-label="Søk i ressursportalen" value="<?php echo esc_attr(get_search_query()); ?>">
    <button class="btn btn-dark" type="submit">Søk</button>
  </form>

  <?php foreach ($etiketter as $type => $navn) : if (!$grupper[$type]) { continue; } ?>
  <div class="resultgroup">
    <h2><?php echo esc_html($navn); ?></h2>
    <ul class="doclist">
      <?php foreach ($grupper[$type] as $p) :
          if ($type === 'ressurs') {
              $merke = dnd_badge(get_post_meta($p->ID, '_dnd_type', true));
              $forelder = dnd_temaside_for($p->ID);
          } elseif ($type === 'temaside') {
              $merke = '<span class="typebadge t-side">Temaside</span>';
              $forelder = dnd_omrade_for($p->ID);
          } else {
              $merke = '<span class="typebadge t-side">Område</span>';
              $forelder = null;
          }
      ?>
      <li><a class="docrow" href="<?php echo esc_url(get_permalink($p)); ?>">
        <?php echo $merke; ?>
        <span class="title"><?php echo esc_html($p->post_title); ?></span>
        <?php if ($forelder) : ?><span class="open"><?php echo esc_html($forelder->post_title); ?></span><?php endif; ?>
      </a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endforeach; ?>

  <?php if (!$wp_query->found_posts) : ?>
  <div class="infobox">Ingen treff. Prøv et annet ord — eller
  <a href="<?php echo esc_url(dnd_tips_url()); ?>">gi oss beskjed</a> om at noe mangler.</div>
  <?php endif; ?>

  <a class="backlink" href="<?php echo esc_url(home_url('/')); ?>">← Til forsiden</a>
</div>

<?php get_footer(); ?>
