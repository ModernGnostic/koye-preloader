<?php
/**
 * Plugin Name: Koye Preloader
 * Description: A full-featured, customizable preloader with image selection from the Media Library, tagline styling, multiple animations, page display rules, and live admin preview.
 * Version: 1.3.0
 * Author: Sam Ayangbola
 * Author URI: https://linktr.ee/iam_wealthyman
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: koye-preloader
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

/** Plugin version for cache-busting */
define('KOYE_PRELOADER_VER', '1.3.0');

/**
 * Helpers
 */
function koye_preloader_plugins_url($path = '') {
    return plugins_url(ltrim($path, '/'), __FILE__);
}

/**
 * Settings and Admin UI
 */
require_once __DIR__ . '/includes/admin-page.php';

/**
 * Should preloader show on current page?
 */
function koye_should_show_preloader() {
    $opts = get_option('koye_preloader_settings');
    if (!$opts) return true;

    $limit = $opts['page_limit'] ?? 'all';
    if ($limit === 'all') return true;

    $list_raw = $opts['page_list'] ?? '';
    if (empty($list_raw)) return false;

    $list = array_map('trim', explode(',', $list_raw));
    $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (is_singular()) {
        $post_id = get_queried_object_id();
        if (in_array((string)$post_id, array_map('strval', $list), true)) return true;
    }
    if (in_array($current_url, $list, true)) return true;

    return false;
}

/**
 * Frontend output (footer)
 */
add_action('wp_footer', function () {
    if (!koye_should_show_preloader()) return;

    $opts      = get_option('koye_preloader_settings');
    $logo      = esc_url($opts['logo'] ?? '');
    $tagline   = esc_html($opts['tagline'] ?? '');
    $animation = $opts['animation'] ?? 'pulse';
    $show_icon = !empty($opts['show_icon']);

    $tagline_style = '';
    if (!empty($opts['tagline_font_size']))  $tagline_style .= 'font-size:' . intval($opts['tagline_font_size']) . 'px;';
    if (!empty($opts['tagline_font_color'])) $tagline_style .= 'color:' . esc_attr($opts['tagline_font_color']) . ';';
    if (!empty($opts['tagline_font_weight']))$tagline_style .= 'font-weight:' . esc_attr($opts['tagline_font_weight']) . ';';
    if (!empty($opts['tagline_font_style'])) $tagline_style .= 'font-style:' . esc_attr($opts['tagline_font_style']) . ';';
    ?>
    <div id="preloader" class="<?php echo esc_attr($animation); ?>">
        <?php if ($logo): ?>
            <img src="<?php echo $logo; ?>" alt="<?php esc_attr_e('Loading...', 'koye-preloader'); ?>" class="preloader-logo" />
        <?php endif; ?>
        <?php if ($tagline): ?>
            <p class="preloader-tagline" style="<?php echo esc_attr($tagline_style); ?>"><?php echo $tagline; ?></p>
        <?php endif; ?>
        <?php if ($show_icon): ?>
            <div class="preloader-icon"></div>
        <?php endif; ?>
    </div>
    <script>
    window.addEventListener('load', function () {
        document.body.classList.add('loaded');
    });
    </script>
    <?php
    // Load frontend stylesheet
    wp_enqueue_style(
        'koye-preloader-style',
        koye_preloader_plugins_url('css/style.css'),
        [],
        KOYE_PRELOADER_VER
    );
});
