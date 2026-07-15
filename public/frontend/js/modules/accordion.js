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

import { Collapse } from "bootstrap";

function drawAccordion() {
    const triggers = document.querySelectorAll('.accordion-trigger');
    const wrappers = document.querySelectorAll('.accordion-wrapper');
    const icons = document.querySelectorAll('.accordion-trigger_icon');


    function checkIconClass() {
        wrappers.forEach((el, index) => {
            if (el.classList.contains('expanded')) {
                icons[index].classList.remove('icon-plus');
                icons[index].classList.add('icon-minus');
            } else {
                icons[index].classList.add('icon-plus');
                icons[index].classList.remove('icon-minus');
            }
        })
    }

    triggers.forEach((el, i) => {
        el.addEventListener('click', e => {
            e.preventDefault();
            wrappers[i].classList.toggle('expanded');
            checkIconClass();
        })
    })

}

export default drawAccordion;