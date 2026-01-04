<?php
/**
 * Define the internationalization functionality
 */
class Smokeiran_AI_i18n {

    /**
     * Load the plugin text domain for translation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'smokeiran-ai',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
