<?php
/**
 * Template per: categorie, tag, archivi data, ricerca
 * Copre: category.php, tag.php, archive.php, search.php
 *
 * @package dspr
 */

get_header();

// Titolo e descrizione della pagina
if ( is_category() ) {
    $arch_title = single_cat_title( '', false );
    $arch_desc  = category_description();
    $arch_count = (int) get_queried_object()->count;
    $arch_type  = 'category';
} elseif ( is_tag() ) {
    $arch_title = single_tag_title( '', false );
    $arch_desc  = tag_description();
    $arch_count = (int) get_queried_object()->count;
    $arch_type  = 'tag';
} elseif ( is_search() ) {
    $arch_title = 'Ricerca: ' . get_search_query();
    $arch_desc  = '';
    $arch_count = (int) $wp_query->found_posts;
    $arch_type  = 'search';
} elseif ( is_author() ) {
    $arch_title = get_the_author_meta( 'display_name', get_queried_object_id() );
    $arch_desc  = get_the_author_meta( 'description', get_queried_object_id() );
    $arch_count = (int) count_user_posts( get_queried_object_id(), 'post' );
    $arch_type  = 'author';
} else {
    $arch_title = get_the_archive_title();
    $arch_desc  = get_the_archive_description();
    $arch_count = (int) $wp_query->found_posts;
    $arch_type  = 'archive';
}
?>

<div class="dspr-archive">

    <!-- ── HEADER ARCHIVIO ── -->
    <div class="dispari-cat-header">
        <nav class="dispari-cat-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a>
            <span class="dispari-cat-breadcrumb__sep" aria-hidden="true">›</span>
            <span class="dispari-cat-breadcrumb__current"><?php echo esc_html( $arch_title ); ?></span>
        </nav>
        <div class="dispari-cat-header__inner">
            <div class="dispari-cat-header__title-row">
                <h1 class="dispari-cat-header__title">
                    <span class="dispari-cat-header__title-accent"><?php echo esc_html( $arch_title ); ?></span>
                </h1>
                <?php if ( $arch_count > 0 ) : ?>
                <span class="dispari-cat-count">
                    <?php echo number_format_i18n( $arch_count ); ?>
                    <?php echo $arch_count === 1 ? 'articolo' : 'articoli'; ?>
                </span>
                <?php endif; ?>
            </div>
            <?php if ( $arch_desc ) : ?>
            <p class="dispari-cat-desc"><?php echo wp_kses_post( $arch_desc ); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="dispari-cat-wrap">

        <?php if ( have_posts() ) : ?>

            <!-- ── HERO: primo post ── -->
            <?php the_post();
            $hero_id    = get_the_ID();
            $hero_thumb = has_post_thumbnail( $hero_id ) ? get_the_post_thumbnail_url( $hero_id, 'ildispari-hero' ) : '';
            $hero_cats  = get_the_category( $hero_id );
            $hero_cat   = ! empty( $hero_cats ) ? esc_html( $hero_cats[0]->name ) : '';
            $hero_date  = get_the_date( 'j F Y' );
            $hero_auth  = get_the_author();
            ?>
            <div class="dispari-cat-hero">
                <a href="<?php the_permalink(); ?>" class="dispari-hero-link<?php echo $hero_thumb ? '' : ' dispari-no-thumb'; ?>">
                    <?php if ( $hero_thumb ) : ?>
                    <img class="dispari-hero-img" src="<?php echo esc_url( $hero_thumb ); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                    <div class="dispari-hero-overlay">
                        <?php if ( $hero_cat ) : ?>
                        <span class="dispari-hero-cat"><?php echo $hero_cat; ?></span>
                        <?php endif; ?>
                        <h2 class="dispari-hero-title"><?php the_title(); ?></h2>
                        <div class="dispari-hero-meta">
                            <?php echo esc_html( $hero_auth ); ?> &middot; <?php echo $hero_date; ?>
                        </div>
                    </div>
                </a>
            </div>

            <!-- ── SECONDARI: 2 post ── -->
            <?php if ( have_posts() ) : ?>
            <div class="dispari-cat-secondary">
            <?php
            $sec_count = 0;
            while ( have_posts() && $sec_count < 2 ) : the_post();
                $sec_id    = get_the_ID();
                $sec_thumb = has_post_thumbnail( $sec_id ) ? get_the_post_thumbnail_url( $sec_id, 'ildispari-inline' ) : '';
                $sec_cats  = get_the_category( $sec_id );
                $sec_cat   = ! empty( $sec_cats ) ? esc_html( $sec_cats[0]->name ) : '';
                $sec_date  = get_the_date( 'j M Y' );
                $sec_auth  = get_the_author();
            ?>
                <div class="dispari-sec-card">
                    <a href="<?php the_permalink(); ?>" class="dispari-sec-link">
                        <?php if ( $sec_thumb ) : ?>
                        <div class="dispari-sec-img-wrap">
                            <img class="dispari-sec-img" src="<?php echo esc_url( $sec_thumb ); ?>" alt="<?php the_title_attribute(); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="dispari-sec-body">
                            <?php if ( $sec_cat ) : ?><div class="dispari-sec-cat"><?php echo $sec_cat; ?></div><?php endif; ?>
                            <h3 class="dispari-sec-title"><?php the_title(); ?></h3>
                            <div class="dispari-sec-meta"><?php echo esc_html( $sec_auth ); ?> &middot; <?php echo $sec_date; ?></div>
                        </div>
                    </a>
                </div>
            <?php $sec_count++; endwhile; ?>
            </div>
            <?php endif; ?>

            <!-- ── GRIGLIA: post restanti ── -->
            <?php if ( have_posts() ) : ?>
            <div class="dispari-cat-grid">
            <?php while ( have_posts() ) : the_post();
                $g_id    = get_the_ID();
                $g_thumb = has_post_thumbnail( $g_id ) ? get_the_post_thumbnail_url( $g_id, 'ildispari-card' ) : '';
                $g_cats  = get_the_category( $g_id );
                $g_cat   = ! empty( $g_cats ) ? esc_html( $g_cats[0]->name ) : '';
                $g_date  = get_the_date( 'j M Y' );
                $g_auth  = get_the_author();
            ?>
                <div class="dispari-grid-card">
                    <a href="<?php the_permalink(); ?>" class="dispari-grid-link">
                        <?php if ( $g_thumb ) : ?>
                        <div class="dispari-grid-img-wrap">
                            <img class="dispari-grid-img" src="<?php echo esc_url( $g_thumb ); ?>" alt="<?php the_title_attribute(); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="dispari-grid-body">
                            <?php if ( $g_cat ) : ?><div class="dispari-grid-cat"><?php echo $g_cat; ?></div><?php endif; ?>
                            <h3 class="dispari-grid-title"><?php the_title(); ?></h3>
                            <div class="dispari-grid-meta"><?php echo esc_html( $g_auth ); ?> &middot; <?php echo $g_date; ?></div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <!-- ── PAGINAZIONE ── -->
            <div class="dispari-pagination">
                <?php
                echo paginate_links([
                    'type'      => 'list',
                    'prev_text' => '‹',
                    'next_text' => '›',
                ]);
                ?>
            </div>

        <?php else : ?>

            <!-- ── NESSUN RISULTATO ── -->
            <div class="dispari-no-results">
                <p><?php
                    if ( is_search() ) {
                        echo 'Nessun risultato per <strong>' . esc_html( get_search_query() ) . '</strong>. Prova con parole chiave diverse.';
                    } else {
                        echo 'Nessun articolo trovato in questa sezione.';
                    }
                ?></p>
                <?php get_search_form(); ?>
            </div>

        <?php endif; ?>

    </div><!-- .dispari-cat-wrap -->

</div><!-- .dspr-archive -->

<?php
get_sidebar();
get_footer();
