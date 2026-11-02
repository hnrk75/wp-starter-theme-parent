<?php
/**
 * Registers the child theme's blocks: every blocks/{name}/block.json.
 *
 * @package WP Starter Theme
 */

/**
 * Folders starting with "_" (e.g. the _example template) are skipped. ACF blocks
 * (block.json with an "acf" key) are only registered when ACF PRO is active.
 */
function wpst_register_theme_blocks() {
	$block_files = glob( get_stylesheet_directory() . '/blocks/*/block.json' );
	if ( ! $block_files ) {
		return;
	}

	$has_acf = function_exists( 'acf_register_block_type' );

	foreach ( $block_files as $block_file ) {
		$dir = dirname( $block_file );
		if ( str_starts_with( basename( $dir ), '_' ) ) {
			continue;
		}

		$metadata = wp_json_file_decode( $block_file, array( 'associative' => true ) );
		if ( ! is_array( $metadata ) || ( isset( $metadata['acf'] ) && ! $has_acf ) ) {
			continue;
		}

		register_block_type( $dir );
	}
}
add_action( 'init', 'wpst_register_theme_blocks' );
