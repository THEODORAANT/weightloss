(function () {
    var SVG_NS = 'http://www.w3.org/2000/svg';

    function parseJSONScript(el) {
        if (!el) return null;
        try {
            return JSON.parse(el.textContent || el.innerText || '{}');
        } catch (e) {
            return null;
        }
    }

    function ensureArrowMarker(svg) {
        var existing = svg.querySelector('#flowchart-arrow');
        if (existing) {
            return 'flowchart-arrow';
        }

        var defs = document.createElementNS(SVG_NS, 'defs');
        var marker = document.createElementNS(SVG_NS, 'marker');
        marker.setAttribute('id', 'flowchart-arrow');
        marker.setAttribute('viewBox', '0 0 10 10');
        marker.setAttribute('refX', '9');
        marker.setAttribute('refY', '5');
        marker.setAttribute('markerWidth', '6');
        marker.setAttribute('markerHeight', '6');
        marker.setAttribute('orient', 'auto');

        var path = document.createElementNS(SVG_NS, 'path');
        path.setAttribute('d', 'M 0 0 L 10 5 L 0 10 z');
        path.setAttribute('fill', '#5b7cfa');
        path.setAttribute('opacity', '0.75');

        marker.appendChild(path);
        defs.appendChild(marker);
        svg.appendChild(defs);

        return 'flowchart-arrow';
    }

    function truncateLabel(label) {
        if (!label) return '';
        if (label.length <= 40) return label;
        return label.slice(0, 37) + '\u2026';
    }

    function drawPath(svg, container, fromEl, toEl, label) {
        if (!fromEl || !toEl) return;

        var containerRect = container.getBoundingClientRect();
        var fromRect = fromEl.getBoundingClientRect();
        var toRect = toEl.getBoundingClientRect();

        var scrollLeft = container.scrollLeft || 0;
        var scrollTop = container.scrollTop || 0;

        var startX = fromRect.left + fromRect.width / 2 - containerRect.left + scrollLeft;
        var startY = fromRect.bottom - containerRect.top + scrollTop;
        var endX = toRect.left + toRect.width / 2 - containerRect.left + scrollLeft;
        var endY = toRect.top - containerRect.top + scrollTop;

        if (startX === endX && startY === endY) {
            return;
        }

        var controlX = (startX + endX) / 2;

        var path = document.createElementNS(SVG_NS, 'path');
        path.setAttribute('d', 'M ' + startX + ' ' + startY + ' C ' + controlX + ' ' + startY + ' ' + controlX + ' ' + endY + ' ' + endX + ' ' + endY);
        path.setAttribute('class', 'flowchart-connection');
        path.setAttribute('marker-end', 'url(#' + ensureArrowMarker(svg) + ')');
        svg.appendChild(path);

        if (label) {
            var text = document.createElementNS(SVG_NS, 'text');
            text.textContent = truncateLabel(label);
            text.setAttribute('x', controlX);
            text.setAttribute('y', (startY + endY) / 2);
            text.setAttribute('class', 'flowchart-connection__label');
            svg.appendChild(text);
        }
    }

    function drawConnections(canvas) {
        var svg = canvas.querySelector('.flowchart-connections');
        if (!svg) return;

        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        var data = parseJSONScript(canvas.querySelector('.js-flowchart-data'));
        if (!data || !data.questions) return;

        var width = canvas.scrollWidth || canvas.clientWidth;
        var height = canvas.scrollHeight || canvas.clientHeight;
        svg.setAttribute('width', width);
        svg.setAttribute('height', height);
        svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);

        var nodes = {};
        var nodeEls = canvas.querySelectorAll('[data-question-key]');
        Array.prototype.forEach.call(nodeEls, function (el) {
            var key = el.getAttribute('data-question-key');
            if (key) {
                nodes[key] = el;
            }
        });

        var steps = {};
        var stepEls = canvas.querySelectorAll('[data-step-slug]');
        Array.prototype.forEach.call(stepEls, function (el) {
            var slug = el.getAttribute('data-step-slug');
            if (slug) {
                steps[slug] = el;
            }
        });

        Object.keys(data.questions).forEach(function (key) {
            var question = data.questions[key];
            if (!question) return;

            var fromEl = nodes[key];
            if (!fromEl) return;

            var connections = [];
            if (Array.isArray(question.connections) && question.connections.length) {
                connections = question.connections;
            } else if (Array.isArray(question.dependencies) && question.dependencies.length) {
                connections = question.dependencies;
            }

            connections.forEach(function (connection) {
                if (!connection) return;

                var target = null;
                if (connection.question && nodes[connection.question]) {
                    target = nodes[connection.question];
                } else if (connection.step && steps[connection.step]) {
                    target = steps[connection.step];
                }

                if (!target || target === fromEl) {
                    return;
                }

                var label = '';
                if (typeof connection.label === 'string' && connection.label.length) {
                    label = connection.label;
                } else if (Array.isArray(connection.displayValues) && connection.displayValues.length) {
                    label = connection.displayValues.join(', ');
                } else if (Array.isArray(connection.values) && connection.values.length) {
                    label = connection.values.join(', ');
                }

                drawPath(svg, canvas, fromEl, target, label);
            });
        });
    }

    function setupNodeInteractions(container) {
        var cards = container.querySelectorAll('.flowchart-node[data-edit-url]');
        Array.prototype.forEach.call(cards, function (card) {
            if (!card.hasAttribute('tabindex')) {
                card.setAttribute('tabindex', '0');
            }
            if (!card.hasAttribute('role')) {
                card.setAttribute('role', 'link');
            }

            card.addEventListener('click', function (event) {
                if (event.defaultPrevented) return;
                if (event.target && event.target.closest('a')) {
                    return;
                }
                var url = card.getAttribute('data-edit-url');
                if (url) {
                    window.location.href = url;
                }
            });

            card.addEventListener('keydown', function (event) {
                if (event.target && event.target.closest('a')) {
                    return;
                }
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    var url = card.getAttribute('data-edit-url');
                    if (url) {
                        window.location.href = url;
                    }
                }
            });
        });
    }

    function initFlowchart(container) {
        var canvases = container.querySelectorAll('[data-flowchart-canvas]');
        if (!canvases.length) return;

        var toggles = [];
        if (container.parentNode) {
            toggles = container.parentNode.querySelectorAll('[data-flowchart-tab]');
        } else {
            toggles = document.querySelectorAll('[data-flowchart-tab]');
        }
        var activeType = container.getAttribute('data-active-type');
        if (!activeType && canvases[0]) {
            activeType = canvases[0].getAttribute('data-flowchart-canvas');
            container.setAttribute('data-active-type', activeType);
        }

        function updateActive(type) {
            var found = false;
            Array.prototype.forEach.call(canvases, function (canvas) {
                var matches = canvas.getAttribute('data-flowchart-canvas') === type;
                canvas.classList.toggle('is-active', matches);
                if (matches) {
                    found = true;
                }
            });
            if (!found && canvases[0]) {
                canvases[0].classList.add('is-active');
                type = canvases[0].getAttribute('data-flowchart-canvas');
            }
            container.setAttribute('data-active-type', type);
            Array.prototype.forEach.call(toggles, function (toggle) {
                if (toggle.getAttribute('data-flowchart-tab')) {
                    toggle.classList.toggle('is-active', toggle.getAttribute('data-flowchart-tab') === type);
                }
            });
            requestAnimationFrame(renderConnections);
        }

        function renderConnections() {
            Array.prototype.forEach.call(canvases, function (canvas) {
                if (canvas.classList.contains('is-active')) {
                    drawConnections(canvas);
                }
            });
        }

        Array.prototype.forEach.call(canvases, function (canvas) {
            canvas.addEventListener('scroll', function () {
                if (canvas.classList.contains('is-active')) {
                    requestAnimationFrame(function () {
                        drawConnections(canvas);
                    });
                }
            });
        });

        Array.prototype.forEach.call(toggles, function (toggle) {
            toggle.addEventListener('click', function (event) {
                var targetType = toggle.getAttribute('data-flowchart-tab');
                if (!targetType) return;
                event.preventDefault();
                updateActive(targetType);
                var href = toggle.getAttribute('href');
                if (href && window.history && typeof window.history.replaceState === 'function') {
                    if (href.charAt(0) === '?') {
                        var base = window.location.href.split('?')[0];
                        window.history.replaceState({}, document.title, base + href);
                    } else {
                        window.history.replaceState({}, document.title, href);
                    }
                }
            });
        });

        window.addEventListener('resize', function () {
            requestAnimationFrame(renderConnections);
        });

        updateActive(activeType);
        setupNodeInteractions(container);
        renderConnections();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var containers = document.querySelectorAll('[data-flowchart]');
        Array.prototype.forEach.call(containers, function (container) {
            initFlowchart(container);
        });
    });
})();
