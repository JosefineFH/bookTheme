<?php
/* Template Name: Bookshelf Archive */

get_header();

$terms = get_terms([
    'taxonomy' => 'library_book_author',
    'hide_empty' => false,
]);

?>
<main class="wrap">
    <div class="heading">
        <h1><?php the_title(); ?></h1>
    </div>
    <div class="content">
        <?= the_content(); ?>
        <ul class="term_list">
            <?php
                if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $link = get_term_link($term);
                    $img = get_field('core_book_info_img', 'book_series_' . $term->term_id);
                    ?>
                    <li class="card">
                        <a href="<?= esc_url(get_term_link($term)) ?>">
                            <?php if($img['url'] ) {?>
                                <div class="col-md-4 card-header">
                                    <img src="<?= $img['url'] ?>" class="img-fluid rounded-start" alt="<?= $img['title'] ?>">
                                </div>
                                <?php } ?>
                                <div class="card-body">
                                    <h3 class="card-title"><?= $term->name ?></h3>
                                    <?php if(!empty($term->description)) {?>
                                        <p class="card-text"><?= $term->description ?></p>
                                    <?php } ?>

                                </div>
                        </a>
                    </li>

                    <?php
                }
            } else {
                echo '<p>No authors found.</p>';
            }
            ?>
        </ul>
    </div>
</main>

<?php
get_footer();
