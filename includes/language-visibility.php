<?php
namespace ConnectPolylangElementor;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;


class LanguageVisibility {

	use \ConnectPolylangElementor\Util\Singleton;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		// Editor styles.
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_styles' ) );

		// Editor add extras settings.
		$visibility_settings = array( $this, 'visibility_settings' );
		add_action( 'elementor/element/section/section_advanced/after_section_end', $visibility_settings, 10, 2 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', $visibility_settings, 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', $visibility_settings, 10, 2 );
		add_action( 'elementor/element/common/_section_style/after_section_end', $visibility_settings, 10, 2 );

		// Front check visibility.
		$visibility_check = array( $this, 'visibility_check' );
		add_filter( 'elementor/frontend/section/should_render', $visibility_check, 10, 2 );
		add_filter( 'elementor/frontend/column/should_render', $visibility_check, 10, 2 );
		add_filter( 'elementor/frontend/container/should_render', $visibility_check, 10, 2 );
		add_filter( 'elementor/frontend/widget/should_render', $visibility_check, 10, 2 );

		add_filter( 'elementor/element/is_dynamic_content', array( $this, 'filter_element_caching_is_dynamic_content' ), 10, 3 );

	}

	/**
	 * Add preview styles for elements with language visibility enabled
	 *
	 * @return void
	 */
	public function preview_styles() {

		wp_add_inline_style( 'editor-preview', '.cpel-lv--yes {outline:2px dashed #d5dadf;}' );

	}

	/**
	 * Add visibility settings
	 *
	 * @param  mixed $element
	 * @param  mixed $section_id
	 * @return void
	 */
	public function visibility_settings( $element, $section_id ) {

		$languages = pll_languages_list( array( 'fields' => '' ) );
		$dropdown  = wp_list_pluck( $languages, 'name', 'slug' );

		$element->start_controls_section(
			'cpel_lv_section',
			array(
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => __( 'Language Visibility', 'connect-polylang-elementor' ),
			)
		);

		$element->add_control(
			'cpel_lv_enabled',
			array(
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Enable', 'connect-polylang-elementor' ),
				'render_type'    => 'template',
				'prefix_class'   => 'cpel-lv--',
				'style_transfer' => false,
			)
		);

		$element->add_control(
			'cpel_lv_action',
			array(
				'label'     => __( 'Visibility', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'show' => array(
						'title' => __( 'Show', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-preview-medium',
					),
					'hide' => array(
						'title' => __( 'Hide', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'show',
				'condition' => array(
					'cpel_lv_enabled' => 'yes',
				),
			)
		);

		$element->add_control(
			'cpel_lv_languages',
			array(
				'label'       => __( 'When language is:', 'connect-polylang-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => array(),
				'multiple'    => true,
				'options'     => $dropdown,
				'condition'   => array(
					'cpel_lv_enabled' => 'yes',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Check render language visibility
	 *
	 * @param  bool         $should_render
	 * @param  Element_Base $element
	 * @return bool
	 */
	public function visibility_check( $should_render, $element ) {

		$settings  = $element->get_settings();
		$enabled   = ! empty( $settings['cpel_lv_enabled'] ) ? $settings['cpel_lv_enabled'] : false;
		$enabled   = filter_var( $enabled, FILTER_VALIDATE_BOOLEAN );
		$languages = isset( $settings['cpel_lv_languages'] ) ? (array) $settings['cpel_lv_languages'] : array();
		$show      = isset( $settings['cpel_lv_action'] ) ? 'hide' !== $settings['cpel_lv_action'] : true;

		if ( ! $enabled || empty( $languages ) ) {
			return $should_render;
		}

		return in_array( pll_current_language(), $languages, true ) ? $show : ! $show;

	}

	/**
	 * Mark element as dynamic content if language visibility is enabled
	 *
	 * @param  bool         $is_dynamic_content
	 * @param  array        $element_rqw_data
	 * @param  Element_Base $element_instance
	 * @return bool
	 */
	public function filter_element_caching_is_dynamic_content( $is_dynamic_content, $element_rqw_data, $element_instance ) {

		if ( isset( $element_rqw_data['settings']['cpel_lv_enabled'] ) && 'yes' === $element_rqw_data['settings']['cpel_lv_enabled'] ) {
			$is_dynamic_content = true;
		}

		return $is_dynamic_content;

	}

}
