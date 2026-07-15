/**
 * Axial
 * EPIKEPC is a universal template for factories, industry, construction, architecture, factories, mechanics, energy
 * Exclusively on https://1.envato.market/axial-html
 *
 * @encoding        UTF-8
 * @version         1.0.0
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Lamber Lilit (winter.rituel@gmail.com)
 * @support         help@merkulov.design
 **/
'use strict';

import {tns} from "tiny-slider/src/tiny-slider";

export function initTinySlider(options, navSelector = '.tns-arrows_arrow') {
    let slider = tns({...options});

    const sliderControls = document.querySelectorAll(navSelector);

    sliderControls.forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
        })
    })

    const changeNavState = function (info) {

        if (info.container.classList.contains('hero_thumbs')) {
            const slides = document.querySelectorAll('.hero_thumbs-thumb');
            slides[info.index].click();
        }

    }

    const test = function (info) {
      console.log(info.index)

        if (info.container.classList.contains('hero_thumbs')) {
            const slides = document.querySelectorAll('.hero_thumbs-thumb');
            const containerEl = document.querySelector('.hero');
            containerEl.style.backgroundImage = `url(${slides[info.index].dataset.bg})`;
        }
    }

    slider.events.on('indexChanged', changeNavState);

    slider.events.on('indexChanged', test);

    window.addEventListener('resize', slider.refresh);
}

export function initHeroSlider() {
    const heroSlider = tns({
        container: '.hero_slider',
        items: 1,
        mode: 'gallery',
        nav: true,
        navContainer: '.hero_slider-nav',
        controls: false,
        speed: 600,
        lazyload: true,
        touch: false,
        rewind: true,
    })

    const setSlideBg = function (info) {
        const slides = document.querySelectorAll('.hero_slider-slide');
        const containerEl = document.querySelector('.hero');
        containerEl.style.backgroundImage = `url(${slides[info.index].dataset.bg})`;
    }

    heroSlider.events.on('indexChanged', setSlideBg);

    window.addEventListener('resize', () => {
        heroSlider.refresh();
    })

    const thumbs = document.querySelectorAll('.hero_thumbs-thumb');

    thumbs.forEach((el, index) => {
        el.addEventListener('click', () => {
            heroSlider.goTo(index);
        })
    })

    function setActivityClass(info) {
        for (let i = 0; i < thumbs.length; i++) {
            thumbs[i].classList.remove('is-current')
        }
        thumbs[info.index].classList.add('is-current')
    }

    heroSlider.events.on('indexChanged', setActivityClass);
}

export function initThumbSliderNav() {
    const thumbNavSlider = tns({
        container: '.hero_thumbs',
        items: 1,
        mode: 'carousel',
        nav: false,
        controls: false,
        loop: false,
        mouseDrag: true,
        gutter: 5,
        responsive: {
            575: {
                gutter: 15,
                items: 2,
                edgePadding: 0,
            },
            992: {
                gutter: 30,
                edgePadding: setEdgePadding('.container--slider'),
            },
            1200: {
                items: 3,
                fixedWidth: 410
            },
        }
    });


    const dots = document.querySelectorAll('.hero_slider-nav_dot');

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            thumbNavSlider.goTo(index);
        })
    })
}


function setEdgePadding(container) {
    const margin = parseInt(window.getComputedStyle(document.querySelector(container)).marginLeft),
        padding = parseInt(window.getComputedStyle(document.querySelector(container)).paddingLeft);
    return margin + padding
}

export function setBackground(target, srcEl) {
    const targetElems = document.querySelectorAll(target),
        slides = document.querySelectorAll(srcEl);

    targetElems.forEach((el, index) => {
        el.style.backgroundImage = `url(${slides[index].dataset.bg})`;
    })
}
