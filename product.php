<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Work+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
          }
        }
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { background-color: #f9f9f9; }
    </style>
<link href="assets/luxe-mark.svg" rel="icon" type="image/svg+xml"/>
<link href="assets/css/site.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background">
<!-- TopNavBar -->
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md shadow-sm border-b border-outline-variant/30">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="font-headline-md text-headline-md font-bold text-on-surface dark:text-inverse-on-surface tracking-tighter" href="index.php">LUXE</a>
<nav class="hidden md:flex items-center gap-lg">
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors" href="collections.php?view=new-arrivals">New Arrivals</a>
<a class="font-label-lg text-label-lg text-primary dark:text-inverse-primary" href="collections.php">Collections</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors" href="collections.php?segment=men">Men</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors" href="collections.php?segment=women">Women</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors" href="collections.php?segment=accessories">Accessories</a>
</nav>
<div class="flex items-center gap-md">
	<a aria-label="View checkout" class="relative hover:opacity-80 transition-opacity active:scale-95 text-primary dark:text-inverse-primary" href="checkout.php">
	<span class="material-symbols-outlined text-primary dark:text-inverse-primary">shopping_bag</span>
	<span class="absolute -top-1 -right-1 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
	</a>
<a aria-label="Account" class="hover:opacity-80 transition-opacity active:scale-95 text-primary dark:text-inverse-primary" href="index.php">
<span class="material-symbols-outlined text-primary dark:text-inverse-primary">person</span>
</a>
	<button class="md:hidden p-base text-primary" type="button">
	<span class="material-symbols-outlined">menu</span>
	</button>
	</div>
	</div>
	</header>
<main class="max-w-container-max mx-auto px-gutter py-xl">
<div class="grid grid-cols-1 md:grid-cols-12 gap-xl">
<!-- Left Column: Image Gallery -->
<div class="md:col-span-7 lg:col-span-8 flex flex-col gap-base">
<div class="w-full bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm">
<img class="w-full h-auto aspect-[4/5] object-cover" data-alt="A high-end editorial product shot of a minimalist, luxury white leather sneaker sitting on a clean architectural stone pedestal. The lighting is soft and diffused, highlighting the fine texture of the leather and precise stitching. The background is a neutral off-white with subtle shadows, creating a sophisticated and expensive atmosphere. The overall aesthetic is clean, modern, and light-themed." data-product-main-image="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD4ijcMwPe30ODtdOZ3frwM4MaVF3Syik2GnJ50PyZFoU1i6enoY0fHRrYAkxWGXit63wK9u888BQNjezZUenYPxofTgiS2Sfh5KNc4lY4C3RhachzPT94TilxcPHI6Jka08dOv3K4B-nMMl9vGKPdJEGoMJCGPUnp01CHGHyte7Aty0lCVvDYy3Xw9Wp4NUTny6wTC3v5ye44kvbW7y8VsKPXgm-G87aDMbQSMlOrt0bkWMJCKSsxwDYQVp3ppql8pnRceMwqmj_0"/>
</div>
<div class="grid grid-cols-2 gap-base">
<div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm aspect-square">
<img class="w-full h-full object-cover" data-alt="A detailed close-up shot of the heel and sole of a premium sneaker, showcasing intricate design elements and high-quality craftsmanship. The materials are displayed with crisp detail under bright, professional studio lighting. The color palette is composed of pure whites and soft greys to maintain a minimalist luxury brand identity." data-product-thumb-1="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRvj71be1jZGfbT9XgRZj3o2GZR_569yxyAPXMQJPJYP1dP-NFqNJo6LRThASOxmGLUf9x6Uxc_JwOp7gjpnETe6eYAqiq8Y5LLzyyD-E5myo6vy0mONulqd0u-Byf0ybajNBVqU-O3CQvkY3lss_SRaQOvRIEuWRiEDs4EGNNoJXaZFRswrlmJJxGFpHByhuIIEbg7szj3-wNYwWBVpbMGHSqt3OGo8wKvgt8Lu-7VDPtle0M4PQP3YvjMGvOQouaGc-jVJFvVqc"/>
</div>
<div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-sm aspect-square">
<img class="w-full h-full object-cover" data-alt="An atmospheric top-down view of luxury footwear arranged artistically on a textured light marble surface. Soft morning light creates gentle shadows, emphasizing the premium nature of the product. The scene is clean, spacious, and exudes a sense of quiet luxury through its restrained color palette and high-key brightness." data-product-thumb-2="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3mFEJZl0VXwBzI2V5o_G8G2ruvZrBPJvZu9yPwNzWsXbLdUcDj5nnsnx6bRlQpIj1A_LB2NH0gfpvPsK-J11L6ZX8ZRyrqosY6AQstq1smDP7cC2F5uZ-hoROYjJQqHgxd2CSvLDBRhPti2djhvU60qYKEhQpTgVUIE2LVf3wHpD_tzHnKwspNWakv187OYS_QVhOdixMJgYvwCCoi6P8swd-UBVHB0xadM-MN4TpEWAP6dtj1B322hErmT4xDwx55N5boqo3ehk"/>
</div>
</div>
</div>
<!-- Right Column: Product Details -->
<div class="md:col-span-5 lg:col-span-4 flex flex-col gap-lg sticky top-32 h-fit">
<div class="flex flex-col gap-xs">
<span class="font-label-md text-label-md text-primary tracking-widest uppercase" data-product-badge="">New Collection</span>
<h1 class="font-headline-xl text-headline-xl text-on-surface" data-product-title="">Architect Low-Top</h1>
<div class="flex items-center gap-sm mt-base">
<div class="flex text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined">star_half</span>
</div>
<span class="font-label-lg text-label-lg text-secondary" data-product-reviews="">(124 Reviews)</span>
</div>
</div>
<div class="font-headline-md text-headline-md text-on-surface" data-product-price-display="">$180.00</div>
<!-- Color Selection -->
<div class="flex flex-col gap-base">
<span class="font-label-lg text-label-lg text-on-surface">Color: <span class="text-secondary font-normal" data-selected-color="">Pristine White</span></span>
<div class="flex gap-sm" data-product-colors="">
<button aria-label="Select Pristine White" class="w-10 h-10 rounded-full border border-outline-variant ring-2 ring-offset-2 ring-transparent bg-[#FFFFFF] shadow-sm is-selected" data-color-name="Pristine White"></button>
<button aria-label="Select Warm Stone" class="w-10 h-10 rounded-full border border-outline-variant bg-[#E5E2E1] hover:scale-105 transition-transform" data-color-name="Warm Stone"></button>
<button aria-label="Select Graphite" class="w-10 h-10 rounded-full border border-outline-variant bg-[#2F3131] hover:scale-105 transition-transform" data-color-name="Graphite"></button>
</div>
</div>
<!-- Size Selection -->
<div class="flex flex-col gap-base">
<div class="flex justify-between items-center">
<span class="font-label-lg text-label-lg text-on-surface">Size <span class="text-secondary font-normal" data-selected-size="">EU 41</span></span>
<button class="font-label-md text-label-md text-primary underline underline-offset-4" data-size-guide="">Size Guide</button>
</div>
<div class="grid grid-cols-4 gap-sm" data-product-sizes="">
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all" data-size="EU 40">EU 40</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all is-selected-option" data-size="EU 41">EU 41</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all" data-size="EU 42">EU 42</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all" data-size="EU 43">EU 43</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all" data-size="EU 44">EU 44</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg hover:border-primary hover:text-primary transition-all" data-size="EU 45">EU 45</button>
<button class="py-sm border border-outline-variant rounded-lg font-label-lg text-label-lg opacity-40 cursor-not-allowed" data-size="EU 46" disabled="">EU 46</button>
</div>
</div>
<div class="flex flex-col gap-sm">
<a class="block w-full py-md bg-primary text-on-primary font-label-lg text-label-lg rounded-lg shadow-lg hover:opacity-90 active:scale-95 transition-all text-center" data-add-to-bag="" data-product-name="Architect Low-Top" data-product-price="180" href="checkout.php">Add to Bag</a>
<button class="w-full py-md border border-on-surface text-on-surface font-label-lg text-label-lg rounded-lg flex items-center justify-center gap-sm hover:bg-surface-container-low active:scale-95 transition-all" data-wishlist="">
<span class="material-symbols-outlined">favorite</span>
                        <span data-wishlist-label="">Add to Wishlist</span>
                    </button>
</div>
<p class="font-body-sm text-body-sm text-secondary text-center">Free express shipping and carbon-neutral returns on all orders.</p>
</div>
</div>
<!-- Secondary Information Sections -->
<section class="mt-xl border-t border-outline-variant/30 pt-xl">
<div class="grid grid-cols-1 md:grid-cols-2 gap-xl">
<div>
<h2 class="font-headline-md text-headline-md mb-md text-on-surface">Product Details</h2>
<ul class="space-y-sm">
<li class="flex items-start gap-sm font-body-md text-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            <span data-product-detail-1="">Hand-crafted in Tuscany from 100% full-grain Italian calf leather.</span>
                        </li>
<li class="flex items-start gap-sm font-body-md text-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            <span data-product-detail-2="">Bespoke rubber cupsole for ultimate comfort and durability.</span>
                        </li>
<li class="flex items-start gap-sm font-body-md text-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            <span data-product-detail-3="">Breathable calfskin lining for moisture management and all-day wear.</span>
                        </li>
</ul>
</div>
<div id="shipping-returns">
<h2 class="font-headline-md text-headline-md mb-md text-on-surface">Shipping &amp; Returns</h2>
<p class="font-body-md text-body-md text-on-surface-variant mb-base">We offer complimentary express shipping globally. Delivery typically takes 2-4 business days.</p>
<p class="font-body-md text-body-md text-on-surface-variant">Returns can be made within 30 days of receipt. All items must be in original, unworn condition with tags attached.</p>
</div>
</div>
</section>
<!-- Recommendation Section -->
<section class="mt-xl">
<div class="flex justify-between items-end mb-lg">
<h2 class="font-headline-lg text-headline-lg text-on-surface">You May Also Like</h2>
<a class="font-label-lg text-label-lg text-primary border-b border-primary pb-1" href="collections.php">View All</a>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-md">
<!-- Product Card 1 -->
<div class="group cursor-pointer" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuDC93J-Mi_uyRyUJEzeo2VUt3gCnx1KweKP_DA251be5VmSSPWzQiE3EhjkhxBwd55Gdn3J9N_12Bg5RS3V-OcMAmCzGyfEn-qdSteSk2Sm2QxMbfFQaTV2PYuxS-hkbx1W8FzSC8WNHMBlUvT6u9qRYmqskiYnAom81LJ-H0Fo81sIZgmqT2a5PI1sSigLrSXPZT1NqMpPwhbGMQ7KSvY-jFjHsUWYYIzRGNgt1QR5JJXBwnUKEJSA31X41LiJjnESmcWWo8QyvTc" data-cart-name="Cloud Runner" data-cart-price="195">
<div class="relative overflow-hidden rounded-lg bg-surface-container-lowest mb-sm aspect-[3/4]">
<a class="block w-full h-full" href="product.php?product=cloud-runner" aria-label="View product"><img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="A side view of a sleek, modern runner-style shoe in a soft grey and off-white colorway. The photo is taken in a professional studio setting with minimal props and soft lighting. The aesthetic is high-fashion and minimalist, focusing on the silhouette and premium materials of the footwear." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDC93J-Mi_uyRyUJEzeo2VUt3gCnx1KweKP_DA251be5VmSSPWzQiE3EhjkhxBwd55Gdn3J9N_12Bg5RS3V-OcMAmCzGyfEn-qdSteSk2Sm2QxMbfFQaTV2PYuxS-hkbx1W8FzSC8WNHMBlUvT6u9qRYmqskiYnAom81LJ-H0Fo81sIZgmqT2a5PI1sSigLrSXPZT1NqMpPwhbGMQ7KSvY-jFjHsUWYYIzRGNgt1QR5JJXBwnUKEJSA31X41LiJjnESmcWWo8QyvTc"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base w-10 h-10 bg-surface-container-lowest/90 backdrop-blur-md rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" href="checkout.php">
<span class="material-symbols-outlined text-on-surface text-[20px]">shopping_bag</span>
</a>
</div>
<h3 class="font-label-lg text-label-lg text-on-surface">Cloud Runner</h3>
<p class="font-body-sm text-body-sm text-secondary">$195.00</p>
</div>
<!-- Product Card 2 -->
<div class="group cursor-pointer" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuAjpeUssbT7HMtTqW4qyakzgJS1FiU-SwjRpIy1Tw9BZ6aW8dg1ia9mBEQTsXehCdnQ38ZNuPh-KwVqfaeAzAieT8_pSEHP2-gnuITMw24A-7ga_cJJtTAdWKtIKtSQB42G6Lq4ASqeKiDYxXOXIJ_svraHjMSC6L9xtrDwdUFYZMV5tu9rBzDSf-JQj_B9GQqtOtgotp4hmyshrGNnQ18q8fvkriRBUhxAH8NCNZuoL9oEZk47FOljzcP6gzOFg5XhTuuE5NEFFuA" data-cart-name="Downtown High-Top" data-cart-price="240">
<div class="relative overflow-hidden rounded-lg bg-surface-container-lowest mb-sm aspect-[3/4]">
<a class="block w-full h-full" href="product.php?product=downtown-high-top" aria-label="View product"><img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="A professional product photograph of luxury high-top leather boots in a deep mahogany brown. The lighting is dramatic but controlled, emphasizing the rich patina of the leather. The environment is clean and architectural, fitting a premium e-commerce brand identity with a modern minimalist soul." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjpeUssbT7HMtTqW4qyakzgJS1FiU-SwjRpIy1Tw9BZ6aW8dg1ia9mBEQTsXehCdnQ38ZNuPh-KwVqfaeAzAieT8_pSEHP2-gnuITMw24A-7ga_cJJtTAdWKtIKtSQB42G6Lq4ASqeKiDYxXOXIJ_svraHjMSC6L9xtrDwdUFYZMV5tu9rBzDSf-JQj_B9GQqtOtgotp4hmyshrGNnQ18q8fvkriRBUhxAH8NCNZuoL9oEZk47FOljzcP6gzOFg5XhTuuE5NEFFuA"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base w-10 h-10 bg-surface-container-lowest/90 backdrop-blur-md rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" href="checkout.php">
<span class="material-symbols-outlined text-on-surface text-[20px]">shopping_bag</span>
</a>
</div>
<h3 class="font-label-lg text-label-lg text-on-surface">Downtown High-Top</h3>
<p class="font-body-sm text-body-sm text-secondary">$240.00</p>
</div>
<!-- Product Card 3 -->
<div class="group cursor-pointer" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuCZA8QsI1z-4yH3tVTcLmNVZSvq3xbj2ReUE6tr_ldz3pUm4x6nLz0K8UtTdzYAXvt7OIQ4lGi_6bYNDOyqL9I_QJVDOF48pDIH-Hl8svspIO25hllQki_zXnMSkeUILE1UWGtW97p85dErHOd_YKll0yV9E6Bw0WrAMHHHh88cflfziP3AfRvQ8HCNboloyIkI7rub2yxdcXDNddCggX3FjoxliU_zAL_I9FGwVFlgZKL2z8nMK2kpTSHbaQalI_DPVzFDIE-mzos" data-cart-name="Essential Tote" data-cart-price="375">
<div class="relative overflow-hidden rounded-lg bg-surface-container-lowest mb-sm aspect-[3/4]">
<a class="block w-full h-full" href="product.php?product=essential-tote" aria-label="View product"><img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="An elegant leather tote bag in a creamy beige color, displayed in a bright, airy studio with soft shadows. The shot is clean and minimalist, showing the texture of the fine leather and the precision of the hardware. The mood is sophisticated and aligns with a luxury lifestyle aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZA8QsI1z-4yH3tVTcLmNVZSvq3xbj2ReUE6tr_ldz3pUm4x6nLz0K8UtTdzYAXvt7OIQ4lGi_6bYNDOyqL9I_QJVDOF48pDIH-Hl8svspIO25hllQki_zXnMSkeUILE1UWGtW97p85dErHOd_YKll0yV9E6Bw0WrAMHHHh88cflfziP3AfRvQ8HCNboloyIkI7rub2yxdcXDNddCggX3FjoxliU_zAL_I9FGwVFlgZKL2z8nMK2kpTSHbaQalI_DPVzFDIE-mzos"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base w-10 h-10 bg-surface-container-lowest/90 backdrop-blur-md rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" href="checkout.php">
<span class="material-symbols-outlined text-on-surface text-[20px]">shopping_bag</span>
</a>
</div>
<h3 class="font-label-lg text-label-lg text-on-surface">Essential Tote</h3>
<p class="font-body-sm text-body-sm text-secondary">$375.00</p>
</div>
<!-- Product Card 4 -->
<div class="group cursor-pointer" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z1PFbN5d5iX569B8_T5_giVQDfygycpDU7KE5ZeWXlQyLIJmRezROLUNVXqri-ol3nabfGSMPEJ4iCQRQ3T6ieAeILwdCX24kcjlMHyXEspuvHJa7qUNV4Ty59HPXyJstJCij6Din9uuEUaPdOqaoCdpTSzOijKAqnOB29zI02kZbQC29RivviKhLtFIO0S6vHSxpncfEwf_k_0GPHYo0IEKYDIqk8vh4-4e-BGwzb7smuC_FkCeST4f3apB7htMF-P6Mo4ECbE" data-cart-name="Mono-Chrome Watch" data-cart-price="220">
<div class="relative overflow-hidden rounded-lg bg-surface-container-lowest mb-sm aspect-[3/4]">
<a class="block w-full h-full" href="product.php?product=mono-watch" aria-label="View product"><img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="A close-up high-key photograph of a luxury minimalist watch with a white face and brushed steel strap. The image is bright and crisp, with soft reflections on the glass. The background is a clean white textured paper, creating a high-end editorial feel that emphasizes clarity and precision." src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z1PFbN5d5iX569B8_T5_giVQDfygycpDU7KE5ZeWXlQyLIJmRezROLUNVXqri-ol3nabfGSMPEJ4iCQRQ3T6ieAeILwdCX24kcjlMHyXEspuvHJa7qUNV4Ty59HPXyJstJCij6Din9uuEUaPdOqaoCdpTSzOijKAqnOB29zI02kZbQC29RivviKhLtFIO0S6vHSxpncfEwf_k_0GPHYo0IEKYDIqk8vh4-4e-BGwzb7smuC_FkCeST4f3apB7htMF-P6Mo4ECbE"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base w-10 h-10 bg-surface-container-lowest/90 backdrop-blur-md rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" href="checkout.php">
<span class="material-symbols-outlined text-on-surface text-[20px]">shopping_bag</span>
</a>
</div>
<h3 class="font-label-lg text-label-lg text-on-surface">Mono-Chrome Watch</h3>
<p class="font-body-sm text-body-sm text-secondary">$220.00</p>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="w-full bg-surface-container-low dark:bg-surface-container-highest border-t border-outline-variant">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto">
<div class="flex flex-col gap-md">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface dark:text-inverse-on-surface">LUXE</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Elevating the everyday through architectural precision and Italian craftsmanship.</p>
</div>
<div class="flex flex-col gap-base">
<h4 class="font-label-lg text-label-lg text-on-surface mb-base">Shop</h4>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="collections.php?view=new-arrivals">New Arrivals</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="collections.php?segment=men">Men</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="collections.php?segment=women">Women</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="collections.php?segment=accessories">Accessories</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="index.php#sustainability">Sustainability</a>
</div>
<div class="flex flex-col gap-base">
<h4 class="font-label-lg text-label-lg text-on-surface mb-base">Support</h4>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="product.php#shipping-returns">Shipping &amp; Returns</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="checkout.php">Contact Us</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="checkout.php">Terms of Service</a>
</div>
<div class="flex flex-col gap-base">
<h4 class="font-label-lg text-label-lg text-on-surface mb-base">Newsletter</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-base">Join for exclusive early access and luxury insights.</p>
<div class="flex gap-xs">
<input class="bg-surface-container-lowest border border-outline-variant px-sm py-xs rounded-lg flex-1 text-body-sm focus:outline-none focus:border-primary" placeholder="Email Address" type="email"/>
<button class="bg-primary text-on-primary px-md py-xs rounded-lg font-label-md text-label-md" type="button">Join</button>
</div>
</div>
</div>
<div class="max-w-container-max mx-auto px-gutter py-md border-t border-outline-variant/30 text-center">
<p class="font-body-sm text-body-sm text-on-surface-variant/80">© 2024 LUXE Premium E-commerce. All rights reserved.</p>
</div>
</footer>
<script src="assets/js/site.js"></script>
</body></html>
