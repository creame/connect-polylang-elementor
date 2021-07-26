<?php
namespace ConnectPolylangElementor\Finder;

defined( 'ABSPATH' ) || exit;


class Manager {

	use \ConnectPolylangElementor\Util\Singleton;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		add_action( 'elementor/finder/categories/init', array( $this, 'elementor_finder_add_items' ) );

	}

	/**
	 * Add categories to Elementor Finder (Elementor v2.3.0+).
	 *
	 * @since 2.0.0
	 *
	 * @param object $categories_manager
	 * @return void
	 */
	function elementor_finder_add_items( $categories_manager ) {

		$categories_manager->add_category( 'polylang-plugin', new PolylangCategory() );

	}

}

