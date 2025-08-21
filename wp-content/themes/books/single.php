<?php get_header(); ?>
<div class="content">
    <h1><?= the_title()?></h1>
    <?php  the_content(); ?>
    <hr>
    <?php

if ( comments_open() || get_comments_number() ) {
    comments_template(); // Loads comments.php
}
?>
</div>
<?php
get_footer();
