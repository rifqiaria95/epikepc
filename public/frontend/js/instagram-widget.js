(function () {
    'use strict';

    function bindFallbacks() {
        document.querySelectorAll('[data-ig-fallback]').forEach(function (img) {
            img.addEventListener('error', function () {
                if (img.dataset.igFailed) {
                    return;
                }
                img.dataset.igFailed = '1';
                img.src = img.getAttribute('data-ig-fallback');
            });
        });
    }

    function storyViewer() {
        var root = document.getElementById('ig-story-viewer');
        if (!root) {
            return;
        }

        var stories = window.IG_STORIES || [];
        var dialog = root.querySelector('.ig-viewer__dialog');
        var progress = root.querySelector('[data-ig-progress]');
        var imageEl = root.querySelector('[data-ig-image]');
        var videoEl = root.querySelector('[data-ig-video]');
        var captionEl = root.querySelector('[data-ig-caption]');
        var permalinkEl = root.querySelector('[data-ig-permalink]');
        var muteBtn = root.querySelector('[data-ig-mute]');
        var holdEl = root.querySelector('[data-ig-hold]');
        var index = 0;
        var timer = null;
        var startedAt = 0;
        var remaining = 0;
        var duration = 5000;
        var paused = false;
        var lastFocus = null;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function bars() {
            progress.innerHTML = stories.map(function () {
                return '<span><i></i></span>';
            }).join('');
        }

        function setProgress(current, ratio) {
            Array.prototype.forEach.call(progress.children, function (bar, i) {
                var fill = bar.firstElementChild;
                if (i < current) {
                    fill.style.width = '100%';
                    fill.style.transition = 'none';
                } else if (i === current) {
                    fill.style.transition = reduceMotion ? 'none' : 'width linear ' + Math.max(remaining, 0) + 'ms';
                    fill.style.width = (Math.min(1, Math.max(0, ratio)) * 100) + '%';
                } else {
                    fill.style.width = '0';
                    fill.style.transition = 'none';
                }
            });
        }

        function stopTimer() {
            if (timer) {
                window.clearTimeout(timer);
                timer = null;
            }
        }

        function play() {
            stopTimer();
            paused = false;
            startedAt = Date.now();
            setProgress(index, 1 - (remaining / duration));
            requestAnimationFrame(function () {
                setProgress(index, 1);
            });
            timer = window.setTimeout(next, remaining);
        }

        function pause() {
            if (paused) {
                return;
            }
            paused = true;
            remaining = Math.max(0, remaining - (Date.now() - startedAt));
            stopTimer();
            if (videoEl && !videoEl.hidden) {
                videoEl.pause();
            }
        }

        function resume() {
            if (!paused) {
                return;
            }
            if (videoEl && !videoEl.hidden) {
                videoEl.play().catch(function () {});
            }
            play();
        }

        function show(i) {
            if (!stories.length) {
                return;
            }
            index = (i + stories.length) % stories.length;
            var story = stories[index];
            stopTimer();
            remaining = story.duration_ms || 5000;
            duration = remaining;
            captionEl.textContent = story.caption || '';
            permalinkEl.href = story.permalink || '#';
            imageEl.hidden = true;
            videoEl.hidden = true;
            videoEl.removeAttribute('src');
            muteBtn.hidden = true;
            videoEl.muted = true;
            muteBtn.textContent = 'Unmute';

            if (story.is_video) {
                videoEl.src = story.media_url;
                videoEl.hidden = false;
                muteBtn.hidden = false;
                videoEl.onloadedmetadata = function () {
                    if (videoEl.duration && isFinite(videoEl.duration)) {
                        remaining = Math.min(15000, Math.max(2000, videoEl.duration * 1000));
                        duration = remaining;
                    }
                    videoEl.play().catch(function () {});
                    play();
                };
            } else {
                imageEl.src = story.media_url || story.preview_url;
                imageEl.alt = story.alt || '';
                imageEl.hidden = false;
                play();
            }

            setProgress(index, 0);
        }

        function next() {
            if (index >= stories.length - 1) {
                close();
                return;
            }
            show(index + 1);
        }

        function prev() {
            show(index - 1);
        }

        function trap(e) {
            if (e.key !== 'Tab' || root.hidden) {
                return;
            }
            var focusable = dialog.querySelectorAll('button, [href], video');
            if (!focusable.length) {
                return;
            }
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }

        function open(start) {
            lastFocus = document.activeElement;
            root.hidden = false;
            document.body.style.overflow = 'hidden';
            bars();
            show(start || 0);
            root.querySelector('[data-ig-close]').focus();
        }

        function close() {
            stopTimer();
            videoEl.pause();
            videoEl.removeAttribute('src');
            root.hidden = true;
            document.body.style.overflow = '';
            if (lastFocus && lastFocus.focus) {
                lastFocus.focus();
            }
        }

        document.querySelectorAll('[data-ig-story-open]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                open(parseInt(btn.getAttribute('data-ig-index'), 10) || 0);
            });
        });

        root.querySelectorAll('[data-ig-close]').forEach(function (el) {
            el.addEventListener('click', close);
        });
        root.querySelector('[data-ig-next]').addEventListener('click', next);
        root.querySelector('[data-ig-prev]').addEventListener('click', prev);

        muteBtn.addEventListener('click', function () {
            videoEl.muted = !videoEl.muted;
            muteBtn.textContent = videoEl.muted ? 'Unmute' : 'Mute';
        });

        holdEl.addEventListener('pointerdown', pause);
        holdEl.addEventListener('pointerup', resume);
        holdEl.addEventListener('pointerleave', resume);
        root.addEventListener('focusout', function () {
            if (!root.contains(document.activeElement)) {
                pause();
            }
        });
        root.addEventListener('focusin', resume);

        document.addEventListener('keydown', function (e) {
            if (root.hidden) {
                return;
            }
            if (e.key === 'Escape') {
                close();
            } else if (e.key === 'ArrowRight') {
                next();
            } else if (e.key === 'ArrowLeft') {
                prev();
            } else {
                trap(e);
            }
        });

        var startX = 0;
        holdEl.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
        }, { passive: true });
        holdEl.addEventListener('touchend', function (e) {
            var diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                diff > 0 ? next() : prev();
            }
        }, { passive: true });
    }

    bindFallbacks();
    storyViewer();
})();
