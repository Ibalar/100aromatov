(function () {
    const MIN_CHARS = 2;
    const DEBOUNCE_MS = 300;

    let activeDropdown = null;
    let activeInput = null;
    let abortController = null;

    function init() {
        document.querySelectorAll('.js-search-input').forEach(function (input) {
            input.setAttribute('autocomplete', 'off');
            input.addEventListener('input', function () { onInput(input); });
            input.addEventListener('keydown', function (e) { onKeydown(input, e); });
            input.addEventListener('focus', function () { if (input.value.trim().length >= MIN_CHARS) onInput(input); });
        });

        document.addEventListener('click', function (e) {
            if (activeDropdown && !e.target.closest('.js-search-wrapper')) {
                closeDropdown();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    }

    var debounceTimer = null;

    function onInput(input) {
        clearTimeout(debounceTimer);
        var q = input.value.trim();

        if (q.length < MIN_CHARS) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(function () { fetchAndShow(input, q); }, DEBOUNCE_MS);
    }

    function onKeydown(input, e) {
        if (!activeDropdown) return;

        var items = activeDropdown.querySelectorAll('.js-search-item');
        var current = activeDropdown.querySelector('.js-search-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusNext(items, current, 1);
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusNext(items, current, -1);
            return;
        }
        if (e.key === 'Enter') {
            var active = activeDropdown.querySelector('.js-search-item.active');
            if (active) {
                e.preventDefault();
                var link = active.querySelector('a');
                if (link) link.click();
                closeDropdown();
            }
        }
    }

    function focusNext(items, current, direction) {
        var idx = current ? Array.prototype.indexOf.call(items, current) : -1;
        var next = direction > 0 ? (idx + 1) % items.length : (idx - 1 + items.length) % items.length;
        items.forEach(function (el) { el.classList.remove('active'); });
        if (items[next]) items[next].classList.add('active');
    }

    function fetchAndShow(input, q) {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        var suggestUrl = input.getAttribute('data-suggest-url') || '/search/suggest';

        try {
            var url = new URL(suggestUrl, window.location.origin);
            url.searchParams.set('q', q);

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: abortController.signal
            })
            .then(function (response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then(function (data) {
                if (input.value.trim() !== q) return;
                showDropdown(input, data, q);
            })
            .catch(function (err) {
                if (err.name !== 'AbortError') {
                    console.debug('Search suggest error:', err);
                }
            });
        } catch (e) {
            console.debug('Search suggest URL error:', e);
        }
    }

    function showDropdown(input, results, query) {
        closeDropdown();

        if (!results || results.length === 0) return;

        var dropdown = document.createElement('div');
        dropdown.className = 'js-search-dropdown';
        dropdown.style.cssText = 'position:absolute;z-index:1060;top:100%;left:0;right:0;background:#fff;border:1px solid #dee2e6;border-radius:0 0 6px 6px;box-shadow:0 4px 12px rgba(0,0,0,.1);max-height:400px;overflow-y:auto;';

        results.forEach(function (item) {
            var link = document.createElement('a');
            link.className = 'js-search-item';
            link.href = '/product/' + item.slug;
            link.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px 14px;color:#333;text-decoration:none;border-bottom:1px solid #f0f0f0;';

            if (item.image) {
                var img = document.createElement('img');
                img.src = item.image;
                img.alt = item.name;
                img.style.cssText = 'width:40px;height:40px;object-fit:cover;border-radius:4px;flex-shrink:0;';
                link.appendChild(img);
            }

            var nameDiv = document.createElement('div');
            nameDiv.className = 'fw-medium';
            nameDiv.innerHTML = highlight(item.name, query);
            nameDiv.style.cssText = 'flex:1;min-width:0;';
            link.appendChild(nameDiv);

            dropdown.appendChild(link);

            link.addEventListener('mouseenter', function () {
                dropdown.querySelectorAll('.js-search-item.active').forEach(function (el) { el.classList.remove('active'); });
                link.classList.add('active');
            });
        });

        // "Все результаты" link
        var allLink = document.createElement('a');
        allLink.href = '/search?q=' + encodeURIComponent(query);
        allLink.style.cssText = 'display:block;padding:10px 14px;text-align:center;color:#1a56db;font-weight:500;text-decoration:none;background:#f8f9fa;border-top:1px solid #dee2e6;';
        allLink.textContent = 'Все результаты';
        dropdown.appendChild(allLink);

        // Insert dropdown AFTER the input without moving the input
        input.parentNode.insertBefore(dropdown, input.nextSibling);

        activeDropdown = dropdown;
        activeInput = input;

        // Ensure parent has position relative for absolute positioning
        if (getComputedStyle(input.parentNode).position === 'static') {
            input.parentNode.style.position = 'relative';
        }
    }

    function highlight(text, query) {
        var idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return escapeHtml(text);
        return escapeHtml(text.substring(0, idx)) +
            '<mark>' + escapeHtml(text.substring(idx, idx + query.length)) + '</mark>' +
            escapeHtml(text.substring(idx + query.length));
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatPrice(price) {
        var num = parseFloat(price);
        if (num === 0) return 'под заказ';
        return num.toFixed(2).replace('.', ',') + ' BYN';
    }

    function closeDropdown() {
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
        activeInput = null;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
