(function () {
    "use strict";

    const selectors = {
        offcanvas: document.getElementById("quickView"),
        images: '[data-quickview-images]',
        category: '[data-quickview-category]',
        name: '[data-quickview-name]',
        reviews: '[data-quickview-reviews]',
        brand: '[data-quickview-brand]',
        sku: '[data-quickview-sku]',
        price: '[data-quickview-price]',
        originalPrice: '[data-quickview-original-price]',
        originalSeparator: '[data-quickview-original-separator]',
        description: '[data-quickview-description]',
        meta: '[data-quickview-meta]',
        variantLabel: '[data-quickview-variant-label]',
        variants: '[data-quickview-variants]',
        addToCart: '.js-quickview-add-to-cart',
        buyNow: '.js-quickview-buy-now',
        qtyInput: '.js-quickview-qty-input',
        detailsLink: '[data-quickview-details-link]',
        buttonPrice: '[data-quickview-button-price]',
    };

    if (!selectors.offcanvas) {
        return;
    }

    let currentProduct = null;
    let activeVariantId = null;

    function $(selector) {
        return selectors.offcanvas.querySelector(selector);
    }

    function escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function setText(selector, value) {
        const element = $(selector);
        if (element) {
            element.textContent = value ?? "";
        }
    }

    function setVisible(selector, visible) {
        const element = $(selector);
        if (element) {
            element.style.display = visible ? "" : "none";
        }
    }

    function setLoadingState() {
        setText(selectors.category, "");
        setText(selectors.name, "Загрузка...");
        setText(selectors.reviews, "");
        setText(selectors.brand, "");
        setText(selectors.sku, "-");
        setText(selectors.price, "...");
        setText(selectors.description, "");
        setText(selectors.meta, "");
        setText(selectors.variantLabel, "-");
        setText(selectors.buttonPrice, "");
        setVisible(selectors.originalPrice, false);
        setVisible(selectors.originalSeparator, false);

        const images = $(selectors.images);
        if (images) {
            images.innerHTML = '<div class="image"><div class="quickview-image-placeholder"><i class="icon icon-shopping-cart-simple fs-36"></i></div></div>';
        }

        const variants = $(selectors.variants);
        if (variants) {
            variants.innerHTML = "";
        }
    }

    function renderImages(images, fallbackAlt) {
        const container = $(selectors.images);
        if (!container) return;

        if (!images || !images.length) {
            container.innerHTML = '<div class="image"><div class="quickview-image-placeholder"><i class="icon icon-shopping-cart-simple fs-36"></i></div></div>';
            return;
        }

        container.innerHTML = images.map((image) => (
            '<div class="image">'
            + '<img loading="lazy" width="340" height="444" src="' + escapeHtml(image.src) + '" alt="' + escapeHtml(image.alt || fallbackAlt) + '">'
            + '</div>'
        )).join("");
    }

    function renderMeta(product) {
        const meta = [];
        if (product.country) {
            meta.push('<strong>Страна:</strong> ' + escapeHtml(product.country));
        }
        if (product.concentration) {
            meta.push('<strong>Концентрация:</strong> ' + escapeHtml(product.concentration));
        }
        const element = $(selectors.meta);
        if (element) {
            element.innerHTML = meta.join(" | ");
        }
    }

    function updateVariantUI(variantId) {
        if (!currentProduct) return;

        const variant = currentProduct.variants.find((item) => String(item.id) === String(variantId));
        if (!variant) return;

        activeVariantId = variant.id;

        setText(selectors.variantLabel, variant.label);
        setText(selectors.sku, variant.sku || "-");
        setText(selectors.price, variant.price_formatted);
        setText(selectors.buttonPrice, variant.price_formatted);

        const originalPrice = $(selectors.originalPrice);
        if (originalPrice) {
            originalPrice.textContent = variant.original_price_formatted || "";
        }
        setVisible(selectors.originalPrice, Boolean(variant.original_price_formatted));
        setVisible(selectors.originalSeparator, Boolean(variant.original_price_formatted));

        selectors.offcanvas.querySelectorAll(".quickview-variant-btn").forEach((button) => {
            button.classList.toggle("active", String(button.dataset.variantId) === String(variant.id));
        });

        const addToCart = $(selectors.addToCart);
        const buyNow = $(selectors.buyNow);
        if (addToCart) addToCart.dataset.variantId = variant.id;
        if (buyNow) buyNow.dataset.variantId = variant.id;
    }

    function renderVariants(product) {
        const container = $(selectors.variants);
        if (!container) return;

        if (!product.variants || !product.variants.length) {
            container.innerHTML = "";
            updateVariantUI(null);
            return;
        }

        container.innerHTML = product.variants.map((variant) => {
            const badges = variant.badges.length
                ? '<span class="meta">' + escapeHtml(variant.badges.join(", ")) + '</span>'
                : "";

            return '<button type="button" class="quickview-variant-btn" data-variant-id="' + escapeHtml(variant.id) + '">'
                + '<span>' + escapeHtml(variant.label) + '</span>'
                + '<span class="meta">' + escapeHtml(variant.price_formatted) + '</span>'
                + badges
                + '</button>';
        }).join("");

        updateVariantUI(product.default_variant_id || product.variants[0].id);
    }

    function renderProduct(product) {
        currentProduct = product;

        renderImages(product.images, product.name);
        setText(selectors.category, product.category_name || "");
        setText(selectors.name, product.name || "");
        setText(selectors.reviews, "(" + (product.reviews_count || 0) + " отзывов)");
        setText(selectors.brand, product.brand_name || "");
        setText(selectors.description, product.description || "");
        renderMeta(product);
        renderVariants(product);

        const detailsLink = $(selectors.detailsLink);
        if (detailsLink) {
            detailsLink.href = product.url || "#";
        }
    }

    async function loadProduct(productId) {
        if (!productId) return;

        setLoadingState();

        try {
            const response = await fetch("/product/" + productId + "/quick-view", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json",
                },
            });

            if (!response.ok) {
                throw new Error("Quick view request failed");
            }

            const data = await response.json();
            if (!data.product) {
                throw new Error("Product payload missing");
            }

            renderProduct(data.product);
        } catch (error) {
            setText(selectors.name, "Не удалось загрузить товар");
            setText(selectors.description, "Попробуйте открыть полную карточку товара.");
            setText(selectors.price, "");
        }
    }

    document.addEventListener("click", function (event) {
        const trigger = event.target.closest(".js-quick-view-trigger[data-product-id]");
        if (trigger) {
            loadProduct(trigger.dataset.productId);
            return;
        }

        const variantButton = event.target.closest(".quickview-variant-btn[data-variant-id]");
        if (variantButton) {
            updateVariantUI(variantButton.dataset.variantId);
            return;
        }

        const qtyMinus = event.target.closest(".js-quickview-qty-minus");
        const qtyPlus = event.target.closest(".js-quickview-qty-plus");
        const qtyInput = $(selectors.qtyInput);
        const addToCart = $(selectors.addToCart);
        const buyNow = $(selectors.buyNow);

        if (!qtyInput) return;

        if (qtyMinus || qtyPlus) {
            event.preventDefault();
            const current = parseInt(qtyInput.value, 10) || 1;
            const next = qtyMinus ? Math.max(1, current - 1) : current + 1;
            qtyInput.value = String(next);
            if (addToCart) addToCart.dataset.qty = String(next);
            if (buyNow) buyNow.dataset.qty = String(next);
        }
    });
})();
