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

export function inViewport(el) {
    let rect = el.getBoundingClientRect();
    return rect.bottom < 0 || rect.right < 0 || rect.left > window.innerWidth || rect.top > window.innerHeight;
}

export function setAnimationOptions() {
    const animatedElems = document.querySelectorAll('[data-aos]');
    animatedElems.forEach(el => {
        el.setAttribute('data-aos-once', 'true');
        el.setAttribute('data-aos-offset', '0');
        if (!el.dataset.aosDelay) {
            el.setAttribute('data-aos-duration', '600');
        }
    })
}

