<?php
/**
 * Il Dispari – Template singola partita live
 * Layout editoriale: hero + titolo + intro + widget plugin
 *
 * @package dspr
 */

get_header();

while ( have_posts() ) : the_post();

    $post_id = get_the_ID();
    $m = function( $k ) use ( $post_id ) { return get_post_meta( $post_id, $k, true ); };

    // Dati partita
    $home_name   = $m('ilm_home_name')   ?: '—';
    $away_name   = $m('ilm_away_name')   ?: '—';
    $competition = $m('ilm_competition') ?: '';
    $intro       = $m('ilm_lineup_intro');
    $status      = $m('ilm_status') ?: 'pre';
    if ( $status === 'live' ) $status = '1T'; // retrocompatibilità

    // Hero
    $hero_id  = get_post_thumbnail_id( $post_id );
    $hero_src = $hero_id ? wp_get_attachment_image_url( $hero_id, 'ildispari-hero' ) : '';
    $hero_alt = $hero_id ? esc_attr( get_post_meta( $hero_id, '_wp_attachment_image_alt', true ) ) : esc_attr( $home_name . ' vs ' . $away_name );
    $hero_cap = $hero_id ? esc_html( wp_get_attachment_caption( $hero_id ) ) : '';

    // Stato label
    $status_labels = [
        'pre'   => 'Pre-partita',
        '1T'    => '1° Tempo',
        'ht'    => 'Intervallo',
        '2T'    => '2° Tempo',
        'ended' => 'Terminata',
    ];
    $status_label = $status_labels[ $status ] ?? $status;
    $current_minute = $m('ilm_current_minute');
    if ( in_array( $status, ['1T','2T'] ) && $current_minute ) {
        $status_label .= ' - ' . $current_minute;
    }

    // Data formattata
    $mesi = ['','gennaio','febbraio','marzo','aprile','maggio','giugno',
             'luglio','agosto','settembre','ottobre','novembre','dicembre'];
    $match_date_raw = $m('ilm_match_date');
    $match_date_fmt = '';
    if ( $match_date_raw ) {
        $ts = strtotime( $match_date_raw );
        $match_date_fmt = intval( date('j',$ts) ) . ' ' . $mesi[ intval( date('n',$ts) ) ] . ' ' . date('Y',$ts) . ' ore ' . date('H:i',$ts);
    }
?>
<main id="primary" class="site-main dspr-match-page">

<?php if ( $hero_src ) : ?>
<!-- HERO FOTO -->
<div class="post-hero">
    <img src="<?php echo esc_url( $hero_src ); ?>" alt="<?php echo $hero_alt; ?>" loading="eager" fetchpriority="high">
    <div class="post-hero__overlay"></div>
    <?php if ( $hero_cap ) : ?>
        <div class="post-hero__caption"><?php echo $hero_cap; ?></div>
    <?php endif; ?>
</div>
<?php else : ?>
<!-- HERO GRAFICO FALLBACK quando non c'è foto -->
<div class="post-hero dspr-match-hero-fallback">
    <div class="dspr-match-hero-fallback__inner">
        <?php if ( $m('ilm_home_logo') ) : ?>
            <img src="<?php echo esc_url( $m('ilm_home_logo') ); ?>" alt="<?php echo esc_attr( $home_name ); ?>" class="dspr-match-hero-fallback__logo">
        <?php endif; ?>
        <span class="dspr-match-hero-fallback__vs">VS</span>
        <?php if ( $m('ilm_away_logo') ) : ?>
            <img src="<?php echo esc_url( $m('ilm_away_logo') ); ?>" alt="<?php echo esc_attr( $away_name ); ?>" class="dspr-match-hero-fallback__logo">
        <?php endif; ?>
    </div>
    <div class="post-hero__overlay"></div>
</div>
<?php endif; ?>

<!-- HEADER ARTICOLO -->
<div class="post-header">
    <div class="post-header-top">
        <?php if ( $competition ) : ?>
            <span class="post-categoria"><?php echo esc_html( $competition ); ?></span>
        <?php endif; ?>
        <span class="post-occhiello--top dspr-match-status dspr-match-status--<?php echo esc_attr( $status ); ?>">
            <?php echo esc_html( $status_label ); ?>
        </span>
    </div>
    <h1 class="post-title"><?php echo esc_html( $home_name . ' – ' . $away_name ); ?></h1>
    <?php if ( $match_date_fmt ) : ?>
    <div class="post-meta">
        <span class="post-meta__date"><?php echo esc_html( $match_date_fmt ); ?></span>
        <?php if ( $m('ilm_venue') ) : ?>
            <span class="post-meta__sep">·</span>
            <span class="post-meta__date"><?php echo esc_html( $m('ilm_venue') ); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if ( $intro ) : ?>
<!-- TESTO INTRO — campo ilm_lineup_intro -->
<div class="post-body">
    <div class="post-body-inner dspr-match-intro">
        <?php echo nl2br( esc_html( $intro ) ); ?>
    </div>
</div>
<?php endif; ?>

<!-- WIDGET LIVE PLUGIN -->
<div class="dspr-match-widget">
    <?php
    if ( function_exists('ilm_shortcode') ) {
        echo ilm_shortcode( ['id' => $post_id] );
    }
    ?>
</div>

<!-- Eventuale contenuto aggiuntivo del post -->
<?php if ( get_the_content() ) : ?>
<div class="post-body">
    <div class="post-body-inner">
        <?php the_content(); ?>
    </div>
</div>
<?php endif; ?>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
