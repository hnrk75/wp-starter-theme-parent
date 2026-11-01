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

// --- Dashboard widget: recommended plugins --------------------
add_action( 'wp_dashboard_setup', 'wpst_register_recommended_plugins_widget' );
add_action( 'wp_ajax_wpst_add_composer_plugin', 'wpst_ajax_add_composer_plugin' );
add_action( 'admin_enqueue_scripts', 'wpst_dashboard_widget_scripts' );

function wpst_register_recommended_plugins_widget() {
	wp_add_dashboard_widget(
		'wpst_recommended_plugins',
		__( 'Recommended Plugins', 'wp-starter-theme' ),
		'wpst_render_recommended_plugins_widget'
	);
}

function wpst_get_recommended_plugins() {
	return array(
		array(
			'name'     => 'HNRK Cookie Consent',
			'file'     => 'hnrk-cookie-consent/hnrk-cookie-consent.php',
			'package'  => 'hnrk75/hnrk-cookie-consent',
			'repo_url' => 'https://github.com/hnrk75/hnrk-cookie-consent',
			'version'  => '^1.0',
		),
		array(
			'name'     => 'Two Factor',
			'file'     => 'two-factor/two-factor.php',
			'package'  => 'wpackagist-plugin/two-factor',
			'repo_url' => '',
			'version'  => '0.17.0',
		),
	);
}

function wpst_render_recommended_plugins_widget() {
	$plugins       = wpst_get_recommended_plugins();
	$nonce         = wp_create_nonce( 'wpst_composer_nonce' );
	$composer_path = dirname( get_template_directory(), 4 ) . '/composer.json';
	$composer      = array();

	if ( file_exists( $composer_path ) ) {
		$composer = json_decode( file_get_contents( $composer_path ), true ) ?? array(); // phpcs:ignore
	}

	echo '<ul style="margin:0;padding:0;list-style:none;">';

	foreach ( $plugins as $plugin ) {
		$active     = is_plugin_active( $plugin['file'] );
		$installed  = file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] );
		$in_compose = isset( $composer['require'][ $plugin['package'] ] );

		if ( $active ) {
			$color  = '#00a32a';
			$icon   = '✓';
			$status = __( 'Active', 'wp-starter-theme' );
		} elseif ( $installed ) {
			$color  = '#d97706';
			$icon   = '○';
			$status = __( 'Not activated', 'wp-starter-theme' );
		} elseif ( $in_compose ) {
			$color  = '#d97706';
			$icon   = '○';
			$status = __( 'Pending', 'wp-starter-theme' );
		} else {
			$color  = '#999';
			$icon   = '○';
			$status = __( 'Not installed', 'wp-starter-theme' );
		}

		echo '<li style="display:flex;align-items:flex-start;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f0;">';
		echo '<span style="color:' . esc_attr( $color ) . ';font-size:16px;line-height:1.4;flex-shrink:0;">' . esc_html( $icon ) . '</span>';
		echo '<div style="flex:1;">';
		echo '<strong>' . esc_html( $plugin['name'] ) . '</strong>';
		echo '<span style="color:' . esc_attr( $color ) . ';margin-left:8px;font-size:12px;">' . esc_html( $status ) . '</span>';

		if ( ! $active && $installed ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin['file'] ) ), 'activate-plugin_' . $plugin['file'] );
				echo '<br><a class="button button-small" style="margin-top:6px;" href="' . esc_url( $activate_url ) . '">' . esc_html__( 'Activate', 'wp-starter-theme' ) . '</a>';
			}
		} elseif ( ! $active && $in_compose ) {
			echo '<br><code style="font-size:11px;background:#fef3c7;padding:2px 6px;border-radius:3px;display:inline-block;margin-top:4px;">' . esc_html__( 'Run in terminal: composer install', 'wp-starter-theme' ) . '</code>';
		} elseif ( ! $active ) {
			echo '<br>';
			echo '<button class="button button-small wpst-composer-btn" style="margin-top:6px;" '
				. 'data-package="' . esc_attr( $plugin['package'] ) . '" '
				. 'data-repo="' . esc_attr( $plugin['repo_url'] ) . '" '
				. 'data-version="' . esc_attr( $plugin['version'] ) . '" '
				. 'data-nonce="' . esc_attr( $nonce ) . '">'
				. esc_html__( 'Add to composer.json', 'wp-starter-theme' )
				. '</button>';
			echo '<span class="wpst-composer-result" style="display:none;margin-left:8px;font-size:12px;"></span>';
		}

		echo '</div>';
		echo '</li>';
	}

	echo '</ul>';
}

function wpst_dashboard_widget_scripts( $hook ) {
	if ( 'index.php' !== $hook ) {
		return;
	}
	wp_add_inline_script(
		'jquery',
		'jQuery( function( $ ) {
			$( document ).on( "click", ".wpst-composer-btn", function() {
				var btn    = $( this );
				var result = btn.siblings( ".wpst-composer-result" );
				btn.prop( "disabled", true ).text( "..." );
				$.post( ajaxurl, {
					action:   "wpst_add_composer_plugin",
					nonce:    btn.data( "nonce" ),
					package:  btn.data( "package" ),
					repo_url: btn.data( "repo" ),
					version:  btn.data( "version" ),
				}, function( response ) {
					if ( response.success ) {
						btn.remove();
						result.show().css( "color", "#00a32a" ).text( "✓ " + response.data );
					} else {
						btn.prop( "disabled", false ).text( "Add to composer.json" );
						result.show().css( "color", "#d63638" ).text( "✗ " + response.data );
					}
				} );
			} );
		} );'
	);
}

function wpst_ajax_add_composer_plugin() {
	check_ajax_referer( 'wpst_composer_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unauthorized.', 'wp-starter-theme' ) );
	}

	$package  = sanitize_text_field( wp_unslash( $_POST['package'] ?? '' ) );
	$repo_url = esc_url_raw( wp_unslash( $_POST['repo_url'] ?? '' ) );
	$version  = sanitize_text_field( wp_unslash( $_POST['version'] ?? '^1.0' ) );

	if ( ! $package ) {
		wp_send_json_error( __( 'Missing package name.', 'wp-starter-theme' ) );
	}

	$composer_path = dirname( get_template_directory(), 4 ) . '/composer.json';

	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	WP_Filesystem();
	global $wp_filesystem;

	if ( ! $wp_filesystem->exists( $composer_path ) ) {
		wp_send_json_error( sprintf( __( 'composer.json not found at: %s', 'wp-starter-theme' ), $composer_path ) );
	}

	if ( ! $wp_filesystem->is_writable( $composer_path ) ) {
		wp_send_json_error( __( 'composer.json is not writable.', 'wp-starter-theme' ) );
	}

	$json = json_decode( $wp_filesystem->get_contents( $composer_path ), true );

	if ( null === $json ) {
		wp_send_json_error( __( 'Could not parse composer.json.', 'wp-starter-theme' ) );
	}

	$changed = false;

	// Add VCS repository entry if not already present.
	if ( $repo_url ) {
		$already = false;
		foreach ( $json['repositories'] ?? array() as $repo ) {
			if ( isset( $repo['url'] ) && $repo['url'] === $repo_url ) {
				$already = true;
				break;
			}
		}
		if ( ! $already ) {
			$json['repositories'][] = array(
				'type' => 'vcs',
				'url'  => $repo_url,
			);
			$changed = true;
		}
	}

	// Add require entry if not already present.
	if ( empty( $json['require'][ $package ] ) ) {
		$json['require'][ $package ] = $version;
		$changed = true;
	}

	if ( ! $changed ) {
		wp_send_json_success( __( 'Already in composer.json.', 'wp-starter-theme' ) );
	}

	$written = $wp_filesystem->put_contents( $composer_path, wp_json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );

	if ( ! $written ) {
		wp_send_json_error( __( 'Failed to write composer.json.', 'wp-starter-theme' ) );
	}

	wp_send_json_success( __( 'Added. Run composer install to complete.', 'wp-starter-theme' ) );
}
