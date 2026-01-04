<?php
/**
 * Meta box template for posts/products
 */
if (!defined('ABSPATH')) {
    exit;
}

$post_id = $post->ID;
?>

<div class="smokeiran-ai-meta-box">
    <p class="description">
        <?php _e('Generate AI-powered content for this post/product.', 'smokeiran-ai'); ?>
    </p>
    
    <div class="smokeiran-meta-field">
        <label for="smokeiran_custom_prompt">
            <strong><?php _e('Custom Prompt (Optional)', 'smokeiran-ai'); ?></strong>
        </label>
        <textarea id="smokeiran_custom_prompt" 
                  name="smokeiran_custom_prompt" 
                  rows="3" 
                  style="width: 100%;"
                  placeholder="<?php esc_attr_e('Enter custom instructions for AI...', 'smokeiran-ai'); ?>"></textarea>
    </div>

    <div class="smokeiran-meta-actions">
        <button type="button" 
                class="button button-primary smokeiran-generate-now" 
                data-post-id="<?php echo esc_attr($post_id); ?>"
                data-post-type="<?php echo esc_attr($post->post_type); ?>">
            <?php _e('Generate Now', 'smokeiran-ai'); ?>
        </button>
        
        <?php if (get_option('smokeiran_ai_queue_enabled', '1') === '1') : ?>
            <button type="button" 
                    class="button smokeiran-add-to-queue" 
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-post-type="<?php echo esc_attr($post->post_type); ?>">
                <?php _e('Add to Queue', 'smokeiran-ai'); ?>
            </button>
        <?php endif; ?>
    </div>

    <div class="smokeiran-meta-message"></div>
    
    <div class="smokeiran-meta-info">
        <p class="description">
            <strong><?php _e('Note:', 'smokeiran-ai'); ?></strong>
            <?php _e('Generated content will be inserted in HTML mode (Code section). Make sure to review and save after generation.', 'smokeiran-ai'); ?>
        </p>
    </div>
</div>

<style>
.smokeiran-ai-meta-box {
    padding: 5px 0;
}
.smokeiran-meta-field {
    margin-bottom: 15px;
}
.smokeiran-meta-actions {
    margin-bottom: 15px;
}
.smokeiran-meta-actions .button {
    margin-right: 10px;
}
.smokeiran-meta-message {
    margin: 10px 0;
}
.smokeiran-meta-message.success {
    padding: 10px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    border-radius: 4px;
}
.smokeiran-meta-message.error {
    padding: 10px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    border-radius: 4px;
}
.smokeiran-meta-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.smokeiran-generate-now, .smokeiran-add-to-queue').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var postId = $button.data('post-id');
        var postType = $button.data('post-type');
        var customPrompt = $('#smokeiran_custom_prompt').val();
        var useQueue = $button.hasClass('smokeiran-add-to-queue');
        var $message = $('.smokeiran-meta-message');
        var originalText = $button.text();

        $button.prop('disabled', true).text('<?php esc_js(_e('Processing...', 'smokeiran-ai')); ?>');
        $message.html('').removeClass('success error');

        $.ajax({
            url: smokeiranAI.ajax_url,
            type: 'POST',
            data: {
                action: 'smokeiran_generate_content',
                nonce: smokeiranAI.nonce,
                post_id: postId,
                post_type: postType,
                custom_prompt: customPrompt,
                use_queue: useQueue
            },
            success: function(response) {
                $button.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $message.html(response.data.message).addClass('success');
                    
                    if (response.data.content) {
                        // Update editor content
                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                            tinymce.get('content').setContent(response.data.content);
                        }
                        if ($('#content').length) {
                            $('#content').val(response.data.content);
                        }
                        
                        // Switch to HTML mode if classic editor
                        if (typeof switchEditors !== 'undefined') {
                            switchEditors.go('content', 'html');
                        }
                        
                        $message.html($message.html() + '<br><?php esc_js(_e('Content has been inserted. Please review and save.', 'smokeiran-ai')); ?>');
                    }
                } else {
                    $message.html(response.data.message).addClass('error');
                }
            },
            error: function() {
                $button.prop('disabled', false).text(originalText);
                $message.html('<?php esc_js(_e('An error occurred. Please try again.', 'smokeiran-ai')); ?>').addClass('error');
            }
        });
    });
});
</script>
