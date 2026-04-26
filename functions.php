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
        '1.0'
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
