<?php

get_header();
$terms = get_the_terms(get_the_ID(), 'category');
$primary_cat = get_primary_category(get_the_ID());
// error_log('Primary Category: ' . print_r($terms, true));
$post_id = get_the_ID();
if ($primary_cat && !is_wp_error($primary_cat)) {
    $primary_color = get_term_meta($primary_cat->term_id, 'term_color_picker_1', true) ?: '#f6f5f0';
    $secondary_color = get_term_meta($primary_cat->term_id, 'term_color_picker_2', true) ?: '';
    $tertiary_color = get_term_meta($primary_cat->term_id, 'term_color_picker_3', true) ?: '';
    $text_color = get_term_meta($primary_cat->term_id, 'term_text_color', true) ?: 'black';
}
// Custom fields (post meta)
$isbn = get_post_meta($post_id, 'book_isbn', true);
$authors = get_post_meta($post_id, 'book_authors', true);
$page_count = get_post_meta($post_id, 'book_page_count', true);
$buy_link = get_post_meta($post_id, 'book_buy_link', true);
$thumbnail_id = get_post_thumbnail_id($post_id);
$thumbnail = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';

// Taxonomies
$categories = get_the_term_list($post_id, 'category', '', ', ');
$bookshelves = get_the_term_list($post_id, 'bookshelf', '', ', ');
$bookshelves_url = get_the_terms($post_id, 'bookshelf');
$book_authors = get_the_term_list($post_id, 'book_author', '', ', ');
// echo "<pre>";
// var_dump($bookshelves_url);
// echo "</pre>";

?>
<article class="book-details" data-prim-cat="<?= esc_attr($primary_cat->slug) ?>" data-primary-color="<?= esc_attr($primary_color) ?>" data-secondary-color="<?= esc_attr($secondary_color) ?>" data-tertiary-color="<?= esc_attr($tertiary_color) ?>" data-text-color="<?= esc_attr($text_color) ?>">


    <div class="book-meta">
        <div class="book-meta-thubmnail">
            <?php if ($thumbnail): ?>
                <img src="<?= esc_url($thumbnail); ?>" alt="<?= esc_attr(get_the_title()); ?>" />
            <?php endif; ?>
             <?php if ($bookshelves_url): ?>
            <?php
            foreach ($bookshelves_url as $bookshelf) {
                $term_name = $bookshelf->name;
                $term_link = get_term_link($bookshelf);

                ?>
                <div class="bookshelf">
                    <a href="<?= $term_link ?>">
                        <strong><?= $term_name; ?></strong> 
                    </a>
                </div>
        <?php
            }
            ?>
        </div>
        <div class="book-meta-data">
       
        <?php endif; ?>
            <h1><?php the_title(); ?></h1>
        <?php if ($authors): ?>
            <p><strong>Authors:</strong> <?= esc_html($authors); ?></p>
        <?php endif; ?>

        <?php if ($isbn): ?>
            <p><strong>ISBN:</strong> <?= esc_html($isbn); ?></p>
        <?php endif; ?>

        <?php if ($categories): ?>
            <div id="book_categorys">
                <p><strong>Categories:</strong> <?= $categories; ?></p>
            </div>
        <?php endif; ?>



        <?php if ($buy_link): ?>
            <p><a href="<?= esc_url($buy_link); ?>" class="button" target="_blank" rel="noopener noreferrer">Buy on Google Play</a></p>
        <?php endif; ?>
        </div>

    </div>

    <div class="book-description">
                <?php if ($page_count): ?>
            <p><strong>Pages:</strong> <?= esc_html($page_count); ?></p>
        <?php endif; ?>
        <?= apply_filters('the_content', get_the_content()); ?>
    </div>
</article>


<?php wp_footer(); ?>
