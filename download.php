<?php
// Prevent execution timeout for media downloads
set_time_limit(0);

$mediaUrl = $_GET['url'] ?? '';
$filename = $_GET['filename'] ?? 'tiktok_download';
$type = $_GET['type'] ?? '';

if (empty($mediaUrl)) {
    http_response_code(400);
    echo 'Media URL is required.';
    exit;
}

// Decode URL if encoded
$mediaUrl = trim(urldecode($mediaUrl));

// Clean and sanitize filename
$filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
if (empty($filename)) {
    $filename = 'tiktok_download';
}

// Determine extension
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!$ext) {
    if (strpos($type, 'audio') !== false || strpos($type, 'mp3') !== false) {
        $filename .= '.mp3';
        $ext = 'mp3';
    } elseif (strpos($type, 'image') !== false || strpos($type, 'cover') !== false || strpos($type, 'jpg') !== false) {
        $filename .= '.jpg';
        $ext = 'jpg';
    } else {
        $filename .= '.mp4';
        $ext = 'mp4';
    }
}

// MIME Types
$mimeTypes = [
    'mp4' => 'video/mp4',
    'mp3' => 'audio/mpeg',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'webp' => 'image/webp'
];

$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Smart headers and referer detection
$parsedHost = parse_url($mediaUrl, PHP_URL_HOST);
$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    'Accept: */*',
    'Accept-Language: en-US,en;q=0.9'
];

if ($parsedHost && stripos($parsedHost, 'tikwm.com') !== false) {
    $headers[] = 'Referer: https://www.tikwm.com/';
} elseif ($parsedHost && (stripos($parsedHost, 'tiktok') !== false || stripos($parsedHost, 'byteoversea') !== false || stripos($parsedHost, 'ibytedtos') !== false)) {
    $headers[] = 'Referer: https://www.tiktok.com/';
}

// Fetch file content via cURL
$ch = curl_init($mediaUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$fileData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// If downloaded successfully with valid status
if ($httpCode === 200 && !empty($fileData)) {
    // Clear any previous output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($fileData));
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    echo $fileData;
    exit;
} else {
    // If proxy failed, redirect directly to original media URL as fallback
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ' . $mediaUrl);
    exit;
}
