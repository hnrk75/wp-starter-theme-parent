<?php
/**
 * Theme setup: supports, menus, editor styles, misc.
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_setup' ) ) :
	function wpst_setup() {

		load_theme_textdomain( 'wp-starter-theme', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'wpst-square', 800, 800, true );  // 1:1
		add_image_size( 'wpst-4-3', 800, 600, true );  // 4:3 landscape
		add_image_size( 'wpst-3-4', 600, 800, true );  // 3:4 portrait
		add_image_size( 'wpst-16-9', 1280, 720, true );  // 16:9 landscape
		add_image_size( 'wpst-9-16', 720, 1280, true );  // 9:16 portrait
		add_image_size( 'wpst-hero', 1920, 1080, true );  // 16:9 full-width hero
		add_image_size( 'wpst-og', 1200, 630, true );  // Open Graph / social

		add_theme_support(
			'html5',
			array(
				'search-form',
				'gallery',
				'caption',
				'script',
				'style',
				'navigation-widgets',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

		register_nav_menus(
			array(
				'main-menu'   => __( 'Main menu', 'wp-starter-theme' ),
				'footer-menu' => __( 'Footer menu', 'wp-starter-theme' ),
			)
		);

		// Fonts must be enqueued separately so the block editor loads them.
		add_theme_support( 'editor-styles' );

		$fonts_url = wpst_get_google_fonts_url();
		if ( $fonts_url ) {
			add_editor_style( $fonts_url );
		}

		add_editor_style( 'assets/css/style.css' );
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'wpst_setup' );

// Polyfill for wp_body_open (WP < 5.2).
if ( ! function_exists( 'wp_body_open' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	function wp_body_open() {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'wp_body_open' );
	}
}


add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		return array_merge(
			$sizes,
			array(
				'wpst-square' => __( '1:1 Square (800×800)', 'wp-starter-theme' ),
				'wpst-4-3'    => __( '4:3 Landscape (800×600)', 'wp-starter-theme' ),
				'wpst-3-4'    => __( '3:4 Portrait (600×800)', 'wp-starter-theme' ),
				'wpst-16-9'   => __( '16:9 Landscape (1280×720)', 'wp-starter-theme' ),
				'wpst-9-16'   => __( '9:16 Portrait (720×1280)', 'wp-starter-theme' ),
				'wpst-hero'   => __( 'Hero (1920×1080)', 'wp-starter-theme' ),
				'wpst-og'     => __( 'Open Graph (1200×630)', 'wp-starter-theme' ),
			)
		);
	}
);

if ( ! function_exists( 'wpst_register_navwalker' ) ) :
	function wpst_register_navwalker() {
		$walker_path = get_parent_theme_file_path( 'inc/navwalker.php' );
		if ( file_exists( $walker_path ) ) {
			require_once $walker_path;
		}
	}
endif;
add_action( 'after_setup_theme', 'wpst_register_navwalker' );
