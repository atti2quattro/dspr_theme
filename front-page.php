<?php
/**
 * Homepage – Il Dispari Quotidiano
 * Template: front-page.php
 *
 * @package dspr
 */

get_header();

/* ============================================================
   HELPER FUNCTIONS
   ============================================================ */

function dspr_hp_thumb( $post_id, $size = 'large' ) {
    return has_post_thumbnail( $post_id )
        ? get_the_post_thumbnail_url( $post_id, $size )
        : '';
}

function dspr_hp_cat( $post_id ) {
    $cats = get_the_category( $post_id );
    return ! empty( $cats ) ? esc_html( $cats[0]->name ) : '';
}

function dspr_hp_cat_url( $post_id ) {
    $cats = get_the_category( $post_id );
    return ! empty( $cats ) ? get_term_link( $cats[0] ) : '#';
}

function dspr_hp_query( $args ) {
    $defaults = [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
    ];
    return new WP_Query( array_merge( $defaults, $args ) );
}

function dspr_hp_time( $post_id ) {
    return human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) . ' fa';
}

?>
<main class="dspr-home" id="primary">

<!-- ============================================================
     BADGE PARTITA LIVE
     Visibile da stato "pre" fino a 12 ore dopo "ended"
     ============================================================ -->
<?php
// Cerca partite attive: pre, live, ht, o ended da meno di 12 ore
$ilm_matches = get_posts([
    'post_type'      => 'ilm_match',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'orderby'        => 'meta_value',
    'meta_key'       => 'ilm_match_date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

$ilm_visibili = [];
foreach ( $ilm_matches as $ilm_post ) {
    $ilm_status   = get_post_meta( $ilm_post->ID, 'ilm_status', true ) ?: 'pre';
    $ilm_modified = get_post_modified_time( 'U', true, $ilm_post->ID );

    if ( $ilm_status === 'ended' ) {
        // Sparisce 12 ore dopo la fine
        if ( ( current_time('timestamp') - $ilm_modified ) > ( 12 * HOUR_IN_SECONDS ) ) {
            continue;
        }
    }

    $ilm_visibili[] = [
        'id'     => $ilm_post->ID,
        'status' => $ilm_status,
        'post'   => $ilm_post,
    ];
}

if ( ! empty( $ilm_visibili ) ) :
?>
<section class="dib-livematch" id="dib-livematch">
<?php foreach ( $ilm_visibili as $ilm ) :
    $ilm_id     = $ilm['id'];
    $ilm_status = $ilm['status'];
    $ilm_m      = function( $k ) use ( $ilm_id ) { return get_post_meta( $ilm_id, $k, true ); };

    $ilm_home     = $ilm_m('ilm_home_name') ?: '—';
    $ilm_away     = $ilm_m('ilm_away_name') ?: '—';
    $ilm_home_logo = $ilm_m('ilm_home_logo');
    $ilm_away_logo = $ilm_m('ilm_away_logo');
    $ilm_score_h  = $ilm_m('ilm_score_home') !== '' ? intval($ilm_m('ilm_score_home')) : '0';
    $ilm_score_a  = $ilm_m('ilm_score_away') !== '' ? intval($ilm_m('ilm_score_away')) : '0';
    $ilm_comp     = $ilm_m('ilm_competition');
    $ilm_minute   = $ilm_m('ilm_current_minute');
    $ilm_date_raw = $ilm_m('ilm_match_date');
    $ilm_url      = get_permalink( $ilm_id );

    $ilm_status_labels = [
        'pre'   => 'Pre-partita',
        '1T'    => '1° Tempo',
        'ht'    => 'Intervallo',
        '2T'    => '2° Tempo',
        'live'  => 'LIVE', // retrocompatibilità
        'ended' => 'Terminata',
    ];
    $ilm_label = $ilm_status_labels[ $ilm_status ] ?? $ilm_status;
    if ( ( $ilm_status === '1T' || $ilm_status === '2T' || $ilm_status === 'live' ) && $ilm_minute ) {
        $ilm_label .= ' - ' . $ilm_minute;
    }

    // Data formattata
    $ilm_data_fmt = '';
    if ( $ilm_date_raw ) {
        $mesi_ilm = ['','gen','feb','mar','apr','mag','giu','lug','ago','set','ott','nov','dic'];
        $ts_ilm   = strtotime( $ilm_date_raw );
        $ilm_data_fmt = date('j', $ts_ilm) . ' ' . $mesi_ilm[ intval(date('n', $ts_ilm)) ] . ' · ' . date('H:i', $ts_ilm);
    }
?>
<a class="dib-livematch__card"
   href="<?php echo esc_url( $ilm_url ); ?>"
   id="dib-lmc-<?php echo $ilm_id; ?>"
   data-match-id="<?php echo $ilm_id; ?>"
   data-status="<?php echo esc_attr( $ilm_status ); ?>">

    <!-- CTA — estrema sinistra -->
    <div class="dib-lmc__cta">Segui la diretta →</div>

    <!-- Scoreboard centrale -->
    <div class="dib-lmc__body">
        <div class="dib-lmc__scoreboard">

            <!-- Casa: nome a dx, logo tra nome e risultato -->
            <div class="dib-lmc__team">
                <span class="dib-lmc__name"><?php echo esc_html( $ilm_home ); ?></span>
                <?php if ( $ilm_home_logo ) : ?>
                    <img class="dib-lmc__logo" src="<?php echo esc_url( $ilm_home_logo ); ?>" alt="<?php echo esc_attr( $ilm_home ); ?>">
                <?php else : ?>
                    <span class="dib-lmc__logo dib-lmc__logo--ph">⚽</span>
                <?php endif; ?>
            </div>

            <!-- Risultato (sempre visibile, 0-0 in prepartita) -->
            <div class="dib-lmc__center">
                <div class="dib-lmc__score">
                    <span class="dib-lmc__digit dib-lmc__score-home"><?php echo intval( $ilm_m('ilm_score_home') ); ?></span>
                    <span class="dib-lmc__sep">-</span>
                    <span class="dib-lmc__digit dib-lmc__score-away"><?php echo intval( $ilm_m('ilm_score_away') ); ?></span>
                </div>
                <?php if ( $ilm_data_fmt ) : ?>
                <div class="dib-lmc__time"><?php echo esc_html( $ilm_data_fmt ); ?></div>
                <?php endif; ?>
            </div>

            <!-- Ospite: logo tra risultato e nome, nome a sx -->
            <div class="dib-lmc__team dib-lmc__team--away">
                <?php if ( $ilm_away_logo ) : ?>
                    <img class="dib-lmc__logo" src="<?php echo esc_url( $ilm_away_logo ); ?>" alt="<?php echo esc_attr( $ilm_away ); ?>">
                <?php else : ?>
                    <span class="dib-lmc__logo dib-lmc__logo--ph">⚽</span>
                <?php endif; ?>
                <span class="dib-lmc__name"><?php echo esc_html( $ilm_away ); ?></span>
            </div>

        </div><!-- .dib-lmc__scoreboard -->
    </div><!-- .dib-lmc__body -->

    <!-- Stato — estrema destra -->
    <div class="dib-lmc__status dib-lmc__status--<?php echo esc_attr( $ilm_status ); ?>">
        <?php echo esc_html( $ilm_label ); ?>
    </div>

</a>
<?php endforeach; ?>
</section><!-- .dib-livematch -->
<?php endif; ?>


<!-- ============================================================
     BENTO GALLERY UNIFICATA (zona2 + zona1)
     Layout secondo schema: mix di card zona2 e zona1
     ============================================================ -->
<?php
// Query zona2 (3 articoli: 1 hero + 2 featured)
$q_zona2 = dspr_hp_query([
    'tag'             => 'zona2',
    'posts_per_page'  => 3,
]);

// Query zona1 (6 articoli: 6 small)
$q_zona1 = dspr_hp_query([
    'tag'            => 'zona1',
    'posts_per_page' => 6,
]);

// Raccolgo gli articoli
$zona2_posts = [];
if ( $q_zona2->have_posts() ) {
    while ( $q_zona2->have_posts() ) {
        $q_zona2->the_post();
        $zona2_posts[] = get_post();
    }
    wp_reset_postdata();
}

$zona1_posts = [];
if ( $q_zona1->have_posts() ) {
    while ( $q_zona1->have_posts() ) {
        $q_zona1->the_post();
        $zona1_posts[] = get_post();
    }
    wp_reset_postdata();
}

// Ordine finale secondo mockup
$unified_posts = [];
if ( !empty($zona2_posts) && !empty($zona1_posts) ) {
    // RIGA 1
    // Box 1: zona2[0] - Hero 2×3
    $unified_posts[] = ['post' => $zona2_posts[0], 'class' => 'dib-bento-item--hero', 'source' => 'zona2'];
    
    // Box 2: zona1[0] - Small 1×1
    $unified_posts[] = ['post' => $zona1_posts[0], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    
    // Box 3: zona1[1] - Small 1×1
    if ( isset($zona1_posts[1]) ) {
        $unified_posts[] = ['post' => $zona1_posts[1], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
    
    // Box 4: zona2[1] - Featured 2×2 (prima featured zona2)
    if ( isset($zona2_posts[1]) ) {
        $unified_posts[] = ['post' => $zona2_posts[1], 'class' => 'dib-bento-item--featured', 'source' => 'zona2'];
    }
    
    // RIGA 2
    // Box 5: zona1[2] - Small 1×1
    if ( isset($zona1_posts[2]) ) {
        $unified_posts[] = ['post' => $zona1_posts[2], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
    
    // Box 6: zona1[3] - Small 1×1
    if ( isset($zona1_posts[3]) ) {
        $unified_posts[] = ['post' => $zona1_posts[3], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
    
    // Box 7: zona2[2] - Featured 2×2 (seconda featured zona2)
    if ( isset($zona2_posts[2]) ) {
        $unified_posts[] = ['post' => $zona2_posts[2], 'class' => 'dib-bento-item--featured', 'source' => 'zona2'];
    }
    
    // Box 8: zona1[4] - Small 1×1
    if ( isset($zona1_posts[4]) ) {
        $unified_posts[] = ['post' => $zona1_posts[4], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
    
    // Box 9: zona1[5] - Small 1×1
    if ( isset($zona1_posts[5]) ) {
        $unified_posts[] = ['post' => $zona1_posts[5], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
    
    // Eventuali altri (se ci sono)
    for ( $i = 3; $i < count($zona2_posts); $i++ ) {
        $unified_posts[] = ['post' => $zona2_posts[$i], 'class' => 'dib-bento-item--small', 'source' => 'zona2'];
    }
    for ( $i = 6; $i < count($zona1_posts); $i++ ) {
        $unified_posts[] = ['post' => $zona1_posts[$i], 'class' => 'dib-flash-bento--small', 'source' => 'zona1'];
    }
}

// Mostro la bento solo se ho articoli
if ( !empty($unified_posts) ) :
?>
<section class="dib-bento-unified">
    <div class="dib-bento-unified__label">
        <span class="dib-bento-unified__dot"></span>
        <span class="dib-bento-unified__label-text">In tempo reale</span>
    </div>
    
    <div class="dib-bento-unified__grid">
    <?php
    foreach ( $unified_posts as $item ) :
        $post = $item['post'];
        $class = $item['class'];
        $source = $item['source'];
        
        setup_postdata($post);
        
        $thumb = dspr_hp_thumb( $post->ID, 'ildispari-hero' );
        $cat = dspr_hp_cat( $post->ID );
        $time = dspr_hp_time( $post->ID );
        $is_new = ( current_time( 'timestamp' ) - get_the_time( 'U', $post->ID ) ) < 3600;
        
        // Determino base class
        $base_class = ( $source === 'zona2' ) ? 'dib-bento-item' : 'dib-flash-bento';
    ?>
        <a href="<?php the_permalink(); ?>" class="<?php echo $base_class . ' ' . $class; ?>">
            <div class="<?php echo $base_class; ?>__bg"<?php if ($thumb) echo ' style="background-image:url(' . esc_url($thumb) . ')"'; ?>>
                <?php if (!$thumb) : ?><span class="dib-bento-item__icon">📰</span><?php endif; ?>
            </div>
            <div class="<?php echo $base_class; ?>__overlay">
                <div class="<?php echo $base_class; ?>__top">
                    <?php if ($is_new && $source === 'zona1') : ?>
                        <span class="dib-flash-bento__badge">Nuovo</span>
                    <?php elseif ($cat) : ?>
                        <span class="<?php echo $base_class; ?>__cat"><?php echo $cat; ?></span>
                    <?php endif; ?>
                </div>
                <div class="<?php echo $base_class; ?>__bottom">
                    <h2 class="<?php echo $base_class; ?>__title"><?php the_title(); ?></h2>
                    <span class="<?php echo $base_class; ?>__time"><?php echo $time; ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE PRIMO PIANO: Griglia verticale 9:16
     ============================================================ -->
<?php
$q_pp = dspr_hp_query([
    'tag'            => 'primopiano',
    'posts_per_page' => 3,
]);
if ( $q_pp->have_posts() ) :
?>
<section class="dib-primopiano" data-section="primopiano">
    <div class="dib-primopiano__grid">
    <?php while ( $q_pp->have_posts() ) : $q_pp->the_post();
        $pp_thumb   = dspr_hp_thumb( get_the_ID(), 'ildispari-hero' );
        $pp_cat     = dspr_hp_cat( get_the_ID() );
        $pp_author  = get_the_author();
        $pp_date    = get_the_date( 'j M' );
    ?>
        <a href="<?php the_permalink(); ?>" class="dib-pp-card">
            <div class="dib-pp-card__bg"<?php if ( $pp_thumb ) echo ' style="background-image:url(' . esc_url( $pp_thumb ) . ')"'; ?>></div>
            <div class="dib-pp-card__overlay">
                <div class="dib-pp-card__top">
                    <?php if ( $pp_cat ) : ?><span class="dib-pp-card__badge"><?php echo $pp_cat; ?></span><?php endif; ?>
                </div>
                <div class="dib-pp-card__bottom">
                    <h3 class="dib-pp-card__title"><?php the_title(); ?></h3>
                    <div class="dib-pp-card__meta">
                        <span>📅 <?php echo $pp_date; ?></span>
                        <span>✍️ <?php echo esc_html( $pp_author ); ?></span>
                    </div>
                </div>
            </div>
        </a>
    <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>



<!-- ============================================================
     SEZIONE 4 — NAVCATS: Barra categorie sticky
     ============================================================ -->
<nav class="dib-navcats" aria-label="Categorie">
    <div class="dib-navcats__scroll">
        <a href="<?php echo home_url('/'); ?>
				 " class="dib-navcats__item dib-navcats__item--active">Tutto</a>
        <a href="<?php echo get_category_link( get_cat_ID('sport') ); ?>" class="dib-navcats__item">Sport</a>
        <a href="<?php echo get_category_link( get_cat_ID('politica') ); ?>" class="dib-navcats__item">Politica</a>
        <a href="<?php echo get_category_link( get_cat_ID('cronaca') ); ?>" class="dib-navcats__item">Cronaca</a>
        <a href="<?php echo get_category_link( get_cat_ID('societa') ); ?>" class="dib-navcats__item">Società</a>
        <a href="<?php echo get_category_link( get_cat_ID('procida') ); ?>" class="dib-navcats__item">Procida</a>
        <a href="<?php echo get_category_link( get_cat_ID('cultura') ); ?>" class="dib-navcats__item">Cultura</a>
        <a href="<?php echo get_category_link( get_cat_ID('magazine') ); ?>" class="dib-navcats__item">Magazine</a>
        <a href="<?php echo get_category_link( get_cat_ID('campania') ); ?>" class="dib-navcats__item">Regione</a>
    </div>
</nav>


<!-- ============================================================
     SEZIONE 5 — IN EVIDENZA: Hero + scroll card
     ============================================================ -->
<?php
$q_evidenza = dspr_hp_query([
    'category_name'  => 'in-evidenza',
    'posts_per_page' => 5,
]);
if ( $q_evidenza->have_posts() ) :
?>
<section class="dib-hs" data-section="in-evidenza" style="border-top:3px solid #f5620f;">
    <div class="dib-hs__header">
        <span class="dib-hs__tag" style="background:#f5620f;">In Evidenza</span>
        <a class="dib-hs__more" href="<?php echo get_category_link( get_cat_ID('in-evidenza') ); ?>">Tutto →</a>
    </div>
    <?php $hs1_i = 0; while ( $q_evidenza->have_posts() ) : $q_evidenza->the_post();
        $hs1_thumb  = dspr_hp_thumb( get_the_ID(), 'ildispari-hero' );
        $hs1_cat    = dspr_hp_cat( get_the_ID() );
        $hs1_author = get_the_author();
        $hs1_date   = get_the_date( 'j M' );

        if ( $hs1_i === 0 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-hs__hero">
                <div class="dib-hs__hero-img"<?php if ( $hs1_thumb ) echo ' style="background-image:url(' . esc_url( $hs1_thumb ) . ')"'; else echo ' class="dib-hs__hero-img--empty"'; ?>></div>
                <div class="dib-hs__hero-body">
                    <?php if ( $hs1_cat ) : ?><span class="dib-hs__hero-cat" style="color:#f5620f;"><?php echo $hs1_cat; ?></span><?php endif; ?>
                    <h3 class="dib-hs__hero-title"><?php the_title(); ?></h3>
                    <div class="dib-hs__hero-meta">
                        <span class="dib-hs__hero-author"><?php echo esc_html( $hs1_author ); ?></span>
                        <span class="dib-hs__hero-date"><?php echo $hs1_date; ?></span>
                    </div>
                </div>
            </a>
            <?php if ( $q_evidenza->post_count > 1 ) : ?><div class="dib-hs__scroll"><?php endif;

        elseif ( $hs1_i <= 4 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-hs__card">
                <div class="dib-hs__card-img"<?php if ( $hs1_thumb ) echo ' style="background-image:url(' . esc_url( $hs1_thumb ) . ')"'; else echo ' class="dib-hs__card-img--empty"'; ?>></div>
                <div class="dib-hs__card-body">
                    <?php if ( $hs1_cat ) : ?><span class="dib-hs__card-cat" style="color:#f5620f;"><?php echo $hs1_cat; ?></span><?php endif; ?>
                    <div class="dib-hs__card-title"><?php the_title(); ?></div>
                    <span class="dib-hs__card-date"><?php echo $hs1_date; ?></span>
                </div>
            </a>
        <?php endif;
        $hs1_i++;
    endwhile;
    if ( $q_evidenza->post_count > 1 ) : ?></div><?php endif;
    wp_reset_postdata();
    ?>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 6 — ACCADE SULLE ISOLE: Griglia 2 colonne
     ============================================================ -->
<?php
$q_isole = dspr_hp_query([
    'category_name'  => 'ischia',
    'posts_per_page' => 6,
]);
if ( $q_isole->have_posts() ) :
?>
<section class="dib-griglia" data-section="isole" style="border-top:3px solid #0a1628;">
    <div class="dib-griglia__header">
        <span class="dib-griglia__tag" style="background:#0a1628;">Accade sulle Isole</span>
        <a class="dib-griglia__more" href="<?php echo get_category_link( get_cat_ID('ischia') ); ?>">Tutto →</a>
    </div>
    <div class="dib-griglia__grid">
    <?php while ( $q_isole->have_posts() ) : $q_isole->the_post();
        $gi_thumb = dspr_hp_thumb( get_the_ID(), 'ildispari-card' );
        $gi_cat   = dspr_hp_cat( get_the_ID() );
        $gi_date  = get_the_date( 'j M' );
    ?>
        <a href="<?php the_permalink(); ?>" class="dib-griglia__card">
            <div class="dib-griglia__card-img"<?php if ( $gi_thumb ) echo ' style="background-image:url(' . esc_url( $gi_thumb ) . ')"'; else echo ' class="dib-griglia__card-img--empty"'; ?>></div>
            <div class="dib-griglia__card-body">
                <?php if ( $gi_cat ) : ?><span class="dib-griglia__card-cat" style="color:#f5620f;"><?php echo $gi_cat; ?></span><?php endif; ?>
                <div class="dib-griglia__card-title"><?php the_title(); ?></div>
                <span class="dib-griglia__card-date"><?php echo $gi_date; ?></span>
            </div>
        </a>
    <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 7 — STORIE D'ISCHIA: Hero + scroll card (verde)
     ============================================================ -->
<?php
$q_storie = dspr_hp_query([
    'category_name'  => 'storie-dischia',
    'posts_per_page' => 5,
]);
if ( $q_storie->have_posts() ) :
?>
<section class="dib-hs" data-section="storie" style="border-top:3px solid #2a6040;">
    <div class="dib-hs__header">
        <span class="dib-hs__tag" style="background:#2a6040;">Storie d'Ischia</span>
        <a class="dib-hs__more" href="<?php echo get_category_link( get_cat_ID('storie-dischia') ); ?>">Tutto →</a>
    </div>
    <?php $hs2_i = 0; while ( $q_storie->have_posts() ) : $q_storie->the_post();
        $hs2_thumb  = dspr_hp_thumb( get_the_ID(), 'ildispari-hero' );
        $hs2_cat    = dspr_hp_cat( get_the_ID() );
        $hs2_author = get_the_author();
        $hs2_date   = get_the_date( 'j M' );

        if ( $hs2_i === 0 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-hs__hero">
                <div class="dib-hs__hero-img"<?php if ( $hs2_thumb ) echo ' style="background-image:url(' . esc_url( $hs2_thumb ) . ')"'; ?>></div>
                <div class="dib-hs__hero-body">
                    <?php if ( $hs2_cat ) : ?><span class="dib-hs__hero-cat" style="color:#2a6040;"><?php echo $hs2_cat; ?></span><?php endif; ?>
                    <h3 class="dib-hs__hero-title"><?php the_title(); ?></h3>
                    <div class="dib-hs__hero-meta">
                        <span class="dib-hs__hero-author"><?php echo esc_html( $hs2_author ); ?></span>
                        <span class="dib-hs__hero-date"><?php echo $hs2_date; ?></span>
                    </div>
                </div>
            </a>
            <?php if ( $q_storie->post_count > 1 ) : ?><div class="dib-hs__scroll"><?php endif;

        elseif ( $hs2_i <= 4 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-hs__card">
                <div class="dib-hs__card-img"<?php if ( $hs2_thumb ) echo ' style="background-image:url(' . esc_url( $hs2_thumb ) . ')"'; ?>></div>
                <div class="dib-hs__card-body">
                    <?php if ( $hs2_cat ) : ?><span class="dib-hs__card-cat" style="color:#2a6040;"><?php echo $hs2_cat; ?></span><?php endif; ?>
                    <div class="dib-hs__card-title"><?php the_title(); ?></div>
                    <span class="dib-hs__card-date"><?php echo $hs2_date; ?></span>
                </div>
            </a>
        <?php endif;
        $hs2_i++;
    endwhile;
    if ( $q_storie->post_count > 1 ) : ?></div><?php endif;
    wp_reset_postdata();
    ?>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 8 — SOCIETÀ: Griglia 2 colonne (viola)
     ============================================================ -->
<?php
$q_societa = dspr_hp_query([
    'category_name'  => 'societa',
    'posts_per_page' => 6,
]);
if ( $q_societa->have_posts() ) :
?>
<section class="dib-griglia" data-section="societa" style="border-top:3px solid #8a2a6a;">
    <div class="dib-griglia__header">
        <span class="dib-griglia__tag" style="background:#8a2a6a;">Società</span>
        <a class="dib-griglia__more" href="<?php echo get_category_link( get_cat_ID('societa') ); ?>">Tutto →</a>
    </div>
    <div class="dib-griglia__grid">
    <?php while ( $q_societa->have_posts() ) : $q_societa->the_post();
        $gs_thumb = dspr_hp_thumb( get_the_ID(), 'ildispari-card' );
        $gs_cat   = dspr_hp_cat( get_the_ID() );
        $gs_date  = get_the_date( 'j M' );
    ?>
        <a href="<?php the_permalink(); ?>" class="dib-griglia__card">
            <div class="dib-griglia__card-img"<?php if ( $gs_thumb ) echo ' style="background-image:url(' . esc_url( $gs_thumb ) . ')"'; else echo ' class="dib-griglia__card-img--empty"'; ?>></div>
            <div class="dib-griglia__card-body">
                <?php if ( $gs_cat ) : ?><span class="dib-griglia__card-cat" style="color:#8a2a6a;"><?php echo $gs_cat; ?></span><?php endif; ?>
                <div class="dib-griglia__card-title"><?php the_title(); ?></div>
                <span class="dib-griglia__card-date"><?php echo $gs_date; ?></span>
            </div>
        </a>
    <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 9 — MAGAZINE: Lista verticale su sfondo scuro
     ============================================================ -->
<?php
$q_mag = dspr_hp_query([
    'category_name'  => 'magazine',
    'posts_per_page' => 4,
]);
if ( $q_mag->have_posts() ) :
?>
<section class="dib-magazine" data-section="magazine">
    <div class="dib-magazine__header">
        <span class="dib-magazine__tag">Magazine</span>
        <a class="dib-magazine__more" href="<?php echo get_category_link( get_cat_ID('magazine') ); ?>">Tutto →</a>
    </div>
    <div class="dib-magazine__list">
    <?php while ( $q_mag->have_posts() ) : $q_mag->the_post();
        $mag_thumb  = dspr_hp_thumb( get_the_ID(), 'ildispari-thumb' );
        $mag_author = get_the_author();
        $mag_date   = get_the_date( 'j M' );
    ?>
        <a href="<?php the_permalink(); ?>" class="dib-magazine__item">
            <div class="dib-magazine__item-img<?php echo $mag_thumb ? '' : ' dib-magazine__item-img--empty'; ?>"<?php if ( $mag_thumb ) echo ' style="background-image:url(' . esc_url( $mag_thumb ) . ')"'; ?>></div>
            <div class="dib-magazine__item-body">
                <span class="dib-magazine__item-label">Magazine</span>
                <div class="dib-magazine__item-title"><?php the_title(); ?></div>
                <span class="dib-magazine__item-meta"><?php echo esc_html( $mag_author ); ?> · <?php echo $mag_date; ?></span>
            </div>
        </a>
    <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 10a — ISCHIA CALCIO
     ============================================================ -->
<?php
$q_ischia = dspr_hp_query([
    'category_name'  => 'ischia-calcio',
    'posts_per_page' => 4,
]);
if ( $q_ischia->have_posts() ) :
?>
<section class="dib-sport" data-section="ischia-calcio">
    <div class="dib-sport__header" style="background:linear-gradient(135deg,#1a3a00 0%,#2a5a00 50%,#1a4a10 100%);">
        <div class="dib-sport__badge">
            <div class="dib-sport__icon">⚽</div>
            <div>
                <div class="dib-sport__nome">Ischia Calcio</div>
                <div class="dib-sport__campionato">Serie D · Girone H</div>
            </div>
        </div>
        <a class="dib-sport__more" href="<?php echo get_category_link( get_cat_ID('ischia-calcio') ); ?>">Tutto →</a>
    </div>
    <div class="dib-sport__body">
    <?php $sp1_i = 0; while ( $q_ischia->have_posts() ) : $q_ischia->the_post();
        $sp1_thumb = dspr_hp_thumb( get_the_ID(), 'ildispari-hero' );
        $sp1_cat   = dspr_hp_cat( get_the_ID() );
        $sp1_date  = get_the_date( 'j M' );

        if ( $sp1_i === 0 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-sport__hero">
                <div class="dib-sport__hero-img"<?php if ( $sp1_thumb ) echo ' style="background-image:url(' . esc_url( $sp1_thumb ) . ')"'; else echo ' style="background:linear-gradient(135deg,#1a3a00,#2a5a00)"'; ?>></div>
                <div class="dib-sport__hero-overlay">
                    <?php if ( $sp1_cat ) : ?><span class="dib-sport__hero-cat"><?php echo $sp1_cat; ?></span><?php endif; ?>
                    <h3 class="dib-sport__hero-title"><?php the_title(); ?></h3>
                    <span class="dib-sport__hero-date"><?php echo $sp1_date; ?></span>
                </div>
            </a>
            <?php if ( $q_ischia->post_count > 1 ) : ?><div class="dib-sport__scroll"><?php endif;

        elseif ( $sp1_i <= 3 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-sport__card">
                <div class="dib-sport__card-img"<?php if ( $sp1_thumb ) echo ' style="background-image:url(' . esc_url( $sp1_thumb ) . ')"'; else echo ' style="background:linear-gradient(135deg,#1a3a00,#2a5a00)"'; ?>></div>
                <div class="dib-sport__card-body">
                    <?php if ( $sp1_cat ) : ?><div class="dib-sport__card-cat" style="color:#2a6a00;"><?php echo $sp1_cat; ?></div><?php endif; ?>
                    <div class="dib-sport__card-title"><?php the_title(); ?></div>
                    <div class="dib-sport__card-date"><?php echo $sp1_date; ?></div>
                </div>
            </a>
        <?php endif;
        $sp1_i++;
    endwhile;
    if ( $q_ischia->post_count > 1 ) : ?></div><?php endif;
    wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 10b — REAL FORIO
     ============================================================ -->
<?php
$q_forio = dspr_hp_query([
    'category_name'  => 'real-forio',
    'posts_per_page' => 4,
]);
if ( $q_forio->have_posts() ) :
?>
<section class="dib-sport" data-section="real-forio">
    <div class="dib-sport__header" style="background:linear-gradient(135deg,#003300 0%,#006600 100%);">
        <div class="dib-sport__badge">
            <div class="dib-sport__icon">⚽</div>
            <div>
                <div class="dib-sport__nome">Real Forio</div>
                <div class="dib-sport__campionato">Eccellenza Campania</div>
            </div>
        </div>
        <a class="dib-sport__more" href="<?php echo get_category_link( get_cat_ID('real-forio') ); ?>">Tutto →</a>
    </div>
    <div class="dib-sport__body">
    <?php $sp2_i = 0; while ( $q_forio->have_posts() ) : $q_forio->the_post();
        $sp2_thumb = dspr_hp_thumb( get_the_ID(), 'ildispari-hero' );
        $sp2_cat   = dspr_hp_cat( get_the_ID() );
        $sp2_date  = get_the_date( 'j M' );

        if ( $sp2_i === 0 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-sport__hero">
                <div class="dib-sport__hero-img"<?php if ( $sp2_thumb ) echo ' style="background-image:url(' . esc_url( $sp2_thumb ) . ')"'; else echo ' style="background:linear-gradient(135deg,#003300,#006600)"'; ?>></div>
                <div class="dib-sport__hero-overlay">
                    <?php if ( $sp2_cat ) : ?><span class="dib-sport__hero-cat"><?php echo $sp2_cat; ?></span><?php endif; ?>
                    <h3 class="dib-sport__hero-title"><?php the_title(); ?></h3>
                    <span class="dib-sport__hero-date"><?php echo $sp2_date; ?></span>
                </div>
            </a>
            <?php if ( $q_forio->post_count > 1 ) : ?><div class="dib-sport__scroll"><?php endif;

        elseif ( $sp2_i <= 3 ) : ?>
            <a href="<?php the_permalink(); ?>" class="dib-sport__card">
                <div class="dib-sport__card-img"<?php if ( $sp2_thumb ) echo ' style="background-image:url(' . esc_url( $sp2_thumb ) . ')"'; else echo ' style="background:linear-gradient(135deg,#003300,#006600)"'; ?>></div>
                <div class="dib-sport__card-body">
                    <?php if ( $sp2_cat ) : ?><div class="dib-sport__card-cat" style="color:#006600;"><?php echo $sp2_cat; ?></div><?php endif; ?>
                    <div class="dib-sport__card-title"><?php the_title(); ?></div>
                    <div class="dib-sport__card-date"><?php echo $sp2_date; ?></div>
                </div>
            </a>
        <?php endif;
        $sp2_i++;
    endwhile;
    if ( $q_forio->post_count > 1 ) : ?></div><?php endif;
    wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 11 — SPORT SULLE ISOLE: Flash 9:16 su sfondo scuro
     ============================================================ -->
<?php
$q_sportisole = dspr_hp_query([
    'category_name'  => 'sport',
    'posts_per_page' => 6,
]);
if ( $q_sportisole->have_posts() ) :
$si_grads = $flash_grads;
?>
<section class="dib-flash" data-section="sport-isole" style="background:#1e2530;border-bottom:none;">
    <div class="dib-section-header">
        <div class="dib-section-title">
            <div class="dib-section-title__text" style="color:#fff;">Lo Sport sulle Isole</div>
            <div class="dib-section-title__sub" style="color:rgba(255,255,255,.35);">Basket · Judo · Nuoto · Altro</div>
        </div>
    </div>
    <div class="dib-flash__scroll">
    <?php $si_i = 0; while ( $q_sportisole->have_posts() ) : $q_sportisole->the_post();
        $si_thumb = dspr_hp_thumb( get_the_ID(), 'ildispari-card' );
        $si_cat   = dspr_hp_cat( get_the_ID() );
        $si_time  = dspr_hp_time( get_the_ID() );
        $si_new   = ( ( current_time( 'timestamp' ) - get_the_time( 'U' ) ) < 3600 );
        $si_bg    = $si_thumb
            ? 'background-image:url(' . esc_url( $si_thumb ) . ');background-size:cover;background-position:center;'
            : 'background:' . $si_grads[ $si_i % count( $si_grads ) ] . ';';
    ?>
        <a href="<?php the_permalink(); ?>" class="dib-flash-card">
            <div class="dib-flash-card__bg" style="<?php echo $si_bg; ?>"></div>
            <div class="dib-flash-card__overlay">
                <div class="dib-flash-card__top">
                    <?php if ( $si_new ) : ?><span class="dib-flash-card__badge">Nuovo</span><?php endif; ?>
                </div>
                <div class="dib-flash-card__bottom">
                    <?php if ( $si_cat ) : ?><div class="dib-flash-card__cat"><?php echo $si_cat; ?></div><?php endif; ?>
                    <div class="dib-flash-card__title"><?php the_title(); ?></div>
                    <div class="dib-flash-card__time"><?php echo $si_time; ?></div>
                </div>
            </div>
        </a>
    <?php $si_i++; endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     SEZIONE 12a � IN REGIONE: Feed RSS Adnkronos Campania
     ============================================================ -->
<?php $regional_news = fetch_regional_news(); ?>
<?php if ( !empty($regional_news) ) : ?>
<section class="dib-rassegna" data-section="regione" style="border-top:2px solid #e8e6e1;">
    <div class="dib-rassegna__header">
        <span class="dib-rassegna__tag" style="border-color:#0a1628;color:#0a1628;">In Regione</span>
        <span class="dib-rassegna__more">Adnkronos</span>
    </div>
    <div class="dib-rassegna__list">
    <?php foreach ($regional_news as $news_item) : ?>
        <a href="<?php echo esc_url($news_item['link']); ?>" target="_blank" rel="noopener" class="dib-rassegna__item">
            <?php if (!empty($news_item['image'])) : ?>
                <div class="dib-rassegna__item-img" style="background-image:url(<?php echo esc_url($news_item['image']); ?>)"></div>
            <?php else : ?>
                <div class="dib-rassegna__item-img dib-rassegna__item-img--empty"></div>
            <?php endif; ?>
            <div class="dib-rassegna__item-body">
                <div class="dib-rassegna__item-title"><?php echo esc_html($news_item['title']); ?></div>
                <span class="dib-rassegna__item-date"><?php echo esc_html($news_item['date']); ?></span>
            </div>
        </a>
    <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     SEZIONE 12b � NEWS DAL MONDO: Feed RSS Adnkronos Nazionale
     ============================================================ -->
<?php $world_news = fetch_world_news(); ?>
<?php if ( !empty($world_news) ) : ?>
<section class="dib-rassegna" data-section="mondo" style="background:#f2f2f0;border-top:2px solid #e8e6e1;">
    <div class="dib-rassegna__header">
        <span class="dib-rassegna__tag" style="border-color:#0a1628;color:#0a1628;">News dal Mondo</span>
        <span class="dib-rassegna__more">Adnkronos</span>
    </div>
    <div class="dib-rassegna__list">
    <?php foreach ($world_news as $news_item) : ?>
        <a href="<?php echo esc_url($news_item['link']); ?>" target="_blank" rel="noopener" class="dib-rassegna__item">
            <?php if (!empty($news_item['image'])) : ?>
                <div class="dib-rassegna__item-img" style="background-image:url(<?php echo esc_url($news_item['image']); ?>)"></div>
            <?php else : ?>
                <div class="dib-rassegna__item-img dib-rassegna__item-img--empty"></div>
            <?php endif; ?>
            <div class="dib-rassegna__item-body">
                <div class="dib-rassegna__item-title"><?php echo esc_html($news_item['title']); ?></div>
                <span class="dib-rassegna__item-date"><?php echo esc_html($news_item['date']); ?></span>
            </div>
        </a>
    <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

</main>

<?php get_sidebar(); ?>
<script>
window.addEventListener('load',()=>{
  if(window.innerWidth>480) return;
  const s=document.getElementById('primopiano-mobile');
  const c=document.getElementById('ppscroll-mobile');
  const a=document.querySelector('.primopiano-arrow');
  if(!s||!c) return;
  
  a.style.opacity='1';
  
  const expand=()=>{
    if(s.classList.contains('expanded')) return;
    console.log('AUTO EXPAND');
    s.classList.add('expanded');
    a.style.opacity='0';
    // Rimuovi debug border dopo 2s
    setTimeout(()=>s.style.border='',2000);
  };
  
  const shrink=()=>{
    s.classList.remove('expanded');
    a.style.opacity='1';
  };
  
  // AUTO-ESPANDI DOPO 1.5s
  setTimeout(expand,1500);
  
  // Trigger manuali aggiuntivi
  c.addEventListener('touchstart',expand,true);
  c.addEventListener('scroll',expand,true);
  
  // Shrink fine scroll
  let check=setInterval(()=>{
    if(c.scrollLeft>=c.scrollWidth-c.clientWidth-20){
      clearInterval(check);
      setTimeout(shrink,800);
    }
  },250);
});
</script>

<?php get_footer(); ?>
