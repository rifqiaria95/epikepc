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

function drawHotspots() {
    const container = document.querySelector('.spots_map');

    const hotSpots = [
        {
            'x': 25,
            'y': 28,
            content: 'Fresh Concept Construction Renovation'
        },
        {
            'x': 34,
            'y': 44,
            content: 'Reconstruction of Old Factory Building'
        },
        {
            'x': 21,
            'y': 71,
            content: 'Jazzy Elite Construction Shopping Center'
        },
        {
            'x': 41,
            'y': 68,
            content: 'Installation a  Supply System'
        },
        {
            'x': 50,
            'y': 83,
            content: 'Molestie nunc non blandit massa'
        },
        {
            'x': 63,
            'y': 53,
            content: 'Lorem ipsum dolor sit amet dolor sit'
        },
        {
            'x': 86,
            'y': 68,
            content: 'Duis aute irure dolor in reprehenderit'
        },
        {
            'x': 66,
            'y': 84,
            content: 'Excepteur sint occaecat cupida'
        }];

    function appendHotSpots() {
        for (let i = 0; i < hotSpots.length; i++) {
            let spot = document.createElement('div');
            spot.classList.add('hot-spot');
            container.append(spot);

            let speechBubble = document.createElement('span');
            speechBubble.classList.add('speech-bubble');
            container.append(speechBubble);
        }
         positionHotSpots();
    }
    appendHotSpots();

    function positionHotSpots() {
        const spots = document.querySelectorAll('.hot-spot');
        const speechBubbles = document.querySelectorAll('.speech-bubble');
        spots.forEach((spot, i) => {
            let xPos = hotSpots[i].x,
                yPos = hotSpots[i].y,
                content = hotSpots[i].content;

            spot.style.top = `${yPos}%`;
            spot.style.left = `${xPos}%`;
            speechBubbles[i].textContent = content;
            if (window.innerWidth >= 992) {
                speechBubbles[i].style.top = `${yPos}%`;
                speechBubbles[i].style.left = `${xPos}%`;
            } else {
                speechBubbles[i].style.top = `50%`;
                speechBubbles[i].style.left = `50%`;
            }

            document.body.addEventListener('click', e => {
                if (e.target === spot) {
                    speechBubbles[i].classList.add('active');
                } else {
                    speechBubbles[i].classList.remove('active');
                }
            })
        })
    }

    window.addEventListener('load', positionHotSpots);
    window.addEventListener('resize', positionHotSpots);
}



export default drawHotspots;