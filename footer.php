<?php
/**
 * The template for displaying the footer
 *
 * @package WP Starter Theme
 */

// Pull footer widget areas directly from what is registered in inc/widgets.php.
global $wp_registered_sidebars;
$footer_widgets = array_filter(
	$wp_registered_sidebars,
	fn( $sidebar ) => str_starts_with( $sidebar['id'], 'footer-' )
);

// Count active widget areas; fall back to all registered when none are active (dev/preview).
$active_sidebars = array_filter( array_column( $footer_widgets, 'id' ), 'is_active_sidebar' );
$active_count    = count( $active_sidebars );
$has_active      = $active_count > 0;
$col_count       = $has_active ? $active_count : count( $footer_widgets );
$cols_class      = 'site-footer__widgets--cols-' . $col_count;
?>

	</div>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="site-footer__widgets <?php echo esc_attr( $cols_class ); ?>">

				<?php
				foreach ( $footer_widgets as $sidebar ) :
					$sidebar_id = $sidebar['id'];
					if ( $has_active && ! is_active_sidebar( $sidebar_id ) ) {
						continue;
					}
					?>
				<aside class="widget-area" aria-labelledby="footer-<?php echo esc_attr( $sidebar_id ); ?>-title">
					<h2 id="footer-<?php echo esc_attr( $sidebar_id ); ?>-title" class="screen-reader-text">
						<?php echo esc_html( $sidebar['name'] ); ?>
					</h2>
					<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>
						<?php dynamic_sidebar( $sidebar_id ); ?>
					<?php else : ?>
						<h2 class="widget-title"><?php echo esc_html( $sidebar['name'] ); ?></h2>
						<p><?php esc_html_e( 'Add widgets here.', 'wp-starter-theme' ); ?></p>
					<?php endif; ?>
				</aside>
				<?php endforeach; ?>

			</div>

			<div class="site-footer__copyright">
				<span>
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				</span>

				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer-menu',
						'container'            => 'nav',
						'container_aria_label' => __( 'Footer menu', 'wp-starter-theme' ),
						'menu_class'           => 'footer-menu',
						'depth'                => 1,
						'fallback_cb'          => '__return_false',
					)
				);
				?>
			</div>
		</div>
	</footer>

</div>

<?php wp_footer(); ?>

</body>
</html>
