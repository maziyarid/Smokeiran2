<?php
/**
 * Plugin Name: Smokeiran AI Content Generator
 * Plugin URI: https://github.com/maziyarid/Smokeiran2
 * Description: Advanced AI-powered content generator for WordPress and WooCommerce products with queue management and classic editor integration
 * Version: 2.0.0
 * Author: Maziyar Moradi
 * Author URI: https://github.com/maziyarid
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smokeiran-ai
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('SMOKEIRAN_AI_VERSION', '2.0.0');
define('SMOKEIRAN_AI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMOKEIRAN_AI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SMOKEIRAN_AI_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_smokeiran_ai() {
    require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-activator.php';
    Smokeiran_AI_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smokeiran_ai() {
    require_once SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai-deactivator.php';
    Smokeiran_AI_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_smokeiran_ai');
register_deactivation_hook(__FILE__, 'deactivate_smokeiran_ai');

/**
 * The core plugin class
 */
require SMOKEIRAN_AI_PLUGIN_DIR . 'includes/class-smokeiran-ai.php';

/**
 * Begins execution of the plugin.
 */
function run_smokeiran_ai() {
    $plugin = new Smokeiran_AI();
    $plugin->run();
}
run_smokeiran_ai();
