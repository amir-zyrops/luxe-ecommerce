<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/database.php";

$segment = isset($_GET["segment"]) ? strtolower(trim((string) $_GET["segment"])) : "";
$view = isset($_GET["view"]) ? strtolower(trim((string) $_GET["view"])) : "";
$segmentOptions = ["men", "women", "accessories"];
$isNewArrivalsActive = $view === "new-arrivals";
$isCollectionsActive = !$isNewArrivalsActive && !in_array($segment, $segmentOptions, true);
$retailerProducts = fetch_collection_retailer_products();

$navBase = "font-label-lg text-label-lg dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors";
$navActive = "text-primary dark:text-inverse-primary";
$navIdle = "text-secondary";

function fetch_collection_retailer_products(): array
{
    try {
        $pdo = luxe_db();
        $stmt = $pdo->query(
            "SELECT p.product_slug, p.name, p.description, p.category, p.segment, p.price, p.image_url,
                    p.default_color, p.available_colors, p.available_sizes, p.is_new_arrival, p.popularity
             FROM products p
             JOIN retailer_accounts r ON r.id = p.vendor_id
             WHERE p.active = true
               AND p.approval_status = 'approved'
               AND p.archived_at IS NULL
               AND r.email <> 'system@luxe.local'
             ORDER BY p.updated_at DESC"
        );
        return array_map("map_collection_product", $stmt->fetchAll());
    } catch (Throwable) {
        return [];
    }
}

function map_collection_product(array $row): array
{
    $colors = json_decode((string) $row["available_colors"], true) ?: [];
    $sizes = json_decode((string) $row["available_sizes"], true) ?: [];
    return [
        "id" => (string) $row["product_slug"],
        "name" => (string) $row["name"],
        "description" => (string) $row["description"],
        "category" => (string) $row["category"],
        "segment" => (string) $row["segment"],
        "price" => (float) $row["price"],
        "image" => (string) $row["image_url"],
        "defaultColor" => (string) $row["default_color"],
        "colors" => collection_string_values($colors),
        "sizes" => collection_string_values($sizes),
        "newArrival" => (bool) $row["is_new_arrival"],
        "popularity" => (int) $row["popularity"],
    ];
}

function collection_string_values(array $values): array
{
    $mapped = array_map(
        static fn ($value): string => is_array($value) ? (string) ($value["label"] ?? $value["name"] ?? "") : (string) $value,
        $values
    );
    return array_values(array_filter($mapped, static fn (string $value): bool => $value !== ""));
}

function collection_color_filter(string $name): string
{
    $value = strtolower($name);
    if (str_contains($value, "blue") || str_contains($value, "navy")) return "blue";
    if (str_contains($value, "olive") || str_contains($value, "green")) return "olive";
    if (str_contains($value, "sand") || str_contains($value, "camel") || str_contains($value, "cream") || str_contains($value, "ivory") || str_contains($value, "champagne") || str_contains($value, "gold")) return "sand";
    if (str_contains($value, "black") || str_contains($value, "oxblood") || str_contains($value, "brown") || str_contains($value, "mahogany") || str_contains($value, "walnut") || str_contains($value, "chestnut")) return "black";
    return "gray";
}

function collection_category_label(string $category): string
{
    return [
        "outerwear" => "Outerwear",
        "knitwear" => "Knitwear",
        "shirts-tops" => "Shirts & Tops",
        "trousers" => "Trousers",
        "footwear" => "Footwear",
        "dresses" => "Dresses",
        "bags" => "Bags",
        "accessories" => "Accessories",
    ][$category] ?? "Product";
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
                }
            }
        };
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .product-card:hover .add-to-bag {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
<link href="/assets/luxe-favicon.svg" rel="icon" type="image/svg+xml"/>
<link href="/assets/css/site.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-surface font-body-md selection:bg-primary-container selection:text-on-primary-container">
<!-- TopNavBar -->
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md shadow-sm border-b border-outline-variant/30">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="luxe-site-logo" href="/" aria-label="LUXE home">
<img src="/assets/luxe-mark.svg" alt="" aria-hidden="true"/>
<span>LUXE</span>
</a>
<nav class="hidden md:flex items-center space-x-lg h-full">
<a class="<?= $navBase ?> <?= $isNewArrivalsActive ? $navActive : $navIdle ?>" href="/collections.php?view=new-arrivals">New Arrivals</a>
<a class="<?= $navBase ?> <?= $isCollectionsActive ? $navActive : $navIdle ?>" href="/collections.php">Collections</a>
<a class="<?= $navBase ?> <?= $segment === "men" ? $navActive : $navIdle ?>" href="/collections.php?segment=men">Men</a>
<a class="<?= $navBase ?> <?= $segment === "women" ? $navActive : $navIdle ?>" href="/collections.php?segment=women">Women</a>
<a class="<?= $navBase ?> <?= $segment === "accessories" ? $navActive : $navIdle ?>" href="/collections.php?segment=accessories">Accessories</a>
<a class="<?= $navBase ?> <?= $navIdle ?>" href="/retailer/login.php">Become a Retailer</a>
</nav>
<div class="flex items-center space-x-md">
<a aria-label="View checkout" class="relative hover:opacity-80 transition-opacity active:scale-95 transition-transform" href="/checkout.php">
<span class="material-symbols-outlined text-primary dark:text-inverse-primary">shopping_bag</span>
<span class="absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
</a>
<a aria-label="Account" class="hover:opacity-80 transition-opacity active:scale-95 transition-transform" href="/">
<span class="material-symbols-outlined text-primary dark:text-inverse-primary">person</span>
</a>
<button class="md:hidden" type="button">
<span class="material-symbols-outlined text-on-surface">menu</span>
</button>
</div>
</div>
</header>
<main class="max-w-container-max mx-auto px-gutter py-lg">
<!-- Hero Title -->
<div class="mb-lg">
<h1 class="font-headline-xl text-headline-xl text-on-surface mb-xs">Spring Collections</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">Discover our curated selection of premium essentials designed for the modern editorial lifestyle. Clean lines, superior materials, and effortless sophistication.</p>
</div>
<div class="flex flex-col md:flex-row gap-lg">
<!-- Sidebar Filters -->
<aside class="w-full md:w-64 flex-shrink-0">
<div class="sticky top-28 space-y-lg">
<!-- Category -->
<div>
<h3 class="font-label-lg text-label-lg uppercase tracking-widest text-on-surface mb-md">Category</h3>
	<div class="space-y-base filter-category-list">
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="outerwear"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Outerwear</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="knitwear"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Knitwear</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="shirts-tops"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Shirts &amp; Tops</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="trousers"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Trousers</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="footwear"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Footwear</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="dresses"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Dresses</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="bags"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Bags</span>
</label>
<label class="flex items-center group cursor-pointer">
<input class="w-5 h-5 rounded border-outline text-primary focus:ring-primary/20" data-category-filter="" type="checkbox" value="accessories"/>
<span class="ml-sm font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Accessories</span>
</label>
</div>
</div>
<hr class="border-outline-variant/30"/>
<!-- Price Range -->
<div>
<h3 class="font-label-lg text-label-lg uppercase tracking-widest text-on-surface mb-md">Price</h3>
<div class="space-y-base">
<input aria-label="Maximum product price" class="w-full accent-primary cursor-pointer" data-price-filter="" max="900" min="0" step="25" type="range" value="900"/>
<div class="flex justify-between items-center mt-sm">
<span class="font-label-md text-label-md text-on-surface-variant">$0</span>
<span class="font-label-md text-label-md text-on-surface-variant" data-price-output="">$900+</span>
</div>
</div>
</div>
<hr class="border-outline-variant/30"/>
<!-- Size -->
<div>
<h3 class="font-label-lg text-label-lg uppercase tracking-widest text-on-surface mb-md">Size</h3>
<div class="grid grid-cols-4 gap-xs">
<button class="py-sm border border-outline-variant text-label-md font-label-md hover:border-primary hover:text-primary transition-colors rounded-lg" data-filter-size="XS">XS</button>
<button class="py-sm border border-outline-variant text-label-md font-label-md hover:border-primary hover:text-primary transition-colors rounded-lg" data-filter-size="S">S</button>
<button class="py-sm border border-outline-variant text-label-md font-label-md hover:border-primary hover:text-primary transition-colors rounded-lg" data-filter-size="M">M</button>
<button class="py-sm border border-outline-variant text-label-md font-label-md hover:border-primary hover:text-primary transition-colors rounded-lg" data-filter-size="L">L</button>
</div>
</div>
<hr class="border-outline-variant/30"/>
<!-- Color -->
<div>
<h3 class="font-label-lg text-label-lg uppercase tracking-widest text-on-surface mb-md">Color</h3>
<div class="flex flex-wrap gap-sm">
<button aria-label="Filter black products" class="w-8 h-8 rounded-full border border-outline-variant bg-[#1A1A1A] ring-2 ring-offset-2 ring-transparent hover:ring-primary transition-all" data-filter-color="black"></button>
<button aria-label="Filter sand products" class="w-8 h-8 rounded-full border border-outline-variant bg-[#F5F5DC] ring-2 ring-offset-2 ring-transparent hover:ring-primary transition-all" data-filter-color="sand"></button>
<button aria-label="Filter gray products" class="w-8 h-8 rounded-full border border-outline-variant bg-[#E0E0E0] ring-2 ring-offset-2 ring-transparent hover:ring-primary transition-all" data-filter-color="gray"></button>
<button aria-label="Filter blue products" class="w-8 h-8 rounded-full border border-outline-variant bg-[#0040DF] ring-2 ring-offset-2 ring-transparent hover:ring-primary transition-all" data-filter-color="blue"></button>
<button aria-label="Filter olive products" class="w-8 h-8 rounded-full border border-outline-variant bg-[#4B5320] ring-2 ring-offset-2 ring-transparent hover:ring-primary transition-all" data-filter-color="olive"></button>
</div>
</div>
</div>
</aside>
<!-- Main Listing -->
<div class="flex-1">
<!-- Toolbar -->
	<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-base mb-md">
	<span class="font-body-sm text-body-sm text-on-surface-variant" data-product-count="">Showing products</span>
	<div class="flex items-center justify-between sm:justify-start space-x-sm w-full sm:w-auto">
	<span class="font-label-md text-label-md text-on-surface-variant">Sort by:</span>
	<select class="bg-surface border-none text-label-md font-label-md text-on-surface focus:ring-0 cursor-pointer max-w-full" data-sort-products="">
<option value="newest">Newest Arrivals</option>
<option value="price-low">Price: Low to High</option>
<option value="price-high">Price: High to Low</option>
<option value="popularity">Popularity</option>
</select>
</div>
</div>
<!-- Product Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md" data-collection-grid="">
<!-- Product Card 1 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="shirts-tops" data-collection-product="" data-colors="white" data-popularity="82" data-price="145" data-segment="men" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=shirt" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A high-fashion studio shot of a minimalist men's white poplin shirt with a sharp collar. The lighting is crisp and cool, casting soft, architectural shadows against a clean, off-white background. The mood is sophisticated and clinical, highlighting the high-quality fabric texture and precision tailoring in a modern editorial style." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrgWwcqbnGCbjTzZ_L6va3PhP4cf4IkTCLovTQVVQ21uNl8Z-iAYJCbtNUwdrpW52ANcPOZwSBpeAkbpFZnb0-vmkLpg0C_vW6vRxtMZj6gBIA2DX3yK8ePkauxAxka-V-BfxLiFYxmc6Mx5JHfFpx3WxafijUCHffhouLljkrx8EiJup7kTSg2xicdPlld859d7hAUJTEWheTEcKB3y6rjnpquQM6TXl1jopT4NOnhBwGC5VSLaG6XmWgnXyjf0PVwYVyU0-uFaM"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Structured Poplin Shirt</h3>
<span class="font-label-lg text-label-lg text-primary">$145</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Cotton Poplin • Optic White</p>
</div>
</div>
<!-- Product Card 2 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="outerwear" data-collection-product="" data-colors="black" data-popularity="95" data-price="895" data-segment="men" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=moto-jacket" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A luxury black leather jacket displayed in a minimalist boutique setting. The lighting is dramatic and cinematic, emphasizing the grain of the premium leather and metallic hardware. The color palette is dominated by deep blacks and dark grays with a sharp blue accent light. The overall aesthetic is moody, expensive, and modern." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTQq3n3DCDOOp4uvO55s47vNfLVHaHL4RRpmQqKOoCNfugHrUSjaTDkAo5K3YISIbA7a6jPcUShZsJ2Kg4juASweAYhtFdUprZXKWLmzBEBqEGrcYqOd5n_2XbRfHpRdAIXuQ_gktPbMPRiK3WoC9oUbfmD9gEkBh2mvSxQF4TL5GvJomBfX-CHRr8oP2nWafsoDLJB-Z1Ip4V9yuEAU8yFbdTe2Vn1fUWblpq2vamVyaLdv6edctwOp1ntp-15r6hJZVsMpz5A6U"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Asymmetric Moto Jacket</h3>
<span class="font-label-lg text-label-lg text-primary">$895</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Calfskin Leather • Midnight Black</p>
</div>
</div>
<!-- Product Card 3 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="knitwear" data-collection-product="" data-colors="sand" data-popularity="88" data-price="275" data-segment="women" data-sizes="XS,S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=cashmere" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A high-end beige cashmere sweater styled elegantly on a minimalist mannequin. The setting is a bright, sun-drenched minimalist loft with soft natural light highlighting the soft texture of the wool. The mood is serene, warm, and inviting, reflecting a high-quality, quiet luxury lifestyle." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKRsTPHqnzmiLlQT6UJpkPFNyO_MC4TNYb8SLuuMT6eYVM26cT9KAJMjVQ1qZWuiqfIJzs66s0pT0Kd283kZj60MPLYqtTndM0cpkUqRHwhU7wS3BBgv6mWNsd1ufC4mvnDY_lBSgkqZOWwcnoMLrVFit4gZ4t7j3erTI0U5TjES76gjQHKOoL4Pyp_EcWeesh6GetZ1KXeMvU5n8YwQzEs_heAJKyYt2P3wc_hLNamSKoddmgUVxUJG1Ek392j3LSNcbQVF-imaw"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Cashmere Crewneck</h3>
<span class="font-label-lg text-label-lg text-primary">$275</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Inner Mongolian Cashmere • Sand</p>
</div>
</div>
<!-- Product Card 4 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="outerwear" data-collection-product="" data-colors="blue" data-popularity="90" data-price="625" data-segment="women" data-sizes="M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=double-coat" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="Close-up detail of a navy blue wool overcoat. The focus is on the premium heavy-weight fabric and clean stitching. The lighting is diffused and soft, creating a calm and premium atmosphere. The background is a simple gray stone surface, maintaining a high-contrast and professional editorial vibe." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcfj6mRnoWAMit-uw8PQAtxQvdlt-iqN_hTRSSJrdUS9RKiKInc0kGnFBpnmILN-0f7085ep0PsOJAyJvcjGQC0AZV2KDseDTQhXIaTE-tlE9PDpl_W1Kv-UkVY5CX9ErctWGn33f-niKBOXx6nxueMslAmGGb8tuaNbO0Qrfd4RvLWNdFAVLw0UQgLLlaiUx3sfstE9KfQ3B8zYjtRhP83jmvpfPPrcIaadZsR6ApcWrRZFI3epE9kosgUMpHTjmgyQrEcdQE2rE"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Double-Breasted Coat</h3>
<span class="font-label-lg text-label-lg text-primary">$625</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Virgin Wool • Deep Navy</p>
</div>
</div>
<!-- Product Card 5 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="footwear" data-collection-product="" data-colors="white,blue" data-popularity="86" data-price="180" data-segment="accessories" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=sneaker" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A pair of clean, minimalist white sneakers arranged in a sculptural composition on a textured concrete pedestal. High-key lighting emphasizes the sleek design and premium materials. The scene is bright and airy with a sophisticated, modern minimalist vibe, punctuated by the high-energy blue of the brand's primary color in the background." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAUUNay2PR3AwHLDUHjx5Rujo1UCiEsk-gFH6OzLEieQu0HBGKsjxnRrFB5OZsc9q7B5o67a2InJi-Tdd3qIOWFJq2Todw4uuvtMZHAi5Cbb1uGXJ2gchjbwXGMDIdDwnmlo3L8KdqDZgb5A0Qu_u9e7ub53CAEpBZQTvyypWWQWnUUmr678MIqY9wQ-sv4MxG_ZRTmCD0MHg1aCUsq0ed1nrSrjQKUfem5AQoxDdWafvYL29gZ9NLCvNCfLF1cHxUNhPawinAQQ3c"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Leather Court Sneaker</h3>
<span class="font-label-lg text-label-lg text-primary">$180</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Full-Grain Leather • White/Blue</p>
</div>
</div>
<!-- Product Card 6 -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="trousers" data-collection-product="" data-colors="gray" data-popularity="80" data-price="225" data-segment="women" data-sizes="XS,S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=trousers" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-alt="A pair of high-waisted wide-leg trousers in a neutral gray tone, draped over a minimalist wooden chair. The lighting is directional, creating strong, artistic shadows and highlighting the clean pleats. The setting is a minimalist gallery space with soft white walls, conveying an effortless sense of luxury and precision." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuE_74HpXg2xZnPmAtU1hwrFAGeUUQLliTQ98uEl4VGiqN3aWycidkTB9OjKPu8hN1UHEG7s1uuA-UExUb0ah0ExP54zzeiWKm1wphy34Jgbk9dJLA87Jx_dvpE7M7bebLb3gIK9A5kb_8oBjTvBaU9fTn6G4Pe35kkg4htY0qyX2bn5Dw_CMLN5YewQj2YFp-A-wnTrwWeORhL54bfahvB8uF9zyiI4qq17CAILQv3ZB8Jn2-TGmQsjtUqheDI91WHuoytYiCkE0"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Pleated Wide Trousers</h3>
<span class="font-label-lg text-label-lg text-primary">$225</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Italian Wool • Ash Gray</p>
</div>
</div>
<!-- Product Card 7 — Trending (homepage) -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="outerwear" data-collection-product="" data-colors="blue" data-new-arrival="true" data-popularity="86" data-price="495" data-segment="women" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=coat" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Wool Tailored Coat" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBchUcD0EUx2j2iuPQpWTJiIV8_zw6fVXkrrcoW0NNOHi1SxdQu66MH8cT3G92PJljVOKIKgAIxyBfrzWWenQqSPhvb1LWVR7mApfsOtw2uz6Y0KuD0iE38tFoufpB9nbsctHFEUVTIfLvBiOPst0XZ0luxheHlGNr5fdlKJaBuMALpmJsNioBdRUkPlV9y0lbkOIJe3LmZLgRYbVi9X9cNiyeudwa6R7G7FYt8ukQkzQCN-Lq6ASZTwC8kocRV1qzT1TLfE4Ck0ws"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Wool Tailored Coat</h3>
<span class="font-label-lg text-label-lg text-primary">$495</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Wool • Navy</p>
</div>
</div>
<!-- Product Card 8 — Trending (homepage) -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="footwear" data-collection-product="" data-colors="black" data-new-arrival="true" data-popularity="84" data-price="285" data-segment="women" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=heels" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Leather Artisan Pump" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJnqionFY9y6EYbY_k4evRIJvyAUyoVg6PwXuwkjSxd9qMHcQnc8LWwRtP425gPIACwYi4BS5rEn6a0KGYC0FNUJvGW5DD0_kSsfAwo0JEcqudDivOu-ipKeVOJq6AGfrgwkWDX--L6eQ64NWowPC_RH-NzHnWgcRyYnqAasPBj1Kkzbs7jslOeGddj7tOwH78WBLt_Aj94R2TFl-YneG96_bN-w1tvvuaSkU-DBLuCJN2XQosE_KpP5P8hSYeH9aEujzdyJQhAKA"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Leather Artisan Pump</h3>
<span class="font-label-lg text-label-lg text-primary">$285</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Leather • Black</p>
</div>
</div>
<!-- Product Card 9 — Trending (homepage) -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="bags" data-collection-product="" data-colors="sand" data-new-arrival="true" data-popularity="83" data-price="425" data-segment="accessories" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=tote" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Structural Leather Tote" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQPtWo0O_h25ZD-uqlJNUOw_CMG6SjaIqwAdS7fF4-9zejORMZ_NS2z4p6KnILnMjKol4ArgZXQDMligjx1c4OZfzlnKZ0QCj3wVXujPKAC1OmvFlLDAwsIRshZBPok04K30_grPaCoCynebD3yteoWChW4NTJTnO3_ms3Qj_z61YdTuKy8wujCaIbt_2-hq2cf1tvvkCCTGkhrSYwJaECUQpkz1Nc7H3rbZ475nBm59md7IDO_JiPOxvWsjB_tlGvr5_oX2AaDRs"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Structural Leather Tote</h3>
<span class="font-label-lg text-label-lg text-primary">$425</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Leather • Cream</p>
</div>
</div>
<!-- Product Card 10 — Trending (homepage) -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="dresses" data-collection-product="" data-colors="white" data-new-arrival="true" data-popularity="87" data-price="690" data-segment="women" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=gown" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Silk Evening Gown" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyT9BhCFk-MjJkEIVTVNYLmb1QxR0SY6MzQjKyVpH6ALokWVkyTHfXcL2ePj0kD36BSmDQ2m1UX1l9b4U2Tdu6Ux_Jwi1oNQSi08hC2M2BgKgNtq5cjSrCZV5A51TrT7tgWxx1dIIwS_fPxiRfi1HDyR9tLER9Vb2k5gL75JToaQ-7z0vD5JDn-D6NuRPHrHGIS4-DIAHKY57ZRJ0l3agSH1MbIGmaB2agVwG8Mxy37aKCRpprkhttpXXdfeoWXyvTErZQS8kAyv4"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Silk Evening Gown</h3>
<span class="font-label-lg text-label-lg text-primary">$690</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Silk • Ivory</p>
</div>
</div>
<!-- Product Card 11 — You may also like -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="footwear" data-collection-product="" data-colors="gray,white" data-popularity="91" data-price="195" data-segment="men" data-sizes="M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=cloud-runner" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Cloud Runner" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDC93J-Mi_uyRyUJEzeo2VUt3gCnx1KweKP_DA251be5VmSSPWzQiE3EhjkhxBwd55Gdn3J9N_12Bg5RS3V-OcMAmCzGyfEn-qdSteSk2Sm2QxMbfFQaTV2PYuxS-hkbx1W8FzSC8WNHMBlUvT6u9qRYmqskiYnAom81LJ-H0Fo81sIZgmqT2a5PI1sSigLrSXPZT1NqMpPwhbGMQ7KSvY-jFjHsUWYYIzRGNgt1QR5JJXBwnUKEJSA31X41LiJjnESmcWWo8QyvTc"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Cloud Runner</h3>
<span class="font-label-lg text-label-lg text-primary">$195</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Mesh • Grey/White</p>
</div>
</div>
<!-- Product Card 12 — You may also like -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="footwear" data-collection-product="" data-colors="black" data-popularity="93" data-price="240" data-segment="men" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=downtown-high-top" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Downtown High-Top" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjpeUssbT7HMtTqW4qyakzgJS1FiU-SwjRpIy1Tw9BZ6aW8dg1ia9mBEQTsXehCdnQ38ZNuPh-KwVqfaeAzAieT8_pSEHP2-gnuITMw24A-7ga_cJJtTAdWKtIKtSQB42G6Lq4ASqeKiDYxXOXIJ_svraHjMSC6L9xtrDwdUFYZMV5tu9rBzDSf-JQj_B9GQqtOtgotp4hmyshrGNnQ18q8fvkriRBUhxAH8NCNZuoL9oEZk47FOljzcP6gzOFg5XhTuuE5NEFFuA"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Downtown High-Top</h3>
<span class="font-label-lg text-label-lg text-primary">$240</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Leather • Mahogany</p>
</div>
</div>
<!-- Product Card 13 — You may also like -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="bags" data-collection-product="" data-colors="sand" data-popularity="89" data-price="375" data-segment="accessories" data-sizes="S,M,L">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=essential-tote" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Essential Tote" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZA8QsI1z-4yH3tVTcLmNVZSvq3xbj2ReUE6tr_ldz3pUm4x6nLz0K8UtTdzYAXvt7OIQ4lGi_6bYNDOyqL9I_QJVDOF48pDIH-Hl8svspIO25hllQki_zXnMSkeUILE1UWGtW97p85dErHOd_YKll0yV9E6Bw0WrAMHHHh88cflfziP3AfRvQ8HCNboloyIkI7rub2yxdcXDNddCggX3FjoxliU_zAL_I9FGwVFlgZKL2z8nMK2kpTSHbaQalI_DPVzFDIE-mzos"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Essential Tote</h3>
<span class="font-label-lg text-label-lg text-primary">$375</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Leather • Sand</p>
</div>
</div>
<!-- Product Card 14 — You may also like -->
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="accessories" data-collection-product="" data-colors="gray" data-popularity="85" data-price="220" data-segment="accessories" data-sizes="S,M">
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=mono-watch" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Mono-Chrome Watch" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4Z1PFbN5d5iX569B8_T5_giVQDfygycpDU7KE5ZeWXlQyLIJmRezROLUNVXqri-ol3nabfGSMPEJ4iCQRQ3T6ieAeILwdCX24kcjlMHyXEspuvHJa7qUNV4Ty59HPXyJstJCij6Din9uuEUaPdOqaoCdpTSzOijKAqnOB29zI02kZbQC29RivviKhLtFIO0S6vHSxpncfEwf_k_0GPHYo0IEKYDIqk8vh4-4e-BGwzb7smuC_FkCeST4f3apB7htMF-P6Mo4ECbE"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Mono-Chrome Watch</h3>
<span class="font-label-lg text-label-lg text-primary">$220</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Steel • Silver</p>
</div>
</div>
<?php foreach ($retailerProducts as $product): ?>
<?php
$colorNames = $product["colors"] ?: [$product["defaultColor"]];
$colorFilters = array_values(array_unique(array_map("collection_color_filter", $colorNames)));
$colorText = $product["defaultColor"] ?: ($colorNames[0] ?? "Default");
$newArrivalAttr = $product["newArrival"] ? ' data-new-arrival="true"' : "";
?>
<div class="product-card group relative bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden" data-category="<?= e($product["category"]) ?>" data-collection-product="" data-colors="<?= e(implode(",", $colorFilters)) ?>" data-popularity="<?= e((string) $product["popularity"]) ?>" data-price="<?= e((string) $product["price"]) ?>" data-segment="<?= e($product["segment"]) ?>" data-sizes="<?= e(implode(",", $product["sizes"])) ?>"<?= $newArrivalAttr ?>>
<div class="aspect-[3/4] relative overflow-hidden bg-surface-container-low">
<a class="block w-full h-full" href="/product.php?product=<?= e($product["id"]) ?>" aria-label="View product"><img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="<?= e($product["name"]) ?>" src="<?= e($product["image"]) ?>"/></a>
<a class="add-to-bag js-cart-add-line absolute bottom-4 left-4 right-4 bg-on-background text-surface-container-lowest py-3 rounded-lg font-label-lg text-label-lg opacity-0 translate-y-2 transition-all duration-300 hover:bg-primary text-center" href="/checkout.php">Add to Bag</a>
</div>
<div class="p-md">
<div class="flex justify-between items-start mb-xs">
<h3 class="font-headline-sm text-headline-sm text-on-surface"><?= e($product["name"]) ?></h3>
<span class="font-label-lg text-label-lg text-primary">$<?= e(number_format((float) $product["price"], 0)) ?></span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant"><?= e(collection_category_label($product["category"])) ?> • <?= e($colorText) ?></p>
</div>
</div>
<?php endforeach; ?>
</div>
<p class="hidden mt-lg text-center font-body-md text-body-md text-on-surface-variant" data-empty-products="">No products match those filters.</p>
<!-- Pagination -->
<div class="mt-xl flex justify-center">
<p class="font-body-sm text-body-sm text-on-surface-variant">All available pieces are shown. Use filters or sorting to refine the collection.</p>
</div>
</div>
</div>
</main>
<!-- Footer -->
<footer class="w-full bg-surface-container-low dark:bg-surface-container-highest border-t border-outline-variant">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto">
<div class="space-y-md">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface dark:text-inverse-on-surface">LUXE</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">Crafting timeless essentials for the modern lifestyle. Quality over quantity, always.</p>
</div>
<div class="space-y-md">
<h4 class="font-label-lg text-label-lg text-on-surface dark:text-inverse-on-surface font-semibold">Shop</h4>
<ul class="space-y-base font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80">
<li><a class="hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/#sustainability">Sustainability</a></li>
<li><a class="hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/product.php#shipping-returns">Shipping &amp; Returns</a></li>
<li><a class="hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Contact Us</a></li>
</ul>
</div>
<div class="space-y-md">
<h4 class="font-label-lg text-label-lg text-on-surface dark:text-inverse-on-surface font-semibold">Legal</h4>
<ul class="space-y-base font-body-sm text-body-sm text-on-surface-variant dark:text-on-surface-variant/80">
<li><a class="hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Privacy Policy</a></li>
<li><a class="hover:text-primary dark:hover:text-inverse-primary transition-colors" href="/checkout.php">Terms of Service</a></li>
</ul>
</div>
<div class="space-y-md">
<h4 class="font-label-lg text-label-lg text-on-surface dark:text-inverse-on-surface font-semibold">Join the list</h4>
<div class="flex">
<input class="bg-surface-container-lowest border border-outline-variant rounded-l-lg px-md py-sm font-body-sm w-full focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="Email address" type="email"/>
<button class="bg-primary text-on-primary px-md py-sm rounded-r-lg font-label-lg hover:opacity-90 transition-opacity" type="button">JOIN</button>
</div>
</div>
</div>
<div class="max-w-container-max mx-auto px-gutter py-md border-t border-outline-variant/30">
<p class="font-body-sm text-body-sm text-on-surface-variant/60 text-center">© 2024 LUXE Premium E-commerce. All rights reserved.</p>
</div>
</footer>
<script src="/assets/js/site.js"></script>
</body></html>
