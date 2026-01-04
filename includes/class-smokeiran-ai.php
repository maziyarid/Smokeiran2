<?php
/**
 * The core plugin class
 */
class Smokeiran_AI {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     */
    protected $loader;

    /**
     * The unique identifier of this plugin
     */
    protected $plugin_name;

    /**
     * The current version of the plugin
     */
    protected $version;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        $this->version = SMOKEIRAN_AI_VERSION;
        $this->plugin_name = 'smokeiran-ai';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies
     */
    private function load_dependencies() {
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-loader.php';
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-i18n.php';
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-queue.php';
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-generator.php';
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'admin/class-smokeiran-ai-admin.php';
        require_once SMOKEIRAN_AI_PLUGIN_DIR . 'public/class-smokeiran-ai-public.php';

        $this->loader = new Smokeiran_AI_Loader();
    }

    /**
     * Define the locale for internationalization
     */
    private function set_locale() {
        $plugin_i18n = new Smokeiran_AI_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all admin-specific hooks
     */
    private function define_admin_hooks() {
        $plugin_admin = new Smokeiran_AI_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        
        // AJAX hooks
        $this->loader->add_action('wp_ajax_smokeiran_generate_content', $plugin_admin, 'ajax_generate_content');
        $this->loader->add_action('wp_ajax_smokeiran_process_queue', $plugin_admin, 'ajax_process_queue');
        $this->loader->add_action('wp_ajax_smokeiran_delete_queue_item', $plugin_admin, 'ajax_delete_queue_item');
        $this->loader->add_action('wp_ajax_smokeiran_get_queue_status', $plugin_admin, 'ajax_get_queue_status');
        
        // Add meta box for products and posts
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_content_generator_meta_box');
        
        // Classic editor integration
        $this->loader->add_filter('user_can_richedit', $plugin_admin, 'force_html_editor', 10, 1);
    }

    /**
     * Register all public-facing hooks
     */
    private function define_public_hooks() {
        $plugin_public = new Smokeiran_AI_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Register shortcodes
        $this->loader->add_shortcode('smokeiran_content', $plugin_public, 'content_shortcode');
        $this->loader->add_shortcode('smokeiran_generate', $plugin_public, 'generate_shortcode');
    }

    /**
     * Run the loader to execute all hooks
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Get plugin name
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Get plugin version
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get the loader
     */
    public function get_loader() {
        return $this->loader;
    }
}
