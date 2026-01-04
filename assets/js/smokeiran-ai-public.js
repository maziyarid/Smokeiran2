/**
 * Smokeiran AI Public JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Verify smokeiranAI object is available
        if (typeof smokeiranAI === 'undefined' || !smokeiranAI.ajax_url || !smokeiranAI.nonce) {
            console.error('Smokeiran AI: Required JavaScript variables not loaded');
            return;
        }
        
        // Handle generate button click from shortcode
        $('.smokeiran-ai-generate-btn').on('click', function() {
            var $button = $(this);
            var postId = $button.data('post-id');
            var postType = $button.data('post-type');
            var prompt = $button.data('prompt');
            var $status = $button.siblings('.smokeiran-ai-generate-status');
            var originalText = $button.text();

            $button.prop('disabled', true).text('Generating...');
            $status.html('Processing your request...').addClass('show loading').removeClass('success error');

            $.ajax({
                url: smokeiranAI.ajax_url,
                type: 'POST',
                data: {
                    action: 'smokeiran_generate_content',
                    nonce: smokeiranAI.nonce,
                    post_id: postId,
                    post_type: postType,
                    custom_prompt: prompt,
                    use_queue: false
                },
                success: function(response) {
                    $button.prop('disabled', false).text(originalText);

                    if (response.success) {
                        $status.html(response.data.message)
                               .removeClass('loading error')
                               .addClass('success');
                        
                        // Reload page after 2 seconds to show updated content
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $status.html(response.data.message)
                               .removeClass('loading success')
                               .addClass('error');
                    }
                },
                error: function() {
                    $button.prop('disabled', false).text(originalText);
                    $status.html('An error occurred. Please try again.')
                           .removeClass('loading success')
                           .addClass('error');
                }
            });
        });

    });

})(jQuery);
