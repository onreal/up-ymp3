<?php

if (!defined('ABSPATH')) exit;

class Upymp3Ajax {
    public function __construct() {
        add_action('wp_ajax_upymp3_convert', [$this, 'handle_conversion']);
        add_action('wp_ajax_nopriv_upymp3_convert', [$this, 'handle_conversion']); // For non-logged-in users

        add_action('wp_ajax_upymp3_check_status', [$this, 'check_status']);
        add_action('wp_ajax_nopriv_upymp3_check_status', [$this, 'check_status']);
    }

    public function handle_conversion() {
        check_ajax_referer('upymp3_nonce', 'nonce');

        $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
        if (empty($url) || !self::is_valid_url($url)) {
            wp_send_json_error('Invalid URL format.');
        }

        $user_ip = $_SERVER['REMOTE_ADDR'];
        if (Upymp3Queue::is_rate_limited($user_ip)) {
            wp_send_json_error('Rate limit exceeded. Try again later.');
        }

        $job_id = Upymp3Queue::add_to_queue($url, $user_ip);
        if (!$job_id) {
            wp_send_json_error('Failed to queue the request.');
        }

        wp_send_json_success(['message' => 'Queued for processing.', 'job_id' => $job_id]);
    }

    public function check_status() {
        check_ajax_referer('upymp3_nonce', 'nonce');

        $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        if (!$job_id) {
            wp_send_json_error('Invalid job ID.');
        }

        $job = Upymp3Queue::get_job($job_id);
        if (!$job) {
            wp_send_json_error('Job not found.');
        }

        if ($job->status === 'completed') {
            $upload_dir = wp_upload_dir();
            $downloadUrl = $upload_dir['baseurl'] . '/upymp3/downloads/' . $job->file_name;
            wp_send_json_success(['status' => 'completed', 'file_url' => $downloadUrl]);
        }

        wp_send_json_success(['status' => $job->status]);
    }

    private static function is_valid_url($url) {
        return preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|soundcloud\.com)\/.+/', $url);
    }

}
new Upymp3Ajax();
