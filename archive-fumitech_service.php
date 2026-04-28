<?php get_header(); ?>

<main class="cpt-archive">

    <div class="archive-hero archive-hero--green">
        <div class="archive-hero-inner">
            <span class="section-label section-label--light">What We Offer</span>
            <h1>Our Services</h1>
            <p>Licensed pest control treatments and professional consultancy for homes, businesses, and the agricultural industry.</p>
        </div>
    </div>

    <section class="section section--white">
        <div class="section-inner">

            <?php
            /* ── Fetch all service categories ─────────────────────────── */
            $svc_cats = get_terms([
                'taxonomy'   => 'service_category',
                'hide_empty' => false,
                'orderby'    => 'name',
            ]);

            /* ── If categories exist, group services under them ─────────── */
            if (!empty($svc_cats) && !is_wp_error($svc_cats)) :

                foreach ($svc_cats as $cat) :
                    $services_in_cat = new WP_Query([
                        'post_type'      => 'fumitech_service',
                        'posts_per_page' => -1,
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                        'tax_query'      => [[
                            'taxonomy' => 'service_category',
                            'field'    => 'term_id',
                            'terms'    => $cat->term_id,
                        ]],
                    ]);

                    if (!$services_in_cat->have_posts()) continue;
                ?>

                <div class="svc-cat-section">
                    <div class="svc-cat-header">
                        <h2 class="svc-cat-title"><?php echo esc_html($cat->name); ?></h2>
                        <?php if ($cat->description) : ?>
                            <p class="svc-cat-desc"><?php echo esc_html($cat->description); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="service-cpt-grid">
                        <?php while ($services_in_cat->have_posts()) : $services_in_cat->the_post();
                            $price     = get_post_meta(get_the_ID(), '_fumitech_price', true);
                            $duration  = get_post_meta(get_the_ID(), '_fumitech_duration', true);
                            $icon      = get_post_meta(get_the_ID(), '_fumitech_icon', true);
                            $wa_msg    = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
                            $sub_items = get_post_meta(get_the_ID(), '_fumitech_sub_items', true);
                            if (!$wa_msg) $wa_msg = "Hi, I'd like to enquire about: " . get_the_title();
                            $wa_url    = 'https://wa.me/254734865099?text=' . rawurlencode($wa_msg);
                            $sub_lines = $sub_items ? array_filter(explode("\n", $sub_items)) : [];
                        ?>
                        <article class="service-cpt-card" id="service-<?php the_ID(); ?>">

                            <div class="service-cpt-top">
                                <div class="service-cpt-icon">
                                    <?php if ($icon) : ?>
                                        <?php echo esc_html($icon); ?>
                                    <?php elseif (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail', ['class' => 'service-cpt-thumb']); ?>
                                    <?php else : ?>
                                        🛡️
                                    <?php endif; ?>
                                </div>
                                <div class="service-cpt-meta">
                                    <?php if ($duration) : ?>
                                        <span class="service-meta-tag">⏱ <?php echo esc_html($duration); ?></span>
                                    <?php endif; ?>
                                    <?php if ($price) : ?>
                                        <span class="service-meta-tag service-meta-tag--price">From <?php echo esc_html($price); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <h3 class="service-cpt-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if (has_excerpt()) : ?>
                                <p class="service-cpt-desc"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($sub_lines)) : ?>
                                <ul class="service-sub-list">
                                    <?php foreach ($sub_lines as $line) : ?>
                                        <li><?php echo esc_html(trim($line)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <div class="service-cpt-footer">
                                <a href="<?php the_permalink(); ?>" class="service-learn-btn">Learn More &rarr;</a>
                                <a href="<?php echo esc_url($wa_url); ?>" class="product-wa-btn" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Book Now
                                </a>
                            </div>

                        </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>

                <?php endforeach; ?>

            <?php else : ?>

                <!-- Fallback: no categories set up yet — show all services flat -->
                <?php if (have_posts()) : ?>
                    <div class="service-cpt-grid">
                        <?php while (have_posts()) : the_post();
                            $price     = get_post_meta(get_the_ID(), '_fumitech_price', true);
                            $duration  = get_post_meta(get_the_ID(), '_fumitech_duration', true);
                            $icon      = get_post_meta(get_the_ID(), '_fumitech_icon', true);
                            $wa_msg    = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
                            $sub_items = get_post_meta(get_the_ID(), '_fumitech_sub_items', true);
                            if (!$wa_msg) $wa_msg = "Hi, I'd like to book: " . get_the_title();
                            $wa_url    = 'https://wa.me/254734865099?text=' . rawurlencode($wa_msg);
                            $sub_lines = $sub_items ? array_filter(explode("\n", $sub_items)) : [];
                        ?>
                        <article class="service-cpt-card" id="service-<?php the_ID(); ?>">
                            <div class="service-cpt-top">
                                <div class="service-cpt-icon">
                                    <?php if ($icon) echo esc_html($icon);
                                    elseif (has_post_thumbnail()) the_post_thumbnail('thumbnail', ['class' => 'service-cpt-thumb']);
                                    else echo '🛡️'; ?>
                                </div>
                                <div class="service-cpt-meta">
                                    <?php if ($duration) : ?><span class="service-meta-tag">⏱ <?php echo esc_html($duration); ?></span><?php endif; ?>
                                    <?php if ($price)    : ?><span class="service-meta-tag service-meta-tag--price">From <?php echo esc_html($price); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <h3 class="service-cpt-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if (has_excerpt()) : ?><p class="service-cpt-desc"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p><?php endif; ?>
                            <?php if (!empty($sub_lines)) : ?>
                                <ul class="service-sub-list">
                                    <?php foreach ($sub_lines as $line) : ?><li><?php echo esc_html(trim($line)); ?></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <div class="service-cpt-footer">
                                <a href="<?php the_permalink(); ?>" class="service-learn-btn">Learn More &rarr;</a>
                                <a href="<?php echo esc_url('https://wa.me/254734865099?text=' . rawurlencode($wa_msg)); ?>" class="product-wa-btn" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Book Now
                                </a>
                            </div>
                        </article>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <div class="archive-empty">
                        <div class="archive-empty-icon">🛠</div>
                        <h2>No services yet</h2>
                        <p>Services will appear here once added from the dashboard.</p>
                        <?php if (current_user_can('manage_options')) : ?>
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=fumitech_service')); ?>" class="btn-primary">
                                + Add First Service
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
