<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>LUXE | Premium E-commerce</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;family=Work+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
<link href="/assets/luxe-mark.svg" rel="icon" type="image/svg+xml"/>
<link href="/assets/css/site.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary-fixed selection:text-on-primary-fixed">
<!-- TopNavBar -->
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 shadow-sm">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="font-headline-md text-headline-md font-bold text-on-surface dark:text-inverse-on-surface tracking-tighter" href="/">
                LUXE
            </a>
<nav class="hidden md:flex items-center gap-lg">
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?view=new-arrivals">New Arrivals</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php">Collections</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=men">Men</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=women">Women</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=accessories">Accessories</a>
<a class="font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/retailer/login.php">Become a Retailer</a>
</nav>
<div class="flex items-center gap-base">
<a aria-label="View checkout" class="relative p-base hover:opacity-80 transition-opacity active:scale-95 transition-transform text-primary dark:text-inverse-primary" href="/checkout.php">
<span class="material-symbols-outlined">shopping_bag</span>
<span class="absolute top-0 right-0 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
</a>
<a aria-label="Account" class="p-base hover:opacity-80 transition-opacity active:scale-95 transition-transform text-primary dark:text-inverse-primary" href="/">
<span class="material-symbols-outlined">person</span>
</a>
<button class="md:hidden p-base text-primary" type="button">
<span class="material-symbols-outlined">menu</span>
</button>
</div>
</div>
</header>
<main>
<!-- Hero Section -->
<section class="relative h-[870px] w-full overflow-hidden bg-surface-container-lowest">
<div class="absolute inset-0 z-0">
<img alt="Premium fashion hero" class="w-full h-full object-cover object-center" data-alt="A cinematic editorial fashion shot of high-end garments in a bright, airy architectural setting. Large floor-to-ceiling windows cast soft, natural morning light across a minimalist space. The aesthetic is clean and luxurious, featuring a sophisticated palette of soft off-whites and electric blue accents. The mood is aspirational and quiet, reflecting a premium e-commerce brand identity." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUwwMEYbTGLBT2DkWzCUSDgudz7-BAdgpLlF6kcnvz_9fnHMagDtUTrABJCxkUorwn05KRhoYpDgjT6r98d95ybrV3vBuyC4EAh6Xfg1U4CCe72cpoC4re8W-GwbOMFaCoPXufb0HM1kY8pjJaOvDXsHzB3_ebQtjB86jr2sRjoOQkwQHPaTwiscEtGz0-L6WRNmqKD0boSm66RvLNQfoPlBHpToFpNEDH33BKRKqG9BCrl-CmvEmqHg4rXwXldagfmdRc0kAFqDE"/>
<div class="absolute inset-0 bg-gradient-to-r from-surface-container-lowest/60 to-transparent"></div>
</div>
<div class="relative z-10 max-w-container-max mx-auto px-gutter h-full flex flex-col justify-center items-start">
<span class="font-label-lg text-label-lg text-primary tracking-[0.2em] mb-base">AUTUMN WINTER 2024</span>
<h1 class="font-headline-xl text-headline-xl max-w-2xl mb-md text-on-surface md:text-headline-xl sm:text-headline-xl-mobile">
                    The Art of Minimalist Living
                </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg mb-lg">
                    Discover our new seasonal collection where timeless silhouettes meet contemporary technical fabrics.
                </p>
<div class="flex gap-base">
<a class="bg-primary text-on-primary px-lg py-md rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all shadow-sm" href="/collections.php?view=new-arrivals">
                        Shop New Arrivals
                    </a>
<a class="bg-transparent border border-on-surface text-on-surface px-lg py-md rounded-lg font-label-lg text-label-lg hover:bg-surface-container-high active:scale-95 transition-all" href="/collections.php">
                        View Collections
                    </a>
</div>
</div>
</section>
<!-- Collections Grid -->
<section class="py-xl max-w-container-max mx-auto px-gutter">
<div class="grid grid-cols-1 md:grid-cols-3 gap-md h-[600px] md:h-[500px]">
<div class="group relative overflow-hidden rounded-lg bg-surface-variant md:col-span-1">
<a aria-label="Browse men's collection" class="absolute inset-0 z-10" href="/collections.php?segment=men"></a>
<img alt="Men's Collection" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A sophisticated editorial portrait of a man wearing high-quality minimalist knitwear. The lighting is soft and diffused, highlighting the rich textures of the fabric against a clean, light-gray studio background. The overall tone is modern, understated, and premium, using a refined color palette of charcoal and soft-white." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDIsYXHYF_zh16Zu9XCiHE4MhxTm3CVmnrzPClTCCyJAprEtk24WTdTkMZRwP6Cf5rhcQNAlJXHyEaEWh_ft0W3v4rWUNLMcx2YwI9pktDgl5oO7TUc4o-s1gBvSUtrZnfPWcWOZLpPHnuUAu1KxKiRiWPBN5chjwnROj51K7Ms7ac-qTsn_RiXQGKdKtf9v92qJ8iAvmKcEqyPLGCOgjasNk5CUuVWht5cavDsVyIKAbL3PChym-t1v1Ri5ZVGatM_d6aMrGgE7LQ"/>
<div class="absolute inset-0 bg-black/10 transition-opacity group-hover:bg-black/20"></div>
<div class="absolute bottom-lg left-lg right-lg z-20">
<h3 class="font-headline-md text-headline-md text-white mb-xs">Men</h3>
<a class="font-label-md text-label-md text-white/90 border-b border-white/50 hover:border-white transition-all pb-1" href="/collections.php?segment=men">Shop Collection</a>
</div>
</div>
<div class="group relative overflow-hidden rounded-lg bg-surface-variant md:col-span-1">
<a aria-label="Browse women's collection" class="absolute inset-0 z-10" href="/collections.php?segment=women"></a>
<img alt="Women's Collection" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A high-fashion shot of a woman in an elegant, structured coat standing in a brightly lit, contemporary art gallery. The mood is editorial and high-end, focusing on the silhouette and craftsmanship. The lighting is crisp and cool, creating deep shadows and bright highlights that emphasize a luxurious, modern aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDo-u6CAKEPbTRwzU2Of26T49jvYAvqxqL9scRoZnbWYu0ig5Zzg7cJWVGu6AM2QsBiFjauV4F6uuWs44Bv0BgPUwzLGYJnkJbmsvK-3_HaHUDTbFPahFZKwoyzh0KdGhdaLBbelUhmz0tbD8DkDJIvw-2zJ3MCMkOZaiyXut-jxEh5KXmCUXgpBOUR9MEwEvp7YVQAofVCn3AC34eEVsjXU4pvTvfW0Crl9S6-G4bBaJ1l-TvMY1k5zjHP0H0myUUXk22R_d4QDJc"/>
<div class="absolute inset-0 bg-black/10 transition-opacity group-hover:bg-black/20"></div>
<div class="absolute bottom-lg left-lg right-lg z-20">
<h3 class="font-headline-md text-headline-md text-white mb-xs">Women</h3>
<a class="font-label-md text-label-md text-white/90 border-b border-white/50 hover:border-white transition-all pb-1" href="/collections.php?segment=women">Shop Collection</a>
</div>
</div>
<div class="group relative overflow-hidden rounded-lg bg-surface-variant md:col-span-1">
<a aria-label="Browse accessories collection" class="absolute inset-0 z-10" href="/collections.php?segment=accessories"></a>
<img alt="Accessories Collection" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A minimalist product shot of premium accessories like a leather watch and sunglasses on a white marble surface. The lighting is bright and high-key, emphasizing the polished surfaces and premium materials. The composition is clean and organized, conveying a sense of quiet luxury and high-end product design." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQ-Q7IrltWSUmN5tTaJJAuU8RfxBwqZXRND2AbOiz-6W5XA7VJ-3QR-nA2JxE5HxZvHdsr5aJyxpJ4v5_3AaiM1JEq6M0QKYsUcHgr2Hx7qWuP2MecKDpCrzw_Hw4fYVlawsfL3bXV1Ytfm3m69cXbwdnmkE06msIs2CEOnLAC04BN9vVHRpiXm7Gm0HZA7dbi3xW7tBnPUWmEnVYiHzZd6nO_bh8KcF9CGbOlyJ4PdOtXTeNz0t8T6eN1yvQeA3JqL84Grj6s1oE"/>
<div class="absolute inset-0 bg-black/10 transition-opacity group-hover:bg-black/20"></div>
<div class="absolute bottom-lg left-lg right-lg z-20">
<h3 class="font-headline-md text-headline-md text-white mb-xs">Accessories</h3>
<a class="font-label-md text-label-md text-white/90 border-b border-white/50 hover:border-white transition-all pb-1" href="/collections.php?segment=accessories">Shop Collection</a>
</div>
</div>
</div>
</section>
<!-- Trending Now Carousel -->
<section class="py-xl bg-surface-container-low overflow-hidden">
<div class="max-w-container-max mx-auto px-gutter mb-lg flex justify-between items-end">
<div>
<span class="font-label-md text-label-md text-primary mb-xs block">CURATED FOR YOU</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Trending Now</h2>
</div>
<div class="flex gap-base">
<button class="w-12 h-12 rounded-full border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors" data-carousel-prev="">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="w-12 h-12 rounded-full border border-outline-variant flex items-center justify-center hover:bg-surface-container transition-colors" data-carousel-next="">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
<div class="max-w-container-max mx-auto px-gutter">
<div class="flex gap-md overflow-x-auto hide-scrollbar pb-base" data-carousel-track="">
<!-- Product Card 1 -->
<div class="min-w-[300px] bg-surface-container-lowest rounded-lg overflow-hidden group shadow-sm hover:shadow-md transition-shadow" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuBchUcD0EUx2j2iuPQpWTJiIV8_zw6fVXkrrcoW0NNOHi1SxdQu66MH8cT3G92PJljVOKIKgAIxyBfrzWWenQqSPhvb1LWVR7mApfsOtw2uz6Y0KuD0iE38tFoufpB9nbsctHFEUVTIfLvBiOPst0XZ0luxheHlGNr5fdlKJaBuMALpmJsNioBdRUkPlV9y0lbkOIJe3LmZLgRYbVi9X9cNiyeudwa6R7G7FYt8ukQkzQCN-Lq6ASZTwC8kocRV1qzT1TLfE4Ck0ws" data-cart-name="Wool Tailored Coat" data-cart-price="495">
<div class="h-[380px] overflow-hidden relative">
<a class="block w-full h-full" href="/product.php?product=coat" aria-label="View product"><img alt="Product 1" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" data-alt="A professional studio product shot of a high-quality navy wool coat on a neutral background. The lighting is even and soft, showcasing the texture and premium tailoring. The aesthetic is clean and modern, highlighting the garment as the hero of the image within a high-end e-commerce context." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBchUcD0EUx2j2iuPQpWTJiIV8_zw6fVXkrrcoW0NNOHi1SxdQu66MH8cT3G92PJljVOKIKgAIxyBfrzWWenQqSPhvb1LWVR7mApfsOtw2uz6Y0KuD0iE38tFoufpB9nbsctHFEUVTIfLvBiOPst0XZ0luxheHlGNr5fdlKJaBuMALpmJsNioBdRUkPlV9y0lbkOIJe3LmZLgRYbVi9X9cNiyeudwa6R7G7FYt8ukQkzQCN-Lq6ASZTwC8kocRV1qzT1TLfE4Ck0ws"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base bg-white/90 backdrop-blur-md p-base rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" href="/checkout.php">
<span class="material-symbols-outlined text-primary">add_shopping_cart</span>
</a>
</div>
<div class="p-md">
<p class="font-label-md text-label-md text-secondary mb-xs">Outerwear</p>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Wool Tailored Coat</h4>
<p class="font-label-lg text-label-lg text-primary">$495.00</p>
</div>
</div>
<!-- Product Card 2 -->
<div class="min-w-[300px] bg-surface-container-lowest rounded-lg overflow-hidden group shadow-sm hover:shadow-md transition-shadow" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuCJnqionFY9y6EYbY_k4evRIJvyAUyoVg6PwXuwkjSxd9qMHcQnc8LWwRtP425gPIACwYi4BS5rEn6a0KGYC0FNUJvGW5DD0_kSsfAwo0JEcqudDivOu-ipKeVOJq6AGfrgwkWDX--L6eQ64NWowPC_RH-NzHnWgcRyYnqAasPBj1Kkzbs7jslOeGddj7tOwH78WBLt_Aj94R2TFl-YneG96_bN-w1tvvuaSkU-DBLuCJN2XQosE_KpP5P8hSYeH9aEujzdyJQhAKA" data-cart-name="Leather Artisan Pump" data-cart-price="285">
<div class="h-[380px] overflow-hidden relative">
<a class="block w-full h-full" href="/product.php?product=heels" aria-label="View product"><img alt="Product 2" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" data-alt="A clean, minimalist product shot of designer leather heels displayed on a geometric pedestal. The setting is bright and monochromatic, with a soft-white background and elegant shadows. The mood is sophisticated and focused on the luxurious material and craftsmanship of the shoes." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJnqionFY9y6EYbY_k4evRIJvyAUyoVg6PwXuwkjSxd9qMHcQnc8LWwRtP425gPIACwYi4BS5rEn6a0KGYC0FNUJvGW5DD0_kSsfAwo0JEcqudDivOu-ipKeVOJq6AGfrgwkWDX--L6eQ64NWowPC_RH-NzHnWgcRyYnqAasPBj1Kkzbs7jslOeGddj7tOwH78WBLt_Aj94R2TFl-YneG96_bN-w1tvvuaSkU-DBLuCJN2XQosE_KpP5P8hSYeH9aEujzdyJQhAKA"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base bg-white/90 backdrop-blur-md p-base rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" href="/checkout.php">
<span class="material-symbols-outlined text-primary">add_shopping_cart</span>
</a>
</div>
<div class="p-md">
<p class="font-label-md text-label-md text-secondary mb-xs">Footwear</p>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Leather Artisan Pump</h4>
<p class="font-label-lg text-label-lg text-primary">$285.00</p>
</div>
</div>
<!-- Product Card 3 -->
<div class="min-w-[300px] bg-surface-container-lowest rounded-lg overflow-hidden group shadow-sm hover:shadow-md transition-shadow" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuCQPtWo0O_h25ZD-uqlJNUOw_CMG6SjaIqwAdS7fF4-9zejORMZ_NS2z4p6KnILnMjKol4ArgZXQDMligjx1c4OZfzlnKZ0QCj3wVXujPKAC1OmvFlLDAwsIRshZBPok04K30_grPaCoCynebD3yteoWChW4NTJTnO3_ms3Qj_z61YdTuKy8wujCaIbt_2-hq2cf1tvvkCCTGkhrSYwJaECUQpkz1Nc7H3rbZ475nBm59md7IDO_JiPOxvWsjB_tlGvr5_oX2AaDRs" data-cart-name="Structural Leather Tote" data-cart-price="425">
<div class="h-[380px] overflow-hidden relative">
<a class="block w-full h-full" href="/product.php?product=tote" aria-label="View product"><img alt="Product 3" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" data-alt="A minimalist studio photograph of a premium leather handbag with metallic hardware. The background is a soft, tonal cream, and the lighting is high-key to emphasize the luxury textures. The composition is balanced and elegant, reflecting the brand's sophisticated and efficient personality." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQPtWo0O_h25ZD-uqlJNUOw_CMG6SjaIqwAdS7fF4-9zejORMZ_NS2z4p6KnILnMjKol4ArgZXQDMligjx1c4OZfzlnKZ0QCj3wVXujPKAC1OmvFlLDAwsIRshZBPok04K30_grPaCoCynebD3yteoWChW4NTJTnO3_ms3Qj_z61YdTuKy8wujCaIbt_2-hq2cf1tvvkCCTGkhrSYwJaECUQpkz1Nc7H3rbZ475nBm59md7IDO_JiPOxvWsjB_tlGvr5_oX2AaDRs"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base bg-white/90 backdrop-blur-md p-base rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" href="/checkout.php">
<span class="material-symbols-outlined text-primary">add_shopping_cart</span>
</a>
</div>
<div class="p-md">
<p class="font-label-md text-label-md text-secondary mb-xs">Bags</p>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Structural Leather Tote</h4>
<p class="font-label-lg text-label-lg text-primary">$425.00</p>
</div>
</div>
<!-- Product Card 4 -->
<div class="min-w-[300px] bg-surface-container-lowest rounded-lg overflow-hidden group shadow-sm hover:shadow-md transition-shadow" data-cart-image="https://lh3.googleusercontent.com/aida-public/AB6AXuDyT9BhCFk-MjJkEIVTVNYLmb1QxR0SY6MzQjKyVpH6ALokWVkyTHfXcL2ePj0kD36BSmDQ2m1UX1l9b4U2Tdu6Ux_Jwi1oNQSi08hC2M2BgKgNtq5cjSrCZV5A51TrT7tgWxx1dIIwS_fPxiRfi1HDyR9tLER9Vb2k5gL75JToaQ-7z0vD5JDn-D6NuRPHrHGIS4-DIAHKY57ZRJ0l3agSH1MbIGmaB2agVwG8Mxy37aKCRpprkhttpXXdfeoWXyvTErZQS8kAyv4" data-cart-name="Silk Evening Gown" data-cart-price="690">
<div class="h-[380px] overflow-hidden relative">
<a class="block w-full h-full" href="/product.php?product=gown" aria-label="View product"><img alt="Product 4" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" data-alt="A professional fashion product photo of a silk dress on a minimalist mannequin. The setting is bright and clean, with soft directional lighting that creates gentle shadows and highlights the sheen of the silk. The style is restrained and editorial, focusing on the high-quality material and fluid drape." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyT9BhCFk-MjJkEIVTVNYLmb1QxR0SY6MzQjKyVpH6ALokWVkyTHfXcL2ePj0kD36BSmDQ2m1UX1l9b4U2Tdu6Ux_Jwi1oNQSi08hC2M2BgKgNtq5cjSrCZV5A51TrT7tgWxx1dIIwS_fPxiRfi1HDyR9tLER9Vb2k5gL75JToaQ-7z0vD5JDn-D6NuRPHrHGIS4-DIAHKY57ZRJ0l3agSH1MbIGmaB2agVwG8Mxy37aKCRpprkhttpXXdfeoWXyvTErZQS8kAyv4"/></a>
<a aria-label="Add item to bag" class="js-cart-add-line absolute bottom-base right-base bg-white/90 backdrop-blur-md p-base rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity" href="/checkout.php">
<span class="material-symbols-outlined text-primary">add_shopping_cart</span>
</a>
</div>
<div class="p-md">
<p class="font-label-md text-label-md text-secondary mb-xs">Dresses</p>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Silk Evening Gown</h4>
<p class="font-label-lg text-label-lg text-primary">$690.00</p>
</div>
</div>
</div>
</div>
</section>
<!-- Brand Values Section -->
<section class="py-xl max-w-container-max mx-auto px-gutter" id="sustainability">
<div class="grid grid-cols-1 md:grid-cols-2 gap-lg items-center">
<div class="rounded-lg overflow-hidden h-[600px] shadow-sm">
<img alt="Sustainable materials" class="w-full h-full object-cover" data-alt="A serene and minimalist image showing a close-up of natural, sustainable textile fibers like linen or organic cotton. The lighting is soft and warm, highlighting the intricate details and purity of the materials. The setting is a clean, bright studio that feels calm and eco-conscious, reflecting the brand's commitment to quality and sustainability." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjbUbWxMFlE4mjZRrkoHekNsnCKXw4AjYBdCr5pd6xys3wUiq0H2Pc07RN-GRwY4GbWlHN6JCDzb9KffeM_BFdov7CBuqax7vDTUjXLLUbDFug2637cfVF72qhJ1y8-CukEP9gjjqpDdNA-5b8kAjB1gn9f3eQMPybm-UwEogULGJFRi6ipVmUWxiM_QwfZtsQXGyGyf7qZMsYOFiNnDD5s99QsVN9U7YZeNzNu4TBSOKNXteOH4luBqQhlb5RTmyY-mzoBVbxaL0"/>
</div>
<div class="space-y-lg">
<div>
<h2 class="font-headline-xl text-headline-xl text-on-surface mb-md">Built for Longevity, Mindful of the Future</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">We believe that luxury should not come at the cost of the planet. Our commitment to sustainability is woven into every stitch and decision we make.</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div class="p-md bg-surface-container rounded-lg border border-outline-variant/20">
<img alt="Sustainability icon" class="mb-base h-8 w-8" src="/assets/icons/eco.svg"/>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Sustainability</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">We use 100% recycled or ethically sourced materials in all our core collections.</p>
</div>
<div class="p-md bg-surface-container rounded-lg border border-outline-variant/20">
<img alt="Quality icon" class="mb-base h-8 w-8" src="/assets/icons/quality.svg"/>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Quality</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Every piece undergoes rigorous testing to ensure it lasts for seasons to come.</p>
</div>
<div class="p-md bg-surface-container rounded-lg border border-outline-variant/20">
<img alt="Craftsmanship icon" class="mb-base h-8 w-8" src="/assets/icons/craft.svg"/>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Craftsmanship</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Partnering with family-owned ateliers that preserve traditional techniques.</p>
</div>
<div class="p-md bg-surface-container rounded-lg border border-outline-variant/20">
<img alt="Transparency icon" class="mb-base h-8 w-8" src="/assets/icons/transparency.svg"/>
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Transparency</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Full visibility into our supply chain and fair labor practices across the globe.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Newsletter Section -->
<section class="py-xl bg-surface-container-highest">
<div class="max-w-container-max mx-auto px-gutter text-center">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-base">Join the LUXE Circle</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg max-w-xl mx-auto">Subscribe for exclusive access to new launches, private sales, and seasonal lookbooks.</p>
<form class="max-w-md mx-auto flex gap-xs" data-newsletter-form="" novalidate="">
<input class="flex-grow bg-white border border-outline-variant rounded-lg px-md py-sm font-body-md focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="Enter your email address" required="" type="email"/>
<button class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">
                        Subscribe
                    </button>
</form>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-low dark:bg-surface-container-highest border-t border-outline-variant">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto">
<div class="space-y-md">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface dark:text-inverse-on-surface">LUXE</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Sophisticated essentials for the modern lifestyle. Quality and sustainability at our core.</p>
<div class="flex gap-base">
<a class="text-on-surface-variant hover:text-primary" href="/"><span class="material-symbols-outlined">public</span></a>
<a class="text-on-surface-variant hover:text-primary" href="/"><span class="material-symbols-outlined">share</span></a>
<a class="text-on-surface-variant hover:text-primary" href="/"><span class="material-symbols-outlined">alternate_email</span></a>
</div>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface mb-md">Shop</h5>
<ul class="space-y-sm">
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?view=new-arrivals">New Arrivals</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=men">Men's Collection</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=women">Women's Collection</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/collections.php?segment=accessories">Accessories</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface mb-md">Support</h5>
<ul class="space-y-sm">
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/product.php#shipping-returns">Shipping &amp; Returns</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Contact Us</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/#sustainability">Sustainability</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">FAQs</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface mb-md">Legal</h5>
<ul class="space-y-sm">
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Privacy Policy</a></li>
<li><a class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80 hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Terms of Service</a></li>
</ul>
</div>
</div>
<div class="px-gutter py-md max-w-container-max mx-auto border-t border-outline-variant/30 text-center">
<p class="font-body-sm text-body-sm text-on-surface-variant/60">© 2024 LUXE Premium E-commerce. All rights reserved.</p>
</div>
</footer>
<script src="/assets/js/site.js"></script>
</body></html>
