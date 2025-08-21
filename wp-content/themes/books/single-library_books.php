<?php
get_header();

$post_id = $post->ID;
$authors = wp_get_post_terms($post_id, 'library_book_author');

$thumb = get_field('library_google_thumbnail');
$rating = get_field('library_average_rating');

$book_authors = wp_get_post_terms($post_id, 'library_book_author');
$categories = wp_get_post_terms($post_id, 'library_book_categories');
$bookshelf = wp_get_post_terms($post_id, 'library_bookshelf');
$series = wp_get_post_terms($post_id, 'book_series');

?>

<?php if (have_posts()):
    while (have_posts()):
        the_post(); ?>
        <div class="library_book_wrapper">

            <div class="book-details">
                <?php if ($thumb): ?>
                    <div class="img-wrapper">
                        <img src="<?= $thumb; ?>" alt="<?= the_title(); ?>">
                    </div>
                    <?php endif; ?>

                <div class="book-meta">
                    <?php if ($bookshelf): 
                        $term = $bookshelf[0];
                        $slug = $term->slug;
                        $name = $term->name;

                        $linkable_slugs = ['favorite', 'read'];
                        $is_linkable = in_array($slug, $linkable_slugs, true);

                        $term_link = $is_linkable ? get_term_link($term) : '';

                        ?>
                        <div class="reading-status <?php echo ($slug !== 'want-to-read') ? 'dot' : ''; ?>">
                            <?php if ($is_linkable && !is_wp_error($term_link)): ?>
                                <p><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($name); ?></a></p>
                            <?php else: ?>
                                <p><?php echo esc_html($name); ?></p>
                            <?php endif; ?>
                            </div>
                        <?php
                    endif;
                    if ($series && is_array($series)) {
                        // echo "<pre>";
                        // var_dump($series);
                        // echo "</pre>";
                    foreach ($series as $serie) {
                        if ($serie instanceof WP_Term) {
                          $serie_link = get_term_link($serie, 'book_series');
                          echo '<div class="series_author-box">';
                            if (!is_wp_error($serie_link)) {
                                echo '<a href="' . esc_url($serie_link) . '">' . esc_html($serie->name) . '</a>';
                            } else {
                                // Fallback: just print the name if link resolution failed
                                echo esc_html($serie->name);
                            }
                        }
                        }
                    }
                    echo '<p> by </p>';
                    if ($book_authors) {
                        foreach ($book_authors as $author) {
                            $author_link = get_term_link($author);

                            if (!is_wp_error($author_link)) {
                                echo '<a href="' . esc_url($author_link) . '">' . esc_html($author->name) . '</a>';
                            }

                        }
                    }
                    ?>
                        </div>

                    <h1><?= the_title(); ?></h1>

                    <?php
                    if ($rating) {
                        include get_template_directory() . '/parts/avg-rating.php';
                    }
                    ?>
                        <p><strong>Published Date: </strong><?= get_field('library_google_published_date') ?></p>
                        <p><strong>Maturity Rating: </strong><?= get_field('library_google_maturity_rating') ?></p>
                    <?php 
                                        if ($categories) {
                        foreach ($categories as $categorie) {
                            $categorie_link = get_term_link($categorie);

                            if (!is_wp_error($categorie_link)) {
                                echo '<a href="' . esc_url($categorie_link) . '">' . esc_html($categorie->name) . '</a> | ';
                            }

                        }
                    }
                    echo '<p>' . $post->post_excerpt . '</p>';
                    ?>
                    
                </div>
            </div>
            <hr>
            <?= the_content(); ?>
            <div class="tab" role="tablist" aria-label="Book sections">
                <button class="tablinks" id="tab-review" role="tab" aria-selected="false" aria-controls="panel-review"> Review</button>
                <button class="tablinks " id="tab-info" role="tab" aria-selected="true" aria-controls="panel-info"> Info </button>
            </div>
            <div class="tab-wrapper">
                <div id="panel-review" class="tabcontent" role="tabpanel" aria-labelledby="tab-review">
                    <div class="panel">
                        <?php 
                            $reviews = get_field('core_link_review');
                            if($reviews){
                                ?>
                                <h2>Review</h2>
                                
                                <div class="reviews">
                                    <?php 
                                
                                            $review_id = $reviews->ID;
                                            $review_title = $reviews->post_title;
                                            $review_excerpt = $reviews->post_excerpt;
                                            $review_permalink = get_permalink($review_id);
                                            ?>
                                            <div class="review-content">
                                                <h2><?= $review_title ?></h2>
                                                <p><?= $review_excerpt ?></p>
                                            </div>
                                            <a class="btn" href="<?= $review_permalink ?>"><?= __('Read More', 'book') ?></a>
                                </div>
                                
                                <?php
                            }
                        ?>
                    <p><strong>Date Read:</strong> <?= get_field('library_date_read') ?></p>
                    <p><strong>Read Count:</strong> <?= get_field('library_read_count') ?></p>

                    </div>
                </div>
            </div>
            <div class="tab-wrapper">
                <div id="panel-info" class="tabcontent" role="tabpanel" aria-labelledby="tab-info" hidden>
                    <div class="panel">
                    <h2>Info</h2>
                    <div class="isbn">
                        <p><strong>ISBN: </strong> <?= get_field('library_isbn') ?> | <strong>ISBN13:</strong> <?= get_field('library_isbn13') ?></p>
                        <p><strong>Publisher: </strong> <?= get_field('library_publisher') ?></p>
                        <p><strong>Number of Pages: </strong> <?= get_field('library_number_of_pages') ?></p>
                        <p><strong>Language: </strong> <?= get_field('library_google_language') ?></p>
                    </div>
                    </div>
                </div>
            </div>






        <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>