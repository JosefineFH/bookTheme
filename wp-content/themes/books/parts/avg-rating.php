<?php
$rating = get_field('library_average_rating');
$my_rating     = get_field('library_my_rating');           

if ($rating !== false && $rating !== null) {
    $max_stars = 5;

    $full_stars = floor($rating); // Whole number part
    $decimal = $rating - $full_stars;

    if ($decimal >= 0.75) {
        $half_stars = 0;
        $full_stars += 1; // Round up
    } elseif ($decimal >= 0.25) {
        $half_stars = 1; // Show half star
    } else {
        $half_stars = 0; // No half star
    }

    $empty_stars = $max_stars - $full_stars - $half_stars;

    echo '<div class="rating-stars" aria-label="Rating: ' . esc_attr($rating) . ' out of 5">';
    
    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        echo '★';
    }

    // Half star
    if ($half_stars === 1) {
        echo '⯪'; // Unicode half-star alternative
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        echo '☆';
    }

    echo '</div>';
}


// <div class="avg-rating">

// </div>