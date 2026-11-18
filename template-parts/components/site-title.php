<?php
/**
 * Template part for the site name as text, when there is no logo.
 *
 * @package WP Starter Theme
 */

?>
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
	<span class="site-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
</a>
