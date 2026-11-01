<?php
/**
 * PHPStan bootstrap — declares what WordPress provides at runtime but the stubs
 * (szepeviktor/phpstan-wordpress) do not. Only used for static analysis.
 *
 * @package WP Starter Theme
 */

defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__ . '/' );
defined( 'WPINC' ) || define( 'WPINC', 'wp-includes' );
defined( 'WP_CONTENT_DIR' ) || define( 'WP_CONTENT_DIR', __DIR__ );
defined( 'WP_PLUGIN_DIR' ) || define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
defined( 'WPMU_PLUGIN_DIR' ) || define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );
defined( 'WP_DEBUG' ) || define( 'WP_DEBUG', false );
defined( 'SCRIPT_DEBUG' ) || define( 'SCRIPT_DEBUG', false );

/**
 * WordPress nav menu items are WP_Post objects with extra properties added
 * dynamically by wp_setup_nav_menu_item(). WP_Nav_Menu_Item does not exist
 * as a declared class in WordPress, so it is declared here for PHPStan.
 */
if ( ! class_exists( 'WP_Nav_Menu_Item' ) ) {
	class WP_Nav_Menu_Item {
		public int $ID                     = 0;
		public string $title               = '';
		/** @var string[] */
		public array $classes              = [];
		public string $attr_title          = '';
		public string $target              = '';
		public string $xfn                 = '';
		public string $url                 = '';
		public bool $current               = false;
		public bool $current_item_ancestor = false;
		public bool $current_item_parent   = false;
		public int $db_id                  = 0;
		public int $menu_item_parent       = 0;
		public string $object              = '';
		public int $object_id              = 0;
		public string $type                = '';
		public string $type_label          = '';
		public string $description         = '';
	}
}
