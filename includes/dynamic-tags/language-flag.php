<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

defined( 'ABSPATH' ) || exit;


class LanguageFlag extends Data_Tag {

	use TagTrait;

	public function get_name() {
		return 'language-flag';
	}

	public function get_title() {
		return _x( 'Language Flag', 'Elementor Dynamic Tag title', 'connect-polylang-elementor' );
	}

	public function get_categories() {
		return array( Module::IMAGE_CATEGORY );
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

		$this->add_control(
			'svg_flag',
			array(
				'label'        => __( 'Scalable Image', 'connect-polylang-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

	}

	public function get_value( array $options = array() ) {

		$settings = $this->get_settings();

		$image_data = array(
			'id'  => '',
			'url' => $this->get_language_field( 'flag' ),
		);

		if ( 'yes' === $settings['svg_flag'] ) {
			$flag_code         = cpel_flag_code( $image_data['url'] );
			$flag_svg          = $flag_code ? cpel_flag_svg( $flag_code ) : false;
			$image_data['url'] = $flag_svg ? $flag_svg['url'] : $image_data['url'];
		}

		return $image_data;
	}

}
