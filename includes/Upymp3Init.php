<?php

if (!defined('ABSPATH')) {
    exit;
}

class Upymp3Init {
    public function __construct() {
        $this->load_dependencies();
        $this->register_hooks();
    }

    private function load_dependencies() {
        require_once UPYMP3_PATH . 'includes/Upymp3Ajax.php';
        require_once UPYMP3_PATH . 'includes/Upymp3Queue.php';
        require_once UPYMP3_PATH . 'includes/Upymp3Converter.php';
        require_once UPYMP3_PATH . 'includes/Upymp3Cleanup.php';
    }

    private function register_hooks() {
        add_shortcode('upymp3_form', [$this, 'render_conversion_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('cron_schedules', [$this, 'register_cron_intervals']);
        add_action('upymp3_cron_hook', ['Upymp3Queue', 'process_queue']);
    }

    public function render_conversion_form() {
        ob_start();
        ?>
        <div class="upymp3-container">
            <h2 class="upymp3-title">Convert YouTube/SoundCloud to MP3</h2>
            <input type="text" id="upymp3-url" class="upymp3-input" placeholder="Enter URL here..." />
            <button id="upymp3-submit" class="upymp3-button">Convert</button>
            <div id="upymp3-loader" class="upymp3-loader"></div>
            <p id="upymp3-status" class="upymp3-status"></p>
        </div>
        <?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('upymp3-style', plugins_url('assets/upymp3.css', __DIR__));
        wp_enqueue_script('upymp3-js', plugins_url('assets/js/upymp3.js', __DIR__), ['jquery'], 1.00, true);
        wp_localize_script('upymp3-js', 'upymp3_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('upymp3_nonce'),
        ]);
    }

    public function register_cron_intervals($schedules) {
        $schedules['every_minute'] = ['interval' => 3, 'display' => 'Every Minute'];
        return $schedules;
    }
}

new Upymp3Init();
