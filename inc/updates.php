<?php
/**
 * Update notices for our Composer packages.
 *
 * Sites install the parent theme and the security plugin with Composer at exact versions,
 * so WordPress's own update system does not know about them (and Bedrock turns it off in
 * production). Every hour WP-Cron asks GitHub for the latest tag of each package — and
 * right away when the installed packages change — and admins see a notice when a newer
 * version exists, like WordPress does for plugins. Updating is still done with Composer.
 *
 * @package WP Starter Theme
 */

/**
 * Packages to check, keyed by GitHub repo (same as the Composer package name).
 *
 * A child theme or plugin can add its own packages with the `wpst_update_check_packages` filter.
 * `plugin` is the plugin file relative to the plugins folder, for regular plugins only —
 * they get an update row in the plugin list.
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

	// Our plugins, checked when they are installed on the site.
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
 * Packages with a newer version on GitHub, from the stored versions (no HTTP request).
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
 * The update message for a package (HTML).
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
 * Highest x.y.z tag of a public GitHub repo, or an empty string if it cannot be fetched.
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
 * Cron job: store the latest version of every package. A failed request keeps the
 * previously stored version, so a GitHub hiccup never hides a known update.
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
		// Hourly. Sites scheduled before 1.3.1 (twice a day) are moved to hourly.
		if ( 'hourly' !== wp_get_schedule( 'wpst_check_package_updates' ) ) {
			wp_clear_scheduled_hook( 'wpst_check_package_updates' );
			wp_schedule_event( time(), 'hourly', 'wpst_check_package_updates' );
			return;
		}

		// Check right away (in the background) when a package was added, removed or
		// updated since the last check, e.g. after `composer require`.
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

/**
 * Admin notice on the dashboard, themes and plugins screens, like WordPress's own
 * update notices. Only reads the stored versions — never calls GitHub on page load.
 * On the plugins screen, regular plugins get an update row instead (below).
 */
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

/**
 * Update row below a plugin in the plugin list, styled like WordPress's own. WordPress's
 * row is not used, since Bedrock hides it in production and its "Update now" cannot
 * update a Composer package.
 */
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

			$list_table = _get_list_table( 'WP_Plugins_List_Table' );
			$colspan    = $list_table ? $list_table->get_column_count() : 3;
			$active     = is_plugin_active( $plugin_file ) ? ' active' : '';

			printf(
				'<tr class="plugin-update-tr%1$s"><td colspan="%2$d" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>%3$s</p></div></td></tr>',
				esc_attr( $active ),
				(int) $colspan,
				wp_kses_post( wpst_update_message( $repo, $update ) )
			);
		}
	}
);
