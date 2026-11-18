<?php
/**
 * The logo or the site name in the header: assets/svg/logo.svg, else the uploaded logo,
 * else the site name as text.
 *
 * Copy this file to the child theme to build a site's own header branding without
 * copying header.php.
 *
 * @package WP Starter Theme
 */

if ( is_readable( get_theme_file_path( 'assets/svg/logo.svg' ) ) ) {
	get_template_part( 'template-parts/components/logo' );
} elseif ( has_custom_logo() ) {
	the_custom_logo();
} else {
	get_template_part( 'template-parts/components/site-title' );
}
