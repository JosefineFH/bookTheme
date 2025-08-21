<?php
// /wp-content/themes/your-theme/taxonomy-book_series.php
get_header();

$term = get_queried_object(); // WP_Term for the current series

?>
<main class="wrap">
  <header class="archive-header">
    <h1 class="archive-title"><?php single_term_title(); ?></h1>
    <?php if (!empty($term->description)) : ?>
      <div class="term-description"><?php echo wp_kses_post(wpautop($term->description)); ?></div>
    <?php endif; ?>
  </header>

  <?php if (have_posts()) : ?>
    <div class="content-grid">
      <?php while (have_posts()) : the_post(); ?>
        <article class="card">
          <a href="<?php the_permalink(); ?>">
            <?php 
            $post_id = get_the_ID();
            $thumb = get_field('library_google_thumbnail', $post_id);
            ?>
            <img src="<?= $thumb ?>" alt="<?= the_title(); ?>">
            <h2 class="book-title"><?= the_title(); ?></h2>
          </a>
        </article>
      <?php endwhile; ?>
    </div>

    <nav class="pagination">
      <?php the_posts_pagination(); ?>
    </nav>
  <?php else : ?>
    <p>No books found in this series.</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
