/**
 * TikTok Video Downloader - Interactive Client Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const urlInput = document.getElementById('tiktokUrl');
    const btnPaste = document.getElementById('btnPaste');
    const btnClear = document.getElementById('btnClear');
    const btnFetch = document.getElementById('btnFetch');
    const loadingBox = document.getElementById('loadingBox');
    const resultSection = document.getElementById('resultSection');
    const historySection = document.getElementById('historySection');
    const historyGrid = document.getElementById('historyGrid');
    const btnClearHistory = document.getElementById('btnClearHistory');
    const qrModal = document.getElementById('qrModal');
    const qrModalClose = document.getElementById('qrModalClose');
    const qrImage = document.getElementById('qrImage');
    const qrVideoTitle = document.getElementById('qrVideoTitle');
    const toastContainer = document.getElementById('toastContainer');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileNavDrawer = document.getElementById('mobileNavDrawer');

    // Mobile Menu Toggle
    if (mobileMenuToggle && mobileNavDrawer) {
        mobileMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenuToggle.classList.toggle('active');
            mobileNavDrawer.classList.toggle('open');
        });

        // Close drawer when clicking any mobile nav link
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuToggle.classList.remove('active');
                mobileNavDrawer.classList.remove('open');
            });
        });

        // Close drawer when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileNavDrawer.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mobileMenuToggle.classList.remove('active');
                mobileNavDrawer.classList.remove('open');
            }
        });
    }

    // State
    let currentVideoData = null;

    // Initialize history
    loadHistory();

    // Event: Input text changes
    urlInput.addEventListener('input', () => {
        if (urlInput.value.trim().length > 0) {
            btnClear.style.display = 'flex';
        } else {
            btnClear.style.display = 'none';
        }
    });

    // Event: Clear button
    btnClear.addEventListener('click', () => {
        urlInput.value = '';
        btnClear.style.display = 'none';
        urlInput.focus();
    });

    // Event: Paste from Clipboard
    btnPaste.addEventListener('click', async () => {
        try {
            if (navigator.clipboard && navigator.clipboard.readText) {
                const text = await navigator.clipboard.readText();
                if (text) {
                    urlInput.value = text.trim();
                    btnClear.style.display = 'flex';
                    showToast('Link pasted from clipboard!', 'success');
                    fetchVideoDetails();
                } else {
                    showToast('Clipboard is empty!', 'error');
                }
            } else {
                showToast('Clipboard access not supported. Please paste manually.', 'error');
            }
        } catch (err) {
            showToast('Unable to read clipboard. Please paste manually.', 'error');
        }
    });

    // Event: Enter key on input
    urlInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchVideoDetails();
        }
    });

    // Event: Fetch Button
    btnFetch.addEventListener('click', () => {
        fetchVideoDetails();
    });

    // Event: Clear History
    btnClearHistory.addEventListener('click', () => {
        localStorage.removeItem('tiktok_download_history');
        loadHistory();
        showToast('Download history cleared.', 'success');
    });

    // Event: Close QR Modal
    qrModalClose.addEventListener('click', () => {
        qrModal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === qrModal) {
            qrModal.style.display = 'none';
        }
    });

    // Setup FAQ Accordion
    document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    /**
     * Fetch TikTok video details from backend API
     */
    async function fetchVideoDetails(targetUrl = null) {
        const rawUrl = targetUrl || urlInput.value.trim();

        if (!rawUrl) {
            showToast('Please enter or paste a TikTok video URL.', 'error');
            urlInput.focus();
            return;
        }

        // Validate basic URL pattern
        if (!rawUrl.includes('tiktok.com')) {
            showToast('Please enter a valid TikTok link (e.g. tiktok.com/...)', 'error');
            return;
        }

        // Show loading state
        loadingBox.style.display = 'block';
        resultSection.style.display = 'none';
        btnFetch.disabled = true;
        btnFetch.innerHTML = '<span>Processing...</span>';

        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ url: rawUrl })
            });

            const data = await response.json();

            if (data.success) {
                currentVideoData = data;
                renderResult(data);
                saveToHistory(data);
                showToast('Video processed successfully!', 'success');

                // Smooth scroll to result
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                showToast(data.message || 'Failed to extract video. Please check the URL.', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Network error while connecting to server.', 'error');
        } finally {
            loadingBox.style.display = 'none';
            btnFetch.disabled = false;
            btnFetch.innerHTML = '<span class="btn-icon">⚡</span><span>Download Now</span>';
        }
    }

    /**
     * Render result card with all download options
     */
    function renderResult(data) {
        const author = data.author || {};
        const stats = data.stats || {};
        const downloads = data.downloads || {};
        const music = data.music || {};

        // Preview Column
        const previewCol = document.getElementById('previewMediaContainer');
        if (data.is_images && data.images && data.images.length > 0) {
            previewCol.innerHTML = `
                <img src="${data.images[0]}" alt="TikTok Slide" id="previewImg">
                <div class="preview-badge">📸 Photo Slideshow (${data.images.length} Photos)</div>
            `;
        } else {
            previewCol.innerHTML = `
                <video id="previewVideo" controls playsinline poster="${data.cover || ''}">
                    <source src="${downloads.no_watermark_sd || downloads.no_watermark_hd || ''}" type="video/mp4">
                    Your browser does not support video playback.
                </video>
                <div class="preview-badge">✨ HD Preview</div>
                ${data.duration ? `<div class="duration-badge">${formatDuration(data.duration)}</div>` : ''}
            `;
        }

        // Creator Profile
        document.getElementById('creatorAvatar').src = author.avatar || 'https://www.tiktok.com/favicon.ico';
        document.getElementById('creatorNickname').textContent = author.nickname || 'TikTok User';
        document.getElementById('creatorUsername').textContent = '@' + (author.unique_id || 'user');

        // Stats
        document.getElementById('statViews').textContent = formatNumber(stats.plays || 0);
        document.getElementById('statLikes').textContent = formatNumber(stats.likes || 0);
        document.getElementById('statComments').textContent = formatNumber(stats.comments || 0);
        document.getElementById('statShares').textContent = formatNumber(stats.shares || 0);

        // Caption
        const captionTextEl = document.getElementById('captionText');
        captionTextEl.textContent = data.title || 'No caption provided.';

        // Copy Caption Button
        const btnCopyCaption = document.getElementById('btnCopyCaption');
        btnCopyCaption.onclick = () => {
            navigator.clipboard.writeText(data.title || '').then(() => {
                showToast('Caption copied to clipboard!', 'success');
            });
        };

        // Sound Bar with In-Page Audio Playback Preview
        const soundBar = document.getElementById('soundInfoBar');
        const audioUrl = downloads.audio || music.url || '';
        if (music.title || audioUrl) {
            soundBar.style.display = 'flex';
            document.getElementById('soundTitle').innerHTML = `
                <span>🎵 <strong>${music.title || 'TikTok Audio Track'}</strong> ${music.author ? '• ' + music.author : ''}</span>
                ${audioUrl ? `<audio controls style="height:30px; margin-left:auto; max-width:180px;"><source src="${audioUrl}" type="audio/mpeg"></audio>` : ''}
            `;
        } else {
            soundBar.style.display = 'none';
        }

        // Download Action Buttons
        const safeTitle = (data.title || 'tiktok_video').replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 40);
        const uniqueId = author.unique_id || 'tiktok';

        // 1. Without Watermark (HD)
        const btnDlHd = document.getElementById('btnDlHd');
        const hdUrl = downloads.no_watermark_hd || downloads.no_watermark_sd || '';
        if (hdUrl) {
            btnDlHd.href = `download.php?url=${encodeURIComponent(hdUrl)}&filename=${encodeURIComponent(uniqueId + '_hd_nowatermark_' + safeTitle + '.mp4')}&type=video`;
            btnDlHd.style.display = 'flex';
        } else {
            btnDlHd.style.display = 'none';
        }

        // 2. Without Watermark (SD)
        const btnDlSd = document.getElementById('btnDlSd');
        const sdUrl = downloads.no_watermark_sd || downloads.no_watermark_hd || '';
        if (sdUrl) {
            btnDlSd.href = `download.php?url=${encodeURIComponent(sdUrl)}&filename=${encodeURIComponent(uniqueId + '_sd_nowatermark_' + safeTitle + '.mp4')}&type=video`;
            btnDlSd.style.display = 'flex';
        } else {
            btnDlSd.style.display = 'none';
        }

        // 3. With Watermark
        const btnDlWm = document.getElementById('btnDlWm');
        const wmUrl = downloads.watermark || sdUrl || hdUrl;
        if (wmUrl) {
            btnDlWm.href = `download.php?url=${encodeURIComponent(wmUrl)}&filename=${encodeURIComponent(uniqueId + '_watermarked_' + safeTitle + '.mp4')}&type=video`;
            btnDlWm.style.display = 'flex';
        } else {
            btnDlWm.style.display = 'none';
        }

        // 4. Audio MP3 (Guaranteed URL with fallbacks)
        const btnDlAudio = document.getElementById('btnDlAudio');
        const finalAudioUrl = downloads.audio || music.url || sdUrl || hdUrl;
        if (finalAudioUrl) {
            btnDlAudio.href = `download.php?url=${encodeURIComponent(finalAudioUrl)}&filename=${encodeURIComponent(uniqueId + '_audio_' + safeTitle + '.mp3')}&type=audio`;
            btnDlAudio.style.display = 'flex';
        } else {
            btnDlAudio.style.display = 'none';
        }

        // 5. HD Cover Image (Guaranteed URL with fallbacks)
        const btnDlCover = document.getElementById('btnDlCover');
        const finalCoverUrl = downloads.cover || downloads.origin_cover || data.cover || data.origin_cover || (data.images && data.images[0]) || '';
        if (finalCoverUrl) {
            btnDlCover.href = `download.php?url=${encodeURIComponent(finalCoverUrl)}&filename=${encodeURIComponent(uniqueId + '_cover_' + safeTitle + '.jpg')}&type=image`;
            btnDlCover.style.display = 'flex';
        } else {
            btnDlCover.style.display = 'none';
        }

        // 6. QR Code Button
        const btnShowQr = document.getElementById('btnShowQr');
        btnShowQr.onclick = () => {
            const qrTarget = hdUrl || sdUrl || downloads.audio;
            qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrTarget)}`;
            qrVideoTitle.textContent = data.title ? data.title.substring(0, 50) + '...' : 'TikTok Video Download';
            qrModal.style.display = 'flex';
        };

        // Render Slideshow Photos if available
        const slidesSection = document.getElementById('slidesSection');
        const slidesGrid = document.getElementById('slidesGrid');
        if (data.is_images && data.images && data.images.length > 0) {
            slidesSection.style.display = 'block';
            slidesGrid.innerHTML = data.images.map((imgUrl, index) => `
                <div class="slide-item">
                    <img src="${imgUrl}" alt="Slide ${index + 1}" loading="lazy">
                    <a href="download.php?url=${encodeURIComponent(imgUrl)}&filename=${encodeURIComponent(uniqueId + '_photo_' + (index + 1) + '.jpg')}&type=image" class="slide-dl-overlay" title="Download Photo ${index + 1}">
                        <span style="color:#fff; font-size:1.4rem;">⬇️</span>
                    </a>
                </div>
            `).join('');
        } else {
            slidesSection.style.display = 'none';
        }

        resultSection.style.display = 'block';
    }

    /**
     * LocalStorage History Manager
     */
    function saveToHistory(data) {
        try {
            let history = JSON.parse(localStorage.getItem('tiktok_download_history') || '[]');
            
            // Check for duplicates
            history = history.filter(item => item.id !== data.id);

            // Add to front
            history.unshift({
                id: data.id,
                title: data.title,
                cover: data.cover,
                author: data.author,
                downloads: data.downloads,
                duration: data.duration,
                date: new Date().toISOString()
            });

            // Keep maximum 8 items
            if (history.length > 8) history = history.slice(0, 8);

            localStorage.setItem('tiktok_download_history', JSON.stringify(history));
            loadHistory();
        } catch (e) {
            console.error('History save error:', e);
        }
    }

    function loadHistory() {
        try {
            const history = JSON.parse(localStorage.getItem('tiktok_download_history') || '[]');
            if (history.length === 0) {
                historySection.style.display = 'none';
                return;
            }

            historySection.style.display = 'block';
            historyGrid.innerHTML = history.map(item => `
                <div class="history-card">
                    <div class="history-thumb-wrapper">
                        <img src="${item.cover || 'https://www.tiktok.com/favicon.ico'}" alt="Cover" loading="lazy">
                        ${item.duration ? `<div class="duration-badge">${formatDuration(item.duration)}</div>` : ''}
                    </div>
                    <div class="history-body">
                        <div class="history-author">@${item.author?.unique_id || 'tiktok'}</div>
                        <div class="history-caption">${item.title || 'TikTok Video'}</div>
                        <div class="history-actions">
                            <a href="download.php?url=${encodeURIComponent(item.downloads?.no_watermark_hd || item.downloads?.no_watermark_sd || '')}&filename=tiktok_hd_${item.id}.mp4&type=video" class="dl-btn dl-hd" style="padding:8px 12px; font-size:0.82rem; justify-content:center;">
                                <span>⚡ Download HD</span>
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.error('History load error:', e);
        }
    }

    /**
     * Helpers
     */
    function formatDuration(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }

    function formatNumber(num) {
        if (!num) return '0';
        if (num >= 1_000_000) return (num / 1_000_000).toFixed(1) + 'M';
        if (num >= 1_000) return (num / 1_000).toFixed(1) + 'K';
        return num.toString();
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? '✅' : (type === 'error' ? '⚠️' : 'ℹ️');
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
});
