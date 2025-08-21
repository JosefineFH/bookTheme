<?php

get_header();


$books = get_posts(array(
    'post_type'      => 'book',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
));

echo "<pre>";
foreach ($books as $book) {
    var_dump($book); // This is a WP_Post object
}
echo "</pre>";




get_footer();
