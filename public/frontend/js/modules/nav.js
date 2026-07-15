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

export function drawNav() {
    const navbar = document.querySelector('.header_navbar'),
        menuTrigger = document.querySelector('.hamburger'),
        menuList = document.querySelector('.header_navbar-nav_list'),
        dropdownTrigger = document.querySelectorAll('.dropdown-trigger'),
        dropdownContent = document.querySelectorAll('.dropdown-content');

    function closeMenu() {
        menuTrigger.classList.remove('active');
        menuList.classList.remove('active');
        document.body.classList.remove('fixed');
    }

    menuTrigger.addEventListener('click', () => {
        menuTrigger.classList.toggle('active');
        menuList.classList.toggle('active');
        document.body.classList.toggle('fixed');
    })

    dropdownTrigger.forEach((trigger, i) => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            trigger.classList.toggle('active');
            dropdownContent[i].classList.toggle('active');
        })
    })

    function makeNavSticky() {
        if (window.innerWidth > 991 && window.pageYOffset > 0) {
            navbar.classList.add('sticky');
        } else {
            navbar.classList.remove('sticky');
        }
    }

    window.addEventListener('resize', closeMenu);
    window.addEventListener('load', makeNavSticky);
    window.addEventListener('resize', makeNavSticky);
    window.addEventListener('scroll', makeNavSticky);

    setDropdown();


}

function setDropdown() {
    const triggers = document.querySelectorAll('.dropdown-trigger'),
        dropdownMenus = document.querySelectorAll('.dropdown-content');

    triggers.forEach((trigger, index) => {
        document.body.addEventListener('mouseover', (e) => {
            if (e.target === trigger) {
                for (let i = 0; i < dropdownMenus.length; i++) {
                    if (dropdownMenus[i].classList.contains('visible')) {
                        dropdownMenus[i].classList.remove('visible');
                    }
                }
                dropdownMenus[index].classList.add('visible');
            }
        })
    })

    dropdownMenus.forEach(menu => {
        document.body.addEventListener('mouseover', (e) => {
            if (e.target === menu || e.target.classList.contains('link--single')) {
                menu.classList.remove('visible');
            }
        })
        document.body.addEventListener('mouseleave', (e) => {
            if (e.target === menu || !e.target.classList.contains('header_navbar')) {
                menu.classList.remove('visible');
            }
        })
    })
}

export function scrollTop() {
    const btn = document.querySelector('#scrollToTop');
    btn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

export function setActivePageClass() {
    const header = document.querySelector('.header');
    const navLinks = document.querySelectorAll('.link--single');

    navLinks.forEach(link => {
        if (link.dataset.page === header.dataset.page) {
            link.classList.add('active');
        }
    })

}