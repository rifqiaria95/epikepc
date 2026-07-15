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

function drawProcessSteps() {
    const steps = document.querySelectorAll('.progress-step');
    const stepsSpotsUnderlay = document.querySelectorAll('.progress-marker_spot--underlay');
    const container = document.querySelector('.process_steps');
    const directionClass = 'progress-tracker--vertical';

    function makeResponsive() {
        if (window.innerWidth >= 992) {
            container.classList.remove(directionClass);
        } else {
            container.classList.add(directionClass);
        }
    }

    window.addEventListener('load', makeResponsive);
    window.addEventListener('resize', makeResponsive);

    function _setTimer(delay = 3000) {
        let duration = 4500 - delay;
        steps[0].classList.add('is-complete');
        steps.forEach((item, i) => {
            stepsSpotsUnderlay[i].style.animationDelay = `${delay}ms`;
            let spot = item.children;
            setTimeout(() => {
                if(item.nextElementSibling !== null) {
                    _toggleClass(item.nextElementSibling, 'is-complete');

                }
            }, delay * i)
        })
    }

    function _toggleClass(el, className) {

        if (el.classList) {
            el.classList.toggle(className);
        } else {
            let classes = el.className.split(' ');
            let existingIndex = classes.indexOf(className);

            if (existingIndex >= 0)
                classes.splice(existingIndex, 1);
            else
                classes.push(className);

            el.className = classes.join(' ');
        }

    }

    function triggerSteps() {
        const options = {
            root: document.querySelector('.process'),
            rootMargin: '0px',
            threshold: 0.5
        }
        let observer = new IntersectionObserver(_setTimer, options);
        observer.observe(container)
    }

    window.addEventListener('load', triggerSteps);

}

export default drawProcessSteps;

