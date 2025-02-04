jQuery(document).ready(function ($) {
    // Scan all posts
    $('#sums-scan-all-posts').on('click', function (e) {
        e.preventDefault(); // Prevent default action if it's a link or form button

        // Disable the button to prevent multiple clicks
        $(this).prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'sums_seo_handle_request',
                action_type: 'scan_all_posts'
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert('An error occurred: ' + response.data.message);
                }
            },
            error: function () {
                alert('An error occurred.');
            },
            complete: function () {
                // Re-enable the button after the request is complete
                $('#sums-scan-all-posts').prop('disabled', false);
            }
        });
    });
});