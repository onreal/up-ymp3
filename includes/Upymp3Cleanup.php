<?php

if (!defined('ABSPATH')) exit;

class Upymp3Cleanup {
    public static function delete_old_files() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';
        $expiry_hours = get_option('upymp3_file_expiry', 24);

        $files = $wpdb->get_results($wpdb->prepare(
            "SELECT file_path FROM $table_name WHERE status = 'completed' AND created_at < NOW() - INTERVAL %d HOUR",
            $expiry_hours
        ));

        foreach ($files as $file) {
            if (file_exists($file->file_path)) {
                unlink($file->file_path);
            }
        }

        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE status = 'completed' AND created_at < NOW() - INTERVAL %d HOUR",
            $expiry_hours
        ));
    }
}

add_action('upymp3_cleanup', ['Upymp3Cleanup', 'delete_old_files']);
if (!wp_next_scheduled('upymp3_cleanup')) {
    wp_schedule_event(time(), 'daily', 'upymp3_cleanup');
}
