(function () {
  const CART_STORAGE_KEY = "luxe:cart";
  const WISHLIST_STORAGE_KEY = "luxe:wishlist";
  const PROFILE_STORAGE_KEY = "luxe:profile";
  const NAV_TRANSITION_STORAGE_KEY = "luxe:nav-transition";
  const API_ENDPOINT = "/api.php";
  let cartSyncTimer;
  let wishlistSyncTimer;

  const UI_TEXT = {
    addToWishlist: "Add to Wishlist",
    savedToWishlist: "Saved to Wishlist",
    wishlistEmpty: "No saved wishlist items yet.",
    remove: "Remove",
    addSavedToBag: "Add to Bag",
    savedWishlistToast: "Saved to wishlist.",
    removedWishlistToast: "Removed from wishlist.",
    addedBagToast: "Added to bag.",
  };

  const money = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  const SIZE_GUIDE_CONFIGS = {
    apparel: {
      description: "uses standard ready-to-wear body measurements.",
      columns: ["Size", "Bust/Chest", "Waist", "Status"],
      measurements: {
        XS: ["31-33 in", "24-26 in"],
        S: ["34-36 in", "27-29 in"],
        M: ["37-39 in", "30-32 in"],
        L: ["40-42 in", "33-35 in"],
        XL: ["43-45 in", "36-38 in"],
      },
      fallback: ["Standard fit", "Check garment fit"],
      note: "Choose your usual size for a clean tailored fit.",
    },
    outerwear: {
      description: "is sized to layer comfortably over light knitwear or shirting.",
      columns: ["Size", "Chest", "Recommended Layer", "Status"],
      measurements: {
        XS: ["33-35 in", "Light shirt"],
        S: ["36-38 in", "Shirt or fine knit"],
        M: ["39-41 in", "Mid-weight knit"],
        L: ["42-44 in", "Layered knit"],
        XL: ["45-47 in", "Heavy layer"],
      },
      fallback: ["Standard chest", "Light layer"],
      note: "If you are between sizes, size up for outerwear.",
    },
    bottoms: {
      description: "uses trouser body measurements.",
      columns: ["Size", "Waist", "Hip", "Status"],
      measurements: {
        XS: ["24-26 in", "34-36 in"],
        S: ["27-29 in", "37-39 in"],
        M: ["30-32 in", "40-42 in"],
        L: ["33-35 in", "43-45 in"],
        XL: ["36-38 in", "46-48 in"],
      },
      fallback: ["Standard waist", "Standard hip"],
      note: "Use waist and hip measurements for the closest fit.",
    },
    footwear: {
      description: "uses EU footwear sizing with approximate US conversion.",
      columns: ["Size", "US", "Foot Length", "Status"],
      measurements: {
        "EU 36": ["US 5.5", "8.9 in / 22.6 cm"],
        "EU 37": ["US 6.5", "9.2 in / 23.4 cm"],
        "EU 38": ["US 7.5", "9.5 in / 24.1 cm"],
        "EU 39": ["US 8.5", "9.8 in / 24.9 cm"],
        "EU 40": ["US 9", "10.0 in / 25.4 cm"],
        "EU 41": ["US 10", "10.3 in / 26.2 cm"],
        "EU 42": ["US 10.5", "10.6 in / 26.9 cm"],
        "EU 43": ["US 11.5", "10.9 in / 27.7 cm"],
        "EU 44": ["US 12", "11.2 in / 28.4 cm"],
        "EU 45": ["US 13", "11.5 in / 29.2 cm"],
        "EU 46": ["US 14", "11.8 in / 30.0 cm"],
      },
      fallback: ["US equivalent", "Measure foot length"],
      note: "For narrow feet, keep your usual EU size. For wider feet, consider the next size up.",
    },
    bag: {
      description: "is offered in a fixed carry size.",
      columns: ["Size", "Dimensions", "Handle Drop", "Status"],
      measurements: {
        "One Size": ["15 x 12 x 6 in", "9 in"],
      },
      fallback: ["Fixed dimensions", "Standard drop"],
      note: "Dimensions are approximate and measured at the widest points.",
    },
    watch: {
      description: "is sized by case diameter and wrist fit.",
      columns: ["Case", "Wrist Fit", "Case Thickness", "Status"],
      measurements: {
        "38 mm": ["5.5-7 in wrist", "8.5 mm"],
        "42 mm": ["6.5-8 in wrist", "9.2 mm"],
      },
      fallback: ["Standard wrist fit", "Standard case"],
      note: "Choose the smaller case for a lower-profile fit and the larger case for more presence.",
    },
  };

  const PRODUCT_COLOR_VARIANTS = {
    coat: [
      { name: "Deep Navy", hex: "#1f2f5d", filter: "blue" },
      { name: "Charcoal", hex: "#2f3131", filter: "gray" },
      { name: "Camel", hex: "#b4946c", filter: "sand" },
    ],
    heels: [
      { name: "Black Leather", hex: "#111111", filter: "black" },
      { name: "Oxblood", hex: "#5a1f24", filter: "black" },
      { name: "Warm Cream", hex: "#e8dfd2", filter: "sand" },
    ],
    tote: [
      { name: "Cream Leather", hex: "#e8dfd2", filter: "sand" },
      { name: "Midnight Black", hex: "#111111", filter: "black" },
      { name: "Olive", hex: "#4b5320", filter: "olive" },
    ],
    gown: [
      { name: "Ivory Silk", hex: "#f6f1e8", filter: "sand" },
      { name: "Midnight", hex: "#151923", filter: "black" },
      { name: "Champagne", hex: "#d8c3a5", filter: "sand" },
    ],
    shirt: [
      { name: "Optic White", hex: "#ffffff", filter: "gray" },
      { name: "Sky Blue", hex: "#9bbbe8", filter: "blue" },
      { name: "Black", hex: "#111111", filter: "black" },
    ],
    "moto-jacket": [
      { name: "Midnight Black", hex: "#111111", filter: "black" },
      { name: "Walnut Brown", hex: "#5a3b2e", filter: "black" },
      { name: "Slate", hex: "#626b73", filter: "gray" },
    ],
    cashmere: [
      { name: "Sand Cashmere", hex: "#d8c3a5", filter: "sand" },
      { name: "Dove Gray", hex: "#c7c6c6", filter: "gray" },
      { name: "Olive Marl", hex: "#697252", filter: "olive" },
    ],
    "double-coat": [
      { name: "Deep Navy", hex: "#1f2f5d", filter: "blue" },
      { name: "Charcoal", hex: "#2f3131", filter: "gray" },
      { name: "Camel", hex: "#b4946c", filter: "sand" },
    ],
    sneaker: [
      { name: "Optic White", hex: "#ffffff", filter: "gray" },
      { name: "Electric Blue", hex: "#0040df", filter: "blue" },
      { name: "Black", hex: "#111111", filter: "black" },
    ],
    trousers: [
      { name: "Ash Gray", hex: "#9ca3af", filter: "gray" },
      { name: "Black", hex: "#111111", filter: "black" },
      { name: "Olive", hex: "#4b5320", filter: "olive" },
    ],
    "cloud-runner": [
      { name: "Soft Gray", hex: "#c9ced6", filter: "gray" },
      { name: "Optic White", hex: "#ffffff", filter: "gray" },
      { name: "Graphite", hex: "#2f3131", filter: "black" },
    ],
    "downtown-high-top": [
      { name: "Mahogany Leather", hex: "#4b2e24", filter: "black" },
      { name: "Black Leather", hex: "#111111", filter: "black" },
      { name: "Chestnut", hex: "#8a552f", filter: "sand" },
    ],
    "essential-tote": [
      { name: "Sand Leather", hex: "#d8c3a5", filter: "sand" },
      { name: "Black Leather", hex: "#111111", filter: "black" },
      { name: "Olive", hex: "#4b5320", filter: "olive" },
    ],
    "mono-watch": [
      { name: "Brushed Silver", hex: "#c7c6c6", filter: "gray" },
      { name: "Graphite", hex: "#2f3131", filter: "black" },
      { name: "Warm Gold", hex: "#c6a15b", filter: "sand" },
    ],
  };

  function t(key) {
    return UI_TEXT[key] || key;
  }

  function parseUsdPrice(text) {
    const value = Number(String(text || "").replace(/[^0-9.]/g, ""));
    return Number.isFinite(value) ? value : 0;
  }

  function readCart() {
    try {
      const raw = localStorage.getItem(CART_STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function writeCartCache(items) {
    try {
      localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(Array.isArray(items) ? items : []));
    } catch (error) {
      // Storage may be unavailable in private mode.
    }
  }

  function writeCart(items) {
    writeCartCache(items);
    syncCartToBackend(items);
  }

  function readWishlist() {
    try {
      const raw = localStorage.getItem(WISHLIST_STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function writeWishlist(items) {
    writeWishlistCache(items);
    syncWishlistToBackend(items);
  }

  function writeWishlistCache(items) {
    try {
      localStorage.setItem(WISHLIST_STORAGE_KEY, JSON.stringify(Array.isArray(items) ? items : []));
    } catch (error) {
      // Wishlist persistence is optional.
    }
  }

  function readProfile() {
    try {
      const raw = localStorage.getItem(PROFILE_STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : null;
      return parsed && typeof parsed === "object" ? parsed : null;
    } catch (error) {
      return null;
    }
  }

  function writeProfileCache(profile) {
    try {
      if (profile) {
        localStorage.setItem(PROFILE_STORAGE_KEY, JSON.stringify(profile));
      } else {
        localStorage.removeItem(PROFILE_STORAGE_KEY);
      }
    } catch (error) {
      // Profile cache is optional; the PHP backend remains the source of truth.
    }
  }

  function dispatchProfileUpdated(profile) {
    window.dispatchEvent(new CustomEvent("luxe:profile-updated", { detail: profile || null }));
  }

  function dispatchCartUpdated() {
    window.dispatchEvent(new CustomEvent("luxe:cart-updated"));
  }

  async function apiRequest(action, options = {}) {
    if (typeof window.fetch !== "function") {
      throw new Error("Fetch is unavailable.");
    }

    const request = {
      method: options.method || "GET",
      credentials: "same-origin",
      headers: {
        Accept: "application/json",
      },
    };

    if (Object.prototype.hasOwnProperty.call(options, "body")) {
      request.headers["Content-Type"] = "application/json";
      request.body = JSON.stringify(options.body || {});
    }

    const response = await window.fetch(`${API_ENDPOINT}?action=${encodeURIComponent(action)}`, request);
    const payload = await response.json().catch(() => ({}));
    if (!response.ok || payload.ok === false) {
      throw new Error(payload.error || "Backend request failed.");
    }

    return payload;
  }

  async function fetchPublicProduct(productId) {
    if (!productId || typeof window.fetch !== "function") {
      return null;
    }

    try {
      const response = await window.fetch(
        `${API_ENDPOINT}?action=product&product=${encodeURIComponent(productId)}`,
        {
          credentials: "same-origin",
          headers: { Accept: "application/json" },
        }
      );
      const payload = await response.json().catch(() => ({}));
      return response.ok && payload.ok !== false && payload.product ? payload.product : null;
    } catch (error) {
      return null;
    }
  }

  function syncCartToBackend(items) {
    if (!Array.isArray(items) || typeof window.fetch !== "function" || !readProfile()) {
      return;
    }

    window.clearTimeout(cartSyncTimer);
    cartSyncTimer = window.setTimeout(() => {
      apiRequest("cart/save", {
        method: "POST",
        body: { items },
      })
        .then((payload) => {
          if (Array.isArray(payload.cart)) {
            writeCartCache(payload.cart);
            updateBagCountDisplay();
            dispatchCartUpdated();
          }
        })
        .catch(() => {});
    }, 180);
  }

  function syncWishlistToBackend(items) {
    if (!Array.isArray(items) || typeof window.fetch !== "function" || !readProfile()) {
      return;
    }

    window.clearTimeout(wishlistSyncTimer);
    wishlistSyncTimer = window.setTimeout(() => {
      apiRequest("wishlist/save", {
        method: "POST",
        body: { items },
      })
        .then((payload) => {
          if (Array.isArray(payload.wishlist)) {
            writeWishlistCache(payload.wishlist);
            renderWishlistButton(document.querySelector("[data-wishlist]"));
            renderWishlistLists();
          }
        })
        .catch(() => {});
    }, 180);
  }

  async function initBackendState() {
    if (typeof window.fetch !== "function") {
      return;
    }

    const localCart = readCart();
    const localWishlist = readWishlist();
    try {
      const state = await apiRequest("state");
      if (state.profile) {
        writeProfileCache(state.profile);
        dispatchProfileUpdated(state.profile);

        if (Array.isArray(state.cart) && state.cart.length) {
          writeCartCache(state.cart);
          dispatchCartUpdated();
        } else if (localCart.length) {
          syncCartToBackend(localCart);
        }

        if (Array.isArray(state.wishlist) && state.wishlist.length) {
          writeWishlistCache(state.wishlist);
        } else if (localWishlist.length) {
          syncWishlistToBackend(localWishlist);
        }
      }

      updateBagCountDisplay();
      renderWishlistButton(document.querySelector("[data-wishlist]"));
      renderWishlistLists();
    } catch (error) {
      // Local storage remains the development fallback if the PHP/PostgreSQL backend is offline.
    }
  }

  function wishlistHas(productId) {
    return readWishlist().some((item) => item.id === productId);
  }

  function getWishlistProductFromButton(button) {
    if (!button) {
      return null;
    }

    const priceNode = document.querySelector("[data-product-price-display]");
    const title = button.dataset.productName || document.querySelector("[data-product-title]")?.textContent?.trim() || "Item";
    const price = Number(button.dataset.productPrice || priceNode?.dataset.usdPrice || parseUsdPrice(priceNode?.textContent));
    const image = document.querySelector("[data-product-main-image]")?.getAttribute("src") || button.dataset.productImage || "";
    const color = document.querySelector("[data-selected-color]")?.textContent?.trim() || "";
    const size = document.querySelector("[data-selected-size]")?.textContent?.trim() || "";

    return {
      id: button.dataset.productId || title.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, ""),
      name: title,
      price,
      image,
      color,
      size,
      savedAt: Date.now(),
    };
  }

  function renderWishlistButton(button) {
    if (!button) {
      return;
    }

    const active = Boolean(button.dataset.productId && wishlistHas(button.dataset.productId));
    button.dataset.active = String(active);
    button.classList.toggle("is-selected-option", active);
    button.setAttribute("aria-pressed", String(active));

    const label = button.querySelector("[data-wishlist-label]");
    if (label) {
      label.textContent = active ? t("savedToWishlist") : t("addToWishlist");
    }

    const icon = button.querySelector(".material-symbols-outlined");
    if (icon) {
      icon.style.fontVariationSettings = active ? "'FILL' 1" : "";
    }
  }

  function renderWishlistLists() {
    const lists = document.querySelectorAll("[data-profile-wishlist-list]");
    if (!lists.length) {
      return;
    }

    const wishlist = readWishlist();
    lists.forEach((list) => {
      list.innerHTML = "";

      if (!wishlist.length) {
        list.innerHTML = `<p class="font-body-sm text-body-sm text-on-surface-variant text-center py-sm">${escapeHtml(t("wishlistEmpty"))}</p>`;
        return;
      }

      wishlist.forEach((item) => {
        const row = document.createElement("div");
        row.className = "luxe-wishlist-item";
        const title = escapeHtml(item.name || "Item");
        const image = escapeHtml(item.image || "");
        const meta = [item.size, item.color].filter(Boolean).join(" • ") || "Saved item";
        row.innerHTML = `
          <div class="luxe-wishlist-thumb">
            ${image ? `<img alt="" src="${image}"/>` : `<div></div>`}
          </div>
          <div class="luxe-wishlist-copy">
            <p class="font-label-lg text-label-lg text-on-surface">${title}</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">${escapeHtml(meta)}</p>
            <p class="font-label-md text-label-md text-primary">${money.format(Number(item.price || 0))}</p>
          </div>
          <div class="luxe-wishlist-actions">
            <button type="button" data-wishlist-add-to-bag="${escapeHtml(item.id || "")}">${escapeHtml(t("addSavedToBag"))}</button>
            <button type="button" data-remove-wishlist="${escapeHtml(item.id || "")}">${escapeHtml(t("remove"))}</button>
          </div>
        `;
        list.appendChild(row);
      });
    });
  }

  function updateBagCountDisplay() {
    const count = readCart().length;
    document.querySelectorAll("[data-bag-count]").forEach((node) => {
      node.textContent = String(count);
      node.classList.toggle("hidden", count === 0);
    });
  }

  function escapeHtml(text) {
    return String(text || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  let toastTimer;

  function showToast(message, type) {
    let toast = document.querySelector("[data-luxe-toast]");
    if (!toast) {
      toast = document.createElement("div");
      toast.dataset.luxeToast = "";
      toast.setAttribute("role", "status");
      toast.setAttribute("aria-live", "polite");
      document.body.appendChild(toast);
    }

    toast.className = "luxe-toast";
    if (type === "error") {
      toast.classList.add("is-error");
    }
    toast.textContent = message;

    window.requestAnimationFrame(() => toast.classList.add("is-visible"));
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => {
      toast.classList.remove("is-visible");
    }, 2800);
  }

  function setStatus(anchor, message, type) {
    const parent = anchor.parentElement || document.body;
    let status = parent.querySelector(":scope > .luxe-form-status");
    if (!status) {
      status = document.createElement("p");
      status.className = "luxe-form-status";
      anchor.insertAdjacentElement("afterend", status);
    }

    status.textContent = message;
    status.classList.toggle("is-error", type === "error");
  }

  function isEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function setButtonDefaults() {
    document.querySelectorAll("button:not([type])").forEach((button) => {
      if (!button.closest("form")) {
        button.type = "button";
      }
    });
  }

  function normalizeHeaderActions() {
    document.querySelectorAll("header").forEach((header) => {
      const actionRow =
        header.querySelector('a[aria-label="View checkout"]')?.parentElement ||
        header.querySelector('a[aria-label="Account"]')?.parentElement;
      if (!actionRow) {
        return;
      }

      const cartLink = actionRow.querySelector('a[aria-label="View checkout"]');
      const accountLink = actionRow.querySelector('a[aria-label="Account"]');
      const menuButton = Array.from(actionRow.querySelectorAll("button")).find((item) =>
        String(item.className).includes("md:hidden")
      );

      [cartLink, accountLink].forEach((link) => {
        if (!link) {
          return;
        }
        link.classList.remove("text-on-surface");
        link.classList.add("text-primary", "dark:text-inverse-primary");
        link.querySelectorAll(".material-symbols-outlined").forEach((icon) => {
          icon.classList.remove("text-on-surface");
          icon.classList.add("text-primary", "dark:text-inverse-primary");
        });
      });

      if (cartLink && accountLink) {
        cartLink.insertAdjacentElement("afterend", accountLink);
      }
      if (menuButton) {
        actionRow.appendChild(menuButton);
      }
    });
  }

  function hydrateImageAltText() {
    document.querySelectorAll("img[data-alt]").forEach((image) => {
      if (!image.getAttribute("alt")) {
        image.setAttribute("alt", image.dataset.alt || "");
      }
    });
  }

  function initMobileMenus() {
    const closeAll = () => {
      document.querySelectorAll(".site-mobile-panel.is-open").forEach((panel) => {
        panel.classList.remove("is-open");
        const button = document.querySelector(`[aria-controls="${panel.id}"]`);
        if (button) {
          button.setAttribute("aria-expanded", "false");
        }
      });
    };

    document.querySelectorAll("header").forEach((header, index) => {
      const nav = header.querySelector("nav");
      const menuButton = Array.from(header.querySelectorAll("button")).find((button) =>
        String(button.className).includes("md:hidden")
      );

      if (!nav || !menuButton || header.querySelector(".site-mobile-panel")) {
        return;
      }

      const links = Array.from(nav.querySelectorAll("a"));
      if (!links.length) {
        return;
      }

      const panel = document.createElement("div");
      panel.className = "site-mobile-panel";
      panel.id = `site-mobile-menu-${index + 1}`;
      panel.innerHTML = links
        .map((link) => {
          const href = link.getAttribute("href") || "#";
          const label = link.textContent.trim();
          return `<a class="site-mobile-link" href="${href}">${label}</a>`;
        })
        .join("");

      header.appendChild(panel);
      menuButton.setAttribute("aria-controls", panel.id);
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "Open navigation menu");

      menuButton.addEventListener("click", (event) => {
        event.stopPropagation();
        const willOpen = !panel.classList.contains("is-open");
        closeAll();
        panel.classList.toggle("is-open", willOpen);
        menuButton.setAttribute("aria-expanded", String(willOpen));
      });
    });

    document.addEventListener("click", (event) => {
      if (!event.target.closest("header")) {
        closeAll();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeAll();
      }
    });
  }

  function initActiveNavState() {
    const page = normalizePageName(window.location.pathname);
    const segment = (new URLSearchParams(window.location.search).get("segment") || "").toLowerCase();
    const view = (new URLSearchParams(window.location.search).get("view") || "").toLowerCase();
    const segmentKeys = new Set(["men", "women", "accessories"]);
    let desired = "";
    if (page === "collections.php") {
      if (view === "new-arrivals") {
        desired = "new-arrivals";
      } else if (segmentKeys.has(segment)) {
        desired = segment;
      } else {
        desired = "collections";
      }
    } else if (page === "product.php") {
      desired = "collections";
    }
    const pendingTransition = takePendingNavTransition(desired);

    document.querySelectorAll("header nav").forEach((nav) => {
      nav.classList.add("luxe-nav-track");
      let indicator = nav.querySelector(":scope > .luxe-nav-indicator");
      if (!indicator) {
        indicator = document.createElement("span");
        indicator.className = "luxe-nav-indicator";
        indicator.setAttribute("aria-hidden", "true");
        nav.appendChild(indicator);
      }

      let activeLink = null;
      const keyedLinks = new Map();
      Array.from(nav.querySelectorAll("a")).forEach((link) => {
        const key = getCollectionNavKey(link);

        if (!key) {
          return;
        }

        keyedLinks.set(key, link);
        link.dataset.luxeNavKey = key;
        if (link.dataset.luxeNavReady !== "true") {
          link.addEventListener("click", () => {
            const currentLink = nav.querySelector("a[aria-current='page']");
            const fromKey = currentLink ? currentLink.dataset.luxeNavKey || "" : "";
            setPendingNavTransition(fromKey, key);
          });
          link.dataset.luxeNavReady = "true";
        }

        link.classList.add("luxe-nav-link");
        link.classList.remove(
          "text-primary",
          "dark:text-inverse-primary",
          "border-b-2",
          "border-primary",
          "dark:border-inverse-primary",
          "pb-1"
        );
        link.classList.add("text-secondary");
        link.removeAttribute("aria-current");

        if (key === desired) {
          link.classList.remove("text-secondary");
          link.classList.add("text-primary", "dark:text-inverse-primary");
          link.setAttribute("aria-current", "page");
          activeLink = link;
        }
      });

      if (!activeLink) {
        indicator.style.opacity = "0";
        indicator.style.width = "0px";
        return;
      }

      window.requestAnimationFrame(() => {
        const previousLink = pendingTransition ? keyedLinks.get(pendingTransition.from) : null;
        if (previousLink) {
          indicator.classList.add("is-jump");
          moveNavIndicator(nav, indicator, previousLink);
          indicator.getBoundingClientRect();
          indicator.classList.remove("is-jump");
          window.requestAnimationFrame(() => {
            moveNavIndicator(nav, indicator, activeLink);
          });
          return;
        }

        indicator.classList.add("is-jump");
        moveNavIndicator(nav, indicator, activeLink);
        indicator.getBoundingClientRect();
        indicator.classList.remove("is-jump");
      });
    });
  }

  function getCollectionNavKey(link) {
    const label = (link.textContent || "").trim().toLowerCase();
    const url = new URL(link.getAttribute("href") || "", window.location.origin);
    const page = normalizePageName(url.pathname);
    if (page !== "collections.php") {
      return "";
    }

    const view = (url.searchParams.get("view") || "").toLowerCase();
    const segment = (url.searchParams.get("segment") || "").toLowerCase();
    if (view === "new-arrivals" && label === "new arrivals") {
      return "new-arrivals";
    }
    if (["men", "women", "accessories"].includes(segment)) {
      return segment;
    }
    if (!view && !segment && label === "collections") {
      return "collections";
    }
    return "";
  }

  function normalizePageName(pathname) {
    const page = String(pathname || "/").split("/").filter(Boolean).pop() || "index.php";
    if (page === "collections") return "collections.php";
    if (page === "product") return "product.php";
    if (page === "checkout") return "checkout.php";
    return page;
  }

  function getNavIndicatorMetrics(nav, link) {
    const navRect = nav.getBoundingClientRect();
    const linkRect = link.getBoundingClientRect();
    return {
      left: linkRect.left - navRect.left,
      top: linkRect.bottom - navRect.top + 5,
      width: linkRect.width,
    };
  }

  function moveNavIndicator(nav, indicator, link) {
    const metrics = getNavIndicatorMetrics(nav, link);
    indicator.style.width = `${Math.max(0, metrics.width)}px`;
    indicator.style.transform = `translate3d(${metrics.left}px, ${metrics.top}px, 0)`;
    indicator.style.opacity = "1";
  }

  function setPendingNavTransition(from, to) {
    if (!from || !to || from === to) {
      return;
    }

    try {
      sessionStorage.setItem(
        NAV_TRANSITION_STORAGE_KEY,
        JSON.stringify({
          from,
          to,
          time: Date.now(),
        })
      );
    } catch (error) {
      // Some browsers disable sessionStorage in strict privacy modes.
    }
  }

  function takePendingNavTransition(target) {
    if (!target) {
      return null;
    }

    try {
      const raw = sessionStorage.getItem(NAV_TRANSITION_STORAGE_KEY);
      sessionStorage.removeItem(NAV_TRANSITION_STORAGE_KEY);
      if (!raw) {
        return null;
      }

      const transition = JSON.parse(raw);
      const isRecent = typeof transition.time === "number" && Date.now() - transition.time < 5000;
      if (!isRecent || transition.to !== target || transition.from === target) {
        return null;
      }

      return transition;
    } catch (error) {
      return null;
    }
  }

  function initHomeCarousel() {
    const track = document.querySelector("[data-carousel-track]");
    if (!track) {
      return;
    }

    const prev = document.querySelector("[data-carousel-prev]");
    const next = document.querySelector("[data-carousel-next]");
    const scrollAmount = () => Math.min(track.clientWidth * 0.82, 680);

    if (prev) {
      prev.setAttribute("aria-label", "Scroll trending products left");
      prev.addEventListener("click", () => {
        track.scrollBy({ left: -scrollAmount(), behavior: "smooth" });
      });
    }

    if (next) {
      next.setAttribute("aria-label", "Scroll trending products right");
      next.addEventListener("click", () => {
        track.scrollBy({ left: scrollAmount(), behavior: "smooth" });
      });
    }
  }

  function initNewsletterForms() {
    document.querySelectorAll("[data-newsletter-form]").forEach((scope) => {
      if (scope.dataset.newsletterBound === "true") {
        return;
      }

      const input = scope.querySelector('input[type="email"]');
      const button = scope.querySelector("button");
      if (!input || !button) {
        return;
      }

      scope.dataset.newsletterBound = "true";
      const submit = (event) => {
        event.preventDefault();
        const value = input.value.trim();

        if (!isEmail(value)) {
          setStatus(scope, "Enter a valid email address.", "error");
          showToast("Enter a valid email address.", "error");
          input.focus();
          return;
        }

        setStatus(scope, "You are on the LUXE list.");
        showToast("You are on the LUXE list.");
        input.value = "";
      };

      if (scope.matches("form")) {
        scope.addEventListener("submit", submit);
      } else {
        button.addEventListener("click", submit);
      }
    });
  }

  function initCollectionFilters() {
    const grid = document.querySelector("[data-collection-grid]");
    if (!grid) {
      return;
    }

    const cards = Array.from(grid.querySelectorAll("[data-collection-product]"));
    const count = document.querySelector("[data-product-count]");
    const emptyState = document.querySelector("[data-empty-products]");
    const categoryInputs = Array.from(document.querySelectorAll("[data-category-filter]"));
    const sizeButtons = Array.from(document.querySelectorAll("[data-filter-size]"));
    const colorButtons = Array.from(document.querySelectorAll("[data-filter-color]"));
    const priceInput = document.querySelector("[data-price-filter]");
    const priceOutput = document.querySelector("[data-price-output]");
    const sortSelect = document.querySelector("[data-sort-products]");
    const segmentFromUrl = new URLSearchParams(window.location.search).get("segment") || "";
    const viewFromUrl = (new URLSearchParams(window.location.search).get("view") || "").toLowerCase();
    let newArrivalsOnly = viewFromUrl === "new-arrivals";
    let activeSize = "";
    let activeColor = "";
    let activeSegment = "";

    const readChecked = () => categoryInputs.filter((input) => input.checked).map((input) => input.value);

    const updatePriceLabel = () => {
      if (!priceInput || !priceOutput) {
        return;
      }

      const value = Number(priceInput.value);
      const max = Number(priceInput.max);
      priceOutput.textContent = value >= max ? `$${max.toLocaleString()}+` : `Up to $${value.toLocaleString()}`;
    };

    const applySelectedClasses = () => {
      sizeButtons.forEach((button) => {
        button.classList.toggle("is-selected-option", button.dataset.filterSize === activeSize);
      });
      colorButtons.forEach((button) => {
        button.classList.toggle("is-selected", button.dataset.filterColor === activeColor);
      });
    };

    const sortCards = () => {
      if (!sortSelect) {
        return;
      }

      const sorted = cards.slice();
      const value = sortSelect.value;
      if (value === "price-low") {
        sorted.sort((a, b) => Number(a.dataset.price) - Number(b.dataset.price));
      } else if (value === "price-high") {
        sorted.sort((a, b) => Number(b.dataset.price) - Number(a.dataset.price));
      } else if (value === "popularity") {
        sorted.sort((a, b) => Number(b.dataset.popularity) - Number(a.dataset.popularity));
      } else {
        sorted.sort((a, b) => Number(a.dataset.rank) - Number(b.dataset.rank));
      }

      sorted.forEach((card) => grid.appendChild(card));
    };

    const applyFilters = () => {
      const categories = readChecked();
      const maxPrice = priceInput ? Number(priceInput.value) : Infinity;
      let visible = 0;

      cards.forEach((card) => {
        const matchesCategory = !categories.length || categories.includes(card.dataset.category);
        const matchesSize = !activeSize || String(card.dataset.sizes || "").split(",").includes(activeSize);
        const matchesColor = !activeColor || String(card.dataset.colors || "").split(",").includes(activeColor);
        const matchesSegment = !activeSegment || card.dataset.segment === activeSegment;
        const matchesPrice = Number(card.dataset.price) <= maxPrice;
        const matchesNewArrivals = !newArrivalsOnly || card.dataset.newArrival === "true";
        const isVisible =
          matchesCategory && matchesSize && matchesColor && matchesSegment && matchesPrice && matchesNewArrivals;

        card.classList.toggle("is-hidden", !isVisible);
        if (isVisible) {
          visible += 1;
        }
      });

      if (count) {
        count.textContent = `Showing ${visible} ${visible === 1 ? "product" : "products"}`;
      }
      if (emptyState) {
        emptyState.hidden = visible !== 0;
        emptyState.classList.toggle("hidden", visible !== 0);
      }
      applySelectedClasses();
      updatePriceLabel();
    };

    categoryInputs.forEach((input) => input.addEventListener("change", applyFilters));
    sizeButtons.forEach((button) => {
      button.addEventListener("click", () => {
        activeSize = activeSize === button.dataset.filterSize ? "" : button.dataset.filterSize;
        applyFilters();
      });
    });
    colorButtons.forEach((button) => {
      button.addEventListener("click", () => {
        activeColor = activeColor === button.dataset.filterColor ? "" : button.dataset.filterColor;
        applyFilters();
      });
    });
    if (priceInput) {
      priceInput.addEventListener("input", applyFilters);
    }
    if (sortSelect) {
      sortSelect.addEventListener("change", () => {
        sortCards();
        applyFilters();
      });
    }

    const applyCollectionRoute = (url, pushState) => {
      const nextUrl = url instanceof URL ? url : new URL(url, window.location.href);
      if ((nextUrl.pathname.split("/").pop() || "index.php") !== "collections.php") {
        return false;
      }

      const nextSegment = (nextUrl.searchParams.get("segment") || "").toLowerCase();
      const nextView = (nextUrl.searchParams.get("view") || "").toLowerCase();
      activeSegment = ["men", "women", "accessories"].includes(nextSegment) ? nextSegment : "";
      newArrivalsOnly = nextView === "new-arrivals";

      if (pushState) {
        window.history.pushState({}, "", `${nextUrl.pathname.split("/").pop()}${nextUrl.search}`);
      }
      if (newArrivalsOnly && sortSelect?.querySelector('option[value="newest"]')) {
        sortSelect.value = "newest";
      }

      sortCards();
      applyFilters();
      initActiveNavState();
      return true;
    };

    document.querySelectorAll("header nav a, .site-mobile-panel a").forEach((link) => {
      link.addEventListener("click", (event) => {
        const nextUrl = new URL(link.href, window.location.href);
        if ((nextUrl.pathname.split("/").pop() || "index.php") !== "collections.php") {
          return;
        }

        const currentLink = document.querySelector("header nav a[aria-current='page']");
        setPendingNavTransition(
          currentLink ? getCollectionNavKey(currentLink) : "",
          getCollectionNavKey(link)
        );
        event.preventDefault();
        applyCollectionRoute(nextUrl, true);
        document.querySelectorAll(".site-mobile-panel.is-open").forEach((panel) => {
          panel.classList.remove("is-open");
        });
        document.querySelectorAll('[aria-controls^="site-mobile-menu-"]').forEach((button) => {
          button.setAttribute("aria-expanded", "false");
        });
      });
    });

    window.addEventListener("popstate", () => {
      applyCollectionRoute(window.location.href, false);
    });

    cards.forEach((card, index) => {
      card.dataset.rank = String(index);
    });
    if (["men", "women", "accessories"].includes(segmentFromUrl)) {
      activeSegment = segmentFromUrl;
    }
    if (newArrivalsOnly && sortSelect && sortSelect.querySelector('option[value="newest"]')) {
      sortSelect.value = "newest";
    }
    sortCards();
    applyFilters();
  }

  async function hydrateApprovedProductCards() {
    const grid = document.querySelector("[data-collection-grid]");
    if (!grid || typeof window.fetch !== "function") {
      return;
    }

    try {
      const payload = await apiRequest("products");
      const products = Array.isArray(payload.products) ? payload.products : [];
      const existingIds = new Set(
        Array.from(grid.querySelectorAll("[data-collection-product]"))
          .map((card) => getProductIdFromCard(card))
          .filter(Boolean)
      );

      products.forEach((product) => {
        const productId = String(product.id || "");
        if (!productId || existingIds.has(productId)) {
          return;
        }
        grid.insertAdjacentHTML("beforeend", collectionProductCardHtml(product));
        existingIds.add(productId);
      });
    } catch (error) {
      // Static seeded products remain available if the backend is offline.
    }
  }

  function collectionProductCardHtml(product) {
    const productId = escapeHtml(product.id || "");
    const name = escapeHtml(product.name || "Product");
    const image = escapeHtml(product.image || "");
    const category = escapeHtml(product.category || "accessories");
    const segment = escapeHtml(product.segment || "accessories");
    const price = Number(product.price || 0);
    const colors = escapeHtml(productColorFilters(product).join(","));
    const sizes = escapeHtml(productSizes(product).join(","));
    const colorText = escapeHtml(product.defaultColor || productColorNames(product)[0] || "Default");
    const categoryText = escapeHtml(categoryLabel(product.category || "Product"));
    const newArrivalAttr = product.newArrival ? ' data-new-arrival="true"' : "";

    return `
      <div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="${category}" data-collection-product="" data-colors="${colors}" data-popularity="${Number(product.popularity || 0)}" data-price="${price}" data-segment="${segment}" data-sizes="${sizes}"${newArrivalAttr}>
        <div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
          <a class="block w-full h-full" href="/product.php?product=${productId}" aria-label="View product">
            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="${name}" src="${image}"/>
          </a>
          <a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
        </div>
        <div class="p-md">
          <div class="flex justify-between items-start gap-sm mb-xs">
            <h3 class="font-headline-sm text-headline-sm text-on-surface">${name}</h3>
            <span class="font-label-lg text-label-lg text-primary">${money.format(price)}</span>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant">${categoryText} • ${colorText}</p>
        </div>
      </div>
    `;
  }

  function initCollectionCardColorSwatches() {
    document.querySelectorAll("[data-collection-product]").forEach((card) => {
      if (card.dataset.colorSwatchesBound === "true") {
        return;
      }

      const productId = getProductIdFromCard(card);
      const image = card.querySelector('a[href*="product.php?product="] img') || card.querySelector("img");
      const defaultImage = image?.getAttribute("src") || "";
      const variants = getProductColorOptions(productId, defaultImage);
      if (!variants.length) {
        return;
      }

      card.dataset.activeColor = variants[0].name;
      if (variants.length < 2) {
        return;
      }

      card.dataset.colorSwatchesBound = "true";
      card.dataset.colors = Array.from(new Set(variants.map((variant) => variant.filter).filter(Boolean))).join(",");

      const media = image?.closest(".relative") || image?.parentElement;
      let tint = media?.querySelector("[data-card-color-tint]");
      if (media && !tint) {
        tint = document.createElement("div");
        tint.className = "collection-card-color-tint";
        tint.dataset.cardColorTint = "";
        media.appendChild(tint);
      }

      const details = card.querySelector(".p-md");
      const meta = details ? Array.from(details.querySelectorAll("p")).pop() : null;
      if (meta && !meta.dataset.baseMaterial) {
        meta.dataset.baseMaterial = (meta.textContent || "").split("•")[0].trim();
      }

      const swatches = document.createElement("div");
      swatches.className = "collection-card-swatches";
      swatches.dataset.cardColorSwatches = "";
      swatches.setAttribute("aria-label", "Available colors");

      const setVariant = (variant, button) => {
        swatches.querySelectorAll("button").forEach((option) => {
          option.classList.toggle("is-selected", option === button);
          option.setAttribute("aria-pressed", String(option === button));
        });

        card.dataset.activeColor = variant.name;
        if (image && variant.image) {
          image.src = variant.image;
          image.alt = `${card.querySelector("h3")?.textContent?.trim() || "Product"} in ${variant.name}`;
        }
        if (tint) {
          tint.style.backgroundColor = variant.hex;
          tint.style.opacity = "0";
        }
        if (meta && meta.dataset.baseMaterial) {
          meta.textContent = `${meta.dataset.baseMaterial} • ${variant.name}`;
        }
      };

      variants.forEach((variant, index) => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "collection-card-swatch";
        button.style.backgroundColor = variant.hex;
        button.dataset.colorName = variant.name;
        button.setAttribute("aria-label", `Preview ${variant.name}`);
        button.setAttribute("aria-pressed", "false");
        button.addEventListener("click", (event) => {
          event.preventDefault();
          event.stopPropagation();
          setVariant(variant, button);
        });
        swatches.appendChild(button);
        if (index === 0) {
          setVariant(variant, button);
        }
      });

      if (meta) {
        meta.insertAdjacentElement("afterend", swatches);
      } else {
        details?.appendChild(swatches);
      }
    });
  }

  function getProductColorOptions(productId, fallbackImage, fallbackColors) {
    const variants =
      Array.isArray(fallbackColors) && fallbackColors.length
        ? fallbackColors.map(normalizeColorOption)
        : PRODUCT_COLOR_VARIANTS[productId] || [];
    const options = variants.map((variant) => ({
      ...variant,
      image: variant.image || fallbackImage || "",
      generatedImage: false,
      hasRealImageVariant: Boolean(variant.image && variant.image !== fallbackImage),
    }));

    if (!options.some((variant) => variant.hasRealImageVariant)) {
      return options.slice(0, 1);
    }

    return options;
  }

  function normalizeColorOption(option) {
    if (typeof option === "string") {
      return {
        name: option,
        hex: colorNameToHex(option),
        filter: colorNameToFilter(option),
      };
    }

    const name = option?.name || option?.label || "Default";
    return {
      ...option,
      name,
      hex: option?.hex || colorNameToHex(name),
      filter: option?.filter || colorNameToFilter(name),
    };
  }

  function productColorNames(product) {
    const colors = Array.isArray(product?.colors) ? product.colors : [];
    const names = colors
      .map((color) => (typeof color === "string" ? color : color?.name || color?.label || ""))
      .filter(Boolean);
    if (!names.length && product?.defaultColor) {
      names.push(product.defaultColor);
    }
    return names;
  }

  function productColorFilters(product) {
    return Array.from(new Set(productColorNames(product).map(colorNameToFilter).filter(Boolean)));
  }

  function productSizes(product) {
    const sizes = Array.isArray(product?.sizes) ? product.sizes : [];
    return sizes
      .map((size) => (typeof size === "string" ? size : size?.label || ""))
      .filter(Boolean);
  }

  function colorNameToFilter(name) {
    const value = String(name || "").toLowerCase();
    if (value.includes("blue") || value.includes("navy")) return "blue";
    if (value.includes("olive") || value.includes("green")) return "olive";
    if (value.includes("sand") || value.includes("camel") || value.includes("cream") || value.includes("ivory") || value.includes("champagne") || value.includes("gold")) return "sand";
    if (value.includes("black") || value.includes("oxblood") || value.includes("brown") || value.includes("mahogany") || value.includes("walnut") || value.includes("chestnut")) return "black";
    return "gray";
  }

  function colorNameToHex(name) {
    const value = String(name || "").toLowerCase();
    if (value.includes("blue") || value.includes("navy")) return "#1f2f5d";
    if (value.includes("olive") || value.includes("green")) return "#4b5320";
    if (value.includes("black")) return "#111111";
    if (value.includes("white") || value.includes("ivory")) return "#f6f1e8";
    if (value.includes("cream") || value.includes("sand") || value.includes("camel")) return "#d8c3a5";
    if (value.includes("gold") || value.includes("champagne")) return "#c6a15b";
    if (value.includes("oxblood") || value.includes("red")) return "#5a1f24";
    if (value.includes("brown") || value.includes("mahogany") || value.includes("walnut") || value.includes("chestnut")) return "#5a3b2e";
    if (value.includes("silver") || value.includes("gray") || value.includes("grey")) return "#c7c6c6";
    return "#c7c6c6";
  }

  function categoryLabel(category) {
    return (
      {
        outerwear: "Outerwear",
        knitwear: "Knitwear",
        "shirts-tops": "Shirts & Tops",
        trousers: "Trousers",
        footwear: "Footwear",
        dresses: "Dresses",
        bags: "Bags",
        accessories: "Accessories",
      }[category] || "Product"
    );
  }

  function getProductIdFromCard(card) {
    const href = card.querySelector('a[href*="product.php?product="]')?.getAttribute("href") || "";
    try {
      return new URL(href, window.location.href).searchParams.get("product") || "";
    } catch (error) {
      return "";
    }
  }

  function normalizeSizeOption(option) {
    return typeof option === "string" ? { label: option, available: true } : option;
  }

  function getProductSizeGuideKind(productId, product) {
    if (["heels", "sneaker", "cloud-runner", "downtown-high-top"].includes(productId)) {
      return "footwear";
    }
    if (["tote", "essential-tote"].includes(productId)) {
      return "bag";
    }
    if (productId === "mono-watch") {
      return "watch";
    }
    if (productId === "trousers") {
      return "bottoms";
    }
    if (String(product?.badge || "").toLowerCase().includes("outerwear")) {
      return "outerwear";
    }

    return "apparel";
  }

  function inferSizeGuideKindFromSizes(sizes) {
    const labels = sizes.map((size) => String(size.label || "").trim());
    if (labels.some((label) => /^EU\s+\d+/.test(label))) {
      return "footwear";
    }
    if (labels.some((label) => /\bmm\b/i.test(label))) {
      return "watch";
    }
    if (labels.length === 1 && labels[0].toLowerCase() === "one size") {
      return "bag";
    }

    return "apparel";
  }

  function getSizeGuideConfig(kind) {
    return SIZE_GUIDE_CONFIGS[kind] || SIZE_GUIDE_CONFIGS.apparel;
  }

  function buildSizeGuideTable(kind, sizes) {
    const config = getSizeGuideConfig(kind);
    const normalizedSizes = Array.isArray(sizes) && sizes.length ? sizes.map(normalizeSizeOption) : [{ label: "One Size" }];
    const headers = config.columns.map((column) => `<th>${escapeHtml(column)}</th>`).join("");
    const rows = normalizedSizes
      .map((size) => {
        const measurements = config.measurements[size.label] || config.fallback;
        const status = size.available === false ? "Out of stock" : "Available";
        return `
          <tr>
            <td>${escapeHtml(size.label)}</td>
            ${measurements.map((value) => `<td>${escapeHtml(value)}</td>`).join("")}
            <td>${status}</td>
          </tr>
        `;
      })
      .join("");

    return `
      <table class="luxe-size-table">
        <thead>
          <tr>${headers}</tr>
        </thead>
        <tbody>
          ${rows}
        </tbody>
      </table>
    `;
  }

  function renderProductColors(colorGroup, colorLabel, colors, fallbackImage) {
    if (!colorGroup || !Array.isArray(colors) || !colors.length) {
      return;
    }

    colorGroup.innerHTML = "";
    if (colorLabel) {
      colorLabel.textContent = colors[0].name;
    }

    if (colors.length < 2) {
      colorGroup.classList.add("hidden");
      return;
    }

    colorGroup.classList.remove("hidden");
    colors.forEach((color, index) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className =
        "w-10 h-10 rounded-full border border-outline-variant ring-2 ring-offset-2 ring-transparent shadow-sm hover:scale-105 transition-transform";
      button.style.backgroundColor = color.hex;
      button.dataset.colorName = color.name;
      button.dataset.colorImage = color.image || fallbackImage || "";
      button.setAttribute("aria-label", `Select ${color.name}`);
      if (index === 0) {
        button.classList.add("is-selected");
      }
      colorGroup.appendChild(button);
    });
  }

  function renderProductSizes(sizeGroup, selectedSize, sizes) {
    if (!sizeGroup || !Array.isArray(sizes) || !sizes.length) {
      return;
    }

    sizeGroup.innerHTML = "";
    sizeGroup.className = "grid grid-cols-4 gap-sm";

    sizes.map(normalizeSizeOption).forEach((size, index) => {
      const button = document.createElement("button");
      const available = size.available !== false;
      button.type = "button";
      button.textContent = size.label;
      button.dataset.size = size.label;
      button.disabled = !available;
      button.className = available
        ? "py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all"
        : "py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg opacity-40 cursor-not-allowed";

      if (available && !sizeGroup.querySelector(".is-selected-option")) {
        button.classList.add("is-selected-option");
        if (selectedSize) {
          selectedSize.textContent = size.label;
        }
      } else if (!available && index === 0 && selectedSize) {
        selectedSize.textContent = "";
      }

      sizeGroup.appendChild(button);
    });
  }

  function apiProductToProductDetail(product) {
    const colors = productColorNames(product).map((name) => normalizeColorOption(name));
    const sizes = productSizes(product);

    return {
      badge: categoryLabel(product.category || ""),
      title: product.name || "Product",
      description: product.description || "",
      reviews: "",
      price: money.format(Number(product.price || 0)),
      mainImage: product.image || "",
      colors: colors.length ? colors : [normalizeColorOption(product.defaultColor || "Default")],
      sizes: sizes.length ? sizes : ["One Size"],
    };
  }

  function renderUnavailableProduct(titleEl) {
    titleEl.textContent = "Product unavailable";
    const badgeEl = document.querySelector("[data-product-badge]");
    const reviewsEl = document.querySelector("[data-product-reviews]");
    const priceEl = document.querySelector("[data-product-price-display]");
    const addToBag = document.querySelector("[data-add-to-bag]");
    const wishlist = document.querySelector("[data-wishlist]");

    if (badgeEl) badgeEl.textContent = "Unavailable";
    if (reviewsEl) reviewsEl.textContent = "";
    if (priceEl) {
      priceEl.dataset.usdPrice = "0";
      priceEl.textContent = "";
    }
    [addToBag, wishlist].forEach((control) => {
      if (!control) return;
      control.classList.add("opacity-50", "pointer-events-none");
      control.setAttribute("aria-disabled", "true");
      if (control instanceof HTMLAnchorElement) {
        control.removeAttribute("href");
      }
    });
  }

  async function initProductFromQuery() {
    const titleEl = document.querySelector("[data-product-title]");
    if (!titleEl) {
      return;
    }

    const productId = (new URLSearchParams(window.location.search).get("product") || "").toLowerCase();
    if (!productId) {
      return;
    }

    const products = {
      coat: {
        badge: "Outerwear",
        title: "Wool Tailored Coat",
        reviews: "(86 Reviews)",
        price: "$495.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuBchUcD0EUx2j2iuPQpWTJiIV8_zw6fVXkrrcoW0NNOHi1SxdQu66MH8cT3G92PJljVOKIKgAIxyBfrzWWenQqSPhvb1LWVR7mApfsOtw2uz6Y0KuD0iE38tFoufpB9nbsctHFEUVTIfLvBiOPst0XZ0luxheHlGNr5fdlKJaBuMALpmJsNioBdRUkPlV9y0lbkOIJe3LmZLgRYbVi9X9cNiyeudwa6R7G7FYt8ukQkzQCN-Lq6ASZTwC8kocRV1qzT1TLfE4Ck0ws",
        colors: [{ name: "Deep Navy", hex: "#1f2f5d" }],
        sizes: ["S", "M", "L"],
      },
      heels: {
        badge: "Footwear",
        title: "Leather Artisan Pump",
        reviews: "(64 Reviews)",
        price: "$285.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuCJnqionFY9y6EYbY_k4evRIJvyAUyoVg6PwXuwkjSxd9qMHcQnc8LWwRtP425gPIACwYi4BS5rEn6a0KGYC0FNUJvGW5DD0_kSsfAwo0JEcqudDivOu-ipKeVOJq6AGfrgwkWDX--L6eQ64NWowPC_RH-NzHnWgcRyYnqAasPBj1Kkzbs7jslOeGddj7tOwH78WBLt_Aj94R2TFl-YneG96_bN-w1tvvuaSkU-DBLuCJN2XQosE_KpP5P8hSYeH9aEujzdyJQhAKA",
        colors: [{ name: "Black Leather", hex: "#111111" }],
        sizes: ["EU 36", "EU 37", "EU 38", "EU 39", "EU 40"],
      },
      tote: {
        badge: "Bags",
        title: "Structural Leather Tote",
        reviews: "(52 Reviews)",
        price: "$425.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuCQPtWo0O_h25ZD-uqlJNUOw_CMG6SjaIqwAdS7fF4-9zejORMZ_NS2z4p6KnILnMjKol4ArgZXQDMligjx1c4OZfzlnKZ0QCj3wVXujPKAC1OmvFlLDAwsIRshZBPok04K30_grPaCoCynebD3yteoWChW4NTJTnO3_ms3Qj_z61YdTuKy8wujCaIbt_2-hq2cf1tvvkCCTGkhrSYwJaECUQpkz1Nc7H3rbZ475nBm59md7IDO_JiPOxvWsjB_tlGvr5_oX2AaDRs",
        colors: [{ name: "Cream Leather", hex: "#e8dfd2" }],
        sizes: ["One Size"],
      },
      gown: {
        badge: "Dresses",
        title: "Silk Evening Gown",
        reviews: "(71 Reviews)",
        price: "$690.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuDyT9BhCFk-MjJkEIVTVNYLmb1QxR0SY6MzQjKyVpH6ALokWVkyTHfXcL2ePj0kD36BSmDQ2m1UX1l9b4U2Tdu6Ux_Jwi1oNQSi08hC2M2BgKgNtq5cjSrCZV5A51TrT7tgWxx1dIIwS_fPxiRfi1HDyR9tLER9Vb2k5gL75JToaQ-7z0vD5JDn-D6NuRPHrHGIS4-DIAHKY57ZRJ0l3agSH1MbIGmaB2agVwG8Mxy37aKCRpprkhttpXXdfeoWXyvTErZQS8kAyv4",
        colors: [{ name: "Ivory Silk", hex: "#f6f1e8" }],
        sizes: ["S", "M", "L"],
      },
      shirt: {
        badge: "Shirts & Tops",
        title: "Structured Poplin Shirt",
        reviews: "(39 Reviews)",
        price: "$145.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuCrgWwcqbnGCbjTzZ_L6va3PhP4cf4IkTCLovTQVVQ21uNl8Z-iAYJCbtNUwdrpW52ANcPOZwSBpeAkbpFZnb0-vmkLpg0C_vW6vRxtMZj6gBIA2DX3yK8ePkauxAxka-V-BfxLiFYxmc6Mx5JHfFpx3WxafijUCHffhouLljkrx8EiJup7kTSg2xicdPlld859d7hAUJTEWheTEcKB3y6rjnpquQM6TXl1jopT4NOnhBwGC5VSLaG6XmWgnXyjf0PVwYVyU0-uFaM",
        colors: [{ name: "Optic White", hex: "#ffffff" }],
        sizes: ["S", "M", "L"],
      },
      "moto-jacket": {
        badge: "Outerwear",
        title: "Asymmetric Moto Jacket",
        reviews: "(95 Reviews)",
        price: "$895.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuCTQq3n3DCDOOp4uvO55s47vNfLVHaHL4RRpmQqKOoCNfugHrUSjaTDkAo5K3YISIbA7a6jPcUShZsJ2Kg4juASweAYhtFdUprZXKWLmzBEBqEGrcYqOd5n_2XbRfHpRdAIXuQ_gktPbMPRiK3WoC9oUbfmD9gEkBh2mvSxQF4TL5GvJomBfX-CHRr8oP2nWafsoDLJB-Z1Ip4V9yuEAU8yFbdTe2Vn1fUWblpq2vamVyaLdv6edctwOp1ntp-15r6hJZVsMpz5A6U",
        colors: [{ name: "Midnight Black", hex: "#111111" }],
        sizes: ["S", "M", "L"],
      },
      cashmere: {
        badge: "Knitwear",
        title: "Cashmere Crewneck",
        reviews: "(58 Reviews)",
        price: "$275.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuBKRsTPHqnzmiLlQT6UJpkPFNyO_MC4TNYb8SLuuMT6eYVM26cT9KAJMjVQ1qZWuiqfIJzs66s0pT0Kd283kZj60MPLYqtTndM0cpkUqRHwhU7wS3BBgv6mWNsd1ufC4mvnDY_lBSgkqZOWwcnoMLrVFit4gZ4t7j3erTI0U5TjES76gjQHKOoL4Pyp_EcWeesh6GetZ1KXeMvU5n8YwQzEs_heAJKyYt2P3wc_hLNamSKoddmgUVxUJG1Ek392j3LSNcbQVF-imaw",
        colors: [{ name: "Sand Cashmere", hex: "#d8c3a5" }],
        sizes: ["XS", "S", "M", "L"],
      },
      "double-coat": {
        badge: "Outerwear",
        title: "Double-Breasted Coat",
        reviews: "(67 Reviews)",
        price: "$625.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuBcfj6mRnoWAMit-uw8PQAtxQvdlt-iqN_hTRSSJrdUS9RKiKInc0kGnFBpnmILN-0f7085ep0PsOJAyJvcjGQC0AZV2KDseDTQhXIaTE-tlE9PDpl_W1Kv-UkVY5CX9ErctWGn33f-niKBOXx6nxueMslAmGGb8tuaNbO0Qrfd4RvLWNdFAVLw0UQgLLlaiUx3sfstE9KfQ3B8zYjtRhP83jmvpfPPrcIaadZsR6ApcWrRZFI3epE9kosgUMpHTjmgyQrEcdQE2rE",
        colors: [{ name: "Deep Navy", hex: "#1f2f5d" }],
        sizes: ["M", "L"],
      },
      sneaker: {
        badge: "Footwear",
        title: "Leather Court Sneaker",
        reviews: "(88 Reviews)",
        price: "$180.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuAUUNay2PR3AwHLDUHjx5Rujo1UCiEsk-gFH6OzLEieQu0HBGKsjxnRrFB5OZsc9q7B5o67a2InJi-Tdd3qIOWFJq2Todw4uuvtMZHAi5Cbb1uGXJ2gchjbwXGMDIdDwnmlo3L8KdqDZgb5A0Qu_u9e7ub53CAEpBZQTvyypWWQWnUUmr678MIqY9wQ-sv4MxG_ZRTmCD0MHg1aCUsq0ed1nrSrjQKUfem5AQoxDdWafvYL29gZ9NLCvNCfLF1cHxUNhPawinAQQ3c",
        colors: [
          { name: "Optic White", hex: "#ffffff" },
          { name: "Electric Blue", hex: "#0040df" },
        ],
        sizes: ["EU 40", "EU 41", "EU 42", "EU 43", "EU 44", "EU 45"],
      },
      trousers: {
        badge: "Trousers",
        title: "Pleated Wide Trousers",
        reviews: "(49 Reviews)",
        price: "$225.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuDuE_74HpXg2xZnPmAtU1hwrFAGeUUQLliTQ98uEl4VGiqN3aWycidkTB9OjKPu8hN1UHEG7s1uuA-UExUb0ah0ExP54zzeiWKm1wphy34Jgbk9dJLA87Jx_dvpE7M7bebLb3gIK9A5kb_8oBjTvBaU9fTn6G4Pe35kkg4htY0qyX2bn5Dw_CMLN5YewQj2YFp-A-wnTrwWeORhL54bfahvB8uF9zyiI4qq17CAILQv3ZB8Jn2-TGmQsjtUqheDI91WHuoytYiCkE0",
        colors: [{ name: "Ash Gray", hex: "#9ca3af" }],
        sizes: ["XS", "S", "M", "L"],
      },
      "cloud-runner": {
        badge: "Footwear",
        title: "Cloud Runner",
        reviews: "(112 Reviews)",
        price: "$195.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuDC93J-Mi_uyRyUJEzeo2VUt3gCnx1KweKP_DA251be5VmSSPWzQiE3EhjkhxBwd55Gdn3J9N_12Bg5RS3V-OcMAmCzGyfEn-qdSteSk2Sm2QxMbfFQaTV2PYuxS-hkbx1W8FzSC8WNHMBlUvT6u9qRYmqskiYnAom81LJ-H0Fo81sIZgmqT2a5PI1sSigLrSXPZT1NqMpPwhbGMQ7KSvY-jFjHsUWYYIzRGNgt1QR5JJXBwnUKEJSA31X41LiJjnESmcWWo8QyvTc",
        colors: [
          { name: "Soft Gray", hex: "#c9ced6" },
          { name: "Optic White", hex: "#ffffff" },
        ],
        sizes: ["EU 40", "EU 41", "EU 42", "EU 43", "EU 44"],
      },
      "downtown-high-top": {
        badge: "Footwear",
        title: "Downtown High-Top",
        reviews: "(76 Reviews)",
        price: "$240.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuAjpeUssbT7HMtTqW4qyakzgJS1FiU-SwjRpIy1Tw9BZ6aW8dg1ia9mBEQTsXehCdnQ38ZNuPh-KwVqfaeAzAieT8_pSEHP2-gnuITMw24A-7ga_cJJtTAdWKtIKtSQB42G6Lq4ASqeKiDYxXOXIJ_svraHjMSC6L9xtrDwdUFYZMV5tu9rBzDSf-JQj_B9GQqtOtgotp4hmyshrGNnQ18q8fvkriRBUhxAH8NCNZuoL9oEZk47FOljzcP6gzOFg5XhTuuE5NEFFuA",
        colors: [{ name: "Mahogany Leather", hex: "#4b2e24" }],
        sizes: ["EU 40", "EU 41", "EU 42", "EU 43", "EU 44"],
      },
      "essential-tote": {
        badge: "Bags",
        title: "Essential Tote",
        reviews: "(44 Reviews)",
        price: "$375.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuCZA8QsI1z-4yH3tVTcLmNVZSvq3xbj2ReUE6tr_ldz3pUm4x6nLz0K8UtTdzYAXvt7OIQ4lGi_6bYNDOyqL9I_QJVDOF48pDIH-Hl8svspIO25hllQki_zXnMSkeUILE1UWGtW97p85dErHOd_YKll0yV9E6Bw0WrAMHHHh88cflfziP3AfRvQ8HCNboloyIkI7rub2yxdcXDNddCggX3FjoxliU_zAL_I9FGwVFlgZKL2z8nMK2kpTSHbaQalI_DPVzFDIE-mzos",
        colors: [{ name: "Sand Leather", hex: "#d8c3a5" }],
        sizes: ["One Size"],
      },
      "mono-watch": {
        badge: "Accessories",
        title: "Mono-Chrome Watch",
        reviews: "(203 Reviews)",
        price: "$220.00",
        mainImage: "https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z1PFbN5d5iX569B8_T5_giVQDfygycpDU7KE5ZeWXlQyLIJmRezROLUNVXqri-ol3nabfGSMPEJ4iCQRQ3T6ieAeILwdCX24kcjlMHyXEspuvHJa7qUNV4Ty59HPXyJstJCij6Din9uuEUaPdOqaoCdpTSzOijKAqnOB29zI02kZbQC29RivviKhLtFIO0S6vHSxpncfEwf_k_0GPHYo0IEKYDIqk8vh4-4e-BGwzb7smuC_FkCeST4f3apB7htMF-P6Mo4ECbE",
        colors: [{ name: "Brushed Silver", hex: "#c7c6c6" }],
        sizes: ["38 mm", "42 mm"],
      },
    };

    let selected = products[productId];
    if (!selected) {
      const apiProduct = await fetchPublicProduct(productId);
      selected = apiProduct ? apiProductToProductDetail(apiProduct) : null;
    }
    if (!selected) {
      renderUnavailableProduct(titleEl);
      return;
    }

    const badgeEl = document.querySelector("[data-product-badge]");
    const reviewsEl = document.querySelector("[data-product-reviews]");
    const priceEl = document.querySelector("[data-product-price-display]");
    const mainImageEl = document.querySelector("[data-product-main-image]");
    const thumbOneEl = document.querySelector("[data-product-thumb-1]");
    const thumbTwoEl = document.querySelector("[data-product-thumb-2]");
    const detailOne = document.querySelector("[data-product-detail-1]");
    const detailTwo = document.querySelector("[data-product-detail-2]");
    const detailThree = document.querySelector("[data-product-detail-3]");
    const addToBag = document.querySelector("[data-add-to-bag]");
    const colorGroup = document.querySelector("[data-product-colors]");
    const colorLabel = document.querySelector("[data-selected-color]");
    const sizeGroup = document.querySelector("[data-product-sizes]");
    const sizeLabel = document.querySelector("[data-selected-size]");
    const sizeGuideTrigger = document.querySelector("[data-size-guide]");
    const wishlist = document.querySelector("[data-wishlist]");
    const colorOptions = getProductColorOptions(productId, selected.mainImage, selected.colors);
    const selectedImage = colorOptions[0]?.image || selected.mainImage;
    const sizeGuideKind = getProductSizeGuideKind(productId, selected);
    const selectedPrice = parseUsdPrice(selected.price);

    if (badgeEl) badgeEl.textContent = selected.badge;
    titleEl.textContent = selected.title;
    if (reviewsEl) reviewsEl.textContent = selected.reviews;
    if (priceEl) {
      priceEl.dataset.usdPrice = String(selectedPrice);
      priceEl.textContent = selected.price;
    }
    [mainImageEl, thumbOneEl, thumbTwoEl].forEach((image) => {
      if (image) {
        image.src = selectedImage;
        image.alt = selected.title;
      }
    });
    renderProductColors(colorGroup, colorLabel, colorOptions, selectedImage);
    renderProductSizes(sizeGroup, sizeLabel, selected.sizes);
    if (sizeGuideTrigger) {
      sizeGuideTrigger.dataset.sizeGuideKind = sizeGuideKind;
      sizeGuideTrigger.dataset.sizeGuideTitle = selected.title;
    }
    if (detailOne) detailOne.textContent = selected.description || `Premium construction tailored for ${selected.title.toLowerCase()}.`;
    if (detailTwo) detailTwo.textContent = "Designed for comfort, longevity, and modern daily wear.";
    if (detailThree) detailThree.textContent = "Finished with refined materials and precision detailing.";
    if (addToBag) {
      addToBag.dataset.productName = selected.title;
      addToBag.dataset.productPrice = String(selectedPrice);
    }
    if (wishlist) {
      wishlist.dataset.productId = productId;
      wishlist.dataset.productName = selected.title;
      wishlist.dataset.productPrice = String(selectedPrice);
      wishlist.dataset.productImage = selectedImage;
      renderWishlistButton(wishlist);
    }
    document.title = `LUXE | ${selected.title}`;
  }

  function initProductOptions() {
    const colorGroup = document.querySelector("[data-product-colors]");
    const sizeGroup = document.querySelector("[data-product-sizes]");
    const colorLabel = document.querySelector("[data-selected-color]");
    const selectedSize = document.querySelector("[data-selected-size]");

    if (colorGroup) {
      colorGroup.querySelectorAll("[data-color-name]").forEach((button) => {
        button.addEventListener("click", () => {
          colorGroup.querySelectorAll("[data-color-name]").forEach((option) => option.classList.remove("is-selected"));
          button.classList.add("is-selected");
          if (colorLabel) {
            colorLabel.textContent = button.dataset.colorName;
          }
          if (button.dataset.colorImage) {
            document.querySelectorAll("[data-product-main-image], [data-product-thumb-1], [data-product-thumb-2]").forEach((image) => {
              image.src = button.dataset.colorImage;
            });
          }
        });
      });
    }

    if (sizeGroup) {
      sizeGroup.querySelectorAll("[data-size]").forEach((button) => {
        button.addEventListener("click", () => {
          if (button.disabled) {
            return;
          }

          sizeGroup.querySelectorAll("[data-size]").forEach((option) => option.classList.remove("is-selected-option"));
          button.classList.add("is-selected-option");
          if (selectedSize) {
            selectedSize.textContent = button.dataset.size;
          }
        });
      });
    }

    const wishlist = document.querySelector("[data-wishlist]");
    if (wishlist) {
      if (!wishlist.dataset.productId) {
        const initialWishlistItem = getWishlistProductFromButton(wishlist);
        if (initialWishlistItem?.id) {
          wishlist.dataset.productId = initialWishlistItem.id;
          wishlist.dataset.productName = initialWishlistItem.name;
          wishlist.dataset.productPrice = String(initialWishlistItem.price);
          wishlist.dataset.productImage = initialWishlistItem.image;
        }
      }
      renderWishlistButton(wishlist);
      wishlist.addEventListener("click", () => {
        const item = getWishlistProductFromButton(wishlist);
        if (!item?.id) {
          return;
        }
        wishlist.dataset.productId = item.id;
        wishlist.dataset.productName = item.name;
        wishlist.dataset.productPrice = String(item.price);
        wishlist.dataset.productImage = item.image;

        const current = readWishlist();
        const exists = current.some((saved) => saved.id === item.id);
        const next = exists
          ? current.filter((saved) => saved.id !== item.id)
          : [item, ...current.filter((saved) => saved.id !== item.id)];

        writeWishlist(next);
        renderWishlistButton(wishlist);
        renderWishlistLists();
        showToast(exists ? t("removedWishlistToast") : t("savedWishlistToast"));
      });
    }

    const addToBag = document.querySelector("[data-add-to-bag]");
    if (addToBag) {
      addToBag.addEventListener("click", () => {
        const name = addToBag.dataset.productName || "Item";
        const price = Number(addToBag.dataset.productPrice || 0);
        const image = document.querySelector("[data-product-main-image]")?.getAttribute("src") || "";
        try {
          const cart = readCart();
        cart.push({
          id: String(Date.now()),
          productId: wishlist?.dataset.productId || new URLSearchParams(window.location.search).get("product") || "",
          name,
          price,
          image,
          meta: (() => {
            const color = document.querySelector("[data-selected-color]")?.textContent?.trim();
            const size = document.querySelector("[data-selected-size]")?.textContent?.trim();
            if (color && size) {
              return `${size} • ${color}`;
            }
            return "Qty: 1";
          })(),
        });
          writeCart(cart);
        } catch (error) {
          // Cart persistence is optional.
        }
        updateBagCountDisplay();
      });
    }
  }

  function initSizeGuide() {
    const trigger = document.querySelector("[data-size-guide]");
    if (!trigger) {
      return;
    }

    const modal = document.createElement("div");
    modal.className = "luxe-modal";
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("aria-label", "Product size guide");

    document.body.appendChild(modal);

    const close = () => modal.classList.remove("is-open");
    const open = () => {
      const productTitle =
        trigger.dataset.sizeGuideTitle || document.querySelector("[data-product-title]")?.textContent?.trim() || "This item";
      const sizes = Array.from(document.querySelectorAll("[data-product-sizes] [data-size]")).map((button) => ({
        label: button.dataset.size || button.textContent || "One Size",
        available: !button.disabled,
      }));
      const kind = trigger.dataset.sizeGuideKind || inferSizeGuideKindFromSizes(sizes);
      const config = getSizeGuideConfig(kind);

      modal.innerHTML = `
        <div class="luxe-modal-panel luxe-size-guide-panel">
          <div class="luxe-modal-body">
            <div class="flex items-start justify-between gap-md">
              <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">Size Guide</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">${escapeHtml(productTitle)} ${escapeHtml(config.description)}</p>
              </div>
              <button type="button" class="p-base text-on-surface hover:text-primary" data-close-size-guide aria-label="Close size guide">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>
            ${buildSizeGuideTable(kind, sizes)}
            <p class="luxe-size-note">${escapeHtml(config.note)}</p>
          </div>
        </div>
      `;
      modal.classList.add("is-open");
    };

    trigger.addEventListener("click", open);
    modal.addEventListener("click", (event) => {
      if (event.target === modal || event.target.closest("[data-close-size-guide]")) {
        close();
      }
    });
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        close();
      }
    });
  }

  function initCheckout() {
    const options = Array.from(document.querySelectorAll("[data-shipping-option]"));
    const totalEl = document.querySelector("[data-order-total]");
    const shippingEl = document.querySelector("[data-order-shipping]");
    const subtotalEl = document.querySelector("[data-order-subtotal]");
    const taxEl = document.querySelector("[data-order-tax]");
    const itemsEl = document.querySelector("[data-checkout-cart-items]");
    const emptyEl = document.querySelector("[data-checkout-empty]");
    const taxRate = 0.08;

    if (!itemsEl) {
      return;
    }

    let shippingCost = 0;
    let subtotalAmount = 0;
    let taxAmount = 0;

    const renderLineItems = () => {
      const cart = readCart();
      itemsEl.innerHTML = "";

      if (!cart.length) {
        emptyEl?.classList.remove("hidden");
        subtotalAmount = 0;
        taxAmount = 0;
      } else {
        emptyEl?.classList.add("hidden");
        subtotalAmount = cart.reduce((sum, line) => sum + Number(line.price || 0), 0);
        taxAmount = Math.round(subtotalAmount * taxRate * 100) / 100;

        cart.forEach((line, index) => {
          const row = document.createElement("div");
          row.className = "flex gap-md items-start";
          const imgSrc = escapeHtml(line.image || "");
          const title = escapeHtml(line.name || "Item");
          const meta = escapeHtml(line.meta || "Qty: 1");
          const lineId = escapeHtml(line.id || String(index));
          row.innerHTML = `
            <div class="w-20 h-24 bg-surface-container rounded-lg overflow-hidden flex-shrink-0">
              ${imgSrc ? `<img class="w-full h-full object-cover" alt="" src="${imgSrc}"/>` : `<div class="w-full h-full bg-surface-container-high"></div>`}
            </div>
            <div class="flex-grow min-w-0">
              <p class="font-label-lg text-label-lg text-on-surface">${title}</p>
              <p class="font-body-sm text-body-sm text-on-surface-variant">${meta}</p>
              <p class="font-label-md text-label-md text-on-surface mt-xs">${money.format(Number(line.price || 0))}</p>
            </div>
            <button type="button" class="luxe-cart-remove" data-remove-cart-line="${lineId}" aria-label="Remove ${title} from bag">
              <span class="material-symbols-outlined" aria-hidden="true">delete</span>
            </button>
          `;
          itemsEl.appendChild(row);
        });
      }

      if (subtotalEl) {
        subtotalEl.textContent = money.format(subtotalAmount);
      }
      if (taxEl) {
        taxEl.textContent = money.format(taxAmount);
      }

      updateTotals();
    };

    const updateTotals = () => {
      if (shippingEl) {
        shippingEl.textContent = shippingCost === 0 ? "Free" : money.format(shippingCost);
        shippingEl.classList.toggle("text-primary", shippingCost === 0);
      }
      if (totalEl) {
        totalEl.textContent = money.format(subtotalAmount + taxAmount + shippingCost);
      }
    };

    itemsEl.addEventListener("click", (event) => {
      const removeButton = event.target.closest("[data-remove-cart-line]");
      if (!removeButton) {
        return;
      }

      const lineId = removeButton.dataset.removeCartLine;
      const nextCart = readCart().filter((line, index) => (line.id || String(index)) !== lineId);
      writeCart(nextCart);
      renderLineItems();
      updateBagCountDisplay();
      showToast("Removed item from bag.");
    });

    const setShipping = (option) => {
      shippingCost = Number(option.dataset.shippingCost || 0);
      options.forEach((item) => item.classList.toggle("is-selected", item === option));
      const radio = option.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
      }
      updateTotals();
    };

    options.forEach((option) => {
      option.addEventListener("click", () => setShipping(option));
      const radio = option.querySelector('input[type="radio"]');
      if (radio?.checked) {
        setShipping(option);
      }
    });
    renderLineItems();
    window.addEventListener("luxe:cart-updated", renderLineItems);

    // Address Label Button Selector Setup
    let selectedAddressLabel = "Home";
    const labelBtns = document.querySelectorAll("[data-address-label]");
    labelBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        selectedAddressLabel = btn.dataset.addressLabel;
        labelBtns.forEach(b => {
          if (b === btn) {
            b.className = "flex-1 py-xs px-sm border border-primary bg-primary text-white rounded-lg transition-all font-body-sm text-body-sm text-center font-medium";
          } else {
            b.className = "flex-1 py-xs px-sm border border-outline-variant text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all font-body-sm text-body-sm text-center";
          }
        });
      });
    });

    // Saved Addresses Dropdown Prefill Bindings
    let savedProfile = readProfile();
    const addressSelectContainer = document.getElementById("saved-addresses-selector-container");
    const addressSelect = document.getElementById("saved-addresses-select");
    const fieldFirstName = document.querySelector('input[name="first_name"]');
    const fieldLastName = document.querySelector('input[name="last_name"]');
    const fieldAddress = document.querySelector('input[name="address"]');
    const fieldCity = document.querySelector('input[name="city"]');
    const fieldPostal = document.querySelector('input[name="postal_code"]');

    const populateSavedAddresses = (profile) => {
      savedProfile = profile && typeof profile === "object" ? profile : null;
      if (!addressSelectContainer || !addressSelect) {
        return;
      }

      if (savedProfile && Array.isArray(savedProfile.addresses) && savedProfile.addresses.length > 0) {
        addressSelectContainer.classList.remove("hidden");
        addressSelect.innerHTML = `<option value="new">-- Enter New Address --</option>`;
        savedProfile.addresses.forEach((addr, idx) => {
          const opt = document.createElement("option");
          opt.value = idx;
          opt.textContent = `${addr.label} - ${addr.first_name} ${addr.last_name}, ${addr.address_line}`;
          addressSelect.appendChild(opt);
        });

        addressSelect.value = "new";
      } else {
        addressSelectContainer.classList.add("hidden");
        addressSelect.innerHTML = `<option value="new">-- Enter New Address --</option>`;
      }
    };

    if (addressSelect) {
      addressSelect.addEventListener("change", (e) => {
        const val = e.target.value;
        if (val === "new") {
          if (fieldFirstName) fieldFirstName.value = "";
          if (fieldLastName) fieldLastName.value = "";
          if (fieldAddress) fieldAddress.value = "";
          if (fieldCity) fieldCity.value = "";
          if (fieldPostal) fieldPostal.value = "";
          const homeBtn = document.querySelector('[data-address-label="Home"]');
          if (homeBtn) homeBtn.click();
          return;
        }

        const addr = savedProfile?.addresses?.[parseInt(val, 10)];
        if (addr) {
          if (fieldFirstName) fieldFirstName.value = addr.first_name || "";
          if (fieldLastName) fieldLastName.value = addr.last_name || "";
          if (fieldAddress) fieldAddress.value = addr.address_line || "";
          if (fieldCity) fieldCity.value = addr.city || "";
          if (fieldPostal) fieldPostal.value = addr.postal_code || "";
          const matchingBtn = document.querySelector(`[data-address-label="${addr.label}"]`);
          if (matchingBtn) matchingBtn.click();
        }
      });
    }

    populateSavedAddresses(savedProfile);
    window.addEventListener("luxe:profile-updated", (event) => populateSavedAddresses(event.detail));

    const complete = document.querySelector("[data-complete-purchase]");
    if (complete) {
      complete.addEventListener("click", () => {
        const required = Array.from(document.querySelectorAll("[data-checkout-required]"));
        required.forEach((field) => field.classList.remove("border-error"));
        const missing = required.find((field) => !field.value.trim());
        if (missing) {
          showToast("Complete the highlighted checkout fields.", "error");
          missing.focus();
          missing.classList.add("border-error");
          return;
        }

        const cart = readCart();
        if (!cart.length) {
          showToast("Your bag is empty. Add something from the collection first.", "error");
          return;
        }

        // Open OTP Confirmation Modal & Confirm Details
        const otpModal = document.getElementById("otp-confirm-modal");
        if (!otpModal) {
          return;
        }

        // Prefill user profile if logged in
        const currentProfile = readProfile();
        const emailField = document.getElementById("checkout-otp-email");
        if (currentProfile) {
          if (emailField && currentProfile.email) emailField.value = currentProfile.email;
        }

        // Render Cart items inside modal
        const modalItemsContainer = otpModal.querySelector("[data-otp-modal-items]");
        if (modalItemsContainer) {
          modalItemsContainer.innerHTML = "";
          cart.forEach(item => {
            const itemRow = document.createElement("div");
            itemRow.className = "flex gap-md py-xs border-b border-outline-variant/10 last:border-b-0 items-center";
            const imgSrc = escapeHtml(item.image || "");
            const title = escapeHtml(item.name || "Item");
            const meta = escapeHtml(item.meta || "Qty: 1");
            itemRow.innerHTML = `
              <div class="w-12 h-16 bg-surface-container rounded overflow-hidden flex-shrink-0">
                ${imgSrc ? `<img class="w-full h-full object-cover" alt="" src="${imgSrc}"/>` : `<div class="w-full h-full bg-surface-container-high"></div>`}
              </div>
              <div class="flex-grow min-w-0 px-sm">
                <p class="font-label-lg text-label-lg text-on-surface truncate">${title}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant">${meta}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <p class="font-label-md text-label-md text-on-surface">${money.format(Number(item.price || 0))}</p>
              </div>
            `;
            modalItemsContainer.appendChild(itemRow);
          });
        }

        // Set totals in modal
        const modalSubtotal = otpModal.querySelector("[data-otp-modal-subtotal]");
        const modalShipping = otpModal.querySelector("[data-otp-modal-shipping]");
        const modalTax = otpModal.querySelector("[data-otp-modal-tax]");
        const modalTotal = otpModal.querySelector("[data-otp-modal-total]");

        if (modalSubtotal) modalSubtotal.textContent = money.format(subtotalAmount);
        if (modalShipping) modalShipping.textContent = shippingCost === 0 ? "Free" : money.format(shippingCost);
        if (modalTax) modalTax.textContent = money.format(taxAmount);
        if (modalTotal) modalTotal.textContent = money.format(subtotalAmount + taxAmount + shippingCost);

        // Reset step view
        document.getElementById("otp-modal-step-1").classList.remove("hidden");
        document.getElementById("otp-modal-step-2").classList.add("hidden");

        otpModal.classList.add("is-open");
      });
    }

    // Modal listeners
    const otpModal = document.getElementById("otp-confirm-modal");
    if (otpModal) {
      const closeOtpModal = () => otpModal.classList.remove("is-open");
      otpModal.querySelectorAll("[data-close-otp-modal]").forEach(btn => {
        btn.addEventListener("click", closeOtpModal);
      });
      otpModal.addEventListener("click", (e) => {
        if (e.target === otpModal) closeOtpModal();
      });

      // Step 1 -> Step 2
      let debugOtpCode = "";
      const sendOtpBtn = otpModal.querySelector("[data-send-otp-btn]");
      sendOtpBtn.addEventListener("click", async () => {
        const emailField = document.getElementById("checkout-otp-email");
        const emailVal = emailField.value.trim();

        if (!isEmail(emailVal)) {
          showToast("Please enter a valid email address.", "error");
          emailField.focus();
          return;
        }

        sendOtpBtn.disabled = true;
        let deliverySent = false;
        let otpDelivery = {};
        try {
          const payload = await apiRequest("request_otp", {
            method: "POST",
            body: { email: emailVal },
          });
          debugOtpCode = payload.debug_code ? String(payload.debug_code) : "";
          otpDelivery = payload.delivery || {};
          deliverySent = Object.values(otpDelivery).some((result) => result?.status === "sent");
          if (!deliverySent && !debugOtpCode) {
            showToast(payload.message || "Configure SMTP email delivery before checkout.", "error");
            return;
          }
        } catch (error) {
          sendOtpBtn.disabled = false;
          showToast(error.message || "Could not send the verification code.", "error");
          return;
        } finally {
          sendOtpBtn.disabled = false;
        }

        const displayEmail = document.getElementById("display-email");
        if (displayEmail) displayEmail.textContent = emailVal;
        const debugOtpBox = otpModal.querySelector("[data-debug-otp-box]");
        const debugOtpDisplay = document.getElementById("debug-otp-display");
        const otpStatusTitle = otpModal.querySelector("[data-otp-status-title]");
        const otpStatusMessage = otpModal.querySelector("[data-otp-status-message]");

        if (otpStatusTitle) {
          otpStatusTitle.textContent = deliverySent ? "Verification Code Sent" : "Development OTP Generated";
        }
        if (otpStatusMessage) {
          if (deliverySent) {
            if (otpDelivery.email?.status === "sent") {
              otpStatusMessage.innerHTML = `We sent a code to <span id="display-email" class="font-semibold text-on-surface">${escapeHtml(emailVal)}</span>.`;
            } else {
              otpStatusMessage.textContent = "We sent a code to your configured email channel.";
            }
          } else {
            otpStatusMessage.textContent =
              "SMTP email delivery is not configured. Use the development code shown below.";
          }
        }
        if (debugOtpBox && debugOtpDisplay) {
          debugOtpDisplay.textContent = debugOtpCode;
          debugOtpBox.classList.toggle("hidden", !debugOtpCode);
        }

        document.getElementById("otp-modal-step-1").classList.add("hidden");
        document.getElementById("otp-modal-step-2").classList.remove("hidden");

        const otpInput = document.getElementById("checkout-otp-input");
        if (otpInput) {
          otpInput.value = "";
          otpInput.focus();
        }
      });

      // Step 2 Back Button
      const backOtpBtn = otpModal.querySelector("[data-back-to-otp-step1]");
      backOtpBtn.addEventListener("click", () => {
        document.getElementById("otp-modal-step-2").classList.add("hidden");
        document.getElementById("otp-modal-step-1").classList.remove("hidden");
      });

      // Verify OTP
      const verifyOtpBtn = otpModal.querySelector("[data-verify-otp-btn]");
      verifyOtpBtn.addEventListener("click", async () => {
        const otpInput = document.getElementById("checkout-otp-input");
        const codeVal = otpInput.value.trim();

        const emailField = document.getElementById("checkout-otp-email");
        const emailVal = emailField.value.trim();
        const firstNameVal = document.querySelector('input[name="first_name"]')?.value?.trim() || "";
        const lastNameVal = document.querySelector('input[name="last_name"]')?.value?.trim() || "";
        const addressLineVal = document.querySelector('input[name="address"]')?.value?.trim() || "";
        const cityVal = document.querySelector('input[name="city"]')?.value?.trim() || "";
        const postalCodeVal = document.querySelector('input[name="postal_code"]')?.value?.trim() || "";
        const cart = readCart();
        const orderTotal = subtotalAmount + taxAmount + shippingCost;
        const shippingAddress = {
          id: String(Date.now()),
          label: selectedAddressLabel,
          first_name: firstNameVal,
          last_name: lastNameVal,
          address_line: addressLineVal,
          city: cityVal,
          postal_code: postalCodeVal
        };

        verifyOtpBtn.disabled = true;
        let profile = null;

        try {
          const verified = await apiRequest("verify_otp", {
            method: "POST",
            body: { email: emailVal, code: codeVal },
          });
          if (verified.profile) {
            writeProfileCache(verified.profile);
            dispatchProfileUpdated(verified.profile);
            syncWishlistToBackend(readWishlist());
          }
        } catch (error) {
          verifyOtpBtn.disabled = false;
          showToast(error.message || "Invalid verification code. Please check and try again.", "error");
          otpInput.classList.add("border-error");
          otpInput.focus();
          return;
        }

        otpInput.classList.remove("border-error");

        try {
          const created = await apiRequest("orders/create", {
            method: "POST",
            body: {
              address: shippingAddress,
              items: cart,
              subtotal: subtotalAmount,
              shipping: shippingCost,
              tax: taxAmount,
              total: orderTotal,
            },
          });
          profile = created.profile || readProfile();
          if (profile) {
            writeProfileCache(profile);
            dispatchProfileUpdated(profile);
          }
          writeCartCache(Array.isArray(created.cart) ? created.cart : []);
        } catch (error) {
          verifyOtpBtn.disabled = false;
          showToast(error.message || "Could not save the order to the backend.", "error");
          return;
        }

        verifyOtpBtn.disabled = false;

        // Clear the local cart after the backend confirms the order.
        writeCartCache([]);
        updateBagCountDisplay();
        renderLineItems();
        if (options[0]) {
          setShipping(options[0]);
        }

        // Re-initialize selector prefill dropdown
        if (profile) {
          populateSavedAddresses(profile);
        }

        otpModal.classList.remove("is-open");
        showToast("Verification successful! Your order has been placed and linked to your profile.");
      });
    }
  }

  function initCartLineAdds() {
    document.addEventListener("click", (event) => {
      const trigger = event.target.closest("a.js-cart-add-line");
      if (!trigger) {
        return;
      }

      const host = trigger.closest("[data-cart-name]");
      let name = host?.dataset.cartName?.trim();
      let price = Number(host?.dataset.cartPrice || 0);
      let image = host?.dataset.cartImage || "";
      let meta = "Qty: 1";
      let productId = host ? getProductIdFromCard(host) : "";

      if (!name) {
        const card = trigger.closest("[data-collection-product]");
        if (card) {
          productId = getProductIdFromCard(card);
          name = card.querySelector("h3")?.textContent?.trim() || "Item";
          price = Number(card.dataset.price || 0);
          image = card.querySelector("img")?.getAttribute("src") || "";
          meta = card.dataset.activeColor ? `Color: ${card.dataset.activeColor}` : meta;
        }
      }

      if (!name || !price) {
        return;
      }

      try {
        const cart = readCart();
        cart.push({
          id: String(Date.now()),
          productId,
          name,
          price,
          image,
          meta,
        });
        writeCart(cart);
      } catch (error) {
        // ignore
      }
      updateBagCountDisplay();
    });
  }

  function initProfileModal() {
    let profileModal = document.getElementById("luxe-profile-modal");
    if (!profileModal) {
      profileModal = document.createElement("div");
      profileModal.id = "luxe-profile-modal";
      profileModal.className = "luxe-modal";
      profileModal.setAttribute("role", "dialog");
      profileModal.setAttribute("aria-modal", "true");
      profileModal.innerHTML = `
        <div class="luxe-modal-panel w-full max-w-lg bg-surface-container-lowest border border-outline-variant/30 rounded-xl shadow-lg overflow-hidden transition-all duration-300">
          <div class="luxe-modal-body p-lg space-y-md">
            <div class="flex justify-between items-center border-b border-outline-variant/20 pb-sm">
              <h2 class="font-headline-md text-headline-md text-on-surface">User Profile</h2>
              <button type="button" class="p-xs text-on-surface hover:text-primary transition-colors flex items-center justify-center rounded-full hover:bg-surface-container-low" data-close-profile-modal="" aria-label="Close profile">
                <span class="material-symbols-outlined text-[20px]">close</span>
              </button>
            </div>

            <!-- Profile Active View (Logged In) -->
            <div id="profile-logged-in" class="space-y-md hidden">
              <div class="flex items-center gap-md p-md bg-surface-container-low rounded-lg border border-outline-variant/20">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-[28px]">person</span>
                </div>
                <div class="overflow-hidden">
                  <p class="font-label-lg text-label-lg text-on-surface truncate" id="profile-email-display"></p>
                </div>
              </div>

              <!-- Address Book Section -->
              <div class="space-y-sm pt-xs border-t border-outline-variant/10">
                <div class="flex justify-between items-center">
                  <h3 class="font-label-lg text-label-lg text-on-surface tracking-wider uppercase font-semibold">Address Book</h3>
                  <button type="button" class="text-primary font-label-md text-label-md hover:underline flex items-center gap-xs font-semibold" id="profile-add-address-btn">
                    <span class="material-symbols-outlined text-[16px]">add</span> Add Address
                  </button>
                </div>

                <!-- Add Address Form (Hidden by default) -->
                <div id="profile-address-form-container" class="hidden p-sm bg-surface-container-low rounded-lg border border-outline-variant/30 space-y-sm">
                  <h4 class="font-label-md text-label-md text-on-surface font-semibold">New Address</h4>
                  <div class="flex gap-xs" id="modal-address-label-selector">
                    <button type="button" class="flex-1 py-xs px-xs border border-primary bg-primary text-white rounded text-center text-xs font-medium" data-modal-label="Home">Home</button>
                    <button type="button" class="flex-1 py-xs px-xs border border-outline-variant text-on-surface-variant rounded text-center text-xs" data-modal-label="Work">Work</button>
                    <button type="button" class="flex-1 py-xs px-xs border border-outline-variant text-on-surface-variant rounded text-center text-xs" data-modal-label="Other">Other</button>
                  </div>
                  <div class="grid grid-cols-2 gap-xs text-xs">
                    <input id="modal-addr-first" placeholder="First Name" class="p-xs border border-outline-variant rounded bg-surface-container-lowest outline-none focus:border-primary" />
                    <input id="modal-addr-last" placeholder="Last Name" class="p-xs border border-outline-variant rounded bg-surface-container-lowest outline-none focus:border-primary" />
                    <input id="modal-addr-line" placeholder="Street Address" class="col-span-2 p-xs border border-outline-variant rounded bg-surface-container-lowest outline-none focus:border-primary" />
                    <input id="modal-addr-city" placeholder="City" class="p-xs border border-outline-variant rounded bg-surface-container-lowest outline-none focus:border-primary" />
                    <input id="modal-addr-zip" placeholder="Postal Code" class="p-xs border border-outline-variant rounded bg-surface-container-lowest outline-none focus:border-primary" />
                  </div>
                  <div class="flex gap-xs pt-xs text-xs">
                    <button type="button" class="flex-1 py-xs border border-outline-variant text-on-surface-variant rounded hover:bg-surface-container-low" id="profile-cancel-address-btn">Cancel</button>
                    <button type="button" class="flex-1 py-xs bg-primary text-on-primary rounded font-semibold hover:opacity-90" id="profile-save-address-btn">Save Address</button>
                  </div>
                </div>

                <div class="max-h-40 overflow-y-auto space-y-sm pr-xs" id="profile-addresses-list">
                  <!-- Dynamic Address list -->
                </div>
              </div>

              <!-- Order History Section -->
              <div class="space-y-sm pt-xs border-t border-outline-variant/10">
                <h3 class="font-label-lg text-label-lg text-on-surface tracking-wider uppercase font-semibold">Order History</h3>
                <div class="max-h-48 overflow-y-auto space-y-sm pr-xs" id="profile-orders-list">
                  <!-- Dynamic Order History Rows -->
                </div>
              </div>

              <button type="button" class="w-full py-md border border-error text-error hover:bg-error-container/10 font-label-lg text-label-lg rounded-lg transition-all" id="profile-signout-btn">
                Sign Out / Clear Profile
              </button>
            </div>

            <!-- Profile Guest View (Not Logged In) -->
            <div id="profile-guest" class="space-y-md text-center py-md">
              <div class="w-16 h-16 rounded-full bg-surface-container-high text-secondary flex items-center justify-center mx-auto mb-sm">
                <span class="material-symbols-outlined text-[36px]">no_accounts</span>
              </div>
              <h3 class="font-headline-sm text-headline-sm text-on-surface">No Profile Found</h3>
              <p class="font-body-sm text-body-sm text-on-surface-variant max-w-sm mx-auto">
                Complete a quick checkout order with your email to automatically create and link your profile.
              </p>
              <button type="button" class="px-lg py-sm bg-primary text-on-primary font-label-lg text-label-lg rounded-lg hover:opacity-90 transition-all" data-close-profile-modal-btn="">
                Continue Browsing
              </button>
            </div>

            <div class="space-y-sm pt-xs border-t border-outline-variant/10">
              <div class="flex justify-between items-center">
                <h3 class="font-label-lg text-label-lg text-on-surface tracking-wider uppercase font-semibold" data-profile-wishlist-title="">Wishlist</h3>
              </div>
              <div class="max-h-56 overflow-y-auto space-y-sm pr-xs" data-profile-wishlist-list="">
                <!-- Dynamic Wishlist Rows -->
              </div>
            </div>
          </div>
        </div>
      `;
      document.body.appendChild(profileModal);
    }

    const closeProfile = () => profileModal.classList.remove("is-open");
    profileModal.querySelectorAll("[data-close-profile-modal], [data-close-profile-modal-btn]").forEach(btn => {
      btn.addEventListener("click", closeProfile);
    });
    profileModal.addEventListener("click", (e) => {
      if (e.target === profileModal) closeProfile();
    });

    const accountBtns = document.querySelectorAll('a[aria-label="Account"]');
    accountBtns.forEach(btn => {
      btn.addEventListener("click", async (event) => {
        event.preventDefault();
        await initBackendState();
        openProfileModal();
      });
    });

    let modalSelectedLabel = "Home";
    const modalLabelBtns = profileModal.querySelectorAll("[data-modal-label]");
    modalLabelBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        modalSelectedLabel = btn.dataset.modalLabel;
        modalLabelBtns.forEach(b => {
          if (b === btn) {
            b.className = "flex-1 py-xs px-xs border border-primary bg-primary text-white rounded text-center text-xs font-medium";
          } else {
            b.className = "flex-1 py-xs px-xs border border-outline-variant text-on-surface-variant rounded text-center text-xs";
          }
        });
      });
    });

    const addAddressBtn = profileModal.querySelector("#profile-add-address-btn");
    const addressFormContainer = profileModal.querySelector("#profile-address-form-container");
    const cancelAddressBtn = profileModal.querySelector("#profile-cancel-address-btn");
    const saveAddressBtn = profileModal.querySelector("#profile-save-address-btn");

    if (addAddressBtn && addressFormContainer) {
      addAddressBtn.addEventListener("click", () => {
        addressFormContainer.classList.toggle("hidden");
        profileModal.querySelector("#modal-addr-first").value = "";
        profileModal.querySelector("#modal-addr-last").value = "";
        profileModal.querySelector("#modal-addr-line").value = "";
        profileModal.querySelector("#modal-addr-city").value = "";
        profileModal.querySelector("#modal-addr-zip").value = "";
        const defaultModalLabelBtn = profileModal.querySelector('[data-modal-label="Home"]');
        if (defaultModalLabelBtn) defaultModalLabelBtn.click();
      });
    }

    if (cancelAddressBtn && addressFormContainer) {
      cancelAddressBtn.addEventListener("click", () => {
        addressFormContainer.classList.add("hidden");
      });
    }

    if (saveAddressBtn) {
      saveAddressBtn.addEventListener("click", async () => {
        const first = profileModal.querySelector("#modal-addr-first").value.trim();
        const last = profileModal.querySelector("#modal-addr-last").value.trim();
        const line = profileModal.querySelector("#modal-addr-line").value.trim();
        const city = profileModal.querySelector("#modal-addr-city").value.trim();
        const zip = profileModal.querySelector("#modal-addr-zip").value.trim();

        if (!first || !line || !city || !zip) {
          showToast("Please fill all address fields.", "error");
          return;
        }

        let profile = readProfile();

        if (profile) {
          if (!profile.addresses) profile.addresses = [];
          const newAddr = {
            id: String(Date.now()),
            label: modalSelectedLabel,
            first_name: first,
            last_name: last,
            address_line: line,
            city: city,
            postal_code: zip
          };

          const existingIdx = profile.addresses.findIndex(addr => addr.label.toLowerCase() === modalSelectedLabel.toLowerCase());
          if (existingIdx > -1) {
            profile.addresses[existingIdx] = newAddr;
          } else {
            profile.addresses.push(newAddr);
          }

          try {
            const saved = await apiRequest("addresses/save", {
              method: "POST",
              body: { address: newAddr },
            });
            profile = saved.profile || profile;
          } catch (error) {
            // Keep the local profile cache usable if the backend is offline.
          }

          writeProfileCache(profile);
          dispatchProfileUpdated(profile);
          showToast("Address saved successfully.");
          addressFormContainer.classList.add("hidden");
          openProfileModal();
        }
      });
    }

    const addrListContainer = profileModal.querySelector("#profile-addresses-list");
    if (addrListContainer) {
      addrListContainer.addEventListener("click", async (e) => {
        const deleteBtn = e.target.closest("[data-delete-address]");
        if (deleteBtn) {
          const addrId = deleteBtn.dataset.deleteAddress;
          let profile = readProfile();
          try {
            const deleted = await apiRequest("addresses/delete", {
              method: "POST",
              body: { id: addrId },
            });
            profile = deleted.profile || profile;
          } catch (e) {}

          if (profile && Array.isArray(profile.addresses)) {
            profile.addresses = profile.addresses.filter(addr => addr.id !== addrId);
            writeProfileCache(profile);
            dispatchProfileUpdated(profile);
            showToast("Address removed.");
            openProfileModal();
          }
        }
      });
    }

    profileModal.addEventListener("click", (event) => {
      const removeWishlist = event.target.closest("[data-remove-wishlist]");
      if (removeWishlist) {
        const id = removeWishlist.dataset.removeWishlist;
        writeWishlist(readWishlist().filter((item) => item.id !== id));
        renderWishlistButton(document.querySelector("[data-wishlist]"));
        renderWishlistLists();
        showToast(t("removedWishlistToast"));
        return;
      }

      const addWishlist = event.target.closest("[data-wishlist-add-to-bag]");
      if (!addWishlist) {
        return;
      }

      const item = readWishlist().find((saved) => saved.id === addWishlist.dataset.wishlistAddToBag);
      if (!item) {
        return;
      }

      const meta = [item.size, item.color].filter(Boolean).join(" • ") || "Qty: 1";
      const cart = readCart();
      cart.push({
        id: String(Date.now()),
        productId: item.id || "",
        name: item.name,
        price: Number(item.price || 0),
        image: item.image || "",
        meta,
      });
      writeCart(cart);
      updateBagCountDisplay();
      showToast(t("addedBagToast"));
    });

    function openProfileModal() {
      const loggedInView = document.getElementById("profile-logged-in");
      const guestView = document.getElementById("profile-guest");
      const profile = readProfile();

      if (profile && profile.email) {
        guestView.classList.add("hidden");
        loggedInView.classList.remove("hidden");

        document.getElementById("profile-email-display").textContent = profile.email;

        // Render Address Book
        const addressesList = document.getElementById("profile-addresses-list");
        addressesList.innerHTML = "";

        const addresses = Array.isArray(profile.addresses) ? profile.addresses : [];
        if (addresses.length === 0) {
          addressesList.innerHTML = `<p class="font-body-sm text-body-sm text-on-surface-variant text-center py-xs">No saved addresses yet.</p>`;
        } else {
          addresses.forEach(addr => {
            const addrRow = document.createElement("div");
            addrRow.className = "flex justify-between items-center p-sm bg-surface-container rounded-lg border border-outline-variant/20 text-body-sm mb-xs";
            addrRow.innerHTML = `
              <div class="min-w-0 pr-sm text-xs">
                <p class="font-semibold text-on-surface flex items-center gap-xs">
                  <span class="px-xs py-[2px] bg-primary/10 text-primary text-[9px] uppercase font-bold rounded">${escapeHtml(addr.label)}</span>
                  ${escapeHtml(addr.first_name)} ${escapeHtml(addr.last_name)}
                </p>
                <p class="text-on-surface-variant truncate">${escapeHtml(addr.address_line)}, ${escapeHtml(addr.city)}, ${escapeHtml(addr.postal_code)}</p>
              </div>
              <button type="button" class="text-error hover:text-error/80 flex items-center justify-center p-xs flex-shrink-0" data-delete-address="${addr.id}" aria-label="Delete address">
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
            `;
            addressesList.appendChild(addrRow);
          });
        }

        // Render Order History
        const listContainer = document.getElementById("profile-orders-list");
        listContainer.innerHTML = "";

        const orders = Array.isArray(profile.orders) ? profile.orders : [];
        if (orders.length === 0) {
          listContainer.innerHTML = `<p class="font-body-sm text-body-sm text-on-surface-variant text-center py-sm">No orders placed yet.</p>`;
        } else {
          orders.forEach(order => {
            const row = document.createElement("div");
            row.className = "p-sm bg-surface-container-low rounded-lg border border-outline-variant/20 space-y-xs mb-sm";

            const dateStr = escapeHtml(order.date || "");
            const idStr = escapeHtml(order.id || "");
            const totalStr = money.format(Number(order.total || 0));

            let itemsHtml = "";
            if (Array.isArray(order.items)) {
              order.items.forEach(item => {
                itemsHtml += `<div class="text-body-sm text-on-surface-variant">• ${escapeHtml(item.name)} (${escapeHtml(item.meta || "Qty: 1")})</div>`;
              });
            }

            row.innerHTML = `
              <div class="flex justify-between items-center font-label-md text-label-md">
                <span class="text-primary">${idStr}</span>
                <span class="text-secondary font-normal">${dateStr}</span>
              </div>
              <div class="space-y-xs py-xs">
                ${itemsHtml}
              </div>
              <div class="flex justify-between items-center text-body-sm pt-xs border-t border-outline-variant/10">
                <span class="text-on-surface-variant">Total Paid:</span>
                <span class="font-semibold text-on-surface">${totalStr}</span>
              </div>
            `;
            listContainer.appendChild(row);
          });
        }
      } else {
        loggedInView.classList.add("hidden");
        guestView.classList.remove("hidden");
      }

      renderWishlistLists();
      profileModal.classList.add("is-open");
    }

    const signoutBtn = document.getElementById("profile-signout-btn");
    if (signoutBtn) {
      signoutBtn.addEventListener("click", () => {
        apiRequest("logout", { method: "POST", body: {} }).catch(() => {});
        writeProfileCache(null);
        dispatchProfileUpdated(null);
        showToast("Signed out. Profile data cleared.");
        closeProfile();
      });
    }
  }

  document.addEventListener("DOMContentLoaded", async () => {
    setButtonDefaults();
    hydrateImageAltText();
    normalizeHeaderActions();
    await initProductFromQuery();
    initMobileMenus();
    initHomeCarousel();
    initNewsletterForms();
    await hydrateApprovedProductCards();
    initCollectionCardColorSwatches();
    initCollectionFilters();
    initProductOptions();
    initSizeGuide();
    initCartLineAdds();
    initCheckout();
    initProfileModal();
    initActiveNavState();
    window.addEventListener("load", initActiveNavState, { once: true });
    window.addEventListener("resize", initActiveNavState);
    updateBagCountDisplay();
    initBackendState();
  });
})();
