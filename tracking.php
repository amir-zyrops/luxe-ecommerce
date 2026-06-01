<?php

declare(strict_types=1);

require_once __DIR__ . "/includes/database.php";
require_once __DIR__ . "/includes/tracking.php";

$trackingNumber = luxe_tracking_clean_number($_GET["tracking"] ?? "");
$tracking = null;
$trackingError = "";

if ($trackingNumber !== "") {
    try {
        $row = luxe_tracking_fetch(luxe_db(), $trackingNumber);
        if ($row) {
            $tracking = luxe_tracking_public_payload($row);
        } else {
            $trackingError = "No delivery was found for that tracking number.";
        }
    } catch (Throwable) {
        $trackingError = "Tracking is temporarily unavailable. Try again later.";
    }
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
<title>LUXE | Track Order</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Work+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "surface": "#f9f9f9",
        "background": "#f9f9f9",
        "surface-container-lowest": "#ffffff",
        "surface-container-low": "#f3f3f3",
        "surface-container": "#eeeeee",
        "surface-container-highest": "#e2e2e2",
        "on-surface": "#1a1c1c",
        "on-background": "#1a1c1c",
        "on-surface-variant": "#434656",
        "primary": "#0040df",
        "on-primary": "#ffffff",
        "outline-variant": "#c4c5d9",
        "secondary": "#5f5e5e",
        "inverse-primary": "#b8c3ff",
        "inverse-on-surface": "#f0f1f1",
        "error": "#ba1a1a",
        "error-container": "#ffdad6"
      },
      borderRadius: {"lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
      spacing: {"xs": "4px", "sm": "12px", "base": "8px", "gutter": "24px", "md": "24px", "lg": "48px", "xl": "80px", "container-max": "1280px"},
      fontFamily: {
        "headline-lg": ["Inter"],
        "headline-sm": ["Inter"],
        "headline-md": ["Inter"],
        "body-md": ["Work Sans"],
        "body-sm": ["Work Sans"],
        "label-lg": ["Work Sans"],
        "label-md": ["Work Sans"]
      },
      fontSize: {
        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
        "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "24px"}],
        "body-sm": ["14px", {"lineHeight": "20px"}],
        "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600"}],
        "label-md": ["12px", {"lineHeight": "16px", "fontWeight": "500"}]
      }
    }
  }
}
</script>
<style>
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
<link href="/assets/luxe-favicon.svg" rel="icon" type="image/svg+xml"/>
<link href="/assets/css/site.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary selection:text-on-primary">
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 shadow-sm">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="luxe-site-logo" href="/" aria-label="LUXE home">
<img src="/assets/luxe-mark.svg" alt="" aria-hidden="true"/>
<span>LUXE</span>
</a>
<nav class="hidden md:flex items-center gap-lg">
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/collections.php?view=new-arrivals">New Arrivals</a>
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/collections.php">Collections</a>
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/collections.php?segment=men">Men</a>
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/collections.php?segment=women">Women</a>
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/collections.php?segment=accessories">Accessories</a>
<a class="font-label-lg text-label-lg text-secondary hover:text-primary transition-colors" href="/retailer/login.php">Become a Retailer</a>
</nav>
<div class="flex items-center gap-md">
<a aria-label="View checkout" class="relative inline-flex items-center text-primary hover:opacity-80 transition-opacity" href="/checkout.php">
<span class="material-symbols-outlined text-primary" data-icon="shopping_bag">shopping_bag</span>
<span class="absolute -top-0.5 -right-1 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
</a>
<a aria-label="Account" class="material-symbols-outlined text-primary hover:opacity-80 transition-opacity" data-icon="person" href="/">person</a>
</div>
</div>
</header>

<main class="max-w-container-max mx-auto px-gutter py-xl">
<section class="max-w-3xl mx-auto">
<div class="mb-lg">
<p class="font-label-md text-label-md text-primary uppercase tracking-widest mb-xs">Delivery</p>
<h1 class="font-headline-lg text-headline-lg text-on-surface">Track Order</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-sm">Enter the tracking number from your confirmation email.</p>
</div>

<form class="bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-md shadow-sm flex flex-col sm:flex-row gap-sm mb-lg" method="get" action="/tracking.php">
<input class="flex-1 px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none uppercase tracking-wide" name="tracking" placeholder="LXTRK-XXXXXXXXXXXX" value="<?= e($trackingNumber) ?>"/>
<button class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">Track</button>
</form>

<?php if ($trackingError !== ""): ?>
<div class="bg-error-container/60 border border-error/20 rounded-xl p-md text-error font-body-md text-body-md">
<?= e($trackingError) ?>
</div>
<?php elseif ($tracking): ?>
<article class="bg-surface-container-lowest border border-outline-variant/30 rounded-xl shadow-sm overflow-hidden">
<div class="p-lg border-b border-outline-variant/30">
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-md">
<div>
<p class="font-label-md text-label-md uppercase tracking-widest text-on-surface-variant">Tracking number</p>
<h2 class="font-headline-md text-headline-md text-on-surface mt-xs"><?= e($tracking["number"]) ?></h2>
</div>
<div class="inline-flex items-center gap-sm px-md py-sm rounded-full bg-surface-container-low text-primary font-label-lg text-label-lg w-fit">
<span class="material-symbols-outlined text-[20px]">local_shipping</span>
<?= e($tracking["statusLabel"]) ?>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-md mt-lg">
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Order</p>
<p class="font-body-md text-body-md text-on-surface mt-xs"><?= e($tracking["orderNumber"]) ?></p>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Carrier</p>
<p class="font-body-md text-body-md text-on-surface mt-xs"><?= e($tracking["carrier"]) ?></p>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Estimated Delivery</p>
<p class="font-body-md text-body-md text-on-surface mt-xs"><?= e($tracking["estimatedDelivery"] ?: "Pending") ?></p>
</div>
</div>
</div>

<div class="p-lg">
<div class="space-y-md">
<?php foreach ($tracking["steps"] as $step): ?>
<?php
    $state = (string) $step["state"];
    $isCurrent = $state === "current";
    $isComplete = $state === "complete";
    $icon = $isComplete ? "check_circle" : ($isCurrent ? "radio_button_checked" : "radio_button_unchecked");
    $color = ($isComplete || $isCurrent) ? "text-primary" : "text-on-surface-variant/50";
?>
<div class="flex items-center gap-md">
<span class="material-symbols-outlined <?= e($color) ?>"><?= e($icon) ?></span>
<span class="font-body-md text-body-md <?= $isCurrent ? "text-on-surface font-semibold" : "text-on-surface-variant" ?>"><?= e($step["label"]) ?></span>
</div>
<?php endforeach; ?>
</div>
<?php if ($tracking["destinationCity"] !== ""): ?>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-lg">Destination: <?= e($tracking["destinationCity"]) ?></p>
<?php endif; ?>
</div>
</article>
<?php else: ?>
<div class="bg-surface-container-low border border-outline-variant/30 rounded-xl p-lg text-center">
<span class="material-symbols-outlined text-primary text-4xl mb-sm">local_shipping</span>
<p class="font-body-md text-body-md text-on-surface-variant">Tracking details will appear here after checkout.</p>
</div>
<?php endif; ?>
</section>
</main>

<footer class="w-full bg-surface-container-low dark:bg-surface-container-highest border-t border-outline-variant mt-xl">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto">
<div class="md:col-span-1">
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-md">LUXE</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">© 2024 LUXE Premium E-commerce. All rights reserved.</p>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Support</h5>
<ul class="space-y-sm font-body-sm text-body-sm text-on-surface-variant">
<li><a class="hover:text-primary transition-colors" href="/tracking.php">Track Order</a></li>
<li><a class="hover:text-primary transition-colors" href="/product.php#shipping-returns">Shipping &amp; Returns</a></li>
<li><a class="hover:text-primary transition-colors" href="/checkout.php">Contact Us</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Shop</h5>
<ul class="space-y-sm font-body-sm text-body-sm text-on-surface-variant">
<li><a class="hover:text-primary transition-colors" href="/collections.php?view=new-arrivals">New Arrivals</a></li>
<li><a class="hover:text-primary transition-colors" href="/collections.php">Collections</a></li>
</ul>
</div>
<div>
<h5 class="font-label-lg text-label-lg text-on-surface font-semibold mb-md">Retailer</h5>
<ul class="space-y-sm font-body-sm text-body-sm text-on-surface-variant">
<li><a class="hover:text-primary transition-colors" href="/retailer/login.php">Become a Retailer</a></li>
</ul>
</div>
</div>
</footer>
<script src="/assets/js/site.js"></script>
</body></html>
