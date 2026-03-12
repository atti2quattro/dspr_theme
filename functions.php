<?php
/**
 * dspr functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package dspr
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.2' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function dspr_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on dspr, use a find and replace
		* to change 'dspr' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'dspr', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'dspr' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'dspr_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Dimensioni immagini Il Dispari
	add_image_size( 'ildispari-hero',   1200, 675, true );
	add_image_size( 'ildispari-card',    600, 400, true );
	add_image_size( 'ildispari-thumb',   200, 150, true );
	add_image_size( 'ildispari-inline',  900, 506, true );
}
add_action( 'after_setup_theme', 'dspr_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function dspr_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'dspr_content_width', 1200 );
}
add_action( 'after_setup_theme', 'dspr_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function dspr_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'dspr' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'dspr' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'dspr_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function dspr_scripts() {
	// Google Fonts
	wp_enqueue_style(
		'dspr-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@400;500;600;700&family=Inter:wght@600;700&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'dspr-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'dspr-style', 'rtl', 'replace' );

	wp_enqueue_script( 'dspr-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	// Share bar
	wp_enqueue_script( 'dspr-share', get_template_directory_uri() . '/js/share.js', array(), _S_VERSION, true );

	// Dark/light toggle — caricato in testa per evitare flash
	wp_enqueue_script( 'dspr-theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', array(), _S_VERSION, false );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dspr_scripts' );

/**
 * Script anti-flash: applica il tema PRIMA che il browser disegni il body.
 * Va nell'<head> tramite wp_head con priorità altissima (1).
 */
add_action( 'wp_head', 'dspr_theme_antiflash_script', 1 );
function dspr_theme_antiflash_script() {
    echo '<script>
    (function(){
        try {
            var t = localStorage.getItem("dspr-theme");
            if (t === "dark") document.documentElement.setAttribute("data-theme","dark");
        } catch(e){}
    })();
    </script>';
}

require get_template_directory() . '/inc/custom-header.php';

// ─── META BOX: Occhiello e Sottotitolo ───────────────────────────────────────

add_action( 'add_meta_boxes', 'dspr_add_testata_metabox' );
function dspr_add_testata_metabox() {
    add_meta_box(
        'dspr_testata',
        'Testata articolo',
        'dspr_testata_metabox_html',
        'post',
        'normal',
        'high'
    );
}

function dspr_testata_metabox_html( $post ) {
    wp_nonce_field( 'dspr_testata_save', 'dspr_testata_nonce' );
    $occhiello   = get_post_meta( $post->ID, '_dspr_occhiello', true );
    $sottotitolo = get_post_meta( $post->ID, '_dspr_sottotitolo', true );
    $immersivo   = get_post_meta( $post->ID, '_dspr_immersivo', true );
    $video_url   = get_post_meta( $post->ID, '_dspr_video_url', true );
    ?>
    <p style="margin-bottom:12px;">
        <label for="dspr_occhiello" style="display:block;font-weight:600;margin-bottom:4px;">
            Occhiello <span style="font-weight:400;color:#666;">(riga sopra il titolo)</span>
        </label>
        <input type="text" id="dspr_occhiello" name="dspr_occhiello"
            value="<?php echo esc_attr( $occhiello ); ?>"
            style="width:100%;padding:6px 8px;"
            placeholder="Es: Emergenza idrica a Casamicciola Terme">
    </p>
    <p style="margin-bottom:12px;">
        <label for="dspr_sottotitolo" style="display:block;font-weight:600;margin-bottom:4px;">
            Sottotitolo / Sommario <span style="font-weight:400;color:#666;">(riga sotto il titolo)</span>
        </label>
        <textarea id="dspr_sottotitolo" name="dspr_sottotitolo" rows="3"
            style="width:100%;padding:6px 8px;"
            placeholder="Es: Gli studenti dell'istituto Mattei impossibilitati a usare i servizi igienici"
        ><?php echo esc_textarea( $sottotitolo ); ?></textarea>
    </p>
    <p style="margin-bottom:12px;">
        <label for="dspr_video_url" style="display:block;font-weight:600;margin-bottom:4px;">
            URL Video <span style="font-weight:400;color:#666;">(YouTube o Vimeo — sostituisce la foto hero)</span>
        </label>
        <input type="url" id="dspr_video_url" name="dspr_video_url"
            value="<?php echo esc_attr( $video_url ); ?>"
            style="width:100%;padding:6px 8px;"
            placeholder="https://www.youtube.com/watch?v=...">
    </p>
    <p>
        <label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
            <input type="checkbox" name="dspr_immersivo" value="1" <?php checked( $immersivo, '1' ); ?> style="width:16px;height:16px;">
            Header immersivo <span style="font-weight:400;color:#666;">(titolo sovrapposto sulla foto, stile app)</span>
        </label>
    </p>
    <?php

add_action( 'save_post', 'dspr_testata_save' );
function dspr_testata_save( $post_id ) {
    if ( ! isset( $_POST['dspr_testata_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['dspr_testata_nonce'], 'dspr_testata_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['dspr_occhiello'] ) ) {
        update_post_meta( $post_id, '_dspr_occhiello', sanitize_text_field( $_POST['dspr_occhiello'] ) );
    }
    if ( isset( $_POST['dspr_sottotitolo'] ) ) {
        update_post_meta( $post_id, '_dspr_sottotitolo', sanitize_textarea_field( $_POST['dspr_sottotitolo'] ) );
    }
    update_post_meta( $post_id, '_dspr_immersivo', isset( $_POST['dspr_immersivo'] ) ? '1' : '' );
    if ( isset( $_POST['dspr_video_url'] ) ) {
        update_post_meta( $post_id, '_dspr_video_url', esc_url_raw( $_POST['dspr_video_url'] ) );
    }
}


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

// ─── Post Thumbnail Helper (fix per content-page.php) ────────────────────────
if ( ! function_exists( 'dspr_post_thumbnail' ) ) {
    function dspr_post_thumbnail() {
        if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
            return;
        }
        
        if ( is_singular() ) {
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div>
            <?php
        } else {
            ?>
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail( 'post-thumbnail' ); ?>
            </a>
            <?php
        }
    }
}


// Gallery lightbox + override blocchi Gutenberg
function dspr_enqueue_gallery() {
    if ( is_singular() ) {
        wp_enqueue_script(
            'dspr-gallery',
            get_stylesheet_directory_uri() . '/js/gallery.js',
            array(),
            '1.0.1',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'dspr_enqueue_gallery' );

// Rimuove lo stile inline di WordPress sui blocchi gallery (evita conflitti CSS)
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'wp-block-gallery' );
    wp_dequeue_style( 'wp-block-image' );
}, 100 );

// ─── GALLERY SLIDER: variante di stile Gutenberg ─────────────────────────────

// Registra lo stile "Slider" sul blocco gallery
add_action( 'init', 'dspr_register_gallery_slider_style' );
function dspr_register_gallery_slider_style() {
    register_block_style( 'core/gallery', [
        'name'  => 'slider',
        'label' => 'Slider orizzontale',
    ] );
}

// Carica lo script slider solo se la pagina ha una gallery slider
add_action( 'wp_enqueue_scripts', 'dspr_enqueue_gallery_slider' );
function dspr_enqueue_gallery_slider() {
    if ( is_singular() || is_archive() || is_front_page() ) {
        wp_enqueue_script(
            'dspr-gallery-slider',
            get_stylesheet_directory_uri() . '/js/gallery-slider.js',
            array(),
            '1.0.0',
            true
        );
    }
}
/**
 * Registra i template custom per i post (non solo per le pagine)
 */
add_filter( 'theme_post_templates', function( $templates ) {
    $templates['single-rubrica.php'] = 'Rubrica / Editoriale';
    return $templates;
} );

/**
 * Forza il caricamento del template selezionato sul single post
 */
add_filter( 'template_include', function( $template ) {
    if ( ! is_singular( 'post' ) ) return $template;
    $custom = get_post_meta( get_the_ID(), '_wp_page_template', true );
    if ( $custom && $custom !== 'default' ) {
        $path = get_template_directory() . '/' . $custom;
        if ( file_exists( $path ) ) return $path;
    }
    return $template;
} );
/**
 * DEBUG template — rimuovere dopo il test
 */
add_action( 'wp_head', function() {
    if ( ! is_singular( 'post' ) ) return;
    $custom = get_post_meta( get_the_ID(), '_wp_page_template', true );
    echo '<!-- DSPR DEBUG: template meta = [' . esc_html( $custom ) . '] -->';
} );
add_action( 'wp_head', function() {
    if ( ! is_singular( 'post' ) ) return;
    $located = locate_template( 'single-rubrica.php' );
    echo '<!-- DSPR DEBUG locate = [' . esc_html( $located ) . '] -->';
} );
add_action( 'wp_head', function() {
    if ( ! is_singular( 'post' ) ) return;
    global $template;
    echo '<!-- DSPR DEBUG template caricato = [' . esc_html( $template ) . '] -->';
} );
// ─── Post Thumbnail Helper ────────────────────────────────────────────────
if ( ! function_exists( 'dspr_post_thumbnail' ) ) {
    function dspr_post_thumbnail() {
        if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
            return;
        }
        
        if ( is_singular() ) {
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div>
            <?php
        } else {
            ?>
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail( 'post-thumbnail' ); ?>
            </a>
            <?php
        }
    }
}
// ============================================================
// RSS IMPORT - Italpress & Adnkronos
// Importazione manuale da pulsante in wp-admin
// ============================================================

// Aggiungi voce di menu in wp-admin
add_action('admin_menu', 'dspr_rss_import_menu');
function dspr_rss_import_menu() {
    add_management_page(
        'Import RSS',           // Titolo pagina
        'Import RSS',           // Titolo menu
        'manage_options',       // Capability richiesta
        'dspr-rss-import',      // Slug
        'dspr_rss_import_page'  // Callback
    );
}

// Pagina admin con pulsante
function dspr_rss_import_page() {
    ?>
    <div class="wrap">
        <h1>Import RSS - Il Dispari</h1>
        
        <?php
        // Se il pulsante è stato premuto, esegui import
        if (isset($_POST['dspr_run_import']) && check_admin_referer('dspr_rss_import_action')) {
            echo '<div class="notice notice-info"><p>Import in corso...</p></div>';
            $results = dspr_import_rss_feeds();
            
            echo '<div class="notice notice-success"><p><strong>Import completato!</strong></p>';
            echo '<ul>';
            foreach ($results as $source => $count) {
                echo '<li>' . esc_html($source) . ': ' . $count . ' articoli importati</li>';
            }
            echo '</ul></div>';
        }
        ?>
        
        <div class="card" style="max-width: 600px;">
            <h2>Feed configurati</h2>
            <ul>
                <li><strong>Italpress</strong> → categoria <code>news-mondo</code>, autore <code>Italpress</code></li>
                <li><strong>Adnkronos Campania</strong> → categoria <code>in-regione</code>, autore <code>ADNKRONOS</code></li>
                <li><strong>Adnkronos Nazionale</strong> → categoria <code>news-mondo</code>, autore <code>ADNKRONOS</code></li>
            </ul>
            
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field('dspr_rss_import_action'); ?>
                <button type="submit" name="dspr_run_import" class="button button-primary button-hero">
                    ▶ Avvia Import RSS
                </button>
            </form>
            
            <p style="margin-top: 20px; color: #666;">
                <small>L'import scarica gli ultimi 20 articoli da ogni feed e li pubblica automaticamente con immagine in evidenza. Gli articoli già presenti (stesso titolo) vengono ignorati.</small>
            </p>
        </div>
    </div>
    <?php
}

// Funzione principale di import
function dspr_import_rss_feeds() {
    // Configurazione feed
    $feeds = [
        [
            'url'      => 'https://www.italpress.com/rss?section=top-news',
            'category' => 'news-mondo',
            'author'   => 'Italpress',
            'source'   => 'Italpress',
            'limit'    => 20
        ],
        [
            'url'      => 'https://www.adnkronos.com/NewsFeed/RegCampania.xml?username=ildispariquotidiano&password=1ld8sp4ut6',
            'category' => 'in-regione',
            'author'   => 'ADNKRONOS',
            'source'   => 'Adnkronos Campania',
            'limit'    => 20
        ],
        [
            'url'      => 'https://www.adnkronos.com/NewsFeed/Ultimora.xml?username=ildispariquotidiano&password=1ld8sp4ut6',
            'category' => 'news-mondo',
            'author'   => 'ADNKRONOS',
            'source'   => 'Adnkronos Nazionale',
            'limit'    => 20
        ]
    ];

    $results = [];
    
    foreach ($feeds as $feed_config) {
        $count = dspr_process_single_feed($feed_config);
        $results[$feed_config['source']] = $count;
    }
    
    return $results;
}

function dspr_process_single_feed($config) {
    $feed = fetch_feed($config['url']);
    
    if (is_wp_error($feed)) {
        error_log('DSPR RSS Import Error: ' . $feed->get_error_message());
        return 0;
    }

    $max_items = $feed->get_item_quantity($config['limit']);
    $items = $feed->get_items(0, $max_items);

    // Ottieni ID categoria
    $category = get_category_by_slug($config['category']);
    if (!$category) {
        error_log('DSPR RSS Import: Categoria ' . $config['category'] . ' non trovata');
        return 0;
    }
    $category_id = $category->term_id;

    // Ottieni ID autore
    $author = get_user_by('login', $config['author']);
    if (!$author) {
        error_log('DSPR RSS Import: Autore ' . $config['author'] . ' non trovato');
        return 0;
    }
    $author_id = $author->ID;

    $imported = 0;

    foreach ($items as $item) {
        $title = $item->get_title();
        $content = $item->get_content();
        $link = $item->get_link();
        $date = $item->get_date('Y-m-d H:i:s');
        
        // Estrai immagine dal feed
        $image_url = '';

        // Metodo 1: enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_link()) {
            $image_url = $enclosure->get_link();
        }

        // Metodo 2: media:content
        if (empty($image_url)) {
            $raw_item = $item->get_item_tags('http://search.yahoo.com/mrss/', 'content');
            if (!empty($raw_item[0]['attribs']['']['url'])) {
                $image_url = $raw_item[0]['attribs']['']['url'];
            }
        }

        // Metodo 3: media:thumbnail
        if (empty($image_url)) {
            $raw_thumb = $item->get_item_tags('http://search.yahoo.com/mrss/', 'thumbnail');
            if (!empty($raw_thumb[0]['attribs']['']['url'])) {
                $image_url = $raw_thumb[0]['attribs']['']['url'];
            }
        }

        // Metodo 4: cerca img nel contenuto HTML (per Adnkronos regionale)
        if (empty($image_url)) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
                $image_url = $matches[1];
                // Rimuovi TUTTI i tag img dal contenuto per evitare duplicati
                $content = preg_replace('/<img[^>]*>.*?<\/img>/i', '', $content);
                $content = preg_replace('/<img[^>]*\/?>/i', '', $content);
            }
        }

        // Controlla se esiste già un post con questo titolo
        $existing = get_posts([
            'post_type'   => 'post',
            'post_status' => 'any',
            'title'       => $title,
            'numberposts' => 1
        ]);

        if (!empty($existing)) {
            continue; // Salta se esiste già
        }

        // Crea il post
        $post_id = wp_insert_post([
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => $author_id,
            'post_category' => [$category_id],
            'post_date'     => $date,
        ]);

        if (is_wp_error($post_id)) {
            error_log('DSPR RSS Import: Errore creazione post - ' . $post_id->get_error_message());
            continue;
        }

        // Aggiungi meta per tracciare la fonte
        add_post_meta($post_id, 'dspr_import_source', $config['source']);
        add_post_meta($post_id, 'dspr_import_url', $link);

        // Scarica e imposta immagine in evidenza
        if ($image_url) {
            $attachment_id = dspr_download_and_attach_image($image_url, $post_id, $title);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        $imported++;
    }

    return $imported;
}

function dspr_download_and_attach_image($image_url, $post_id, $title) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Download temporaneo
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return false;
    }

    $file_array = [
        'name'     => basename($image_url),
        'tmp_name' => $tmp
    ];

    // Crea attachment
    $attachment_id = media_handle_sideload($file_array, $post_id, $title);

    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    return $attachment_id;
}

// Chiusura della funzione dspr_testata_metabox_save
    function dspr_post_thumbnail() {
    if ( has_post_thumbnail() ) {
        the_post_thumbnail();
    }
}
}

/**
 * Enqueue Bento Gallery Styles
 */
function dspr_enqueue_bento_gallery_styles() {
    wp_enqueue_style( 
        'dspr-bento-gallery', 
        get_template_directory_uri() . '/assets/css/bento-gallery-styles.css',
        array(),
        '1.0.0',
        'all'
    );
}
add_action( 'wp_enqueue_scripts', 'dspr_enqueue_bento_gallery_styles' );

// ============================================================
// RSS IMPORT - Italpress & Adnkronos
// Importazione manuale da pulsante in wp-admin
// ============================================================

// Aggiungi voce di menu in wp-admin
add_action('admin_menu', 'dspr_rss_import_menu');
function dspr_rss_import_menu() {
    add_management_page(
        'Import RSS',           // Titolo pagina
        'Import RSS',           // Titolo menu
        'manage_options',       // Capability richiesta
        'dspr-rss-import',      // Slug
        'dspr_rss_import_page'  // Callback
    );
}

// Pagina admin con pulsante
function dspr_rss_import_page() {
    ?>
    <div class="wrap">
        <h1>Import RSS - Il Dispari</h1>
        
        <?php
        // Se il pulsante è stato premuto, esegui import
        if (isset($_POST['dspr_run_import']) && check_admin_referer('dspr_rss_import_action')) {
            echo '<div class="notice notice-info"><p>Import in corso...</p></div>';
            $results = dspr_import_rss_feeds();
            
            echo '<div class="notice notice-success"><p><strong>Import completato!</strong></p>';
            echo '<ul>';
            foreach ($results as $source => $count) {
                echo '<li>' . esc_html($source) . ': ' . $count . ' articoli importati</li>';
            }
            echo '</ul></div>';
        }
        ?>
        
        <div class="card" style="max-width: 600px;">
            <h2>Feed configurati</h2>
            <ul>
                <li><strong>Italpress</strong> → categoria <code>news-mondo</code>, autore <code>Italpress</code></li>
                <li><strong>Adnkronos Campania</strong> → categoria <code>in-regione</code>, autore <code>ADNKRONOS</code></li>
                <li><strong>Adnkronos Nazionale</strong> → categoria <code>news-mondo</code>, autore <code>ADNKRONOS</code></li>
            </ul>
            
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field('dspr_rss_import_action'); ?>
                <button type="submit" name="dspr_run_import" class="button button-primary button-hero">
                    ▶ Avvia Import RSS
                </button>
            </form>
            
            <p style="margin-top: 20px; color: #666;">
                <small>L'import scarica gli ultimi 20 articoli da ogni feed e li pubblica automaticamente con immagine in evidenza. Gli articoli già presenti (stesso titolo) vengono ignorati.</small>
            </p>
        </div>
    </div>
    <?php
}

// Funzione principale di import
function dspr_import_rss_feeds() {
    // Configurazione feed
    $feeds = [
        [
            'url'      => 'https://www.italpress.com/rss?section=top-news',
            'category' => 'news-mondo',
            'author'   => 'Italpress',
            'source'   => 'Italpress',
            'limit'    => 20
        ],
        [
            'url'      => 'https://www.adnkronos.com/NewsFeed/RegCampania.xml?username=ildispariquotidiano&password=1ld8sp4ut6',
            'category' => 'in-regione',
            'author'   => 'ADNKRONOS',
            'source'   => 'Adnkronos Campania',
            'limit'    => 20
        ],
        [
            'url'      => 'https://www.adnkronos.com/NewsFeed/Ultimora.xml?username=ildispariquotidiano&password=1ld8sp4ut6',
            'category' => 'news-mondo',
            'author'   => 'ADNKRONOS',
            'source'   => 'Adnkronos Nazionale',
            'limit'    => 20
        ]
    ];

    $results = [];
    
    foreach ($feeds as $feed_config) {
        $count = dspr_process_single_feed($feed_config);
        $results[$feed_config['source']] = $count;
    }
    
    return $results;
}

function dspr_process_single_feed($config) {
    $feed = fetch_feed($config['url']);
    
    if (is_wp_error($feed)) {
        error_log('DSPR RSS Import Error: ' . $feed->get_error_message());
        return 0;
    }

    $max_items = $feed->get_item_quantity($config['limit']);
    $items = $feed->get_items(0, $max_items);

    // Ottieni ID categoria
    $category = get_category_by_slug($config['category']);
    if (!$category) {
        error_log('DSPR RSS Import: Categoria ' . $config['category'] . ' non trovata');
        return 0;
    }
    $category_id = $category->term_id;

    // Ottieni ID autore
    $author = get_user_by('login', $config['author']);
    if (!$author) {
        error_log('DSPR RSS Import: Autore ' . $config['author'] . ' non trovato');
        return 0;
    }
    $author_id = $author->ID;

    $imported = 0;

    foreach ($items as $item) {
        $title = $item->get_title();
        $content = $item->get_content();
        $link = $item->get_link();
        $date = $item->get_date('Y-m-d H:i:s');
        
        // Estrai immagine dal feed
        $image_url = '';

        // Metodo 1: enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_link()) {
            $image_url = $enclosure->get_link();
        }

        // Metodo 2: media:content
        if (empty($image_url)) {
            $raw_item = $item->get_item_tags('http://search.yahoo.com/mrss/', 'content');
            if (!empty($raw_item[0]['attribs']['']['url'])) {
                $image_url = $raw_item[0]['attribs']['']['url'];
            }
        }

        // Metodo 3: media:thumbnail
        if (empty($image_url)) {
            $raw_thumb = $item->get_item_tags('http://search.yahoo.com/mrss/', 'thumbnail');
            if (!empty($raw_thumb[0]['attribs']['']['url'])) {
                $image_url = $raw_thumb[0]['attribs']['']['url'];
            }
        }

        // Metodo 4: cerca img nel contenuto HTML (per Adnkronos regionale)
        if (empty($image_url)) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
                $image_url = $matches[1];
                // Rimuovi TUTTI i tag img dal contenuto per evitare duplicati
                $content = preg_replace('/<img[^>]*>.*?<\/img>/i', '', $content);
                $content = preg_replace('/<img[^>]*\/?>/i', '', $content);
            }
        }

        // Controlla se esiste già un post con questo titolo
        $existing = get_posts([
            'post_type'   => 'post',
            'post_status' => 'any',
            'title'       => $title,
            'numberposts' => 1
        ]);

        if (!empty($existing)) {
            continue; // Salta se esiste già
        }

        // Crea il post
        $post_id = wp_insert_post([
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => $author_id,
            'post_category' => [$category_id],
            'post_date'     => $date,
        ]);

        if (is_wp_error($post_id)) {
            error_log('DSPR RSS Import: Errore creazione post - ' . $post_id->get_error_message());
            continue;
        }

        // Aggiungi meta per tracciare la fonte
        add_post_meta($post_id, 'dspr_import_source', $config['source']);
        add_post_meta($post_id, 'dspr_import_url', $link);

        // Scarica e imposta immagine in evidenza
        if ($image_url) {
            $attachment_id = dspr_download_and_attach_image($image_url, $post_id, $title);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        $imported++;
    }

    return $imported;
}

function dspr_download_and_attach_image($image_url, $post_id, $title) {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Download temporaneo
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return false;
    }

    $file_array = [
        'name'     => basename($image_url),
        'tmp_name' => $tmp
    ];

    // Crea attachment
    $attachment_id = media_handle_sideload($file_array, $post_id, $title);

    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    return $attachment_id;
}
// Funzione per recuperare news regionali da Adnkronos Campania
function fetch_regional_news() {
    $rss_url = 'https://www.adnkronos.com/NewsFeed/RegCampania.xml?username=ildispariquotidiano&password=1ld8sp4ut6';
    
    $rss = fetch_feed($rss_url);
    
    if (is_wp_error($rss)) {
        return array();
    }
    
    $maxitems = $rss->get_item_quantity(5);
    $rss_items = $rss->get_items(0, $maxitems);
    
    $news = array();
    foreach ($rss_items as $item) {
        $image_url = '';
        
        // Estrai immagine da enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_type() == 'image/jpeg') {
            $image_url = $enclosure->get_link();
        }
        
        $news[] = array(
            'title' => $item->get_title(),
            'link' => $item->get_permalink(),
            'description' => $item->get_description(),
            'date' => $item->get_date('j F Y'),
            'image' => $image_url
        );
    }
    
    return $news;
}

// Funzione per recuperare news nazionali da Adnkronos
function fetch_world_news() {
    $rss_url = 'https://www.adnkronos.com/NewsFeed/Ultimora.xml?username=ildispariquotidiano&password=1ld8sp4ut6';
    
    $rss = fetch_feed($rss_url);
    
    if (is_wp_error($rss)) {
        return array();
    }
    
    $maxitems = $rss->get_item_quantity(5);
    $rss_items = $rss->get_items(0, $maxitems);
    
    $news = array();
    foreach ($rss_items as $item) {
        $image_url = '';
        
        // Estrai immagine da enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && $enclosure->get_type() == 'image/jpeg') {
            $image_url = $enclosure->get_link();
        }
        
        $news[] = array(
            'title' => $item->get_title(),
            'link' => $item->get_permalink(),
            'description' => $item->get_description(),
            'date' => $item->get_date('j F Y'),
            'image' => $image_url
        );
    }
    
    return $news;
}