<?php
/**
 * FumiPro — functions.php
 */

// ── Enqueue assets ──────────────────────────────────────────────────────────
function fumipro_enqueue_assets() {
    wp_enqueue_style(
        'fumipro-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'fumipro-main',
        get_stylesheet_directory_uri() . '/css/main.css',
        ['fumipro-google-fonts'],
        '3.4'
    );

    wp_enqueue_script(
        'fumipro-main',
        get_stylesheet_directory_uri() . '/js/main.js',
        [],
        '2.1',
        true
    );
}
add_action('wp_enqueue_scripts', 'fumipro_enqueue_assets');


// ── Theme supports ──────────────────────────────────────────────────────────
function fumipro_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'fumipro'),
        'footer'  => __('Footer Navigation', 'fumipro'),
    ]);
}
add_action('after_setup_theme', 'fumipro_setup');

// Dequeue WooCommerce default styles that conflict with the theme
add_filter('woocommerce_enqueue_styles', '__return_empty_array');


// ── Remove default WP block styles ─────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}, 100);


// ── Hero Slides admin menu ───────────────────────────────────────────────────
add_action('admin_menu', function () {
    add_menu_page(
        'Hero Carousel',
        'Hero Slides',
        'manage_options',
        'fumitech-hero-slides',
        'fumitech_hero_slides_page',
        'dashicons-images-alt2',
        4
    );
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_fumitech-hero-slides') return;
    wp_enqueue_media();
    wp_enqueue_script(
        'fumitech-hero-admin',
        get_template_directory_uri() . '/js/hero-admin.js',
        ['jquery'],
        '1.0',
        true
    );
});

function fumitech_hero_slides_page() {
    $saved = false;

    if (isset($_POST['fumitech_hero_save']) &&
        isset($_POST['fumitech_hero_nonce']) &&
        wp_verify_nonce($_POST['fumitech_hero_nonce'], 'fumitech_hero_save') &&
        current_user_can('manage_options')) {

        $clean = [];
        if (!empty($_POST['slides']) && is_array($_POST['slides'])) {
            foreach (array_values($_POST['slides']) as $s) {
                $id = absint($s['id'] ?? 0);
                if (!$id) continue;
                $clean[] = [
                    'id'       => $id,
                    'url'      => wp_get_attachment_image_url($id, 'full') ?: esc_url_raw($s['url'] ?? ''),
                    'headline' => sanitize_text_field($s['headline'] ?? ''),
                    'sub'      => sanitize_text_field($s['sub'] ?? ''),
                ];
            }
        }
        update_option('fumitech_hero_slides', $clean);
        $saved = true;
    }

    $slides = get_option('fumitech_hero_slides', []);
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:26px;">🖼</span> Hero Carousel Slides
        </h1>
        <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p><strong>Slides saved successfully!</strong></p></div>
        <?php endif; ?>
        <p style="color:#64748b;max-width:640px;margin-bottom:24px;">
            Add background images for the homepage hero. Images rotate automatically every 5 seconds.
            <br><strong>Recommended size:</strong> 1920 × 1080 px (landscape). The gradient overlay is always applied over the image.
        </p>

        <form method="post" id="hero-slides-form">
            <?php wp_nonce_field('fumitech_hero_save', 'fumitech_hero_nonce'); ?>

            <div id="hero-slides-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:28px;">
                <?php foreach ($slides as $i => $slide) :
                    $thumb = wp_get_attachment_image_url($slide['id'], 'medium') ?: $slide['url'];
                ?>
                <div class="fh-slide-card" data-index="<?php echo $i; ?>">
                    <div class="fh-thumb" style="background-image:url('<?php echo esc_url($thumb); ?>')">
                        <button type="button" class="fh-remove" title="Remove slide">&times;</button>
                        <span class="fh-slide-num"><?php echo $i + 1; ?></span>
                    </div>
                    <div class="fh-fields">
                        <input type="hidden" name="slides[<?php echo $i; ?>][id]"  value="<?php echo esc_attr($slide['id']); ?>">
                        <input type="hidden" name="slides[<?php echo $i; ?>][url]" value="<?php echo esc_url($slide['url']); ?>">
                        <label>Headline <small>(leave blank for default)</small></label>
                        <input type="text" name="slides[<?php echo $i; ?>][headline]" value="<?php echo esc_attr($slide['headline']); ?>" placeholder="Protecting Your Home &amp; Business…">
                        <label>Subheadline <small>(leave blank for default)</small></label>
                        <input type="text" name="slides[<?php echo $i; ?>][sub]" value="<?php echo esc_attr($slide['sub']); ?>" placeholder="Fast, effective, eco-friendly…">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                <button type="button" id="fh-add-slide" class="button button-secondary" style="height:38px;font-size:14px;">
                    + Add Image
                </button>
                <button type="submit" name="fumitech_hero_save" class="button button-primary" style="height:38px;font-size:14px;">
                    Save Slides
                </button>
            </div>
        </form>
    </div>

    <style>
        .fh-slide-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; }
        .fh-thumb { aspect-ratio:16/9; background:center/cover no-repeat #0ea5e9; position:relative; }
        .fh-remove { position:absolute; top:8px; right:8px; width:28px; height:28px; border-radius:50%; background:rgba(0,0,0,0.55); color:#fff; border:none; cursor:pointer; font-size:18px; line-height:1; display:flex; align-items:center; justify-content:center; transition:background .2s; }
        .fh-remove:hover { background:rgba(220,38,38,.8); }
        .fh-slide-num { position:absolute; bottom:8px; left:10px; background:rgba(0,0,0,0.45); color:#fff; font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; }
        .fh-fields { padding:14px; display:flex; flex-direction:column; gap:6px; }
        .fh-fields label { font-size:12px; font-weight:600; color:#1e293b; }
        .fh-fields small { font-weight:400; color:#94a3b8; }
        .fh-fields input[type=text] { width:100%; padding:7px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; }
        .fh-fields input[type=text]:focus { outline:none; border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.15); }
    </style>
    <?php
}


// ── Custom Post Types ────────────────────────────────────────────────────────
function fumitech_register_post_types() {

    register_post_type('fumitech_product', [
        'labels' => [
            'name'               => 'Products',
            'singular_name'      => 'Product',
            'add_new'            => 'Add New Product',
            'add_new_item'       => 'Add New Product',
            'edit_item'          => 'Edit Product',
            'new_item'           => 'New Product',
            'view_item'          => 'View Product',
            'search_items'       => 'Search Products',
            'not_found'          => 'No products found',
            'not_found_in_trash' => 'No products found in trash',
            'menu_name'          => 'Products',
            'all_items'          => 'All Products',
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => ['slug' => 'products', 'with_front' => false],
        'menu_icon'         => 'dashicons-cart',
        'menu_position'     => 5,
        'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'      => true,
    ]);

    register_post_type('fumitech_service', [
        'labels' => [
            'name'               => 'Services',
            'singular_name'      => 'Service',
            'add_new'            => 'Add New Service',
            'add_new_item'       => 'Add New Service',
            'edit_item'          => 'Edit Service',
            'new_item'           => 'New Service',
            'view_item'          => 'View Service',
            'search_items'       => 'Search Services',
            'not_found'          => 'No services found',
            'not_found_in_trash' => 'No services found in trash',
            'menu_name'          => 'Services',
            'all_items'          => 'All Services',
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => ['slug' => 'services', 'with_front' => false],
        'menu_icon'         => 'dashicons-clipboard',
        'menu_position'     => 6,
        'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'      => true,
    ]);

    // Service category taxonomy
    register_taxonomy('service_category', 'fumitech_service', [
        'labels' => [
            'name'          => 'Service Categories',
            'singular_name' => 'Service Category',
            'add_new_item'  => 'Add New Category',
            'edit_item'     => 'Edit Category',
            'menu_name'     => 'Categories',
        ],
        'hierarchical'  => true,
        'public'        => true,
        'rewrite'       => ['slug' => 'service-category'],
        'show_in_rest'  => true,
    ]);

    // Product category taxonomy
    register_taxonomy('product_category', 'fumitech_product', [
        'labels' => [
            'name'          => 'Product Categories',
            'singular_name' => 'Product Category',
            'add_new_item'  => 'Add New Category',
            'edit_item'     => 'Edit Category',
            'menu_name'     => 'Categories',
        ],
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => ['slug' => 'product-category'],
        'show_in_rest'      => true,
    ]);
}
add_action('init', 'fumitech_register_post_types');

// Flush rewrite rules when theme is activated
add_action('after_switch_theme', function () {
    fumitech_register_post_types();
    flush_rewrite_rules();
});


// ── Seed service categories ──────────────────────────────────────────────────
add_action('admin_init', function () {
    if (get_option('fumitech_service_cats_seeded')) return;

    $service_cats = ['Pest Control', 'Consultancy'];
    foreach ($service_cats as $name) {
        if (!term_exists($name, 'service_category')) {
            wp_insert_term($name, 'service_category');
        }
    }
    update_option('fumitech_service_cats_seeded', true);
});


// ── Seed product categories & subcategories ──────────────────────────────────
add_action('admin_init', function () {
    if (get_option('fumitech_categories_seeded')) return;

    $taxonomy = 'product_category';

    $tree = [
        'Equipment' => [
            'Thermal Foggers',
            'Knapsack Sprayers',
            'Protective Gear',
            'Sealing Tarps',
            'Bait Stations',
            'Gas Measuring Equipment',
        ],
        'Chemicals & Products' => [
            'Industrial Chemicals',
            'Rodenticides',
            'Fungicides',
            'Insecticides',
            'Miticides',
            'Insect Traps',
            'Herbicides',
            'Biologicals',
            'Fumigants',
            'Termiticides',
            'Nematicides',
            'Foliar',
            'Disinfectants',
        ],
    ];

    foreach ($tree as $parent_name => $children) {
        // Insert parent if it doesn't exist
        $existing = term_exists($parent_name, $taxonomy);
        if ($existing) {
            $parent_id = is_array($existing) ? (int) $existing['term_id'] : (int) $existing;
        } else {
            $result    = wp_insert_term($parent_name, $taxonomy);
            $parent_id = is_wp_error($result) ? 0 : (int) $result['term_id'];
        }

        if (!$parent_id) continue;

        foreach ($children as $child_name) {
            if (!term_exists($child_name, $taxonomy, $parent_id)) {
                wp_insert_term($child_name, $taxonomy, ['parent' => $parent_id]);
            }
        }
    }

    update_option('fumitech_categories_seeded', true);
});


// ── Hide the default taxonomy checkbox metabox (replaced by dropdown below) ──
add_action('add_meta_boxes', function () {
    remove_meta_box('product_categorydiv', 'fumitech_product', 'side');
}, 20);

// ── Product meta box ─────────────────────────────────────────────────────────
add_action('add_meta_boxes', function () {
    add_meta_box(
        'fumitech_product_details',
        '📦 Product Details',
        'fumitech_product_meta_cb',
        'fumitech_product',
        'normal',
        'high'
    );
});

function fumitech_product_meta_cb($post) {
    wp_nonce_field('fumitech_product_save', 'fumitech_product_nonce');

    $price   = get_post_meta($post->ID, '_fumitech_price', true);
    $badge   = get_post_meta($post->ID, '_fumitech_badge', true);
    $wa_msg  = get_post_meta($post->ID, '_fumitech_wa_message', true);

    // ── Category data ────────────────────────────────────────────────────────
    $all_terms = get_terms([
        'taxonomy'   => 'product_category',
        'hide_empty' => false,
        'orderby'    => 'name',
    ]);

    $parent_terms = [];
    $child_map    = []; // parent_id => [ child term objects ]
    if (!is_wp_error($all_terms)) {
        foreach ($all_terms as $term) {
            if ($term->parent == 0) {
                $parent_terms[] = $term;
            } else {
                $child_map[$term->parent][] = ['id' => $term->term_id, 'name' => $term->name];
            }
        }
    }

    // Current assignment
    $current_terms     = wp_get_object_terms($post->ID, 'product_category', ['fields' => 'ids']);
    $current_parent_id = 0;
    $current_child_id  = 0;

    if (!is_wp_error($current_terms) && !empty($current_terms)) {
        $tid  = (int) $current_terms[0];
        $term = get_term($tid, 'product_category');
        if ($term && !is_wp_error($term)) {
            if ($term->parent == 0) {
                $current_parent_id = $tid;
            } else {
                $current_child_id  = $tid;
                $current_parent_id = (int) $term->parent;
            }
        }
    }
    ?>
    <style>
        .fumitech-meta-table { width:100%; border-collapse:collapse; }
        .fumitech-meta-table th { width:200px; padding:12px 12px 12px 0; text-align:left; font-weight:600; vertical-align:top; color:#1e293b; }
        .fumitech-meta-table td { padding:8px 0; }
        .fumitech-meta-table input[type=text],
        .fumitech-meta-table select { width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; background:#fff; }
        .fumitech-meta-table input[type=text]:focus,
        .fumitech-meta-table select:focus { outline:none; border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,0.15); }
        .fumitech-meta-table .desc { font-size:12px; color:#64748b; margin-top:4px; }
        .fumitech-cat-hint { display:inline-block; margin-top:6px; font-size:12px; color:#64748b; }
        .fumitech-cat-hint a { color:#0ea5e9; text-decoration:none; }
        .fumitech-cat-hint a:hover { text-decoration:underline; }
    </style>

    <table class="fumitech-meta-table">
        <!-- ── Category ───────────────────────────────────────────────────── -->
        <tr>
            <th><label for="fumitech_cat_parent">Category</label></th>
            <td>
                <select id="fumitech_cat_parent" name="fumitech_cat_parent">
                    <option value="">— Select a category —</option>
                    <?php foreach ($parent_terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>"
                            <?php selected($current_parent_id, $term->term_id); ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($parent_terms)) : ?>
                    <p class="fumitech-cat-hint">
                        No categories yet.
                        <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=product_category&post_type=fumitech_product')); ?>" target="_blank">
                            Add categories &rarr;
                        </a>
                    </p>
                <?php endif; ?>
            </td>
        </tr>

        <!-- ── Sub-category (shown only when parent has children) ─────────── -->
        <tr id="fumitech_subcat_row" style="<?php echo ($current_parent_id && !empty($child_map[$current_parent_id])) ? '' : 'display:none'; ?>">
            <th><label for="fumitech_cat_child">Sub-category</label></th>
            <td>
                <select id="fumitech_cat_child" name="fumitech_cat_child">
                    <option value="">— None (keep parent only) —</option>
                    <?php if ($current_parent_id && !empty($child_map[$current_parent_id])) :
                        foreach ($child_map[$current_parent_id] as $child) : ?>
                            <option value="<?php echo esc_attr($child['id']); ?>"
                                <?php selected($current_child_id, $child['id']); ?>>
                                <?php echo esc_html($child['name']); ?>
                            </option>
                        <?php endforeach;
                    endif; ?>
                </select>
                <p class="desc">Optional — pick a more specific sub-category.</p>
            </td>
        </tr>

        <!-- ── Price ──────────────────────────────────────────────────────── -->
        <tr>
            <th><label for="fumitech_price">Price (Ksh)</label></th>
            <td>
                <input type="text" id="fumitech_price" name="fumitech_price"
                       value="<?php echo esc_attr($price); ?>" placeholder="e.g. 1,500"/>
            </td>
        </tr>

        <!-- ── Badge ──────────────────────────────────────────────────────── -->
        <tr>
            <th><label for="fumitech_badge">Badge Label</label></th>
            <td>
                <input type="text" id="fumitech_badge" name="fumitech_badge"
                       value="<?php echo esc_attr($badge); ?>" placeholder="e.g. Best Seller · New · Sale"/>
                <p class="desc">Shown as a coloured tag on the card. Leave blank to hide.</p>
            </td>
        </tr>

        <!-- ── WhatsApp Message ────────────────────────────────────────────── -->
        <tr>
            <th><label for="fumitech_wa_message">WhatsApp Order Message</label></th>
            <td>
                <input type="text" id="fumitech_wa_message" name="fumitech_wa_message"
                       value="<?php echo esc_attr($wa_msg); ?>"
                       placeholder="Hi, I'd like to order: <?php echo esc_attr(get_the_title()); ?>"/>
                <p class="desc">Pre-filled WhatsApp message. Leave blank to auto-generate from title.</p>
            </td>
        </tr>
    </table>

    <script>
    (function () {
        var childMap   = <?php echo wp_json_encode($child_map); ?>;
        var parentSel  = document.getElementById('fumitech_cat_parent');
        var childSel   = document.getElementById('fumitech_cat_child');
        var childRow   = document.getElementById('fumitech_subcat_row');

        function refreshChildren() {
            var pid      = parseInt(parentSel.value, 10);
            var children = childMap[pid] || [];

            childSel.innerHTML = '<option value="">— None (keep parent only) —</option>';
            children.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value       = c.id;
                opt.textContent = c.name;
                childSel.appendChild(opt);
            });

            childRow.style.display = (pid && children.length) ? '' : 'none';
        }

        parentSel.addEventListener('change', refreshChildren);
    })();
    </script>
    <?php
}

add_action('save_post_fumitech_product', function ($post_id) {
    if (!isset($_POST['fumitech_product_nonce']) ||
        !wp_verify_nonce($_POST['fumitech_product_nonce'], 'fumitech_product_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // ── Save custom meta fields ───────────────────────────────────────────────
    $fields = ['fumitech_price', 'fumitech_badge', 'fumitech_wa_message'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // ── Save category assignment ──────────────────────────────────────────────
    // If a sub-category is chosen, assign that (it implies the parent).
    // Otherwise assign the parent category. Clear if nothing selected.
    $child_id  = isset($_POST['fumitech_cat_child'])  ? (int) $_POST['fumitech_cat_child']  : 0;
    $parent_id = isset($_POST['fumitech_cat_parent']) ? (int) $_POST['fumitech_cat_parent'] : 0;

    if ($child_id) {
        wp_set_object_terms($post_id, [$child_id], 'product_category');
    } elseif ($parent_id) {
        wp_set_object_terms($post_id, [$parent_id], 'product_category');
    } else {
        wp_set_object_terms($post_id, [], 'product_category');
    }
});


// ── Featured on Homepage metabox ─────────────────────────────────────────────
add_action('add_meta_boxes', function () {
    add_meta_box(
        'fumitech_product_featured',
        '⭐ Featured on Homepage',
        'fumitech_featured_meta_cb',
        'fumitech_product',
        'side',
        'high'
    );
});

function fumitech_featured_meta_cb($post) {
    wp_nonce_field('fumitech_featured_save', 'fumitech_featured_nonce');
    $featured = get_post_meta($post->ID, '_fumitech_featured', true);
    ?>
    <style>
        .fumitech-featured-wrap { padding: 4px 0; }
        .fumitech-featured-wrap label {
            display: flex; align-items: center; gap: 10px;
            cursor: pointer; font-size: 14px; font-weight: 500;
        }
        .fumitech-featured-wrap input[type=checkbox] {
            width: 18px; height: 18px; accent-color: #0ea5e9; cursor: pointer;
        }
        .fumitech-featured-wrap .desc {
            margin: 8px 0 0; font-size: 12px; color: #64748b; line-height: 1.5;
        }
    </style>
    <div class="fumitech-featured-wrap">
        <label>
            <input type="checkbox" name="fumitech_featured" value="1"
                   <?php checked($featured, '1'); ?> />
            Show on homepage
        </label>
        <p class="desc">Tick to include this product in the homepage showcase. Up to 8 products are shown (4 columns × 2 rows on desktop).</p>
    </div>
    <?php
}

add_action('save_post_fumitech_product', function ($post_id) {
    if (!isset($_POST['fumitech_featured_nonce']) ||
        !wp_verify_nonce($_POST['fumitech_featured_nonce'], 'fumitech_featured_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, '_fumitech_featured', isset($_POST['fumitech_featured']) ? '1' : '0');
}, 20);


// ── Service meta box ─────────────────────────────────────────────────────────
add_action('add_meta_boxes', function () {
    add_meta_box(
        'fumitech_service_details',
        '🛠 Service Details',
        'fumitech_service_meta_cb',
        'fumitech_service',
        'normal',
        'high'
    );
});

function fumitech_service_meta_cb($post) {
    wp_nonce_field('fumitech_service_save', 'fumitech_service_nonce');
    $price     = get_post_meta($post->ID, '_fumitech_price', true);
    $duration  = get_post_meta($post->ID, '_fumitech_duration', true);
    $icon      = get_post_meta($post->ID, '_fumitech_icon', true);
    $wa_msg    = get_post_meta($post->ID, '_fumitech_wa_message', true);
    $sub_items = get_post_meta($post->ID, '_fumitech_sub_items', true);

    $svc_cats       = get_terms(['taxonomy' => 'service_category', 'hide_empty' => false]);
    $current_cat    = wp_get_object_terms($post->ID, 'service_category', ['fields' => 'ids']);
    $current_cat_id = (!is_wp_error($current_cat) && !empty($current_cat)) ? (int) $current_cat[0] : 0;
    ?>
    <style>
        .fumitech-meta-table { width:100%; border-collapse:collapse; }
        .fumitech-meta-table th { width:200px; padding:12px 12px 12px 0; text-align:left; font-weight:600; vertical-align:top; color:#1e293b; }
        .fumitech-meta-table td { padding:8px 0; }
        .fumitech-meta-table input[type=text],
        .fumitech-meta-table select,
        .fumitech-meta-table textarea { width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; }
        .fumitech-meta-table input[type=text]:focus,
        .fumitech-meta-table select:focus,
        .fumitech-meta-table textarea:focus { outline:none; border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,0.15); }
        .fumitech-meta-table .desc { font-size:12px; color:#64748b; margin-top:4px; }
    </style>
    <table class="fumitech-meta-table">
        <tr>
            <th><label for="fumitech_svc_category">Service Category</label></th>
            <td>
                <select id="fumitech_svc_category" name="fumitech_svc_category">
                    <option value="">— Select —</option>
                    <?php if (!is_wp_error($svc_cats)) foreach ($svc_cats as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($current_cat_id, $cat->term_id); ?>>
                            <?php echo esc_html($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="desc">Groups services on the Services page (Pest Control or Consultancy).</p>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_price">Price / Starting From</label></th>
            <td>
                <input type="text" id="fumitech_price" name="fumitech_price"
                       value="<?php echo esc_attr($price); ?>" placeholder="e.g. Ksh 5,000"/>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_duration">Duration</label></th>
            <td>
                <input type="text" id="fumitech_duration" name="fumitech_duration"
                       value="<?php echo esc_attr($duration); ?>" placeholder="e.g. 2–4 hours"/>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_icon">Emoji Icon</label></th>
            <td>
                <input type="text" id="fumitech_icon" name="fumitech_icon"
                       value="<?php echo esc_attr($icon); ?>" placeholder="e.g. 🐜"/>
                <p class="desc">Emoji shown on the service card. Use any emoji.</p>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_sub_items">Sub-services / Sub-types</label></th>
            <td>
                <textarea id="fumitech_sub_items" name="fumitech_sub_items" rows="4"
                          placeholder="One item per line, e.g.&#10;Conventional pesticide registration&#10;Bio-pesticides registration&#10;Bio-stimulants registration"><?php echo esc_textarea($sub_items); ?></textarea>
                <p class="desc">One item per line. Shown as a bullet list on the service card. Leave blank to hide.</p>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_wa_message">WhatsApp Booking Message</label></th>
            <td>
                <input type="text" id="fumitech_wa_message" name="fumitech_wa_message"
                       value="<?php echo esc_attr($wa_msg); ?>"
                       placeholder="Hi, I'd like to book: <?php echo esc_attr(get_the_title()); ?>"/>
                <p class="desc">Leave blank to auto-generate from service title.</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('save_post_fumitech_service', function ($post_id) {
    if (!isset($_POST['fumitech_service_nonce']) ||
        !wp_verify_nonce($_POST['fumitech_service_nonce'], 'fumitech_service_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['fumitech_price', 'fumitech_duration', 'fumitech_icon', 'fumitech_wa_message'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Sub-items (raw textarea — sanitize each line)
    if (isset($_POST['fumitech_sub_items'])) {
        $lines = array_filter(array_map('sanitize_text_field', explode("\n", $_POST['fumitech_sub_items'])));
        update_post_meta($post_id, '_fumitech_sub_items', implode("\n", $lines));
    }

    // Service category
    $cat_id = isset($_POST['fumitech_svc_category']) ? (int) $_POST['fumitech_svc_category'] : 0;
    if ($cat_id) {
        wp_set_object_terms($post_id, [$cat_id], 'service_category');
    } else {
        wp_set_object_terms($post_id, [], 'service_category');
    }
});
