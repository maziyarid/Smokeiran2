<?php
/**
 * Fired during plugin deactivation
 */
class Smokeiran_AI_Deactivator {

    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear any scheduled cron jobs
        wp_clear_scheduled_hook('smokeiran_ai_process_queue');
    }
}
