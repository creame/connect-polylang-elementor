<?php
namespace ConnectPolylangElementor\DynamicTags;

use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;


class Manager {

	use \ConnectPolylangElementor\Util\Singleton;

	const TAG_GROUP = 'polylang-languages';

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		$hook = cpel_elementor_min_version( '3.5.0' )
			? 'elementor/dynamic_tags/register'
			: 'elementor/dynamic_tags/register_tags';

		add_action( $hook, array( $this, 'register_dynamic_tags' ) );

	}

	/**
	 * Register language Dynamic Tags for Elementor, including a new Tag Group.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_dynamic_tags( $dynamic_tags ) {

		// Register our tag group.
		Plugin::instance()->dynamic_tags->register_group( self::TAG_GROUP, array( 'title' => __( 'Languages', 'connect-polylang-elementor' ) ) );

		// Register the tags.
		if ( cpel_elementor_min_version( '3.5.0' ) ) {
			$dynamic_tags->register( new LanguageName() );
			$dynamic_tags->register( new LanguageCode() );
			$dynamic_tags->register( new LanguageFlag() );
			$dynamic_tags->register( new LanguageUrl() );
		} else {
			$dynamic_tags->register_tag( __NAMESPACE__ . '\\LanguageName' );
			$dynamic_tags->register_tag( __NAMESPACE__ . '\\LanguageCode' );
			$dynamic_tags->register_tag( __NAMESPACE__ . '\\LanguageFlag' );
			$dynamic_tags->register_tag( __NAMESPACE__ . '\\LanguageUrl' );
		}

	}

}
