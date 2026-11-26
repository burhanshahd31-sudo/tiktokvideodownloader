<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok Video Downloader - Without Watermark (SD & HD) & With Watermark</title>
    <meta name="description" content="Download TikTok videos without watermark in HD and SD quality, with watermark, or extract MP3 audio for free. Fast, unlimited, and works on all devices.">
    <meta name="keywords" content="tiktok downloader, download tiktok video, tiktok without watermark, tiktok hd download, tiktok mp3, tiktok watermarked">
    
    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="TikTok Video Downloader - Without Watermark (HD/SD)">
    <meta property="og:description" content="Free online tool to download TikTok videos without watermark in HD/SD and extract MP3 audio directly.">
    
    <!-- Favicon -->
    <link rel="icon" href="https://www.tiktok.com/favicon.ico" type="image/x-icon">
    
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Ambient Background Glows -->
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <!-- Header / Navbar -->
    <header class="site-header">
        <div class="container navbar">
            <a href="index.php" class="brand-logo" id="brandLogo">
                <div class="brand-icon">⚡</div>
                <div class="brand-text">
                    <span class="brand-title">TikSaver Ultra</span>
                    <span class="brand-tagline">HD & SD Downloader</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <ul class="nav-links">
                <li><a href="#howItWorks" class="nav-link">How to Download</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#faq" class="nav-link">FAQ</a></li>
            </ul>

            <!-- Header Right Actions -->
            <div class="header-right-actions">
                <div class="btn-header-badge">
                    <span>🔥 100% Free</span>
                </div>
                <!-- Mobile Hamburger Toggle -->
                <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Navigation Menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div class="mobile-nav-drawer" id="mobileNavDrawer">
            <div class="mobile-nav-inner">
                <ul class="mobile-nav-links">
                    <li><a href="#howItWorks" class="mobile-nav-link">📖 How to Download</a></li>
                    <li><a href="#features" class="mobile-nav-link">✨ Features</a></li>
                    <li><a href="#faq" class="mobile-nav-link">❓ FAQ</a></li>
                </ul>
                <div class="mobile-nav-footer">
                    <div class="mobile-badge-pill">🔥 100% Free & Unlimited Downloads</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="badge-pill">
                <span>✨ No Watermark • Full HD 1080p • SD Quality • MP3 Audio</span>
            </div>
            
            <h1 class="hero-title">
                Download TikTok Videos <br>
                <span class="gradient-text">Without Watermark & In High Quality</span>
            </h1>
            
            <p class="hero-description">
                Paste any TikTok link below to download crystal-clear HD MP4 videos (without or with watermark) and extract studio-quality MP3 audio in seconds.
            </p>

            <!-- Downloader Input Box -->
            <div class="downloader-box">
                <div class="input-wrapper">
                    <div class="input-icon">🔗</div>
                    <input 
                        type="url" 
                        id="tiktokUrl" 
                        class="url-input" 
                        placeholder="Paste TikTok video link here (e.g. https://www.tiktok.com/@... or https://vt.tiktok.com/...)" 
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <div class="input-actions">
                        <button type="button" class="btn-icon-action btn-clear" id="btnClear" title="Clear input">✕ Clear</button>
                        <button type="button" class="btn-icon-action" id="btnPaste" title="Paste from clipboard">📋 Paste</button>
                        <button type="button" class="btn-download-primary" id="btnFetch">
                            <span class="btn-icon">⚡</span>
                            <span>Download Now</span>
                        </button>
                    </div>
                </div>

                <div class="supported-links-hint">
                    <span>💡 Supported links:</span>
                    <span>✓ <code>tiktok.com/@user/video/...</code></span>
                    <span>✓ <code>vt.tiktok.com/...</code></span>
                    <span>✓ <code>vm.tiktok.com/...</code></span>
                    <span>✓ Photo Carousels</span>
                </div>
            </div>

            <!-- Loading Spinner State -->
            <div class="loading-box" id="loadingBox">
                <div class="spinner"></div>
                <div class="loading-text">Fetching & Processing TikTok Video...</div>
                <div class="loading-subtext">Extracting HD & SD streams without watermark...</div>
            </div>
        </section>

        <!-- Result Section -->
        <section class="result-section" id="resultSection">
            <div class="result-card">
                <div class="result-layout">
                    
                    <!-- Left: Video / Media Preview -->
                    <div class="preview-col">
                        <div class="preview-media-wrapper" id="previewMediaContainer">
                            <!-- Injected via JavaScript -->
                        </div>
                    </div>

                    <!-- Right: Details & Download Actions -->
                    <div class="details-col">
                        <div>
                            <!-- Creator Profile -->
                            <div class="creator-info-header">
                                <div class="creator-profile">
                                    <img src="" alt="Creator Avatar" class="creator-avatar" id="creatorAvatar">
                                    <div class="creator-meta">
                                        <h3><span id="creatorNickname">User</span> <span class="verified-icon">✓</span></h3>
                                        <div class="creator-username" id="creatorUsername">@user</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Real-time Stats -->
                            <div class="stats-bar" style="margin-top: 16px;">
                                <div class="stat-item">👁️ Views: <strong id="statViews">0</strong></div>
                                <div class="stat-item">❤️ Likes: <strong id="statLikes">0</strong></div>
                                <div class="stat-item">💬 Comments: <strong id="statComments">0</strong></div>
                                <div class="stat-item">🔁 Shares: <strong id="statShares">0</strong></div>
                            </div>

                            <!-- Caption Box -->
                            <div class="caption-box" style="margin-top: 16px;">
                                <div class="caption-text" id="captionText">Video description...</div>
                                <button type="button" class="btn-copy-caption" id="btnCopyCaption" title="Copy caption">📋 Copy</button>
                            </div>

                            <!-- Sound Bar -->
                            <div class="sound-info-bar" id="soundInfoBar" style="margin-top: 14px; display: none;">
                                <span class="sound-icon">🎵</span>
                                <span id="soundTitle">Original Sound</span>
                            </div>
                        </div>

                        <!-- Download Action Grid -->
                        <div>
                            <div class="download-options-title">
                                <span>📥 Available Download Qualities</span>
                            </div>

                            <div class="download-buttons-grid">
                                <!-- Option 1: No Watermark HD -->
                                <a href="#" id="btnDlHd" class="dl-btn dl-hd">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">Without Watermark (HD)</span>
                                        <span class="dl-btn-subtitle">1080p Ultra High Quality MP4</span>
                                    </div>
                                    <span class="dl-btn-icon">⚡</span>
                                </a>

                                <!-- Option 2: No Watermark SD -->
                                <a href="#" id="btnDlSd" class="dl-btn dl-sd">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">Without Watermark (SD)</span>
                                        <span class="dl-btn-subtitle">Fast & Lightweight MP4</span>
                                    </div>
                                    <span class="dl-btn-icon">🚀</span>
                                </a>

                                <!-- Option 3: With Watermark -->
                                <a href="#" id="btnDlWm" class="dl-btn dl-wm">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">With Watermark</span>
                                        <span class="dl-btn-subtitle">Original TikTok with logo</span>
                                    </div>
                                    <span class="dl-btn-icon">💧</span>
                                </a>

                                <!-- Option 4: Audio MP3 -->
                                <a href="#" id="btnDlAudio" class="dl-btn dl-audio">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">Download MP3 Audio</span>
                                        <span class="dl-btn-subtitle">Extract Sound Track 320kbps</span>
                                    </div>
                                    <span class="dl-btn-icon">🎵</span>
                                </a>

                                <!-- Option 5: HD Cover / Thumbnail -->
                                <a href="#" id="btnDlCover" class="dl-btn dl-cover">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">HD Cover Image</span>
                                        <span class="dl-btn-subtitle">Download Video Poster JPG</span>
                                    </div>
                                    <span class="dl-btn-icon">🖼️</span>
                                </a>

                                <!-- Option 6: Mobile QR Code -->
                                <button type="button" id="btnShowQr" class="dl-btn dl-qr">
                                    <div class="dl-btn-content">
                                        <span class="dl-btn-title">Scan QR Code</span>
                                        <span class="dl-btn-subtitle">Download on Mobile Phone</span>
                                    </div>
                                    <span class="dl-btn-icon">📲</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Slideshow Photos Container (if photo post) -->
                <div class="slides-section" id="slidesSection" style="display: none; padding: 0 32px 32px;">
                    <div class="download-options-title">
                        <span>📸 Slideshow Images (Click to Download Individual Photos)</span>
                    </div>
                    <div class="slides-grid" id="slidesGrid">
                        <!-- Injected via JS -->
                    </div>
                </div>

            </div>
        </section>

        <!-- Download History Section -->
        <section class="history-section" id="historySection" style="display: none;">
            <div class="section-header">
                <h2 class="section-title">🕒 Recent Downloads</h2>
                <button type="button" class="btn-text-danger" id="btnClearHistory">🗑️ Clear History</button>
            </div>
            <div class="history-grid" id="historyGrid">
                <!-- Injected via JS -->
            </div>
        </section>

        <!-- Features Showcase -->
        <section class="features-section" id="features">
            <div style="text-align: center;">
                <h2 class="section-title" style="justify-content: center;">Why Choose TikSaver Ultra?</h2>
                <p style="color: var(--text-secondary); margin-top: 8px;">The fastest and most reliable TikTok downloader on the web</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">✨</div>
                    <h3 class="feature-title">No Watermark & Watermarked</h3>
                    <p class="feature-desc">Download clean videos without the bouncing TikTok logo or keep the original watermark if you prefer.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">🚀</div>
                    <h3 class="feature-title">SD & Full HD (1080p)</h3>
                    <p class="feature-desc">Get the highest original quality available up to full 1080p 60fps HD or fast lightweight SD for low data.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">🎵</div>
                    <h3 class="feature-title">MP3 Audio Extraction</h3>
                    <p class="feature-desc">Easily extract background songs, trending sounds, and voice tracks directly to high bitrate MP3 audio.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">📱</div>
                    <h3 class="feature-title">Works on All Devices</h3>
                    <p class="feature-desc">Seamlessly works on iPhone, iPad, Android, Windows, Mac, and Linux without installing any third-party app.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">🔒</div>
                    <h3 class="feature-title">100% Free & Secure</h3>
                    <p class="feature-desc">Unlimited downloads with zero fees. We never store your personal data or login credentials.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">📸</div>
                    <h3 class="feature-title">Photo Slideshow Support</h3>
                    <p class="feature-desc">Supports TikTok photo carousels so you can download all original photos and swipe images in full resolution.</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works" id="howItWorks">
            <h2 class="section-title" style="justify-content: center;">How to Download TikTok Videos</h2>
            <p style="color: var(--text-secondary); margin-top: 8px;">Follow these 3 easy steps to save any TikTok to your device</p>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <div style="font-size: 2rem;">📋</div>
                    <h3>1. Copy TikTok Link</h3>
                    <p>Open TikTok app or website, find your desired video, tap <strong>Share</strong> and select <strong>Copy Link</strong>.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">02</div>
                    <div style="font-size: 2rem;">📥</div>
                    <h3>2. Paste Link Here</h3>
                    <p>Paste the copied video link into the input box above and click the <strong>Download Now</strong> button.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">03</div>
                    <div style="font-size: 2rem;">💾</div>
                    <h3>3. Choose SD / HD & Save</h3>
                    <p>Select your desired format (Without Watermark HD/SD, With Watermark, or MP3) to download instantly!</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section" id="faq">
            <div style="text-align: center;">
                <h2 class="section-title" style="justify-content: center;">Frequently Asked Questions</h2>
                <p style="color: var(--text-secondary); margin-top: 8px;">Got questions? We've got answers.</p>
            </div>

            <div class="faq-list">
                <div class="faq-item active">
                    <div class="faq-question">
                        <span>Is it free to download TikTok videos without watermark?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        Yes! TikSaver Ultra is 100% free with no limits on how many videos or audios you can download per day.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>What is the difference between SD and HD quality?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        HD (High Definition) provides the best available crystal-clear resolution (up to 1080p), while SD (Standard Definition) is optimized for smaller file sizes and faster downloads on slower internet connections.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I download TikTok videos with watermark?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        Yes! You have options to download both Without Watermark (clean video) and With Watermark (original TikTok brand logo and author username).
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Where are downloaded videos saved on my phone or PC?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        Files are automatically saved in your device's default "Downloads" folder or your browser's designated download directory.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I download MP3 music from TikTok videos?</span>
                        <span class="faq-icon">▼</span>
                    </div>
                    <div class="faq-answer">
                        Yes! Simply click the "Download MP3 Audio" button to extract the background soundtrack in high audio quality.
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- QR Code Modal -->
    <div class="modal-overlay" id="qrModal">
        <div class="modal-content">
            <button type="button" class="modal-close-btn" id="qrModalClose">✕</button>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">Scan to Download on Mobile</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 6px;" id="qrVideoTitle">TikTok Video</p>
            <img src="" alt="QR Code" class="qr-code-img" id="qrImage">
            <p style="font-size: 0.82rem; color: var(--text-muted);">Point your phone camera at this QR code to download directly to your mobile device.</p>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="brand-logo">
                    <div class="brand-icon" style="width:36px; height:36px; font-size:18px;">⚡</div>
                    <span class="brand-title" style="font-size:1.15rem;">TikSaver Ultra</span>
                </div>
                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                    &copy; <?php echo date('Y'); ?> TikSaver Ultra. All rights reserved.
                </div>
            </div>
            <p class="disclaimer-text">
                Disclaimer: TikSaver Ultra is an independent tool and is not affiliated, endorsed, or certified by TikTok or ByteDance Ltd. We do not host any copyrighted videos on our servers. All videos and audios belong to their respective creators.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>
</html>
