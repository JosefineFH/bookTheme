<?php
function register_library_post_type()
{
    $labels = array(
        'name' => 'Books',
        'singular_name' => 'Book',
        'menu_name' => 'Library',
        'name_admin_bar' => 'Book',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Book',
        'new_item' => 'New Book',
        'edit_item' => 'Edit Book',
        'view_item' => 'View Book',
        'all_items' => 'All Books',
        'search_items' => 'Search Books',
        'not_found' => 'No books found.',
        'not_found_in_trash' => 'No books found in Trash.',
    );

    $args = array(
    'labels' => $labels,
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'books'],
    'menu_icon' => 'dashicons-book',
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
    'show_in_rest' => true,
    'show_ui' => true,
    );

    register_post_type('library_books', $args);
}
function register_books_taxonomies()
{
    // Author taxonomy
    register_taxonomy('library_book_author', 'library_books', array(
        'labels' => array(
            'name' => 'Authors',
            'singular_name' => 'Author',
            'search_items' => 'Search Authors',
            'all_items' => 'All Authors',
            'edit_item' => 'Edit Author',
            'update_item' => 'Update Author',
            'add_new_item' => 'Add New Author',
            'new_item_name' => 'New Author Name',
            'menu_name' => 'Authors',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_ui' => true,
        'rewrite' => array('slug' => 'authors', 'with_front' => false),
    ));

    // Author taxonomy
        register_taxonomy('library_book_categories', 'library_books', array(
            'labels' => array(
                'name' => 'Book Categories',
                'singular_name' => 'Book Category',
                'add_new_item' => 'Add New Category',
                'menu_name' => 'Book Categories',
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
            'show_ui' => true,
            'rewrite' => array('slug' => 'categories'),
        ));

    // Bookshelf taxonomy
    register_taxonomy('library_bookshelf', 'library_books', array(
        'labels' => array(
            'name' => 'Bookshelves',
            'singular_name' => 'Bookshelf',
            'search_items' => 'Search Bookshelves',
            'all_items' => 'All Bookshelves',
            'edit_item' => 'Edit Bookshelf',
            'update_item' => 'Update Bookshelf',
            'add_new_item' => 'Add New Bookshelf',
            'new_item_name' => 'New Bookshelf Name',
            'menu_name' => 'Bookshelves',
        ),
        'hierarchical' => true,  // Like categories (hierarchical)
        'show_in_rest' => true,
        'show_ui' => true,
        'rewrite' => array('slug' => 'bookshelf', 'with_front' => false),
    ));

    register_taxonomy('book_series', 'library_books', array(
        'labels' => array(
            'name' => 'Book Series',
            'singular_name' => 'Book Series',
            'search_items' => 'Search Book Series',
            'all_items' => 'All Book Series',
            'edit_item' => 'Edit Book Series',
            'update_item' => 'Update Book Series',
            'add_new_item' => 'Add New Book Series',
            'new_item_name' => 'New Book Series Name',
            'menu_name' => 'Book Series',
        ),
        'hierarchical' => true,  // Like categories (hierarchical)
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'query_var' => 'book_series',
        'rewrite' => ['slug' => 'series', 'hierarchical' => true, 'with_front' => true],
    ));
}


add_action('init', 'register_library_post_type', 0);
add_action('init', 'register_books_taxonomies');


function render_primary_library_category_meta_box($post) {
    $taxonomy = 'library_book_categories';
    $terms = wp_get_post_terms($post->ID, $taxonomy);
    $selected = get_post_meta($post->ID, '_primary_library_category', true);

    if (empty($terms)) {
        echo '<p>Please assign categories first.</p>';
        return;
    }

    echo '<select name="primary_library_category">';
    echo '<option value="">-- Select Primary Category --</option>';
    foreach ($terms as $term) {
        $is_selected = selected($selected, $term->term_id, false);
        echo "<option value='{$term->term_id}' {$is_selected}>{$term->name}</option>";
    }
    echo '</select>';
}

add_action('save_post_library_books', function($post_id) {
    if (isset($_POST['primary_library_category'])) {
        update_post_meta($post_id, '_primary_library_category', sanitize_text_field($_POST['primary_library_category']));
    }
});
