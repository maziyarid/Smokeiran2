# Smokeiran AI Examples

## Installation and Setup

### Step 1: Install the Plugin
1. Upload all plugin files to `/wp-content/plugins/smokeiran-ai/`
2. Activate through WordPress admin → Plugins
3. You'll see "Smokeiran AI" in the admin menu

### Step 2: Configure API Settings
```
1. Navigate to: Smokeiran AI → Settings
2. Enter your OpenAI API Key
3. Select Model: GPT-4 (best quality) or GPT-3.5-Turbo (faster/cheaper)
4. Set Temperature: 0.7 (balanced creativity)
5. Set Max Tokens: 2000 (for longer content)
6. Enable Queue System: Yes
7. Force HTML Mode: Yes (recommended)
8. Click "Save Settings"
```

## Usage Examples

### Example 1: Generate Content for a Blog Post

**Using Meta Box:**
```
1. Go to Posts → Add New or edit existing post
2. Enter post title: "10 Best WordPress Plugins for 2026"
3. Look for "AI Content Generator" meta box in sidebar
4. Optionally add custom prompt:
   "Write a comprehensive guide about the top WordPress plugins 
    with detailed explanations and use cases"
5. Click "Generate Now"
6. Content appears in editor (HTML mode)
7. Review and click "Update"
```

**Result:** Complete blog post with headings, paragraphs, and structured content in HTML format.

---

### Example 2: Generate WooCommerce Product Description

**Using Meta Box:**
```
1. Go to Products → Edit product
2. Product already has:
   - Name: "Professional Yoga Mat"
   - Price: $49.99
   - Categories: Fitness, Yoga
   - Short description: "Premium non-slip yoga mat"
3. Find "AI Content Generator" meta box
4. Leave prompt empty (uses default product template)
5. Click "Generate Now"
6. AI generates detailed product description
7. Click "Update"
```

**Generated Content Example:**
```html
<h2>Premium Quality Yoga Mat for Your Practice</h2>
<p>Experience ultimate comfort and stability with our Professional Yoga Mat...</p>
<h3>Key Features:</h3>
<ul>
  <li>Non-slip textured surface for superior grip</li>
  <li>Extra thick cushioning for joint protection</li>
  <li>Eco-friendly, non-toxic materials</li>
</ul>
```

---

### Example 3: Batch Process Multiple Products

**Using Queue Manager:**
```
1. Add multiple products to queue:
   - Go to each product page
   - Click "Add to Queue" in meta box
   - Repeat for 10-20 products

2. Process the queue:
   - Navigate to: Smokeiran AI → Queue Manager
   - See all pending items listed
   - Click "Process 5 Items" button
   - Watch progress in real-time
   - Repeat until all items processed

3. Review completed items:
   - Green "completed" badges show success
   - Red "failed" badges show errors
   - Click "Edit Post" to review content
```

---

### Example 4: Custom Prompt for Specific Content Style

**Custom Fashion Blog Prompt:**
```
Generate a trendy, conversational blog post about sustainable fashion.
Use a friendly tone, include emojis, and focus on:
- Environmental benefits
- Top sustainable brands
- Shopping tips
- Style advice
Target audience: 25-35 year old women interested in ethical fashion.
```

**Usage:**
```
1. Edit post: "Sustainable Fashion Guide 2026"
2. Open AI Content Generator meta box
3. Paste custom prompt above
4. Click "Generate Now"
5. AI generates content matching your specifications
```

---

### Example 5: Using Shortcodes

**Display Content Shortcode:**
```php
// In a page or post:
[smokeiran_content id="123"]

// In a template file:
<?php echo do_shortcode('[smokeiran_content id="123"]'); ?>
```

**Generate Button Shortcode:**
```php
// Add generate button for a product:
[smokeiran_generate type="product" id="456" button_text="Create Description"]

// With custom prompt:
[smokeiran_generate type="post" id="789" 
    button_text="Generate Article"
    prompt="Write a technical tutorial with code examples"]
```

**Frontend Example:**
```html
<!-- Add to single-product.php template -->
<?php if (current_user_can('edit_post', get_the_ID())): ?>
    <div class="admin-tools">
        <?php echo do_shortcode('[smokeiran_generate type="product" id="' . get_the_ID() . '"]'); ?>
    </div>
<?php endif; ?>
```

---

### Example 6: Auto-Processing Queue with Cron

**Setup Automatic Processing:**
```
1. Go to: Smokeiran AI → Settings
2. Enable "Queue System": ✓
3. Enable "Auto-process Queue": ✓
4. Save Settings

5. Add items throughout the day:
   - Use "Add to Queue" on posts/products
   - Items accumulate in queue

6. Automatic processing:
   - WP-Cron runs hourly
   - Processes up to 5 items per run
   - No manual intervention needed
```

**Check Processing Status:**
```
1. Go to: Smokeiran AI → Queue Manager
2. View statistics:
   - Total: 47
   - Pending: 12
   - Processing: 1
   - Completed: 32
   - Failed: 2
3. Click failed items to see error messages
4. Re-queue or delete as needed
```

---

### Example 7: Bulk Content Generation

**Generate Content for 50 Products:**
```php
// PHP script (run in WordPress environment)
// Add to functions.php temporarily or use WP-CLI

function bulk_generate_product_descriptions() {
    if (!current_user_can('manage_options')) return;
    
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => 50,
        'post_status' => 'publish',
    ));
    
    foreach ($products as $product) {
        // Check if product already has content
        if (empty($product->post_content)) {
            // Add to queue
            Smokeiran_AI_Queue::add(
                $product->ID,
                'product',
                'product_description',
                get_option('smokeiran_ai_default_prompt'),
                5 // priority
            );
        }
    }
    
    echo 'Added ' . count($products) . ' products to queue';
}

// Call function once, then remove
// bulk_generate_product_descriptions();
```

---

### Example 8: Integration with Contact Form 7

**Auto-generate content based on form submission:**
```php
// Add to functions.php

add_action('wpcf7_before_send_mail', 'smokeiran_process_content_request');

function smokeiran_process_content_request($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    
    if ($submission) {
        $data = $submission->get_posted_data();
        
        // Create a new post
        $post_id = wp_insert_post(array(
            'post_title' => $data['post_title'],
            'post_status' => 'draft',
            'post_type' => 'post',
        ));
        
        if ($post_id) {
            // Add to generation queue
            Smokeiran_AI_Queue::add(
                $post_id,
                'post',
                'general',
                $data['content_instructions'], // from form field
                10 // high priority
            );
        }
    }
}
```

---

## Troubleshooting Examples

### Issue: API Key Error
```
Error: "OpenAI API key is not configured"

Solution:
1. Go to Smokeiran AI → Settings
2. Verify API key is entered correctly
3. Test key at: https://platform.openai.com/api-keys
4. Check API key has available credits
5. Save settings again
```

### Issue: Content Not Inserting
```
Error: Content generates but doesn't appear in editor

Solution:
1. Check Classic Editor is installed
2. Switch to "Text" (HTML) tab manually
3. Enable "Force HTML Mode" in plugin settings
4. Try generating again
5. Content will appear in HTML/Code section
```

### Issue: Queue Stuck
```
Problem: Items stay in "pending" forever

Solution:
1. Go to Queue Manager
2. Click "Process Next Item" manually
3. If error appears, check:
   - API key is valid
   - Internet connection works
   - No PHP errors in debug.log
4. Clear failed items
5. Re-add to queue with lower batch size
```

---

## Advanced Customization

### Custom Prompt Templates
```php
// Add to functions.php

add_filter('smokeiran_ai_default_prompt', 'custom_product_prompt', 10, 2);

function custom_product_prompt($prompt, $product_id) {
    $product = wc_get_product($product_id);
    
    if ($product->get_type() === 'simple') {
        return 'Generate a concise, benefit-focused description 
                with clear call-to-action for: ' . $product->get_name();
    }
    
    return $prompt;
}
```

### Custom Content Processing
```php
// Modify generated content before saving

add_filter('smokeiran_ai_generated_content', 'modify_generated_content', 10, 2);

function modify_generated_content($content, $post_id) {
    // Add custom CSS classes
    $content = str_replace('<h2>', '<h2 class="ai-heading">', $content);
    
    // Add disclaimer
    $content .= '<p class="ai-disclaimer"><em>Content generated with AI assistance.</em></p>';
    
    return $content;
}
```

---

## Best Practices

### 1. Content Review
Always review AI-generated content before publishing:
- Check for accuracy
- Verify tone and style
- Add personal touches
- Optimize for SEO

### 2. API Cost Management
- Use GPT-3.5-Turbo for bulk operations
- Use GPT-4 for important content only
- Set reasonable max_tokens limits
- Monitor usage in OpenAI dashboard

### 3. Queue Management
- Process during off-peak hours
- Use priority for urgent items
- Clear completed items regularly
- Monitor failed items

### 4. Security
- Restrict access to trusted users
- Regularly update the plugin
- Don't share API keys
- Use environment variables for keys

---

## Performance Tips

1. **Batch Processing**: Process multiple items during low-traffic periods
2. **Caching**: Generated content is stored in posts - no repeated API calls
3. **Queue Priority**: Use priority system for important content
4. **Auto-Process**: Enable for hands-off operation
5. **Monitoring**: Check queue stats regularly

---

For more information, visit: https://github.com/maziyarid/Smokeiran2
