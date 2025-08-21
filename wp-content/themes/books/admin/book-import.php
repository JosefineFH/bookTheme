<h1>Import</h1>

<div class="wrap">
    <h1>Import Books From CSV</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="books_csv" accept=".csv" required>
        <?php submit_button('Import Books'); ?>
    </form>
</div>

<?php
// Strip spaces, dashes, etc. Keep digits and X.
function normalize_isbn($raw) {
    $s = preg_replace('/[^0-9Xx]/', '', (string) $raw);
    return strtoupper($s);
}

function fetch_google_books_data($isbn) {
    $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);
    $url  = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;

    error_log("📡 Google Books URL: " . $url);

    $response = wp_remote_get($url, ['timeout' => 15, 'headers' => ['Accept' => 'application/json']]);
    if (is_wp_error($response)) {
        error_log('❌ wp_remote_get error: ' . $response->get_error_message());
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    error_log("📄 API items count: " . (isset($data['items']) ? count($data['items']) : 0));

    if (empty($data['items'][0])) return false;

    return [
        'volumeInfo' => $data['items'][0]['volumeInfo'] ?? [],
        'searchInfo' => $data['items'][0]['searchInfo'] ?? [],
    ];
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['books_csv'])) {
    $file = $_FILES['books_csv']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle, 0, ';');
        $header = array_map(fn($key) => preg_replace('/\x{FEFF}/u', '', trim($key)), $header);

        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (!array_filter($data)) continue;

            // Normalize row length
            $data = array_pad($data, count($header), null);
            $book = array_combine($header, $data);

            // Sanitize fields
            $title           = sanitize_text_field($book['Title'] ?? '');
            $author          = sanitize_text_field($book['Author'] ?? '');
            $ISBN            = sanitize_text_field($book['ISBN'] ?? '');
            $ISBN13          = sanitize_text_field($book['ISBN13'] ?? '');
            $my_rating       = sanitize_text_field($book['My Rating'] ?? '');
            $average_rating  = sanitize_text_field($book['Average Rating'] ?? '');
            $publisher       = sanitize_text_field($book['Publisher'] ?? '');
            $number_of_pages = sanitize_text_field($book['Number of Pages'] ?? '');
            $date_read       = sanitize_text_field($book['Date Read'] ?? '');
            $read_count      = sanitize_text_field($book['Read Count'] ?? '');
            $my_review       = sanitize_textarea_field($book['My Review'] ?? '');
            $bookshelves     = sanitize_text_field($book['Bookshelves'] ?? '');
            $exclusive_shelf = sanitize_text_field($book['Exclusive Shelf'] ?? '');

            if (empty($title)) {
                error_log("❌ Skipped due to missing title. Row data:");
                error_log(print_r($book, true));
                $skipped++;
                continue;
            }

            // 🔍 Fetch Google Books Data — ISBN only
            $google_data = null;
            $thumbnail = $google_description = $google_subtitle = $publishedDate = $maturity = $language = '';
            $categories = [];

            $isbn13_norm = normalize_isbn($ISBN13);
            $isbn_norm   = normalize_isbn($ISBN);
            $lookup_isbn = $isbn13_norm ?: $isbn_norm;

            if ($lookup_isbn) {
                error_log("➡️ Fetching by ISBN for '{$title}': {$lookup_isbn}");
                // ✅ pass RAW ISBN (no 'isbn:' prefix here)
                $google_data = fetch_google_books_data($lookup_isbn);
            } else {
                error_log("⏭️ No ISBN/ISBN13 for '{$title}' — skipping Google Books fetch.");
            }

            // Defaults
            $post_content = $my_review;
            $post_excerpt = '';

            // Safe logging of the returned structure
            error_log('🔎 google_data: ' . print_r($google_data, true));

            if ($google_data) {
                $volumeInfo = $google_data['volumeInfo'] ?? [];
                $searchInfo = $google_data['searchInfo'] ?? [];

                // 👇 All of these live under volumeInfo
                $thumbnail          = $volumeInfo['imageLinks']['thumbnail'] ?? '';
                $google_description = $volumeInfo['description'] ?? '';
                $google_subtitle    = $volumeInfo['subtitle'] ?? '';
                $publishedDate      = $volumeInfo['publishedDate'] ?? '';
                $maturity           = $volumeInfo['maturityRating'] ?? '';
                $categories         = $volumeInfo['categories'] ?? [];
                $language           = $volumeInfo['language'] ?? '';

                // Excerpt priority: searchInfo.textSnippet -> subtitle
                if (!empty($searchInfo['textSnippet'])) {
                    $post_excerpt = wp_strip_all_tags($searchInfo['textSnippet']);
                } elseif (!empty($google_subtitle)) {
                    $post_excerpt = $google_subtitle;
                }

                $post_content = $google_description ?: $my_review;

                error_log("📚 Google data set for: {$title}");
            } else {
                error_log("⚠️ No Google Books data found for: {$title}");
            }


            // 📘 Insert Post
            $post_id = wp_insert_post(array(
                'post_type'    => 'library_books',
                'post_title'   => $title,
                'post_content' => $post_content,
                'post_excerpt' => $post_excerpt,
                'post_status'  => 'publish',
            ));

            if (!is_wp_error($post_id)) {
                // ✅ Taxonomies
                if (!empty($author)) {
                    wp_set_object_terms($post_id, $author, 'library_book_author', true);
                }

                if (!empty($bookshelves)) {
                    $shelves = array_map('trim', explode(',', $bookshelves));
                    wp_set_object_terms($post_id, $shelves, 'library_bookshelf', true);
                }

                if (!empty($categories)) {
                    wp_set_object_terms($post_id, $categories, 'library_book_categories', true);
                }

                // ✅ Meta Fields
                update_field('library_isbn', $ISBN, $post_id);
                update_field('library_isbn13', $ISBN13, $post_id);
                update_field('library_my_rating', $my_rating, $post_id);
                update_field('library_average_rating', $average_rating, $post_id);
                update_field('library_publisher', $publisher, $post_id);
                update_field('library_number_of_pages', $number_of_pages, $post_id);
                update_field('library_date_read', $date_read, $post_id);
                update_field('library_read_count', $read_count, $post_id);
                update_field('library_exclusive_shelf', $exclusive_shelf, $post_id);

                if (!empty($thumbnail)) {
                    update_field('library_google_thumbnail', $thumbnail, $post_id);
                }
                if (!empty($google_description)) {
                    update_field('library_google_description', $google_description, $post_id);
                }
                if (!empty($publishedDate)) {
                    update_field('library_google_published_date', $publishedDate, $post_id);
                }
                if (!empty($maturity)) {
                    update_field('library_google_maturity_rating', $maturity, $post_id);
                }
                if (!empty($language)) {
                    update_field('library_google_language', $language, $post_id);
                }

                $imported++;
                error_log("✅ Imported: $title");
            } else {
                $skipped++;
                error_log("❌ Failed to import: $title");
            }
        }

        fclose($handle);

        echo '<div class="notice notice-success"><p>';
        echo "📘 Imported <strong>$imported</strong> books. ";
        echo "🛑 Skipped <strong>$skipped</strong> rows.";
        echo '</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>❌ Failed to open uploaded file.</p></div>';
    }
}
?>
