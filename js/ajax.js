jQuery(document).ready(function ($) {
    // Handle Auto Fix button click
    $('.sums-auto-fix-button').on('click', function () {
        var button = $(this);
        var post_id = button.data('post-id');

        // Disable the button and show loading icon
        button.prop('disabled', true).text('Fixing...');

        $.ajax({
            url: sums_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'sums_auto_fix',
                post_id: post_id,
                _ajax_nonce: sums_ajax_object.nonce,
            },
            success: function (response) {
                if (response.success) {
                    // Remove the fixed post row from the table
                    button.closest('tr').fadeOut(500, function () {
                        $(this).remove(); // Removes the row from the table
                    });
                } else {
                    alert('No issues found for this post or the fix failed.');
                }
                button.prop('disabled', false).text('Auto Fix');
            },
            error: function () {
                alert('An error occurred. Please try again.');
                button.prop('disabled', false).text('Auto Fix');
            },
        });
    });

    // Handle Fix with OpenAI button click
    $('.sums-openai-fix-button').on('click', function () {
        var button = $(this);
        var post_id = button.data('post-id');

        // Disable the button and show loading icon
        button.prop('disabled', true).text('Fixing with OpenAI...');

        $.ajax({
            url: sums_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'sums_openai_fix',
                post_id: post_id,
                _ajax_nonce: sums_ajax_object.nonce,
            },
            success: function (response) {
                if (response.success) {
                    alert('Fix applied successfully with OpenAI!');
                    button.closest('tr').fadeOut(500, function () {
                        $(this).remove(); // Removes the row from the table
                    });
                } else {
                    alert('OpenAI fix failed or no issues found.');
                }
                button.prop('disabled', false).text('Fix with OpenAI');
            },
            error: function () {
                alert('An error occurred. Please try again.');
                button.prop('disabled', false).text('Fix with OpenAI');
            },
        });
    });
});
