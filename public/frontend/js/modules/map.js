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

const _KEY = '';

import { Loader } from "@googlemaps/js-api-loader";
import {mapNightTheme} from "./map-theme";

function initMap(containerSelector) {
    const loader = new Loader({
        apiKey: _KEY,
        version: "weekly",

    });
    const mapContainer = document.querySelector(containerSelector);
    const defaultMarker = { lat: 40.74881584652125, lng: -73.98561648634562 };

    if (mapContainer) {
        loader.load().then(() => {
            const map = new google.maps.Map(mapContainer, {
                center: { lat: 40.74956081164776, lng: -73.98700531672688 },
                zoom: 12,
                styles: [...mapNightTheme],
            });
            const marker = new google.maps.Marker({
                position: defaultMarker,
                map: map,
                icon: './img/pin-static.svg'
            });
        });
    }
}



export default initMap;