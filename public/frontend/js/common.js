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

import svg4everybody from "svg4everybody";
import LazyLoad from "vanilla-lazyload";
import AOS from 'aos';
import { setAnimationOptions } from "./modules/helpers";
import validateForms from "./modules/forms";
import { drawNav, scrollTop, setActivePageClass } from "./modules/nav";
import animateNumber from "./modules/count";
import { initGallery } from "./modules/gallery";
import initFilter from "./modules/filter";
import initMap from "./modules/map";

document.addEventListener('DOMContentLoaded', () => {
    const lazyload = new LazyLoad();
    svg4everybody();
    drawNav();
    scrollTop();
    setActivePageClass();

    window.addEventListener('load', () => {
        AOS.init();
    })
    setAnimationOptions();
    animateNumber();

    initGallery('.gallery_list');
    initGallery('.gallery_grid');
    initGallery('.gallery_masonry');
    initFilter('.gallery_masonry', '.gallery_filters-filter', {
        itemSelector: '.gallery_masonry-item'
    });

    validateForms('#newsletterForm');
    validateForms('#newsletterForm--widget');
    validateForms('[name="feedbackForm"]');
    validateForms('[name="replyForm"]');

    initMap('#map');

})