<?php declare(strict_types=1); ?>
<?php

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

add_action('after_setup_theme', 'dawson_support');

function dawson_styles()
{
    wp_enqueue_style(
        'dawson-base-style',
        get_stylesheet_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'dawson_styles');

function custom_dawson_styles()
{
    wp_enqueue_style(
        'custom-style',
        get_stylesheet_directory_uri() . '/styles/style.css',
        array('dawson-base-style'),  // if you want to load after base
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'custom_dawson_styles');

// wp-content/themes/dawson-wpcom/styles/style.css
add_action('wp_enqueue_scripts', 'dawson_styles');



include_once dirname(__FILE__) . '/core/custom-posttypes.php';

function add_book_import_page()
{
    add_menu_page('Import Books', 'Import Books', 'manage_options', 'import-books', 'render_import_page');
}

add_action('admin_menu', 'add_book_import_page');

function render_import_page()
{
    include get_template_directory() . '/admin/import-books.php';
}


function mytheme_register_menus() {
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'your-textdomain'),
    ));
}
add_action('after_setup_theme', 'mytheme_register_menus');
