<?php
/**
 * Fired during plugin activation
 */
class Smokeiran_AI_Activator {

    /**
     * Activate the plugin
     */
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'smokeiran_queue';
        
        // Create queue table
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            post_type varchar(50) NOT NULL DEFAULT 'post',
            content_type varchar(100) NOT NULL DEFAULT 'general',
            prompt text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            generated_content longtext,
            error_message text,
            priority int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY status (status),
            KEY priority (priority)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Set default options
        add_option('smokeiran_ai_api_key', '');
        add_option('smokeiran_ai_model', 'gpt-4');
        add_option('smokeiran_ai_temperature', '0.7');
        add_option('smokeiran_ai_max_tokens', '2000');
        add_option('smokeiran_ai_queue_enabled', '1');
        add_option('smokeiran_ai_auto_process', '0');
        add_option('smokeiran_ai_classic_editor_mode', '1');
        add_option('smokeiran_ai_default_prompt', 'Generate detailed, SEO-optimized content for the following product:');
    }
}
