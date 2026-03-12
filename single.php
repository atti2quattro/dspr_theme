<?php
/**
 * Il Dispari – Template Single Post
 * @package dspr
 */

get_header();
?>

<main id="primary" class="site-main">
<?php while ( have_posts() ) : the_post();

  $post_id     = get_the_ID();
  $categorie   = get_the_category();
  $cat_nome    = ! empty( $categorie ) ? esc_html( $categorie[0]->name ) : '';
  $cat_url     = ! empty( $categorie ) ? esc_url( get_category_link( $categorie[0]->term_id ) ) : '#';
  $cat_id      = ! empty( $categorie ) ? $categorie[0]->term_id : 0;
  $occhiello   = get_post_meta( $post_id, '_dspr_occhiello',   true );
  $sottotitolo = get_post_meta( $post_id, '_dspr_sottotitolo', true );
  $immersivo   = get_post_meta( $post_id, '_dspr_immersivo',   true );
  $video_url   = get_post_meta( $post_id, '_dspr_video_url',   true );
  $autore_id   = get_the_author_meta( 'ID' );
  $autore      = esc_html( get_the_author_meta( 'display_name' ) );
  $autore_url  = esc_url( get_author_posts_url( $autore_id ) );
  $autore_bio  = get_the_author_meta( 'description' );
  $autore_avatar = get_avatar_url( $autore_id, [ 'size' => 80 ] );
  $hero_id     = get_post_thumbnail_id( $post_id );
  $hero_src    = $hero_id ? wp_get_attachment_image_url( $hero_id, 'ildispari-hero' ) : '';
  $hero_alt    = $hero_id ? esc_attr( get_post_meta( $hero_id, '_wp_attachment_image_alt', true ) ) : esc_attr( get_the_title() );
  $hero_cap    = $hero_id ? esc_html( wp_get_attachment_caption( $hero_id ) ) : '';

  // Data in italiano
  $mesi = [ '', 'gennaio','febbraio','marzo','aprile','maggio','giugno',
                'luglio','agosto','settembre','ottobre','novembre','dicembre' ];
  $data = intval( get_the_date('j') ) . ' ' . $mesi[ intval( get_the_date('n') ) ] . ' ' . get_the_date('Y');

  // Embed video
  $video_embed = '';
  if ( $video_url ) {
    if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $m ) ) {
      $video_embed = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=0&rel=0';
    } elseif ( preg_match( '/vimeo\.com\/(\d+)/', $video_url, $m ) ) {
      $video_embed = 'https://player.vimeo.com/video/' . $m[1];
    }
  }
?>

<?php if ( $video_embed ) : ?>
<!-- HERO VIDEO -->
<div class="post-hero post-hero--video">
  <div class="post-hero__iframe-wrap">
    <iframe src="<?php echo esc_url( $video_embed ); ?>"
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      allowfullscreen loading="lazy">
    </iframe>
  </div>
</div>

<?php elseif ( $immersivo && $hero_src ) : ?>
<!-- HERO IMMERSIVA -->
<div class="post-hero post-hero--immersiva" style="background-image:url('<?php echo esc_url( $hero_src ); ?>');">
  <div class="post-hero__immersiva-gradient"></div>
  <?php if ( $hero_cap ) : ?>
    <div class="post-hero__caption"><?php echo $hero_cap; ?></div>
  <?php endif; ?>
  <div class="post-hero__immersiva-content">
    <div class="post-header-top">
      <?php if ( $cat_nome ) : ?>
        <a class="post-categoria" href="<?php echo $cat_url; ?>"><?php echo $cat_nome; ?></a>
      <?php endif; ?>
      <?php if ( $occhiello ) : ?>
        <span class="post-occhiello--top"><?php echo esc_html( $occhiello ); ?></span>
      <?php endif; ?>
    </div>
    <h1 class="post-title"><?php the_title(); ?></h1>
    <?php if ( $sottotitolo ) : ?>
      <p class="post-sottotitolo"><?php echo esc_html( $sottotitolo ); ?></p>
    <?php endif; ?>
    <div class="post-meta">
      <img class="post-meta__avatar" src="<?php echo esc_url( $autore_avatar ); ?>" alt="<?php echo $autore; ?>" width="28" height="28">
      <a class="post-meta__author" href="<?php echo $autore_url; ?>">di <?php echo $autore; ?></a>
      <span class="post-meta__date"><?php echo $data; ?></span>
    </div>
  </div>
</div>

<?php else : ?>
<!-- HERO FOTO STANDARD -->
<?php if ( $hero_src ) : ?>
<div class="post-hero">
  <img src="<?php echo esc_url( $hero_src ); ?>" alt="<?php echo $hero_alt; ?>" loading="eager" fetchpriority="high">
  <div class="post-hero__overlay"></div>
  <?php if ( $hero_cap ) : ?><div class="post-hero__caption"><?php echo $hero_cap; ?></div><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if ( ! $immersivo || $video_embed ) : ?>
<!-- HEADER ARTICOLO STANDARD -->
<div class="post-header">
  <div class="post-header-top">
    <?php if ( $cat_nome ) : ?>
      <a class="post-categoria" href="<?php echo $cat_url; ?>"><?php echo $cat_nome; ?></a>
    <?php endif; ?>
    <?php if ( $occhiello ) : ?>
      <span class="post-occhiello--top"><?php echo esc_html( $occhiello ); ?></span>
    <?php endif; ?>
  </div>
  <h1 class="post-title"><?php the_title(); ?></h1>
  <?php if ( $sottotitolo ) : ?>
    <p class="post-sottotitolo"><?php echo esc_html( $sottotitolo ); ?></p>
  <?php endif; ?>
  <div class="post-meta">
    <img class="post-meta__avatar" src="<?php echo esc_url( $autore_avatar ); ?>" alt="<?php echo $autore; ?>" width="28" height="28">
    <a class="post-meta__author" href="<?php echo $autore_url; ?>">di <?php echo $autore; ?></a>
    <span class="post-meta__sep">·</span>
    <span class="post-meta__date"><?php echo $data; ?></span>
  </div>
</div>
<?php endif; ?>

<!-- RTBuzz: top articolo (leadmobile + insideposttop + topmobile) -->
<div style="padding-bottom:20px;">
  <div id="rtbuzz_leadmobile"></div><script>window.RTBuzz.cmd.push("leadmobile");</script>
  <div id="rtbuzz_insideposttop"></div><script>window.RTBuzz.cmd.push("insideposttop");</script>
  <div id="rtbuzz_topmobile"></div><script>window.RTBuzz.cmd.push("topmobile");</script>
</div>

<!-- SHARE BAR MINIMAL -->
<div class="post-share" aria-label="Condividi">
  <span class="share-label">Condividi</span>
  <div class="share-icon share-icon--fb" role="button" tabindex="0" aria-label="Facebook">
    <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
  </div>
  <div class="share-icon share-icon--wa" role="button" tabindex="0" aria-label="WhatsApp">
    <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.999 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.948-1.42A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.522 2 12 2z"/></svg>
  </div>
  <div class="share-icon share-icon--tg" role="button" tabindex="0" aria-label="Telegram">
    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8l-1.68 7.92c-.12.56-.46.7-.92.44l-2.56-1.88-1.24 1.2c-.14.14-.26.26-.52.26l.18-2.6 4.72-4.26c.2-.18-.04-.28-.32-.1L7.6 14.26 5.08 13.5c-.56-.16-.58-.56.12-.82l9.28-3.58c.46-.18.88.1.72.8l.44-.1z"/></svg>
  </div>
</div>

<!-- CORPO ARTICOLO -->
<div class="post-body">
  <div class="post-body-inner">
    <?php
    // Dividiamo il contenuto a metà per inserire il banner a centro articolo
    $content = apply_filters( 'the_content', get_the_content() );
    $paragraphs = preg_split( '/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
    $total = count( $paragraphs );
    $half  = max( 2, intval( $total / 2 ) );

    $first_half  = implode( '', array_slice( $paragraphs, 0, $half ) );
    $second_half = implode( '', array_slice( $paragraphs, $half ) );
    ?>

    <?php echo $first_half; ?>

    <!-- RTBuzz: metà articolo -->
    <div style="float:left;margin:8px 8px 8px 0;" id="rtbuzz_insidepostmiddle"></div>
    <script>window.RTBuzz.cmd.push("insidepostmiddle");</script>
    <div id="rtbuzz_middlemobile"></div>
    <script>window.RTBuzz.cmd.push("middlemobile");</script>

    <?php echo $second_half; ?>

    <?php
    $tags = get_the_tags();
    if ( $tags ) : ?>
    <div class="post-tags">
      <span class="post-tags__label">Tag</span>
      <?php foreach ( $tags as $tag ) : ?>
        <a class="tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- RTBuzz: fine articolo -->
<div id="rtbuzz_native"></div><script>window.RTBuzz.cmd.push("native");</script>
<div id="rtbuzz_bottommobile"></div><script>window.RTBuzz.cmd.push("bottommobile");</script>

<!-- BOX AUTORE -->
<?php if ( $autore ) : ?>
<div class="post-autore">
  <img class="post-autore__avatar" src="<?php echo esc_url( $autore_avatar ); ?>" alt="<?php echo $autore; ?>" width="64" height="64">
  <div class="post-autore__info">
    <div class="post-autore__label">L'autore</div>
    <a class="post-autore__name" href="<?php echo $autore_url; ?>"><?php echo $autore; ?></a>
    <?php if ( $autore_bio ) : ?>
      <p class="post-autore__bio"><?php echo esc_html( $autore_bio ); ?></p>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- PREV / NEXT -->
<?php
$prev = get_previous_post( true );
$next = get_next_post( true );
if ( $prev || $next ) : ?>
<nav class="post-prevnext" aria-label="Navigazione articoli">
  <?php if ( $prev ) : ?>
  <a class="prevnext-item" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
    <div class="prevnext-item__dir">← Precedente</div>
    <div class="prevnext-item__title"><?php echo esc_html( get_the_title( $prev ) ); ?></div>
  </a>
  <?php else : ?><div class="prevnext-item"></div><?php endif; ?>
  <?php if ( $next ) : ?>
  <a class="prevnext-item" href="<?php echo esc_url( get_permalink( $next ) ); ?>" style="text-align:right;">
    <div class="prevnext-item__dir" style="justify-content:flex-end;">Successivo →</div>
    <div class="prevnext-item__title"><?php echo esc_html( get_the_title( $next ) ); ?></div>
  </a>
  <?php else : ?><div class="prevnext-item"></div><?php endif; ?>
</nav>
<?php endif; ?>

<!-- CORRELATI -->
<?php
$correlati = new WP_Query( [
  'post_type'           => 'post',
  'posts_per_page'      => 3,
  'post__not_in'        => [ $post_id ],
  'category__in'        => [ $cat_id ],
  'orderby'             => 'date',
  'order'               => 'DESC',
  'ignore_sticky_posts' => true,
  'no_found_rows'       => true,
] );
if ( $correlati->have_posts() ) : ?>
<section class="post-correlati">
  <div class="correlati-title">Leggi anche</div>
  <div class="correlati-list">
    <?php while ( $correlati->have_posts() ) : $correlati->the_post();
      $c_cats  = get_the_category();
      $c_cat   = ! empty( $c_cats ) ? esc_html( $c_cats[0]->name ) : '';
      $c_thumb = get_the_post_thumbnail_url( get_the_ID(), 'ildispari-thumb' );
    ?>
    <a class="correlati-item" href="<?php the_permalink(); ?>">
      <div class="correlati-item__thumb">
        <?php if ( $c_thumb ) : ?>
          <img src="<?php echo esc_url( $c_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
        <?php endif; ?>
      </div>
      <div>
        <?php if ( $c_cat ) : ?><div class="correlati-item__cat"><?php echo $c_cat; ?></div><?php endif; ?>
        <div class="correlati-item__title"><?php the_title(); ?></div>
      </div>
    </a>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>
<?php endif; ?>

<!-- COMMENTI -->
<?php if ( comments_open() || get_comments_number() ) : ?>
<div class="post-commenti">
  <?php comments_template(); ?>
</div>
<?php endif; ?>

<?php endwhile; ?>
</main>

<?php get_footer(); ?>
