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

import drawNotification from './notification';

const emailRegExp = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

function validateForms(formSelector, fieldSelector = '.field') {
    const form = document.querySelector(formSelector);
    const inputsArr = document.querySelectorAll(`${formSelector} ${fieldSelector}`);

    let text = '';

    let notification = {
        title: "Thank you!",
        showCloseButton: true,
        customClass: {
            popup: 'alert_popup',
            title: 'alert_popup-title',
            htmlContainer: 'alert_popup-content',
            closeButton: 'alert_popup-close'
        }
    };

    const valid = elem => !elem.classList.contains('error');


    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            for (let i = 0; i < inputsArr.length; i++) {
                const el = inputsArr[i];
                const value = el.value;
                if (el.classList.contains('required') && value === '') {
                    el.classList.add('error');
                } else if (el.dataset.type === 'email' && !emailRegExp.test(value)) {
                    el.classList.add('error');
                } else if (el.dataset.type === 'tel' && isNaN(+value)) {
                    el.classList.add('error');
                }

                el.addEventListener('input', () => {
                    el.classList.remove('error');
                });
            }

            if (Array.from(inputsArr).every(valid)) {
                inputsArr.forEach(el => {
                    el.classList.remove('error');
                    el.value = '';
                })
                if (form.dataset.type === 'newsletter') {
                    text = 'Now you\'re subscribed to our newsletter.';
                } else if (form.dataset.type === 'feedback') {
                    text = 'Your message has been sent. We\'ll reply you as soon as possible.';
                } else if (form.dataset.type === 'reply') {
                    text = 'Your comment is awaiting moderation.';
                }
                drawNotification(notification, text);
            }
        })
    }

}

export default validateForms;