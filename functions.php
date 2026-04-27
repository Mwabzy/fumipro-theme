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
        '3.2'
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

    register_nav_menus([
        'primary' => __('Primary Navigation', 'fumipro'),
        'footer'  => __('Footer Navigation', 'fumipro'),
    ]);
}
add_action('after_setup_theme', 'fumipro_setup');


// ── Remove default WP block styles ─────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}, 100);


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
    $price    = get_post_meta($post->ID, '_fumitech_price', true);
    $duration = get_post_meta($post->ID, '_fumitech_duration', true);
    $icon     = get_post_meta($post->ID, '_fumitech_icon', true);
    $wa_msg   = get_post_meta($post->ID, '_fumitech_wa_message', true);
    ?>
    <style>
        .fumitech-meta-table { width:100%; border-collapse:collapse; }
        .fumitech-meta-table th { width:200px; padding:12px 12px 12px 0; text-align:left; font-weight:600; vertical-align:top; color:#1e293b; }
        .fumitech-meta-table td { padding:8px 0; }
        .fumitech-meta-table input[type=text] { width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; }
        .fumitech-meta-table input[type=text]:focus { outline:none; border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,0.15); }
        .fumitech-meta-table .desc { font-size:12px; color:#64748b; margin-top:4px; }
    </style>
    <table class="fumitech-meta-table">
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
});
