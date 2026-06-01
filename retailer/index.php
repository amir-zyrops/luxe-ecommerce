<?php

declare(strict_types=1);

require_once __DIR__ . "/../includes/database.php";

session_name("LUXESELLERSESSID");
session_start();

$pdo = luxe_db();

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        handle_retailer_post($pdo);
    }
} catch (Throwable $error) {
    set_retailer_flash("error", $error instanceof DomainException ? $error->getMessage() : "Retailer request failed.");
    redirect_back_to_retailer();
}

$retailer = current_retailer_account($pdo);
$view = strtolower(trim((string) ($_GET["view"] ?? "dashboard")));
$publicViews = ["login", "signup"];

if (!$retailer && !in_array($view, $publicViews, true)) {
    $view = "login";
}
if ($retailer && in_array($view, $publicViews, true)) {
    redirect_to_retailer("dashboard");
}

$flash = take_retailer_flash();
$isAdmin = ($retailer["role"] ?? "") === "admin";
$editingProduct = null;
$myProducts = [];
$pendingProducts = [];
$approvedProducts = [];
$retailerDirectory = [];
$retailerMessages = [];
$adminMessageRetailers = [];
$adminMessageThreads = [];
$stats = $retailer ? retailer_stats($pdo, $retailer) : [];

if ($retailer && !$isAdmin && $view === "products") {
    $view = "dashboard";
}

if ($retailer && $view === "add" && isset($_GET["edit"])) {
    $editingProduct = fetch_owned_product($pdo, (int) $retailer["id"], retailer_slug($_GET["edit"]));
    if (!$editingProduct) {
        set_retailer_flash("error", "Product not found for this retailer account.");
        redirect_to_retailer("dashboard");
    }
}
if ($retailer && !$isAdmin && $view === "dashboard") {
    $myProducts = fetch_retailer_products($pdo, (int) $retailer["id"]);
}
if ($retailer && $isAdmin && $view === "admin") {
    $pendingProducts = fetch_pending_products($pdo);
}
if ($retailer && $isAdmin && $view === "approved") {
    $approvedProducts = fetch_approved_products($pdo);
}
if ($retailer && $isAdmin && $view === "retailers") {
    $retailerDirectory = fetch_retailer_directory($pdo);
}
if ($retailer && $isAdmin) {
    $adminMessageRetailers = fetch_message_retailers($pdo);
    $adminMessageThreads = fetch_admin_message_threads($pdo);
}
if ($retailer && !$isAdmin) {
    $retailerMessages = fetch_retailer_messages($pdo, (int) $retailer["id"]);
}
$navLink = "font-label-lg text-label-lg text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors";
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>LUXE | Retailer Portal</title>
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
      }
    </script>
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
</style>
<link href="/assets/luxe-favicon.svg" rel="icon" type="image/svg+xml"/>
<link href="/assets/css/site.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary-fixed selection:text-on-primary-fixed">
<header class="sticky top-0 w-full z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 shadow-sm">
<div class="flex justify-between items-center h-20 px-gutter max-w-container-max mx-auto">
<a class="luxe-site-logo" href="/" aria-label="LUXE home">
<img src="/assets/luxe-mark.svg" alt="" aria-hidden="true"/>
<span>LUXE</span>
</a>
<nav class="hidden md:flex items-center gap-lg">
<a class="<?= $navLink ?>" href="/">Storefront</a>
<a class="<?= $navLink ?>" href="/collections.php">Collections</a>
<?php if ($retailer): ?>
<a class="<?= $navLink ?> <?= in_array($view, ["dashboard", "admin", "approved", "retailers", "add"], true) ? "text-primary dark:text-inverse-primary" : "" ?>" href="index.php?view=dashboard">Dashboard</a>
<?php else: ?>
<a class="<?= $navLink ?> <?= in_array($view, ["login", "signup"], true) ? "text-primary dark:text-inverse-primary" : "" ?>" href="login.php">Become a Retailer</a>
<?php endif; ?>
</nav>
<div class="flex items-center gap-md">
<a aria-label="View checkout" class="relative hover:opacity-80 transition-opacity active:scale-95 text-primary dark:text-inverse-primary" href="/checkout.php">
<span class="material-symbols-outlined text-primary dark:text-inverse-primary">shopping_bag</span>
<span class="absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 px-0.5 bg-primary text-on-primary text-[10px] leading-none flex items-center justify-center rounded-full hidden" data-bag-count="">0</span>
</a>
<?php if ($retailer): ?>
<form action="index.php" method="post">
<input name="action" type="hidden" value="logout"/>
<button class="p-base text-primary dark:text-inverse-primary hover:opacity-80 transition-opacity" type="submit" aria-label="Sign out">
<span class="material-symbols-outlined">logout</span>
</button>
</form>
<?php else: ?>
<a aria-label="Become a retailer" class="p-base hover:opacity-80 transition-opacity active:scale-95 text-primary dark:text-inverse-primary" href="login.php">
<span class="material-symbols-outlined">storefront</span>
</a>
<?php endif; ?>
<button class="md:hidden p-base text-primary" type="button">
<span class="material-symbols-outlined">menu</span>
</button>
</div>
</div>
</header>

<main class="max-w-container-max mx-auto px-gutter py-xl">
<?php if ($flash): ?>
<div class="mb-lg rounded-lg border <?= $flash["type"] === "error" ? "border-error/30 bg-error-container text-on-error-container" : "border-primary/20 bg-primary-fixed text-on-primary-fixed" ?> px-md py-sm font-body-sm text-body-sm">
<?= e($flash["message"]) ?>
</div>
<?php endif; ?>

<?php if ($view === "login"): ?>
<section class="grid grid-cols-1 lg:grid-cols-2 gap-lg items-start">
<div>
<span class="font-label-md text-label-md text-primary uppercase tracking-widest">Retailer Portal</span>
<h1 class="font-headline-lg text-headline-lg text-on-surface mt-xs mb-sm">Retailer Login</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">Use a retailer account to manage products immediately. Products become public only after approval.</p>
</div>
<section class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm">
<h2 class="font-headline-md text-headline-md text-on-surface mb-md">Retailer Login</h2>
<form class="space-y-md" action="index.php?view=login" method="post">
<input name="action" type="hidden" value="login"/>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-login-email">Email</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-login-email" name="email" required type="email"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-login-password">Password</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-login-password" name="password" required type="password"/>
</div>
<button class="w-full bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">Login</button>
<p class="font-body-sm text-body-sm text-on-surface-variant text-center">New seller? <a class="text-primary hover:underline" href="signup.php">Create a retailer account</a>.</p>
</form>
</section>
</section>
<?php elseif ($view === "signup"): ?>
<section class="grid grid-cols-1 lg:grid-cols-2 gap-lg items-start">
<div>
<span class="font-label-md text-label-md text-primary uppercase tracking-widest">Retailer Portal</span>
<h1 class="font-headline-lg text-headline-lg text-on-surface mt-xs mb-sm">Create Retailer Account</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">Create a seller account with your store email, then add and manage products from the dashboard. Admin approval only controls public product visibility.</p>
</div>
<section class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm">
<h2 class="font-headline-md text-headline-md text-on-surface mb-md">Create Retailer Account</h2>
<form class="space-y-md" action="index.php?view=signup" method="post">
<input name="action" type="hidden" value="register"/>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-name">Name</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-name" maxlength="120" name="display_name" required type="text"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-business">Store / Business Name</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-business" maxlength="160" name="business_name" required type="text"/>
</div>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-register-email">Email</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-register-email" name="email" required type="email"/>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-register-password">Password</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-register-password" minlength="8" name="password" required type="password"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="retailer-confirm-password">Confirm Password</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="retailer-confirm-password" minlength="8" name="confirm_password" required type="password"/>
</div>
</div>
<button class="w-full border border-on-surface text-on-surface px-md py-sm rounded-lg font-label-lg text-label-lg hover:bg-surface-container-high active:scale-95 transition-all" type="submit">Register</button>
<p class="font-body-sm text-body-sm text-on-surface-variant text-center">Already registered? <a class="text-primary hover:underline" href="login.php">Login to the retailer portal</a>.</p>
</form>
</section>
</section>
<?php elseif ($view === "dashboard" && $retailer): ?>
<section class="space-y-lg">
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-md">
<div>
<span class="font-label-md text-label-md text-primary uppercase tracking-widest">Retailer Portal</span>
<h1 class="font-headline-lg text-headline-lg text-on-surface mt-xs"><?= e($retailer["display_name"]) ?></h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs"><?= e($retailer["business_name"] ?: ucfirst((string) $retailer["role"]) . " account") ?></p>
</div>
<?php if (!$isAdmin): ?>
<a class="inline-flex items-center justify-center gap-sm bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" href="index.php?view=add">
<span class="material-symbols-outlined">add</span>
Add Product
</a>
<?php endif; ?>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-md">
<?php foreach ($stats as $label => $value): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm">
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest"><?= e($label) ?></p>
<p class="font-headline-lg text-headline-lg text-on-surface mt-sm"><?= e((string) $value) ?></p>
</div>
<?php endforeach; ?>
</div>
<?php if ($isAdmin): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-md">
<a class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm hover:border-primary transition-colors" href="index.php?view=admin">
<span class="material-symbols-outlined text-primary">fact_check</span>
<h2 class="font-headline-md text-headline-md mt-sm text-on-surface">Admin Approval</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Review pending product submissions before they appear publicly.</p>
</a>
<a class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm hover:border-primary transition-colors" href="index.php?view=approved">
<span class="material-symbols-outlined text-primary">inventory</span>
<h2 class="font-headline-md text-headline-md mt-sm text-on-surface">Approved Products</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Open live product pages or remove approved listings.</p>
</a>
<a class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm hover:border-primary transition-colors" href="index.php?view=retailers">
<span class="material-symbols-outlined text-primary">store</span>
<h2 class="font-headline-md text-headline-md mt-sm text-on-surface">Retailers</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">View retailer account details and product counts.</p>
</a>
</div>
<?php render_admin_message_panel($adminMessageRetailers, $adminMessageThreads); ?>
<?php else: ?>
<section class="space-y-md">
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Products</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Edit, resubmit, or delete products owned by this retailer account.</p>
</div>
<?php render_retailer_product_table($myProducts); ?>
</section>
<?php render_retailer_message_panel($retailerMessages); ?>
<?php endif; ?>
</section>
<?php elseif ($view === "approved" && $retailer && $isAdmin): ?>
<section class="space-y-lg">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface">Approved Products</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs">Approved products that can appear on the public storefront.</p>
</div>
<?php render_admin_product_table($approvedProducts, "approved"); ?>
</section>
<?php elseif ($view === "retailers" && $retailer && $isAdmin): ?>
<section class="space-y-lg">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface">Retailers</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs">Retailer account details and product totals.</p>
</div>
<?php render_retailer_directory($retailerDirectory); ?>
</section>
<?php elseif ($view === "add" && $retailer && !$isAdmin): ?>
<?php
$isEdit = is_array($editingProduct);
$formProduct = $editingProduct ?: [
    "product_slug" => "",
    "name" => "",
    "description" => "",
    "category" => "shirts-tops",
    "segment" => "women",
    "price" => "",
    "stock_quantity" => 0,
    "image_url" => "",
    "default_color" => "",
    "available_colors" => ["Black"],
    "available_sizes" => ["S", "M", "L"],
    "active" => true,
];
$colorValue = implode(", ", array_values((array) $formProduct["available_colors"]));
$sizeValue = implode(", ", array_values((array) $formProduct["available_sizes"]));
?>
<section class="max-w-3xl">
<div class="mb-lg">
<h1 class="font-headline-lg text-headline-lg text-on-surface"><?= $isEdit ? "Edit Product" : "Add Product" ?></h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs"><?= $isEdit ? "You can manage this product now; edited products return to pending review before public listing." : "You can add products now; customers see them only after admin approval." ?></p>
</div>
<form class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm space-y-md" action="index.php" method="post" enctype="multipart/form-data">
<input name="action" type="hidden" value="save_product"/>
<?php if ($isEdit): ?>
<input name="product_slug" type="hidden" value="<?= e($formProduct["product_slug"]) ?>"/>
<?php endif; ?>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-name">Product Name</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-name" maxlength="180" name="name" required type="text" value="<?= e($formProduct["name"]) ?>"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-description">Description</label>
<textarea class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none min-h-28" id="product-description" maxlength="1000" name="description" required><?= e($formProduct["description"]) ?></textarea>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-category">Category</label>
<select class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-category" name="category" required>
<?php foreach (retailer_categories() as $category): ?>
<option value="<?= e($category) ?>" <?= $formProduct["category"] === $category ? "selected" : "" ?>><?= e(category_label($category)) ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-segment">Segment</label>
<select class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-segment" name="segment" required>
<?php foreach (["women" => "Women", "men" => "Men", "accessories" => "Accessories"] as $value => $label): ?>
<option value="<?= e($value) ?>" <?= $formProduct["segment"] === $value ? "selected" : "" ?>><?= e($label) ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-price">Price</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-price" min="0.01" name="price" required step="0.01" type="number" value="<?= e((string) $formProduct["price"]) ?>"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-stock">Stock Quantity</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-stock" min="0" name="stock_quantity" required step="1" type="number" value="<?= e((string) $formProduct["stock_quantity"]) ?>"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-status">Status</label>
<select class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-status" name="active" required>
<option value="1" <?= ((bool) $formProduct["active"]) ? "selected" : "" ?>>Active</option>
<option value="0" <?= !((bool) $formProduct["active"]) ? "selected" : "" ?>>Inactive</option>
</select>
</div>
<div class="space-y-sm">
<label class="block font-label-md text-label-md text-on-surface" for="product-image">Image URL</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-image" name="image_url" type="url" value="<?= e($formProduct["image_url"]) ?>" placeholder="https://example.com/product.jpg"/>
<label class="block font-label-md text-label-md text-on-surface" for="product-image-file">Upload Image</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none bg-surface-container-lowest" id="product-image-file" name="product_image" type="file" accept="image/jpeg,image/png,image/webp,image/gif"/>
<p class="font-body-sm text-body-sm text-on-surface-variant">Paste an image URL or upload a JPG, PNG, WebP, or GIF file up to 5 MB.</p>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-colors">Available Colors</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-colors" name="available_colors" required type="text" value="<?= e($colorValue) ?>"/>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="product-sizes">Available Sizes</label>
<input class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="product-sizes" name="available_sizes" required type="text" value="<?= e($sizeValue) ?>"/>
</div>
</div>
<div class="flex flex-col sm:flex-row gap-sm">
<button class="bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit"><?= $isEdit ? "Resubmit Product" : "Submit Product" ?></button>
<a class="border border-on-surface text-on-surface px-md py-sm rounded-lg font-label-lg text-label-lg hover:bg-surface-container-high transition-all text-center" href="index.php?view=dashboard">Cancel</a>
</div>
</form>
</section>
<?php elseif ($view === "admin" && $retailer && $isAdmin): ?>
<section class="space-y-lg">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface">Admin Approval</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs">Pending retailer products must be approved before public shoppers can see them.</p>
</div>
<?php if (!$pendingProducts): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No pending product submissions.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 gap-md">
<?php foreach ($pendingProducts as $product): ?>
<article class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm">
<div class="grid grid-cols-1 md:grid-cols-[120px_1fr] gap-md">
<div class="aspect-[3/4] bg-surface-container rounded-lg overflow-hidden">
<?php if ($product["image_url"]): ?>
<img class="w-full h-full object-cover" alt="" src="<?= e($product["image_url"]) ?>"/>
<?php endif; ?>
</div>
<div class="space-y-md">
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-sm">
<div>
<p class="font-label-md text-label-md text-primary uppercase tracking-widest"><?= e($product["business_name"] ?: $product["display_name"]) ?></p>
<h2 class="font-headline-md text-headline-md text-on-surface"><?= e($product["name"]) ?></h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs"><?= e($product["description"]) ?></p>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs"><?= e(category_label($product["category"])) ?> / <?= e(ucfirst((string) $product["segment"])) ?> / $<?= e(number_format((float) $product["price"], 2)) ?> / Stock <?= e((string) $product["stock_quantity"]) ?> / <?= ((bool) $product["active"]) ? "Active" : "Inactive" ?></p>
</div>
<span class="<?= status_badge_class("pending") ?>">Pending</span>
</div>
<div class="flex flex-col md:flex-row gap-sm">
<form action="index.php" method="post">
<input name="action" type="hidden" value="admin_approve"/>
<input name="product_slug" type="hidden" value="<?= e($product["product_slug"]) ?>"/>
<button class="inline-flex items-center justify-center gap-sm bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">
<span class="material-symbols-outlined">check</span>
Approve
</button>
</form>
<form action="index.php" method="post">
<input name="action" type="hidden" value="admin_delete_product"/>
<input name="return_view" type="hidden" value="admin"/>
<input name="product_slug" type="hidden" value="<?= e($product["product_slug"]) ?>"/>
<button class="inline-flex items-center justify-center gap-sm border border-on-error-container text-on-error-container px-md py-sm rounded-lg font-label-lg text-label-lg hover:bg-error-container active:scale-95 transition-all" type="submit">
<span class="material-symbols-outlined">delete</span>
Delete
</button>
</form>
<form class="flex flex-col sm:flex-row gap-sm flex-1" action="index.php" method="post">
<input name="action" type="hidden" value="admin_reject"/>
<input name="product_slug" type="hidden" value="<?= e($product["product_slug"]) ?>"/>
<input class="flex-1 px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" maxlength="300" name="rejection_reason" placeholder="Reason for rejection" type="text"/>
<button class="inline-flex items-center justify-center gap-sm border border-on-error-container text-on-error-container px-md py-sm rounded-lg font-label-lg text-label-lg hover:bg-error-container active:scale-95 transition-all" type="submit">
<span class="material-symbols-outlined">close</span>
Reject
</button>
</form>
</div>
</div>
</div>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</section>
<?php else: ?>
<section class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<h1 class="font-headline-md text-headline-md text-on-surface">Page unavailable</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-xs">This retailer portal page is not available for the current account.</p>
</section>
<?php endif; ?>
</main>

<script src="/assets/js/site.js"></script>
</body>
</html>
<?php

function render_retailer_product_table(array $products): void
{
    if (!$products): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No products submitted yet.</p>
</div>
<?php
        return;
    endif;
?>
<div class="overflow-x-auto bg-surface-container-lowest border border-outline-variant/30 rounded-lg shadow-sm">
<table class="w-full text-left">
<thead class="border-b border-outline-variant/30">
<tr>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Product</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Category</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Price</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Stock</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Approval</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Visibility</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Updated</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($products as $product): ?>
<tr class="border-b border-outline-variant/20 last:border-0">
<td class="p-md">
<div class="flex items-center gap-sm min-w-64">
<div class="w-12 h-16 bg-surface-container rounded overflow-hidden flex-shrink-0">
<?php if ($product["image_url"]): ?>
<img class="w-full h-full object-cover" alt="" src="<?= e($product["image_url"]) ?>"/>
<?php endif; ?>
</div>
<div>
<p class="font-label-lg text-label-lg text-on-surface"><?= e($product["name"]) ?></p>
<p class="font-body-sm text-body-sm text-on-surface-variant"><?= e($product["description"]) ?></p>
<?php if ($product["rejection_reason"]): ?>
<p class="font-body-sm text-body-sm text-on-error-container"><?= e($product["rejection_reason"]) ?></p>
<?php endif; ?>
</div>
</div>
</td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e(category_label((string) $product["category"])) ?></td>
<td class="p-md font-label-md text-label-md text-primary">$<?= e(number_format((float) $product["price"], 2)) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e((string) $product["stock_quantity"]) ?></td>
<td class="p-md"><span class="<?= status_badge_class((string) $product["approval_status"]) ?>"><?= e(ucfirst((string) $product["approval_status"])) ?></span></td>
<td class="p-md"><span class="<?= visibility_badge_class((bool) $product["active"]) ?>"><?= ((bool) $product["active"]) ? "Active" : "Inactive" ?></span></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e(date("M j, Y", strtotime((string) $product["updated_at"]))) ?></td>
<td class="p-md">
<div class="flex gap-sm">
<a class="inline-flex items-center justify-center p-base text-primary hover:bg-primary-fixed rounded-lg" href="index.php?view=add&amp;edit=<?= e($product["product_slug"]) ?>" aria-label="Edit <?= e($product["name"]) ?>">
<span class="material-symbols-outlined">edit</span>
</a>
<form action="index.php" method="post" onsubmit="return confirm('Delete this product? This will remove it from your dashboard and the public storefront.');">
<input name="action" type="hidden" value="delete_product"/>
<input name="product_slug" type="hidden" value="<?= e($product["product_slug"]) ?>"/>
<button class="inline-flex items-center justify-center p-base text-on-error-container hover:bg-error-container rounded-lg" type="submit" aria-label="Delete <?= e($product["name"]) ?>">
<span class="material-symbols-outlined">delete</span>
</button>
</form>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php
}

function render_admin_product_table(array $products, string $returnView): void
{
    if (!$products): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No approved products found.</p>
</div>
<?php
        return;
    endif;
?>
<div class="overflow-x-auto bg-surface-container-lowest border border-outline-variant/30 rounded-lg shadow-sm">
<table class="w-full text-left">
<thead class="border-b border-outline-variant/30">
<tr>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Product</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Retailer</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Category</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Price</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Visibility</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Updated</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($products as $product): ?>
<tr class="border-b border-outline-variant/20 last:border-0">
<td class="p-md">
<a class="flex items-center gap-sm min-w-64 hover:text-primary transition-colors" href="/product.php?product=<?= e($product["product_slug"]) ?>">
<span class="w-12 h-16 bg-surface-container rounded overflow-hidden flex-shrink-0">
<?php if ($product["image_url"]): ?>
<img class="w-full h-full object-cover" alt="" src="<?= e($product["image_url"]) ?>"/>
<?php endif; ?>
</span>
<span>
<span class="block font-label-lg text-label-lg text-on-surface"><?= e($product["name"]) ?></span>
<span class="block font-body-sm text-body-sm text-on-surface-variant"><?= e($product["description"]) ?></span>
</span>
</a>
</td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e($product["business_name"] ?: $product["display_name"]) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e(category_label((string) $product["category"])) ?></td>
<td class="p-md font-label-md text-label-md text-primary">$<?= e(number_format((float) $product["price"], 2)) ?></td>
<td class="p-md"><span class="<?= visibility_badge_class((bool) $product["active"]) ?>"><?= ((bool) $product["active"]) ? "Active" : "Inactive" ?></span></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e(date("M j, Y", strtotime((string) $product["updated_at"]))) ?></td>
<td class="p-md">
<form action="index.php" method="post">
<input name="action" type="hidden" value="admin_delete_product"/>
<input name="return_view" type="hidden" value="<?= e($returnView) ?>"/>
<input name="product_slug" type="hidden" value="<?= e($product["product_slug"]) ?>"/>
<button class="inline-flex items-center justify-center p-base text-on-error-container hover:bg-error-container rounded-lg" type="submit" aria-label="Delete <?= e($product["name"]) ?>">
<span class="material-symbols-outlined">delete</span>
</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php
}

function render_retailer_directory(array $retailers): void
{
    if (!$retailers): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No retailer accounts found.</p>
</div>
<?php
        return;
    endif;
?>
<div class="overflow-x-auto bg-surface-container-lowest border border-outline-variant/30 rounded-lg shadow-sm">
<table class="w-full text-left">
<thead class="border-b border-outline-variant/30">
<tr>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Retailer</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Email</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Status</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Products</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Pending</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Approved</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Rejected</th>
<th class="p-md font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Joined</th>
</tr>
</thead>
<tbody>
<?php foreach ($retailers as $retailer): ?>
<tr class="border-b border-outline-variant/20 last:border-0">
<td class="p-md">
<p class="font-label-lg text-label-lg text-on-surface"><?= e($retailer["business_name"] ?: $retailer["display_name"]) ?></p>
<p class="font-body-sm text-body-sm text-on-surface-variant"><?= e($retailer["display_name"]) ?></p>
</td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e($retailer["email"]) ?></td>
<td class="p-md"><span class="<?= visibility_badge_class($retailer["status"] === "active") ?>"><?= e(ucfirst((string) $retailer["status"])) ?></span></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e((string) $retailer["product_count"]) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e((string) $retailer["pending_count"]) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e((string) $retailer["approved_count"]) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e((string) $retailer["rejected_count"]) ?></td>
<td class="p-md font-body-sm text-body-sm text-on-surface-variant"><?= e(date("M j, Y", strtotime((string) $retailer["created_at"]))) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php
}

function render_retailer_message_panel(array $messages): void
{
?>
<section class="space-y-md">
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Messages</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Send product or approval questions to the admin team.</p>
</div>
<form class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm space-y-sm" action="index.php" method="post">
<input name="action" type="hidden" value="send_message"/>
<label class="block font-label-md text-label-md text-on-surface" for="retailer-message">Message</label>
<textarea class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none min-h-24" id="retailer-message" maxlength="1000" name="message" required></textarea>
<button class="inline-flex items-center justify-center gap-sm bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">
<span class="material-symbols-outlined">send</span>
Send Message
</button>
</form>
<?php render_message_list($messages); ?>
</section>
<?php
}

function render_admin_message_panel(array $retailers, array $threads): void
{
?>
<section class="space-y-md">
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Retailer Messages</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Reply to retailer questions without leaving the portal.</p>
</div>
<?php if ($retailers): ?>
<form class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm space-y-sm" action="index.php" method="post">
<input name="action" type="hidden" value="send_message"/>
<div class="grid grid-cols-1 md:grid-cols-[240px_1fr] gap-sm">
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="admin-message-retailer">Retailer</label>
<select class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none" id="admin-message-retailer" name="retailer_id" required>
<?php foreach ($retailers as $retailer): ?>
<option value="<?= e((string) $retailer["id"]) ?>"><?= e($retailer["business_name"] ?: $retailer["display_name"]) ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<label class="block font-label-md text-label-md mb-xs text-on-surface" for="admin-message">Message</label>
<textarea class="w-full px-md py-sm border border-outline-variant rounded-lg focus:border-primary focus:ring-0 outline-none min-h-24" id="admin-message" maxlength="1000" name="message" required></textarea>
</div>
</div>
<button class="inline-flex items-center justify-center gap-sm bg-primary text-on-primary px-md py-sm rounded-lg font-label-lg text-label-lg hover:opacity-90 active:scale-95 transition-all" type="submit">
<span class="material-symbols-outlined">send</span>
Send Reply
</button>
</form>
<?php endif; ?>
<?php if (!$threads): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No retailer messages yet.</p>
</div>
<?php else: ?>
<div class="space-y-md">
<?php foreach ($threads as $thread): ?>
<article class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-md shadow-sm">
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-sm mb-md">
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface"><?= e($thread["business_name"] ?: $thread["display_name"]) ?></h3>
<p class="font-body-sm text-body-sm text-on-surface-variant"><?= e($thread["email"]) ?></p>
</div>
<span class="font-label-md text-label-md text-primary"><?= e((string) count($thread["messages"])) ?> messages</span>
</div>
<?php render_message_list($thread["messages"], false); ?>
</article>
<?php endforeach; ?>
</div>
<?php endif; ?>
</section>
<?php
}

function render_message_list(array $messages, bool $framed = true): void
{
    if (!$messages): ?>
<div class="bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-lg text-center">
<p class="font-body-md text-body-md text-on-surface-variant">No messages yet.</p>
</div>
<?php
        return;
    endif;
?>
<div class="<?= $framed ? "bg-surface-container-lowest border border-outline-variant/30 rounded-lg shadow-sm " : "border-t border-outline-variant/30 " ?>divide-y divide-outline-variant/30">
<?php foreach ($messages as $message): ?>
<div class="p-md">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-xs">
<p class="font-label-lg text-label-lg text-on-surface"><?= e($message["sender_role"] === "admin" ? "Admin" : ($message["sender_name"] ?: "Retailer")) ?></p>
<time class="font-body-sm text-body-sm text-on-surface-variant" datetime="<?= e((string) $message["created_at"]) ?>"><?= e(date("M j, Y g:i A", strtotime((string) $message["created_at"]))) ?></time>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-xs"><?= e($message["body"]) ?></p>
</div>
<?php endforeach; ?>
</div>
<?php
}

function handle_retailer_post(PDO $pdo): void
{
    $action = (string) ($_POST["action"] ?? "");

    switch ($action) {
        case "register":
            handle_retailer_register($pdo);
            break;
        case "login":
            handle_retailer_login($pdo);
            break;
        case "logout":
            unset($_SESSION["luxe_retailer_id"]);
            session_regenerate_id(true);
            set_retailer_flash("success", "Signed out of the retailer portal.");
            redirect_to_retailer("login");
            break;
        case "save_product":
            handle_retailer_save_product($pdo);
            break;
        case "delete_product":
            handle_retailer_delete_product($pdo);
            break;
        case "send_message":
            handle_retailer_message($pdo);
            break;
        case "admin_approve":
            handle_admin_approval($pdo, true);
            break;
        case "admin_reject":
            handle_admin_approval($pdo, false);
            break;
        case "admin_delete_product":
            handle_admin_delete_product($pdo);
            break;
        default:
            throw new DomainException("Unknown retailer action.");
    }
}

function handle_retailer_register(PDO $pdo): void
{
    $email = retailer_email($_POST["email"] ?? "");
    $displayName = retailer_text($_POST["display_name"] ?? "", 120);
    $businessName = retailer_text($_POST["business_name"] ?? "", 160);
    $password = (string) ($_POST["password"] ?? "");
    $confirmPassword = (string) ($_POST["confirm_password"] ?? "");

    if (!$displayName || !$businessName || !$email) {
        throw new DomainException("Enter your name, business name, and email.");
    }
    if (strlen($password) < 8) {
        throw new DomainException("Password must be at least 8 characters.");
    }
    if (!hash_equals($password, $confirmPassword)) {
        throw new DomainException("Password and confirmation do not match.");
    }

    $existing = $pdo->prepare("SELECT 1 FROM retailer_accounts WHERE email = :email");
    $existing->execute(["email" => $email]);
    if ($existing->fetchColumn()) {
        throw new DomainException("A retailer account already exists for that email.");
    }

    $stmt = $pdo->prepare(
        "INSERT INTO retailer_accounts (email, password_hash, display_name, business_name, role, status)
         VALUES (:email, :password_hash, :display_name, :business_name, 'retailer', 'active')
         RETURNING id"
    );
    $stmt->execute([
        "email" => $email,
        "password_hash" => password_hash($password, PASSWORD_DEFAULT),
        "display_name" => $displayName,
        "business_name" => $businessName,
    ]);

    $_SESSION["luxe_retailer_id"] = (int) $stmt->fetchColumn();
    session_regenerate_id(true);
    set_retailer_flash("success", "Retailer account created.");
    redirect_to_retailer("dashboard");
}

function handle_retailer_login(PDO $pdo): void
{
    $email = retailer_email($_POST["email"] ?? "");
    $password = (string) ($_POST["password"] ?? "");
    if (!$email || $password === "") {
        throw new DomainException("Enter a valid retailer email and password.");
    }

    $adminId = try_env_admin_login($pdo, $email, $password);
    if ($adminId) {
        $_SESSION["luxe_retailer_id"] = $adminId;
        session_regenerate_id(true);
        set_retailer_flash("success", "Signed in to the retailer portal.");
        redirect_to_retailer("dashboard");
    }

    $stmt = $pdo->prepare(
        "SELECT id, password_hash, role, status
         FROM retailer_accounts
         WHERE email = :email"
    );
    $stmt->execute(["email" => $email]);
    $account = $stmt->fetch();
    if (!$account
        || $account["status"] !== "active"
        || $account["role"] === "system"
        || !password_verify($password, (string) $account["password_hash"])
    ) {
        throw new DomainException("Invalid retailer login.");
    }

    $_SESSION["luxe_retailer_id"] = (int) $account["id"];
    session_regenerate_id(true);
    set_retailer_flash("success", "Signed in to the retailer portal.");
    redirect_to_retailer("dashboard");
}

function try_env_admin_login(PDO $pdo, string $email, string $password): ?int
{
    $adminEmail = retailer_email(getenv("RETAILER_ADMIN_EMAIL") ?: "");
    $adminPassword = (string) (getenv("RETAILER_ADMIN_PASSWORD") ?: "");
    if (!$adminEmail || $adminPassword === "" || $email !== $adminEmail || !hash_equals($adminPassword, $password)) {
        return null;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO retailer_accounts (email, password_hash, display_name, role, status)
         VALUES (:email, :password_hash, 'LUXE Admin', 'admin', 'active')
         ON CONFLICT (email) DO UPDATE SET
           password_hash = EXCLUDED.password_hash,
           display_name = EXCLUDED.display_name,
           role = 'admin',
           status = 'active',
           updated_at = now()
         RETURNING id"
    );
    $stmt->execute([
        "email" => $email,
        "password_hash" => password_hash($password, PASSWORD_DEFAULT),
    ]);

    return (int) $stmt->fetchColumn();
}

function handle_retailer_save_product(PDO $pdo): void
{
    $account = require_retailer_account($pdo);
    if ($account["role"] !== "retailer") {
        throw new DomainException("Only retailer accounts can submit products.");
    }

    $product = sanitize_retailer_product_input();
    $vendorId = (int) $account["id"];
    $slug = retailer_slug($_POST["product_slug"] ?? "");

    if ($slug) {
        // Ownership is checked in the WHERE clause so retailers cannot edit another seller's product.
        $stmt = $pdo->prepare(
            "UPDATE products
             SET name = :name,
                 description = :description,
                 category = :category,
                 segment = :segment,
                 price = :price,
                 stock_quantity = :stock_quantity,
                 image_url = :image_url,
                 default_color = :default_color,
                 available_colors = CAST(:available_colors AS jsonb),
                 available_sizes = CAST(:available_sizes AS jsonb),
                 approval_status = 'pending',
                 rejection_reason = '',
                 is_new_arrival = true,
                 active = :active,
                 archived_at = NULL
             WHERE product_slug = :product_slug
               AND vendor_id = :vendor_id
               AND archived_at IS NULL"
        );
        $stmt->execute($product + [
            "product_slug" => $slug,
            "vendor_id" => $vendorId,
        ]);
        if ($stmt->rowCount() === 0) {
            throw new DomainException("Product not found for this retailer account.");
        }

        set_retailer_flash("success", "Product resubmitted for admin approval.");
        redirect_to_retailer("dashboard");
    }

    $productSlug = unique_product_slug($pdo, retailer_slug($product["name"]), $vendorId);
    $stmt = $pdo->prepare(
        "INSERT INTO products
         (product_slug, name, description, category, segment, price, stock_quantity, image_url, default_color,
          available_colors, available_sizes, vendor_id, approval_status, rejection_reason,
          archived_at, is_new_arrival, popularity, active)
         VALUES
         (:product_slug, :name, :description, :category, :segment, :price, :stock_quantity, :image_url, :default_color,
          CAST(:available_colors AS jsonb), CAST(:available_sizes AS jsonb), :vendor_id,
          'pending', '', NULL, true, 0, :active)"
    );
    $stmt->execute($product + [
        "product_slug" => $productSlug,
        "vendor_id" => $vendorId,
    ]);

    set_retailer_flash("success", "Product submitted for admin approval.");
    redirect_to_retailer("dashboard");
}

function handle_retailer_delete_product(PDO $pdo): void
{
    $account = require_retailer_account($pdo);
    if ($account["role"] !== "retailer") {
        throw new DomainException("Only retailer accounts can delete products.");
    }

    $slug = retailer_slug($_POST["product_slug"] ?? "");
    if (!$slug) {
        throw new DomainException("Invalid product.");
    }

    // Ownership is checked in the WHERE clause so retailers cannot delete another seller's product.
    $stmt = $pdo->prepare(
        "UPDATE products
         SET active = false,
             archived_at = now()
         WHERE product_slug = :product_slug
           AND vendor_id = :vendor_id
           AND archived_at IS NULL"
    );
    $stmt->execute([
        "product_slug" => $slug,
        "vendor_id" => (int) $account["id"],
    ]);
    if ($stmt->rowCount() === 0) {
        throw new DomainException("Product not found for this retailer account.");
    }

    set_retailer_flash("success", "Product deleted.");
    redirect_to_retailer("dashboard");
}

function handle_retailer_message(PDO $pdo): void
{
    $account = require_retailer_account($pdo);
    if (!in_array($account["role"], ["retailer", "admin"], true)) {
        throw new DomainException("Retailer portal access is required.");
    }

    $body = retailer_text($_POST["message"] ?? "", 1000);
    if ($body === "") {
        throw new DomainException("Enter a message.");
    }

    if ($account["role"] === "admin") {
        $retailerId = retailer_int($_POST["retailer_id"] ?? 0, 1, 2147483647);
        if (!retailer_can_receive_admin_message($pdo, $retailerId)) {
            throw new DomainException("Choose a valid retailer.");
        }
    } else {
        $retailerId = (int) $account["id"];
    }

    $stmt = $pdo->prepare(
        "INSERT INTO retailer_messages (retailer_id, sender_id, sender_role, body)
         VALUES (:retailer_id, :sender_id, :sender_role, :body)"
    );
    $stmt->execute([
        "retailer_id" => $retailerId,
        "sender_id" => (int) $account["id"],
        "sender_role" => $account["role"],
        "body" => $body,
    ]);

    set_retailer_flash("success", "Message sent.");
    redirect_to_retailer("dashboard");
}

function handle_admin_approval(PDO $pdo, bool $approved): void
{
    $account = require_retailer_account($pdo);
    if ($account["role"] !== "admin") {
        throw new DomainException("Admin access is required.");
    }

    $slug = retailer_slug($_POST["product_slug"] ?? "");
    if (!$slug) {
        throw new DomainException("Invalid product.");
    }

    if ($approved) {
        $stmt = $pdo->prepare(
            "UPDATE products
             SET approval_status = 'approved',
                 rejection_reason = '',
                 active = true,
                 is_new_arrival = true,
                 archived_at = NULL
             WHERE product_slug = :product_slug
               AND approval_status = 'pending'
               AND archived_at IS NULL"
        );
        $stmt->execute(["product_slug" => $slug]);
        set_retailer_flash("success", "Product approved.");
    } else {
        $reason = retailer_text($_POST["rejection_reason"] ?? "", 300) ?: "Product was rejected by admin.";
        $stmt = $pdo->prepare(
            "UPDATE products
             SET approval_status = 'rejected',
                 rejection_reason = :rejection_reason,
                 active = false
             WHERE product_slug = :product_slug
               AND approval_status = 'pending'
               AND archived_at IS NULL"
        );
        $stmt->execute([
            "product_slug" => $slug,
            "rejection_reason" => $reason,
        ]);
        set_retailer_flash("success", "Product rejected.");
    }

    if ($stmt->rowCount() === 0) {
        throw new DomainException("Pending product not found.");
    }
    redirect_to_retailer("admin");
}

function handle_admin_delete_product(PDO $pdo): void
{
    $account = require_retailer_account($pdo);
    if ($account["role"] !== "admin") {
        throw new DomainException("Admin access is required.");
    }

    $slug = retailer_slug($_POST["product_slug"] ?? "");
    if (!$slug) {
        throw new DomainException("Invalid product.");
    }

    $stmt = $pdo->prepare(
        "UPDATE products
         SET active = false,
             archived_at = now()
         WHERE product_slug = :product_slug
           AND archived_at IS NULL"
    );
    $stmt->execute(["product_slug" => $slug]);
    if ($stmt->rowCount() === 0) {
        throw new DomainException("Product not found.");
    }

    set_retailer_flash("success", "Product deleted.");
    redirect_to_retailer(admin_return_view($_POST["return_view"] ?? "dashboard"));
}

function sanitize_retailer_product_input(): array
{
    $name = retailer_text($_POST["name"] ?? "", 180);
    $description = retailer_text($_POST["description"] ?? "", 1000);
    $category = retailer_slug($_POST["category"] ?? "");
    $segment = retailer_slug($_POST["segment"] ?? "");
    $price = retailer_money($_POST["price"] ?? 0);
    $stockQuantity = retailer_int($_POST["stock_quantity"] ?? 0, 0, 100000);
    $imageUrl = retailer_product_image_url();
    $colors = retailer_csv($_POST["available_colors"] ?? "");
    $sizes = retailer_csv($_POST["available_sizes"] ?? "");
    $active = (string) ($_POST["active"] ?? "1") === "1";

    if (!$name || !$description || !in_array($category, retailer_categories(), true) || !in_array($segment, ["men", "women", "accessories"], true)) {
        throw new DomainException("Enter valid product name, description, category, and segment.");
    }
    if ($price <= 0) {
        throw new DomainException("Enter a valid product price.");
    }
    if (!$imageUrl) {
        throw new DomainException("Enter a valid product image URL or upload a product image.");
    }
    if (!$colors || !$sizes) {
        throw new DomainException("Enter at least one color and one size.");
    }

    return [
        "name" => $name,
        "description" => $description,
        "category" => $category,
        "segment" => $segment,
        "price" => $price,
        "stock_quantity" => $stockQuantity,
        "image_url" => $imageUrl,
        "default_color" => $colors[0],
        "available_colors" => json_encode($colors),
        "available_sizes" => json_encode($sizes),
        "active" => $active ? "true" : "false",
    ];
}

function current_retailer_account(PDO $pdo): ?array
{
    $id = (int) ($_SESSION["luxe_retailer_id"] ?? 0);
    if ($id <= 0) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT id, email, display_name, business_name, role, status
         FROM retailer_accounts
         WHERE id = :id"
    );
    $stmt->execute(["id" => $id]);
    $account = $stmt->fetch();
    if (!$account || $account["status"] !== "active") {
        unset($_SESSION["luxe_retailer_id"]);
        return null;
    }

    return $account;
}

function require_retailer_account(PDO $pdo): array
{
    $account = current_retailer_account($pdo);
    if (!$account) {
        throw new DomainException("Retailer login is required.");
    }
    return $account;
}

function retailer_stats(PDO $pdo, array $account): array
{
    if ($account["role"] === "admin") {
        $pending = (int) $pdo->query("SELECT count(*) FROM products WHERE approval_status = 'pending' AND archived_at IS NULL")->fetchColumn();
        $approved = (int) $pdo->query("SELECT count(*) FROM products WHERE approval_status = 'approved' AND archived_at IS NULL")->fetchColumn();
        $retailers = (int) $pdo->query("SELECT count(*) FROM retailer_accounts WHERE role = 'retailer'")->fetchColumn();
        return [
            "Pending" => $pending,
            "Approved" => $approved,
            "Retailers" => $retailers,
            "Role" => "Admin",
        ];
    }

    $vendorId = (int) $account["id"];
    $stmt = $pdo->prepare(
        "SELECT approval_status, count(*) AS count
         FROM products
         WHERE vendor_id = :vendor_id
           AND archived_at IS NULL
         GROUP BY approval_status"
    );
    $stmt->execute(["vendor_id" => $vendorId]);
    $counts = ["pending" => 0, "approved" => 0, "rejected" => 0];
    foreach ($stmt->fetchAll() as $row) {
        $counts[(string) $row["approval_status"]] = (int) $row["count"];
    }
    $activeStmt = $pdo->prepare(
        "SELECT count(*)
         FROM products
         WHERE vendor_id = :vendor_id
           AND active = true
           AND archived_at IS NULL"
    );
    $activeStmt->execute(["vendor_id" => $vendorId]);

    return [
        "Active" => (int) $activeStmt->fetchColumn(),
        "Pending" => $counts["pending"],
        "Approved" => $counts["approved"],
        "Rejected" => $counts["rejected"],
    ];
}

function fetch_retailer_products(PDO $pdo, int $vendorId): array
{
    $stmt = $pdo->prepare(
        "SELECT product_slug, name, description, category, segment, price, stock_quantity, image_url, default_color,
                available_colors, available_sizes, approval_status, rejection_reason, active, updated_at
         FROM products
         WHERE vendor_id = :vendor_id
           AND archived_at IS NULL
         ORDER BY updated_at DESC"
    );
    $stmt->execute(["vendor_id" => $vendorId]);
    return array_map("retailer_product_row", $stmt->fetchAll());
}

function fetch_owned_product(PDO $pdo, int $vendorId, string $slug): ?array
{
    $stmt = $pdo->prepare(
        "SELECT product_slug, name, description, category, segment, price, stock_quantity, image_url, default_color,
                available_colors, available_sizes, approval_status, rejection_reason, active, updated_at
         FROM products
         WHERE product_slug = :product_slug
           AND vendor_id = :vendor_id
           AND archived_at IS NULL"
    );
    $stmt->execute(["product_slug" => $slug, "vendor_id" => $vendorId]);
    $row = $stmt->fetch();
    return $row ? retailer_product_row($row) : null;
}

function fetch_pending_products(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT p.product_slug, p.name, p.description, p.category, p.segment, p.price, p.stock_quantity, p.image_url,
                p.active, p.updated_at, r.display_name, r.business_name
         FROM products p
         JOIN retailer_accounts r ON r.id = p.vendor_id
         WHERE p.approval_status = 'pending'
           AND p.archived_at IS NULL
         ORDER BY p.updated_at ASC"
    );
    return $stmt->fetchAll();
}

function fetch_approved_products(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT p.product_slug, p.name, p.description, p.category, p.segment, p.price, p.stock_quantity, p.image_url,
                p.active, p.updated_at, r.display_name, r.business_name
         FROM products p
         JOIN retailer_accounts r ON r.id = p.vendor_id
         WHERE p.approval_status = 'approved'
           AND p.archived_at IS NULL
         ORDER BY p.updated_at DESC"
    );
    return $stmt->fetchAll();
}

function fetch_retailer_directory(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT r.id, r.email, r.display_name, r.business_name, r.status, r.created_at,
                count(p.product_slug) FILTER (WHERE p.archived_at IS NULL) AS product_count,
                count(p.product_slug) FILTER (WHERE p.approval_status = 'pending' AND p.archived_at IS NULL) AS pending_count,
                count(p.product_slug) FILTER (WHERE p.approval_status = 'approved' AND p.archived_at IS NULL) AS approved_count,
                count(p.product_slug) FILTER (WHERE p.approval_status = 'rejected' AND p.archived_at IS NULL) AS rejected_count
         FROM retailer_accounts r
         LEFT JOIN products p ON p.vendor_id = r.id
         WHERE r.role = 'retailer'
         GROUP BY r.id, r.email, r.display_name, r.business_name, r.status, r.created_at
         ORDER BY r.created_at DESC"
    );
    return $stmt->fetchAll();
}

function fetch_retailer_messages(PDO $pdo, int $retailerId): array
{
    $stmt = $pdo->prepare(
        "SELECT m.body, m.sender_role, m.created_at, a.display_name AS sender_name
         FROM retailer_messages m
         JOIN retailer_accounts a ON a.id = m.sender_id
         WHERE m.retailer_id = :retailer_id
         ORDER BY m.created_at DESC
         LIMIT 20"
    );
    $stmt->execute(["retailer_id" => $retailerId]);
    return array_reverse($stmt->fetchAll());
}

function fetch_message_retailers(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT id, email, display_name, business_name
         FROM retailer_accounts
         WHERE role = 'retailer'
           AND status = 'active'
         ORDER BY business_name ASC, display_name ASC"
    );
    return $stmt->fetchAll();
}

function fetch_admin_message_threads(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT m.retailer_id, m.body, m.sender_role, m.created_at, sender.display_name AS sender_name,
                r.email, r.display_name, r.business_name
         FROM retailer_messages m
         JOIN retailer_accounts sender ON sender.id = m.sender_id
         JOIN retailer_accounts r ON r.id = m.retailer_id
         WHERE r.role = 'retailer'
           AND r.status = 'active'
         ORDER BY m.created_at DESC
         LIMIT 80"
    );

    $threads = [];
    foreach ($stmt->fetchAll() as $row) {
        $retailerId = (int) $row["retailer_id"];
        if (!isset($threads[$retailerId])) {
            $threads[$retailerId] = [
                "email" => $row["email"],
                "display_name" => $row["display_name"],
                "business_name" => $row["business_name"],
                "messages" => [],
            ];
        }

        $threads[$retailerId]["messages"][] = [
            "body" => $row["body"],
            "sender_role" => $row["sender_role"],
            "sender_name" => $row["sender_name"],
            "created_at" => $row["created_at"],
        ];
    }

    foreach ($threads as &$thread) {
        $thread["messages"] = array_reverse(array_slice($thread["messages"], 0, 8));
    }
    unset($thread);

    return array_values($threads);
}

function retailer_can_receive_admin_message(PDO $pdo, int $retailerId): bool
{
    $stmt = $pdo->prepare(
        "SELECT 1
         FROM retailer_accounts
         WHERE id = :id
           AND role = 'retailer'
           AND status = 'active'"
    );
    $stmt->execute(["id" => $retailerId]);
    return (bool) $stmt->fetchColumn();
}

function retailer_product_row(array $row): array
{
    $row["available_colors"] = json_decode((string) $row["available_colors"], true) ?: [];
    $row["available_sizes"] = json_decode((string) $row["available_sizes"], true) ?: [];
    $row["active"] = (bool) $row["active"];
    return $row;
}

function unique_product_slug(PDO $pdo, string $baseSlug, int $vendorId): string
{
    $baseSlug = $baseSlug ?: "product";
    $suffixes = ["", "-" . $vendorId];
    for ($i = 2; $i <= 99; $i++) {
        $suffixes[] = "-" . $vendorId . "-" . $i;
    }

    $stmt = $pdo->prepare("SELECT 1 FROM products WHERE product_slug = :product_slug");
    foreach ($suffixes as $suffix) {
        $slug = substr($baseSlug . $suffix, 0, 120);
        $stmt->execute(["product_slug" => $slug]);
        if (!$stmt->fetchColumn()) {
            return $slug;
        }
    }

    return substr($baseSlug, 0, 80) . "-" . bin2hex(random_bytes(4));
}

function retailer_categories(): array
{
    return ["outerwear", "knitwear", "shirts-tops", "trousers", "footwear", "dresses", "bags", "accessories"];
}

function category_label(string $category): string
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
    ][$category] ?? ucfirst($category);
}

function status_badge_class(string $status): string
{
    $base = "inline-flex px-sm py-xs rounded-full font-label-md text-label-md ";
    return match ($status) {
        "approved" => $base . "bg-primary-fixed text-on-primary-fixed",
        "rejected" => $base . "bg-error-container text-on-error-container",
        default => $base . "bg-surface-container-high text-on-surface-variant",
    };
}

function visibility_badge_class(bool $active): string
{
    $base = "inline-flex px-sm py-xs rounded-full font-label-md text-label-md ";
    return $active
        ? $base . "bg-primary-fixed text-on-primary-fixed"
        : $base . "bg-surface-container-high text-on-surface-variant";
}

function retailer_email(mixed $value): string
{
    $email = strtolower(trim((string) $value));
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : "";
}

function retailer_text(mixed $value, int $max): string
{
    $text = trim(preg_replace("/\s+/", " ", (string) $value));
    return substr($text, 0, $max);
}

function retailer_slug(mixed $value): string
{
    return trim(preg_replace("/[^a-z0-9-]+/", "-", strtolower((string) $value)), "-");
}

function admin_return_view(mixed $value): string
{
    $view = retailer_slug($value);
    return in_array($view, ["dashboard", "admin", "approved", "retailers"], true) ? $view : "dashboard";
}

function retailer_url(mixed $value): string
{
    $url = trim((string) $value);
    return filter_var($url, FILTER_VALIDATE_URL) ? substr($url, 0, 1200) : "";
}

function retailer_product_image_url(): string
{
    $uploaded = retailer_uploaded_product_image($_FILES["product_image"] ?? null);
    if ($uploaded !== "") {
        return $uploaded;
    }

    return retailer_url($_POST["image_url"] ?? "");
}

function retailer_uploaded_product_image(mixed $file): string
{
    if (!is_array($file) || ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return "";
    }
    if (($file["error"] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new DomainException("Product image upload failed.");
    }
    if ((int) ($file["size"] ?? 0) > 5 * 1024 * 1024) {
        throw new DomainException("Product image must be 5 MB or smaller.");
    }

    $tmpName = (string) ($file["tmp_name"] ?? "");
    if ($tmpName === "" || !is_uploaded_file($tmpName)) {
        throw new DomainException("Product image upload is invalid.");
    }

    $info = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $info ? (string) finfo_file($info, $tmpName) : "";
    if ($info) {
        finfo_close($info);
    }

    $extensions = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp",
        "image/gif" => "gif",
    ];
    if (!isset($extensions[$mime])) {
        throw new DomainException("Product image must be a JPG, PNG, WebP, or GIF file.");
    }

    $uploadDir = dirname(__DIR__) . "/assets/uploads/products";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        throw new DomainException("Product image upload directory is not writable.");
    }

    $filename = bin2hex(random_bytes(16)) . "." . $extensions[$mime];
    $target = $uploadDir . "/" . $filename;
    if (!move_uploaded_file($tmpName, $target)) {
        throw new DomainException("Could not save the uploaded product image.");
    }

    return "/assets/uploads/products/" . $filename;
}

function retailer_money(mixed $value): float
{
    $number = is_numeric($value) ? (float) $value : (float) preg_replace("/[^0-9.]/", "", (string) $value);
    return round(max(0, $number), 2);
}

function retailer_int(mixed $value, int $min, int $max): int
{
    $number = is_numeric($value) ? (int) $value : (int) preg_replace("/[^0-9]/", "", (string) $value);
    return min($max, max($min, $number));
}

function retailer_csv(mixed $value): array
{
    $parts = array_map(
        static fn ($item): string => retailer_text($item, 80),
        explode(",", (string) $value)
    );
    $parts = array_values(array_filter($parts, static fn ($item): bool => $item !== ""));
    return array_values(array_unique(array_slice($parts, 0, 20)));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function set_retailer_flash(string $type, string $message): void
{
    $_SESSION["retailer_flash"] = ["type" => $type, "message" => $message];
}

function take_retailer_flash(): ?array
{
    $flash = $_SESSION["retailer_flash"] ?? null;
    unset($_SESSION["retailer_flash"]);
    return is_array($flash) ? $flash : null;
}

function redirect_to_retailer(string $view = "dashboard"): void
{
    header("Location: index.php?view=" . rawurlencode($view));
    exit;
}

function redirect_back_to_retailer(): void
{
    $target = "index.php";
    $referer = (string) ($_SERVER["HTTP_REFERER"] ?? "");
    if ($referer !== "") {
        $parts = parse_url($referer);
        $path = (string) ($parts["path"] ?? "");
        if (in_array(basename($path), ["index.php", "login.php", "signup.php", "dashboard.php"], true)) {
            $query = isset($parts["query"]) ? "?" . $parts["query"] : "";
            $target = basename($path) . $query;
        }
    }
    header("Location: " . $target);
    exit;
}
