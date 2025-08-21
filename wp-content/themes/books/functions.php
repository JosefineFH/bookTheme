<?php declare(strict_types=1); ?>
<?php
include_once dirname(__FILE__) . '/core/posttypes.php';
include_once dirname(__FILE__) . '/core/shortcode.php';
include_once dirname(__FILE__) . '/admin/tax.php';
function always_enqueue_frontend_jquery()
{
    wp_enqueue_script('jquery');
}


/**
 * Dawson functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Dawson
 * @since Dawson 1.0
 */
if (!function_exists('dawson_support')):
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * @since Dawson 1.0
     *
     * @return void
     */
    function dawson_support()
    {
        // Enqueue editor styles.
        add_editor_style('style.css');

        // Make theme available for translation.
        load_theme_textdomain('dawson');
    }
endif;

function dawson_styles()
{
    wp_enqueue_style(
        'dawson-base-style',
        get_stylesheet_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}

function custom_dawson_styles()
{

    wp_enqueue_style(
        'custom-style',
        get_stylesheet_directory_uri() . '/public/style/style.css',
        array('dawson-base-style'),  // if you want to load after base
        wp_get_theme()->get('Version')
    );
}
add_action('admin_enqueue_scripts', 'my_custom_admin_styles');

function my_custom_admin_styles() {
    wp_enqueue_style(
        'my-admin-style',
        get_stylesheet_directory_uri() . '/public/style/admin-style.css', // use get_template_directory_uri() if not using a child theme
        [],
        filemtime(get_stylesheet_directory() . '/public/style/admin-style.css') // for cache busting
    );
}

// functions.php
add_action('wp_enqueue_scripts', function () {
  $handle = 'theme-main';
  wp_enqueue_script(
    $handle,
    get_template_directory_uri() . '/public/script/main.js',
    [], // deps
    '1.0.0',
    true // in footer
  );
  // mark as type="module"
  wp_script_add_data($handle, 'type', 'module');
});

add_action('admin_enqueue_scripts', function () {
    $handle = 'theme-admin';
    wp_enqueue_script(
        $handle,
        get_template_directory_uri() . '/public/script/admin.js',
        [], // dependencies
        '1.0.0',
        true // load in footer
    );
    // wp_script_add_data($handle, 'type', 'module');

    wp_localize_script($handle, 'GoogleMetaAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('google_meta_nonce'),
    ]);
});


function add_book_add_page() {
    add_menu_page(
        'Import Books From File',
        'Import Books From File',
        'manage_options',
        'import-books-file',
        'render_import_page_imoprt'  // ← Make sure this matches the function below
    );
}
function render_import_page()
{
    include get_template_directory() . '/admin/import-books.php';
}
function render_import_page_imoprt()
{
    error_log(get_template_directory() . '/admin/book-import.php');
    include get_template_directory() . '/admin/book-import.php';
}
function mytheme_register_menus()
{
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'your-textdomain'),
    ));
}




// Add meta box
add_action('add_meta_boxes', function () {
    add_meta_box(
        'google_books_meta',
        'Google Books Metadata',
        'render_google_books_meta_box',
        'library_books',
        'side',
        'default'
    );
});

// Render meta box
function render_google_books_meta_box($post) {
    $isbn = get_post_meta($post->ID, 'isbn', true);
    wp_nonce_field('save_google_books_meta', 'google_books_meta_nonce');

    echo '<p>';
    echo '<label for="isbn_field"><strong>ISBN:</strong></label><br>';
    echo '<input type="text" id="isbn_field" name="isbn_field" value="' . esc_attr($isbn) . '" style="width: 300px;">';
    echo '</p>';

    echo '<button type="button" class="button" id="fetch-google-meta" data-postid="' . esc_attr($post->ID) . '">Fetch Google Metadata</button>';

    echo '<div id="google-meta-loading" style="margin-top:10px; color: #888; display:none;">Loading...</div>';
    echo '<div id="google-meta-status" style="margin-top:10px; color:green;"></div>';
}



add_action('save_post_library_books', function($post_id) {
    if (!isset($_POST['google_books_meta_nonce']) || !wp_verify_nonce($_POST['google_books_meta_nonce'], 'save_google_books_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['isbn_field'])) {
        update_post_meta($post_id, 'isbn', sanitize_text_field($_POST['isbn_field']));
    }
});


add_action('init', function () {
    $post_types = get_post_types([], 'objects');
});

$primary_term_id = get_post_meta(get_the_ID(), '_primary_library_category', true);
if ($primary_term_id) {
    $term = get_term($primary_term_id, 'library_book_categories');
    echo 'Primary Category: ' . esc_html($term->name);
}


add_action('wp_head', 'output_primary_category_colors_as_css');

function output_primary_category_colors_as_css() {
    if (!is_singular('library_books')) {
        return;
    }

    $post_id  = get_the_ID();
    $taxonomy = 'library_book_categories';

    // Get primary term id via Yoast (with sensible fallbacks)
    $primary_term_id = my_get_primary_term_id($post_id, $taxonomy);

    if (!$primary_term_id) {
        return;
    }

    // ACF term meta: prefer the "term_{$id}" reference
    $acf_ref = 'term_' . $primary_term_id;

    $primary_color   = get_field('book_categories_primary_colors', $acf_ref);
    $secondary_color = get_field('book_secondary_colors', $acf_ref);
    $background_color= get_field('color_background_colors', $acf_ref);
    $txt_color       = get_field('book_categories_txt_colors', $acf_ref);

    if ($primary_color || $txt_color || $secondary_color || $background_color) {
        echo "<style>:root {";
        if ($primary_color) {
            echo '--primary-category-color:' . esc_attr($primary_color) . ';';
        }
        if ($secondary_color) {
            echo '--secondary-color:' . esc_attr($secondary_color) . ';';
        }
        if ($background_color) {
            echo '--background-color:' . esc_attr($background_color) . ';';
        }
        if ($txt_color) {
            echo '--primary-category-text-color:' . esc_attr($txt_color) . ';';
        }
        echo "}</style>";
    }
}

function my_get_primary_term_id($post_id, $taxonomy) {
    // 1) Yoast helper class
    if (class_exists('WPSEO_Primary_Term')) {
        $yoast_primary_term = new WPSEO_Primary_Term($taxonomy, $post_id);
        $term_id = (int) $yoast_primary_term->get_primary_term();
        if ($term_id && !is_wp_error($term_id)) {
            return $term_id;
        }
    }

    // 2) Yoast meta key fallback
    $meta_key = '_yoast_wpseo_primary_' . $taxonomy; // e.g. _yoast_wpseo_primary_library_book_categories
    $term_id  = (int) get_post_meta($post_id, $meta_key, true);
    if ($term_id) {
        return $term_id;
    }

    // 3) First assigned term as a final fallback
    $term_ids = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'ids']);
    return !empty($term_ids) ? (int) $term_ids[0] : 0;
}

add_action('wp_enqueue_scripts', 'always_enqueue_frontend_jquery');

add_action('after_setup_theme', 'dawson_support');
add_action('after_setup_theme', 'mytheme_register_menus');
add_action('wp_enqueue_scripts', 'custom_dawson_styles');
add_action('wp_enqueue_scripts', 'dawson_styles');
add_action('admin_menu', 'add_book_add_page');

add_theme_support('post-thumbnails', array('post', 'page', 'book'));

add_filter('script_loader_tag', function($tag, $handle, $src){
    if ($handle === 'custom-script') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);


// Handle for logged-in users (wp-admin)
add_action('wp_ajax_fetch_google_metadata', 'handle_fetch_google_metadata');

// If this is used on the front-end for visitors, also include:
add_action('wp_ajax_nopriv_fetch_google_metadata', 'handle_fetch_google_metadata');

function handle_fetch_google_metadata() {
    check_ajax_referer('google_meta_nonce', 'nonce');
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error(['message' => 'Missing post_id'], 400);
    }

    wp_send_json_success(['message' => 'Metadata fetched successfully', 'post_id' => $post_id]);
}


add_action('after_setup_theme', function () {
    // Enable support for custom logo
    add_theme_support('custom-logo', [
        'height'      => 100,   // suggested height (px)
        'width'       => 400,   // suggested width (px)
        'flex-height' => true,  // allow any height
        'flex-width'  => true,  // allow any width
    ]);
});
