# UPYMP3 - YouTube & SoundCloud MP3 Converter for WordPress

## Overview
UPYMP3 is a WordPress plugin that enables users to convert YouTube and SoundCloud videos into MP3 files using a **self-hosted** `yt-dlp` instance. It includes a queue system to efficiently handle multiple conversions and ensures smooth background processing using AJAX.

## Features
- ✅ **Self-Hosted `yt-dlp`** – No reliance on external services.
- ✅ **Queue System** – Handles multiple conversions concurrently.
- ✅ **Rate Limiting** – Prevents abuse by restricting conversions per user.
- ✅ **AJAX Processing** – Non-blocking operations for seamless user experience.
- ✅ **Cron Job for Queue Processing** – Automatically schedules background conversions.
- ✅ **Secure File Management** – Saves MP3 files in the WordPress uploads directory.

## Installation
1. Upload the `upymp3` plugin folder to your WordPress plugins directory (`/wp-content/plugins/`).
2. Activate the plugin from the WordPress admin panel.
3. Ensure `yt-dlp` is installed on your server and accessible via command line.
4. Configure plugin settings, such as rate limits and queue handling, via the admin panel.

## Usage
1. Add a YouTube or SoundCloud URL in the plugin interface.
2. Click the **Convert** button to start the MP3 conversion process.
3. The plugin processes the request in the background and provides a download link once completed.

## Cron Job Setup
The plugin utilizes a WordPress cron event (`upymp3_cron_hook`) to process the queue. Ensure WP-Cron is enabled, or set up a real cron job:
```bash
* * * * * wget -q -O - https://yourwebsite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```
If you need to delete an existing cron event before registering a new one, you can use:
```php
wp_clear_scheduled_hook('upymp3_cron_hook');
```

## Troubleshooting
- **Conversions not working?** Ensure `yt-dlp` is installed and executable.
- **Cron jobs not running?** Verify WP-Cron is enabled or manually trigger it via a system cron.
- **File not found errors?** Ensure WordPress has write permissions to the uploads directory.

## Upcoming Features
- Support for additional audio sources.
- Enhanced admin controls and conversion logs.
- Integration with cloud storage for file uploads.

## Contribute & Support
Feel free to fork this repository, submit pull requests, or open issues for improvements. 🚀

