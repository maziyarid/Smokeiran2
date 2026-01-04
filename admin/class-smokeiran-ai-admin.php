<?php
/**
 * The admin-specific functionality of the plugin
 */
class Smokeiran_AI_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            SMOKEIRAN_AI_PLUGIN_URL . 'assets/css/smokeiran-ai-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            SMOKEIRAN_AI_PLUGIN_URL . 'assets/js/smokeiran-ai-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script(
            $this->plugin_name,
            'smokeiranAI',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('smokeiran_ai_nonce'),
                'strings' => array(
                    'generating' => __('Generating content...', 'smokeiran-ai'),
                    'success' => __('Content generated successfully!', 'smokeiran-ai'),
                    'error' => __('Error generating content', 'smokeiran-ai'),
                    'confirm_delete' => __('Are you sure you want to delete this item?', 'smokeiran-ai'),
                )
            )
        );
    }

    /**
     * Add plugin admin menu
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('Smokeiran AI', 'smokeiran-ai'),
            __('Smokeiran AI', 'smokeiran-ai'),
            'manage_options',
            'smokeiran-ai',
            array($this, 'display_settings_page'),
            'dashicons-edit',
            30
        );

        add_submenu_page(
            'smokeiran-ai',
            __('Settings', 'smokeiran-ai'),
            __('Settings', 'smokeiran-ai'),
            'manage_options',
            'smokeiran-ai',
            array($this, 'display_settings_page')
        );

        add_submenu_page(
            'smokeiran-ai',
            __('Queue Manager', 'smokeiran-ai'),
            __('Queue Manager', 'smokeiran-ai'),
            'manage_options',
            'smokeiran-ai-queue',
            array($this, 'display_queue_page')
        );

        add_submenu_page(
            'smokeiran-ai',
            __('Generate Content', 'smokeiran-ai'),
            __('Generate Content', 'smokeiran-ai'),
            'edit_posts',
            'smokeiran-ai-generate',
            array($this, 'display_generate_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_api_key');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_model');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_temperature');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_max_tokens');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_queue_enabled');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_auto_process');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_classic_editor_mode');
        register_setting('smokeiran_ai_settings', 'smokeiran_ai_default_prompt');
    }

    /**
     * Display settings page
     */
    public function display_settings_page() {
        include_once SMOKEIRAN_AI_PLUGIN_DIR . 'admin/partials/smokeiran-ai-admin-settings.php';
    }

    /**
     * Display queue page
     */
    public function display_queue_page() {
        include_once SMOKEIRAN_AI_PLUGIN_DIR . 'admin/partials/smokeiran-ai-admin-queue.php';
    }

    /**
     * Display generate page
     */
    public function display_generate_page() {
        include_once SMOKEIRAN_AI_PLUGIN_DIR . 'admin/partials/smokeiran-ai-admin-generate.php';
    }

    /**
     * Add content generator meta box
     */
    public function add_content_generator_meta_box() {
        $post_types = array('post', 'page');
        
        // Add product post type if WooCommerce is active
        if (class_exists('WooCommerce')) {
            $post_types[] = 'product';
        }

        foreach ($post_types as $post_type) {
            add_meta_box(
                'smokeiran_ai_generator',
                __('AI Content Generator', 'smokeiran-ai'),
                array($this, 'render_meta_box'),
                $post_type,
                'side',
                'high'
            );
        }
    }

    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('smokeiran_ai_meta_box', 'smokeiran_ai_meta_box_nonce');
        include_once SMOKEIRAN_AI_PLUGIN_DIR . 'admin/partials/smokeiran-ai-meta-box.php';
    }

    /**
     * Force HTML editor mode for classic editor
     */
    public function force_html_editor($default) {
        if (get_option('smokeiran_ai_classic_editor_mode', '1') === '1') {
            // Return false to disable visual editor
            add_filter('wp_default_editor', array($this, 'default_to_html_editor'));
        }
        return $default;
    }

    /**
     * Set default editor to HTML mode
     */
    public function default_to_html_editor() {
        return 'html';
    }

    /**
     * AJAX: Generate content
     */
    public function ajax_generate_content() {
        check_ajax_referer('smokeiran_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'smokeiran-ai')));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
        $custom_prompt = isset($_POST['custom_prompt']) ? sanitize_textarea_field($_POST['custom_prompt']) : '';
        $use_queue = isset($_POST['use_queue']) ? (bool) $_POST['use_queue'] : false;

        if ($use_queue && get_option('smokeiran_ai_queue_enabled', '1') === '1') {
            // Add to queue
            $prompt = !empty($custom_prompt) ? $custom_prompt : '';
            if (empty($prompt)) {
                if ($post_type === 'product') {
                    $prompt = get_option('smokeiran_ai_default_prompt', 'Generate detailed, SEO-optimized content for the following product:');
                } else {
                    $prompt = 'Generate SEO-optimized content for this ' . $post_type;
                }
            }

            $queue_id = Smokeiran_AI_Queue::add($post_id, $post_type, 'general', $prompt, 5);
            
            if ($queue_id) {
                wp_send_json_success(array(
                    'message' => __('Added to queue successfully', 'smokeiran-ai'),
                    'queue_id' => $queue_id
                ));
            } else {
                wp_send_json_error(array('message' => __('Failed to add to queue', 'smokeiran-ai')));
            }
        } else {
            // Generate immediately
            if ($post_type === 'product' && class_exists('WooCommerce')) {
                $result = Smokeiran_AI_Generator::generate_product_content($post_id);
            } else {
                $result = Smokeiran_AI_Generator::generate_post_content($post_id, $custom_prompt);
            }

            if ($result['success']) {
                // Update post content in HTML mode
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_content' => $result['content']
                ));

                wp_send_json_success(array(
                    'message' => __('Content generated and updated successfully', 'smokeiran-ai'),
                    'content' => $result['content']
                ));
            } else {
                wp_send_json_error(array('message' => $result['error']));
            }
        }
    }

    /**
     * AJAX: Process queue
     */
    public function ajax_process_queue() {
        check_ajax_referer('smokeiran_ai_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'smokeiran-ai')));
        }

        $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 1;
        $processed = 0;
        $errors = array();

        for ($i = 0; $i < $batch_size; $i++) {
            $item = Smokeiran_AI_Queue::get_next_pending();
            
            if (!$item) {
                break;
            }

            Smokeiran_AI_Queue::update_status($item->id, 'processing');

            if ($item->post_type === 'product' && class_exists('WooCommerce')) {
                $result = Smokeiran_AI_Generator::generate_product_content($item->post_id);
            } else {
                $result = Smokeiran_AI_Generator::generate_post_content($item->post_id, $item->prompt);
            }

            if ($result['success']) {
                // Update post content
                wp_update_post(array(
                    'ID' => $item->post_id,
                    'post_content' => $result['content']
                ));

                Smokeiran_AI_Queue::update_status($item->id, 'completed', $result['content']);
                $processed++;
            } else {
                Smokeiran_AI_Queue::update_status($item->id, 'failed', null, $result['error']);
                $errors[] = $result['error'];
            }
        }

        $stats = Smokeiran_AI_Queue::get_stats();

        wp_send_json_success(array(
            'processed' => $processed,
            'errors' => $errors,
            'stats' => $stats
        ));
    }

    /**
     * AJAX: Delete queue item
     */
    public function ajax_delete_queue_item() {
        check_ajax_referer('smokeiran_ai_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'smokeiran-ai')));
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (Smokeiran_AI_Queue::delete($id)) {
            wp_send_json_success(array('message' => __('Item deleted successfully', 'smokeiran-ai')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete item', 'smokeiran-ai')));
        }
    }

    /**
     * AJAX: Get queue status
     */
    public function ajax_get_queue_status() {
        check_ajax_referer('smokeiran_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'smokeiran-ai')));
        }

        $stats = Smokeiran_AI_Queue::get_stats();
        wp_send_json_success(array('stats' => $stats));
    }
}
