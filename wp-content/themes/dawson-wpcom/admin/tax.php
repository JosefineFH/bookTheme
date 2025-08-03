<?php 

function add_color_picker_to_taxonomy_field($taxonomy) {
    ?>
    <div class="form-field term-color-picker-wrap">
        <label for="term_color_picker_1"><?php _e('Primary Color', 'your-textdomain'); ?></label>
        <input name="term_color_picker_1" id="term_color_picker_1" type="text" value="" class="color-picker-field" />
    </div>
    <div class="form-field term-color-picker-wrap">
        <label for="term_color_picker_2"><?php _e('Secondary Color', 'your-textdomain'); ?></label>
        <input name="term_color_picker_2" id="term_color_picker_2" type="text" value="" class="color-picker-field" />
    </div>
    <div class="form-field term-color-picker-wrap">
        <label for="term_color_picker_3"><?php _e('Tertiary Color', 'your-textdomain'); ?></label>
        <input name="term_color_picker_3" id="term_color_picker_3" type="text" value="" class="color-picker-field" />
    </div>
    <div class="form-field term-text-color-wrap">
        <label for="term_text_color"><?php _e('Text Color', 'your-textdomain'); ?></label>
        <select name="term_text_color" id="term_text_color">
            <option value="black"><?php _e('Black', 'your-textdomain'); ?></option>
            <option value="white"><?php _e('White', 'your-textdomain'); ?></option>
        </select>
    </div>
    <?php
}

function edit_color_picker_to_taxonomy_field($term, $taxonomy) {
    $color1 = get_term_meta($term->term_id, 'term_color_picker_1', true) ?: '';
    $color2 = get_term_meta($term->term_id, 'term_color_picker_2', true) ?: '';
    $color3 = get_term_meta($term->term_id, 'term_color_picker_3', true) ?: '';
    $text_color = get_term_meta($term->term_id, 'term_text_color', true) ?: 'black';
    ?>
    <tr class="form-field term-color-picker-wrap">
        <th scope="row"><label for="term_color_picker_1"><?php _e('Primary Color', 'your-textdomain'); ?></label></th>
        <td>
            <input name="term_color_picker_1" id="term_color_picker_1" type="text" value="<?php echo esc_attr($color1); ?>" class="color-picker-field" />
        </td>
    </tr>
    <tr class="form-field term-color-picker-wrap">
        <th scope="row"><label for="term_color_picker_2"><?php _e('Secondary Color', 'your-textdomain'); ?></label></th>
        <td>
            <input name="term_color_picker_2" id="term_color_picker_2" type="text" value="<?php echo esc_attr($color2); ?>" class="color-picker-field" />
        </td>
    </tr>
    <tr class="form-field term-color-picker-wrap">
        <th scope="row"><label for="term_color_picker_3"><?php _e('Tertiary Color', 'your-textdomain'); ?></label></th>
        <td>
            <input name="term_color_picker_3" id="term_color_picker_3" type="text" value="<?php echo esc_attr($color3); ?>" class="color-picker-field" />
        </td>
    </tr>
    <tr class="form-field term-text-color-wrap">
        <th scope="row"><label for="term_text_color"><?php _e('Text Color', 'your-textdomain'); ?></label></th>
        <td>
            <select name="term_text_color" id="term_text_color">
                <option value="black" <?php selected($text_color, 'black'); ?>><?php _e('Black', 'your-textdomain'); ?></option>
                <option value="white" <?php selected($text_color, 'white'); ?>><?php _e('White', 'your-textdomain'); ?></option>
            </select>
        </td>
    </tr>
    <?php
}

function save_color_picker_term_meta($term_id) {
    if (isset($_POST['term_color_picker_1'])) {
        $color1 = sanitize_hex_color($_POST['term_color_picker_1']);
        if ($color1) {
            update_term_meta($term_id, 'term_color_picker_1', $color1);
        } else {
            delete_term_meta($term_id, 'term_color_picker_1');
        }
    }
    if (isset($_POST['term_color_picker_2'])) {
        $color2 = sanitize_hex_color($_POST['term_color_picker_2']);
        if ($color2) {
            update_term_meta($term_id, 'term_color_picker_2', $color2);
        } else {
            delete_term_meta($term_id, 'term_color_picker_2');
        }
    }
    if (isset($_POST['term_color_picker_3'])) {
        $color3 = sanitize_hex_color($_POST['term_color_picker_3']);
        if ($color3) {
            update_term_meta($term_id, 'term_color_picker_3', $color3);
        } else {
            delete_term_meta($term_id, 'term_color_picker_3');
        }
    }
    if (isset($_POST['term_text_color'])) {
        $text_color = sanitize_text_field($_POST['term_text_color']);
        if (in_array($text_color, ['black', 'white'])) {
            update_term_meta($term_id, 'term_text_color', $text_color);
        } else {
            delete_term_meta($term_id, 'term_text_color');
        }
    }
}


function enqueue_taxonomy_color_picker_assets($hook_suffix) {
    // Only load on taxonomy add/edit screens
    if ('edit-tags.php' === $hook_suffix || 'term.php' === $hook_suffix) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('taxonomy-color-picker', get_template_directory_uri() . '/js/taxonomy-color-picker.js', array('wp-color-picker'), false, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_taxonomy_color_picker_assets');


// // Add fields for book_author
// add_action('book_author_add_form_fields', 'add_color_picker_to_taxonomy_field');
// add_action('book_author_edit_form_fields', 'edit_color_picker_to_taxonomy_field', 10, 2);
// add_action('created_book_author', 'save_color_picker_term_meta');
// add_action('edited_book_author', 'save_color_picker_term_meta');

// Add fields for bookshelf
// add_action('bookshelf_add_form_fields', 'add_color_picker_to_taxonomy_field');
// add_action('bookshelf_edit_form_fields', 'edit_color_picker_to_taxonomy_field', 10, 2);
// add_action('created_bookshelf', 'save_color_picker_term_meta');
// add_action('edited_bookshelf', 'save_color_picker_term_meta');

// Add fields for category (optional)
add_action('category_add_form_fields', 'add_color_picker_to_taxonomy_field');
add_action('category_edit_form_fields', 'edit_color_picker_to_taxonomy_field', 10, 2);
add_action('created_category', 'save_color_picker_term_meta');
add_action('edited_category', 'save_color_picker_term_meta');
