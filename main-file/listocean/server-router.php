<?php
// Built-in PHP server router: serve existing files, otherwise route to index.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false; // serve the requested resource as-is
}

// Fallback: for missing media-uploader images (including thumbnails), serve a placeholder
if (preg_match('#^/assets/uploads/media-uploader/(?:grid/|large/|semi-large/)?[^/]+\.(png|jpe?g|gif|webp|svg)$#i', $uri)) {
    $placeholder = __DIR__ . '/assets/frontend/img/gallery/single-image-upload.png';
    if (file_exists($placeholder)) {
        header('Content-Type: image/png');
        header('Cache-Control: no-store');
        readfile($placeholder);
        exit;
    }
}

require_once __DIR__ . '/index.php';
