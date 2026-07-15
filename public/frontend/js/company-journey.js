'use strict';

(function () {

    /* ─────────────────────────────────────────────────────
       Video: click poster → load iframe
    ───────────────────────────────────────────────────── */
    function initVideo() {
        var wrap = document.querySelector('[data-cp-video]');
        if (!wrap) return;

        var poster  = wrap.querySelector('[data-cp-poster]');
        var iframe  = wrap.querySelector('[data-cp-iframe]');

        if (!poster || !iframe) return;

        poster.addEventListener('click', function () {
            var src = iframe.getAttribute('data-src');
            if (src) {
                iframe.setAttribute('src', src);
                iframe.classList.add('is-active');
            }
            poster.classList.add('is-hidden');
        });
    }

    /* ─────────────────────────────────────────────────────
       Timeline: drag-to-scroll + nav buttons + progress
    ───────────────────────────────────────────────────── */
    function initTimeline() {
        var wrap = document.querySelector('[data-cj-timeline]');
        if (!wrap) return;

        var viewport = wrap.querySelector('[data-tl-viewport]');
        var prevBtn  = wrap.querySelector('[data-tl-prev]');
        var nextBtn  = wrap.querySelector('[data-tl-next]');
        var pager    = wrap.querySelector('[data-tl-pager]');
        var progFill = wrap.querySelector('[data-tl-progress]');
        var lineFill = wrap.querySelector('[data-tl-line-fill]');
        var hint     = wrap.querySelector('[data-tl-hint]');
        var items    = Array.from(wrap.querySelectorAll('[data-tl-item]'));

        if (!viewport) return;

        /* ── Drag state ── */
        var isDragging = false;
        var startX     = 0;
        var scrollStart = 0;
        var hintHidden = false;

        /* ── Step: scroll one "page" (visible width) ── */
        function step() {
            return viewport.clientWidth * 0.7;
        }

        /* ── Update all UI indicators ── */
        function update() {
            var sl  = viewport.scrollLeft;
            var max = viewport.scrollWidth - viewport.clientWidth;
            var pct = max > 0 ? sl / max : 0;

            if (progFill) progFill.style.width = (pct * 100).toFixed(1) + '%';
            if (lineFill) lineFill.style.width  = (pct * 100).toFixed(1) + '%';

            if (prevBtn) prevBtn.disabled = sl <= 0;
            if (nextBtn) nextBtn.disabled = sl >= max - 1;

            /* active item */
            items.forEach(function (item) {
                var rect = item.getBoundingClientRect();
                var vpRect = viewport.getBoundingClientRect();
                var itemCenter = rect.left + rect.width / 2;
                var vpCenter   = vpRect.left + vpRect.width / 2;
                item.classList.toggle('is-active', Math.abs(itemCenter - vpCenter) < rect.width * 0.6);
            });

            /* pager: current visible item index / total */
            if (pager && items.length) {
                var vpRect2 = viewport.getBoundingClientRect();
                var vpCx = vpRect2.left + vpRect2.width / 2;
                var closestIdx = 0;
                var minDist = Infinity;
                items.forEach(function (item, i) {
                    var r = item.getBoundingClientRect();
                    var d = Math.abs(r.left + r.width / 2 - vpCx);
                    if (d < minDist) { minDist = d; closestIdx = i; }
                });
                pager.textContent = (closestIdx + 1) + ' / ' + items.length;
            }

            /* hide hint after first scroll */
            if (!hintHidden && sl > 10) {
                hintHidden = true;
                if (hint) hint.classList.add('is-hidden');
            }
        }

        /* ── Scroll listener ── */
        viewport.addEventListener('scroll', update, { passive: true });

        /* ── Nav buttons ── */
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                viewport.scrollBy({ left: -step(), behavior: 'smooth' });
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                viewport.scrollBy({ left: step(), behavior: 'smooth' });
            });
        }

        /* ── Mouse drag ── */
        viewport.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            isDragging  = true;
            startX      = e.pageX;
            scrollStart = viewport.scrollLeft;
            viewport.classList.add('is-dragging');
            e.preventDefault();
        });

        window.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            var delta = e.pageX - startX;
            viewport.scrollLeft = scrollStart - delta;
        });

        window.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            viewport.classList.remove('is-dragging');
        });

        /* ── Touch drag ── */
        var touchStartX = 0;
        var touchScrollStart = 0;

        viewport.addEventListener('touchstart', function (e) {
            touchStartX      = e.touches[0].pageX;
            touchScrollStart = viewport.scrollLeft;
        }, { passive: true });

        viewport.addEventListener('touchmove', function (e) {
            var delta = touchStartX - e.touches[0].pageX;
            viewport.scrollLeft = touchScrollStart + delta;
        }, { passive: true });

        /* ── Keyboard ── */
        viewport.setAttribute('tabindex', '0');
        viewport.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { viewport.scrollBy({ left: -step() / 2, behavior: 'smooth' }); }
            if (e.key === 'ArrowRight') { viewport.scrollBy({ left:  step() / 2, behavior: 'smooth' }); }
        });

        /* ── Resize ── */
        window.addEventListener('resize', update);

        /* ── Init ── */
        update();
    }

    /* ─────────────────────────────────────────────────────
       Boot
    ───────────────────────────────────────────────────── */
    function boot() {
        initVideo();
        initTimeline();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

})();
