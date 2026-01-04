<?php
/**
 * AI Content Generator class
 */
class Smokeiran_AI_Generator {

    /**
     * Generate content using OpenAI API
     */
    public static function generate($prompt, $options = array()) {
        $api_key = get_option('smokeiran_ai_api_key');
        
        if (empty($api_key)) {
            return array(
                'success' => false,
                'error' => __('OpenAI API key is not configured. Please configure it in settings.', 'smokeiran-ai')
            );
        }

        $defaults = array(
            'model' => get_option('smokeiran_ai_model', 'gpt-4'),
            'temperature' => floatval(get_option('smokeiran_ai_temperature', 0.7)),
            'max_tokens' => intval(get_option('smokeiran_ai_max_tokens', 2000)),
        );

        $options = wp_parse_args($options, $defaults);

        $api_url = 'https://api.openai.com/v1/chat/completions';
        
        $body = array(
            'model' => $options['model'],
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a professional content writer. Generate high-quality, SEO-optimized content in HTML format suitable for WordPress. Return only the HTML content without any markdown formatting or code blocks.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => $options['temperature'],
            'max_tokens' => $options['max_tokens'],
        );

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body' => wp_json_encode($body),
            'timeout' => 60,
        );

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if ($response_code !== 200) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown API error';
            return array(
                'success' => false,
                'error' => $error_message
            );
        }

        if (!isset($data['choices'][0]['message']['content'])) {
            return array(
                'success' => false,
                'error' => __('Invalid API response format', 'smokeiran-ai')
            );
        }

        $content = $data['choices'][0]['message']['content'];
        
        // Clean up the content - remove markdown code blocks if present
        $content = preg_replace('/```html\s*/i', '', $content);
        $content = preg_replace('/```\s*$/i', '', $content);
        $content = trim($content);

        return array(
            'success' => true,
            'content' => $content,
            'usage' => isset($data['usage']) ? $data['usage'] : null
        );
    }

    /**
     * Generate product content
     */
    public static function generate_product_content($product_id) {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return array(
                'success' => false,
                'error' => __('Product not found', 'smokeiran-ai')
            );
        }

        $product_name = $product->get_name();
        $product_price = $product->get_price();
        $short_description = $product->get_short_description();
        $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
        
        $default_prompt = get_option('smokeiran_ai_default_prompt', 'Generate detailed, SEO-optimized content for the following product:');
        
        $prompt = $default_prompt . "\n\n";
        $prompt .= "Product Name: " . $product_name . "\n";
        $prompt .= "Price: " . $product_price . "\n";
        
        if (!empty($categories)) {
            $prompt .= "Categories: " . implode(', ', $categories) . "\n";
        }
        
        if (!empty($short_description)) {
            $prompt .= "Short Description: " . $short_description . "\n";
        }
        
        $prompt .= "\nPlease create engaging product description with features, benefits, and call-to-action in HTML format.";

        return self::generate($prompt);
    }

    /**
     * Generate post content
     */
    public static function generate_post_content($post_id, $custom_prompt = '') {
        $post = get_post($post_id);
        
        if (!$post) {
            return array(
                'success' => false,
                'error' => __('Post not found', 'smokeiran-ai')
            );
        }

        $post_title = $post->post_title;
        
        if (empty($custom_prompt)) {
            $prompt = "Write a comprehensive, SEO-optimized blog post about: " . $post_title;
            $prompt .= "\n\nCreate engaging content with proper HTML formatting including headings, paragraphs, and lists.";
        } else {
            $prompt = $custom_prompt;
            $prompt .= "\n\nPost Title: " . $post_title;
        }

        return self::generate($prompt);
    }
}
