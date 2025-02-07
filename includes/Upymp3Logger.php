<?php

if (!defined('ABSPATH')) exit;

class Upymp3Logger {
    public static function log($message) {
        $log_file = UPYMP3_PATH . 'logs/upymp3.log';
        $date = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
    }
}
