<?php
/**
 * Queue management class
 */
class Smokeiran_AI_Queue {

    /**
     * Get the queue table name
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'smokeiran_queue';
    }

    /**
     * Add an item to the queue
     */
    public static function add($post_id, $post_type, $content_type, $prompt, $priority = 0) {
        global $wpdb;
        $table_name = self::get_table_name();

        $result = $wpdb->insert(
            $table_name,
            array(
                'post_id'      => $post_id,
                'post_type'    => $post_type,
                'content_type' => $content_type,
                'prompt'       => $prompt,
                'status'       => 'pending',
                'priority'     => $priority,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%d')
        );

        return $result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Get queue items
     */
    public static function get_items($status = null, $limit = 100, $offset = 0) {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = '';
        if ($status !== null) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }

        $sql = "SELECT * FROM $table_name $where ORDER BY priority DESC, created_at ASC LIMIT %d OFFSET %d";
        return $wpdb->get_results($wpdb->prepare($sql, $limit, $offset));
    }

    /**
     * Get a single queue item
     */
    public static function get_item($id) {
        global $wpdb;
        $table_name = self::get_table_name();
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    }

    /**
     * Update queue item status
     */
    public static function update_status($id, $status, $generated_content = null, $error_message = null) {
        global $wpdb;
        $table_name = self::get_table_name();

        $data = array('status' => $status);
        $format = array('%s');

        if ($generated_content !== null) {
            $data['generated_content'] = $generated_content;
            $format[] = '%s';
        }

        if ($error_message !== null) {
            $data['error_message'] = $error_message;
            $format[] = '%s';
        }

        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }

    /**
     * Delete a queue item
     */
    public static function delete($id) {
        global $wpdb;
        $table_name = self::get_table_name();
        return $wpdb->delete($table_name, array('id' => $id), array('%d'));
    }

    /**
     * Get queue statistics
     */
    public static function get_stats() {
        global $wpdb;
        $table_name = self::get_table_name();

        $stats = array(
            'total'      => 0,
            'pending'    => 0,
            'processing' => 0,
            'completed'  => 0,
            'failed'     => 0
        );

        $results = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table_name GROUP BY status");
        
        foreach ($results as $row) {
            $stats[$row->status] = (int) $row->count;
            $stats['total'] += (int) $row->count;
        }

        return $stats;
    }

    /**
     * Clear completed items
     */
    public static function clear_completed() {
        global $wpdb;
        $table_name = self::get_table_name();
        return $wpdb->delete($table_name, array('status' => 'completed'), array('%s'));
    }

    /**
     * Clear failed items
     */
    public static function clear_failed() {
        global $wpdb;
        $table_name = self::get_table_name();
        return $wpdb->delete($table_name, array('status' => 'failed'), array('%s'));
    }

    /**
     * Get next pending item
     */
    public static function get_next_pending() {
        global $wpdb;
        $table_name = self::get_table_name();
        return $wpdb->get_row("SELECT * FROM $table_name WHERE status = 'pending' ORDER BY priority DESC, created_at ASC LIMIT 1");
    }
}
