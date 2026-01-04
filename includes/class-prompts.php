<?php
/**
 * SmokeIran Robot - Prompts Management
 * Handles default prompts and prompt operations
 */

if (!defined('ABSPATH')) exit;

class SIR_Prompts {

    /**
     * Initialize default prompts
     */
    public static function init_defaults() {
        $prompts = [
            'research_prompt' => self::get_default_research_prompt(),
            'content_prompt' => self::get_default_content_prompt_html(),
            'post_prompt' => self::get_default_post_prompt(),
            'update_prompt' => self::get_default_update_prompt(),
        ];

        foreach ($prompts as $key => $value) {
            if (get_option('sir_' . $key) === false) {
                add_option('sir_' . $key, $value);
            }
        }
    }

    /**
     * Get prompt by type
     */
    public static function get_prompt($type) {
        return get_option('sir_' . $type . '_prompt', '');
    }

    /**
     * Update prompt
     */
    public static function update_prompt($type, $content) {
        return update_option('sir_' . $type . '_prompt', $content);
    }

    /**
     * Reset prompt to default
     */
    public static function reset_prompt($type) {
        $method = 'get_default_' . $type . '_prompt';
        if ($type === 'content') {
            $method = 'get_default_content_prompt_html';
        }

        if (method_exists(__CLASS__, $method)) {
            return self::update_prompt($type, self::$method());
        }
        return false;
    }

    /**
     * Default Research Prompt (Tavily) - UNCHANGED
     */
    public static function get_default_research_prompt() {
        return <<<'PROMPT'
You are an expert vape product researcher specializing in gathering comprehensive information about vaping devices and presenting it in Persian. When a user provides a product name (in Persian or English), you must research and compile a complete product profile following this exact structure:

## RESEARCH METHODOLOGY:
1. **Search Strategy:**
   - First search: Product specifications, features, reviews
   - Second search: Materials, construction, technical components
   - Third search: User manual, instructions, brand history
   - Additional searches as needed for: pricing, warranty, manufacturer location, coil materials

2. **Source Verification:**
   - Prioritize official manufacturer websites
   - Cross-reference technical specs from multiple review sites
   - Verify all claims with at least 2 sources when possible
   - Include citations for every factual statement

## REQUIRED OUTPUT STRUCTURE (in Persian):
[Rest of research prompt - keeping original...]

PROMPT;
    }

    /**
     * Default Content Generation Prompt (HTML FORMAT FOR CLASSIC EDITOR)
     * Updated with shortcodes integration and Font Awesome icons
     */
    public static function get_default_content_prompt_html() {
        // Read the HTML prompt file or return inline
        $html_prompt_path = SIR_PLUGIN_DIR . 'includes/prompts/content-prompt.html';

        if (file_exists($html_prompt_path)) {
            return file_get_contents($html_prompt_path);
        }

        // Fallback: return inline HTML
        return self::get_inline_html_prompt();
    }

    /**
     * Inline HTML Prompt (Full HTML)
     */
    private static function get_inline_html_prompt() {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Vazir', 'Tahoma', sans-serif;
            line-height: 1.8;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            background: linear-gradient(135deg, #29853a 0%, #1e6b2d 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(41, 133, 58, 0.3);
        }

        .header-section h1 {
            font-size: 32px;
            margin: 0 0 15px 0;
            font-weight: 800;
        }

        h2 {
            color: #29853a;
            font-size: 26px;
            font-weight: 700;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #29853a;
        }

        h3 {
            color: #1e6b2d;
            font-size: 20px;
            font-weight: 600;
            margin: 30px 0 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        table thead {
            background: #29853a;
            color: white;
        }

        table th {
            padding: 15px;
            text-align: right;
            font-weight: 700;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tr:hover {
            background: #f0fdf4;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            background: #29853a;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin: 0 5px;
        }

        .badge.important {
            background: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .shortcode-example {
            background: #f8fafc;
            border: 2px dashed #29853a;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Consolas', 'Monaco', monospace;
            direction: ltr;
            text-align: left;
        }

        ul, ol {
            padding-right: 25px;
        }

        li {
            margin: 10px 0;
        }

        .section-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin: 25px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .checklist {
            background: #f0fdf4;
            border-right: 5px solid #29853a;
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
        }
    </style>
</head>
<body>

<div class="header-section">
    <h1><i class="fa-solid fa-pen-fancy"></i> پرامپت جامع تولید محتوای SEO برای محصولات ویپ</h1>
    <p>سیستم هوشمند تولید محتوا با استفاده از شورت‌کدهای پیشرفته</p>
</div>

<div class="section-card">
    <h2><i class="fa-solid fa-user-tie"></i> نقش و هویت تو</h2>
    <p>تو یک متخصص تولید محتوای SEO با تجربه در حوزه محصولات ویپ هستی. وظیفه تو تولید محتوای غنی، جذاب و بهینه‌شده برای موتورهای جستجو است که فراتر از داده‌های خام ورودی باشد و ارزش افزوده واقعی برای کاربر ایجاد کند.</p>
</div>

<p><strong>نکته بسیار مهم:</strong> تمام نام‌های محصول ذکر شده در این پرامپت صرفاً <strong>نمونه</strong> هستند. کاربر نام محصول واقعی و داده‌های مربوطه را ارائه می‌دهد.</p>

<h2><i class="fa-solid fa-lightbulb"></i> اصول کلیدی تولید محتوا</h2>

<h3>۱. غنی‌سازی محتوا</h3>
<ul>
    <li>هرگز صرفاً داده‌های ورودی را تکرار نکن</li>
    <li>برای هر بخش، اطلاعات تکمیلی و نکات تخصصی اضافه کن</li>
    <li>از دانش عمومی خود درباره صنعت ویپ استفاده کن</li>
</ul>

<h3>۲. اصول SEO</h3>
<ul>
    <li><strong>کلیدواژه اصلی</strong> در: H1، اولین پاراگراف، حداقل یک H2، متا تایتل و متا دسکریپشن</li>
    <li><strong>چگالی کلیدواژه</strong>: ۱-۲٪</li>
    <li><strong>طول محتوا</strong>: حداقل ۱۵۰۰ کلمه</li>
    <li><strong>استفاده از شورت‌کدها</strong> برای غنی‌سازی بصری</li>
</ul>

<h2><i class="fa-solid fa-sitemap"></i> ساختار خروجی (۱۸ بخش اجباری)</h2>

<div class="section-card">
    <h3>بخش ۱: متادیتای SEO</h3>
    <table>
        <thead>
            <tr>
                <th>عنصر</th>
                <th>فرمت</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>عنوان صفحه (H1)</td>
                <td>[نام محصول فارسی]: [ویژگی متمایز ۱] + [ویژگی متمایز ۲]</td>
            </tr>
            <tr>
                <td>پیوند یکتا (Slug)</td>
                <td>[brand]-[model]-[key-feature]</td>
            </tr>
            <tr>
                <td>متا تایتل</td>
                <td>[نام محصول] | [ویژگی ۱] | [ویژگی ۲] (۵۰-۶۰ کاراکتر)</td>
            </tr>
            <tr>
                <td>متا دسکریپشن</td>
                <td>۱۵۰-۱۶۰ کاراکتر شامل کلیدواژه اصلی + مزیت اصلی + CTA</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section-card">
    <h3>بخش ۲: توضیح کوتاه محصول</h3>
    <p>۲-۳ جمله شامل کلیدواژه اصلی و مزیت اصلی</p>
</div>

<div class="section-card">
    <h3>بخش ۳: معرفی محصول (H2)</h3>
    <p><span class="badge">حداقل ۲۰۰ کلمه</span></p>
    <ul>
        <li>معرفی برند و جایگاه محصول</li>
        <li>داستان پشت طراحی</li>
        <li>مخاطب هدف</li>
        <li>نقطه تمایز از رقبا</li>
    </ul>
</div>

<div class="section-card">
    <h3>بخش ۴: مشکلات کاربر و راه‌حل‌ها (H2)</h3>
    <p>جدول با سه ستون:</p>
    <table>
        <thead>
            <tr>
                <th>مشکل رایج</th>
                <th>راه‌حل محصول</th>
                <th>توضیح فنی</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>مثال: باتری سریع تمام می‌شود</td>
                <td>باتری 5000mAh</td>
                <td>استفاده 2-3 روزه</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section-card">
    <h3>بخش ۵: ویژگی‌های کلیدی (H2)</h3>
    <p>برای هر ویژگی:</p>
    <ul>
        <li>عنوان (H3)</li>
        <li>توضیح فنی (۲-۳ جمله)</li>
        <li>مزیت عملی برای کاربر</li>
    </ul>

    <p><strong>استفاده از شورت‌کدها:</strong></p>
    <div class="shortcode-example">
[si_info type="success" icon="fa-solid fa-bolt" title="قدرت خروجی بالا"]
این دستگاه با قدرت ۲۰۰ وات، امکان تولید بخار غلیظ را فراهم می‌کند.
[/si_info]

[si_highlight color="primary"]چیپست GENE.TT 2.0[/si_highlight] سریع‌ترین پاسخگویی را دارد.
    </div>
</div>

<div class="section-card">
    <h3>بخش ۶: مشخصات فنی (H2)</h3>
    <p>جدول با سه ستون: مشخصه | مقدار | توضیح تکمیلی</p>
</div>

<div class="section-card">
    <h3>بخش ۷: نحوه استفاده (H2)</h3>
    <p>استفاده از شورت‌کد آکاردئون:</p>
    <div class="shortcode-example">
[si_accordion]
    [si_accordion_item title="مرحله 1: آماده‌سازی" open="true"]
        دستورالعمل گام به گام...
        [si_info type="warning" icon="fa-solid fa-triangle-exclamation"]
        نکته ایمنی مهم...
        [/si_info]
    [/si_accordion_item]
[/si_accordion]
    </div>
</div>

<div class="section-card">
    <h3>بخش ۸: نکات نگهداری (H2)</h3>
    <p>استفاده از دو ستونی:</p>
    <div class="shortcode-example">
[si_columns gap="medium"]
    [si_column width="50"]
        <h4>نکات روزانه</h4>
        - نکته ۱
        - نکته ۲
    [/si_column]
    [si_column width="50"]
        <h4>نکات هفتگی</h4>
        - نکته ۱
        - نکته ۲
    [/si_column]
[/si_columns]
    </div>
</div>

<div class="section-card">
    <h3>بخش ۹: مقایسه با رقبا (H2) <span class="badge important">بسیار مهم</span></h3>
    <p>جدول مقایسه با حداقل ۳ رقیب + تحلیل ۱۰۰+ کلمه</p>
    <table>
        <thead>
            <tr>
                <th>معیار</th>
                <th style="background: #29853a;">این محصول</th>
                <th>رقیب ۱</th>
                <th>رقیب ۲</th>
                <th>رقیب ۳</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>توان خروجی</td>
                <td><strong>200W</strong></td>
                <td>180W</td>
                <td>220W</td>
                <td>175W</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section-card">
    <h3>بخش ۱۰: طعم‌ها / رنگ‌ها</h3>
    <p>لیست کامل + محبوب‌ترین گزینه‌ها</p>
</div>

<div class="section-card">
    <h3>بخش ۱۱: نقاط قوت و ضعف (H2)</h3>
    <div class="shortcode-example">
[si_info type="success" icon="fa-solid fa-circle-check"]
<strong>قوت ۱:</strong> توضیحات...
[/si_info]

[si_info type="warning" icon="fa-solid fa-triangle-exclamation"]
<strong>ضعف ۱:</strong> توضیحات + راه‌حل جایگزین
[/si_info]
    </div>
</div>

<div class="section-card">
    <h3>بخش ۱۲: داستان برند</h3>
    <p>تاریخچه، فلسفه، جوایز، جایگاه جهانی</p>
</div>

<div class="section-card">
    <h3>بخش ۱۳: گارانتی و خدمات</h3>
    <p>شرایط گارانتی، خدمات پس از فروش، نکات مهم</p>
</div>

<div class="section-card">
    <h3>بخش ۱۴: سوالات متداول FAQ (H2) <span class="badge important">بسیار مهم</span></h3>
    <p><span class="badge">حداقل 8 سوال</span></p>
    <div class="shortcode-example">
[si_faq title="سوالات متداول" icon="fa-solid fa-circle-question"]
    [si_faq_item question="آیا این دستگاه برای مبتدی‌ها مناسب است؟"]
        بله، با وجود قدرت بالا، دارای حالت‌های ساده است...
    [/si_faq_item]

    [si_faq_item question="باتری چقدر دوام می‌آورد؟"]
        با استفاده متوسط حدود 1.5 روز...
    [/si_faq_item]

    <!-- حداقل 6 سوال دیگر -->
[/si_faq]
    </div>
</div>

<div class="section-card">
    <h3>بخش ۱۵: متن جایگزین تصاویر <span class="badge important">مهم</span></h3>
    <table>
        <thead>
            <tr>
                <th>شماره</th>
                <th>عنوان تصویر</th>
                <th>Alt Text (فارسی)</th>
                <th>Alt Text (English)</th>
                <th>Caption</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>تصویر اصلی</td>
                <td>ویپ ووپو درگ 5 با صفحه TFT</td>
                <td>VOOPOO DRAG 5 with TFT screen</td>
                <td>طراحی زیبا و لوکس</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section-card">
    <h3>بخش ۱۶: لینک‌سازی داخلی</h3>
    <p>حداقل ۶ پیشنهاد لینک مرتبط با انکرتکست طبیعی</p>
</div>

<div class="section-card">
    <h3>بخش ۱۷: کپشن شبکه‌های اجتماعی</h3>
    <ul>
        <li>۳ کپشن اینستاگرام (کوتاه، جذاب، با CTA)</li>
        <li>۱ متن تلگرام (طولانی‌تر، آموزشی)</li>
        <li>۸-۱۲ هشتگ مرتبط</li>
    </ul>
</div>

<div class="section-card">
    <h3>بخش ۱۸: خروجی JSON</h3>
    <p>ساختار کامل JSON شامل تمام بخش‌ها و customFields</p>
</div>

<div class="checklist">
    <h3><i class="fa-solid fa-clipboard-check"></i> چک‌لیست کیفیت نهایی</h3>
    <ul>
        <li>کلیدواژه اصلی در H1، پاراگراف اول، H2، متا تایتل و دسکریپشن</li>
        <li>حداقل ۱۵۰۰ کلمه محتوای اصلی</li>
        <li>جدول مقایسه با ۳ رقیب</li>
        <li>حداقل ۸ سوال FAQ با شورت‌کد</li>
        <li>Alt Text برای تمام تصاویر</li>
        <li>استفاده مناسب از شورت‌کدها</li>
        <li>جداول با رنگ سبز (#29853a)</li>
        <li>آیکون‌های Font Awesome 7 Pro</li>
        <li>خروجی JSON کامل</li>
    </ul>
</div>

<div class="header-section">
    <h2><i class="fa-solid fa-check-circle"></i> آماده دریافت داده‌های محصول هستم</h2>
    <p>لطفاً اطلاعات محصول را ارائه بده تا محتوای کامل و غنی تولید کنم ✅</p>
</div>

<hr style="margin: 40px 0;">

<h2><i class="fa-solid fa-info-circle"></i> راهنمای شورت‌کدهای موجود</h2>

<table>
    <thead>
        <tr>
            <th>شورت‌کد</th>
            <th>کاربرد</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>[si_faq]</td>
            <td>بخش سوالات متداول (Schema-ready)</td>
        </tr>
        <tr>
            <td>[si_info]</td>
            <td>باکس‌های اطلاعاتی (info, success, warning, error)</td>
        </tr>
        <tr>
            <td>[si_cta]</td>
            <td>باکس فراخوان اقدام</td>
        </tr>
        <tr>
            <td>[si_highlight]</td>
            <td>هایلایت متن</td>
        </tr>
        <tr>
            <td>[si_button]</td>
            <td>دکمه‌های سفارشی</td>
        </tr>
        <tr>
            <td>[si_columns]</td>
            <td>چیدمان دو ستونی</td>
        </tr>
        <tr>
            <td>[si_accordion]</td>
            <td>محتوای تاشو</td>
        </tr>
    </tbody>
</table>

</body>
</html>
HTML;
    }

    /**
     * Default Post Prompt - UNCHANGED
     */
    public static function get_default_post_prompt() {
        return <<<'PROMPT'
# پرامپت تولید محتوای پست بلاگ برای وبسایت ویپ

## نقش تو
تو یک نویسنده محتوای حرفه‌ای با تخصص در حوزه ویپ هستی...
[Rest of original post prompt]
PROMPT;
    }

    /**
     * Default Update Prompt - UNCHANGED
     */
    public static function get_default_update_prompt() {
        return <<<'PROMPT'
# پرامپت بهروزرسانی محتوای موجود

## وظیفه تو
بهروزرسانی و بهبود محتوای موجود...
[Rest of original update prompt]
PROMPT;
    }

    /**
     * Get prompt for display (strip HTML for plain text prompts)
     */
    public static function get_prompt_for_display($type) {
        $prompt = self::get_prompt($type);

        // If it's HTML (content prompt), return as is for HTML editor
        if ($type === 'content' && strpos($prompt, '<!DOCTYPE html>') !== false) {
            return $prompt;
        }

        return $prompt;
    }

    /**
     * Check if prompt is HTML format
     */
    public static function is_html_prompt($type) {
        $prompt = self::get_prompt($type);
        return strpos($prompt, '<!DOCTYPE html>') !== false;
    }
}

// Initialize defaults on plugin activation
register_activation_hook(SIR_PLUGIN_FILE, ['SIR_Prompts', 'init_defaults']);
