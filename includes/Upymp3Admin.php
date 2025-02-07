<?php

if (!defined('ABSPATH')) exit;

class Upymp3Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_menu() {
        add_options_page('UPYMP3 Settings', 'UPYMP3', 'manage_options', 'upymp3-settings', [$this, 'settings_page']);
        add_submenu_page('options-general.php', 'UPYMP3 Logs', 'UPYMP3 Logs', 'manage_options', 'upymp3-logs', [$this, 'logs_page']);
    }

    public function logs_page() {
        $log_file = UPYMP3_PATH . 'logs/upymp3.log';
        $logs = file_exists($log_file) ? file_get_contents($log_file) : 'No logs available.';

        ?>
        <div class="wrap">
            <h1>UPYMP3 Logs</h1>
            <textarea style="width: 100%; height: 500px;" readonly><?php echo esc_textarea($logs); ?></textarea>
            <form method="post">
                <input type="hidden" name="clear_logs" value="1">
                <?php submit_button('Clear Logs'); ?>
            </form>
        </div>
        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_logs'])) {
            file_put_contents($log_file, ''); // Clear logs
            wp_redirect(admin_url('options-general.php?page=upymp3-logs'));
            exit;
        }
    }

    public function register_settings() {
        register_setting('upymp3_settings', 'upymp3_rate_limit');
        register_setting('upymp3_settings', 'upymp3_allowed_services');
        register_setting('upymp3_settings', 'upymp3_file_expiry');

        add_settings_section('upymp3_main_section', 'General Settings', null, 'upymp3-settings');

        add_settings_field('upymp3_rate_limit', 'Max conversions per hour', [$this, 'rate_limit_field'], 'upymp3-settings', 'upymp3_main_section');
        add_settings_field('upymp3_allowed_services', 'Allowed Services', [$this, 'allowed_services_field'], 'upymp3-settings', 'upymp3_main_section');
        add_settings_field('upymp3_file_expiry', 'File Expiry Time (hours)', [$this, 'file_expiry_field'], 'upymp3-settings', 'upymp3_main_section');
    }

    public function rate_limit_field() {
        $value = get_option('upymp3_rate_limit', 3);
        echo '<input type="number" name="upymp3_rate_limit" value="' . esc_attr($value) . '" min="1">';
    }

    public function allowed_services_field() {
        $value = get_option('upymp3_allowed_services', ['youtube', 'soundcloud']);
        echo '<select name="upymp3_allowed_services[]" multiple>';
        echo '<option value="youtube" ' . (in_array('youtube', $value) ? 'selected' : '') . '>YouTube</option>';
        echo '<option value="soundcloud" ' . (in_array('soundcloud', $value) ? 'selected' : '') . '>SoundCloud</option>';
        echo '</select>';
    }

    public function file_expiry_field() {
        $value = get_option('upymp3_file_expiry', 24);
        echo '<input type="number" name="upymp3_file_expiry" value="' . esc_attr($value) . '" min="1">';
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>UPYMP3 Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('upymp3_settings');
                do_settings_sections('upymp3-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
new Upymp3Admin();
