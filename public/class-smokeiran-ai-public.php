<?php
/**
 * The public-facing functionality of the plugin
 */
class Smokeiran_AI_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Enqueue public styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            SMOKEIRAN_AI_PLUGIN_URL . 'assets/css/smokeiran-ai-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue public scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            SMOKEIRAN_AI_PLUGIN_URL . 'assets/js/smokeiran-ai-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Shortcode to display generated content
     * Usage: [smokeiran_content id="123"]
     */
    public function content_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'type' => 'post',
        ), $atts);

        $post_id = intval($atts['id']);
        
        if ($post_id === 0) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return '<p class="smokeiran-ai-error">' . __('Invalid post ID', 'smokeiran-ai') . '</p>';
        }

        $post = get_post($post_id);
        
        if (!$post) {
            return '<p class="smokeiran-ai-error">' . __('Post not found', 'smokeiran-ai') . '</p>';
        }

        $content = apply_filters('the_content', $post->post_content);
        
        return '<div class="smokeiran-ai-content">' . $content . '</div>';
    }

    /**
     * Shortcode to trigger content generation
     * Usage: [smokeiran_generate type="product" id="123" button_text="Generate Content"]
     */
    public function generate_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'type' => 'post',
            'button_text' => __('Generate AI Content', 'smokeiran-ai'),
            'prompt' => '',
        ), $atts);

        $post_id = intval($atts['id']);
        
        if ($post_id === 0) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return '<p class="smokeiran-ai-error">' . __('Invalid post ID', 'smokeiran-ai') . '</p>';
        }

        // Check if user has permission
        if (!current_user_can('edit_post', $post_id)) {
            return '<p class="smokeiran-ai-error">' . __('You do not have permission to generate content', 'smokeiran-ai') . '</p>';
        }

        ob_start();
        ?>
        <div class="smokeiran-ai-generate-wrapper">
            <button class="smokeiran-ai-generate-btn" 
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-post-type="<?php echo esc_attr($atts['type']); ?>"
                    data-prompt="<?php echo esc_attr($atts['prompt']); ?>">
                <?php echo esc_html($atts['button_text']); ?>
            </button>
            <div class="smokeiran-ai-generate-status"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
