<?php
/**
 * Update notices for our Composer packages, based on their GitHub tags.
 *
 * @package WP Starter Theme
 */

/**
 * Installed packages, keyed by GitHub repo. `plugin` is set for regular plugins.
 *
 * @return array<string, array{name: string, version: string, plugin?: string}>
 */
function wpst_update_check_packages() {
	$theme    = wp_get_theme( get_template() );
	$packages = array(
		'hnrk75/wp-starter-theme-parent' => array(
			'name'    => (string) $theme->get( 'Name' ),
			'version' => (string) $theme->get( 'Version' ),
		),
	);

	$plugins = array(
		'hnrk75/wp-security-hardening' => array( WPMU_PLUGIN_DIR, 'wp-security-hardening/wp-security-hardening.php', false ),
		'hnrk75/hnrk-cookie-consent'   => array( WP_PLUGIN_DIR, 'hnrk-cookie-consent/hnrk-cookie-consent.php', true ),
	);

	foreach ( $plugins as $repo => list( $dir, $plugin, $is_regular ) ) {
		if ( ! file_exists( $dir . '/' . $plugin ) ) {
			continue;
		}

		$data = get_file_data(
			$dir . '/' . $plugin,
			array(
				'Name'    => 'Plugin Name',
				'Version' => 'Version',
			)
		);

		$packages[ $repo ] = array(
			'name'    => $data['Name'],
			'version' => $data['Version'],
		);

		if ( $is_regular ) {
			$packages[ $repo ]['plugin'] = $plugin;
		}
	}

	return apply_filters( 'wpst_update_check_packages', $packages );
}

/**
 * Packages with a newer version on GitHub.
 *
 * @return array<string, array{name: string, version: string, plugin?: string, new: string}>
 */
function wpst_available_updates() {
	$latest  = (array) get_site_option( 'wpst_latest_versions', array() );
	$updates = array();

	foreach ( wpst_update_check_packages() as $repo => $package ) {
		$new = $latest[ $repo ] ?? '';

		if ( $new && $package['version'] && version_compare( $new, $package['version'], '>' ) ) {
			$updates[ $repo ] = $package + array( 'new' => $new );
		}
	}

	return $updates;
}

/**
 * Update message (HTML).
 *
 * @param string $repo   Composer package / GitHub repo.
 * @param array  $update Entry from wpst_available_updates().
 * @return string
 */
function wpst_update_message( $repo, $update ) {
	return sprintf(
		/* translators: 1: package name, 2: changelog URL, 3: new version */
		__( 'There is a new version of %1$s available. <a href="%2$s" target="_blank" rel="noopener noreferrer">View version %3$s details</a>.', 'wp-starter-theme' ),
		esc_html( $update['name'] ),
		esc_url( 'https://github.com/' . $repo . '/blob/main/CHANGELOG.md' ),
		esc_html( $update['new'] )
	);
}

/**
 * Highest x.y.z tag of a public GitHub repo, or '' on failure.
 *
 * @param string $repo GitHub repo, e.g. "hnrk75/wp-starter-theme-parent".
 * @return string
 */
function wpst_fetch_latest_version( $repo ) {
	$response = wp_remote_get(
		'https://api.github.com/repos/' . $repo . '/tags?per_page=100',
		array(
			'timeout' => 10,
			'headers' => array( 'Accept' => 'application/vnd.github+json' ),
		)
	);

	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	$latest = '';
	foreach ( (array) json_decode( wp_remote_retrieve_body( $response ), true ) as $tag ) {
		$version = ltrim( (string) ( $tag['name'] ?? '' ), 'v' );

		if ( preg_match( '/^\d+\.\d+\.\d+$/', $version ) && ( '' === $latest || version_compare( $version, $latest, '>' ) ) ) {
			$latest = $version;
		}
	}

	return $latest;
}

/**
 * Cron job. A failed request keeps the previously stored version.
 */
function wpst_check_package_updates() {
	$packages = wpst_update_check_packages();
	$latest   = (array) get_site_option( 'wpst_latest_versions', array() );

	foreach ( array_keys( $packages ) as $repo ) {
		$version = wpst_fetch_latest_version( $repo );

		if ( $version ) {
			$latest[ $repo ] = $version;
		}
	}

	update_site_option( 'wpst_latest_versions', $latest );
	update_site_option( 'wpst_update_check_fingerprint', md5( (string) wp_json_encode( $packages ) ) );
}
add_action( 'wpst_check_package_updates', 'wpst_check_package_updates' );

add_action(
	'admin_init',
	function () {
		if ( 'hourly' !== wp_get_schedule( 'wpst_check_package_updates' ) ) {
			wp_clear_scheduled_hook( 'wpst_check_package_updates' );
			wp_schedule_event( time(), 'hourly', 'wpst_check_package_updates' );
			return;
		}

		// Check again right away when the installed packages have changed.
		$fingerprint = md5( (string) wp_json_encode( wpst_update_check_packages() ) );

		if ( get_site_option( 'wpst_update_check_fingerprint' ) !== $fingerprint && ! wp_next_scheduled( 'wpst_check_package_updates_now' ) ) {
			wp_schedule_single_event( time(), 'wpst_check_package_updates_now' );
		}
	}
);
add_action( 'wpst_check_package_updates_now', 'wpst_check_package_updates' );

add_action(
	'switch_theme',
	function () {
		wp_clear_scheduled_hook( 'wpst_check_package_updates' );
		wp_clear_scheduled_hook( 'wpst_check_package_updates_now' );
	}
);

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();

		if ( ! current_user_can( 'manage_options' ) || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins' ), true ) ) {
			return;
		}

		foreach ( wpst_available_updates() as $repo => $update ) {
			if ( 'plugins' === $screen->id && ! empty( $update['plugin'] ) ) {
				continue;
			}

			wp_admin_notice( wpst_update_message( $repo, $update ), array( 'type' => 'info' ) );
		}
	}
);

// Own update row: WordPress's is hidden in production and cannot update Composer packages.
add_action(
	'after_plugin_row',
	function ( $plugin_file ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		foreach ( wpst_available_updates() as $repo => $update ) {
			if ( ( $update['plugin'] ?? '' ) !== $plugin_file ) {
				continue;
			}

			$colspan = _get_list_table( 'WP_Plugins_List_Table' )->get_column_count();
			$active  = is_plugin_active( $plugin_file ) ? ' active' : '';

			printf(
				'<tr class="plugin-update-tr%1$s"><td colspan="%2$d" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>%3$s</p></div></td></tr>',
				esc_attr( $active ),
				(int) $colspan,
				wp_kses_post( wpst_update_message( $repo, $update ) )
			);
		}
	}
);
