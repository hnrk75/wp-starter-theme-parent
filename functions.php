<?php
/**
 * Functions and definitions
 *
 * The theme's own files are always loaded from this (parent) theme, so a child theme
 * can never replace a whole file and miss future updates to it. Child themes customize
 * through hooks, filters and pluggable functions (wrapped in function_exists()).
 *
 * @package WP Starter Theme
 */

require_once get_parent_theme_file_path( 'inc/setup.php' );
require_once get_parent_theme_file_path( 'inc/admin.php' );
require_once get_parent_theme_file_path( 'inc/breadcrumbs.php' );
require_once get_parent_theme_file_path( 'inc/helpers-buttons.php' );
require_once get_parent_theme_file_path( 'inc/helpers-icon.php' );
require_once get_parent_theme_file_path( 'inc/enqueue.php' );
require_once get_parent_theme_file_path( 'inc/template-tags.php' );
require_once get_parent_theme_file_path( 'inc/widgets.php' );
require_once get_parent_theme_file_path( 'inc/meta.php' );
require_once get_parent_theme_file_path( 'inc/privacy.php' );
