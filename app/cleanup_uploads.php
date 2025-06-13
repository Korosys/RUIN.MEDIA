<?php
// Cleanup script to delete files older than 5 minutes
$uploadDir = '/var/www/html/uploads/';

// Create uploads directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Delete files older than 5 minutes
$files = glob($uploadDir . '*');
$now   = time();

foreach ($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) > 300) { // 300 seconds = 5 minutes
            unlink($file);
        }
    }
}