'use strict';

(function () {

    /* Prefer server-provided markers; keep empty fallback (no hardcoded demo data). */
    var PROJECTS = Array.isArray(window.PROJECT_MAP_DATA) ? window.PROJECT_MAP_DATA : [];
    var MAP_CONFIG = window.PROJECT_MAP_CONFIG || {};
    var FILTER_MODE = MAP_CONFIG.filterMode || 'category';

    var CAT_COLORS = {
        architecture:   { bg: '#253C74', text: '#fff' },
        civil:          { bg: '#1a6b45', text: '#fff' },
        infrastructure: { bg: '#b45309', text: '#fff' },
        specialty:      { bg: '#6d28d9', text: '#fff' },
        project:        { bg: '#253C74', text: '#fff' }
    };

    var STATUS_COLORS = {
        ongoing:   { bg: '#b45309', text: '#fff' },
        completed: { bg: '#1a6b45', text: '#fff' }
    };

    function catColor(category) {
        return CAT_COLORS[category] || CAT_COLORS.project;
    }

    function statusColor(status) {
        return STATUS_COLORS[status] || STATUS_COLORS.completed;
    }

    function markerColor(project) {
        if (FILTER_MODE === 'status') {
            return statusColor(project.status);
        }
        return catColor(project.category);
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function makeIcon(active, project) {
        var c = markerColor(project || {});
        var outerFill = active ? c.bg : '#fff';
        var outerStroke = active ? '#fff' : c.bg;
        var innerFill = active ? '#FFdf08' : c.bg;
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

    function matchesFilter(project, filter) {
        if (!filter || filter === 'all') {
            return true;
        }

        if (FILTER_MODE === 'status') {
            return project.status === filter;
        }

        return project.category === filter;
    }

    function init() {
        if (typeof L !== 'undefined') {
            delete L.Icon.Default.prototype._getIconUrl;
            var leafletBase = (MAP_CONFIG.leafletBasePath || '/frontend/img/leaflet').replace(/\/$/, '');
            L.Icon.Default.mergeOptions({
                iconUrl: leafletBase + '/marker-icon.png',
                iconRetinaUrl: leafletBase + '/marker-icon-2x.png',
                shadowUrl: leafletBase + '/marker-shadow.png'
            });
        }

        var container = document.getElementById('indonesia-leaflet-map');
        var panel = document.querySelector('[data-proj-map-panel]');
        var placeholder = document.querySelector('[data-proj-map-placeholder]');
        var detailEl = document.querySelector('[data-proj-map-detail]');

        if (!container || typeof L === 'undefined') return;

        var map = L.map(container, {
            center: [-2.5, 118],
            zoom: 4,
            minZoom: 3,
            maxZoom: 12,
            scrollWheelZoom: false,
            zoomControl: true,
            attributionControl: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        var indoBounds = L.latLngBounds(
            L.latLng(-11, 94),
            L.latLng(6, 142)
        );
        map.fitBounds(indoBounds, { padding: [10, 10] });

        L.control.attribution({ position: 'bottomright', prefix: false }).addTo(map);

        var activeId = null;
        var markers = {};
        var currentFilter = MAP_CONFIG.initialFilter || 'all';

        function showDetail(project) {
            if (placeholder) placeholder.style.display = 'none';
            if (!detailEl) return;

            var badge = FILTER_MODE === 'status'
                ? statusColor(project.status)
                : catColor(project.category);
            var badgeLabel = FILTER_MODE === 'status'
                ? (project.statusLabel || project.status || 'Project')
                : (project.categoryLabel || project.category || 'Project');

            detailEl.style.display = 'block';
            detailEl.className = 'proj-map__detail is-visible';
            detailEl.setAttribute('data-category', project.category || '');
            detailEl.setAttribute('data-status', project.status || '');

            var detailLink = project.url
                ? '<a class="proj-map__detail-link" href="' + escapeHtml(project.url) + '">View Project Details <i class="icon-arrow_right"></i></a>'
                : '';

            detailEl.innerHTML =
                '<div class="proj-map__detail-inner">' +
                    '<div class="proj-map__detail-head">' +
                        '<span class="proj-map__detail-badge" style="background:' + badge.bg + ';color:' + badge.text + ';">' + escapeHtml(badgeLabel) + '</span>' +
                        '<button class="proj-map__detail-close" type="button" aria-label="Close detail">&#x2715;</button>' +
                    '</div>' +
                    '<h3 class="proj-map__detail-title">' + escapeHtml(project.name) + '</h3>' +
                    '<div class="proj-map__detail-meta-row">' +
                        '<span class="proj-map__detail-meta-item">' +
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21c-4-4.5-6-8-6-11a6 6 0 0 1 12 0c0 3-2 6.5-6 11z"/><circle cx="12" cy="10" r="2"/></svg>' +
                            escapeHtml(project.city || 'Indonesia') +
                        '</span>' +
                        '<span class="proj-map__detail-meta-sep"></span>' +
                        '<span class="proj-map__detail-meta-item">' +
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' +
                            escapeHtml(project.year || '-') +
                        '</span>' +
                    '</div>' +
                    '<div class="proj-map__detail-divider"></div>' +
                    '<p class="proj-map__detail-text">' + escapeHtml(project.description || '') + '</p>' +
                    detailLink +
                '</div>';

            var closeBtn = detailEl.querySelector('.proj-map__detail-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    activeId = null;
                    resetDetail();
                    refreshIcons();
                });
            }
        }

        function resetDetail() {
            if (placeholder) placeholder.style.display = '';
            if (detailEl) {
                detailEl.style.display = 'none';
                detailEl.className = 'proj-map__detail';
                detailEl.innerHTML = '';
            }
        }

        function refreshIcons() {
            PROJECTS.forEach(function (p) {
                var m = markers[p.id];
                if (!m) return;
                m.setIcon(makeIcon(p.id === activeId, p));
            });
        }

        function applyFilter(filter) {
            currentFilter = filter || 'all';
            activeId = null;
            resetDetail();

            var visibleLatLngs = [];

            PROJECTS.forEach(function (project) {
                var m = markers[project.id];
                if (!m) return;

                var visible = matchesFilter(project, currentFilter);
                if (visible) {
                    if (!map.hasLayer(m)) m.addTo(map);
                    visibleLatLngs.push([project.lat, project.lng]);
                } else if (map.hasLayer(m)) {
                    map.removeLayer(m);
                }
            });

            if (visibleLatLngs.length > 1) {
                map.fitBounds(L.latLngBounds(visibleLatLngs), { padding: [30, 30], maxZoom: 8 });
            } else if (visibleLatLngs.length === 1) {
                map.setView(visibleLatLngs[0], 7);
            } else {
                map.fitBounds(indoBounds, { padding: [10, 10] });
            }

            refreshIcons();
        }

        PROJECTS.forEach(function (project) {
            if (project.lat == null || project.lng == null) return;

            var m = L.marker([project.lat, project.lng], {
                icon: makeIcon(false, project),
                title: project.name,
                alt: (project.name || 'Project') + ', ' + (project.city || '')
            });

            m.on('click', function (event) {
                L.DomEvent.stopPropagation(event);
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

        window.projMapFilter = applyFilter;

        map.on('click', function () {
            if (activeId) {
                activeId = null;
                resetDetail();
                refreshIcons();
            }
        });

        if (currentFilter !== 'all') {
            applyFilter(currentFilter);
        }

        setTimeout(function () { map.invalidateSize(); }, 400);
        window.addEventListener('resize', function () { map.invalidateSize(); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
