<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>LUXE | Checkout</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Work+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "inverse-primary": "#b8c3ff",
                        "surface": "#f9f9f9",
                        "tertiary": "#555555",
                        "on-tertiary-container": "#f2f0ef",
                        "inverse-on-surface": "#f0f1f1",
                        "tertiary-container": "#6d6d6d",
                        "surface-container-low": "#f3f3f3",
                        "on-surface": "#1a1c1c",
                        "primary": "#0040df",
                        "primary-container": "#2d5bff",
                        "surface-dim": "#dadada",
                        "on-primary-fixed-variant": "#0035bd",
                        "secondary-fixed": "#e5e2e1",
                        "primary-fixed": "#dde1ff",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#747688",
                        "surface-container-high": "#e8e8e8",
                        "surface-variant": "#e2e2e2",
                        "on-error-container": "#93000a",
                        "on-surface-variant": "#434656",
                        "on-primary-container": "#efefff",
                        "on-secondary-container": "#656464",
                        "on-error": "#ffffff",
                        "surface-bright": "#f9f9f9",
                        "on-background": "#1a1c1c",
                        "inverse-surface": "#2f3131",
                        "secondary": "#5f5e5e",
                        "surface-tint": "#104af0",
                        "on-secondary-fixed": "#1c1b1b",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-fixed": "#1b1c1c",
                        "on-secondary": "#ffffff",
                        "background": "#f9f9f9",
                        "secondary-fixed-dim": "#c8c6c5",
                        "tertiary-fixed": "#e4e2e2",
                        "on-tertiary-fixed-variant": "#464747",
                        "secondary-container": "#e5e2e1",
                        "error-container": "#ffdad6",
                        "tertiary-fixed-dim": "#c7c6c6",
                        "on-primary": "#ffffff",
                        "on-secondary-fixed-variant": "#474646",
                        "on-primary-fixed": "#001355",
                        "outline-variant": "#c4c5d9",
                        "primary-fixed-dim": "#b8c3ff",
                        "error": "#ba1a1a",
                        "surface-container-highest": "#e2e2e2",
                        "surface-container": "#eeeeee"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "sm": "12px",
                        "base": "8px",
                        "gutter": "24px",
                        "md": "24px",
                        "lg": "48px",
                        "container-max": "1280px",
                        "xs": "4px",
                        "xl": "80px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Inter"],
                        "body-md": ["Work Sans"],
                        "headline-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-sm": ["Work Sans"],
                        "headline-xl-mobile": ["Inter"],
                        "label-lg": ["Work Sans"],
                        "body-lg": ["Work Sans"],
                        "label-md": ["Work Sans"],
                        "headline-xl": ["Inter"],
                        "headline-lg-mobile": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "headline-xl-mobile": ["36px", {"lineHeight": "44px", "fontWeight": "700"}],
                        "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "label-md": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "headline-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            background-color: #f9f9f9;
            color: #1a1c1c;
        }
    </style>
<link href="assets/luxe-mark.svg" rel="icon" type="image/svg+xml"/>
<link href="assets/css/site.css" rel="stylesheet"/>
</head>
<body class="antialiased">
<!-- Top Navigation Bar -->
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md shadow-sm border-b border-outline-variant/30">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="font-headline-md text-headline-md font-bold text-on-surface dark:text-inverse-on-surface tracking-tighter" href="index.php">LUXE</a>
<nav class="hidden md:flex items-center gap-lg">
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="collections.php?view=new-arrivals">New Arrivals</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="collections.php">Collections</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="collections.php?segment=men">Men</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="collections.php?segment=women">Women</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="collections.php?segment=accessories">Accessories</a>
</nav>
<div class="flex items-center gap-md">
<a aria-label="View checkout" class="relative inline-flex items-center text-primary dark:text-inverse-primary hover:opacity-80 transition-opacity" href="checkout.php">
<span class="material-symbols-outlined text-primary dark:text-inverse-primary" data-icon="shopping_bag">shopping_bag</span>
<span class="absolute -top-0.5 -right-1 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
	</a>
	<a aria-label="Account" class="material-symbols-outlined text-primary dark:text-inverse-primary hover:opacity-80 transition-opacity" data-icon="person" href="index.php">person</a>
	<button class="md:hidden p-base text-primary" type="button">
	<span class="material-symbols-outlined">menu</span>
	</button>
	</div>
	</div>
	</header>
<main class="max-w-container-max mx-auto px-gutter py-xl">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-xl">
<!-- Left Side: Checkout Form -->
<div class="lg:col-span-7 space-y-xl">
<!-- Shipping Address Section -->
<section>
<div class="flex items-center gap-sm mb-lg">
<span class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">1</span>
<h2 class="font-headline-lg text-headline-lg">Shipping Address</h2>
</div>

<!-- Saved Addresses Selector (shown only if logged in and has addresses) -->
<div id="saved-addresses-selector-container" class="hidden mb-md">
  <label class="block font-label-md text-label-md mb-xs text-on-surface font-semibold">Saved Shipping Address</label>
  <select id="saved-addresses-select" class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors font-body-md bg-surface-container-lowest">
    <option value="new">-- Enter New Address --</option>
  </select>
</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div class="col-span-1">
<label class="block font-label-md text-label-md mb-xs">First Name</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="first_name" placeholder="Jane" required="" type="text"/>
</div>
<div class="col-span-1">
<label class="block font-label-md text-label-md mb-xs">Last Name</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="last_name" placeholder="Doe" required="" type="text"/>
</div>
	<div class="sm:col-span-2">
<label class="block font-label-md text-label-md mb-xs">Address</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="address" placeholder="123 Luxury Lane" required="" type="text"/>
</div>
	<div class="sm:col-span-1">
<label class="block font-label-md text-label-md mb-xs">City</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="city" placeholder="New York" required="" type="text"/>
</div>
	<div class="sm:col-span-1">
<label class="block font-label-md text-label-md mb-xs">Postal Code</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="postal_code" placeholder="10001" required="" type="text"/>
</div>

	<div class="sm:col-span-2">
  <label class="block font-label-md text-label-md mb-xs font-semibold text-on-surface">Save This Address As</label>
  <div class="flex gap-sm" id="address-label-selector">
    <button type="button" class="flex-1 py-xs px-sm border border-primary bg-primary text-white rounded-lg transition-all font-body-sm text-body-sm text-center font-medium" data-address-label="Home">
      Home
    </button>
    <button type="button" class="flex-1 py-xs px-sm border border-outline-variant text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all font-body-sm text-body-sm text-center" data-address-label="Work">
      Work
    </button>
    <button type="button" class="flex-1 py-xs px-sm border border-outline-variant text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all font-body-sm text-body-sm text-center" data-address-label="Other">
      Other
    </button>
  </div>
</div>
</div>
</section>
<!-- Shipping Method Section -->
<section>
<div class="flex items-center gap-sm mb-lg">
<span class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">2</span>
<h2 class="font-headline-lg text-headline-lg">Shipping Method</h2>
</div>
<div class="space-y-md">
<label class="flex items-center justify-between p-md border border-outline-variant rounded-lg cursor-pointer hover:border-primary transition-colors is-selected" data-shipping-cost="0" data-shipping-option="">
<div class="flex items-center gap-md">
<input checked="" class="w-5 h-5 text-primary border-outline focus:ring-primary" name="shipping" type="radio"/>
<div>
<p class="font-label-lg text-label-lg text-on-surface">Standard Shipping</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">3-5 business days</p>
</div>
</div>
<span class="font-label-lg text-label-lg text-primary">Free</span>
</label>
<label class="flex items-center justify-between p-md border border-outline-variant rounded-lg cursor-pointer hover:border-primary transition-colors" data-shipping-cost="25" data-shipping-option="">
<div class="flex items-center gap-md">
<input class="w-5 h-5 text-primary border-outline focus:ring-primary" name="shipping" type="radio"/>
<div>
<p class="font-label-lg text-label-lg text-on-surface">Express Delivery</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Next day delivery</p>
</div>
</div>
<span class="font-label-lg text-label-lg text-on-surface">$25.00</span>
</label>
</div>
</section>
<!-- Payment Details Section -->
<section>
<div class="flex items-center gap-sm mb-lg">
<span class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold">3</span>
<h2 class="font-headline-lg text-headline-lg">Payment Details</h2>
</div>
<div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/30 space-y-md">
<div class="relative">
<label class="block font-label-md text-label-md mb-xs">Card Number</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="card_number" placeholder="0000 0000 0000 0000" required="" type="text"/>
<div class="absolute right-3 bottom-2.5 flex gap-xs">
<span class="material-symbols-outlined text-outline" data-icon="credit_card">credit_card</span>
</div>
</div>
	<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs">Expiry Date</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="expiry" placeholder="MM/YY" required="" type="text"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs">CVV</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors" data-checkout-required="" name="cvv" placeholder="123" required="" type="text"/>
</div>
</div>
</div>
</section>
<button class="w-full py-md bg-primary text-on-primary font-label-lg text-label-lg rounded-lg shadow-md hover:opacity-90 active:scale-[0.98] transition-all" data-complete-purchase="">
                    Complete Purchase
                </button>
</div>
<!-- Right Side: Order Summary (Sticky) -->
<div class="lg:col-span-5">
<aside class="sticky top-24 bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-lg shadow-sm">
<h3 class="font-headline-sm text-headline-sm mb-lg border-b border-outline-variant/30 pb-sm">Order Summary</h3>
<!-- Item List (filled from bag in localStorage; empty until you add items) -->
<div class="mb-lg">
<div class="space-y-md" data-checkout-cart-items=""></div>
<div class="rounded-lg border border-outline-variant/40 bg-surface-container-low p-md text-center" data-checkout-empty="">
<p class="font-body-md text-body-md text-on-surface-variant mb-sm">Your bag is empty.</p>
<a class="font-label-lg text-label-lg text-primary underline underline-offset-4" href="collections.php">Continue shopping</a>
</div>
</div>
<!-- Price Breakdown -->
<div class="space-y-sm border-t border-outline-variant/30 pt-lg">
<div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
<span>Subtotal</span>
<span data-order-subtotal="">$0.00</span>
</div>
<div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
<span>Shipping</span>
<span class="text-primary font-semibold" data-order-shipping="">Free</span>
</div>
<div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
<span>Estimated Tax</span>
<span data-order-tax="">$0.00</span>
</div>
<div class="flex justify-between font-headline-sm text-headline-sm text-on-surface pt-md border-t border-outline-variant/30 mt-md">
<span>Total</span>
<span data-order-total="">$0.00</span>
</div>
</div>
<div class="mt-lg p-sm bg-surface-container-low rounded-lg flex items-center gap-sm">
<span class="material-symbols-outlined text-primary text-[20px]" data-icon="verified_user">verified_user</span>
<p class="font-label-md text-label-md text-secondary">Secure checkout with 256-bit SSL encryption</p>
</div>
</aside>
</div>
</div>
</main>
<!-- Footer Component -->
<footer class="w-full bg-surface-container-low dark:bg-surface-container-highest border-t border-outline-variant mt-xl">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto">
<div class="md:col-span-1">
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface dark:text-inverse-on-surface mb-md">LUXE</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80">© 2024 LUXE Premium E-commerce. All rights reserved.</p>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Support</h5>
<ul class="space-y-sm font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80">
<li><a class="hover:text-primary transition-colors" href="product.php#shipping-returns">Shipping &amp; Returns</a></li>
<li><a class="hover:text-primary transition-colors" href="checkout.php">Contact Us</a></li>
<li><a class="hover:text-primary transition-colors" href="index.php#sustainability">Sustainability</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Legal</h5>
<ul class="space-y-sm font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80">
<li><a class="hover:text-primary transition-colors" href="checkout.php">Privacy Policy</a></li>
<li><a class="hover:text-primary transition-colors" href="checkout.php">Terms of Service</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Newsletter</h5>
<div class="flex gap-xs">
<input class="bg-surface-container-lowest border border-outline-variant px-sm py-xs rounded w-full text-body-sm outline-none focus:border-primary" placeholder="Email address" type="email"/>
<button class="bg-secondary text-white px-md py-xs rounded hover:opacity-90 transition-opacity" type="button">Join</button>
</div>
</div>
</div>
</footer>

<!-- OTP Confirmation Modal -->
<div id="otp-confirm-modal" class="luxe-modal" role="dialog" aria-modal="true">
  <div class="luxe-modal-panel w-full max-w-lg bg-surface-container-lowest border border-outline-variant/30 rounded-xl shadow-lg overflow-hidden transition-all duration-300">
    <div class="luxe-modal-body p-lg space-y-md">
      <div class="flex justify-between items-center border-b border-outline-variant/20 pb-sm">
        <h2 class="font-headline-md text-headline-md text-on-surface">Confirm Your Order</h2>
        <button type="button" class="p-xs text-on-surface hover:text-primary transition-colors flex items-center justify-center rounded-full hover:bg-surface-container-low" data-close-otp-modal="" aria-label="Close confirmation modal">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>
      
      <!-- Step 1: Input Email/Phone & Confirm Details -->
      <div id="otp-modal-step-1" class="space-y-md">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
          Confirm your items and enter your details below. We'll send a 4-digit verification code to your phone and email to link this order to your profile.
        </p>
        
        <!-- Cart Items Preview -->
        <div class="max-h-48 overflow-y-auto space-y-sm pr-xs border border-outline-variant/20 rounded-lg p-sm bg-surface-container-low" data-otp-modal-items="">
          <!-- JS will populate these -->
        </div>
        
        <!-- Totals Summary -->
        <div class="p-sm bg-surface-container rounded-lg space-y-xs font-body-sm text-body-sm">
          <div class="flex justify-between text-on-surface-variant">
            <span>Subtotal:</span>
            <span data-otp-modal-subtotal="">$0.00</span>
          </div>
          <div class="flex justify-between text-on-surface-variant">
            <span>Shipping:</span>
            <span data-otp-modal-shipping="">Free</span>
          </div>
          <div class="flex justify-between text-on-surface-variant mb-xs">
            <span>Estimated Tax:</span>
            <span data-otp-modal-tax="">$0.00</span>
          </div>
          <div class="flex justify-between font-label-lg text-label-lg text-on-surface border-t border-outline-variant/30 pt-xs">
            <span>Total:</span>
            <span data-otp-modal-total="">$0.00</span>
          </div>
        </div>

        <!-- User Information Fields -->
        <div class="grid grid-cols-1 gap-md">
          <div>
            <label class="block font-label-md text-label-md mb-xs text-on-surface">Email Address</label>
            <input id="checkout-otp-email" type="email" class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors font-body-md" placeholder="name@example.com" />
          </div>
          <div>
            <label class="block font-label-md text-label-md mb-xs text-on-surface">Phone Number</label>
            <input id="checkout-otp-phone" type="tel" class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors font-body-md" placeholder="+1 (555) 000-0000" />
          </div>
        </div>

        <button type="button" class="w-full py-md bg-primary text-on-primary font-label-lg text-label-lg rounded-lg shadow-md hover:opacity-90 active:scale-[0.98] transition-all" data-send-otp-btn="">
          Send Verification Code
        </button>
      </div>

      <!-- Step 2: Input OTP -->
      <div id="otp-modal-step-2" class="space-y-md hidden">
        <div class="text-center p-md bg-primary/10 text-on-primary-fixed-variant rounded-lg border border-outline-variant/30 space-y-xs">
          <p class="font-label-lg text-label-lg text-primary">Verification Code Sent!</p>
          <p class="font-body-sm text-body-sm text-on-surface-variant">We've simulated sending a code to <span id="display-phone" class="font-semibold text-on-surface"></span> and a confirmation email to <span id="display-email" class="font-semibold text-on-surface"></span>.</p>
          <div class="mt-xs py-xs px-md bg-surface-container-lowest rounded border border-outline-variant/30 inline-block font-mono text-label-lg text-primary">
            MOCK OTP: <span id="mock-otp-display" class="font-bold tracking-widest text-[16px]"></span>
          </div>
        </div>

        <div class="space-y-sm">
          <label class="block font-label-md text-label-md mb-xs text-center text-on-surface">Enter 4-Digit Passcode</label>
          <input id="checkout-otp-input" type="text" maxlength="4" class="w-36 mx-auto text-center tracking-[0.5em] font-mono text-headline-md px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none transition-colors block" placeholder="0000" />
        </div>

        <div class="flex gap-base mt-md">
          <button type="button" class="flex-1 py-md border border-outline-variant text-on-surface font-label-lg text-label-lg rounded-lg hover:bg-surface-container-low transition-all" data-back-to-otp-step1="">
            Back
          </button>
          <button type="button" class="flex-1 py-md bg-primary text-on-primary font-label-lg text-label-lg rounded-lg shadow-md hover:opacity-90 active:scale-[0.98] transition-all" data-verify-otp-btn="">
            Verify & Confirm Order
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/site.js"></script>
</body></html>
