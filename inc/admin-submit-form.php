<?php
/**
 * Admin "Add Product / Service" quick-entry form.
 * Mirrors the exact meta fields used by the product and service meta boxes.
 * Saves as Draft — admin publishes from the standard edit screen.
 */

// ── Register admin menu item ──────────────────────────────────────────────────
add_action('admin_menu', function () {
    add_menu_page(
        'Add Product / Service',
        'Add Item',
        'edit_posts',
        'fumitech-submit-item',
        'fumitech_submit_item_page',
        'dashicons-plus-alt2',
        3
    );
}, 15);

// ── Enqueue WP media uploader on this page only ───────────────────────────────
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_fumitech-submit-item') return;
    wp_enqueue_media();
});

// ── Page callback ─────────────────────────────────────────────────────────────
function fumitech_submit_item_page() {
    if (!current_user_can('edit_posts')) {
        wp_die('You do not have permission to access this page.');
    }

    $message = '';
    $error   = '';

    // Determine active tab (GET default, overridden by POST)
    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'product';
    if (!in_array($tab, ['product', 'service'], true)) $tab = 'product';

    // ── Handle form submission ────────────────────────────────────────────────
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['fumitech_submit_nonce']) &&
        wp_verify_nonce($_POST['fumitech_submit_nonce'], 'fumitech_submit_item')
    ) {
        $tab   = sanitize_key($_POST['fumitech_item_type'] ?? 'product');
        if (!in_array($tab, ['product', 'service'], true)) $tab = 'product';

        $title = sanitize_text_field(wp_unslash($_POST['item_title'] ?? ''));

        if (empty($title)) {
            $error = 'Name / Title is required.';
        } else {
            $post_type = $tab === 'service' ? 'fumitech_service' : 'fumitech_product';

            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => wp_kses_post(wp_unslash($_POST['item_content'] ?? '')),
                'post_excerpt' => sanitize_text_field(wp_unslash($_POST['item_excerpt'] ?? '')),
                'post_type'    => $post_type,
                'post_status'  => 'draft',
            ]);

            if (is_wp_error($post_id)) {
                $error = 'Could not save: ' . $post_id->get_error_message();
            } else {
                // Featured image
                $thumb_id = absint($_POST['item_thumbnail_id'] ?? 0);
                if ($thumb_id) set_post_thumbnail($post_id, $thumb_id);

                if ($tab === 'product') {
                    update_post_meta($post_id, '_fumitech_price',      sanitize_text_field(wp_unslash($_POST['fumitech_price']      ?? '')));
                    update_post_meta($post_id, '_fumitech_badge',      sanitize_text_field(wp_unslash($_POST['fumitech_badge']      ?? '')));
                    update_post_meta($post_id, '_fumitech_wa_message', sanitize_text_field(wp_unslash($_POST['fumitech_wa_message'] ?? '')));
                    update_post_meta($post_id, '_fumitech_featured',   isset($_POST['fumitech_featured']) ? '1' : '0');

                    $child_id  = absint($_POST['fumitech_cat_child']  ?? 0);
                    $parent_id = absint($_POST['fumitech_cat_parent'] ?? 0);
                    if ($child_id && $parent_id) {
                        wp_set_object_terms($post_id, [$child_id, $parent_id], 'product_category');
                    } elseif ($child_id) {
                        $t = get_term($child_id, 'product_category');
                        $terms = ($t && !is_wp_error($t) && $t->parent) ? [$child_id, $t->parent] : [$child_id];
                        wp_set_object_terms($post_id, $terms, 'product_category');
                    } elseif ($parent_id) {
                        wp_set_object_terms($post_id, [$parent_id], 'product_category');
                    }
                } else {
                    update_post_meta($post_id, '_fumitech_price',      sanitize_text_field(wp_unslash($_POST['fumitech_price']      ?? '')));
                    update_post_meta($post_id, '_fumitech_duration',   sanitize_text_field(wp_unslash($_POST['fumitech_duration']   ?? '')));
                    update_post_meta($post_id, '_fumitech_icon',       sanitize_text_field(wp_unslash($_POST['fumitech_icon']       ?? '')));
                    update_post_meta($post_id, '_fumitech_wa_message', sanitize_text_field(wp_unslash($_POST['fumitech_wa_message'] ?? '')));

                    $raw_lines = explode("\n", wp_unslash($_POST['fumitech_sub_items'] ?? ''));
                    $lines     = implode("\n", array_filter(array_map('sanitize_text_field', $raw_lines)));
                    update_post_meta($post_id, '_fumitech_sub_items', $lines);

                    $cat_id = absint($_POST['fumitech_svc_category'] ?? 0);
                    if ($cat_id) wp_set_object_terms($post_id, [$cat_id], 'service_category');
                }

                $edit_url = get_edit_post_link($post_id, 'raw');
                $label    = $tab === 'service' ? 'Service' : 'Product';
                $message  = $label . ' <strong>' . esc_html($title) . '</strong> saved as Draft. '
                    . '<a href="' . esc_url($edit_url) . '" style="color:#0ea5e9;font-weight:600;">Review &amp; Publish →</a>';
            }
        }
    }

    // ── Taxonomy data ─────────────────────────────────────────────────────────
    $prod_parents   = [];
    $prod_child_map = [];
    $all_prod_terms = get_terms(['taxonomy' => 'product_category', 'hide_empty' => false, 'orderby' => 'name']);
    if (!is_wp_error($all_prod_terms)) {
        foreach ($all_prod_terms as $t) {
            if ($t->parent == 0) $prod_parents[]               = $t;
            else                 $prod_child_map[$t->parent][] = ['id' => $t->term_id, 'name' => $t->name];
        }
    }
    $svc_cats = get_terms(['taxonomy' => 'service_category', 'hide_empty' => false]);

    $list_url = $tab === 'service'
        ? admin_url('edit.php?post_type=fumitech_service')
        : admin_url('edit.php?post_type=fumitech_product');
    $list_label = $tab === 'service' ? 'Services' : 'Products';
    ?>
    <div class="wrap fsi-wrap">

        <h1 class="fsi-heading">Add Product / Service</h1>

        <?php if ($message) : ?>
            <div class="fsi-notice fsi-notice--ok"><?php echo $message; // contains safe HTML ?></div>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="fsi-notice fsi-notice--err"><?php echo esc_html($error); ?></div>
        <?php endif; ?>

        <!-- ── Tab switcher ── -->
        <nav class="fsi-tabs" aria-label="Item type">
            <a href="<?php echo esc_url(admin_url('admin.php?page=fumitech-submit-item&tab=product')); ?>"
               class="fsi-tab<?php echo $tab === 'product' ? ' is-active' : ''; ?>">
               📦 Product
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=fumitech-submit-item&tab=service')); ?>"
               class="fsi-tab<?php echo $tab === 'service' ? ' is-active' : ''; ?>">
               🛠 Service
            </a>
        </nav>

        <form method="post" action="" class="fsi-form">
            <?php wp_nonce_field('fumitech_submit_item', 'fumitech_submit_nonce'); ?>
            <input type="hidden" name="fumitech_item_type" value="<?php echo esc_attr($tab); ?>">

            <!-- ── Basic info ── -->
            <div class="fsi-card">
                <h2 class="fsi-card__title">Basic Info</h2>
                <div class="fsi-grid fsi-grid--1">

                    <div class="fsi-field">
                        <label for="item_title">
                            <?php echo $tab === 'service' ? 'Service Name' : 'Product Name'; ?>
                            <span class="fsi-req">*</span>
                        </label>
                        <input type="text" id="item_title" name="item_title" required
                               placeholder="Enter <?php echo $tab === 'service' ? 'service' : 'product'; ?> name">
                    </div>

                    <div class="fsi-field">
                        <label for="item_excerpt">Short Description</label>
                        <textarea id="item_excerpt" name="item_excerpt" rows="2"
                                  placeholder="One-line summary shown on listing cards"></textarea>
                    </div>

                    <div class="fsi-field">
                        <label for="item_content">Full Description</label>
                        <textarea id="item_content" name="item_content" rows="6"
                                  placeholder="Detailed description for the single item page"></textarea>
                    </div>

                </div>
            </div>

            <!-- ── Featured image ── -->
            <div class="fsi-card">
                <h2 class="fsi-card__title">Featured Image</h2>
                <div class="fsi-img-row">
                    <div class="fsi-img-preview" id="fsi-img-preview">
                        <span class="fsi-img-empty">No image selected</span>
                    </div>
                    <div class="fsi-img-controls">
                        <button type="button" class="button button-secondary" id="fsi-choose-img">Choose Image</button>
                        <button type="button" class="button fsi-btn--danger" id="fsi-remove-img" style="display:none">✕ Remove</button>
                        <input type="hidden" name="item_thumbnail_id" id="item_thumbnail_id" value="">
                        <p class="fsi-hint">Recommended: 800 × 600 px JPG or PNG.</p>
                    </div>
                </div>
            </div>

            <?php if ($tab === 'product') : ?>

            <!-- ══ PRODUCT — Category ══ -->
            <div class="fsi-card">
                <h2 class="fsi-card__title">Category</h2>
                <div class="fsi-grid fsi-grid--2">

                    <div class="fsi-field">
                        <label for="fumitech_cat_parent">Parent Category</label>
                        <select id="fumitech_cat_parent" name="fumitech_cat_parent">
                            <option value="">— Select category —</option>
                            <?php foreach ($prod_parents as $t) : ?>
                                <option value="<?php echo esc_attr($t->term_id); ?>">
                                    <?php echo esc_html($t->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($prod_parents)) : ?>
                            <p class="fsi-hint">
                                No categories yet.
                                <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=product_category&post_type=fumitech_product')); ?>" target="_blank">Add categories →</a>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="fsi-field" id="fsi-subcat-row" style="display:none">
                        <label for="fumitech_cat_child">Sub-category</label>
                        <select id="fumitech_cat_child" name="fumitech_cat_child">
                            <option value="">— None (parent only) —</option>
                        </select>
                        <p class="fsi-hint">Optional — pick a more specific sub-category.</p>
                    </div>

                </div>
            </div>

            <!-- ══ PRODUCT — Details ══ -->
            <div class="fsi-card">
                <h2 class="fsi-card__title">Product Details</h2>
                <div class="fsi-grid fsi-grid--3">

                    <div class="fsi-field">
                        <label for="fumitech_price">Price (Ksh)</label>
                        <input type="text" id="fumitech_price" name="fumitech_price"
                               placeholder="e.g. 1,500">
                    </div>

                    <div class="fsi-field">
                        <label for="fumitech_badge">Badge Label</label>
                        <input type="text" id="fumitech_badge" name="fumitech_badge"
                               placeholder="e.g. Best Seller · New · Sale">
                        <p class="fsi-hint">Leave blank to hide the badge tag.</p>
                    </div>

                    <div class="fsi-field">
                        <label for="fumitech_wa_message">WhatsApp Order Message</label>
                        <input type="text" id="fumitech_wa_message" name="fumitech_wa_message"
                               placeholder="Hi, I'd like to order…">
                        <p class="fsi-hint">Leave blank to auto-generate from title.</p>
                    </div>

                </div>

                <div class="fsi-field fsi-field--mt">
                    <label class="fsi-check-label">
                        <input type="checkbox" name="fumitech_featured" value="1">
                        <span>Feature on Homepage</span>
                    </label>
                    <p class="fsi-hint fsi-hint--indent">Shows this product in the homepage showcase grid (up to 8 slots).</p>
                </div>
            </div>

            <?php else : ?>

            <!-- ══ SERVICE — Category & Details ══ -->
            <div class="fsi-card">
                <h2 class="fsi-card__title">Service Category &amp; Details</h2>
                <div class="fsi-grid fsi-grid--2">

                    <div class="fsi-field">
                        <label for="fumitech_svc_category">Service Category</label>
                        <select id="fumitech_svc_category" name="fumitech_svc_category">
                            <option value="">— Select category —</option>
                            <?php if (!is_wp_error($svc_cats)) foreach ($svc_cats as $cat) : ?>
                                <option value="<?php echo esc_attr($cat->term_id); ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="fsi-hint">Groups services on the Services page (e.g. Pest Control, Consultancy).</p>
                    </div>

                    <div class="fsi-field">
                        <label for="fumitech_icon">Emoji Icon</label>
                        <input type="text" id="fumitech_icon" name="fumitech_icon"
                               placeholder="e.g. 🐜">
                        <p class="fsi-hint">Displayed on the service card.</p>
                    </div>

                    <div class="fsi-field">
                        <label for="fumitech_price">Price / Starting From</label>
                        <input type="text" id="fumitech_price" name="fumitech_price"
                               placeholder="e.g. Ksh 5,000">
                    </div>

                    <div class="fsi-field">
                        <label for="fumitech_duration">Duration</label>
                        <input type="text" id="fumitech_duration" name="fumitech_duration"
                               placeholder="e.g. 2–4 hours">
                    </div>

                </div>

                <div class="fsi-field fsi-field--mt">
                    <label for="fumitech_sub_items">Sub-services / Sub-types</label>
                    <textarea id="fumitech_sub_items" name="fumitech_sub_items" rows="5"
                              placeholder="One item per line, e.g.&#10;Conventional pesticide registration&#10;Bio-pesticides registration&#10;Bio-stimulants registration"></textarea>
                    <p class="fsi-hint">Each line becomes a bullet on the service card. Leave blank to hide.</p>
                </div>

                <div class="fsi-field fsi-field--mt">
                    <label for="fumitech_wa_message">WhatsApp Booking Message</label>
                    <input type="text" id="fumitech_wa_message" name="fumitech_wa_message"
                           placeholder="Hi, I'd like to book…">
                    <p class="fsi-hint">Leave blank to auto-generate from service name.</p>
                </div>
            </div>

            <?php endif; ?>

            <!-- ── Submit ── -->
            <div class="fsi-footer">
                <button type="submit" class="button button-primary fsi-submit">Save as Draft →</button>
                <p class="fsi-hint">
                    Saved as Draft — go to
                    <a href="<?php echo esc_url($list_url); ?>"><?php echo esc_html($list_label); ?> list</a>
                    to review and publish.
                </p>
            </div>

        </form>
    </div><!-- .fsi-wrap -->

    <style>
    .fsi-wrap  { max-width: 900px; padding-bottom: 60px; }
    .fsi-heading { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 20px; }

    /* notices */
    .fsi-notice { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; line-height: 1.5; }
    .fsi-notice--ok  { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .fsi-notice--err { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

    /* tabs */
    .fsi-tabs { display: flex; gap: 2px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
    .fsi-tab  { display: inline-block; padding: 9px 20px; font-size: 14px; font-weight: 600; color: #64748b; border-radius: 6px 6px 0 0; border: 1px solid transparent; border-bottom: none; text-decoration: none; margin-bottom: -2px; transition: color .15s, background .15s; }
    .fsi-tab:hover    { color: #0ea5e9; background: #f0f9ff; }
    .fsi-tab.is-active { color: #0ea5e9; background: #fff; border-color: #e2e8f0; border-bottom-color: #fff; }

    /* cards */
    .fsi-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 22px 24px; margin-bottom: 16px; }
    .fsi-card__title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin: 0 0 16px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }

    /* grid */
    .fsi-grid   { display: grid; gap: 16px; }
    .fsi-grid--1 { grid-template-columns: 1fr; }
    .fsi-grid--2 { grid-template-columns: 1fr 1fr; }
    .fsi-grid--3 { grid-template-columns: 1fr 1fr 1fr; }

    /* fields */
    .fsi-field label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .fsi-req { color: #ef4444; margin-left: 2px; }
    .fsi-field--mt { margin-top: 4px; }
    .fsi-field input[type="text"],
    .fsi-field select,
    .fsi-field textarea { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: inherit; background: #fff; color: #0f172a; box-sizing: border-box; }
    .fsi-field input[type="text"]:focus,
    .fsi-field select:focus,
    .fsi-field textarea:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.15); }
    .fsi-field textarea { resize: vertical; }
    .fsi-hint { font-size: 12px; color: #94a3b8; margin-top: 5px; line-height: 1.4; }
    .fsi-hint a { color: #0ea5e9; text-decoration: none; }
    .fsi-hint a:hover { text-decoration: underline; }
    .fsi-hint--indent { margin-left: 24px; }

    /* checkbox */
    .fsi-check-label { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 500; color: #334155; }
    .fsi-check-label input[type="checkbox"] { width: 16px; height: 16px; accent-color: #0ea5e9; cursor: pointer; flex-shrink: 0; }

    /* image picker */
    .fsi-img-row { display: flex; align-items: flex-start; gap: 20px; }
    .fsi-img-preview { width: 160px; height: 120px; flex-shrink: 0; border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .fsi-img-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .fsi-img-empty { font-size: 12px; color: #94a3b8; text-align: center; padding: 12px; }
    .fsi-img-controls { display: flex; flex-direction: column; gap: 8px; padding-top: 4px; }
    .fsi-btn--danger { color: #ef4444 !important; border-color: #ef4444 !important; }
    .fsi-btn--danger:hover { color: #fff !important; background: #ef4444 !important; }

    /* footer row */
    .fsi-footer { display: flex; align-items: center; gap: 16px; padding: 8px 0 24px; }
    .fsi-submit { padding: 10px 28px !important; font-size: 14px !important; font-weight: 600 !important; background: #0ea5e9 !important; border-color: #0284c7 !important; color: #fff !important; border-radius: 6px !important; }
    .fsi-submit:hover { background: #0284c7 !important; }

    @media (max-width: 780px) {
        .fsi-grid--2, .fsi-grid--3 { grid-template-columns: 1fr; }
        .fsi-img-row { flex-direction: column; }
        .fsi-footer  { flex-direction: column; align-items: flex-start; }
    }
    </style>

    <script>
    (function () {

        /* ── Product category cascade ──────────────────────────────────────── */
        var childMap  = <?php echo wp_json_encode($prod_child_map); ?>;
        var parentSel = document.getElementById('fumitech_cat_parent');
        var childSel  = document.getElementById('fumitech_cat_child');
        var subcatRow = document.getElementById('fsi-subcat-row');

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
                subcatRow.style.display = (pid && children.length) ? '' : 'none';
            });
        }

        /* ── Media uploader ────────────────────────────────────────────────── */
        var chooseBtn  = document.getElementById('fsi-choose-img');
        var removeBtn  = document.getElementById('fsi-remove-img');
        var thumbInput = document.getElementById('item_thumbnail_id');
        var preview    = document.getElementById('fsi-img-preview');
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
                    thumbInput.value       = att.id;
                    preview.innerHTML      = '<img src="' + url + '" alt="">';
                    removeBtn.style.display = '';
                });
                frame.open();
            });

            removeBtn.addEventListener('click', function () {
                thumbInput.value        = '';
                preview.innerHTML       = '<span class="fsi-img-empty">No image selected</span>';
                removeBtn.style.display = 'none';
            });
        }

    })();
    </script>
    <?php
}
