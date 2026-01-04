# Smokeiran AI Content Generator

A comprehensive WordPress plugin for AI-powered content generation with advanced queue management, WooCommerce integration, and classic editor support.

## Features

### 🚀 Core Functionality
- **AI-Powered Content Generation**: Leverage OpenAI's GPT models to generate high-quality content
- **Queue Management System**: Process content generation requests in batches
- **WooCommerce Integration**: Generate product descriptions automatically
- **Classic Editor Support**: Content inserted directly in HTML/Code mode
- **Shortcode System**: Embed AI generation capabilities anywhere
- **Custom Prompts**: Full control over content generation instructions

### 🎯 Key Improvements
- **Enhanced Queue System**: Priority-based processing with real-time status tracking
- **Improved UI/UX**: Modern, responsive admin interface with visual feedback
- **Better Error Handling**: Comprehensive logging and user-friendly error messages
- **Security Hardened**: Nonces, capability checks, and input sanitization throughout
- **Performance Optimized**: AJAX-based operations for smooth user experience

### 📝 Content Management
- Generate content for posts, pages, and products
- Batch processing capabilities
- Queue status monitoring
- Automatic HTML mode insertion for classic editor
- Real-time generation preview

## Installation

1. Upload the `smokeiran-ai-content.php` and all related files to `/wp-content/plugins/smokeiran-ai/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Smokeiran AI' → 'Settings' to configure your OpenAI API key
4. Start generating content!

## Configuration

### API Settings
1. Go to **Smokeiran AI** → **Settings**
2. Enter your OpenAI API key (get one from [OpenAI Platform](https://platform.openai.com/api-keys))
3. Select your preferred AI model (GPT-4, GPT-4 Turbo, or GPT-3.5 Turbo)
4. Adjust temperature and max tokens as needed
5. Configure queue and editor settings

### Queue Settings
- **Enable Queue System**: Allow queuing of generation requests
- **Auto-process Queue**: Automatically process pending items (requires WP-Cron)
- **Batch Processing**: Process multiple items at once

### Editor Settings
- **Force HTML Mode**: Automatically switch to HTML/Code mode in classic editor
- Ensures generated content is inserted as code, not visual content

## Usage

### Method 1: Meta Box (Recommended)
1. Edit any post, page, or product
2. Find the "AI Content Generator" meta box in the sidebar
3. Optionally enter a custom prompt
4. Click "Generate Now" or "Add to Queue"
5. Content will be inserted directly into the editor

### Method 2: Generate Content Page
1. Go to **Smokeiran AI** → **Generate Content**
2. Select content type (Post or Product)
3. Choose the item to generate content for
4. Optionally provide a custom prompt
5. Click "Generate Content"

### Method 3: Queue Manager
1. Add items to queue using meta box or generate page
2. Go to **Smokeiran AI** → **Queue Manager**
3. View queue statistics and items
4. Process items individually or in batches
5. Monitor status and manage queue items

### Method 4: Shortcodes

#### Display Generated Content
```
[smokeiran_content id="123"]
```
Displays the content of post/product with ID 123.

#### Add Generation Button
```
[smokeiran_generate type="product" id="123" button_text="Generate Content" prompt="Custom prompt here"]
```
Adds a button to generate content for the specified item.

## API Models

### GPT-4
- Highest quality output
- Best for complex content
- Higher cost per request
- Recommended for important content

### GPT-4 Turbo
- Fast and high quality
- Good balance of speed and quality
- Moderate cost
- Recommended for most use cases

### GPT-3.5 Turbo
- Fast generation
- Good for simple content
- Lower cost
- Recommended for bulk operations

## Temperature Settings

- **0.0 - 0.3**: Very focused and deterministic
- **0.4 - 0.7**: Balanced creativity (recommended)
- **0.8 - 1.5**: More creative and varied
- **1.6 - 2.0**: Highly creative (experimental)

## Queue Management

### Queue Status
- **Pending**: Waiting to be processed
- **Processing**: Currently being generated
- **Completed**: Successfully generated
- **Failed**: Generation failed (check error message)

### Queue Actions
- **Process Next Item**: Generate content for one item
- **Process 5 Items**: Batch process five items
- **Clear Completed**: Remove all completed items
- **Clear Failed**: Remove all failed items
- **Delete**: Remove individual queue item

## Security Features

- ✅ Nonce verification on all AJAX requests
- ✅ Capability checks (manage_options, edit_posts)
- ✅ Input sanitization and validation
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ CSRF protection
- ✅ API key encryption in database

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- OpenAI API key
- WooCommerce (optional, for product features)
- Classic Editor plugin (optional, for classic editor features)

## Troubleshooting

### Content Not Generating
1. Verify API key is correct
2. Check API key has sufficient credits
3. Ensure internet connectivity
4. Check WordPress error logs

### Queue Not Processing
1. Verify queue is enabled in settings
2. Check WP-Cron is functioning
3. Try manual processing in Queue Manager
4. Check for PHP errors

### Classic Editor Issues
1. Ensure Classic Editor plugin is installed
2. Enable "Force HTML Mode" in settings
3. Manually switch to HTML tab before generating

## Support

For issues, feature requests, or contributions:
- GitHub: [maziyarid/Smokeiran2](https://github.com/maziyarid/Smokeiran2)
- Create an issue on GitHub

## Changelog

### Version 2.0.0
- Complete plugin rebuild with enhanced architecture
- Advanced queue management system
- Improved UI/UX with modern design
- WooCommerce integration
- Enhanced security measures
- Better error handling and logging
- Custom prompt support
- Shortcode system
- Classic editor HTML mode support
- AJAX-based operations
- Real-time status updates
- Batch processing capabilities

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Developed by Maziyar Moradi
Powered by OpenAI GPT Models

---

**Note**: This plugin requires an OpenAI API key and will incur costs based on your usage. Please review OpenAI's pricing before extensive use.