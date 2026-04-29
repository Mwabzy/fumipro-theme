<?php get_header(); ?>

<!-- ══════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ -->
<?php
$hero_slides = get_option('fumitech_hero_slides', []);
$slide_count  = count($hero_slides);
?>
<section class="hero" id="hero">

    <!-- Background image slides -->
    <div class="hero-slides-bg" id="hero-slides-bg" aria-hidden="true">
        <?php if ($slide_count) :
            foreach ($hero_slides as $i => $slide) :
                $img_url = wp_get_attachment_image_url($slide['id'], 'full') ?: $slide['url'];
        ?>
            <div class="hero-slide<?php echo $i === 0 ? ' active' : ''; ?>"
                 style="background-image:url('<?php echo esc_url($img_url); ?>')">
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Gradient overlay (always present) -->
    <div class="hero-overlay" aria-hidden="true"></div>

    <!-- Content -->
    <div class="hero-content">
        <div class="hero-badge">&#x2714; Licensed &amp; Certified Professionals</div>
        <h1 class="hero-title" id="hero-headline">Protecting Your Home &amp; Business from Pests</h1>
        <p class="hero-sub" id="hero-sub">Fast, effective, and eco-friendly fumigation services. Available 24/7 for emergency treatments across Nairobi and surrounding areas.</p>
        <div class="hero-btns">
            <button type="button" class="btn-primary" id="hero-book-btn" data-modal="book-now">Book Now</button>
            <a href="tel:+254734865099" class="btn-outline" id="hero-call-btn">&#x1F4DE; Call Now</a>
        </div>
        <div class="hero-trust">
            <div class="trust-item"><strong>10+</strong><span>Years Experience</span></div>
            <div class="trust-item"><strong>100%</strong><span>Safe Products</span></div>
            <div class="trust-item"><strong>24/7</strong><span>Emergency Response</span></div>
        </div>
    </div>

    <!-- Floating badges -->
    <div class="hero-visual" aria-hidden="true">
        <div class="hero-card-stack">
            <div class="hero-badge-float hero-badge-float--1">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Licensed</span>
            </div>
            <div class="hero-badge-float hero-badge-float--2">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                <span>Eco-Friendly</span>
            </div>
            <div class="hero-badge-float hero-badge-float--3">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                <span>24/7 Response</span>
            </div>
            <div class="hero-circle"></div>
            <!-- Fumigation mist particles (purely decorative) -->
            <span class="fumi-particle fumi-p-1"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-2"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-3"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-4"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-5"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-6"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-7"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-8"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-9"  aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-10" aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-11" aria-hidden="true"></span>
            <span class="fumi-particle fumi-p-12" aria-hidden="true"></span>
        </div>
    </div>

    <!-- Carousel dots (only when multiple slides exist) -->
    <?php if ($slide_count > 1) : ?>
    <div class="hero-dots" id="hero-dots" role="tablist" aria-label="Hero slides">
        <?php foreach ($hero_slides as $i => $slide) : ?>
        <button class="hero-dot<?php echo $i === 0 ? ' active' : ''; ?>"
                role="tab"
                aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                aria-label="Slide <?php echo $i + 1; ?>"
                data-index="<?php echo $i; ?>">
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($slide_count) : ?>
    <script>
    window.fumiHeroSlides = <?php echo wp_json_encode(array_map(function($s) {
        return [
            'headline' => $s['headline'] ?? '',
            'sub'      => $s['sub'] ?? '',
        ];
    }, $hero_slides)); ?>;
    </script>
    <?php endif; ?>

</section>

<!-- ══════════════════════════════════════════════
     EMERGENCY BANNER
══════════════════════════════════════════════════ -->
<div class="emergency-banner" id="emergency-banner">
    <div class="emergency-inner">
        <span class="emerg-pulse"></span>
        <span>&#x1F6A8; 24/7 Emergency Pest Control Available — We respond within 2 hours</span>
        <a href="tel:+254734865099" class="emerg-link" id="emerg-call">Call Now: +254 734 865 099 &rarr;</a>
    </div>
</div>

<!-- ══════════════════════════════════════════════
     SERVICES
══════════════════════════════════════════════════ -->
<?php
/* ── Try to load Pest Control services from CPT ─────────────────────────── */
$_hp_pc_term  = get_term_by('name', 'Pest Control', 'service_category');
$_hp_svc_args = [
    'post_type'   => 'fumitech_service',
    'numberposts' => 6,
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
];
if ($_hp_pc_term && !is_wp_error($_hp_pc_term)) {
    $_hp_svc_args['tax_query'] = [['taxonomy' => 'service_category', 'field' => 'term_id', 'terms' => $_hp_pc_term->term_id]];
}
$_hp_services    = get_posts($_hp_svc_args);
$_hp_use_dynamic = !empty($_hp_services);
$_svc_url        = get_post_type_archive_link('fumitech_service') ?: home_url('/services');
?>
<section class="section section--white" id="services">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">What We Do</span>
            <h2 class="section-title">Complete Pest Control Solutions</h2>
            <p class="section-sub">From residential homes to large commercial facilities, we handle every pest problem with precision and care.</p>
        </div>
        <div class="services-grid">

            <?php if ($_hp_use_dynamic) :
                foreach ($_hp_services as $_hp_svc) :
                    $_hp_icon    = get_post_meta($_hp_svc->ID, '_fumitech_icon', true) ?: '🛡️';
                    $_hp_excerpt = $_hp_svc->post_excerpt ?: wp_trim_words(strip_tags($_hp_svc->post_content), 20, '');
                    $_hp_link    = esc_url($_svc_url . '#service-' . $_hp_svc->ID);
            ?>
            <div class="service-card" id="svc-<?php echo esc_attr($_hp_svc->post_name); ?>">
                <div class="service-icon"><?php echo esc_html($_hp_icon); ?></div>
                <h3><?php echo esc_html($_hp_svc->post_title); ?></h3>
                <?php if ($_hp_excerpt) : ?>
                    <p><?php echo esc_html($_hp_excerpt); ?></p>
                <?php endif; ?>
                <a href="<?php echo $_hp_link; ?>" class="card-link">Learn more &rarr;</a>
            </div>
            <?php endforeach;

            else : /* ── Static fallback ─────────────────────────────── */ ?>


            <div class="service-card" id="svc-pubhealth">
                <div class="service-icon">&#x1F9BA;</div>
                <h3>Public Health Pest Management</h3>
                <p>Vector control programs targeting disease-carrying pests — mosquitoes, flies, and rodents — in communities and public spaces.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-structural">
                <div class="service-icon">&#x1F3D7;&#xFE0F;</div>
                <h3>Structural Pest Management</h3>
                <p>Pre-construction and post-construction treatments protecting buildings from termites, wood borers, and soil-dwelling pests.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-fumigation">
                <div class="service-icon">&#x1F33F;</div>
                <h3>Agricultural Fumigation</h3>
                <p>Pre-shipment and post-harvest fumigation for flowers, fruits, vegetables, and grains. KEPHIS-compliant and export-ready.</p>
                <a href="<?php echo esc_url(home_url('/#agri-fumigation')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <?php endif; ?>

        </div>

        <?php if ($_hp_use_dynamic) : ?>
        <div style="text-align:center;margin-top:40px;">
            <a href="<?php echo esc_url($_svc_url); ?>" class="btn-primary">View All Services &rarr;</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ══════════════════════════════════════════════
     WHO WE SERVE
══════════════════════════════════════════════════ -->
<section class="section section--sky-light" id="sectors">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">Who We Serve</span>
            <h2 class="section-title">Trusted Across Every Industry</h2>
            <p class="section-sub">From family homes to large commercial operations, we deliver tailored pest management solutions to every sector.</p>
        </div>
        <div class="sectors-grid">

            <div class="sector-card sector-card--featured">
                <div class="sector-icon">&#x1F33E;</div>
                <h3>Agriculture</h3>
                <p>Pre-shipment and post-harvest fumigation for cut flowers, fresh fruits, vegetables, and stored grains. KEPHIS-compliant &amp; export-ready.</p>
                <a href="<?php echo esc_url(home_url('/#agri-fumigation')); ?>" class="sector-link">Learn more &rarr;</a>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F3ED;</div>
                <h3>Food Facilities</h3>
                <p>Grain stores, mills, food processing plants, and cold storage units — protecting perishables and inventory from pest infestation.</p>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F3E5;</div>
                <h3>Healthcare Facilities</h3>
                <p>Hospitals and clinics require strict hygiene standards. We deliver discreet, WHO-approved pest control meeting infection control protocols.</p>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F37D;&#xFE0F;</div>
                <h3>Hotels &amp; Restaurants</h3>
                <p>Protect your reputation with scheduled pest management programs for hospitality establishments and food service operations.</p>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F3DB;&#xFE0F;</div>
                <h3>Public Institutions</h3>
                <p>Schools, government offices, and public spaces fumigated with minimal disruption — keeping communities safe and compliant.</p>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F4BC;</div>
                <h3>Private Institutions</h3>
                <p>Corporate offices, banks, and private facilities served with tailored, confidential pest management plans and service reports.</p>
            </div>

            <div class="sector-card">
                <div class="sector-icon">&#x1F3EA;</div>
                <h3>Industries &amp; Retail Stores</h3>
                <p>Factories, warehouses, and retail stores. Safeguard stock, equipment, and workspaces from pest damage and regulatory non-compliance.</p>
            </div>

        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     AGRICULTURAL FUMIGATION CALLOUT
══════════════════════════════════════════════════ -->
<section class="section agri-fumi-section" id="agri-fumigation">
    <div class="section-inner agri-fumi-inner">

        <div class="agri-fumi-text">
            <span class="section-label section-label--light">Speciality Service</span>
            <h2 class="section-title agri-fumi-title">Flowers, Fruits &amp; Vegetable Fumigation</h2>
            <p class="agri-fumi-intro">Kenya is one of the world's leading exporters of cut flowers and fresh produce. Meeting international phytosanitary requirements isn't optional — it's the difference between a shipment clearing customs and one being turned back at the border.</p>
            <p class="agri-fumi-intro">Fumitech Services Limited provides KEPHIS-compliant pre-shipment and post-harvest fumigation treatments designed specifically for horticulture and agriculture operations.</p>
            <ul class="agri-checklist">
                <li>Pre-shipment fumigation for cut flowers, fresh fruits &amp; vegetables</li>
                <li>Post-harvest grain, seed &amp; cereal storage fumigation</li>
                <li>Approved fumigants: Methyl Bromide, Aluminium Phosphide &amp; Magnesium Phosphine</li>
                <li>Cold room &amp; controlled atmosphere facility treatment</li>
                <li>Quarantine pest treatment &amp; phytosanitary certificate support</li>
                <li>Fumigation sheets, gas monitoring &amp; safety equipment on-site</li>
                <li>Compliance with KEPHIS, EPPO, and importing-country standards</li>
            </ul>
            <div class="agri-fumi-btns">
                <button type="button" class="btn-white" data-modal="book-now">Book Inspection</button>
                <a href="tel:+254734865099" class="btn-outline-white">&#x1F4DE; Call Now</a>
            </div>
        </div>

        <div class="agri-fumi-visual" aria-hidden="true">
            <div class="agri-fumi-badge-wrap">
                <div class="agri-badge agri-badge--1">
                    <span class="agri-badge-icon">&#x1F337;</span>
                    <div>
                        <strong>Cut Flowers</strong>
                        <span>Roses, Carnations, Lilies</span>
                    </div>
                </div>
                <div class="agri-badge agri-badge--2">
                    <span class="agri-badge-icon">&#x1F353;</span>
                    <div>
                        <strong>Fresh Produce</strong>
                        <span>Fruits &amp; Vegetables</span>
                    </div>
                </div>
                <div class="agri-badge agri-badge--3">
                    <span class="agri-badge-icon">&#x1F33E;</span>
                    <div>
                        <strong>Stored Grains</strong>
                        <span>Cereals, Seeds, Pulses</span>
                    </div>
                </div>
                <div class="agri-badge agri-badge--4">
                    <span class="agri-badge-icon">&#x2705;</span>
                    <div>
                        <strong>KEPHIS Compliant</strong>
                        <span>Export-ready treatment</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════
     FEATURED PRODUCTS
══════════════════════════════════════════════════ -->
<?php
$featured_products = new WP_Query([
    'post_type'      => 'fumitech_product',
    'posts_per_page' => 8,
    'meta_query'     => [[
        'key'     => '_fumitech_featured',
        'value'   => '1',
        'compare' => '=',
    ]],
    'orderby' => 'date',
    'order'   => 'DESC',
]);
if ($featured_products->have_posts()) : ?>
<section class="section section--sky-light" id="featured-products">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">Shop With Us</span>
            <h2 class="section-title">Featured Products</h2>
            <p class="section-sub">Professional pest control products available for direct purchase. Order instantly via WhatsApp.</p>
        </div>
        <div class="hp-product-grid">
            <?php while ($featured_products->have_posts()) : $featured_products->the_post();
                $price   = get_post_meta(get_the_ID(), '_fumitech_price', true);
                $badge   = get_post_meta(get_the_ID(), '_fumitech_badge', true);
                $wa_msg  = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
                if (!$wa_msg) $wa_msg = "Hi, I'd like to order: " . get_the_title();
                $wa_url  = 'https://wa.me/254734865099?text=' . rawurlencode($wa_msg);
            ?>
            <article class="product-card hp-product-card">
                <?php if ($badge) : ?>
                    <span class="product-badge"><?php echo esc_html($badge); ?></span>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="product-img-wrap">
                    <?php if (has_post_thumbnail()) :
                        the_post_thumbnail('medium', ['class' => 'product-img', 'alt' => get_the_title()]);
                    else : ?>
                        <div class="product-img-placeholder">
                            <svg viewBox="0 0 80 80" fill="none"><rect width="80" height="80" rx="12" fill="#f0f9ff"/><path d="M20 58L32 40l8 10 7-9L60 58H20z" fill="#bae6fd"/><circle cx="52" cy="30" r="8" fill="#7dd3fc"/></svg>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="product-body">
                    <h3 class="product-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($price) : ?>
                        <div class="product-price">Ksh <strong><?php echo esc_html($price); ?></strong></div>
                    <?php endif; ?>
                </div>
                <div class="product-footer">
                    <a href="<?php the_permalink(); ?>" class="product-details-btn">Details</a>
                    <a href="<?php echo esc_url($wa_url); ?>" class="product-wa-btn" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Order
                    </a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <div style="text-align:center;margin-top:36px;">
            <a href="<?php echo esc_url(get_post_type_archive_link('fumitech_product')); ?>" class="btn-primary">
                View All Products &rarr;
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════
     ABOUT US TEASER
══════════════════════════════════════════════════ -->
<section class="section section--white" id="about">
    <div class="section-inner about-teaser-inner">
        <div class="about-teaser-visual" aria-hidden="true">
            <div class="about-teaser-img-box">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <circle cx="40" cy="40" r="38" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                    <path d="M40 14C30 28 20 30 14 28C14 42 22 56 40 62C58 56 66 42 66 28C60 30 50 28 40 14Z" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.7)" stroke-width="2"/>
                    <polyline points="28,42 36,50 54,32" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
                <p class="about-teaser-img-label">Fumitech Services</p>
            </div>
            <div class="about-teaser-stats">
                <div class="about-stat"><strong>10+</strong><span>Years in Business</span></div>
                <div class="about-stat"><strong>2</strong><span>Licensed Operators</span></div>
                <div class="about-stat"><strong>Nairobi</strong><span>&amp; All Kenya</span></div>
            </div>
        </div>
        <div class="about-teaser-text">
            <span class="section-label">About Us</span>
            <h2 class="section-title" style="text-align:left;">We Are Fumitech Services Limited</h2>
            <p class="about-teaser-body">Founded in Nairobi, Fumitech Services Limited is a fully licensed and certified pest control company serving homes, businesses, and industries across Kenya. Our team of trained professionals uses WHO&#8209;approved, eco&#8209;friendly products to deliver fast, effective, and lasting pest control solutions.</p>
            <p class="about-teaser-body">We believe every space deserves to be safe and pest&#8209;free — and we stand behind every treatment with a <strong>30&#8209;day satisfaction guarantee</strong>.</p>
            <div class="about-teaser-badges">
                <span class="about-badge">&#x2714; PCPB Licensed</span>
                <span class="about-badge">&#x2714; Fully Insured</span>
                <span class="about-badge">&#x2714; 24/7 Response</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     WHY CHOOSE US
══════════════════════════════════════════════════ -->
<section class="section section--sky-light" id="why-us">
    <div class="section-inner why-inner">
        <div class="why-visual" aria-hidden="true">
            <div class="why-img-box">
                <div class="why-img-content">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="36" cy="36" r="34" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                        <path d="M36 10C27 24 18 26 12 24C12 38 20 52 36 58C52 52 60 38 60 24C54 26 45 24 36 10Z" fill="rgba(255,255,255,0.18)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                        <path d="M28 44c-1 2-1 4 0 6M36 45v6M44 44c1 2 1 4 0 6" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="36" cy="33" r="8" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="1.5"/>
                    </svg>
                    <p class="why-img-label">Trusted Experts</p>
                </div>
                <div class="why-badge-box">
                    <strong>10+</strong>
                    <span>Years Serving Nairobi</span>
                </div>
            </div>
        </div>
        <div class="why-text">
            <span class="section-label">Why Choose Fumitech</span>
            <h2 class="section-title why-h2">Trusted by Thousands of Homes &amp; Businesses</h2>
            <p class="section-sub why-sub">We combine industry expertise with cutting-edge techniques to deliver lasting pest control solutions.</p>
            <ul class="why-list">
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>Licensed &amp; Certified</strong><p>Certified by national pest control authorities and fully insured.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>Eco-Friendly Products</strong><p>WHO-approved, family and pet-safe formulations every time.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>30-Day Guarantee</strong><p>All treatments come with a full satisfaction guarantee.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>24/7 Emergency Response</strong><p>Severe infestations don't wait — neither do we.</p></div>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     PROCESS
══════════════════════════════════════════════════ -->
<section class="section section--blue" id="process">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label section-label--light">Our Process</span>
            <h2 class="section-title section-title--white">Simple. Effective. Guaranteed.</h2>
            <p class="section-sub section-sub--light">From first call to final follow-up — here's how we work.</p>
        </div>
        <div class="process-grid">
            <div class="process-step" id="step-1">
                <div class="step-num">01</div>
                <h3>Book Inspection</h3>
                <p>Call or fill our form. We schedule a free site inspection at your convenience.</p>
            </div>
            <div class="process-step" id="step-2">
                <div class="step-num">02</div>
                <h3>Treatment Plan</h3>
                <p>Our expert designs a targeted plan specifically for your property and pest type.</p>
            </div>
            <div class="process-step" id="step-3">
                <div class="step-num">03</div>
                <h3>Professional Treatment</h3>
                <p>Our certified team applies the treatment using safe, proven techniques.</p>
            </div>
            <div class="process-step" id="step-4">
                <div class="step-num">04</div>
                <h3>Follow-Up &amp; Guarantee</h3>
                <p>We follow up to ensure the problem is fully resolved under our guarantee.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     TESTIMONIALS
══════════════════════════════════════════════════ -->
<section class="section section--white" id="testimonials">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">Customer Reviews</span>
            <h2 class="section-title">What Our Clients Say</h2>
        </div>
        <div class="testi-grid">
            <div class="testi-card" id="testi-1">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"Fumitech treated our office for cockroaches and the results were incredible. Professional team, very thorough. Highly recommend!"</p>
                <strong>Mary N.</strong>
                <span>Westlands, Nairobi</span>
            </div>
            <div class="testi-card" id="testi-2">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"We had a severe bed bug problem. Fumitech came the next morning and within 48 hours we had zero activity. Amazing service!"</p>
                <strong>James M.</strong>
                <span>Kilimani</span>
            </div>
            <div class="testi-card" id="testi-3">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"Reliable, honest, and effective. They set up a monthly prevention plan for us and we haven't had any pest issues since."</p>
                <strong>Sarah K.</strong>
                <span>Industrial Area</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     CONTACT / ENQUIRY FORM
══════════════════════════════════════════════════ -->
<section class="section section--sky-light" id="contact">
    <div class="section-inner contact-section-inner">

        <!-- Left: contact details -->
        <div class="contact-info">
            <span class="section-label">Get in Touch</span>
            <h2 class="section-title" style="text-align:left;">Send Us an Enquiry</h2>
            <p class="section-sub" style="text-align:left;margin:0 0 32px;">Have a question or want a quote? Fill in the form and we&rsquo;ll respond via WhatsApp or email as soon as possible.</p>

            <div class="contact-detail-list">
                <div class="contact-detail-item">
                    <div class="contact-detail-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
                    </div>
                    <div>
                        <strong>Phone</strong>
                        <a href="tel:+254734865099">+254 734 865 099</a><br>
                        <a href="tel:+254791165382">+254 791 165 382</a>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-detail-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <div>
                        <strong>Email</strong>
                        <a href="mailto:info@fumitechservices.co.ke">info@fumitechservices.co.ke</a>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-detail-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div>
                        <strong>WhatsApp</strong>
                        <a href="https://wa.me/254734865099" target="_blank" rel="noopener noreferrer">+254 734 865 099</a>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-detail-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                    </div>
                    <div>
                        <strong>Hours</strong>
                        <span>Mon – Sat: 7am – 6pm</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: enquiry form -->
        <div class="contact-form-box">
            <form class="enquiry-form" id="enquiry-form" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="enq-name">Full Name <span class="req">*</span></label>
                        <input type="text" id="enq-name" name="name" required placeholder="Your name">
                    </div>
                    <div class="form-group">
                        <label for="enq-phone">Phone <span class="req">*</span></label>
                        <input type="tel" id="enq-phone" name="phone" required placeholder="+254 700 000 000">
                    </div>
                </div>
                <div class="form-group">
                    <label for="enq-email">Email Address</label>
                    <input type="email" id="enq-email" name="email" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label for="enq-subject">Subject <span class="req">*</span></label>
                    <select id="enq-subject" name="subject" required>
                        <option value="">Select a topic&hellip;</option>
                        <option>Request a Quote</option>
                        <option>Service Enquiry</option>
                        <option>Product Enquiry</option>
                        <option>Complaint / Feedback</option>
                        <option>Partnership / Business</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="enq-message">Message <span class="req">*</span></label>
                    <textarea id="enq-message" name="message" rows="4" required placeholder="Tell us how we can help you&hellip;"></textarea>
                </div>
                <button type="submit" class="btn-primary enquiry-submit">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Send via WhatsApp
                </button>
            </form>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════
     CTA BAND
══════════════════════════════════════════════════ -->
<section class="cta-band" id="cta-band">
    <div class="cta-band-inner">
        <h2>Ready for a Pest-Free Property?</h2>
        <p>Get a free inspection and quote today. Our expert team is standing by.</p>
        <div class="cta-btns">
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-white" id="cta-quote-btn">Get a Free Quote</a>
            <a href="tel:+254734865099" class="btn-outline-white" id="cta-call-btn">&#x1F4DE; +254 734 865 099</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
