<?php 
function ml_preview_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'title' => 'This book',
        ), $atts, 'book_preview'
    );

    return '<div class="ml-preview-box" style="border:1px solid #ddd; padding:15px; margin:20px 0; border-radius:8px;">
        <strong>' . esc_html($atts['title']) . '</strong><br>
        A preview has been generated just for you using machine learning.
    </div>';
}
add_shortcode('book_preview', 'ml_preview_shortcode');
