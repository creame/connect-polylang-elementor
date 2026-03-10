<?php
/**
 * Connect Polylang for Elementor
 *
 * @package           ConnectPolylangElementor
 * @license           GPL-2.0-or-later
 * @link              https://wordpress.org/plugins/connect-polylang-elementor/
 *
 * @wordpress-plugin
 * Plugin Name:       Connect Polylang for Elementor
 * Plugin URI:        https://github.com/creame/connect-polylang-elementor
 * Description:       Connect Polylang with Elementor. Display templates in the correct language, language switcher widget, language visibility conditions and dynamic tags.
 * Version:           2.5.5
 * Author:            Creame
 * Author URI:        https://crea.me/
 * License:           GPL-2.0-or-later
 * License URI:       https://opensource.org/licenses/GPL-2.0
 * Text Domain:       connect-polylang-elementor
 * Domain Path:       /languages/
 * Requires WP:       5.4
 * Requires PHP:      5.6
 * Requires Plugins:  polylang, elementor
 * Elementor tested up to: 3.34.0
 * Elementor Pro tested up to: 3.34.0
 *
 * Copyright (c) 2021 Paco Toledo - CREAME
 * Copyright (c) 2018-2021 David Decker - DECKERWEB
 */

namespace ConnectPolylangElementor;

defined( 'ABSPATH' ) || exit;


/**
 * Setting constants.
 *
 * @since 2.0.0
 */
define( 'CPEL_PLUGIN_VERSION', '2.5.5' );
define( 'CPEL_FILE', __FILE__ );
define( 'CPEL_DIR', plugin_dir_path( CPEL_FILE ) );
define( 'CPEL_BASENAME', plugin_basename( CPEL_FILE ) );


/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the plugin.
 *
 * @since 2.0.0
 */
spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__; // project-specific namespace prefix.
		$base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix.

		// Does the class use the namespace prefix? No, move to the next registered autoloader.
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class_name = substr( $class, $len );

		// Replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name,
		// append with .php and transform CamelCase to lower-dashed.
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class_name );
		$file = strtolower( preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $file ) );
		$path = $base_dir . $file . '.php';

		if ( file_exists( $path ) ) {
			require $path;
		}
	}
);


// Initialize plugin.
add_action( 'plugins_loaded', 'ConnectPolylangElementor\\setup', 20 );

// Fixes CROSS Domain issues (add before Elementor & Polylang start).
add_filter( 'plugins_url', 'ConnectPolylangElementor\\fix_cross_domain_assets' );
add_filter( 'pll_context', 'ConnectPolylangElementor\\fix_elementor_editor_context' );


/**
 * Plugin setup.
 *
 * @since 2.0.0
 *
 * @return void
 */
function setup() {

	require CPEL_DIR . 'includes/functions.php';

	if ( cpel_is_polylang_api_active() && cpel_is_elementor_active() ) {

		ElementorAssets::instance();
		ConnectPlugins::instance();
		LanguageVisibility::instance();
		DynamicTags\Manager::instance();
		Finder\Manager::instance();
		Widgets\Manager::instance();

	}

	if ( is_admin() || is_network_admin() ) {

		AdminExtras::instance();

	}
}

/**
 * Fixes cross origin domain issues with Elementor and Polylang
 *
 * Must be run before loading Elementor.
 *
 * View https://github.com/polylang/polylang/issues/590
 * View https://gist.github.com/JoryHogeveen/1a9f41406f2e1f1b542d725a1954f774
 *
 * @since 2.1.0
 * @param  string $url
 * @return string
 */
function fix_cross_domain_assets( $url ) {

	if ( false === strpos( $url, 'elementor' ) ) {
		return $url;
	}

	if ( defined( 'WP_CLI' ) ) {
		return $url;
	}

	$pll_options = get_option( 'polylang' );

	// Is a multidomain configuration.
	if ( isset( $pll_options['force_lang'] ) && 3 === $pll_options['force_lang'] && ! empty( $pll_options['domains'] ) ) {

		$srv_host = wp_parse_url( '//' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ), PHP_URL_HOST );
		$url_host = wp_parse_url( $url, PHP_URL_HOST );

		if ( $url_host ) {
			foreach ( $pll_options['domains'] as $domain ) {
				$domain_host = wp_parse_url( $domain, PHP_URL_HOST );
				if ( $domain_host === $srv_host ) {
					$url = str_replace( $url_host, $srv_host, $url );
					break;
				}
			}
		}
	}

	return $url;

}

/**
 * Fix Elementor editor context
 *
 * Load PLL_Admin class for Elementor editor.
 *
 * @since 2.1.0
 * @param  string $class
 * @return string
 */
function fix_elementor_editor_context( $class ) {

	return 'PLL_Frontend' === $class && is_admin() && isset( $_GET['action'] ) && 'elementor' === sanitize_key( wp_unslash( $_GET['action'] ) ) ? 'PLL_Admin' : $class;

}

