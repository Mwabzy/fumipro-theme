<?php
/**
 * WooCommerce shop page override — shows fumitech_product CPT products by category.
 */
defined('ABSPATH') || exit;

get_header();

$current_cat_id = isset($_GET['product_cat']) ? (int) $_GET['product_cat'] : 0;
$shop_url       = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
?>

<main class="cpt-archive">

    <div class="archive-hero">
        <div class="archive-hero-inner">
            <span class="section-label section-label--light">Our Products</span>
            <h1>Pest Control Products</h1>
            <p>Professional-grade equipment and chemicals available for purchase. Order easily via WhatsApp.</p>
        </div>
    </div>

    <section class="section section--white">
        <div class="section-inner">

            <?php
            $top_cats = get_terms([
                'taxonomy'   => 'product_category',
                'parent'     => 0,
                'hide_empty' => true,
                'orderby'    => 'name',
            ]);

            if (!empty($top_cats) && !is_wp_error($top_cats)) :
            ?>
            <div class="prod-cat-filter">
                <a href="<?php echo esc_url($shop_url); ?>"
                   class="prod-cat-btn<?php echo (!$current_cat_id) ? ' active' : ''; ?>">
                    All Products
                </a>
                <?php foreach ($top_cats as $cat) :
                    $is_active = ($current_cat_id === $cat->term_id);
                    $children  = get_terms([
                        'taxonomy'   => 'product_category',
                        'parent'     => $cat->term_id,
                        'hide_empty' => true,
                    ]);
                    $cat_url = add_query_arg('product_cat', $cat->term_id, $shop_url);
                ?>
                <a href="<?php echo esc_url($cat_url); ?>"
                   class="prod-cat-btn<?php echo $is_active ? ' active' : ''; ?>">
                    <?php echo esc_html($cat->name); ?>
                    <?php if (!empty($children) && !is_wp_error($children)) : ?>
                        <span style="margin-left:4px;font-size:10px;opacity:.6;">&#9660;</span>
                    <?php endif; ?>
                </a>
                <?php if (!empty($children) && !is_wp_error($children) && $is_active) : ?>
                <div class="prod-cat-children">
                    <?php foreach ($children as $child) :
                        $child_active = ($current_cat_id === $child->term_id);
                        $child_url    = add_query_arg('product_cat', $child->term_id, $shop_url);
                    ?>
                    <a href="<?php echo esc_url($child_url); ?>"
                       class="prod-cat-btn prod-cat-btn--sub<?php echo $child_active ? ' active' : ''; ?>">
                        <?php echo esc_html($child->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php
            $query_args = [
                'post_type'      => 'fumitech_product',
                'posts_per_page' => 12,
                'paged'          => max(1, get_query_var('paged')),
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ];

            if ($current_cat_id) {
                $query_args['tax_query'] = [[
                    'taxonomy'         => 'product_category',
                    'field'            => 'term_id',
                    'terms'            => $current_cat_id,
                    'include_children' => true,
                ]];
            }

            $products = new WP_Query($query_args);

            if ($products->have_posts()) :
            ?>
                <div class="product-grid">
                    <?php while ($products->have_posts()) : $products->the_post();
                        $price  = get_post_meta(get_the_ID(), '_fumitech_price', true);
                        $badge  = get_post_meta(get_the_ID(), '_fumitech_badge', true);
                        $wa_msg = get_post_meta(get_the_ID(), '_fumitech_wa_message', true);
                        if (!$wa_msg) $wa_msg = "Hi, I'd like to order: " . get_the_title();
                        $wa_url = 'https://wa.me/254734865099?text=' . rawurlencode($wa_msg);
                    ?>
                    <article class="product-card" id="product-<?php the_ID(); ?>">

                        <?php if ($badge) : ?>
                            <span class="product-badge"><?php echo esc_html($badge); ?></span>
                        <?php endif; ?>

                        <a href="<?php the_permalink(); ?>" class="product-img-wrap">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large', ['class' => 'product-img', 'alt' => get_the_title()]); ?>
                            <?php else : ?>
                                <div class="product-img-placeholder">
                                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="80" height="80" rx="12" fill="#f0f9ff"/>
                                        <path d="M20 58L32 40l8 10 7-9L60 58H20z" fill="#bae6fd"/>
                                        <circle cx="52" cy="30" r="8" fill="#7dd3fc"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="product-body">
                            <?php $cats = get_the_terms(get_the_ID(), 'product_category');
                            if ($cats && !is_wp_error($cats)) : ?>
                                <span class="product-cat"><?php echo esc_html($cats[0]->name); ?></span>
                            <?php endif; ?>
                            <h3 class="product-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <?php if (has_excerpt()) : ?>
                                <p class="product-desc"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
                            <?php endif; ?>
                            <?php if ($price) : ?>
                                <div class="product-price">Ksh <strong><?php echo esc_html($price); ?></strong></div>
                            <?php endif; ?>
                        </div>

                        <div class="product-footer">
                            <a href="<?php the_permalink(); ?>" class="product-details-btn">View Details</a>
                            <a href="<?php echo esc_url($wa_url); ?>" class="product-wa-btn" target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Order via WhatsApp
                            </a>
                        </div>

                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <div class="archive-pagination">
                    <?php
                    echo paginate_links([
                        'total'     => $products->max_num_pages,
                        'current'   => max(1, get_query_var('paged')),
                        'mid_size'  => 2,
                        'prev_text' => '&larr; Prev',
                        'next_text' => 'Next &rarr;',
                        'add_args'  => $current_cat_id ? ['product_cat' => $current_cat_id] : [],
                    ]);
                    ?>
                </div>

            <?php else : ?>
                <div class="archive-empty">
                    <div class="archive-empty-icon">📦</div>
                    <h2>No products yet</h2>
                    <p>Products will appear here once added from the dashboard.</p>
                    <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=fumitech_product')); ?>" class="btn-primary">
                            + Add First Product
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
