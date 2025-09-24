<?php
if (!defined('ABSPATH')) exit;

/**
 * Admin menu
 */
add_action('admin_menu', function () {
    add_options_page(
        __('Koye Preloader Settings', 'koye-preloader'),
        __('Koye Preloader', 'koye-preloader'),
        'manage_options',
        'koye-preloader',
        'koye_preloader_settings_page'
    );
});

/**
 * Register settings
 */
add_action('admin_init', function () {
    register_setting('koye_preloader_options', 'koye_preloader_settings');
});

/**
 * Enqueue admin scripts/styles
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'settings_page_koye-preloader') return;

    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    wp_enqueue_script(
        'koye-media-uploader',
        plugins_url('../js/media-uploader.js', __FILE__),
        ['jquery', 'wp-color-picker'],
        KOYE_PRELOADER_VER,
        true
    );
});

/**
 * Settings page markup
 */
function koye_preloader_settings_page() {
    $o = get_option('koye_preloader_settings', []);
    $logo = esc_attr($o['logo'] ?? '');
    $tagline = esc_attr($o['tagline'] ?? '');
    $animation = $o['animation'] ?? 'pulse';
    $show_icon = !empty($o['show_icon']);
    $page_limit = $o['page_limit'] ?? 'all';
    $page_list = esc_textarea($o['page_list'] ?? '');
    $tagline_font_size  = $o['tagline_font_size'] ?? '16';
    $tagline_font_color = $o['tagline_font_color'] ?? '#333333';
    $tagline_font_weight= $o['tagline_font_weight'] ?? 'normal';
    $tagline_font_style = $o['tagline_font_style'] ?? 'normal';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Koye Preloader Settings', 'koye-preloader'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('koye_preloader_options'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Logo Image', 'koye-preloader'); ?></th>
                    <td>
                        <input type="text" id="koye_logo" name="koye_preloader_settings[logo]" value="<?php echo $logo; ?>" class="regular-text" />
                        <button type="button" id="koye_logo_button" class="button"><?php esc_html_e('Select Image', 'koye-preloader'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Tagline', 'koye-preloader'); ?></th>
                    <td><input type="text" name="koye_preloader_settings[tagline]" value="<?php echo $tagline; ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Tagline Font Size (px)', 'koye-preloader'); ?></th>
                    <td><input type="number" name="koye_preloader_settings[tagline_font_size]" value="<?php echo esc_attr($tagline_font_size); ?>" min="8" max="72" /></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Tagline Font Color', 'koye-preloader'); ?></th>
                    <td><input type="text" name="koye_preloader_settings[tagline_font_color]" value="<?php echo esc_attr($tagline_font_color); ?>" class="koye-color-picker" /></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Tagline Font Weight', 'koye-preloader'); ?></th>
                    <td>
                        <select name="koye_preloader_settings[tagline_font_weight]">
                            <option value="normal" <?php selected($tagline_font_weight, 'normal'); ?>>Normal</option>
                            <option value="bold" <?php selected($tagline_font_weight, 'bold'); ?>>Bold</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Tagline Font Style', 'koye-preloader'); ?></th>
                    <td>
                        <select name="koye_preloader_settings[tagline_font_style]">
                            <option value="normal" <?php selected($tagline_font_style, 'normal'); ?>>Normal</option>
                            <option value="italic" <?php selected($tagline_font_style, 'italic'); ?>>Italic</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Animation Style', 'koye-preloader'); ?></th>
                    <td>
                        <select id="koye_animation" name="koye_preloader_settings[animation]">
                            <?php
                            $animations = ['pulse' => 'Pulse', 'fade' => 'Fade', 'bounce' => 'Bounce', 'rotate' => 'Rotate'];
                            foreach ($animations as $key => $label) {
                                printf('<option value="%s" %s>%s</option>', esc_attr($key), selected($animation, $key, false), esc_html($label));
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Show Spinner Icon', 'koye-preloader'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="koye_preloader_settings[show_icon]" value="1" <?php checked($show_icon); ?> />
                            <?php esc_html_e('Enable spinner below logo', 'koye-preloader'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Preloader Display Limit', 'koye-preloader'); ?></th>
                    <td>
                        <select id="koye_page_limit" name="koye_preloader_settings[page_limit]">
                            <option value="all" <?php selected($page_limit, 'all'); ?>><?php esc_html_e('All Pages', 'koye-preloader'); ?></option>
                            <option value="selected" <?php selected($page_limit, 'selected'); ?>><?php esc_html_e('Only Selected Pages', 'koye-preloader'); ?></option>
                        </select>
                        <p><small><?php esc_html_e('Enter page/post IDs or full URLs separated by commas.', 'koye-preloader'); ?></small></p>
                        <textarea name="koye_preloader_settings[page_list]" rows="3" cols="50"><?php echo $page_list; ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <h2><?php esc_html_e('Preloader Preview', 'koye-preloader'); ?></h2>
        <div id="koye-preloader-preview" class="<?php echo esc_attr($animation); ?>">
            <?php if ($logo): ?>
                <img src="<?php echo esc_url($logo); ?>" alt="<?php esc_attr_e('Logo Preview', 'koye-preloader'); ?>" />
            <?php else: ?>
                <div style="font-style:italic;color:#666;"><?php esc_html_e('No logo set', 'koye-preloader'); ?></div>
            <?php endif; ?>
            <?php if ($tagline): ?>
                <p class="preloader-tagline"><?php echo $tagline; ?></p>
            <?php endif; ?>
            <?php if ($show_icon): ?>
                <div class="preloader-icon"></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}