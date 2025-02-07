<?php

if (!defined('ABSPATH')) exit;

class Upymp3Converter {
    public static function process_queue() {
        $job = Upymp3Queue::get_next_job();
        if (!$job) return;

        Upymp3Logger::log("Processing job: {$job->id} - {$job->url}");

        Upymp3Queue::update_job_status($job->id, 'processing');
        $upload_dir = wp_upload_dir();
        $output_dir = $upload_dir['basedir'] . '/upymp3/';
        $output_dir = $output_dir . 'downloads/';
        $file_name = uniqid() . '.mp3';
        $file_path = escapeshellarg($output_dir . $file_name);

        $command = escapeshellcmd("yt-dlp --extract-audio --audio-format mp3 -o '$file_path' '$job->url'");
        exec($command, $output, $return_var);

        if ($return_var === 0) {
            Upymp3Queue::update_job_status($job->id, 'completed', $file_path, $file_name);
            Upymp3Logger::log("Conversion success: {$job->id}");
        } else {
            Upymp3Queue::update_job_status($job->id, 'failed', $file_path, $file_name);
            Upymp3Logger::log("Conversion failed: {$job->id}");
        }
    }
}
