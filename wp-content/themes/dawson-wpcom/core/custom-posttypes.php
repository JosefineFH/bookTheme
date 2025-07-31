<?php 

function register_books_post_type() {
    $labels = array(
        'name'               => 'Books',
        'singular_name'      => 'Book',
        'menu_name'          => 'Books',
        'name_admin_bar'     => 'Book',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Book',
        'new_item'           => 'New Book',
        'edit_item'          => 'Edit Book',
        'view_item'          => 'View Book',
        'all_items'          => 'All Books',
        'search_items'       => 'Search Books',
        'not_found'          => 'No books found.',
        'not_found_in_trash' => 'No books found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'books'),
        'menu_icon'          => 'dashicons-book', // Optional: book icon
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies'         => array('category'), // ✅ Enable default categories
        'show_in_rest'       => true, // Enable Gutenberg editor
    );

    register_post_type('book', $args);
}
function register_book_taxonomies() {
    // Author taxonomy
    register_taxonomy('book_author', 'book', array(
    'labels' => array(
        'name' => 'Authors',
        'singular_name' => 'Author',
            'search_items'      => 'Search Authors',
            'all_items'         => 'All Authors',
            'edit_item'         => 'Edit Author',
            'update_item'       => 'Update Author',
            'add_new_item'      => 'Add New Author',
            'new_item_name'     => 'New Author Name',
            'menu_name'         => 'Authors',
        ),
    'hierarchical' => true,
    'show_in_rest' => true,
    'show_ui' => true,
    'rewrite' => array('slug' => 'authors', 'with_front' => false),
    ));

    // Bookshelf taxonomy
    register_taxonomy('bookshelf', 'book', array(
        'labels' => array(
            'name'              => 'Bookshelves',
            'singular_name'     => 'Bookshelf',
            'search_items'      => 'Search Bookshelves',
            'all_items'         => 'All Bookshelves',
            'edit_item'         => 'Edit Bookshelf',
            'update_item'       => 'Update Bookshelf',
            'add_new_item'      => 'Add New Bookshelf',
            'new_item_name'     => 'New Bookshelf Name',
            'menu_name'         => 'Bookshelves',
        ),
        'hierarchical' => true,  // Like categories (hierarchical)
        'show_in_rest' => true,
        'show_ui'      => true,
        'rewrite' => array('slug' => 'bookshelf', 'with_front' => false),
    ));
}
// Flush rewrite rules on theme activation (run once)
function mytheme_flush_rewrite_rules() {
    register_book_taxonomies();
    flush_rewrite_rules();
}


function custom_book_author_rewrite() {
    add_rewrite_rule(
        '^authors/?$',
        'index.php?authors_archive=1',
        'top'
    );
}
add_action('init', 'custom_book_author_rewrite');


function register_book_author_query_var($vars) {
    $vars[] = 'authors_archive';
    return $vars;
}
add_filter('query_vars', 'register_book_author_query_var');


function load_book_author_archive_template($template) {

    if (get_query_var('authors_archive')) {
        return get_template_directory() . '/taxonomy-book_author-archive.php';


    }
    return $template;
}
add_filter('template_include', 'load_book_author_archive_template');


add_action('after_switch_theme', 'mytheme_flush_rewrite_rules');
add_action('init', 'register_book_taxonomies');
add_action('init', 'register_books_post_type');
