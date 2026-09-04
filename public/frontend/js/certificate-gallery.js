(function (window, document) {
    'use strict';

    function parseConfig(root) {
        try {
            return JSON.parse(root.getAttribute('data-config') || '{}');
        } catch (e) {
            return {};
        }
    }

    function parseItems(root) {
        try {
            return (JSON.parse(root.getAttribute('data-items') || '[]') || [])
                .filter(function (item) { return item && item.image_url; });
        } catch (e) {
            return [];
        }
    }

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function CertificateGallery(root) {
        if (root.dataset.certInitialized === 'true') {
            return;
        }
        root.dataset.certInitialized = 'true';

        this.root = root;
        this.config = parseConfig(root);
        this.items = parseItems(root);

        this.viewport = root.querySelector('[data-cert-viewport]');
        this.track = root.querySelector('[data-cert-track]');
        this.controls = root.querySelector('[data-cert-controls]');
        this.prevBtn = root.querySelector('[data-cert-prev]');
        this.nextBtn = root.querySelector('[data-cert-next]');
        this.dotsHost = root.querySelector('[data-cert-dots]');
        this.live = root.querySelector('[data-cert-live]');

        this.lightboxRoot = document.getElementById('certLightbox');
        this.lightbox = this.lightboxRoot ? new CertificateLightbox(this.lightboxRoot, this.items) : null;

        this.index = 0;
        this.slideStep = 0;
        this.gap = 18;
        this.reducedMotion = prefersReducedMotion();
        this.threshold = this.config.gesture_threshold_px || 50;
        this.drag = null;

        this.init();
    }

    CertificateGallery.prototype.init = function () {
        if (!this.items.length || !this.track) {
            return;
        }

        this.thumbs = [];
        this.attachThumbs();
        this.bind();
        this.measure();
        this.render(false);

        var self = this;
        window.addEventListener('resize', function () {
            clearTimeout(self._resizeTimer);
            self._resizeTimer = setTimeout(function () {
                self.measure();
                self.index = self.clampIndex(self.index);
                self.render(false);
            }, 200);
        });
    };

    // Thumbnails are rendered server-side (progressive enhancement / no-JS fallback).
    // This only binds interaction behavior to the already-present markup; it never
    // creates or duplicates DOM nodes.
    CertificateGallery.prototype.attachThumbs = function () {
        var self = this;
        var nodes = Array.prototype.slice.call(this.track.querySelectorAll('.cert-thumb'));

        nodes.forEach(function (thumb, i) {
            var item = self.items[i];
            var img = thumb.querySelector('img');

            if (img) {
                if (img.complete && img.naturalWidth === 0) {
                    // Already failed to load before this script attached.
                    thumb.classList.add('is-broken');
                } else {
                    img.addEventListener('error', function () {
                        thumb.classList.add('is-broken');
                        if (window.console && console.warn) {
                            console.warn('Certificate thumbnail failed to load:', item && item.id);
                        }
                        self.measure();
                        self.render(false);
                    });
                }
            }

            // Keep click for keyboard activation; pointer path opens in endDrag
            // so real mouse/touch clicks still work when setPointerCapture suppresses click.
            thumb.addEventListener('click', function (e) {
                if (self._suppressClick) {
                    e.preventDefault();
                    return;
                }
                if (self.lightbox) {
                    self.lightbox.open(i, thumb);
                }
            });

            self.thumbs.push(thumb);
        });
    };

    CertificateGallery.prototype.visibleThumbs = function () {
        return this.thumbs.filter(function (t) { return !t.classList.contains('is-broken'); });
    };

    CertificateGallery.prototype.measure = function () {
        var visible = this.visibleThumbs();
        if (!visible.length) {
            this.slideStep = 0;
            return;
        }
        this.slideStep = visible[0].getBoundingClientRect().width + this.gap;
        this.buildDots();
    };

    CertificateGallery.prototype.visibleCount = function () {
        if (!this.slideStep) {
            return 1;
        }
        return Math.max(1, Math.floor((this.viewport.clientWidth + this.gap) / this.slideStep));
    };

    CertificateGallery.prototype.maxIndex = function () {
        var total = this.visibleThumbs().length;
        return Math.max(0, total - this.visibleCount());
    };

    CertificateGallery.prototype.clampIndex = function (i) {
        return Math.min(Math.max(i, 0), this.maxIndex());
    };

    CertificateGallery.prototype.buildDots = function () {
        var self = this;
        var visible = this.visibleThumbs();
        var totalPages = this.maxIndex() + 1;

        if (!this.dotsHost) {
            return;
        }

        this.dotsHost.innerHTML = '';

        if (visible.length <= this.visibleCount()) {
            if (this.controls) {
                this.controls.hidden = true;
            }
            return;
        }

        if (this.controls) {
            this.controls.hidden = false;
        }

        for (var i = 0; i < totalPages; i++) {
            var dot = document.createElement('li');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cert-dot';
            btn.setAttribute('aria-label', 'Go to certificate ' + (i + 1));
            btn.setAttribute('role', 'tab');
            (function (idx) {
                btn.addEventListener('click', function () { self.goTo(idx); });
            })(i);
            dot.appendChild(btn);
            this.dotsHost.appendChild(dot);
        }
    };

    CertificateGallery.prototype.render = function (withTransition) {
        this.index = this.clampIndex(this.index);
        this.track.style.transition = withTransition && !this.reducedMotion
            ? 'transform .45s cubic-bezier(.22,.68,.36,1)'
            : 'none';
        this.track.style.transform = 'translateX(' + (-this.index * this.slideStep) + 'px)';

        if (this.dotsHost) {
            var dots = this.dotsHost.querySelectorAll('.cert-dot');
            dots.forEach(function (d, i) {
                if (i === this.index) {
                    d.setAttribute('aria-current', 'true');
                } else {
                    d.removeAttribute('aria-current');
                }
            }, this);
        }

        if (this.prevBtn) {
            this.prevBtn.disabled = this.index === 0;
        }
        if (this.nextBtn) {
            this.nextBtn.disabled = this.index >= this.maxIndex();
        }

        if (this.live) {
            var total = this.maxIndex() + 1;
            this.live.textContent = total > 1
                ? 'Showing certificate page ' + (this.index + 1) + ' of ' + total
                : '';
        }
    };

    CertificateGallery.prototype.goTo = function (i) {
        this.index = this.clampIndex(i);
        this.render(true);
    };

    CertificateGallery.prototype.bind = function () {
        var self = this;

        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function () { self.goTo(self.index - 1); });
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function () { self.goTo(self.index + 1); });
        }

        this.viewport.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') { e.preventDefault(); self.goTo(self.index + 1); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); self.goTo(self.index - 1); }
            if (e.key === 'Enter' || e.key === ' ') {
                var focused = e.target.closest && e.target.closest('.cert-thumb');
                if (!focused && document.activeElement) {
                    focused = document.activeElement.closest
                        ? document.activeElement.closest('.cert-thumb')
                        : null;
                }
                // When viewport itself is focused, open the first visible thumb.
                if (!focused) {
                    focused = self.visibleThumbs()[self.index] || self.thumbs[0];
                }
                if (focused && self.lightbox) {
                    e.preventDefault();
                    var idx = self.thumbs.indexOf(focused);
                    if (idx < 0) idx = self.index;
                    self.lightbox.open(idx, focused);
                }
            }
        });

        this.viewport.addEventListener('pointerdown', function (e) {
            var thumb = e.target.closest('.cert-thumb');
            if (!thumb || e.button === 2) {
                return;
            }
            self.drag = {
                startX: e.clientX,
                base: -self.index * self.slideStep,
                current: -self.index * self.slideStep,
                moved: false,
                captured: false,
                pointerId: e.pointerId,
                thumb: thumb,
                thumbIndex: self.thumbs.indexOf(thumb),
            };
            self._suppressClick = false;
        });

        this.viewport.addEventListener('pointermove', function (e) {
            if (!self.drag || self.drag.pointerId !== e.pointerId) {
                return;
            }
            var delta = e.clientX - self.drag.startX;

            // Only treat as a drag after the gesture threshold so a normal
            // click is never swallowed by setPointerCapture.
            if (!self.drag.moved && Math.abs(delta) < self.threshold) {
                return;
            }

            if (!self.drag.moved) {
                self.drag.moved = true;
                self._suppressClick = true;
                self.track.style.transition = 'none';
                self.viewport.classList.add('is-dragging');
                try {
                    self.viewport.setPointerCapture(e.pointerId);
                    self.drag.captured = true;
                } catch (err) {}
            }

            self.drag.current = self.drag.base + delta;
            self.track.style.transform = 'translateX(' + self.drag.current + 'px)';
        });

        var endDrag = function (e) {
            if (!self.drag || self.drag.pointerId !== e.pointerId) {
                return;
            }

            var drag = self.drag;
            self.drag = null;
            self.viewport.classList.remove('is-dragging');

            if (drag.captured) {
                try { self.viewport.releasePointerCapture(e.pointerId); } catch (err) {}
            }

            if (!drag.moved) {
                // Open on pointerup, then ignore the trailing click so it cannot
                // hit the newly-opened overlay and immediately close it.
                self._suppressClick = true;
                window.setTimeout(function () { self._suppressClick = false; }, 400);
                if (self.lightbox && drag.thumbIndex >= 0) {
                    self.lightbox.open(drag.thumbIndex, drag.thumb);
                }
                return;
            }

            var delta = drag.current - drag.base;
            var threshold = self.slideStep * 0.18;

            if (delta < -threshold) {
                self.goTo(self.index + 1);
            } else if (delta > threshold) {
                self.goTo(self.index - 1);
            } else {
                self.render(true);
            }
        };

        this.viewport.addEventListener('pointerup', endDrag);
        this.viewport.addEventListener('pointercancel', endDrag);
    };

    function CertificateLightbox(root, items) {
        this.root = root;
        this.items = items;
        this.index = 0;
        this.lastFocus = null;
        this.drag = null;
        this.reducedMotion = prefersReducedMotion();

        // Escape any page stacking context (header is z-index 100000) so the
        // overlay always paints above the sticky navbar.
        if (root.parentElement !== document.body) {
            document.body.appendChild(root);
        }

        this.viewport = root.querySelector('[data-lb-viewport]');
        this.track = root.querySelector('[data-lb-track]');
        this.closeBtn = root.querySelector('[data-lb-close]');
        this.prevBtn = root.querySelector('[data-lb-prev]');
        this.nextBtn = root.querySelector('[data-lb-next]');
        this.titleEl = root.querySelector('[data-lb-title]');
        this.metaEl = root.querySelector('[data-lb-meta]');
        this.counterEl = root.querySelector('[data-lb-counter]');

        this.buildSlides();
        this.bind();
    }

    CertificateLightbox.prototype.buildSlides = function () {
        var self = this;

        this.items.forEach(function (item) {
            var slide = document.createElement('div');
            slide.className = 'cert-lightbox__slide';

            var img = document.createElement('img');
            img.alt = item.image_alt || item.title || '';
            img.loading = 'lazy';
            img.addEventListener('error', function () {
                slide.style.display = 'none';
            });
            img.dataset.src = item.image_url;

            slide.appendChild(img);
            self.track.appendChild(slide);
        });

        this.slides = Array.from(this.track.children);
    };

    CertificateLightbox.prototype.ensureLoaded = function (i) {
        var img = this.slides[i] && this.slides[i].querySelector('img');
        if (img && !img.src && img.dataset.src) {
            img.src = img.dataset.src;
        }
    };

    CertificateLightbox.prototype.render = function (withTransition) {
        var w = this.viewport.clientWidth;
        this.track.style.transition = withTransition && !this.reducedMotion
            ? 'transform .4s cubic-bezier(.22,.68,.36,1)'
            : 'none';
        this.track.style.transform = 'translateX(' + (-this.index * w) + 'px)';

        this.ensureLoaded(this.index - 1);
        this.ensureLoaded(this.index);
        this.ensureLoaded(this.index + 1);

        var item = this.items[this.index];
        if (item) {
            if (this.titleEl) this.titleEl.textContent = item.title || '';
            if (this.metaEl) this.metaEl.textContent = item.issuer ? 'Issued by ' + item.issuer : '';
        }
        if (this.counterEl) {
            this.counterEl.textContent = (this.index + 1) + ' / ' + this.items.length;
        }
        if (this.prevBtn) this.prevBtn.disabled = this.index === 0;
        if (this.nextBtn) this.nextBtn.disabled = this.index >= this.items.length - 1;
    };

    CertificateLightbox.prototype.goTo = function (i) {
        this.index = Math.min(Math.max(i, 0), this.items.length - 1);
        this.render(true);
    };

    CertificateLightbox.prototype.open = function (index, trigger) {
        this.lastFocus = trigger || document.activeElement;
        this.index = index;
        this.openedAt = Date.now();
        this.root.classList.add('is-open');
        this.root.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        this.render(false);
        this.closeBtn.focus();
        document.addEventListener('keydown', this._onKeydown = this.onKeydown.bind(this));
    };

    CertificateLightbox.prototype.close = function () {
        this.root.classList.remove('is-open');
        this.root.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (this._onKeydown) {
            document.removeEventListener('keydown', this._onKeydown);
        }
        if (this.lastFocus && this.lastFocus.focus) {
            this.lastFocus.focus();
        }
    };

    CertificateLightbox.prototype.onKeydown = function (e) {
        if (!this.root.classList.contains('is-open')) {
            return;
        }
        if (e.key === 'Escape') {
            e.preventDefault();
            this.close();
        }
        if (e.key === 'ArrowRight') {
            this.goTo(this.index + 1);
        }
        if (e.key === 'ArrowLeft') {
            this.goTo(this.index - 1);
        }
        if (e.key === 'Tab') {
            this.trapFocus(e);
        }
    };

    CertificateLightbox.prototype.trapFocus = function (e) {
        var focusable = [this.closeBtn, this.prevBtn, this.nextBtn].filter(function (el) {
            return el && !el.disabled;
        });
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
    };

    CertificateLightbox.prototype.bind = function () {
        var self = this;

        this.closeBtn.addEventListener('click', function () { self.close(); });
        this.root.addEventListener('click', function (e) {
            // Ignore the trailing click from the thumb tap that opened us.
            if (self.openedAt && Date.now() - self.openedAt < 400) {
                return;
            }
            if (e.target === self.root) {
                self.close();
            }
        });

        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function () { self.goTo(self.index - 1); });
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function () { self.goTo(self.index + 1); });
        }

        this.viewport.addEventListener('pointerdown', function (e) {
            self.drag = { startX: e.clientX, base: -self.index * self.viewport.clientWidth, moved: false, pointerId: e.pointerId };
            self.track.style.transition = 'none';
            try { self.viewport.setPointerCapture(e.pointerId); } catch (err) {}
        });

        this.viewport.addEventListener('pointermove', function (e) {
            if (!self.drag || self.drag.pointerId !== e.pointerId) {
                return;
            }
            var delta = e.clientX - self.drag.startX;
            if (Math.abs(delta) > 10) {
                self.drag.moved = true;
            }
            self.track.style.transform = 'translateX(' + (self.drag.base + delta) + 'px)';
        });

        var endDrag = function (e) {
            if (!self.drag || self.drag.pointerId !== e.pointerId) {
                return;
            }
            var delta = (self.drag.base + (e.clientX - self.drag.startX)) - self.drag.base;
            var threshold = self.viewport.clientWidth * 0.15;
            try { self.viewport.releasePointerCapture(e.pointerId); } catch (err) {}

            if (delta < -threshold) {
                self.goTo(self.index + 1);
            } else if (delta > threshold) {
                self.goTo(self.index - 1);
            } else {
                self.render(true);
            }

            self.drag = null;
        };

        this.viewport.addEventListener('pointerup', endDrag);
        this.viewport.addEventListener('pointercancel', endDrag);

        window.addEventListener('resize', function () {
            if (self.root.classList.contains('is-open')) {
                self.render(false);
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('certificateGallery');
        if (root) {
            new CertificateGallery(root);
        }
    });
})(window, document);
