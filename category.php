<?php
/**
 * Il Dispari Child — category.php
 * Layout completo pagina categoria:
 * 1. [dispari_cat_header]  — shortcode plugin ildispari-inner-pages
 * 2. Hero (primo articolo)
 * 3. 2 articoli secondari
 * 4. Banner: adrotate group="2" + RTBuzz native
 * 5. Griglia articoli (offset 3, limit 24)
 * 6. Secondo banner
 * 7. Paginazione WordPress nativa
 */

get_header();

$cat_id = get_queried_object_id();
$paged  = max( 1, get_query_var('paged') );

// Query principale
$args = array(
    'cat'                 => $cat_id,
    'posts_per_page'      => 27,
    'paged'               => $paged,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
);
$query = new WP_Query( $args );
$posts = $query->posts;

$hero      = isset( $posts[0] ) ? $posts[0] : null;
$secondari = array_slice( $posts, 1, 2 );
$griglia   = array_slice( $posts, 3 );
?>

    <?php echo do_shortcode('[dispari_cat_header]'); ?>

    <div class="dispari-cat-wrap">

        <?php if ( $hero ) : ?>
        <!-- HERO -->
        <div class="dispari-cat-hero">
            <a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="dispari-hero-link">
                <?php if ( has_post_thumbnail( $hero ) ) : ?>
                    <?php echo get_the_post_thumbnail( $hero, 'large', array( 'class' => 'dispari-hero-img' ) ); ?>
                <?php else : ?>
                    <div class="dispari-hero-img dispari-no-thumb"></div>
                <?php endif; ?>
                <div class="dispari-hero-overlay">
                    <span class="dispari-hero-cat"><?php
                        $cats = get_the_category( $hero->ID );
                        if ( $cats ) echo esc_html( $cats[0]->name );
                    ?></span>
                    <h2 class="dispari-hero-title"><?php echo esc_html( get_the_title( $hero ) ); ?></h2>
                    <div class="dispari-hero-meta"><?php echo get_the_date( 'j F Y', $hero ); ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ( $secondari ) : ?>
        <!-- 2 ARTICOLI SECONDARI -->
        <div class="dispari-cat-secondary">
            <?php foreach ( $secondari as $post ) : setup_postdata( $post ); ?>
            <article class="dispari-sec-card">
                <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="dispari-sec-link">
                    <div class="dispari-sec-img-wrap">
                        <?php if ( has_post_thumbnail( $post ) ) : ?>
                            <?php echo get_the_post_thumbnail( $post, 'medium_large', array( 'class' => 'dispari-sec-img' ) ); ?>
                        <?php else : ?>
                            <div class="dispari-sec-img dispari-no-thumb"></div>
                        <?php endif; ?>
                    </div>
                    <div class="dispari-sec-body">
                        <div class="dispari-sec-cat"><?php
                            $cats = get_the_category( $post->ID );
                            if ( $cats ) echo esc_html( $cats[0]->name );
                        ?></div>
                        <h3 class="dispari-sec-title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
                        <div class="dispari-sec-meta"><?php echo get_the_date( 'j F Y', $post ); ?></div>
                    </div>
                </a>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <!-- BANNER #1: AdRotate + RTBuzz native -->
        <div class="dispari-banner">
            <?php echo do_shortcode('[adrotate group="2"]'); ?>
            <div id="rtbuzz_native"></div><script>window.RTBuzz.cmd.push("native");</script>
        </div>

        <?php if ( $griglia ) : ?>
        <!-- GRIGLIA ARTICOLI -->
        <div class="dispari-cat-grid">
            <?php foreach ( $griglia as $post ) : setup_postdata( $post ); ?>
            <article class="dispari-grid-card">
                <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="dispari-grid-link">
                    <div class="dispari-grid-img-wrap">
                        <?php if ( has_post_thumbnail( $post ) ) : ?>
                            <?php echo get_the_post_thumbnail( $post, 'medium', array( 'class' => 'dispari-grid-img' ) ); ?>
                        <?php else : ?>
                            <div class="dispari-grid-img dispari-no-thumb"></div>
                        <?php endif; ?>
                    </div>
                    <div class="dispari-grid-body">
                        <div class="dispari-grid-cat"><?php
                            $cats = get_the_category( $post->ID );
                            if ( $cats ) echo esc_html( $cats[0]->name );
                        ?></div>
                        <h3 class="dispari-grid-title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
                        <div class="dispari-grid-meta"><?php echo get_the_date( 'j F Y', $post ); ?></div>
                    </div>
                </a>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <!-- BANNER #2: AdRotate + RTBuzz native -->
        <div class="dispari-banner">
            <?php echo do_shortcode('[adrotate group="2"]'); ?>
            <div id="rtbuzz_native2"></div><script>window.RTBuzz.cmd.push("native");</script>
        </div>

        <!-- PAGINAZIONE -->
        <div class="dispari-pagination">
            <?php
            echo paginate_links( array(
                'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $query->max_num_pages,
                'prev_text' => '‹',
                'next_text' => '›',
                'type'      => 'list',
            ) );
            ?>
        </div>

    </div><!-- /dispari-cat-wrap -->

<?php get_footer(); ?>
