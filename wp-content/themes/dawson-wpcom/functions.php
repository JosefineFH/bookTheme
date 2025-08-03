<?php declare(strict_types=1); ?>
<?php
include_once dirname(__FILE__) . '/core/custom-posttypes.php';
include_once dirname(__FILE__) . '/admin/tax.php';
function always_enqueue_frontend_jquery()
{
    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'always_enqueue_frontend_jquery');

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
        get_stylesheet_directory_uri() . '/styles/style.css',
        array('dawson-base-style'),  // if you want to load after base
        wp_get_theme()->get('Version')
    );
}

function custom_dawson_scripts()
{
    $script_path = get_template_directory() . '/scripts/main.js';
    if (file_exists($script_path)) {
        wp_enqueue_script(
            'custom-script',
            get_template_directory_uri() . '/scripts/main.js',
            ['jquery'],
            filemtime($script_path),
            true
        );
        error_log('Enqueued custom-script');
    } else {
        error_log('Script file missing: ' . $script_path);
    }
}



function add_book_import_page()
{
    add_menu_page('Import Books', 'Import Books', 'manage_options', 'import-books', 'render_import_page');
}

function render_import_page()
{
    include get_template_directory() . '/admin/import-books.php';
}

function mytheme_register_menus()
{
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'your-textdomain'),
    ));
}

add_action('after_setup_theme', 'dawson_support');
add_action('after_setup_theme', 'mytheme_register_menus');
add_action('wp_enqueue_scripts', 'custom_dawson_scripts', 20);
add_action('wp_enqueue_scripts', 'custom_dawson_styles');
add_action('wp_enqueue_scripts', 'dawson_styles');
add_action('admin_menu', 'add_book_import_page');

add_theme_support('post-thumbnails', array('post', 'page', 'book'));

function get_primary_category($post_id) {
    $primary_cat_id = get_post_meta($post_id, '_primary_category', true);

    if ($primary_cat_id && term_exists((int)$primary_cat_id, 'category')) {
        return get_term($primary_cat_id, 'category');
    }

    // Fallback: return the first category assigned
    $categories = get_the_terms($post_id, 'category');
    if ($categories && !is_wp_error($categories)) {
        return $categories[0];
    }
    return false;
}
