(function () {
    "use strict";

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
    }

    function ensureStyles() {
        if (document.getElementById("wishlist-inline-style")) return;
        const style = document.createElement("style");
        style.id = "wishlist-inline-style";
        style.textContent = ""
            + ".js-wishlist-toggle.addwishlist .icon{color:#e11d48;}"
            + ".js-wishlist-link.is-active .icon{color:#e11d48;}";
        document.head.appendChild(style);
    }

    function showToast(message, type) {
        if (!message) return;

        const root = document.getElementById("cart-toast-wrap") || (function () {
            const r = document.createElement("div");
            r.id = "cart-toast-wrap";
            r.className = "cart-toast-wrap";
            document.body.appendChild(r);
            return r;
        })();

        if (!document.getElementById("cart-toast-style")) {
            const st = document.createElement("style");
            st.id = "cart-toast-style";
            st.textContent = ""
                + ".cart-toast-wrap{position:fixed;right:16px;top:16px;z-index:3000;display:flex;flex-direction:column;gap:10px;max-width:360px;}"
                + ".cart-toast{padding:12px 14px;border-radius:10px;color:#fff;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.18);opacity:0;transform:translateY(6px);transition:all .2s ease;}"
                + ".cart-toast.show{opacity:1;transform:translateY(0);}"
                + ".cart-toast.success{background:#1f9d55;}.cart-toast.error{background:#d64545;}.cart-toast.info{background:#374151;}";
            document.head.appendChild(st);
        }

        const el = document.createElement("div");
        el.className = "cart-toast " + (type || "info");
        el.textContent = message;
        root.appendChild(el);
        requestAnimationFrame(() => el.classList.add("show"));
        setTimeout(() => {
            el.classList.remove("show");
            setTimeout(() => el.remove(), 220);
        }, 2200);
    }

    async function request(url, method, payload) {
        const response = await fetch(url, {
            method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf(),
                "X-Requested-With": "XMLHttpRequest",
            },
            body: payload ? JSON.stringify(payload) : undefined,
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || "Request failed");
        }

        return data;
    }

    function updateHeaderIcons(count) {
        const active = Number(count || 0) > 0;

        document.querySelectorAll(".js-wishlist-link").forEach((link) => {
            const icon = link.querySelector(".icon");
            if (!icon) return;

            link.classList.toggle("is-active", active);
            icon.classList.toggle("icon-heart", active);
            icon.classList.toggle("icon-HeartStraight", !active);
        });
    }

    function setProductState(productId, active) {
        const selector = '.js-wishlist-toggle[data-product-id="' + productId + '"]';
        document.querySelectorAll(selector).forEach((el) => {
            el.classList.toggle("addwishlist", !!active);

            const tt = el.querySelector(".tooltip");
            if (tt) {
                tt.textContent = active ? "Удалить из избранного" : "В избранное";
            }
        });
    }

    function refreshWishlistEmptyState() {
        const grid = document.getElementById("js-wishlist-grid");
        const empty = document.getElementById("js-wishlist-empty");
        const clearBtn = document.getElementById("js-wishlist-clear");

        if (!grid || !empty) return;

        const hasItems = grid.querySelectorAll(".wishlist-item").length > 0;
        empty.classList.toggle("d-none", hasItems);

        if (clearBtn) {
            clearBtn.classList.toggle("d-none", !hasItems);
        }
    }

    async function sync() {
        try {
            const data = await request("/wishlist/summary", "GET");
            const ids = data.ids || [];

            updateHeaderIcons(data.count);

            document.querySelectorAll(".js-wishlist-toggle[data-product-id]").forEach((el) => {
                const pid = Number(el.getAttribute("data-product-id"));
                el.classList.toggle("addwishlist", ids.includes(pid));
            });

            refreshWishlistEmptyState();
        } catch (_) {
        }
    }

    async function toggle(productId) {
        const data = await request("/wishlist/toggle", "POST", { product_id: Number(productId) });

        updateHeaderIcons(data.count);
        setProductState(productId, data.in_wishlist);

        const row = document.querySelector('.wishlist-item[data-product-id="' + productId + '"]');
        if (row && !data.in_wishlist) {
            row.remove();
            refreshWishlistEmptyState();
        }

        showToast(data.message, data.in_wishlist ? "success" : "info");
    }

    async function clearWishlist() {
        const data = await request("/wishlist/clear", "POST");

        updateHeaderIcons(data.count);

        document.querySelectorAll(".js-wishlist-toggle.addwishlist").forEach((el) => {
            el.classList.remove("addwishlist");
        });

        document.querySelectorAll(".wishlist-item").forEach((el) => el.remove());
        refreshWishlistEmptyState();

        showToast(data.message, "info");
    }

    document.addEventListener("click", async function (event) {
        const toggleBtn = event.target.closest(".js-wishlist-toggle");
        if (toggleBtn) {
            event.preventDefault();
            const productId = toggleBtn.getAttribute("data-product-id");
            if (!productId) return;

            try {
                await toggle(productId);
            } catch (e) {
                showToast(e.message || "Ошибка", "error");
            }

            return;
        }

        const clearBtn = event.target.closest(".js-wishlist-clear");
        if (clearBtn) {
            event.preventDefault();
            try {
                await clearWishlist();
            } catch (e) {
                showToast(e.message || "Ошибка", "error");
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        ensureStyles();
        sync();
    });
})();
