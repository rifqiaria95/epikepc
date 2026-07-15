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

import ProgressBar from 'progressbar.js/dist/progressbar';
import { inViewport } from './helpers';

function initProgressbar(container = '.skills') {
    const progressBars = document.querySelectorAll('.progressLine');
    const rootEl = document.querySelector(container);
    progressBars.forEach(bar => {
        let id = bar.getAttribute('id');
        let value = bar.dataset.value;
        let color = bar.dataset.fill;
        let limit = value / 100;

        let lineBar = new ProgressBar.Line(`#${id}`, {
            strokeWidth: 7.5,
            trailWidth: 7.5,
            from: {color: '#f8f8f8'},
            to: {color: color},
            text: {
                value: '0',
                className: 'progress-text',
                style: {
                    position: 'absolute',
                    right: '0',
                    padding: 0,
                    margin: 0,
                    transform: null
                }
            },
            step: (state, shape) => {
                shape.path.setAttribute("stroke", state.color);
                shape.setText(`${value}%`);
            }
        });

        function animateProgress() {
            lineBar.animate(limit, {
                duration: 500
            });
        }

        window.addEventListener('scroll', checkVisibility);
        window.addEventListener('load', checkVisibility);

        function checkVisibility() {
            if(!inViewport(rootEl)) {
               animateProgress();
            }
        }

    })

}

export default initProgressbar;