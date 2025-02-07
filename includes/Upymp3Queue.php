<?php

if (!defined('ABSPATH')) exit;

class Upymp3Queue {
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            url TEXT NOT NULL,
            user_ip VARCHAR(45) NOT NULL,
            status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
            file_path TEXT DEFAULT NULL,
            file_name VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function schedule_cron() {
        wp_clear_scheduled_hook('upymp3_cron_hook');
        if (!wp_next_scheduled('upymp3_cron_hook')) {
            wp_schedule_event(time(), 'every_minute', 'upymp3_cron_hook');
        }
    }

    public static function process_queue() {
        //$jobs = Upymp3Queue::get_pending_jobs(3); // Process 3 jobs at once

        Upymp3Converter::process_queue();
    }

    public static function get_pending_jobs($limit = 3) {
        global $wpdb;

        // Get pending jobs, limit number of results
        $table_name = $wpdb->prefix . 'upymp3_queue';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE status = %s LIMIT %d", 'pending', $limit);
        $results = $wpdb->get_results($sql);

        return $results;
    }

    public static function add_to_queue($url, $user_ip) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';

        $wpdb->insert($table_name, [
            'url' => $url,
            'user_ip' => $user_ip,
            'status' => 'pending'
        ]);

        return $wpdb->insert_id;
    }

    public static function get_next_job() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';

        return $wpdb->get_row("SELECT * FROM $table_name WHERE status = 'pending' ORDER BY created_at ASC LIMIT 1");
    }

    public static function update_job_status($id, $status, $file_path = null, $file_name = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';

        $wpdb->update($table_name, [
            'status' => $status,
            'file_path' => $file_path,
            'file_name' => $file_name
        ], ['id' => $id]);
    }

    public static function is_rate_limited($user_ip) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'upymp3_queue';
        $max_conversions = get_option('upymp3_rate_limit', 15);

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_ip = %s AND created_at > NOW() - INTERVAL 1 HOUR",
            $user_ip
        ));

        return $count >= $max_conversions;
    }

    public static function get_job($job_id) {
        global $wpdb;

        // Define the table name
        $table_name = $wpdb->prefix . 'upymp3_queue';

        // Prepare the SQL query to get the job by job_id
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $job_id);

        // Execute the query and fetch the result
        $job = $wpdb->get_row($sql);

        return $job;
    }
}
