<?php
/**
 * Settings page template
 */
if (!defined('ABSPATH')) {
    exit;
}

// Save settings if form submitted
if (isset($_POST['smokeiran_ai_save_settings'])) {
    check_admin_referer('smokeiran_ai_settings_nonce');
    
    update_option('smokeiran_ai_api_key', sanitize_text_field($_POST['smokeiran_ai_api_key']));
    update_option('smokeiran_ai_model', sanitize_text_field($_POST['smokeiran_ai_model']));
    update_option('smokeiran_ai_temperature', floatval($_POST['smokeiran_ai_temperature']));
    update_option('smokeiran_ai_max_tokens', intval($_POST['smokeiran_ai_max_tokens']));
    update_option('smokeiran_ai_queue_enabled', isset($_POST['smokeiran_ai_queue_enabled']) ? '1' : '0');
    update_option('smokeiran_ai_auto_process', isset($_POST['smokeiran_ai_auto_process']) ? '1' : '0');
    update_option('smokeiran_ai_classic_editor_mode', isset($_POST['smokeiran_ai_classic_editor_mode']) ? '1' : '0');
    update_option('smokeiran_ai_default_prompt', sanitize_textarea_field($_POST['smokeiran_ai_default_prompt']));
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'smokeiran-ai') . '</p></div>';
}

$api_key = get_option('smokeiran_ai_api_key', '');
$model = get_option('smokeiran_ai_model', 'gpt-4');
$temperature = get_option('smokeiran_ai_temperature', '0.7');
$max_tokens = get_option('smokeiran_ai_max_tokens', '2000');
$queue_enabled = get_option('smokeiran_ai_queue_enabled', '1');
$auto_process = get_option('smokeiran_ai_auto_process', '0');
$classic_editor_mode = get_option('smokeiran_ai_classic_editor_mode', '1');
$default_prompt = get_option('smokeiran_ai_default_prompt', 'Generate detailed, SEO-optimized content for the following product:');
?>

<div class="wrap smokeiran-ai-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="smokeiran-ai-header">
        <p class="description">
            <?php _e('Configure your AI content generation settings. Make sure to save your OpenAI API key before generating content.', 'smokeiran-ai'); ?>
        </p>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field('smokeiran_ai_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="smokeiran_ai_api_key"><?php _e('OpenAI API Key', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <input type="password" 
                           id="smokeiran_ai_api_key" 
                           name="smokeiran_ai_api_key" 
                           value="<?php echo esc_attr($api_key); ?>" 
                           class="regular-text" 
                           required>
                    <p class="description">
                        <?php _e('Enter your OpenAI API key. You can get one from', 'smokeiran-ai'); ?>
                        <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="smokeiran_ai_model"><?php _e('AI Model', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <select id="smokeiran_ai_model" name="smokeiran_ai_model">
                        <option value="gpt-4" <?php selected($model, 'gpt-4'); ?>>GPT-4</option>
                        <option value="gpt-4-turbo" <?php selected($model, 'gpt-4-turbo'); ?>>GPT-4 Turbo</option>
                        <option value="gpt-3.5-turbo" <?php selected($model, 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo</option>
                    </select>
                    <p class="description">
                        <?php _e('Select the OpenAI model to use for content generation. GPT-4 provides better quality but costs more.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="smokeiran_ai_temperature"><?php _e('Temperature', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <input type="number" 
                           id="smokeiran_ai_temperature" 
                           name="smokeiran_ai_temperature" 
                           value="<?php echo esc_attr($temperature); ?>" 
                           step="0.1" 
                           min="0" 
                           max="2" 
                           class="small-text">
                    <p class="description">
                        <?php _e('Controls randomness: Lower values (0.2) = more focused, Higher values (1.5) = more creative. Range: 0-2', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="smokeiran_ai_max_tokens"><?php _e('Max Tokens', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <input type="number" 
                           id="smokeiran_ai_max_tokens" 
                           name="smokeiran_ai_max_tokens" 
                           value="<?php echo esc_attr($max_tokens); ?>" 
                           min="100" 
                           max="8000" 
                           class="small-text">
                    <p class="description">
                        <?php _e('Maximum number of tokens to generate. Higher = longer content but more expensive.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="smokeiran_ai_default_prompt"><?php _e('Default Product Prompt', 'smokeiran-ai'); ?></label>
                </th>
                <td>
                    <textarea id="smokeiran_ai_default_prompt" 
                              name="smokeiran_ai_default_prompt" 
                              rows="4" 
                              class="large-text"><?php echo esc_textarea($default_prompt); ?></textarea>
                    <p class="description">
                        <?php _e('Default prompt template for product content generation. Product details will be appended automatically.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Queue Settings', 'smokeiran-ai'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   name="smokeiran_ai_queue_enabled" 
                                   value="1" 
                                   <?php checked($queue_enabled, '1'); ?>>
                            <?php _e('Enable Queue System', 'smokeiran-ai'); ?>
                        </label>
                        <p class="description">
                            <?php _e('When enabled, content generation requests can be queued for batch processing.', 'smokeiran-ai'); ?>
                        </p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" 
                                   name="smokeiran_ai_auto_process" 
                                   value="1" 
                                   <?php checked($auto_process, '1'); ?>>
                            <?php _e('Auto-process Queue', 'smokeiran-ai'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatically process queue items in the background (requires WP-Cron).', 'smokeiran-ai'); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Editor Settings', 'smokeiran-ai'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" 
                               name="smokeiran_ai_classic_editor_mode" 
                               value="1" 
                               <?php checked($classic_editor_mode, '1'); ?>>
                        <?php _e('Force HTML/Code Mode in Classic Editor', 'smokeiran-ai'); ?>
                    </label>
                    <p class="description">
                        <?php _e('When enabled, generated content will be inserted in HTML mode (Code section, not Visual). Recommended for better control.', 'smokeiran-ai'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" 
                   name="smokeiran_ai_save_settings" 
                   class="button button-primary" 
                   value="<?php esc_attr_e('Save Settings', 'smokeiran-ai'); ?>">
        </p>
    </form>

    <hr>

    <div class="smokeiran-ai-info">
        <h2><?php _e('Quick Start Guide', 'smokeiran-ai'); ?></h2>
        <ol>
            <li><?php _e('Enter your OpenAI API key above and save settings', 'smokeiran-ai'); ?></li>
            <li><?php _e('Navigate to Posts, Pages, or Products', 'smokeiran-ai'); ?></li>
            <li><?php _e('Look for the "AI Content Generator" meta box in the sidebar', 'smokeiran-ai'); ?></li>
            <li><?php _e('Click "Generate Content" to create AI-powered content', 'smokeiran-ai'); ?></li>
            <li><?php _e('Use Queue Manager to process multiple items at once', 'smokeiran-ai'); ?></li>
        </ol>

        <h3><?php _e('Shortcodes', 'smokeiran-ai'); ?></h3>
        <ul>
            <li><code>[smokeiran_content id="123"]</code> - <?php _e('Display content for specific post/product', 'smokeiran-ai'); ?></li>
            <li><code>[smokeiran_generate type="product" id="123"]</code> - <?php _e('Add generation button for post/product', 'smokeiran-ai'); ?></li>
        </ul>
    </div>
</div>
