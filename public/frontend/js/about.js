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

import {initTinySlider} from "./modules/slider";

document.addEventListener('DOMContentLoaded', () => {
    initTinySlider({
        container: ".services_slider",
        items: 1,
        mode: 'gallery',
        axis: 'vertical',
        nav: true,
        controls: false,
        preventScrollOnTouch: 'force',
        speed: 700
    });
    initTinySlider({
        container: ".reviews_slider",
        controlsContainer: ".reviews_slider-controls",
        items: 1,
        mode: 'carousel',
        controls: true,
        nav: false,
        loop: false,
        responsive: {
            768: {
                gutter: 30,
                items: 2
            }
        }
    });
})