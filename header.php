<?php
/**
 * Il Dispari – Header
 * @package dspr
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- RTBuzz: formati globali (si attivano su tutte le pagine) -->
<div id="rtbuzz_interstitial"></div><script>window.RTBuzz.cmd.push("interstitial");</script>
<div id="rtbuzz_sticky"></div><script>window.RTBuzz.cmd.push("sticky");</script>
<div id="rtbuzz_skin"></div><script>window.RTBuzz.cmd.push("skin");</script>
<div id="rtbuzz_vip"></div><script>window.RTBuzz.cmd.push("vip");</script>
<div id="rtbuzz_in-image"></div><script>window.RTBuzz.cmd.push("in-image");</script>

<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dspr' ); ?></a>

<!-- RTBuzz: masthead — banner in cima, sopra l'header -->
<div id="rtbuzz_masthead"></div><script>window.RTBuzz.cmd.push("masthead");</script>

<header id="masthead" class="site-header">

	<div class="site-branding">

		<?php if ( has_custom_logo() ) : ?>
			<div class="site-logo">
				<?php the_custom_logo(); ?>
			</div>
		<?php else : ?>
			<a class="site-title-text" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				Il <span>Dispari</span>
			</a>
		<?php endif; ?>

		<?php
		$dspr_description = get_bloginfo( 'description', 'display' );
		if ( $dspr_description || is_customize_preview() ) :
		?>
			<p class="site-description screen-reader-text"><?php echo $dspr_description; ?></p>
		<?php endif; ?>

	</div><!-- .site-branding -->

	<div class="header-right">

		<button class="theme-toggle" aria-label="Passa al tema scuro">
			<span class="theme-toggle__icon">☾</span>
		</button>

		<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Menu">
			<span class="menu-toggle__bar"></span>
			<span class="menu-toggle__bar"></span>
			<span class="menu-toggle__bar"></span>
		</button>

	</div><!-- .header-right -->

	<nav id="site-navigation" class="main-navigation">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'menu-1',
			'menu_id'        => 'primary-menu',
			'fallback_cb'    => false,
		) );
		?>
	</nav><!-- #site-navigation -->

</header><!-- #masthead -->
