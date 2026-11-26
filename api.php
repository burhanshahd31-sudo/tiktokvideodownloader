<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get input URL
$inputUrl = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $inputUrl = $data['url'] ?? $_POST['url'] ?? '';
} else {
    $inputUrl = $_GET['url'] ?? '';
}

$inputUrl = trim($inputUrl);

if (empty($inputUrl)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid TikTok video URL.']);
    exit;
}

// Extract URL from raw text if user pasted extra text
if (preg_match('/https?:\/\/[^\s]+/', $inputUrl, $matches)) {
    $inputUrl = $matches[0];
}

// Helper: Fix & normalize full URLs
function normalizeMediaUrl($url, $domain = 'https://www.tikwm.com') {
    if (empty($url)) return '';
    $url = trim($url);
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    if (strpos($url, '//') === 0) {
        return 'https:' . $url;
    }
    return rtrim($domain, '/') . '/' . ltrim($url, '/');
}

// 1. Primary parser using TikWM endpoints
function fetchTikWM($url) {
    $endpoints = [
        'https://www.tikwm.com/api/',
        'https://api.tikwm.com/api/'
    ];

    foreach ($endpoints as $endpoint) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'url' => $url,
            'count' => 12,
            'cursor' => 0,
            'web' => 1,
            'hd' => 1
        ]));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept: application/json, text/plain, */*',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $json = json_decode($response, true);
            if ($json && isset($json['code']) && $json['code'] == 0 && !empty($json['data'])) {
                return $json['data'];
            }
        }
    }

    return null;
}

// 2. Fallback parser using Lovetik
function fetchLovetik($url) {
    $apiUrl = 'https://lovetik.com/api/ajax/search';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['query' => $url]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && !empty($response)) {
        $json = json_decode($response, true);
        if ($json && !empty($json['links'])) {
            return $json;
        }
    }
    return null;
}

// 3. Fallback parser using SSSTik AJAX
function fetchSSSTik($url) {
    $ch = curl_init('https://ssstik.io/abc?url=dl');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'id' => $url,
        'locale' => 'en',
        'tt' => '0'
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Origin: https://ssstik.io',
        'Referer: https://ssstik.io/en'
    ]);

    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && !empty($html)) {
        // Parse HTML output from SSSTik
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $links = [];
        $noWm = '';
        $hd = '';
        $audio = '';
        $title = '';
        $avatar = '';
        $author = '';

        // Extract links
        $aTags = $xpath->query('//a[contains(@class, "download_link")]');
        foreach ($aTags as $a) {
            $href = $a->getAttribute('href');
            $class = $a->getAttribute('class');
            if (strpos($class, 'without_watermark_direct') !== false) {
                $hd = $href;
            } elseif (strpos($class, 'without_watermark') !== false && empty($noWm)) {
                $noWm = $href;
            } elseif (strpos($class, 'music') !== false || strpos($href, 'mp3') !== false) {
                $audio = $href;
            }
        }

        // Extract title / caption
        $titleNodes = $xpath->query('//p[contains(@class, "maintext")]');
        if ($titleNodes->length > 0) {
            $title = trim($titleNodes->item(0)->textContent);
        }

        // Extract avatar
        $imgNodes = $xpath->query('//img[contains(@class, "result_author")]');
        if ($imgNodes->length > 0) {
            $avatar = $imgNodes->item(0)->getAttribute('src');
        }

        // Extract author name
        $authorNodes = $xpath->query('//h2');
        if ($authorNodes->length > 0) {
            $author = trim($authorNodes->item(0)->textContent);
        }

        if (!empty($noWm) || !empty($hd) || !empty($audio)) {
            return [
                'type' => 'ssstik',
                'title' => $title,
                'author' => $author,
                'avatar' => $avatar,
                'no_watermark' => $noWm ?: $hd,
                'hd' => $hd ?: $noWm,
                'audio' => $audio
            ];
        }
    }
    return null;
}

try {
    // Try Primary TikWM
    $tikData = fetchTikWM($inputUrl);

    if ($tikData) {
        $isImages = !empty($tikData['images']);

        $noWmSd = normalizeMediaUrl($tikData['play'] ?? '');
        $noWmHd = normalizeMediaUrl($tikData['hdplay'] ?? $tikData['play'] ?? '');
        $wmPlay = normalizeMediaUrl($tikData['wmplay'] ?? '');

        // Resolve MP3 Audio URL with multiple fallbacks
        $musicUrl = normalizeMediaUrl(
            $tikData['music'] ?? 
            $tikData['music_info']['play'] ?? 
            $tikData['music_info']['url'] ?? 
            ''
        );

        // Resolve HD Cover & Standard Cover
        $originCover = normalizeMediaUrl($tikData['origin_cover'] ?? '');
        $stdCover = normalizeMediaUrl($tikData['cover'] ?? '');
        $hdCover = $originCover ?: $stdCover;
        $dynamicCover = normalizeMediaUrl($tikData['dynamic_cover'] ?? '');

        // Images list for photo carousels
        $imagesList = [];
        if ($isImages && is_array($tikData['images'])) {
            foreach ($tikData['images'] as $img) {
                $imagesList[] = normalizeMediaUrl($img);
            }
        }

        $formatted = [
            'success' => true,
            'id' => $tikData['id'] ?? uniqid(),
            'title' => $tikData['title'] ?? 'TikTok Video',
            'duration' => (int)($tikData['duration'] ?? 0),
            'cover' => $hdCover,
            'origin_cover' => $originCover ?: $stdCover,
            'dynamic_cover' => $dynamicCover,
            'author' => [
                'id' => $tikData['author']['id'] ?? '',
                'unique_id' => $tikData['author']['unique_id'] ?? 'tiktok_user',
                'nickname' => $tikData['author']['nickname'] ?? 'TikTok Creator',
                'avatar' => normalizeMediaUrl($tikData['author']['avatar'] ?? '')
            ],
            'stats' => [
                'plays' => (int)($tikData['play_count'] ?? 0),
                'likes' => (int)($tikData['digg_count'] ?? 0),
                'comments' => (int)($tikData['comment_count'] ?? 0),
                'shares' => (int)($tikData['share_count'] ?? 0),
                'downloads' => (int)($tikData['download_count'] ?? 0)
            ],
            'music' => [
                'id' => $tikData['music_info']['id'] ?? '',
                'title' => $tikData['music_info']['title'] ?? ($musicUrl ? 'Original Soundtrack' : ''),
                'author' => $tikData['music_info']['author'] ?? '',
                'url' => $musicUrl,
                'duration' => $tikData['music_info']['duration'] ?? 0
            ],
            'downloads' => [
                'no_watermark_sd' => $noWmSd,
                'no_watermark_hd' => $noWmHd,
                'watermark' => $wmPlay ?: $noWmSd,
                'audio' => $musicUrl,
                'cover' => $hdCover
            ],
            'is_images' => $isImages,
            'images' => $imagesList
        ];

        echo json_encode($formatted, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    // Try Fallback 1: Lovetik
    $fallbackData = fetchLovetik($inputUrl);
    if ($fallbackData) {
        $noWm = '';
        $hd = '';
        $wm = '';
        $audio = '';

        if (!empty($fallbackData['links'])) {
            foreach ($fallbackData['links'] as $link) {
                $type = strtolower($link['t'] ?? '');
                $quality = strtolower($link['s'] ?? '');
                $url = $link['a'] ?? '';

                if (strpos($type, 'mp4') !== false || strpos($type, 'video') !== false) {
                    if (strpos($quality, 'hd') !== false) {
                        $hd = $url;
                    } elseif (strpos($quality, 'watermark') !== false) {
                        $wm = $url;
                    } else {
                        if (empty($noWm)) $noWm = $url;
                    }
                } elseif (strpos($type, 'mp3') !== false || strpos($type, 'audio') !== false) {
                    $audio = $url;
                }
            }
        }

        $cover = $fallbackData['cover'] ?? '';

        $formatted = [
            'success' => true,
            'id' => $fallbackData['vid'] ?? uniqid(),
            'title' => $fallbackData['desc'] ?? 'TikTok Video',
            'duration' => 0,
            'cover' => $cover,
            'origin_cover' => $cover,
            'dynamic_cover' => '',
            'author' => [
                'id' => '',
                'unique_id' => $fallbackData['author'] ?? 'tiktok_user',
                'nickname' => $fallbackData['author_name'] ?? $fallbackData['author'] ?? 'TikTok Creator',
                'avatar' => $fallbackData['author_a'] ?? ''
            ],
            'stats' => [
                'plays' => 0,
                'likes' => 0,
                'comments' => 0,
                'shares' => 0,
                'downloads' => 0
            ],
            'music' => [
                'id' => '',
                'title' => 'Original Music Track',
                'author' => '',
                'url' => $audio,
                'duration' => 0
            ],
            'downloads' => [
                'no_watermark_sd' => $noWm ?: $hd,
                'no_watermark_hd' => $hd ?: $noWm,
                'watermark' => $wm ?: $noWm,
                'audio' => $audio,
                'cover' => $cover
            ],
            'is_images' => false,
            'images' => []
        ];

        echo json_encode($formatted, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    // Try Fallback 2: SSSTik
    $ssstikData = fetchSSSTik($inputUrl);
    if ($ssstikData) {
        $formatted = [
            'success' => true,
            'id' => uniqid(),
            'title' => $ssstikData['title'] ?: 'TikTok Video',
            'duration' => 0,
            'cover' => $ssstikData['avatar'],
            'origin_cover' => $ssstikData['avatar'],
            'dynamic_cover' => '',
            'author' => [
                'id' => '',
                'unique_id' => 'tiktok_user',
                'nickname' => $ssstikData['author'] ?: 'TikTok Creator',
                'avatar' => $ssstikData['avatar']
            ],
            'stats' => [
                'plays' => 0,
                'likes' => 0,
                'comments' => 0,
                'shares' => 0,
                'downloads' => 0
            ],
            'music' => [
                'id' => '',
                'title' => 'Original Music',
                'author' => '',
                'url' => $ssstikData['audio'],
                'duration' => 0
            ],
            'downloads' => [
                'no_watermark_sd' => $ssstikData['no_watermark'],
                'no_watermark_hd' => $ssstikData['hd'] ?: $ssstikData['no_watermark'],
                'watermark' => $ssstikData['no_watermark'],
                'audio' => $ssstikData['audio'],
                'cover' => $ssstikData['avatar']
            ],
            'is_images' => false,
            'images' => []
        ];

        echo json_encode($formatted, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch video details. Please ensure the TikTok video link is public and valid.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
