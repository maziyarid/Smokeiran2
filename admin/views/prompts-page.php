<?php
/**
 * SmokeIran Robot - Prompts Management Page
 * Enhanced to support HTML prompts with visual editor
 */

if (!defined('ABSPATH')) exit;

// Handle prompt update
if (isset($_POST['sir_save_prompt']) && check_admin_referer('sir_update_prompt')) {
    $prompt_type = sanitize_text_field($_POST['prompt_type']);
    $prompt_content = wp_kses_post($_POST['prompt_content']); // Allow HTML

    if (SIR_Prompts::update_prompt($prompt_type, $prompt_content)) {
        echo '<div class="notice notice-success"><p>پرامپت با موفقیت ذخیره شد ✅</p></div>';
    }
}

// Handle reset
if (isset($_POST['sir_reset_prompt']) && check_admin_referer('sir_reset_prompt')) {
    $prompt_type = sanitize_text_field($_POST['prompt_type']);

    if (SIR_Prompts::reset_prompt($prompt_type)) {
        echo '<div class="notice notice-success"><p>پرامپت به حالت پیش‌فرض بازگردانده شد 🔄</p></div>';
    }
}

$prompts = [
    'research' => [
        'label' => '🔍 پرامپت تحقیق (Tavily)',
        'description' => 'برای جمع‌آوری اطلاعات محصول از منابع اینترنتی',
        'value' => SIR_Prompts::get_prompt('research'),
        'is_html' => false
    ],
    'content' => [
        'label' => '📦 پرامپت محتوای محصول (Claude)',
        'description' => 'برای تولید محتوای ۱۸ بخشی محصول با شورت‌کدها',
        'value' => SIR_Prompts::get_prompt('content'),
        'is_html' => SIR_Prompts::is_html_prompt('content')
    ],
    'post' => [
        'label' => '📝 پرامپت پست بلاگ (Claude)',
        'description' => 'برای تولید مقالات و پست‌های بلاگ',
        'value' => SIR_Prompts::get_prompt('post'),
        'is_html' => false
    ],
    'update' => [
        'label' => '🔄 پرامپت بهروزرسانی (Claude)',
        'description' => 'برای بهروزرسانی محتوای موجود',
        'value' => SIR_Prompts::get_prompt('update'),
        'is_html' => false
    ]
];
?>

<div class="wrap sir-wrap">
    <h1>
        <i class="fa-solid fa-file-lines"></i>
        ویرایش و سفارشی‌سازی پرامپت‌های هوش مصنوعی
    </h1>

    <div class="sir-card">
        <div class="sir-card-header">
            <h2>📋 راهنما</h2>
        </div>
        <div class="sir-card-body">
            <p>در این بخش می‌توانید پرامپت‌های مورد استفاده هوش مصنوعی را ویرایش کنید.</p>

            <h3>متغیرهای قابل استفاده:</h3>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>متغیر</th>
                        <th>توضیح</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>{product_name}</code></td>
                        <td>نام محصول وارد شده</td>
                    </tr>
                    <tr>
                        <td><code>{keywords}</code></td>
                        <td>کلیدواژه‌های هدف</td>
                    </tr>
                    <tr>
                        <td><code>{research_data}</code></td>
                        <td>داده‌های تحقیق از Tavily</td>
                    </tr>
                    <tr>
                        <td><code>{current_content}</code></td>
                        <td>محتوای فعلی (برای بهروزرسانی)</td>
                    </tr>
                </tbody>
            </table>

            <div class="sir-info-box" style="margin-top: 20px;">
                <i class="fa-solid fa-circle-info"></i>
                <strong>نکته:</strong> پرامپت محتوا (Content) به صورت HTML است و برای استفاده در Classic Editor طراحی شده. 
                شامل شورت‌کدهای پیشرفته و آیکون‌های Font Awesome 7 Pro می‌باشد.
            </div>
        </div>
    </div>

    <!-- Prompts Tabs -->
    <div class="sir-tabs">
        <div class="sir-tab-buttons">
            <?php foreach ($prompts as $key => $prompt): ?>
                <button class="sir-tab-button <?php echo $key === 'research' ? 'active' : ''; ?>" 
                        data-tab="<?php echo $key; ?>">
                    <?php echo $prompt['label']; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($prompts as $key => $prompt): ?>
            <div class="sir-tab-content <?php echo $key === 'research' ? 'active' : ''; ?>" 
                 id="tab-<?php echo $key; ?>">

                <div class="sir-card">
                    <div class="sir-card-header">
                        <h3><?php echo $prompt['label']; ?></h3>
                        <p><?php echo $prompt['description']; ?></p>
                    </div>

                    <div class="sir-card-body">
                        <form method="post" action="">
                            <?php wp_nonce_field('sir_update_prompt'); ?>
                            <input type="hidden" name="prompt_type" value="<?php echo $key; ?>">

                            <?php if ($prompt['is_html']): ?>
                                <!-- HTML Editor for Content Prompt -->
                                <div class="sir-html-editor-notice">
                                    <i class="fa-solid fa-code"></i>
                                    <strong>پرامپت HTML:</strong> این پرامپت شامل کدهای HTML، CSS و نمونه شورت‌کدها است.
                                    برای ویرایش، از حالت متنی (Text) استفاده کنید.
                                </div>

                                <div class="html-editor-buttons" style="margin-bottom: 10px;">
                                    <button type="button" class="button button-secondary" onclick="switchToTextMode('<?php echo $key; ?>')">
                                        <i class="fa-solid fa-code"></i> حالت متنی (HTML)
                                    </button>
                                    <button type="button" class="button button-secondary" onclick="previewHTML('<?php echo $key; ?>')">
                                        <i class="fa-solid fa-eye"></i> پیش‌نمایش
                                    </button>
                                </div>

                                <?php
                                $editor_id = 'prompt_content_' . $key;
                                $settings = array(
                                    'textarea_name' => 'prompt_content',
                                    'textarea_rows' => 25,
                                    'media_buttons' => false,
                                    'teeny' => false,
                                    'tinymce' => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,blockquote,link,unlink,code',
                                        'toolbar2' => '',
                                        'content_css' => SIR_PLUGIN_URL . 'assets/css/prompt-preview.css'
                                    ),
                                    'quicktags' => array(
                                        'buttons' => 'strong,em,link,block,code,ul,ol,li'
                                    )
                                );
                                wp_editor($prompt['value'], $editor_id, $settings);
                                ?>

                            <?php else: ?>
                                <!-- Plain Text Editor for other prompts -->
                                <textarea 
                                    name="prompt_content" 
                                    class="sir-prompt-editor" 
                                    rows="20"
                                    style="font-family: 'Courier New', monospace; direction: ltr; text-align: left;"
                                ><?php echo esc_textarea($prompt['value']); ?></textarea>
                            <?php endif; ?>

                            <div class="sir-form-actions" style="margin-top: 20px;">
                                <button type="submit" name="sir_save_prompt" class="button button-primary button-large">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    ذخیره پرامپت
                                </button>
                            </div>
                        </form>

                        <!-- Reset Form -->
                        <form method="post" action="" style="margin-top: 10px;" 
                              onsubmit="return confirm('آیا مطمئن هستید؟ تمام تغییرات از بین می‌رود.');">
                            <?php wp_nonce_field('sir_reset_prompt'); ?>
                            <input type="hidden" name="prompt_type" value="<?php echo $key; ?>">
                            <button type="submit" name="sir_reset_prompt" class="button button-secondary">
                                <i class="fa-solid fa-rotate-left"></i>
                                بازگردانی به حالت پیش‌فرض
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.sir-html-editor-notice {
    background: #e7f3ff;
    border-right: 4px solid #2271b1;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.sir-html-editor-notice i {
    color: #2271b1;
    margin-left: 8px;
}

.html-editor-buttons {
    display: flex;
    gap: 10px;
}

.sir-prompt-editor {
    width: 100%;
    font-size: 13px;
    line-height: 1.6;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
}

#prompt-preview-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    z-index: 999999;
    overflow: auto;
}

#prompt-preview-content {
    background: white;
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.3);
}

.preview-close {
    position: fixed;
    top: 20px;
    left: 20px;
    background: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    z-index: 1000000;
}
</style>

<script>
function switchToTextMode(promptType) {
    const editorId = 'prompt_content_' + promptType;
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get(editorId);
        if (editor) {
            // Switch to text mode
            tinymce.execCommand('mceRemoveEditor', false, editorId);
            // Focus on textarea
            setTimeout(() => {
                document.getElementById(editorId).focus();
            }, 100);
        }
    }
}

function previewHTML(promptType) {
    const editorId = 'prompt_content_' + promptType;
    let content = '';

    // Get content from editor
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get(editorId);
        if (editor) {
            content = editor.getContent();
        } else {
            content = document.getElementById(editorId).value;
        }
    } else {
        content = document.getElementById(editorId).value;
    }

    // Create modal
    let modal = document.getElementById('prompt-preview-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'prompt-preview-modal';
        modal.innerHTML = `
            <button class="preview-close" onclick="closePreview()">
                <i class="fa-solid fa-xmark"></i> بستن
            </button>
            <div id="prompt-preview-content"></div>
        `;
        document.body.appendChild(modal);
    }

    // Set content and show
    document.getElementById('prompt-preview-content').innerHTML = content;
    modal.style.display = 'block';
}

function closePreview() {
    document.getElementById('prompt-preview-modal').style.display = 'none';
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreview();
    }
});
</script>
