<?php
namespace ConnectPolylangElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;


/**
 * Polylang Switcher
 *
 * Elementor widget for Polylang Language Switcher.
 *
 * Note: Code based on Widget class of plugin "Language Switcher for Elementor",
 *       licensed under GPLv2 or later.
 *
 * @author Solitweb
 * @link https://solitweb.be/
 *
 * @since 1.0.0
 */
class PolylangLanguageSwitcher extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {

		return 'polylang-language-switcher';

	}


	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {

		return __( 'Language switcher', 'connect-polylang-elementor' );

	}


	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {

		return 'eicon-global-settings';

	}


	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {

		return array( 'general', 'theme-elements' );

	}


	/**
	 * Set keywords for widgets search.
	 *
	 * @since 1.0.0
	 */
	public function get_keywords() {

		$keywords = _x(
			'languages, switcher, polylang, multilingual, flags, countries, country',
			'Comma separated keywords',
			'connect-polylang-elementor'
		);

		return explode( ', ', $keywords );

	}


	/**
	 * Retrieve the list of styles the widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {

		return array( 'cpel-language-switcher' );

	}


	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {

		return array();

	}


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the
	 *   widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 *
	 * @uses pll_the_languages()
	 */
	protected function register_controls() {

		/** Content: Layout etc. */
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'connect-polylang-elementor' ) )
		);

		$this->add_control(
			'layout',
			array(
				'label'        => __( 'Layout', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'horizontal' => __( 'Horizontal', 'connect-polylang-elementor' ),
					'vertical'   => __( 'Vertical', 'connect-polylang-elementor' ),
					'dropdown'   => __( 'Dropdown', 'connect-polylang-elementor' ),
				),
				'default'      => 'horizontal',
				'prefix_class' => 'cpel-switcher--layout-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'align_items',
			array(
				'label'        => __( 'Alignment', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'    => array(
						'title' => __( 'Left', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
					'justify' => array(
						'title' => __( 'Stretch', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'prefix_class' => 'cpel-switcher--align-',
			)
		);

		$this->add_control(
			'hide_current',
			array(
				'label'        => __( 'Hides the current language', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'hide_missing',
			array(
				'label'        => __( 'Hides languages with no translation', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_country_flag',
			array(
				'label'        => __( 'Displays flags', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_language_name',
			array(
				'label'        => __( 'Displays language names', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_language_code',
			array(
				'label'        => __( 'Displays language codes', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		/** Style: Main menu */
		$this->start_controls_section(
			'main_section',
			array(
				'label' => __( 'Main Menu', 'connect-polylang-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			array( 'label' => __( 'Normal', 'connect-polylang-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_menu_item',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .cpel-switcher__lang a',
			)
		);

		$this->add_control(
			'color_menu_item',
			array(
				'label'     => __( 'Text Color', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__lang a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cpel-switcher__icon'   => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'bg_dropdown_item',
			array(
				'label'     => __( 'Background Color', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}}.cpel-switcher--layout-dropdown .cpel-switcher__lang a' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'layout' => 'dropdown' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			array( 'label' => __( 'Hover', 'connect-polylang-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_menu_item_hover',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .cpel-switcher__lang a:hover, {{WRAPPER}} .cpel-switcher__lang a:focus',
			)
		);

		$this->add_control(
			'color_menu_item_hover',
			array(
				'label'     => __( 'Text Color', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_ACCENT ),
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__lang a:hover, {{WRAPPER}} .cpel-switcher__lang a:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'bg_dropdown_hover',
			array(
				'label'     => __( 'Background Color', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#D9D9D9',
				'selectors' => array(
					'{{WRAPPER}}.cpel-switcher--layout-dropdown .cpel-switcher__lang a:hover, {{WRAPPER}}.cpel-switcher--layout-dropdown .cpel-switcher__lang a:focus' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'layout' => 'dropdown' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			array(
				'label'     => __( 'Active', 'connect-polylang-elementor' ),
				'condition' => array(
					'hide_current!' => 'yes',
					'layout!'       => 'dropdown',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_menu_item_active',
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .cpel-switcher__lang.cpel-switcher__lang--active a',
			)
		);

		$this->add_control(
			'color_menu_item_active',
			array(
				'label'     => __( 'Text Color', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .cpel-switcher__lang--active a' => 'color: {{VALUE}}' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'padding_horizontal_menu_item',
			array(
				'label'     => __( 'Horizontal Padding', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 50 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__lang a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'padding_vertical_menu_item',
			array(
				'label'     => __( 'Vertical Padding', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__lang a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'menu_space_between',
			array(
				'label'     => __( 'Space Between', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 100 ),
				),
				'default'   => array( 'size' => 15 ),
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__nav' => '--cpel-switcher-space: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'layout!' => 'dropdown' ),
			)
		);

		$this->end_controls_section();

		/**
		 * Style: Dropdown
		 */

		$this->start_controls_section(
			'dropdown_section',
			array(
				'label'     => __( 'Dropdown', 'connect-polylang-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => 'dropdown' ),
			)
		);

		$this->add_control(
			'dropdown_on',
			array(
				'label'        => __( 'Drop', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'click' => __( 'On Click', 'connect-polylang-elementor' ),
					'hover' => __( 'On Mouse Hover', 'connect-polylang-elementor' ),
				),
				'default'      => 'click',
				'prefix_class' => 'cpel-switcher--drop-on-',
			)
		);

		$this->add_control(
			'dropdown_to',
			array(
				'label'        => __( 'Drop Direction', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'down' => array(
						'title' => __( 'Down', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
					'up'   => array(
						'title' => __( 'Up', 'connect-polylang-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
				),
				'default'      => 'down',
				'prefix_class' => 'cpel-switcher--drop-to-',
			)
		);

		$this->add_control(
			'dropdown_icon',
			array(
				'label'                  => __( 'Icon', 'connect-polylang-elementor' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'icon',
				'recommended'            => array(
					'fa-solid' => array(
						'chevron-down',
						'angle-down',
						'caret-down',
						'plus',
					),
				),
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => 'svg',
				'default'                => array(
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'dropdown_icon_indent',
			array(
				'label'     => __( 'Icon Spacing', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 50 ),
				),
				'default'   => array( 'size' => '10' ),
				'selectors' => array( '{{WRAPPER}} .cpel-switcher__icon' => 'padding-left: {{SIZE}}{{UNIT}};' ),
				'condition' => array(
					'dropdown_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style: Language flag
		 */

		$this->start_controls_section(
			'country_flag_section',
			array(
				'label'     => __( 'Flag', 'connect-polylang-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_country_flag' => 'yes' ),
			)
		);

		$this->add_control(
			'svg_flag',
			array(
				'label'        => __( 'Scalable Image', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'aspect_ratio_flag',
			array(
				'label'        => __( 'Aspect Ratio', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'43' => '4:3',
					'11' => '1:1',
				),
				'default'      => '43',
				'prefix_class' => 'cpel-switcher--aspect-ratio-',
				'condition'    => array( 'svg_flag' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'size_flag',
			array(
				'label'     => __( 'Size', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 16 ),
				),
				'default'   => array( 'size' => 20 ),
				'selectors' => array(
					'{{WRAPPER}}.cpel-switcher--aspect-ratio-11 .cpel-switcher__flag' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cpel-switcher--aspect-ratio-43 .cpel-switcher__flag' => 'width: {{SIZE}}{{UNIT}}; height: calc({{SIZE}}{{UNIT}} * 0.75);',
				),
				'condition' => array( 'svg_flag' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_flag',
			array(
				'label'      => __( 'Border Radius', 'connect-polylang-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%' => array( 'max' => 50 ),
				),
				'default'    => array( 'size' => 0 ),
				'selectors'  => array( '{{WRAPPER}} .cpel-switcher__flag img' => 'border-radius: {{SIZE}}{{UNIT}}' ),
				'condition'  => array( 'svg_flag' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/**
		 * Style: Language name
		 */

		$this->start_controls_section(
			'language_name_section',
			array(
				'label'     => __( 'Language Name', 'connect-polylang-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_language_name' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'text_indent_language_name',
			array(
				'label'     => __( 'Text Indent', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 50 ),
				),
				'default'   => array( 'size' => '10' ),
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__name' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style: Language code
		 */

		$this->start_controls_section(
			'language_code_section',
			array(
				'label'     => __( 'Language Code', 'connect-polylang-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_language_code' => 'yes' ),
			)
		);

		$this->add_control(
			'uppercase_language_code',
			array(
				'label'        => _x( 'Uppercase', 'Typography Control', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			'text_indent_language_code',
			array(
				'label'     => __( 'Text Indent', 'connect-polylang-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'max' => 50 ),
				),
				'default'   => array( 'size' => '10' ),
				'selectors' => array(
					'{{WRAPPER}} .cpel-switcher__code' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'before_language_code',
			array(
				'label' => __( 'Before', 'connect-polylang-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'after_language_code',
			array(
				'label' => __( 'After', 'connect-polylang-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

	}


	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.0.0
	 *
	 * @access protected
	 *
	 * @uses pll_the_languages() Holds Polylang languages for switcher.
	 * @uses pll_current_language() Get the current language.
	 * @return void
	 */
	protected function render() {

		// Get the widget settings.
		$settings = $this->get_active_settings();

		// Add render attributes for Elementor.
		$this->add_render_attribute(
			array(
				'nav'  => array(
					'class' => 'cpel-switcher__nav',
				),
				'icon' => array(
					'class'       => array(
						'cpel-switcher__icon',
						empty( $settings['dropdown_icon']['value'] ) ? '' : $settings['dropdown_icon']['value'],
					),
					'aria-hidden' => 'true',
				),
			)
		);

		// Get the available languages for switcher.
		$languages = pll_the_languages( array( 'raw' => 1 ) );
		$lang_curr = strtolower( pll_current_language() );

		// Max number of items in language dropdown
		if ( 'dropdown' === $settings['layout'] ) {
			$this->add_render_attribute( '_wrapper', 'style', '--langs:' . ( absint( count( $languages ) - 1 ) ) );
		}

		if ( ! empty( $languages ) ) {

			$lang_links = array();

			foreach ( $languages as $lang_code => $language ) {

				// Hide the current language.
				if ( 'yes' === $settings['hide_current'] && $language['current_lang'] ) {
					continue;
				}

				// Hide language without translation.
				if ( 'yes' === $settings['hide_missing'] && $language['no_translation'] ) {
					continue;
				}

				// Language code.
				$language_code = sprintf(
					'%s%s%s',
					$settings['before_language_code'] ? $settings['before_language_code'] : '',
					'yes' === $settings['uppercase_language_code'] ? strtoupper( $language['slug'] ) : strtolower( $language['slug'] ),
					$settings['after_language_code'] ? $settings['after_language_code'] : ''
				);

				// Language flag.
				$language_flag = '';
				if ( $settings['show_country_flag'] ) {
					$flag_code = cpel_flag_code( $language['flag'] );
					$flag_svg  = $flag_code ? cpel_flag_svg( $flag_code ) : false;

					if ( 'yes' === $settings['svg_flag'] && $flag_svg ) {

						// If data uri encoded flags are preferred.
						if ( ! defined( 'PLL_ENCODED_FLAGS' ) || PLL_ENCODED_FLAGS ) {
							$file_contents   = file_get_contents( CPEL_DIR . $flag_svg['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
							$flag_svg['src'] = 'data:image/svg+xml;base64,' . base64_encode( $file_contents );
						} else {
							$flag_svg['src'] = $flag_svg['url'];
						}

						$language_flag = \PLL_Language::get_flag_html( $flag_svg, '', $language['name'] );
					} elseif ( $flag_code ) {
						$flag_information = method_exists( '\PLL_Language', 'get_flag_information' ) ? \PLL_Language::get_flag_information( $flag_code ) : \PLL_Language::get_flag_informations( $flag_code );
						$language_flag    = \PLL_Language::get_flag_html( $flag_information, '', $language['name'] );
					} else {
						$language_flag = '<img src="' . esc_url( $language['flag'] ) . '" alt="' . esc_attr( $language['name'] ) . '" />';
					}

					if ( $flag_code ) {
						$language_flag = '<span class="cpel-switcher__flag cpel-switcher__flag--' . $flag_code . '">' . $language_flag . '</span>';
					} else {
						$language_flag = '<span class="cpel-switcher__flag">' . $language_flag . '</span>';
					}
				}

				// Language link.
				$lang_links[ strtolower( $lang_code ) ] = sprintf(
					'<a lang="%1$s" hreflang="%1$s" href="%2$s">%3$s%4$s%5$s</a>',
					esc_attr( $language['locale'] ),
					esc_url( $language['url'] ),
					$language_flag,
					$settings['show_language_name'] ? '<span class="cpel-switcher__name">' . esc_html( $language['name'] ) . '</span>' : '',
					$settings['show_language_code'] ? '<span class="cpel-switcher__code">' . esc_html( $language_code ) . '</span>' : ''
				);
			}

			$output = '<nav ' . $this->get_render_attribute_string( 'nav' ) . '>';

			// Dropdown toggle link.
			if ( count( $lang_links ) && 'dropdown' === $settings['layout'] ) {
				$lang_code = array_key_exists( $lang_curr, $lang_links ) ? $lang_curr : current( array_keys( $lang_links ) );
				$lang_link = $lang_links[ $lang_code ];

				unset( $lang_links[ $lang_code ] );

				if ( ! empty( $settings['dropdown_icon']['value'] ) && count( $lang_links ) ) {
					$lang_link = str_replace( '</a>', '<i ' . $this->get_render_attribute_string( 'icon' ) . '></i></a>', $lang_link );
				}

				$output .= '<div class="cpel-switcher__toggle cpel-switcher__lang" onclick="this.classList.toggle(\'cpel-switcher__toggle--on\')">' . $lang_link . '</div>';
			}

			// Languages list.
			if ( count( $lang_links ) ) {

				$output .= '<ul class="cpel-switcher__list">';

				foreach ( $lang_links as $lang_code => $lang_link ) {
					$output .= '<li class="cpel-switcher__lang' . ( $lang_code === $lang_curr ? ' cpel-switcher__lang--active' : '' ) . '">' . $lang_link . '</li>';
				}

				$output .= '</ul>';
			}

			$output .= '</nav>';

			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		}

	}

}
