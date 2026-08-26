<?php get_header(); the_post(); ?>

<div class="shell">
  <div class="pagehead">
    <?php echo dnd_crumbs([['label' => 'Hjem', 'url' => home_url('/')], ['label' => get_the_title()]]); ?>
    <h1><?php the_title(); ?></h1>
  </div>
  <div class="prose"><?php the_content(); ?></div>
  <a class="backlink" href="<?php echo esc_url(home_url('/')); ?>">← Til forsiden</a>
</div>

<?php get_footer(); ?>
