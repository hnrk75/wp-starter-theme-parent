<?php
/**
 * Register widget areas
 *
 * @package WP Starter Theme
 */

function wpst_widgets_init() {

	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wp-starter-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'wp-starter-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer one', 'wp-starter-theme' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Add widgets here.', 'wp-starter-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer two', 'wp-starter-theme' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here.', 'wp-starter-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer three', 'wp-starter-theme' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here.', 'wp-starter-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer four', 'wp-starter-theme' ),
			'id'            => 'footer-4',
			'description'   => esc_html__( 'Add widgets here.', 'wp-starter-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wpst_widgets_init' );
