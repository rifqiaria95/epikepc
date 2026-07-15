'use strict';

(function () {

    /* ────────────────────────────────────────────────
       Project data — lat/lng match real city locations
    ──────────────────────────────────────────────── */
    var PROJECTS = [
        {
            id: 'grand-meridian',
            lat: -6.200, lng: 106.816,
            category: 'architecture',
            categoryLabel: 'Architecture',
            name: 'Grand Meridian Tower',
            year: '2023', city: 'Jakarta',
            description: '48-storey mixed-use skyscraper featuring a sustainable double-skin facade and green sky gardens on every 12th floor.'
        },
        {
            id: 'harbor-bridge',
            lat: -7.257, lng: 112.752,
            category: 'infrastructure',
            categoryLabel: 'Infrastructure',
            name: 'Harbor Bridge Expansion',
            year: '2022', city: 'Surabaya',
            description: '3.2 km dual-carriageway cable-stayed bridge connecting the eastern industrial port to the main arterial road network.'
        },
        {
            id: 'sports-complex',
            lat: -6.917, lng: 107.619,
            category: 'civil',
            categoryLabel: 'Civil Engineering',
            name: 'Metropolitan Sports Complex',
            year: '2023', city: 'Bandung',
            description: '65,000-seat multi-sport stadium with retractable roof, underground parking for 4,000 vehicles, and integrated transit hub.'
        },
        {
            id: 'waterfront',
            lat: -6.966, lng: 110.416,
            category: 'infrastructure',
            categoryLabel: 'Infrastructure',
            name: 'Urban Waterfront Development',
            year: '2021', city: 'Semarang',
            description: 'Revitalisation of 4.8 km coastal area into a mixed-use public promenade with flood mitigation infrastructure.'
        },
        {
            id: 'city-hall',
            lat: -7.801, lng: 110.364,
            category: 'architecture',
            categoryLabel: 'Architecture',
            name: 'City Hall Renovation',
            year: '2022', city: 'Yogyakarta',
            description: 'Heritage conservation and seismic retrofit of a 1920s Dutch colonial civic building, preserving its facade while modernising all MEP systems.'
        },
        {
            id: 'processing-plant',
            lat: 1.148, lng: 104.030,
            category: 'specialty',
            categoryLabel: 'Specialty Services',
            name: 'Industrial Processing Plant',
            year: '2024', city: 'Batam',
            description: 'High-spec petrochemical facility with ISO Class 8 cleanrooms, blast-resistant control buildings, and fully automated safety systems.'
        },
        {
            id: 'coastal-highway',
            lat: -8.409, lng: 115.189,
            category: 'civil',
            categoryLabel: 'Civil Engineering',
            name: 'Coastal Highway Project',
            year: '2020', city: 'Bali',
            description: '18 km elevated expressway along the southern coastline, incorporating eco-tunnels for wildlife corridors and stormwater management.'
        },
        {
            id: 'green-campus',
            lat: -6.178, lng: 106.630,
            category: 'architecture',
            categoryLabel: 'Architecture',
            name: 'Green Tech Campus',
            year: '2024', city: 'Tangerang',
            description: 'LEED Platinum R&D campus for 3,200 employees featuring net-zero energy systems, living walls, and a central bio-retention pond.'
        },
        {
            id: 'water-treatment',
            lat: -6.235, lng: 107.010,
            category: 'specialty',
            categoryLabel: 'Specialty Services',
            name: 'Water Treatment Facility',
            year: '2021', city: 'Bekasi',
            description: '300 MLD advanced membrane bioreactor plant supplying potable water to 1.2 million residents across three municipalities.'
        }
    ];

    /* ────────────────────────────────────────────────
       Category colors
    ──────────────────────────────────────────────── */
    var CAT_COLORS = {
        architecture:  { bg: '#253C74', text: '#fff' },
        civil:         { bg: '#1a6b45', text: '#fff' },
        infrastructure:{ bg: '#b45309', text: '#fff' },
        specialty:     { bg: '#6d28d9', text: '#fff' }
    };

    function catColor(category) {
        return CAT_COLORS[category] || { bg: '#253C74', text: '#fff' };
    }

    /* ────────────────────────────────────────────────
       Marker icon factory  (light-map friendly)
    ──────────────────────────────────────────────── */
    function makeIcon(active, category) {
        var c = catColor(category || 'architecture');
        var outerFill  = active ? c.bg     : '#fff';
        var outerStroke= active ? '#fff'   : c.bg;
        var innerFill  = active ? '#FFdf08': c.bg;
        var svg = [
            '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">',
            '<circle cx="16" cy="16" r="13" fill="', outerFill, '" stroke="', outerStroke, '" stroke-width="3"/>',
            '<circle cx="16" cy="16" r="5.5" fill="', innerFill, '"/>',
            '</svg>'
        ].join('');

        return L.divIcon({
            html: '<div class="proj-lf-marker' + (active ? ' proj-lf-marker--active' : '') + '">' + svg + '</div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -18]
        });
    }

    /* ────────────────────────────────────────────────
       Main init
    ──────────────────────────────────────────────── */
    function init() {
        /* Fix Leaflet default icon paths to local folder */
        if (typeof L !== 'undefined') {
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconUrl: 'img/leaflet/marker-icon.png',
                iconRetinaUrl: 'img/leaflet/marker-icon-2x.png',
                shadowUrl: 'img/leaflet/marker-shadow.png'
            });
        }
        var container = document.getElementById('indonesia-leaflet-map');
        var panel = document.querySelector('[data-proj-map-panel]');
        var placeholder = document.querySelector('[data-proj-map-placeholder]');
        var detailEl = document.querySelector('[data-proj-map-detail]');

        if (!container || typeof L === 'undefined') return;

        /* Map setup */
        var map = L.map(container, {
            center: [-2.5, 118],
            zoom: 4,
            minZoom: 3,
            maxZoom: 10,
            scrollWheelZoom: false,
            zoomControl: true,
            attributionControl: false
        });

        /* Light tile layer — CartoDB Positron */
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        /* Fit roughly to Indonesia bounds */
        var indoBounds = L.latLngBounds(
            L.latLng(-11, 94),
            L.latLng(6, 142)
        );
        map.fitBounds(indoBounds, { padding: [10, 10] });

        /* Attribution small */
        L.control.attribution({ position: 'bottomright', prefix: false }).addTo(map);

        /* State */
        var activeId = null;
        var markers = {};
        var currentFilter = 'all';

        /* Show detail panel */
        function showDetail(project) {
            if (placeholder) placeholder.style.display = 'none';
            if (detailEl) {
                var c = catColor(project.category);
                detailEl.style.display = 'block';
                detailEl.className = 'proj-map__detail is-visible';
                detailEl.setAttribute('data-category', project.category);
                /* build HTML then wire up close button after render */
                detailEl.innerHTML =
                    '<div class="proj-map__detail-inner">' +
                        '<div class="proj-map__detail-head">' +
                            '<span class="proj-map__detail-badge" style="background:' + c.bg + ';color:' + c.text + ';">' + project.categoryLabel + '</span>' +
                            '<button class="proj-map__detail-close" aria-label="Close detail">&#x2715;</button>' +
                        '</div>' +
                        '<h3 class="proj-map__detail-title">' + project.name + '</h3>' +
                        '<div class="proj-map__detail-meta-row">' +
                            '<span class="proj-map__detail-meta-item">' +
                                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21c-4-4.5-6-8-6-11a6 6 0 0 1 12 0c0 3-2 6.5-6 11z"/><circle cx="12" cy="10" r="2"/></svg>' +
                                project.city +
                            '</span>' +
                            '<span class="proj-map__detail-meta-sep"></span>' +
                            '<span class="proj-map__detail-meta-item">' +
                                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' +
                                project.year +
                            '</span>' +
                        '</div>' +
                        '<div class="proj-map__detail-divider"></div>' +
                        '<p class="proj-map__detail-text">' + project.description + '</p>' +
                    '</div>';

                /* Wire close button */
                var closeBtn = detailEl.querySelector('.proj-map__detail-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        activeId = null;
                        resetDetail();
                        refreshIcons();
                    });
                }
            }
        }

        /* Reset panel */
        function resetDetail() {
            if (placeholder) placeholder.style.display = '';
            if (detailEl) {
                detailEl.style.display = 'none';
                detailEl.className = 'proj-map__detail';
                detailEl.innerHTML = '';
            }
        }

        /* Refresh all marker icons based on active/hidden state */
        function refreshIcons() {
            PROJECTS.forEach(function (p) {
                var m = markers[p.id];
                if (!m) return;
                m.setIcon(makeIcon(p.id === activeId, p.category));
            });
        }

        /* Add markers */
        PROJECTS.forEach(function (project) {
            var m = L.marker([project.lat, project.lng], {
                icon: makeIcon(false, project.category),
                title: project.name,
                alt: project.name + ', ' + project.city
            });

            m.on('click', function () {
                if (activeId === project.id) {
                    activeId = null;
                    resetDetail();
                } else {
                    activeId = project.id;
                    showDetail(project);
                }
                refreshIcons();
            });

            m.addTo(map);
            markers[project.id] = m;
        });

        /* Filter API — called from projFilter() in inline script */
        window.projMapFilter = function (filter) {
            currentFilter = filter;
            activeId = null;
            resetDetail();

            PROJECTS.forEach(function (project) {
                var m = markers[project.id];
                if (!m) return;
                var visible = (filter === 'all' || project.category === filter);
                if (visible) {
                    if (!map.hasLayer(m)) m.addTo(map);
                } else {
                    if (map.hasLayer(m)) map.removeLayer(m);
                }
            });

            refreshIcons();
        };

        /* Close detail when clicking on the map background */
        map.on('click', function () {
            if (activeId) {
                activeId = null;
                resetDetail();
                refreshIcons();
            }
        });

        /* Invalidate size after any AOS / layout transition */
        setTimeout(function () { map.invalidateSize(); }, 400);
        window.addEventListener('resize', function () { map.invalidateSize(); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
