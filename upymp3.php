<?php
/**
 * Plugin Name: UPYMP3 Converter
 * Description: Convert YouTube/SoundCloud to MP3.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

define('UPYMP3_PATH', plugin_dir_path(__FILE__));

require_once UPYMP3_PATH . 'includes/Upymp3Admin.php';
require_once UPYMP3_PATH . 'includes/Upymp3Ajax.php';
require_once UPYMP3_PATH . 'includes/Upymp3Queue.php';
require_once UPYMP3_PATH . 'includes/Upymp3Converter.php';
require_once UPYMP3_PATH . 'includes/Upymp3Cleanup.php';
require_once UPYMP3_PATH . 'includes/Upymp3Logger.php';
require_once UPYMP3_PATH . 'includes/Upymp3Init.php';
function upymp3_activate() {
    Upymp3Queue::create_table();
    Upymp3Queue::schedule_cron();
    add_action('upymp3_cron_hook', ['Upymp3Queue', 'process_queue']);
}
register_activation_hook(__FILE__, 'upymp3_activate');

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('process_queue');
});
