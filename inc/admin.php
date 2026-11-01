<?php
/**
 * Admin customizations.
 *
 * @package WP Starter Theme
 */

// --- Comments disabled globally -------------------------------
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );

add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

add_action(
	'init',
	function () {
		remove_post_type_support( 'post', 'comments' );
		remove_post_type_support( 'page', 'comments' );
	}
);

// --- Remove built-in page-state labels ------------------------
add_filter(
	'display_post_states',
	function ( $post_states ) {
		unset( $post_states['page_for_privacy_policy'] );
		return $post_states;
	}
);

// --- Hide Site Editor (Design) --------------------------------
// Editors should not see FSE/patterns — misleading for a classic theme.
add_action(
	'admin_menu',
	function () {
		remove_submenu_page( 'themes.php', 'site-editor.php' );
	},
	999
);
