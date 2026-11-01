<?php
/**
 * The sidebar containing the main widget area. Templates call it without a wrapper.
 *
 * @package WP Starter Theme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="content-sidebar widget-area" aria-label="<?php echo esc_attr__( 'Sidebar', 'wp-starter-theme' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
