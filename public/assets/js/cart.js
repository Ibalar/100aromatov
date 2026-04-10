(function () {
    "use strict";

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
    }

    async function request(url, method, payload) {
        const response = await fetch(url, {
            method: method,
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

    function ensureToastStyles() {
        if (document.getElementById("cart-toast-style")) return;

        const style = document.createElement("style");
        style.id = "cart-toast-style";
        style.textContent = ""
            + ".cart-toast-wrap{position:fixed;right:16px;top:16px;z-index:3000;display:flex;flex-direction:column;gap:10px;max-width:360px;}"
            + ".cart-toast{padding:12px 14px;border-radius:10px;color:#fff;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.18);opacity:0;transform:translateY(6px);transition:all .2s ease;}"
            + ".cart-toast.show{opacity:1;transform:translateY(0);}"
            + ".cart-toast.success{background:#1f9d55;}"
            + ".cart-toast.error{background:#d64545;}"
            + ".cart-toast.info{background:#374151;}";
        document.head.appendChild(style);
    }

    function toastRoot() {
        let root = document.getElementById("cart-toast-wrap");
        if (!root) {
            root = document.createElement("div");
            root.id = "cart-toast-wrap";
            root.className = "cart-toast-wrap";
            document.body.appendChild(root);
        }
        return root;
    }

    function notify(message, type) {
        if (!message) return;
        ensureToastStyles();
        const root = toastRoot();
        const toast = document.createElement("div");
        toast.className = "cart-toast " + (type || "info");
        toast.textContent = message;
        root.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add("show"));

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 220);
        }, 2200);
    }

    function updateCounts(count) {
        document.querySelectorAll(".js-cart-count").forEach((el) => {
            el.textContent = String(count || 0);
        });
    }

    function updateMiniCart(data) {
        const container = document.getElementById("js-cart-modal-items");
        const total = document.getElementById("js-cart-modal-total");
        if (container) container.innerHTML = data.items_html;
        if (total) total.textContent = data.total_byn_formatted;
    }

    function updatePageTotals(data) {
        const pageTotal = document.getElementById("js-cart-page-total");
        const checkoutTotal = document.getElementById("js-checkout-total");
        const pageItems = document.getElementById("js-cart-page-items");
        const checkoutItems = document.getElementById("js-checkout-items");

        if (pageItems) pageItems.innerHTML = data.items_html;
        if (checkoutItems) checkoutItems.innerHTML = data.items_html;
        if (pageTotal) pageTotal.textContent = data.total_byn_formatted;
        if (checkoutTotal) checkoutTotal.textContent = data.total_byn_formatted;
    }

    function getSelectedVariantId() {
        return document.getElementById("selected-variant-id")?.value ||
            document.querySelector('input[name="variant_id"]:checked')?.value ||
            "";
    }

    async function refreshCart() {
        const data = await request("/cart/summary", "GET");
        updateCounts(data.count);
        updateMiniCart(data);
        updatePageTotals(data);
        return data;
    }

    async function addToCart(variantId, qty) {
        if (!variantId) {
            notify("Выберите вариант товара", "error");
            return;
        }

        try {
            const result = await request("/cart/add", "POST", {
                variant_id: Number(variantId),
                qty: Number(qty || 1),
            });
            await refreshCart();
            notify(result.message || "Товар добавлен в список для бронирования", "success");
        } catch (error) {
            notify(error.message || "Ошибка добавления в список для бронирования", "error");
        }
    }

    async function updateQty(variantId, qty) {
        try {
            await request("/cart/update", "POST", {
                variant_id: Number(variantId),
                qty: Number(qty),
            });
            await refreshCart();
            notify("Количество обновлено", "info");
        } catch (error) {
            notify(error.message || "Ошибка обновления количества", "error");
        }
    }

    async function removeItem(variantId) {
        try {
            await request("/cart/remove", "POST", {
                variant_id: Number(variantId),
            });
            await refreshCart();
            notify("Товар удален из списка для бронирования", "info");
        } catch (error) {
            notify(error.message || "Ошибка удаления товара", "error");
        }
    }

    async function clearCart() {
        try {
            await request("/cart/clear", "POST");
            await refreshCart();
            notify("Список для бронирования очищен", "info");
        } catch (error) {
            notify(error.message || "Ошибка очистки списка для бронирования", "error");
        }
    }

    document.addEventListener("click", async function (event) {
        const addBtn = event.target.closest(".js-add-to-cart");
        if (addBtn) {
            event.preventDefault();
            const variantId = addBtn.dataset.variantId || getSelectedVariantId();
            const qtyInput = document.querySelector(".js-product-qty");
            const qty = addBtn.dataset.qty || (qtyInput ? qtyInput.value : 1);
            await addToCart(variantId, qty);
            return;
        }

        const buyNowBtn = event.target.closest(".js-buy-now");
        if (buyNowBtn) {
            event.preventDefault();
            const variantId = buyNowBtn.dataset.variantId || getSelectedVariantId();
            const qtyInput = document.querySelector(".js-product-qty");
            const qty = buyNowBtn.dataset.qty || (qtyInput ? qtyInput.value : 1);
            await addToCart(variantId, qty);
            window.location.href = "/checkout";
            return;
        }

        const qtyBtn = event.target.closest(".js-cart-qty");
        if (qtyBtn) {
            event.preventDefault();
            const item = qtyBtn.closest(".cart-item");
            const input = item?.querySelector(".quantity-product");
            if (!item || !input) return;
            const variantId = item.dataset.variantId;
            const current = parseInt(input.value, 10) || 1;
            const action = qtyBtn.dataset.action;
            const next = action === "increase" ? current + 1 : Math.max(1, current - 1);
            await updateQty(variantId, next);
            return;
        }

        const removeBtn = event.target.closest(".js-cart-remove");
        if (removeBtn) {
            event.preventDefault();
            const item = removeBtn.closest(".cart-item");
            if (!item) return;
            await removeItem(item.dataset.variantId);
            return;
        }

        const clearBtn = event.target.closest(".js-cart-clear");
        if (clearBtn) {
            event.preventDefault();
            await clearCart();
        }
    });

    document.addEventListener("click", function (event) {
        const qtyMinus = event.target.closest(".js-product-qty-minus");
        const qtyPlus = event.target.closest(".js-product-qty-plus");
        const qtyInput = document.querySelector(".js-product-qty");
        if (!qtyInput) return;

        if (qtyMinus) {
            event.preventDefault();
            const val = parseInt(qtyInput.value, 10) || 1;
            qtyInput.value = String(Math.max(1, val - 1));
        }
        if (qtyPlus) {
            event.preventDefault();
            const val = parseInt(qtyInput.value, 10) || 1;
            qtyInput.value = String(val + 1);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        refreshCart().catch(function () { });
    });
})();
