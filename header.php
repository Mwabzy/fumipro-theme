<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fumitech Services Limited — Licensed, certified, and trusted pest control services across Nairobi and Kenya. Fast, effective, and eco-friendly fumigation available 24/7.">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ══════════════════════════════════════════════
     QUICK LINKS BAR
══════════════════════════════════════════════════ -->
<div class="quick-links-bar" id="quick-links-bar">
    <div class="ql-inner">
        <div class="ql-left">
            <a href="tel:+254734865099" class="ql-item" id="ql-phone">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
                +254 734 865 099
            </a>
            <a href="mailto:info@fumitechservices.co.ke" class="ql-item" id="ql-email">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                info@fumitechservices.co.ke
            </a>
            <span class="ql-item ql-divider" id="ql-hours">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                Mon–Sat: 7am – 6pm
            </span>
        </div>
        <div class="ql-right">
            <a href="#" class="ql-item ql-social" id="ql-fb" aria-label="Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
            <a href="#" class="ql-item ql-social" id="ql-wa" aria-label="WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <button type="button" class="ql-cta" id="ql-quote" data-modal="book-now">Book Now</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════ -->
<?php
/* ── Service nav data (grouped by service_category) ─────────────────────── */
$_nav_svc_cats = get_terms(['taxonomy' => 'service_category', 'hide_empty' => false, 'orderby' => 'name']);
$_nav_svc_data = [];  // [ 'Category Name' => [ ['id'=>, 'title'=>, 'slug'=>], … ] ]
if (!empty($_nav_svc_cats) && !is_wp_error($_nav_svc_cats)) {
    foreach ($_nav_svc_cats as $_nc) {
        $_nc_posts = get_posts([
            'post_type'   => 'fumitech_service',
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'tax_query'   => [['taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $_nc->term_id]],
        ]);
        if (!empty($_nc_posts)) {
            $_nav_svc_data[$_nc->name] = array_map(fn($p) => [
                'id'    => $p->ID,
                'title' => $p->post_title,
                'slug'  => $p->post_name,
            ], $_nc_posts);
        }
    }
}
$_svc_archive_url = get_post_type_archive_link('fumitech_service') ?: home_url('/services');

/* ── Product nav data (parent categories + children) ────────────────────── */
$_nav_prod_archive_url = get_post_type_archive_link('fumitech_product') ?: home_url('/products');
$_nav_prod_parents     = get_terms(['taxonomy' => 'product_category', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name']);
$_nav_prod_data        = [];
if (!empty($_nav_prod_parents) && !is_wp_error($_nav_prod_parents)) {
    foreach ($_nav_prod_parents as $_pp) {
        $_pp_kids = get_terms(['taxonomy' => 'product_category', 'parent' => $_pp->term_id, 'hide_empty' => false, 'orderby' => 'name']);
        $_nav_prod_data[] = [
            'term'     => $_pp,
            'children' => (!is_wp_error($_pp_kids) && !empty($_pp_kids)) ? $_pp_kids : [],
        ];
    }
}
?>

<header class="site-header" id="site-header">
    <div class="header-inner">

        <!-- Brand row: logo + mobile hamburger -->
        <div class="header-brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" id="site-logo">
                <?php
                $_logo_id  = (int) get_option('fumitech_logo_id', 0);
                $_logo_url = $_logo_id
                    ? (wp_get_attachment_image_url($_logo_id, 'full') ?: get_option('fumitech_logo_url', ''))
                    : get_option('fumitech_logo_url', '');
                if (!$_logo_url) $_logo_url = get_template_directory_uri() . '/images/logo.png';
                ?>
                <img src="<?php echo esc_url($_logo_url); ?>" alt="Fumitech Services Limited" class="site-logo-img" id="site-logo-img">
            </a>
            <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>

        <!-- Desktop Nav -->
        <nav class="desktop-nav" id="desktop-nav">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '<ul class="nav-list">%3$s</ul>',
                'fallback_cb'    => function() use ($_nav_svc_data, $_svc_archive_url, $_nav_prod_data, $_nav_prod_archive_url) {
                    $h = home_url('/');

                    /* ── Build Services dropdown HTML ─────────────────── */
                    $svc_dd = '';
                    if (!empty($_nav_svc_data)) {
                        foreach ($_nav_svc_data as $cat_name => $items) {
                            $svc_dd .= '<li class="has-submenu"><a href="#">' . esc_html($cat_name) . ' <span class="nav-arrow nav-arrow--right">&#9658;</span></a><ul class="submenu">';
                            foreach ($items as $item) {
                                $svc_dd .= '<li><a href="' . esc_url($_svc_archive_url . '#service-' . $item['id']) . '">' . esc_html($item['title']) . '</a></li>';
                            }
                            $svc_dd .= '</ul></li>';
                        }
                    } else {
                        /* Static fallback — Pest Control links to homepage anchors */
                        $svc_dd = '
                          <li class="has-submenu">
                            <a href="#">Pest Control <span class="nav-arrow nav-arrow--right">&#9658;</span></a>
                            <ul class="submenu">
                              <li><a href="' . esc_url($h . '#svc-termite') . '">Termite Control</a></li>
                              <li><a href="' . esc_url($h . '#svc-rodent') . '">Rodent Extermination</a></li>
                              <li><a href="' . esc_url($h . '#svc-bedbug') . '">Bed Bug Treatment</a></li>
                              <li><a href="' . esc_url($h . '#svc-cockroach') . '">Cockroach Control</a></li>
                              <li><a href="' . esc_url($h . '#svc-spider') . '">Spider &amp; Insect Control</a></li>
                              <li><a href="' . esc_url($h . '#svc-commercial') . '">Commercial Fumigation</a></li>
                              <li><a href="' . esc_url($h . '#svc-pubhealth') . '">Public Health Pest Management</a></li>
                              <li><a href="' . esc_url($h . '#svc-structural') . '">Structural Pest Management</a></li>
                              <li><a href="' . esc_url($h . '#agri-fumigation') . '">Agricultural Fumigation</a></li>
                            </ul>
                          </li>
                          <li class="has-submenu">
                            <a href="#">Consultancy <span class="nav-arrow nav-arrow--right">&#9658;</span></a>
                            <ul class="submenu">
                              <li><a href="' . esc_url($h . 'services') . '">Pesticide Registration</a></li>
                              <li><a href="' . esc_url($h . 'services') . '">Product Development</a></li>
                              <li><a href="' . esc_url($h . 'services') . '">Regulatory Policy Insights</a></li>
                            </ul>
                          </li>';
                    }

                    /* ── Build Products dropdown HTML ────────────────── */
                    $prod_dd = '';
                    foreach ($_nav_prod_data as $_pg) {
                        $pt      = $_pg['term'];
                        $kids    = $_pg['children'];
                        $two_col = (count($kids) > 6) ? ' submenu--two-col' : '';
                        $prod_dd .= '<li class="has-submenu"><a href="' . esc_url(get_term_link($pt)) . '">' . esc_html($pt->name) . ' <span class="nav-arrow nav-arrow--right">&#9658;</span></a>';
                        if (!empty($kids)) {
                            $prod_dd .= '<ul class="submenu' . $two_col . '">';
                            foreach ($kids as $_kid) {
                                $prod_dd .= '<li><a href="' . esc_url(get_term_link($_kid)) . '">' . esc_html($_kid->name) . '</a></li>';
                            }
                            $prod_dd .= '</ul>';
                        }
                        $prod_dd .= '</li>';
                    }

                    echo '
                    <ul class="nav-list">
                      <li><a href="' . esc_url($h) . '">Home</a></li>
                      <li><a href="' . esc_url($h . '#about') . '">About Us</a></li>

                      <li class="has-dropdown">
                        <a href="' . esc_url($_nav_prod_archive_url) . '">Products <span class="nav-arrow">&#9660;</span></a>
                        <ul class="dropdown">
                          ' . $prod_dd . '
                        </ul>
                      </li>

                      <li class="has-dropdown">
                        <a href="' . esc_url($_svc_archive_url) . '">Services <span class="nav-arrow">&#9660;</span></a>
                        <ul class="dropdown">
                          ' . $svc_dd . '
                        </ul>
                      </li>

                      <li class="has-dropdown">
                        <a href="' . esc_url($h . 'pests') . '">Pests <span class="nav-arrow">&#9660;</span></a>
                        <ul class="dropdown">
                          <li class="has-submenu">
                            <a href="#">Crawling Insects <span class="nav-arrow nav-arrow--right">&#9658;</span></a>
                            <ul class="submenu">
                              <li><a href="#">Ants</a></li>
                              <li><a href="#">Bed Bugs</a></li>
                              <li><a href="#">Cockroaches</a></li>
                              <li><a href="#">Fleas</a></li>
                              <li><a href="#">Spiders</a></li>
                              <li><a href="#">Silverfish</a></li>
                            </ul>
                          </li>
                          <li class="has-submenu">
                            <a href="#">Flying Insects <span class="nav-arrow nav-arrow--right">&#9658;</span></a>
                            <ul class="submenu">
                              <li><a href="#">Flies</a></li>
                              <li><a href="#">Mosquitoes &amp; Midges</a></li>
                              <li><a href="#">Moths</a></li>
                              <li><a href="#">Wasps</a></li>
                            </ul>
                          </li>
                          <li class="has-submenu">
                            <a href="#">Rodents <span class="nav-arrow nav-arrow--right">&#9658;</span></a>
                            <ul class="submenu">
                              <li><a href="#">Mice</a></li>
                              <li><a href="#">Rats</a></li>
                            </ul>
                          </li>
                          <li><a href="#">Termites</a></li>
                          <li><a href="#">Birds &amp; Snakes</a></li>
                        </ul>
                      </li>

                      <li><a href="' . esc_url($h . 'products') . '">Shop</a></li>
                      <li><a href="' . esc_url($h . '#contact') . '">Contact Us</a></li>
                    </ul>';
                },
            ]);
            ?>
        </nav>

        <!-- Search -->
        <div class="header-search" id="header-search">
            <button class="search-toggle" id="search-toggle" aria-label="Toggle search" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
            <div class="search-drawer" id="search-drawer" aria-hidden="true">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" name="s" id="search-input" class="search-input" placeholder="Search products, services&hellip;" autocomplete="off" aria-label="Search">
                    <button type="submit" class="search-submit" aria-label="Submit search">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Mobile nav drawer -->
    <div class="mobile-nav" id="mobile-nav" aria-hidden="true">

        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <a href="<?php echo esc_url(home_url('/#about')); ?>">About Us</a>

        <!-- Products -->
        <div class="mob-section">
            <div class="mob-header">
                <a href="<?php echo esc_url($_nav_prod_archive_url); ?>">Products</a>
                <button class="mob-toggle" aria-expanded="false">+</button>
            </div>
            <div class="mob-dropdown">
                <?php foreach ($_nav_prod_data as $_mob_pg) :
                    $mob_pt   = $_mob_pg['term'];
                    $mob_kids = $_mob_pg['children'];
                ?>
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="<?php echo esc_url(get_term_link($mob_pt)); ?>"><?php echo esc_html($mob_pt->name); ?></a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <?php if (!empty($mob_kids)) : ?>
                    <div class="mob-dropdown">
                        <?php foreach ($mob_kids as $_mob_kid) : ?>
                        <a href="<?php echo esc_url(get_term_link($_mob_kid)); ?>"><?php echo esc_html($_mob_kid->name); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Services -->
        <div class="mob-section">
            <div class="mob-header">
                <a href="<?php echo esc_url($_svc_archive_url); ?>">Services</a>
                <button class="mob-toggle" aria-expanded="false">+</button>
            </div>
            <div class="mob-dropdown">
                <?php if (!empty($_nav_svc_data)) :
                    foreach ($_nav_svc_data as $mob_cat => $mob_items) : ?>
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#"><?php echo esc_html($mob_cat); ?></a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <?php foreach ($mob_items as $mob_item) : ?>
                        <a href="<?php echo esc_url($_svc_archive_url . '#service-' . $mob_item['id']); ?>">
                            <?php echo esc_html($mob_item['title']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                    <?php endforeach;
                else : ?>
                <!-- Static fallback -->
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#">Pest Control</a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <a href="<?php echo esc_url(home_url('/#svc-termite')); ?>">Termite Control</a>
                        <a href="<?php echo esc_url(home_url('/#svc-rodent')); ?>">Rodent Extermination</a>
                        <a href="<?php echo esc_url(home_url('/#svc-bedbug')); ?>">Bed Bug Treatment</a>
                        <a href="<?php echo esc_url(home_url('/#svc-cockroach')); ?>">Cockroach Control</a>
                        <a href="<?php echo esc_url(home_url('/#svc-spider')); ?>">Spider &amp; Insect Control</a>
                        <a href="<?php echo esc_url(home_url('/#svc-commercial')); ?>">Commercial Fumigation</a>
                        <a href="<?php echo esc_url(home_url('/#svc-pubhealth')); ?>">Public Health Pest Management</a>
                        <a href="<?php echo esc_url(home_url('/#svc-structural')); ?>">Structural Pest Management</a>
                        <a href="<?php echo esc_url(home_url('/#agri-fumigation')); ?>">Agricultural Fumigation</a>
                    </div>
                </div>
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#">Consultancy</a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <a href="<?php echo esc_url(home_url('/services')); ?>">Pesticide Registration</a>
                        <a href="<?php echo esc_url(home_url('/services')); ?>">Product Development</a>
                        <a href="<?php echo esc_url(home_url('/services')); ?>">Regulatory Policy Insights</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pests -->
        <div class="mob-section">
            <div class="mob-header">
                <a href="<?php echo esc_url(home_url('/pests')); ?>">Pests</a>
                <button class="mob-toggle" aria-expanded="false">+</button>
            </div>
            <div class="mob-dropdown">
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#">Crawling Insects</a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <a href="#">Ants</a>
                        <a href="#">Bed Bugs</a>
                        <a href="#">Cockroaches</a>
                        <a href="#">Fleas</a>
                        <a href="#">Spiders</a>
                        <a href="#">Silverfish</a>
                    </div>
                </div>
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#">Flying Insects</a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <a href="#">Flies</a>
                        <a href="#">Mosquitoes &amp; Midges</a>
                        <a href="#">Moths</a>
                        <a href="#">Wasps</a>
                    </div>
                </div>
                <div class="mob-section">
                    <div class="mob-header">
                        <a href="#">Rodents</a>
                        <button class="mob-toggle" aria-expanded="false">+</button>
                    </div>
                    <div class="mob-dropdown">
                        <a href="#">Mice</a>
                        <a href="#">Rats</a>
                    </div>
                </div>
                <a href="#">Termites</a>
                <a href="#">Birds &amp; Snakes</a>
            </div>
        </div>

        <a href="<?php echo esc_url(home_url('/shop')); ?>">Shop</a>
        <a href="<?php echo esc_url(home_url('/#contact')); ?>">Contact Us</a>
        <form role="search" method="get" class="mob-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" class="mob-search-input" placeholder="Search products, services&hellip;" autocomplete="off" aria-label="Search">
            <button type="submit" class="mob-search-btn" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    </div>
</header>

<!-- ══════════════════════════════════════════════
     BOOK NOW MODAL (global — available on every page)
══════════════════════════════════════════════════ -->
<div class="modal-overlay" id="book-now-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
    <div class="modal-dialog">
        <button class="modal-close" id="modal-close" aria-label="Close booking form">&times;</button>
        <div class="modal-header">
            <span class="modal-icon">&#x1F4CB;</span>
            <h2 class="modal-title" id="modal-title">Book a Service</h2>
            <p class="modal-subtitle">Fill in your details and we&rsquo;ll confirm via WhatsApp</p>
        </div>
        <form class="booking-form" id="booking-form" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label for="book-name">Full Name <span class="req">*</span></label>
                    <input type="text" id="book-name" name="name" required placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label for="book-phone">Phone Number <span class="req">*</span></label>
                    <input type="tel" id="book-phone" name="phone" required placeholder="+254 700 000 000">
                </div>
            </div>
            <div class="form-group">
                <label for="book-email">Email Address</label>
                <input type="email" id="book-email" name="email" placeholder="your@email.com">
            </div>
            <div class="form-group">
                <label for="book-service">Service Needed <span class="req">*</span></label>
                <select id="book-service" name="service" required>
                    <option value="">Select a service&hellip;</option>
                    <?php if (!empty($_nav_svc_data)) :
                        foreach ($_nav_svc_data as $_modal_cat => $_modal_items) : ?>
                        <optgroup label="<?php echo esc_attr($_modal_cat); ?>">
                            <?php foreach ($_modal_items as $_modal_item) : ?>
                            <option value="<?php echo esc_attr($_modal_item['title']); ?>"><?php echo esc_html($_modal_item['title']); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach;
                    else : /* ── Static fallback ── */ ?>
                    <optgroup label="Pest Control">
                        <option>Termite Control</option>
                        <option>Rodent Extermination</option>
                        <option>Bed Bug Treatment</option>
                        <option>Cockroach Control</option>
                        <option>Spider &amp; Insect Control</option>
                        <option>Commercial Fumigation</option>
                        <option>Public Health Pest Management</option>
                        <option>Structural Pest Management</option>
                        <option>Agricultural Fumigation</option>
                    </optgroup>
                    <optgroup label="Consultancy">
                        <option>Consultancy on Pesticide Registration</option>
                        <option>Product Development</option>
                        <option>Regulatory Policy Insights</option>
                    </optgroup>
                    <?php endif; ?>
                    <option value="other">Other / Not sure</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="book-location">Property Location <span class="req">*</span></label>
                    <input type="text" id="book-location" name="location" required placeholder="e.g. Westlands, Nairobi">
                </div>
                <div class="form-group">
                    <label for="book-date">Preferred Date</label>
                    <input type="date" id="book-date" name="date">
                </div>
            </div>
            <div class="form-group">
                <label for="book-message">Additional Notes</label>
                <textarea id="book-message" name="message" rows="3" placeholder="Describe the pest problem or any specific requirements&hellip;"></textarea>
            </div>
            <button type="submit" class="btn-primary booking-submit">
                <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Confirm Booking via WhatsApp
            </button>
        </form>
    </div>
</div>
