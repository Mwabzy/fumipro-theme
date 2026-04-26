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
        '2.0'
    );

    wp_enqueue_script(
        'fumipro-main',
        get_stylesheet_directory_uri() . '/js/main.js',
        [],
        '1.0',
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
            <th><label for="fumitech_price">Price (Ksh)</label></th>
            <td>
                <input type="text" id="fumitech_price" name="fumitech_price"
                       value="<?php echo esc_attr($price); ?>" placeholder="e.g. 1,500"/>
            </td>
        </tr>
        <tr>
            <th><label for="fumitech_badge">Badge Label</label></th>
            <td>
                <input type="text" id="fumitech_badge" name="fumitech_badge"
                       value="<?php echo esc_attr($badge); ?>" placeholder="e.g. Best Seller · New · Sale"/>
                <p class="desc">Shown as a coloured tag on the card. Leave blank to hide.</p>
            </td>
        </tr>
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
    <?php
}

add_action('save_post_fumitech_product', function ($post_id) {
    if (!isset($_POST['fumitech_product_nonce']) ||
        !wp_verify_nonce($_POST['fumitech_product_nonce'], 'fumitech_product_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['fumitech_price', 'fumitech_badge', 'fumitech_wa_message'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
});


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
