<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header id="site-header" role="banner">
        <div class="site-branding">
            <?php 
            if (has_custom_logo()) {
                the_custom_logo();
            } else { ?>
                <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a></h1>
            <?php } ?>
        </div>

        <nav id="site-navigation" role="navigation">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'main-menu',
            'menu_id'        => 'primary-menu',
            'container'      => 'nav', // Optional: adds a <nav> wrapper
            'container_class'=> 'main-navigation', // Optional: CSS class
        ));
        ?>

        </nav>
    </header>
