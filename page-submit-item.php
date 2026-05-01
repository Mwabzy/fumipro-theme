<?php
/**
 * Template Name: Submit Product / Service
 *
 * Standalone entry form — no WP header/footer chrome.
 * Requires the user to be logged in with edit_posts capability.
 */

/* ── Bootstrap WordPress (runs before any HTML) ─────────────────────────── */
if (!function_exists('add_action')) {
    // Direct file-load fallback — should not happen with a page template
    define('ABSPATH', dirname(__FILE__, 4) . '/');
    require ABSPATH . 'wp-load.php';
}

/* ── Auth guard ─────────────────────────────────────────────────────────── */
if (!is_user_logged_in() || !current_user_can('edit_posts')) {
    auth_redirect(); // sends to WP login, then back here after login
    exit;
}

/* ══════════════════════════════════════════════════════════════════════════
   FORM PROCESSING
══════════════════════════════════════════════════════════════════════════ */
$sf_message = '';
$sf_error   = '';

// Determine active tab
$sf_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'product';
if (!in_array($sf_tab, ['product', 'service'], true)) $sf_tab = 'product';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['sf_nonce']) &&
    wp_verify_nonce($_POST['sf_nonce'], 'sf_submit_item')
) {
    $sf_tab   = sanitize_key($_POST['sf_item_type'] ?? 'product');
    if (!in_array($sf_tab, ['product', 'service'], true)) $sf_tab = 'product';

    $sf_title = sanitize_text_field(wp_unslash($_POST['sf_title'] ?? ''));

    if (empty($sf_title)) {
        $sf_error = 'Name / Title is required.';
    } else {
        $post_type = $sf_tab === 'service' ? 'fumitech_service' : 'fumitech_product';

        $post_id = wp_insert_post([
            'post_title'   => $sf_title,
            'post_content' => wp_kses_post(wp_unslash($_POST['sf_content'] ?? '')),
            'post_excerpt' => sanitize_text_field(wp_unslash($_POST['sf_excerpt'] ?? '')),
            'post_type'    => $post_type,
            'post_status'  => 'draft',
        ]);

        if (is_wp_error($post_id)) {
            $sf_error = 'Could not save: ' . $post_id->get_error_message();
        } else {
            // Featured image
            $thumb_id = absint($_POST['sf_thumbnail_id'] ?? 0);
            if ($thumb_id) set_post_thumbnail($post_id, $thumb_id);

            if ($sf_tab === 'product') {
                update_post_meta($post_id, '_fumitech_price',      sanitize_text_field(wp_unslash($_POST['sf_price']      ?? '')));
                update_post_meta($post_id, '_fumitech_badge',      sanitize_text_field(wp_unslash($_POST['sf_badge']      ?? '')));
                update_post_meta($post_id, '_fumitech_wa_message', sanitize_text_field(wp_unslash($_POST['sf_wa_message'] ?? '')));
                update_post_meta($post_id, '_fumitech_featured',   isset($_POST['sf_featured']) ? '1' : '0');

                $child_id  = absint($_POST['sf_cat_child']  ?? 0);
                $parent_id = absint($_POST['sf_cat_parent'] ?? 0);
                if ($child_id && $parent_id) {
                    wp_set_object_terms($post_id, [$child_id, $parent_id], 'product_category');
                } elseif ($child_id) {
                    $t = get_term($child_id, 'product_category');
                    wp_set_object_terms(
                        $post_id,
                        ($t && !is_wp_error($t) && $t->parent) ? [$child_id, $t->parent] : [$child_id],
                        'product_category'
                    );
                } elseif ($parent_id) {
                    wp_set_object_terms($post_id, [$parent_id], 'product_category');
                }
            } else {
                update_post_meta($post_id, '_fumitech_price',      sanitize_text_field(wp_unslash($_POST['sf_price']      ?? '')));
                update_post_meta($post_id, '_fumitech_duration',   sanitize_text_field(wp_unslash($_POST['sf_duration']   ?? '')));
                update_post_meta($post_id, '_fumitech_icon',       sanitize_text_field(wp_unslash($_POST['sf_icon']       ?? '')));
                update_post_meta($post_id, '_fumitech_wa_message', sanitize_text_field(wp_unslash($_POST['sf_wa_message'] ?? '')));

                $raw_lines = explode("\n", wp_unslash($_POST['sf_sub_items'] ?? ''));
                update_post_meta(
                    $post_id,
                    '_fumitech_sub_items',
                    implode("\n", array_filter(array_map('sanitize_text_field', $raw_lines)))
                );

                $cat_id = absint($_POST['sf_svc_category'] ?? 0);
                if ($cat_id) wp_set_object_terms($post_id, [$cat_id], 'service_category');
            }

            $edit_url   = get_edit_post_link($post_id, 'raw');
            $sf_message = $edit_url; // pass URL to JS for the success screen
        }
    }
}

/* ══════════════════════════════════════════════════════════════════════════
   TAXONOMY DATA
══════════════════════════════════════════════════════════════════════════ */
$sf_prod_parents   = [];
$sf_prod_child_map = [];
$_all_prod         = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false, 'orderby' => 'name']);
if (!is_wp_error($_all_prod)) {
    foreach ($_all_prod as $_t) {
        if ($_t->parent == 0) $sf_prod_parents[]                   = $_t;
        else                  $sf_prod_child_map[$_t->parent][]    = ['id' => $_t->term_id, 'name' => $_t->name];
    }
}
$sf_svc_cats = get_terms(['taxonomy' => 'service_category', 'hide_empty' => false]);

/* ══════════════════════════════════════════════════════════════════════════
   HTML OUTPUT — completely standalone (no get_header / get_footer)
══════════════════════════════════════════════════════════════════════════ */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add <?php echo $sf_tab === 'service' ? 'Service' : 'Product'; ?> — Fumitech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php
    // Load WP media uploader assets (needed for the image picker)
    wp_enqueue_media();
    wp_print_scripts('media-editor');
    wp_print_scripts('media-views');
    wp_print_styles('media-views');
    ?>
    <style>
    /* ── Reset & base ─────────────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { -webkit-font-smoothing: antialiased; }
    body {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background: #f0f9ff;
        color: #0f172a;
        min-height: 100vh;
    }
    a { color: #0ea5e9; text-decoration: none; }
    a:hover { text-decoration: underline; }
    ul { list-style: none; }
    button { font-family: inherit; cursor: pointer; border: none; background: none; }

    /* ── Page shell ───────────────────────────────────────────────────────── */
    .sf-shell {
        min-height: 100vh;
        display: grid;
        grid-template-rows: auto 1fr auto;
    }

    /* ── Top bar ──────────────────────────────────────────────────────────── */
    .sf-topbar {
        background: #0369a1;
        padding: 0 24px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .sf-topbar__brand {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.02em;
        text-decoration: none;
    }
    .sf-topbar__brand-dot {
        width: 28px;
        height: 28px;
        background: #0ea5e9;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    .sf-topbar__user {
        font-size: 13px;
        color: #bae6fd;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sf-topbar__user a { color: #bae6fd; }
    .sf-topbar__user a:hover { color: #fff; text-decoration: none; }

    /* ── Main content area ────────────────────────────────────────────────── */
    .sf-main {
        padding: 32px 24px 60px;
        max-width: 860px;
        width: 100%;
        margin: 0 auto;
    }

    /* ── Page heading ─────────────────────────────────────────────────────── */
    .sf-page-head {
        margin-bottom: 28px;
    }
    .sf-page-head h1 {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.03em;
    }
    .sf-page-head p {
        font-size: 14px;
        color: #64748b;
        margin-top: 4px;
    }

    /* ── Success screen ───────────────────────────────────────────────────── */
    .sf-success {
        display: none;
        text-align: center;
        padding: 60px 24px;
    }
    .sf-success__icon {
        width: 68px;
        height: 68px;
        background: #dcfce7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 30px;
    }
    .sf-success h2 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .sf-success p {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }
    .sf-success__actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* ── Error banner ─────────────────────────────────────────────────────── */
    .sf-error-banner {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* ── Tabs ─────────────────────────────────────────────────────────────── */
    .sf-tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
    }
    .sf-tab {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 10px 22px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border-radius: 8px 8px 0 0;
        border: 1.5px solid transparent;
        border-bottom: none;
        text-decoration: none;
        margin-bottom: -2px;
        transition: color 0.15s, background 0.15s;
    }
    .sf-tab:hover { color: #0ea5e9; background: #f0f9ff; text-decoration: none; }
    .sf-tab.is-active {
        color: #0ea5e9;
        background: #fff;
        border-color: #e2e8f0;
        border-bottom-color: #fff;
    }
    .sf-tab__emoji { font-size: 16px; }

    /* ── Cards ────────────────────────────────────────────────────────────── */
    .sf-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px 26px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .sf-card__title {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* ── Grid ─────────────────────────────────────────────────────────────── */
    .sf-grid   { display: grid; gap: 18px; }
    .sf-col-1  { grid-template-columns: 1fr; }
    .sf-col-2  { grid-template-columns: 1fr 1fr; }
    .sf-col-3  { grid-template-columns: 1fr 1fr 1fr; }
    .sf-col-full { grid-column: 1 / -1; }

    /* ── Fields ───────────────────────────────────────────────────────────── */
    .sf-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }
    .sf-label .req { color: #ef4444; margin-left: 2px; }
    .sf-input,
    .sf-select,
    .sf-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
        color: #0f172a;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .sf-input:focus,
    .sf-select:focus,
    .sf-textarea:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14,165,233,.12);
    }
    .sf-input::placeholder,
    .sf-textarea::placeholder { color: #94a3b8; }
    .sf-textarea { resize: vertical; }
    .sf-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 5px;
        line-height: 1.45;
    }
    .sf-mt { margin-top: 6px; }

    /* ── Checkbox ─────────────────────────────────────────────────────────── */
    .sf-check-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 4px;
    }
    .sf-check-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        accent-color: #0ea5e9;
        cursor: pointer;
        flex-shrink: 0;
    }
    .sf-check-row label {
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        line-height: 1.4;
    }

    /* ── Image picker ─────────────────────────────────────────────────────── */
    .sf-img-row {
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }
    .sf-img-preview {
        width: 156px;
        height: 118px;
        flex-shrink: 0;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        transition: border-color 0.15s;
    }
    .sf-img-preview:hover { border-color: #0ea5e9; }
    .sf-img-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .sf-img-empty {
        font-size: 12px;
        color: #94a3b8;
        text-align: center;
        padding: 16px;
        line-height: 1.5;
    }
    .sf-img-controls {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 4px;
    }

    /* ── Buttons ──────────────────────────────────────────────────────────── */
    .sf-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        border: 1.5px solid transparent;
    }
    .sf-btn--primary {
        background: #0ea5e9;
        color: #fff;
        border-color: #0ea5e9;
    }
    .sf-btn--primary:hover { background: #0284c7; border-color: #0284c7; color: #fff; text-decoration: none; }
    .sf-btn--outline {
        background: #fff;
        color: #334155;
        border-color: #e2e8f0;
    }
    .sf-btn--outline:hover { border-color: #0ea5e9; color: #0ea5e9; text-decoration: none; }
    .sf-btn--ghost {
        background: transparent;
        color: #ef4444;
        border-color: #fca5a5;
        padding: 8px 14px;
        font-size: 13px;
    }
    .sf-btn--ghost:hover { background: #fef2f2; }
    .sf-btn--img { padding: 9px 16px; font-size: 13px; background: #f8fafc; border-color: #e2e8f0; color: #334155; }
    .sf-btn--img:hover { border-color: #0ea5e9; color: #0ea5e9; }
    .sf-btn--lg { padding: 13px 32px; font-size: 15px; }

    /* ── Form footer ──────────────────────────────────────────────────────── */
    .sf-form-footer {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 8px 0 32px;
        flex-wrap: wrap;
    }

    /* ── Bottom bar ───────────────────────────────────────────────────────── */
    .sf-bottombar {
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        color: #94a3b8;
    }

    /* ── Responsive ───────────────────────────────────────────────────────── */
    @media (max-width: 700px) {
        .sf-main  { padding: 20px 16px 48px; }
        .sf-col-2, .sf-col-3 { grid-template-columns: 1fr; }
        .sf-img-row { flex-direction: column; }
        .sf-tabs { gap: 0; }
        .sf-tab  { padding: 10px 14px; }
        .sf-form-footer { flex-direction: column; align-items: flex-start; }
        .sf-success__actions { flex-direction: column; align-items: center; }
    }
    </style>
</head>
<body>
<div class="sf-shell">

    <!-- ── Top bar ── -->
    <header class="sf-topbar">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="sf-topbar__brand">
            <span class="sf-topbar__brand-dot">F</span>
            Fumitech
        </a>
        <div class="sf-topbar__user">
            <?php $sf_user = wp_get_current_user(); ?>
            <span><?php echo esc_html($sf_user->display_name); ?></span>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=fumitech_product')); ?>">Products</a>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=fumitech_service')); ?>">Services</a>
            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Sign out</a>
        </div>
    </header>

    <!-- ── Main ── -->
    <main class="sf-main">

        <div class="sf-page-head">
            <h1>Add a New Item</h1>
            <p>Fill in the details below and click <strong>Save as Draft</strong> — you can review and publish from the admin.</p>
        </div>

        <?php /* ── Success screen (shown via JS after save) ── */ ?>
        <div class="sf-success" id="sf-success">
            <div class="sf-success__icon">✓</div>
            <h2 id="sf-success-title">Saved successfully!</h2>
            <p id="sf-success-msg">Your item has been saved as a Draft.</p>
            <div class="sf-success__actions">
                <a href="#" id="sf-edit-link" class="sf-btn sf-btn--primary">Review &amp; Publish →</a>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="sf-btn sf-btn--outline">Add Another</a>
            </div>
        </div>

        <?php /* ── Main form wrapper (hidden on success) ── */ ?>
        <div id="sf-form-area">

            <?php if ($sf_error) : ?>
                <div class="sf-error-banner">
                    <span>⚠</span> <?php echo esc_html($sf_error); ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <nav class="sf-tabs">
                <a href="<?php echo esc_url(add_query_arg('tab', 'product', get_permalink())); ?>"
                   class="sf-tab <?php echo $sf_tab === 'product' ? 'is-active' : ''; ?>">
                    <span class="sf-tab__emoji">📦</span> Product
                </a>
                <a href="<?php echo esc_url(add_query_arg('tab', 'service', get_permalink())); ?>"
                   class="sf-tab <?php echo $sf_tab === 'service' ? 'is-active' : ''; ?>">
                    <span class="sf-tab__emoji">🛠</span> Service
                </a>
            </nav>

            <form method="post" action="" id="sf-form">
                <?php wp_nonce_field('sf_submit_item', 'sf_nonce'); ?>
                <input type="hidden" name="sf_item_type" value="<?php echo esc_attr($sf_tab); ?>">

                <!-- ── Basic info ── -->
                <div class="sf-card">
                    <p class="sf-card__title">Basic Info</p>
                    <div class="sf-grid sf-col-1">

                        <div>
                            <label class="sf-label" for="sf_title">
                                <?php echo $sf_tab === 'service' ? 'Service Name' : 'Product Name'; ?>
                                <span class="req">*</span>
                            </label>
                            <input class="sf-input" type="text" id="sf_title" name="sf_title" required
                                   placeholder="Enter <?php echo $sf_tab === 'service' ? 'service' : 'product'; ?> name">
                        </div>

                        <div>
                            <label class="sf-label" for="sf_excerpt">Short Description</label>
                            <textarea class="sf-textarea" id="sf_excerpt" name="sf_excerpt" rows="2"
                                      placeholder="One-line summary shown on listing cards"></textarea>
                        </div>

                        <div>
                            <label class="sf-label" for="sf_content">Full Description</label>
                            <textarea class="sf-textarea" id="sf_content" name="sf_content" rows="6"
                                      placeholder="Detailed description shown on the item's own page"></textarea>
                        </div>

                    </div>
                </div>

                <!-- ── Featured image ── -->
                <div class="sf-card">
                    <p class="sf-card__title">Featured Image</p>
                    <div class="sf-img-row">
                        <div class="sf-img-preview" id="sf-img-preview">
                            <span class="sf-img-empty">📷<br>No image<br>selected</span>
                        </div>
                        <div class="sf-img-controls">
                            <button type="button" class="sf-btn sf-btn--img" id="sf-choose-img">Choose Image</button>
                            <button type="button" class="sf-btn sf-btn--ghost" id="sf-remove-img" style="display:none">✕ Remove</button>
                            <input type="hidden" name="sf_thumbnail_id" id="sf_thumbnail_id" value="">
                            <p class="sf-hint">Recommended: 800 × 600 px JPG or PNG.</p>
                        </div>
                    </div>
                </div>

                <?php if ($sf_tab === 'product') : ?>

                <!-- ══ PRODUCT — Category ══ -->
                <div class="sf-card">
                    <p class="sf-card__title">Category</p>
                    <div class="sf-grid sf-col-2">

                        <div>
                            <label class="sf-label" for="sf_cat_parent">Parent Category</label>
                            <select class="sf-select" id="sf_cat_parent" name="sf_cat_parent">
                                <option value="">— Select category —</option>
                                <?php foreach ($sf_prod_parents as $_pt) : ?>
                                    <option value="<?php echo esc_attr($_pt->term_id); ?>">
                                        <?php echo esc_html($_pt->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($sf_prod_parents)) : ?>
                                <p class="sf-hint">
                                    No categories yet —
                                    <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=product_category&post_type=fumitech_product')); ?>" target="_blank">add categories in admin →</a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div id="sf-subcat-col" style="display:none">
                            <label class="sf-label" for="sf_cat_child">Sub-category</label>
                            <select class="sf-select" id="sf_cat_child" name="sf_cat_child">
                                <option value="">— None (parent only) —</option>
                            </select>
                            <p class="sf-hint">Optional — more specific grouping.</p>
                        </div>

                    </div>
                </div>

                <!-- ══ PRODUCT — Details ══ -->
                <div class="sf-card">
                    <p class="sf-card__title">Product Details</p>
                    <div class="sf-grid sf-col-3">

                        <div>
                            <label class="sf-label" for="sf_price">Price (Ksh)</label>
                            <input class="sf-input" type="text" id="sf_price" name="sf_price"
                                   placeholder="e.g. 1,500">
                        </div>

                        <div>
                            <label class="sf-label" for="sf_badge">Badge Label</label>
                            <input class="sf-input" type="text" id="sf_badge" name="sf_badge"
                                   placeholder="e.g. Best Seller · New · Sale">
                            <p class="sf-hint">Leave blank to hide the badge.</p>
                        </div>

                        <div>
                            <label class="sf-label" for="sf_wa_message">WhatsApp Order Message</label>
                            <input class="sf-input" type="text" id="sf_wa_message" name="sf_wa_message"
                                   placeholder="Hi, I'd like to order…">
                            <p class="sf-hint">Leave blank to auto-generate from title.</p>
                        </div>

                    </div>

                    <div class="sf-check-row sf-mt">
                        <input type="checkbox" id="sf_featured" name="sf_featured" value="1">
                        <label for="sf_featured">
                            Feature on Homepage
                            <span class="sf-hint" style="font-weight:400;display:block">Shows this product in the homepage showcase grid (max 8 slots).</span>
                        </label>
                    </div>
                </div>

                <?php else : ?>

                <!-- ══ SERVICE — Category & Details ══ -->
                <div class="sf-card">
                    <p class="sf-card__title">Service Category &amp; Details</p>
                    <div class="sf-grid sf-col-2">

                        <div>
                            <label class="sf-label" for="sf_svc_category">Service Category</label>
                            <select class="sf-select" id="sf_svc_category" name="sf_svc_category">
                                <option value="">— Select category —</option>
                                <?php if (!is_wp_error($sf_svc_cats)) foreach ($sf_svc_cats as $_sc) : ?>
                                    <option value="<?php echo esc_attr($_sc->term_id); ?>">
                                        <?php echo esc_html($_sc->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="sf-hint">Groups services on the Services page (e.g. Pest Control, Consultancy).</p>
                        </div>

                        <div>
                            <label class="sf-label" for="sf_icon">Emoji Icon</label>
                            <input class="sf-input" type="text" id="sf_icon" name="sf_icon"
                                   placeholder="e.g. 🐜">
                            <p class="sf-hint">Displayed on the service card.</p>
                        </div>

                        <div>
                            <label class="sf-label" for="sf_price">Price / Starting From</label>
                            <input class="sf-input" type="text" id="sf_price" name="sf_price"
                                   placeholder="e.g. Ksh 5,000">
                        </div>

                        <div>
                            <label class="sf-label" for="sf_duration">Duration</label>
                            <input class="sf-input" type="text" id="sf_duration" name="sf_duration"
                                   placeholder="e.g. 2–4 hours">
                        </div>

                    </div>

                    <div class="sf-mt">
                        <label class="sf-label" for="sf_sub_items">Sub-services / Sub-types</label>
                        <textarea class="sf-textarea" id="sf_sub_items" name="sf_sub_items" rows="5"
                                  placeholder="One item per line, e.g.&#10;Conventional pesticide registration&#10;Bio-pesticides registration&#10;Bio-stimulants registration"></textarea>
                        <p class="sf-hint">Each line becomes a bullet on the service card. Leave blank to hide.</p>
                    </div>

                    <div class="sf-mt">
                        <label class="sf-label" for="sf_wa_message">WhatsApp Booking Message</label>
                        <input class="sf-input" type="text" id="sf_wa_message" name="sf_wa_message"
                               placeholder="Hi, I'd like to book…">
                        <p class="sf-hint">Leave blank to auto-generate from the service name.</p>
                    </div>
                </div>

                <?php endif; ?>

                <!-- ── Submit ── -->
                <div class="sf-form-footer">
                    <button type="submit" class="sf-btn sf-btn--primary sf-btn--lg">
                        Save as Draft →
                    </button>
                    <p class="sf-hint">
                        Saved as <strong>Draft</strong> — go to
                        <a href="<?php echo esc_url(admin_url($sf_tab === 'service' ? 'edit.php?post_type=fumitech_service' : 'edit.php?post_type=fumitech_product')); ?>" target="_blank">
                            <?php echo $sf_tab === 'service' ? 'Services list' : 'Products list'; ?>
                        </a>
                        to review and publish.
                    </p>
                </div>

            </form>
        </div><!-- #sf-form-area -->

    </main>

    <!-- ── Bottom bar ── -->
    <footer class="sf-bottombar">
        <span>Fumitech Phyto Services</span>
        <span>·</span>
        <a href="<?php echo esc_url(home_url('/')); ?>">Visit site</a>
        <span>·</span>
        <a href="<?php echo esc_url(admin_url()); ?>">Admin dashboard</a>
    </footer>

</div><!-- .sf-shell -->

<script>
(function () {

    /* ── Show success screen if PHP saved something ───────────────────────── */
    <?php if ($sf_message) : ?>
    (function () {
        var editUrl = <?php echo wp_json_encode($sf_message); ?>;
        var tab     = <?php echo wp_json_encode($sf_tab === 'service' ? 'Service' : 'Product'); ?>;
        var title   = <?php echo wp_json_encode(sanitize_text_field(wp_unslash($_POST['sf_title'] ?? ''))); ?>;
        showSuccess(editUrl, tab + ' "' + title + '" saved as Draft.', editUrl);
    })();
    <?php endif; ?>

    function showSuccess(editUrl, msg, link) {
        document.getElementById('sf-form-area').style.display = 'none';
        var s = document.getElementById('sf-success');
        s.style.display = 'block';
        document.getElementById('sf-success-msg').textContent  = msg  || 'Item saved as Draft.';
        document.getElementById('sf-edit-link').href            = link || editUrl;
    }

    /* ── Product category cascade ─────────────────────────────────────────── */
    var childMap   = <?php echo wp_json_encode($sf_prod_child_map); ?>;
    var parentSel  = document.getElementById('sf_cat_parent');
    var childSel   = document.getElementById('sf_cat_child');
    var subcatCol  = document.getElementById('sf-subcat-col');

    if (parentSel) {
        parentSel.addEventListener('change', function () {
            var pid      = parseInt(this.value, 10);
            var children = childMap[pid] || [];
            childSel.innerHTML = '<option value="">— None (parent only) —</option>';
            children.forEach(function (c) {
                var o = document.createElement('option');
                o.value = c.id; o.textContent = c.name;
                childSel.appendChild(o);
            });
            subcatCol.style.display = (pid && children.length) ? '' : 'none';
        });
    }

    /* ── Media uploader ───────────────────────────────────────────────────── */
    var chooseBtn  = document.getElementById('sf-choose-img');
    var removeBtn  = document.getElementById('sf-remove-img');
    var thumbInput = document.getElementById('sf_thumbnail_id');
    var preview    = document.getElementById('sf-img-preview');
    var frame;

    if (chooseBtn && typeof wp !== 'undefined' && wp.media) {
        chooseBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title:    'Select Featured Image',
                button:   { text: 'Use this image' },
                multiple: false,
                library:  { type: 'image' }
            });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                thumbInput.value        = att.id;
                preview.innerHTML       = '<img src="' + url + '" alt="">';
                removeBtn.style.display = '';
            });
            frame.open();
        });

        removeBtn.addEventListener('click', function () {
            thumbInput.value        = '';
            preview.innerHTML       = '<span class="sf-img-empty">📷<br>No image<br>selected</span>';
            removeBtn.style.display = 'none';
        });
    }

})();
</script>

<?php
// Required WP footer hooks (for media uploader JS to work)
wp_footer();
?>
</body>
</html>
