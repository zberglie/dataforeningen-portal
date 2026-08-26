<?php get_header(); ?>

<div class="shell">
  <div class="pagehead">
    <?php echo dnd_crumbs([['label' => 'Hjem', 'url' => home_url('/')], ['label' => 'Innhold']]); ?>
    <h1><?php echo have_posts() ? 'Innhold' : 'Fant ikke siden'; ?></h1>
  </div>
  <ul class="doclist">
    <?php while (have_posts()) : the_post(); ?>
    <li><a class="docrow" href="<?php the_permalink(); ?>">
      <span class="title"><?php the_title(); ?></span><span class="open">Åpne →</span></a></li>
    <?php endwhile; ?>
  </ul>
  <a class="backlink" href="<?php echo esc_url(home_url('/')); ?>">← Til forsiden</a>
</div>

<?php get_footer(); ?>
