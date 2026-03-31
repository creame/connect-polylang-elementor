<?php
namespace ConnectPolylangElementor\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;

defined( 'ABSPATH' ) || exit;


/**
 * Add the "Polylang" category to the Elementor Finder.
 *   - Settings pages
 *   - Plugin resources
 *
 * @since 1.0.0
 */
class PolylangCategory extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Translateable category title.
	 */
	public function get_title() {

		return __( 'Languages', 'connect-polylang-elementor' );

	}

	/**
	 * Get a unique category ID.
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_id() {

		return 'cpel';

	}

	/**
	 * Get category items.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @uses pll_languages_list() Holds array of Polylang languages.
	 *
	 * @param array $options
	 * @return array $items Filterable array of additional Finder items.
	 */
	public function get_category_items( array $options = array() ) {

		$items['languages'] = array(
			'title'       => _x( 'Setup Languages', 'Title in Elementor Finder', 'connect-polylang-elementor' ),
			'url'         => admin_url( 'admin.php?page=mlang' ),
			'icon'        => 'comments',
			'keywords'    => explode( ',', _x( 'polylang,languages,setup,flags,country,countries', 'Tags in Elementor Finder', 'connect-polylang-elementor' ) ),
			'description' => __( 'All languages of your website', 'connect-polylang-elementor' ),
		);

		$items['string-translations'] = array(
			'title'       => _x( 'String Translations', 'Title in Elementor Finder', 'connect-polylang-elementor' ),
			'url'         => admin_url( 'admin.php?page=mlang_strings' ),
			'icon'        => 'exchange',
			'keywords'    => explode( ',', _x( 'polylang,translations,translate,strings', 'Tags in Elementor Finder', 'connect-polylang-elementor' ) ),
			'description' => __( 'From Widgets and other website parts', 'connect-polylang-elementor' ),
		);

		$items['settings'] = array(
			'title'       => _x( 'Settings', 'Title in Elementor Finder', 'connect-polylang-elementor' ),
			'url'         => admin_url( 'admin.php?page=mlang_settings' ),
			'icon'        => 'settings',
			'keywords'    => explode( ',', _x( 'polylang,settings,options,modules', 'Tags in Elementor Finder', 'connect-polylang-elementor' ) ),
			'description' => __( "Plugin's settings, enable/disable modules", 'connect-polylang-elementor' ),
		);

		// List all defined languages.
		$languages = (array) pll_languages_list( array( 'fields' => false ) );

		foreach ( $languages as $lang_data ) {

			$url = method_exists( $lang_data, 'get_home_url' ) ? $lang_data->get_home_url() : $lang_data->home_url;

			$items[ 'website-language-' . $lang_data->slug ] = array(
				'title'       => $lang_data->name,
				'url'         => esc_url( $url ),
				'icon'        => 'eye',
				'keywords'    => array(
					'polylang',
					'language',
					'home',
					'website',
					$lang_data->name,
					$lang_data->slug,
					$lang_data->locale,
				),
				'description' => __( 'View website in this language', 'connect-polylang-elementor' ),
				'actions'     => array(
					array(
						'name' => 'settings',
						'url'  => esc_url_raw( admin_url( 'admin.php?page=mlang&pll_action=edit&lang=' . absint( $lang_data->term_id ) ) ),
						'icon' => 'settings',
					),
				),
			);
		}

		return apply_filters( 'cpel/filter/elementor_finder/items/polylang', $items );

	}

}
