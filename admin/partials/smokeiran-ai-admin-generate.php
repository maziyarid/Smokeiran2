<?php
/**
 * Generate content page template
 */
if (!defined('ABSPATH')) {
    exit;
}

// Get recent posts and products
$posts = get_posts(array(
    'numberposts' => 20,
    'post_status' => 'any',
    'post_type' => 'post'
));

$products = array();
if (class_exists('WooCommerce')) {
    $products = get_posts(array(
        'numberposts' => 20,
        'post_status' => 'any',
        'post_type' => 'product'
    ));
}
?>

<div class="wrap smokeiran-ai-generate">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="smokeiran-generate-form">
        <h2><?php _e('Generate AI Content', 'smokeiran-ai'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="content_type"><?php _e('Content Type', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <select id="content_type" name="content_type">
                        <option value="post"><?php _e('Blog Post', 'smokeiran-ai'); ?></option>
                        <?php if (class_exists('WooCommerce')) : ?>
                            <option value="product"><?php _e('Product', 'smokeiran-ai'); ?></option>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>

            <tr id="post_select_row">
                <th scope="row">
                    <label for="post_id"><?php _e('Select Post', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <select id="post_id" name="post_id">
                        <option value=""><?php _e('-- Select Post --', 'smokeiran-ai'); ?></option>
                        <?php foreach ($posts as $post) : ?>
                            <option value="<?php echo esc_attr($post->ID); ?>">
                                <?php echo esc_html($post->post_title . ' (ID: ' . $post->ID . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <?php if (class_exists('WooCommerce') && !empty($products)) : ?>
            <tr id="product_select_row" style="display: none;">
                <th scope="row">
                    <label for="product_id"><?php _e('Select Product', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <select id="product_id" name="product_id">
                        <option value=""><?php _e('-- Select Product --', 'smokeiran-ai'); ?></option>
                        <?php foreach ($products as $product) : ?>
                            <option value="<?php echo esc_attr($product->ID); ?>">
                                <?php echo esc_html($product->post_title . ' (ID: ' . $product->ID . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php endif; ?>

            <tr>
                <th scope="row">
                    <label for="custom_prompt"><?php _e('Custom Prompt (Optional)', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <textarea id="custom_prompt" 
                              name="custom_prompt" 
                              rows="5" 
                              class="large-text" 
                              placeholder="<?php esc_attr_e('Enter a custom prompt or leave empty to use default...', 'smokeiran-ai'); ?>"></textarea>
                    <p class="description">
                        <?php _e('Provide specific instructions for content generation. If empty, default prompt will be used.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="use_queue"><?php _e('Processing Method', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="radio" name="processing_method" value="immediate" checked>
                        <?php _e('Generate Immediately', 'smokeiran-ai'); ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="processing_method" value="queue">
                        <?php _e('Add to Queue', 'smokeiran-ai'); ?>
                    </label>
                    <p class="description">
                        <?php _e('Choose whether to generate content immediately or add to queue for later processing.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="smokeiran-generate-btn" class="button button-primary">
                <?php _e('Generate Content', 'smokeiran-ai'); ?>
            </button>
        </p>

        <div id="smokeiran-generate-result" class="smokeiran-message"></div>
        <div id="smokeiran-generated-content" class="smokeiran-content-preview" style="display: none;">
            <h3><?php _e('Generated Content Preview', 'smokeiran-ai'); ?></h3>
            <div class="content-preview-box"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#content_type').on('change', function() {
        if ($(this).val() === 'product') {
            $('#post_select_row').hide();
            $('#product_select_row').show();
        } else {
            $('#post_select_row').show();
            $('#product_select_row').hide();
        }
    });

    $('#smokeiran-generate-btn').on('click', function() {
        var contentType = $('#content_type').val();
        var postId = contentType === 'product' ? $('#product_id').val() : $('#post_id').val();
        var customPrompt = $('#custom_prompt').val();
        var useQueue = $('input[name="processing_method"]:checked').val() === 'queue';

        if (!postId) {
            alert('<?php esc_js(_e('Please select a post or product', 'smokeiran-ai')); ?>');
            return;
        }

        var $button = $(this);
        var originalText = $button.text();
        $button.prop('disabled', true).text('<?php esc_js(_e('Generating...', 'smokeiran-ai')); ?>');
        $('#smokeiran-generate-result').html('').removeClass('error success');

        $.ajax({
            url: smokeiranAI.ajax_url,
            type: 'POST',
            data: {
                action: 'smokeiran_generate_content',
                nonce: smokeiranAI.nonce,
                post_id: postId,
                post_type: contentType,
                custom_prompt: customPrompt,
                use_queue: useQueue
            },
            success: function(response) {
                $button.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $('#smokeiran-generate-result')
                        .html('<p>' + response.data.message + '</p>')
                        .addClass('success notice notice-success');
                    
                    if (response.data.content) {
                        $('#smokeiran-generated-content .content-preview-box').html(response.data.content);
                        $('#smokeiran-generated-content').show();
                    }
                } else {
                    $('#smokeiran-generate-result')
                        .html('<p>' + response.data.message + '</p>')
                        .addClass('error notice notice-error');
                }
            },
            error: function() {
                $button.prop('disabled', false).text(originalText);
                $('#smokeiran-generate-result')
                    .html('<p><?php esc_js(_e('An error occurred. Please try again.', 'smokeiran-ai')); ?></p>')
                    .addClass('error notice notice-error');
            }
        });
    });
});
</script>
