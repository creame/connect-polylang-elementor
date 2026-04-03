<?php
namespace ConnectPolylangElementor;

defined( 'ABSPATH' ) || exit;

/**
 * Fixes cross origin domain issues with Elementor and Polylang
 *
 * Based on code from JoryHogeveen: https://gist.github.com/JoryHogeveen/1a9f41406f2e1f1b542d725a1954f774
 */
class ElementorAssets {

	use \ConnectPolylangElementor\Util\Singleton;

	protected $current_domain = '';
	protected $default_domain = '';
	protected $all_domains    = array();

	/**
	 * Add actions if is multidomain
	 *
	 * @return void
	 */
	protected function __construct() {

		if ( ! cpel_is_polylang_multidomain() ) {
			return;
		}

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'editor_domain_redirect' ) );

	}

	/**
	 * Initialize
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function init() {

		// Prepare domains info.
		$return           = OBJECT;
		$current_language = pll_current_language( $return );
		$default_language = pll_default_language( $return );

		if ( ! $current_language || ! $default_language ) {
			return;
		}

		$preview_val = isset( $_GET['elementor_preview'] ) ? sanitize_key( wp_unslash( $_GET['elementor_preview'] ) ) : '';
		$is_preview  = $preview_val !== '';

		if ( ! cpel_is_elementor_editor() && ! $is_preview ) {
			return;
		}

		$domains = pll_languages_list( array( 'fields' => 'home_url' ) );

		foreach ( $domains as $domain ) {
			$this->all_domains[] = wp_parse_url( $domain, PHP_URL_HOST );
		}

		if ( method_exists( $current_language, 'get_home_url' ) ) {
			$this->current_domain = $current_language->get_home_url();
			$this->default_domain = $default_language->get_home_url();
		} else {
			$this->current_domain = $current_language->home_url;
			$this->default_domain = $default_language->home_url;
		}

		// Add filters.
		add_filter( 'script_loader_src', array( $this, 'translate_url' ) );
		add_filter( 'style_loader_src', array( $this, 'translate_url' ) );

		add_filter( 'allowed_http_origins', array( $this, 'add_allowed_origins' ) );

		add_filter( 'admin_url', array( $this, 'replace_ajax_url' ), 10, 3 );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'replace_src' ), 10, 2 );

		add_filter( 'elementor/editor/localize_settings', array( $this, 'translate_url_recursive' ) );

	}

	/**
	 * Translate URL domain
	 *
	 * @since 2.1.0
	 * @param  string $url
	 * @return string
	 */
	public function translate_url( $url ) {

		return str_replace( $this->default_domain, $this->current_domain, $url );

	}

	/**
	 * Translate URL domain recursive
	 *
	 * @since 2.1.0
	 * @param  mixed $data
	 * @return mixed
	 */
	public function translate_url_recursive( $data ) {

		if ( is_string( $data ) ) {
			$data = $this->translate_url( $data );
		} elseif ( is_array( $data ) ) {
			$data = array_map( array( $this, 'translate_url_recursive' ), $data );
		}

		return $data;

	}

	/**
	 * Add all domains to allowed origins
	 *
	 * @since 2.1.0
	 * @param  array $origins
	 * @return array
	 */
	public function add_allowed_origins( $origins ) {

		foreach ( $this->all_domains as $domain ) {
			$origins = array_merge( $origins, array( 'http://' . $domain, 'https://' . $domain ) );
		}

		return array_unique( $origins );

	}

	/**
	 * Replace domain for admin-ajax.php
	 *
	 * @since 2.1.0
	 * @param  string $url
	 * @param  string $path
	 * @param  int    $blog_id
	 * @return string
	 */
	public function replace_ajax_url( $url, $path, $blog_id ) {

		return 'admin-ajax.php' === $path ? $this->translate_url( $url ) : $url;

	}

	/**
	 * Replace domain for image src
	 *
	 * @since 2.1.0
	 * @param  mixed $attr
	 * @param  mixed $attachment
	 * @return void
	 */
	public function replace_src( $attr, $attachment ) {

		$attr['src'] = $this->translate_url( $attr['src'] );

		if ( isset( $attr['srcset'] ) ) {
			$attr['srcset'] = $this->translate_url( $attr['srcset'] );
		}

		return $attr;

	}

	/**
	 * Redirect Elementor editor with post domain
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function editor_domain_redirect() {

		if ( ! cpel_is_elementor_editor() ) {
			return;
		}

		$current_url = add_query_arg( wp_unslash( $_SERVER['QUERY_STRING'] ), '', admin_url( 'post.php' ) );
		$server_host = wp_parse_url( '//' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ), PHP_URL_HOST );
		$post_host   = wp_parse_url( pll_get_post_language( absint( $_GET['post'] ), 'home_url' ), PHP_URL_HOST );

		if ( null !== $post_host && $server_host !== $post_host ) {
			wp_safe_redirect( str_replace( $server_host, $post_host, $current_url ) );
			exit;
		}

	}
}

