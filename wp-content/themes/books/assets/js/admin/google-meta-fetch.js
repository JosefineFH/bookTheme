// assets/js/function/test.js
class GoogleMetaFetch {
  constructor() {
    this.metaFetch();
  }
  metaFetch() {
    jQuery(function ($) {
      // Ensure the button exists and doesn't submit a form
      $(document).on('click', '#fetch-google-meta', function (e) {
        e.preventDefault();

        var postId = $(this).data('postid');

        $('#google-meta-status').hide();
        $('#google-meta-loading').show();

        $.ajax({
          url: GoogleMetaAjax.ajax_url,
          method: 'POST',
          data: {
            action: 'fetch_google_metadata',
            post_id: postId,
            nonce: GoogleMetaAjax.nonce
          }
        })
        .done(function (response) {
          $('#google-meta-loading').hide();
          $('#google-meta-status')
            .css('color', '')
            .text(response?.data?.message || 'Metadata fetched successfully!')
            .show();
          console.log('AJAX success:', response);
        })
        .fail(function (jqXHR) {
          $('#google-meta-loading').hide();
          $('#google-meta-status')
            .css('color', 'red')
            .text('Error fetching metadata.')
            .show();
          console.error('AJAX error:', jqXHR.responseText || jqXHR.statusText);
        });
      });
    });
  }
}

// instantiate!
window.GoogleMetaFetch = new GoogleMetaFetch();
export default GoogleMetaFetch;
