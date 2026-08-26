<?php get_header(); the_post(); ?>

<div class="shell">
  <div class="pagehead">
    <?php echo dnd_crumbs([
        ['label' => 'Hjem', 'url' => home_url('/')],
        ['label' => 'Ressursportal', 'url' => home_url('/')],
        ['label' => get_the_title()],
    ]); ?>
    <h1><?php the_title(); ?></h1>
    <div class="intro"><?php the_content(); ?></div>
  </div>

  <div class="temagrid">
    <?php foreach (dnd_temasider(get_the_ID()) as $t) :
        $antall = count(dnd_ressurser($t->ID)); ?>
    <article class="card">
      <div class="cardhead">
        <span class="chip"><?php echo dnd_smiley(); ?></span>
        <h3><a href="<?php echo esc_url(get_permalink($t)); ?>"><?php echo esc_html($t->post_title); ?></a></h3>
      </div>
      <p class="intro"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($t->post_content), 22, ' …')); ?></p>
      <div class="more">
        <a href="<?php echo esc_url(get_permalink($t)); ?>">Åpne temasiden →</a>
        <span>(<?php echo $antall; ?> <?php echo $antall === 1 ? 'ressurs' : 'ressurser'; ?>)</span>
      </div>
    </article>
    <?php endforeach; ?>
  </div>

  <a class="backlink" href="<?php echo esc_url(home_url('/')); ?>">← Til forsiden</a>
</div>

<?php get_footer(); ?>
