jQuery(document).ready(function ($) {
    // Scan all posts
    $('#sums-scan-all-posts').on('click', function () {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'sums_seo_handle_request',
                action_type: 'scan_all_posts'
            },
            success: function (response) {
                alert(response.data.message);
            },
            error: function () {
                alert('An error occurred.');
            }
        });
    });
});