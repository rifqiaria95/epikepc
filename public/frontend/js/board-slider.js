/**
 * Board of Directors – Portrait Slider
 * Vanilla JS, no external dependencies.
 */
'use strict';

(function () {
    const BREAKPOINTS = [
        { minWidth: 1200, perView: 4, gap: 24 },
        { minWidth: 992, perView: 3, gap: 24 },
        { minWidth: 576, perView: 2, gap: 20 },
        { minWidth: 0, perView: 1, gap: 16 },
    ];

    function getConfig() {
        const width = window.innerWidth;
        return BREAKPOINTS.find((bp) => width >= bp.minWidth) || BREAKPOINTS[BREAKPOINTS.length - 1];
    }

    class BoardSlider {
        constructor(root) {
            this.root = root;
            this.viewport = root.querySelector('[data-board-viewport]');
            this.track = root.querySelector('[data-board-track]');
            this.slides = Array.from(root.querySelectorAll('[data-board-slide]'));
            this.prevBtn = root.querySelector('[data-board-prev]');
            this.nextBtn = root.querySelector('[data-board-next]');
            this.dotsContainer = root.querySelector('[data-board-dots]');

            this.index = 0;
            this.perView = 1;
            this.gap = 16;
            this.maxIndex = 0;
            this.dragStartX = 0;
            this.dragCurrentX = 0;
            this.isDragging = false;
            this.pointerId = null;

            if (!this.track || this.slides.length === 0) return;

            this.buildDots();
            this.bindEvents();
            this.update();
        }

        get slideCount() {
            return this.slides.length;
        }

        buildDots() {
            if (!this.dotsContainer) return;
            this.dotsContainer.innerHTML = '';

            const pageCount = () => this.maxIndex + 1;
            const render = () => {
                this.dotsContainer.innerHTML = '';
                for (let i = 0; i <= this.maxIndex; i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'board-directors__dot' + (i === this.index ? ' is-active' : '');
                    dot.setAttribute('aria-label', `Go to slide group ${i + 1}`);
                    dot.addEventListener('click', () => this.goTo(i));
                    this.dotsContainer.appendChild(dot);
                }
            };

            this.renderDots = render;
            render();
            this.pageCount = pageCount;
        }

        bindEvents() {
            this.prevBtn?.addEventListener('click', () => this.prev());
            this.nextBtn?.addEventListener('click', () => this.next());

            this.viewport?.addEventListener('pointerdown', (e) => this.onPointerDown(e));
            this.viewport?.addEventListener('pointermove', (e) => this.onPointerMove(e));
            this.viewport?.addEventListener('pointerup', (e) => this.onPointerUp(e));
            this.viewport?.addEventListener('pointercancel', (e) => this.onPointerUp(e));

            this.root.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.prev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.next();
                }
            });

            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this.update(), 150);
            });
        }

        onPointerDown(e) {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            this.isDragging = true;
            this.pointerId = e.pointerId;
            this.dragStartX = e.clientX;
            this.dragCurrentX = e.clientX;
            this.track.classList.add('is-dragging');
            this.viewport.setPointerCapture(e.pointerId);
        }

        onPointerMove(e) {
            if (!this.isDragging || e.pointerId !== this.pointerId) return;
            this.dragCurrentX = e.clientX;
            const offset = this.dragCurrentX - this.dragStartX;
            const base = this.getTranslateX(this.index);
            this.track.style.transform = `translate3d(${base + offset}px, 0, 0)`;
        }

        onPointerUp(e) {
            if (!this.isDragging || e.pointerId !== this.pointerId) return;
            this.isDragging = false;
            this.track.classList.remove('is-dragging');

            const diff = this.dragCurrentX - this.dragStartX;
            const threshold = Math.min(80, this.viewport.clientWidth * 0.15);

            if (diff < -threshold) {
                this.next();
            } else if (diff > threshold) {
                this.prev();
            } else {
                this.applyTransform();
            }

            try {
                this.viewport.releasePointerCapture(e.pointerId);
            } catch (_) {
                /* noop */
            }
        }

        getTranslateX(index) {
            const slideWidth = this.slides[0].getBoundingClientRect().width;
            return -(index * slideWidth);
        }

        applyTransform() {
            this.track.style.transform = `translate3d(${this.getTranslateX(this.index)}px, 0, 0)`;
        }

        updateLayout() {
            const { perView, gap } = getConfig();
            this.perView = Math.min(perView, this.slideCount);
            this.gap = gap;
            this.maxIndex = Math.max(0, this.slideCount - this.perView);

            if (this.index > this.maxIndex) {
                this.index = this.maxIndex;
            }

            const slideWidthPercent = 100 / this.perView;
            this.root.style.setProperty('--slide-width', `${slideWidthPercent}%`);

            this.renderDots?.();
            this.applyTransform();
            this.updateControls();
        }

        updateControls() {
            if (this.prevBtn) this.prevBtn.disabled = this.index <= 0;
            if (this.nextBtn) this.nextBtn.disabled = this.index >= this.maxIndex;

            const dots = this.dotsContainer?.querySelectorAll('.board-directors__dot');
            dots?.forEach((dot, i) => {
                dot.classList.toggle('is-active', i === this.index);
            });
        }

        goTo(index) {
            this.index = Math.max(0, Math.min(index, this.maxIndex));
            this.applyTransform();
            this.updateControls();
        }

        prev() {
            this.goTo(this.index - 1);
        }

        next() {
            this.goTo(this.index + 1);
        }

        update() {
            this.updateLayout();
        }
    }

    function init() {
        document.querySelectorAll('[data-board-slider]').forEach((el) => {
            if (!el._boardSlider) {
                el._boardSlider = new BoardSlider(el);
            } else {
                el._boardSlider.update();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
