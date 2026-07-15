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

import Shuffle from 'shufflejs';

function initFilter(container, trigger, options) {
    const containerEl = document.querySelector(container);

    if (containerEl) {
        const shuffleInstance = new Shuffle(containerEl, {...options});
        const triggers = document.querySelectorAll(trigger);

        triggers.forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                for (let i = 0; i < triggers.length; i++) {
                    triggers[i].classList.remove('current');
                }
                this.classList.add('current');
                const filter = this.dataset.target;
                shuffleInstance.filter(filter);
            })
        })
    }
}


export default initFilter;

