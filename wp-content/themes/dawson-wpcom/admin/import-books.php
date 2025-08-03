<?php
if (!current_user_can('manage_options'))
    return;

// Verify nonce for security
if (isset($_POST['import_book_nonce']) && !wp_verify_nonce($_POST['import_book_nonce'], 'import_book_action')) {
    echo '<div class="notice notice-error"><p>Security check failed. Please try again.</p></div>';
    return;
}

if (isset($_POST['search_term'])) {
    $query = sanitize_text_field($_POST['search_term']);
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($query);
    $response = wp_remote_get($url);
    $books = [];

    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        $books = $data['items'] ?? [];
    }
}

if (isset($_POST['import_book'])) {
    $book_data = json_decode(stripslashes($_POST['import_book']), true);

    $buy_link = $book_data['buyLink'] ?? '';
    $page_count = $book_data['pageCount'] ?? '';
    $categories = $book_data['categories'] ?? [];

    if ($buy_link) {
        // Try fetching extra data from Google Play page
        $response = wp_remote_get($buy_link);

        if (!is_wp_error($response)) {
            $html = wp_remote_retrieve_body($response);

            // 1. Extract updated page count if found
            if (preg_match('/(\d+)\s+pages/i', $html, $matches)) {
                $updated_page_count = (int) $matches[1];
                if ($updated_page_count !== (int) $page_count) {
                    $page_count = $updated_page_count;
                }
            }

            // 2. Extract genres like "Fiction / Romance / Contemporary"
            if (preg_match('/<a[^>]+href="\/store\/books\/category[^"]+"[^>]*>(.*?)<\/a>/is', $html, $cat_matches)) {
                $extra_genre = trim(strip_tags($cat_matches[1]));
                if (!in_array($extra_genre, $categories)) {
                    $categories[] = $extra_genre;
                }
            }
        }
    }

    $title = $book_data['title'] ?? 'Untitled';
    $description = $book_data['description'] ?? '';
    $excerpt = $book_data['excerpt'] ?? '';
    $isbn = $book_data['isbn'] ?? '';
    $authors = $book_data['authors'] ?? [];
    $thumbnail = $book_data['thumbnail'] ?? '';
    $publisher = $book_data['publisher'] ?? '';
    $publishedDate = $book_data['publishedDate'] ?? '';
    $maturityRating = $book_data['maturityRating'] ?? '';

    $post_id = wp_insert_post([
        'post_type' => 'book',
        'post_title' => $title,
        'post_content' => $description,
        'post_status' => 'publish',
        'post_excerpt' => $excerpt,
    ]);

    // Helper function: insert term or get existing term slug
    function insert_or_get_term_slug($term_name, $taxonomy)
    {
        if (empty($term_name)) {
            return '';
        }
        $term = wp_insert_term($term_name, $taxonomy);
        if (is_wp_error($term)) {
            if ($term->get_error_code() === 'term_exists') {
                $existing_term = get_term_by('name', $term_name, $taxonomy);
                if ($existing_term && !is_wp_error($existing_term)) {
                    return $existing_term->slug;
                }
            }
            // On other errors, just return empty string
            return '';
        } else {
            $term_obj = get_term($term['term_id']);
            if ($term_obj && !is_wp_error($term_obj)) {
                return $term_obj->slug;
            }
            return '';
        }
    }

    // Get selected or new taxonomy terms
    $bookshelf_slug = sanitize_text_field($_POST['book_tax_bookshelf'] ?? '');
    $bookshelf_new = sanitize_text_field($_POST['book_tax_bookshelf_new'] ?? '');

    $category_slug = sanitize_text_field($_POST['book_tax_category'] ?? '');
    $category_new = sanitize_text_field($_POST['book_tax_category_new'] ?? '');

    $author_slug = sanitize_text_field($_POST['book_tax_author'] ?? '');
    $author_new = sanitize_text_field($_POST['book_tax_author_new'] ?? '');

    // Use new term if provided, otherwise fallback to selected
    if (!empty($bookshelf_new)) {
        $bookshelf_slug = insert_or_get_term_slug($bookshelf_new, 'bookshelf');
    }

    if (!empty($category_new)) {
        $category_slug = insert_or_get_term_slug($category_new, 'category');
    }

    if (!empty($author_new)) {
        $author_slug = insert_or_get_term_slug($author_new, 'book_author');
    }

    if ($post_id) {
        if ($bookshelf_slug)
            wp_set_object_terms($post_id, [$bookshelf_slug], 'bookshelf');
        if ($category_slug)
            wp_set_object_terms($post_id, [$category_slug], 'category');
        if ($author_slug)
            wp_set_object_terms($post_id, [$author_slug], 'book_author');

        // publisher
        update_post_meta($post_id, 'book_publisher', sanitize_text_field($publisher));
        // Save published date
        update_post_meta($post_id, 'book_published_date', sanitize_text_field($publishedDate));
        // Save maturity rating
        update_post_meta($post_id, 'book_maturity_rating', sanitize_text_field($maturityRating));
        update_post_meta($post_id, 'book_isbn', $isbn);
        update_post_meta($post_id, 'book_authors', implode(', ', $authors));
        update_post_meta($post_id, 'book_page_count', $page_count);


        if (!empty($categories)) {
            foreach ($categories as $cat_name) {
                $slug = insert_or_get_term_slug($cat_name, 'category');
                if ($slug) {
                    wp_set_object_terms($post_id, [$slug], 'category', true);
                }
            }
        }
        if ($thumbnail) {
            $thumbnail = preg_replace('/^http:\/\//', 'https://', $thumbnail);

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Download the image temporarily
            $tmp = download_url($thumbnail);

            if (!is_wp_error($tmp)) {
                $file_array = [
                    'name' => 'book-cover' . $isbn . '.jpg',
                    'tmp_name' => $tmp
                ];

                $image_id = media_handle_sideload($file_array, $post_id);

                // If image upload is successful, set it as thumbnail
                if (!is_wp_error($image_id)) {
                    set_post_thumbnail($post_id, $image_id);
                    error_log('Thumbnail set successfully.');
                } else {
                    error_log('Image sideload error: ' . $image_id->get_error_message());
                    @unlink($tmp);  // Delete temp file on failure
                }
            } else {
                error_log('Download URL failed: ' . $tmp->get_error_message());
            }
        }

        echo '<div class="notice notice-success"><p>Book "' . esc_html($title) . '" imported successfully!</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Import Book</h1>

    <form method="post">
        <input type="text" name="search_term" placeholder="Enter title or ISBN" required style="width:300px;" />
        <button class="button button-primary" type="submit">Search</button>
    </form>

    <?php if (!empty($books)): ?>
        <h2>Results</h2>
        <ul>
            <?php
            foreach ($books as $book):
                $info = $book['volumeInfo'];
                $title = $info['title'] ?? 'No title';
                $authors = $info['authors'] ?? [];
                $description = $info['description'] ?? '';
                $subtitle = $info['subtitle'] ?? '';
                $image_links = $info['imageLinks'] ?? [];
                $thumbnail = $image_links['thumbnail'] ?? '';
                $small_thumbnail = $image_links['smallThumbnail'] ?? '';
                $industryIds = $info['industryIdentifiers'] ?? [];
                $isbn = '';
                $pageCount = $info['pageCount'] ?? '';
                $publisher = $info['publisher'] ?? '';
                $publishedDate = $info['publishedDate'] ?? '';
                $maturityRating = $info['maturityRating'] ?? '';
                foreach ($industryIds as $id) {
                    if ($id['type'] === 'ISBN_13') {
                        $isbn = $id['identifier'];
                        break;
                    }
                }

                $book_payload = json_encode([
                    'title' => $title,
                    'description' => $description,
                    'isbn' => $isbn,
                    'authors' => $authors,
                    'thumbnail' => $thumbnail,
                    'thumbnail_small' => $small_thumbnail,
                    'excerpt' => $subtitle,
                    'pageCount' => $pageCount,
                    'publisher' => $publisher,
                    'publishedDate' => $publishedDate,
                    'maturityRating' => $maturityRating,
                    'categories' => $info['categories'] ?? [],
                ]);
                ?>
                <li style="margin: 1em 0; padding: 1em; background: #fff; border: 1px solid #ccc;">
                    <h3><?= esc_html($title); ?></h3>
                    <p><strong>Author(s):</strong> <?= esc_html(implode(', ', $authors)); ?></p>
                    <?php if ($thumbnail): ?>
                        <img src="<?= esc_url($thumbnail); ?>" alt="" style="height:150px;" />
                    <?php endif; ?>

                    <form method="post" style="margin-top: 1em;">
                        <?php
                        // Fetch terms for dropdowns
                        $bookshelves = get_terms(['taxonomy' => 'bookshelf', 'hide_empty' => false]);
                        $categories = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
                        $authors_terms = get_terms(['taxonomy' => 'book_author', 'hide_empty' => false]);
                        ?>

                        <p>
                            <label>Bookshelf:</label><br>
                            <select name="book_tax_bookshelf" style="width: 200px;">
                                <option value="">Select a bookshelf</option>
                                <?php foreach ($bookshelves as $term): ?>
                                    <option value="<?= esc_attr($term->slug); ?>"><?= esc_html($term->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br><small>Or add new:</small><br>
                            <input type="text" name="book_tax_bookshelf_new" placeholder="New bookshelf" style="width: 200px;">
                        </p>

                        <p>
                            <label>Category:</label><br>
                            <select name="book_tax_category" style="width: 200px;">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $term): ?>
                                    <option value="<?= esc_attr($term->slug); ?>"><?= esc_html($term->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br><small>Or add new:</small><br>
                            <input type="text" name="book_tax_category_new" placeholder="New category" style="width: 200px;">
                        </p>

                        <p>
                            <label>Author:</label><br>
                            <select name="book_tax_author" style="width: 200px;">
                                <option value="">Select an author</option>
                                <?php foreach ($authors_terms as $term): ?>
                                    <option value="<?= esc_attr($term->slug); ?>"><?= esc_html($term->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br><small>Or add new:</small><br>
                            <input type="text" name="book_tax_author_new" placeholder="New author" style="width: 200px;">
                        </p>

                        <input type="hidden" name="import_book" value='<?= esc_attr($book_payload); ?>' />
                        <?php wp_nonce_field('import_book_action', 'import_book_nonce'); ?>
                        <button class="button button-secondary" type="submit">Add this book</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
