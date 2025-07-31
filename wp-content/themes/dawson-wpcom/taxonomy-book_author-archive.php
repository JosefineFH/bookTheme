<?php
/* Template Name: Book Author Archive */

get_header();

echo '<div class="wrap">';
echo '<h1>All Authors</h1>';

$terms = get_terms([
    'taxonomy' => 'book_author',
    'hide_empty' => false,
]);

if (!empty($terms) && !is_wp_error($terms)) {
    echo '<ul>';
    foreach ($terms as $term) {
        $link = get_term_link($term);
        echo '<li><a href="' . esc_url($link) . '">' . esc_html($term->name) . '</a></li>';
    }
    echo '</ul>';
} else {
    echo '<p>No authors found.</p>';
}

echo '</div>';

get_footer();
