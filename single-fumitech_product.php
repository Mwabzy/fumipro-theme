<?php get_header(); the_post();
$price  = get_post_meta(get_the_ID(), '_fumitech_price', true);
$badge  = get_post_meta(get_the_ID(), '_fumitech_badge', true);
$wa_msg = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
if (!$wa_msg) $wa_msg = "Hi, I'd like to order: " . get_the_title();
$wa_url = 'https://wa.me/254734865099?text=' . rawurlencode($wa_msg);
$cats   = get_the_terms(get_the_ID(), 'product_category');
?>

<main class="single-product-page">

    <div class="single-product-inner">

        <!-- Image column -->
        <div class="single-product-gallery">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', ['class' => 'single-product-img']); ?>
            <?php else : ?>
                <div class="single-product-img-placeholder">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="120" rx="16" fill="#f0f9ff"/>
                        <path d="M28 88L48 60l14 18 12-15L92 88H28z" fill="#bae6fd"/>
                        <circle cx="76" cy="44" r="14" fill="#7dd3fc"/>
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details column -->
        <div class="single-product-details">

            <?php if ($cats && !is_wp_error($cats)) : ?>
                <a href="<?php echo esc_url(get_term_link($cats[0])); ?>" class="single-product-cat">
                    <?php echo esc_html($cats[0]->name); ?>
                </a>
            <?php endif; ?>

            <?php if ($badge) : ?>
                <span class="product-badge product-badge--inline"><?php echo esc_html($badge); ?></span>
            <?php endif; ?>

            <h1 class="single-product-title"><?php the_title(); ?></h1>

            <?php if ($price) : ?>
                <div class="single-product-price">Ksh <strong><?php echo esc_html($price); ?></strong></div>
            <?php endif; ?>

            <div class="single-product-desc">
                <?php the_content(); ?>
            </div>

            <div class="single-product-actions">
                <a href="<?php echo esc_url($wa_url); ?>" class="product-wa-btn product-wa-btn--large" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Order via WhatsApp
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('fumitech_product')); ?>" class="product-back-btn">
                    &larr; Back to Products
                </a>
            </div>

            <div class="single-product-meta">
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Genuine Product</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Fast WhatsApp Response</span>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Delivery across Nairobi</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Related products -->
    <?php
    $related = new WP_Query([
        'post_type'      => 'fumitech_product',
        'posts_per_page' => 5,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
    ]);
    if ($related->have_posts()) : ?>
    <section class="related-products section section--white">
        <div class="section-inner">
            <h2 class="related-title">You May Also Like</h2>
            <div class="product-grid product-grid--compact">
                <?php while ($related->have_posts()) : $related->the_post();
                    $r_price  = get_post_meta(get_the_ID(), '_fumitech_price', true);
                    $r_badge  = get_post_meta(get_the_ID(), '_fumitech_badge', true);
                    $r_wa_msg = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
                    if (!$r_wa_msg) $r_wa_msg = "Hi, I'd like to order: " . get_the_title();
                    $r_wa_url = 'https://wa.me/254734865099?text=' . rawurlencode($r_wa_msg);
                ?>
                <article class="product-card">
                    <?php if ($r_badge) : ?><span class="product-badge"><?php echo esc_html($r_badge); ?></span><?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="product-img-wrap">
                        <?php if (has_post_thumbnail()) : the_post_thumbnail('medium', ['class' => 'product-img']);
                        else : ?><div class="product-img-placeholder"><svg viewBox="0 0 80 80" fill="none"><rect width="80" height="80" rx="12" fill="#f0f9ff"/><path d="M20 58L32 40l8 10 7-9L60 58H20z" fill="#bae6fd"/><circle cx="52" cy="30" r="8" fill="#7dd3fc"/></svg></div><?php endif; ?>
                    </a>
                    <div class="product-body">
                        <h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ($r_price) : ?><div class="product-price">Ksh <strong><?php echo esc_html($r_price); ?></strong></div><?php endif; ?>
                    </div>
                    <div class="product-footer">
                        <a href="<?php the_permalink(); ?>" class="product-details-btn">View</a>
                        <a href="<?php echo esc_url($r_wa_url); ?>" class="product-wa-btn" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Order
                        </a>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
