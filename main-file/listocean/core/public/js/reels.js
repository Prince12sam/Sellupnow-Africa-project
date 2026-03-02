/**
 * reels.js — IntersectionObserver-driven autoplay for the TikTok-style Reels feed.
 *
 * Behaviour:
 *  - When a .reel-wrapper scrolls >= 65% into view → play its .reel-main-video.
 *  - When it scrolls out → pause + rewind.
 *  - Attaches a timeupdate listener to the active video to update the
 *    per-card progress bar (.reel-progress-fill) and time display (.rl-time).
 *  - Muted by default; unmuted once the user interacts with the page.
 */

(function () {
    'use strict';

    /* ── State ──────────────────────────────────────────────────────────── */
    let userInteracted = false;

    document.addEventListener('click', function () {
        userInteracted = true;
    }, { once: true });

    /* ── Format seconds → M:SS ──────────────────────────────────────────── */
    function fmt(s) {
        s = Math.floor(s || 0);
        return Math.floor(s / 60) + ':' + (s % 60 < 10 ? '0' : '') + (s % 60);
    }

    /* ── Attach progress / time update to a video ────────────────────────── */
    function attachProgress(wrapper) {
        const video = wrapper.querySelector('video.reel-main-video');
        if (!video || video._rlProgressAttached) return;
        video._rlProgressAttached = true;

        video.addEventListener('timeupdate', function () {
            if (!video.duration) return;
            const fill = wrapper.querySelector('.reel-progress-fill');
            const time = wrapper.querySelector('.rl-time');
            if (fill) fill.style.width = (video.currentTime / video.duration * 100) + '%';
            if (time) time.textContent = fmt(video.currentTime) + ' / ' + fmt(video.duration);
        });
    }

    /* ── Play a reel wrapper ────────────────────────────────────────────── */
    function playReel(wrapper) {
        const video = wrapper.querySelector('video.reel-main-video');
        if (!video) return;

        video.muted = !userInteracted;
        attachProgress(wrapper);

        const promise = video.play();
        if (promise !== undefined) {
            promise.catch(function () {
                video.muted = true;
                video.play().catch(function () { /* silent */ });
            });
        }
    }

    /* ── Pause a reel wrapper ───────────────────────────────────────────── */
    function pauseReel(wrapper) {
        const video = wrapper.querySelector('video.reel-main-video');
        if (!video) return;
        video.pause();
        video.currentTime = 0;

        /* Reset progress bar */
        const fill = wrapper.querySelector('.reel-progress-fill');
        const time = wrapper.querySelector('.rl-time');
        if (fill) fill.style.width = '0%';
        if (time) time.textContent = '0:00';
    }

    /* ── Main init (DOM guaranteed ready) ───────────────────────────────── */
    function init() {
        const feedEl = document.getElementById('reelsFeed');

        /* Reinforce scroll-snap */
        if (feedEl) {
            feedEl.style.overflowY      = 'scroll';
            feedEl.style.scrollSnapType = 'y mandatory';
            feedEl.style.height         = '100vh';
        }

        /* root = feedEl so only the reel inside the snap viewport triggers */
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    playReel(entry.target);
                } else {
                    pauseReel(entry.target);
                }
            });
        }, {
            root:       feedEl || null,
            rootMargin: '0px',
            threshold:  0.65,
        });

        /* Observe all current wrappers */
        document.querySelectorAll('.reel-wrapper').forEach(function (el) {
            observer.observe(el);
        });

        /* Watch for wrappers added by infinite scroll */
        if (feedEl && window.MutationObserver) {
            const mutObs = new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1 && node.classList.contains('reel-wrapper')) {
                            observer.observe(node);
                        }
                    });
                });
            });
            mutObs.observe(feedEl, { childList: true });
        }
    }

    /* ── Bootstrap ──────────────────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
