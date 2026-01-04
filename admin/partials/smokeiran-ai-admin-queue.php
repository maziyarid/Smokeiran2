<?php
/**
 * Queue manager page template
 */
if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk actions
if (isset($_POST['smokeiran_queue_action']) && check_admin_referer('smokeiran_queue_nonce')) {
    $action = sanitize_text_field($_POST['smokeiran_queue_action']);
    
    switch ($action) {
        case 'clear_completed':
            Smokeiran_AI_Queue::clear_completed();
            echo '<div class="notice notice-success"><p>' . __('Completed items cleared successfully!', 'smokeiran-ai') . '</p></div>';
            break;
        case 'clear_failed':
            Smokeiran_AI_Queue::clear_failed();
            echo '<div class="notice notice-success"><p>' . __('Failed items cleared successfully!', 'smokeiran-ai') . '</p></div>';
            break;
    }
}

$stats = Smokeiran_AI_Queue::get_stats();
$items = Smokeiran_AI_Queue::get_items(null, 50);
?>

<div class="wrap smokeiran-ai-queue">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="smokeiran-queue-stats">
        <div class="stat-box">
            <span class="stat-label"><?php _e('Total', 'smokeiran-ai'); ?></span>
            <span class="stat-value"><?php echo esc_html($stats['total']); ?></span>
        </div>
        <div class="stat-box stat-pending">
            <span class="stat-label"><?php _e('Pending', 'smokeiran-ai'); ?></span>
            <span class="stat-value"><?php echo esc_html($stats['pending']); ?></span>
        </div>
        <div class="stat-box stat-processing">
            <span class="stat-label"><?php _e('Processing', 'smokeiran-ai'); ?></span>
            <span class="stat-value"><?php echo esc_html($stats['processing']); ?></span>
        </div>
        <div class="stat-box stat-completed">
            <span class="stat-label"><?php _e('Completed', 'smokeiran-ai'); ?></span>
            <span class="stat-value"><?php echo esc_html($stats['completed']); ?></span>
        </div>
        <div class="stat-box stat-failed">
            <span class="stat-label"><?php _e('Failed', 'smokeiran-ai'); ?></span>
            <span class="stat-value"><?php echo esc_html($stats['failed']); ?></span>
        </div>
    </div>

    <div class="smokeiran-queue-actions">
        <button id="smokeiran-process-queue" class="button button-primary">
            <?php _e('Process Next Item', 'smokeiran-ai'); ?>
        </button>
        <button id="smokeiran-process-batch" class="button button-secondary" data-batch="5">
            <?php _e('Process 5 Items', 'smokeiran-ai'); ?>
        </button>
        <button id="smokeiran-refresh-queue" class="button">
            <?php _e('Refresh', 'smokeiran-ai'); ?>
        </button>
        
        <form method="post" style="display: inline-block; margin-left: 20px;">
            <?php wp_nonce_field('smokeiran_queue_nonce'); ?>
            <input type="hidden" name="smokeiran_queue_action" value="clear_completed">
            <button type="submit" class="button" onclick="return confirm('<?php esc_attr_e('Clear all completed items?', 'smokeiran-ai'); ?>')">
                <?php _e('Clear Completed', 'smokeiran-ai'); ?>
            </button>
        </form>
        
        <form method="post" style="display: inline-block;">
            <?php wp_nonce_field('smokeiran_queue_nonce'); ?>
            <input type="hidden" name="smokeiran_queue_action" value="clear_failed">
            <button type="submit" class="button" onclick="return confirm('<?php esc_attr_e('Clear all failed items?', 'smokeiran-ai'); ?>')">
                <?php _e('Clear Failed', 'smokeiran-ai'); ?>
            </button>
        </form>
    </div>

    <div id="smokeiran-queue-message" class="smokeiran-message"></div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th class="column-id"><?php _e('ID', 'smokeiran-ai'); ?></th>
                <th class="column-post"><?php _e('Post/Product', 'smokeiran-ai'); ?></th>
                <th class="column-type"><?php _e('Type', 'smokeiran-ai'); ?></th>
                <th class="column-status"><?php _e('Status', 'smokeiran-ai'); ?></th>
                <th class="column-priority"><?php _e('Priority', 'smokeiran-ai'); ?></th>
                <th class="column-created"><?php _e('Created', 'smokeiran-ai'); ?></th>
                <th class="column-actions"><?php _e('Actions', 'smokeiran-ai'); ?></th>
            </tr>
        </thead>
        <tbody id="smokeiran-queue-items">
            <?php if (empty($items)) : ?>
                <tr>
                    <td colspan="7" class="no-items"><?php _e('No items in queue', 'smokeiran-ai'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($items as $item) : 
                    $post = get_post($item->post_id);
                    $post_title = $post ? $post->post_title : __('Unknown', 'smokeiran-ai');
                    $status_class = 'status-' . $item->status;
                ?>
                    <tr data-item-id="<?php echo esc_attr($item->id); ?>">
                        <td><?php echo esc_html($item->id); ?></td>
                        <td>
                            <strong><?php echo esc_html($post_title); ?></strong>
                            <br>
                            <small><?php echo esc_html($item->prompt); ?></small>
                            <?php if ($item->error_message) : ?>
                                <br>
                                <span class="error-message"><?php echo esc_html($item->error_message); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($item->post_type); ?></td>
                        <td><span class="status-badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($item->status); ?></span></td>
                        <td><?php echo esc_html($item->priority); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item->created_at))); ?></td>
                        <td>
                            <button class="button button-small smokeiran-delete-item" data-item-id="<?php echo esc_attr($item->id); ?>">
                                <?php _e('Delete', 'smokeiran-ai'); ?>
                            </button>
                            <?php if ($post) : ?>
                                <a href="<?php echo esc_url(get_edit_post_link($item->post_id)); ?>" class="button button-small">
                                    <?php _e('Edit Post', 'smokeiran-ai'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
