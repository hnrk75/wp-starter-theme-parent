<?php
/**
 * Update notices for our Composer packages.
 *
 * Sites install the parent theme and the security plugin with Composer at exact versions,
 * so WordPress's own update system does not know about them (and Bedrock turns it off in
 * production). Twice a day WP-Cron asks GitHub for the latest tag of each package, and
 * admins see a notice when a newer version exists — like WordPress does for plugins.
 * Updating is still done with Composer.
 *
 * @package WP Starter Theme
 */

/**
 * Packages to check, keyed by GitHub repo (same as the Composer package name).
 *
 * A child theme or plugin can add its own packages with the `wpst_update_check_packages` filter.
 *
 * @return array<string, array{name: string, version: string}>
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
		'hnrk75/wp-security-hardening' => WPMU_PLUGIN_DIR . '/wp-security-hardening/wp-security-hardening.php',
		'hnrk75/hnrk-cookie-consent'   => WP_PLUGIN_DIR . '/hnrk-cookie-consent/hnrk-cookie-consent.php',
	);

	foreach ( $plugins as $repo => $file ) {
		if ( ! file_exists( $file ) ) {
			continue;
		}

		$data = get_file_data(
			$file,
			array(
				'Name'    => 'Plugin Name',
				'Version' => 'Version',
			)
		);

		$packages[ $repo ] = array(
			'name'    => $data['Name'],
			'version' => $data['Version'],
		);
	}

	return apply_filters( 'wpst_update_check_packages', $packages );
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
	$latest = (array) get_site_option( 'wpst_latest_versions', array() );

	foreach ( array_keys( wpst_update_check_packages() ) as $repo ) {
		$version = wpst_fetch_latest_version( $repo );

		if ( $version ) {
			$latest[ $repo ] = $version;
		}
	}

	update_site_option( 'wpst_latest_versions', $latest );
}
add_action( 'wpst_check_package_updates', 'wpst_check_package_updates' );

add_action(
	'admin_init',
	function () {
		if ( ! wp_next_scheduled( 'wpst_check_package_updates' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'wpst_check_package_updates' );
		}
	}
);

add_action(
	'switch_theme',
	function () {
		wp_clear_scheduled_hook( 'wpst_check_package_updates' );
	}
);

/**
 * Admin notice on the dashboard, themes and plugins screens, like WordPress's own
 * update notices. Only reads the stored versions — never calls GitHub on page load.
 */
add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();

		if ( ! current_user_can( 'manage_options' ) || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins' ), true ) ) {
			return;
		}

		$latest = (array) get_site_option( 'wpst_latest_versions', array() );

		foreach ( wpst_update_check_packages() as $repo => $package ) {
			$new = $latest[ $repo ] ?? '';

			if ( ! $new || ! $package['version'] || ! version_compare( $new, $package['version'], '>' ) ) {
				continue;
			}

			wp_admin_notice(
				sprintf(
					/* translators: 1: package name, 2: new version, 3: installed version, 4: Composer package name, 5: changelog URL */
					__( '<strong>%1$s %2$s is available</strong> (installed: %3$s). It is updated with Composer: set the version to %2$s in composer.json and run <code>composer update %4$s</code>. <a href="%5$s" target="_blank" rel="noopener noreferrer">See what has changed</a>', 'wp-starter-theme' ),
					esc_html( $package['name'] ),
					esc_html( $new ),
					esc_html( $package['version'] ),
					esc_html( $repo ),
					esc_url( 'https://github.com/' . $repo . '/blob/main/CHANGELOG.md' )
				),
				array( 'type' => 'info' )
			);
		}
	}
);
