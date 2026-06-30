(function () {
    const MIN_CHARS = 2;
    const DEBOUNCE_MS = 300;
    const MAX_RESULTS = 8;

    let activeDropdown = null;
    let abortController = null;

    function init() {
        document.querySelectorAll('.js-search-input').forEach((input) => {
            input.addEventListener('input', () => onInput(input));
            input.addEventListener('keydown', (e) => onKeydown(input, e));
            input.addEventListener('focus', () => onInput(input));
        });

        document.addEventListener('click', (e) => {
            if (activeDropdown && !e.target.closest('.js-search-autocomplete')) {
                closeDropdown();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            }
        });
    }

    let debounceTimer = null;

    function onInput(input) {
        clearTimeout(debounceTimer);

        const q = input.value.trim();

        if (q.length < MIN_CHARS) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(() => fetchAndShow(input, q), DEBOUNCE_MS);
    }

    function onKeydown(input, e) {
        if (!activeDropdown) return;

        const items = activeDropdown.querySelectorAll('.js-search-item');
        const current = activeDropdown.querySelector('.js-search-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusNext(items, current, 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusNext(items, current, -1);
        } else if (e.key === 'Enter') {
            const active = activeDropdown.querySelector('.js-search-item.active');
            if (active) {
                e.preventDefault();
                active.querySelector('a')?.click();
            }
        }
    }

    function focusNext(items, current, direction) {
        const idx = current ? Array.from(items).indexOf(current) : -1;
        const next = direction > 0 ? (idx + 1) % items.length : (idx - 1 + items.length) % items.length;
        items.forEach((el) => el.classList.remove('active'));
        items[next]?.classList.add('active');
    }

    async function fetchAndShow(input, q) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        try {
            const url = new URL(window.location.origin + '/search/suggest');
            url.searchParams.set('q', q);

            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: abortController.signal,
            });

            if (!response.ok) return;

            const data = await response.json();
            if (input.value.trim() !== q) return;

            showDropdown(input, data, q);
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.debug('Search suggest failed', err);
            }
        }
    }

    function showDropdown(input, results, query) {
        closeDropdown();

        if (!results || results.length === 0) return;

        const dropdown = document.createElement('div');
        dropdown.className = 'search-autocomplete dropdown-menu show w-100 js-search-autocomplete';
        dropdown.style.position = 'absolute';
        dropdown.style.zIndex = '1050';
        dropdown.style.maxHeight = '400px';
        dropdown.style.overflowY = 'auto';

        results.forEach((item) => {
            const link = document.createElement('a');
            link.className = 'dropdown-item d-flex align-items-center py-2 js-search-item';
            link.href = '/product/' + item.slug;

            let html = '<div class="flex-grow-1">';
            html += '<div class="fw-medium">' + highlight(item.name, query) + '</div>';
            html += '<div class="small text-muted">';
            if (item.brand) html += item.brand;
            if (item.sku) html += (item.brand ? ' · ' : '') + 'SKU: ' + item.sku;
            html += '</div></div>';
            html += '<div class="text-end fw-medium ms-3">' + formatPrice(item.price_byn) + '</div>';

            link.innerHTML = html;
            dropdown.appendChild(link);
        });

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);

        activeDropdown = dropdown;
    }

    function highlight(text, query) {
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return escapeHtml(text);
        return (
            escapeHtml(text.substring(0, idx)) +
            '<mark>' +
            escapeHtml(text.substring(idx, idx + query.length)) +
            '</mark>' +
            escapeHtml(text.substring(idx + query.length))
        );
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatPrice(price) {
        return parseFloat(price).toFixed(2).replace('.', ',') + ' BYN';
    }

    function closeDropdown() {
        if (activeDropdown) {
            const wrapper = activeDropdown.parentNode;
            const input = wrapper.querySelector('input');
            if (input) {
                wrapper.parentNode.insertBefore(input, wrapper);
            }
            wrapper.remove();
            activeDropdown = null;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
