<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;


trait TagTrait {

	final public function get_group() {
		return Manager::TAG_GROUP;
	}

	protected function register_controls() {

		$languages = pll_languages_list( array( 'fields' => '' ) );
		$options   = wp_list_pluck( $languages, 'name', 'slug' );
		$options   = array( 'current' => __( 'Current Language', 'connect-polylang-elementor' ) ) + $options;

		$this->add_control(
			'language',
			array(
				'label'   => __( 'Language', 'connect-polylang-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'current',
			)
		);

	}

	protected function get_language_field( $field ) {

		$settings = $this->get_settings();

		$language = $settings['language'];
		$value    = '';

		$languages = pll_the_languages( array( 'raw' => 1 ) );
		if ( is_array( $languages ) ) {
			if ( 'current' === $language ) {
				$current_lang = pll_current_language();
				// If no current language, fallback to default language
				if ( ! $current_lang ) {
					$current_lang = pll_default_language();
				}
				if ( $current_lang && isset( $languages[ $current_lang ] ) && isset( $languages[ $current_lang ][ $field ] ) ) {
					$value = $languages[ $current_lang ][ $field ];
				}
			} elseif ( isset( $languages[ $language ] ) && isset( $languages[ $language ][ $field ] ) ) {
				$value = $languages[ $language ][ $field ];
			}
		}

		return $value;

	}

}
