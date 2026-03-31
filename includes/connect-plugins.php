<?php
namespace ConnectPolylangElementor;

use Elementor\Core\Settings\Manager as SettingsManager;


defined( 'ABSPATH' ) || exit;


class ConnectPlugins {

	use \ConnectPolylangElementor\Util\Singleton;

	/**
	 * Current template ID.
	 *
	 * @var int|null
	 */
	private $template_id = null;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

		// Auto add post types for translation.
		add_filter( 'pll_get_post_types', array( $this, 'add_polylang_post_types' ), 10, 2 );

		// Front template loading.
		add_filter( 'elementor/theme/get_location_templates/template_id', array( $this, 'template_id_translation' ) );
		add_filter( 'elementor/theme/get_location_templates/condition_sub_id', array( $this, 'condition_sub_id_translation' ), 10, 2 );

		// Shortcode [elementor-template] translate 'template_id'.
		add_filter( 'pre_do_shortcode_tag', array( $this, 'shortcode_template_translate' ), 10, 3 );
		// Widget Template translate 'template_id'.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'widget_template_translate' ) );
		// WordPress Widget "Elementor Library" translate 'template_id'.
		add_filter( 'widget_display_callback', array( $this, 'wp_widget_template_translate' ), 10, 2 );

		// Elementor Kit template loading.
		add_filter( 'option_elementor_active_kit', array( $this, 'elementor_kit_translation' ) );

		// Fix home_url() for site-url Dynamic Tag and Search Form widget.
		add_filter( 'pll_home_url_white_list', array( $this, 'elementor_home_url_white_list' ) );
		add_filter( 'home_url', array( $this, 'home_url_language_dir_slash' ), 11, 2 );

		// Translate Elementor order resume page (thankyou page).
		add_filter( 'option_elementor_woocommerce_purchase_summary_page_id', 'pll_get_post' );

		// Fix search url for Search Form widget.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'add_search_form_home_url_filter' ) );
		add_action( 'elementor/frontend/widget/after_render', array( $this, 'remove_search_form_home_url_filter' ) );

		if ( is_admin() ) {

			// All langs for template conditions & global widgets.
			add_action( 'parse_query', array( $this, 'query_all_languages' ), 1 );

			// Empty template conditions on translations.
			add_filter( 'get_post_metadata', array( $this, 'elementor_conditions_empty_on_translations' ), 10, 3 );
			add_filter( 'pre_update_option_elementor_pro_theme_builder_conditions', array( $this, 'theme_builder_conditions_remove_empty' ) );

			// Global widgets hide language column.
			add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'hide_language_column_pre' ), 9, 2 );
			add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'hide_language_column_pos' ), 11, 2 );

			if ( cpel_is_elementor_pro_active() ) {
				// Update template conditions on language terms change.
				add_action( 'set_object_terms', array( $this, 'update_conditions_on_term_change' ), 10, 4 );

				// Translations conditions column.
				add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'instances_column_pre' ), 9, 2 );
				add_action( 'manage_elementor_library_posts_custom_column', array( $this, 'instances_column_pos' ), 11, 2 );
			}

			// Don't add "_elementor_css" meta.
			add_filter( 'update_post_metadata', array( $this, 'prevent_elementor_css_meta' ), 10, 3 );
			add_action( 'pll_post_synchronized', array( $this, 'bulk_delete_elementor_css_meta' ), 10, 3 );

			// Edit links for each language domain.
			if ( cpel_is_polylang_multidomain() ) {

				add_filter( 'post_row_actions', array( $this, 'fix_edit_link' ), 12, 2 );
				add_filter( 'page_row_actions', array( $this, 'fix_edit_link' ), 12, 2 );

				add_filter( 'elementor/document/urls/edit', array( $this, 'fix_elementor_edit_link' ), 10, 2 );
			}
		}

		// Check if Elementor is installed and its version is greater than 3.25.0.
		if ( cpel_elementor_min_version( '3.25.0' ) ) {
			// Elementor 3.25.0 introduced a new way to handle language switcher controls.
			add_action( 'elementor/documents/register_controls', array( $this, 'register_language_switcher_controls' ) );
		} else {
			// Deprecated way to handle language switcher controls.
			// Elementor editor menu links to translations.
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'elementor_editor_script' ) );
		}
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'elementor_editor_style' ) );

		// Elementor Site Editor template tweaks.
		add_filter( 'elementor-pro/site-editor/data/template', array( $this, 'elementor_site_editor_template' ) );

	}

	/**
	 * Enable Elementor-specific post types automatically for Polylang translation
	 *
	 * @link   https://polylang.pro/doc/filter-reference/
	 *
	 * @since  2.0.0
	 *
	 * @param array $types The list of post type names for which Polylang manages language and translations.
	 * @param bool  $is_settings  True when displaying the list in Polylang settings.
	 * @return array The list of post type names for which Polylang manages language and translations
	 */
	public function add_polylang_post_types( $types, $is_settings ) {

		$relevant_types = apply_filters(
			'cpel/filter/polylang/post_types',
			array(
				'elementor_library',   // Elementor.
				'e-landing-page',      // Elementor Landing pages.
				'oceanwp_library',     // OceanWP Library.
				'astra-advanced-hook', // Astra Custom Layouts (Astra Pro).
				'gp_elements',         // GeneratePress Elements (GP Premium).
				'jet-theme-core',      // JetThemeCore (Kava Pro/ CrocoBlock).
				'jet-engine',          // JetEngine Listing Item (CrocoBlock).
				'customify_hook',      // Customify (Customify Pro).
				'wpbf_hooks',          // Page Builder Framework Sections (WPBF Premium).
				'ae_global_templates', // AnyWhere Elementor plugin.
			)
		);

		return array_merge( $types, array_combine( $relevant_types, $relevant_types ) );

	}

	/**
	 * Query all languages if conditions meets
	 *
	 *   Note: Needs to be priority 1, since Polylang uses the action parse_query
	 *         which is fired before 'pre_get_posts'.
	 *
	 * @link  https://github.com/polylang/polylang/issues/152#issuecomment-320602328
	 * @link  https://github.com/pojome/elementor/issues/4839
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Query $query current query.
	 * @return void
	 */
	public function query_all_languages( $query ) {

		$global_widget_meta_query = array(
			'key'   => '_elementor_template_type',
			'value' => 'widget',
		);

		$is_elementor_conditions = isset( $query->query_vars['meta_key'] )
			&& '_elementor_conditions' === $query->query_vars['meta_key'];

		$is_global_widget = isset( $query->query_vars['post_type'], $query->query_vars['meta_query'] )
			&& 'elementor_library' === $query->query_vars['post_type']
			&& is_array( $query->query_vars['meta_query'] )
			&& in_array( $global_widget_meta_query, $query->query_vars['meta_query'], true );

		if ( $is_elementor_conditions || $is_global_widget ) {
			$query->set( 'lang', '' );
		}

	}

	/**
	 * Return empty conditions on secondary translations
	 *
	 * @since  2.0.0
	 *
	 * @param  mixed  $null null for bypass.
	 * @param  int    $post_id current post ID.
	 * @param  string $meta_key name of meta key.
	 * @return mixed null or empty array
	 */
	public function elementor_conditions_empty_on_translations( $null, $post_id, $meta_key ) {

		return '_elementor_conditions' === $meta_key && cpel_is_translation( $post_id ) ? array( array() ) : $null;

	}

	/**
	 * Clear empty conditions before save 'elementor_pro_theme_builder_conditions' option
	 *
	 * @since  2.0.0
	 *
	 * @param  array $value array of theme builder conditions.
	 * @return array  filtered array
	 */
	public function theme_builder_conditions_remove_empty( $value ) {

		foreach ( $value as $location => $items ) {
			$value[ $location ] = array_filter( $items );
		}

		return array_filter( $value );

	}

	/**
	 * Bypass Elementor template shortcode with their translation for the current language (if exists).
	 *
	 * @since  2.2.0
	 *
	 * @uses   pll_get_post()
	 *
	 * @param  mixed  $false false or string with bypass output.
	 * @param  string $tag   shortcode tag.
	 * @param  array  $attr  shortcode attributes.
	 * @return false|string  false or string with bypass output
	 */
	public function shortcode_template_translate( $false, $tag, $attr ) {

		if ( 'elementor-template' !== $tag ) {
			return $false;
		}

		if ( isset( $attr['skip'] ) ) {
			return $false;
		}

		// Translate post_id safely.
		if ( isset( $attr['id'] ) && '' !== $attr['id'] ) {
			$id = absint( $attr['id'] );
			if ( $id ) {
				$attr['id'] = pll_get_post( $id ) ?: $id; //phpcs:ignore WordPress.PHP.DisallowShortTernary
			}
		}
		// Skip next call.
		$attr['skip'] = 1;

		$output = '';
		foreach ( $attr as $key => $val ) {
			$output .= ' ' . esc_attr( sanitize_key( $key ) ) . '="' . esc_attr( $val ) . '"';
		}

		return do_shortcode( '[elementor-template' . $output . ']' );

	}

	/**
	 * Widget Template translate template_id
	 *
	 * @since  2.3.5
	 *
	 * @uses   pll_get_post()
	 *
	 * @param  \Elementor\Element_Base $element
	 * @return void
	 */
	public function widget_template_translate( $element ) {

		if ( 'template' !== $element->get_name() ) {
			return;
		}

		$template_id = pll_get_post( $element->get_settings( 'template_id' ) ) ?: $element->get_settings( 'template_id' ); //phpcs:ignore WordPress.PHP.DisallowShortTernary

		$element->set_settings( 'template_id', $template_id );

	}

	/**
	 * WordPress Widget "Elementor Library" translate 'template_id'
	 *
	 * @since  2.4.4
	 *
	 * @param  array     $instance
	 * @param  WP_Widget $widget
	 * @return array
	 */
	public function wp_widget_template_translate( $instance, $widget ) {

		if ( is_a( $widget, 'ElementorPro\Modules\Library\WP_Widgets\Elementor_Library' ) ) {
			$instance['template_id'] = pll_get_post( absint( $instance['template_id'] ) ) ?: $instance['template_id']; //phpcs:ignore WordPress.PHP.DisallowShortTernary
		}

		return $instance;

	}

	/**
	 * Change Elementor Kit template with their translation for the current language (if exists).
	 *
	 * @since  2.3.0
	 *
	 * @uses   pll_get_post()
	 * @uses   pll_get_post_language()
	 *
	 * @param  mixed $value Value of 'elementor_active_kit' option, the ID of current Elementor Kit.
	 * @return int The translation ID, or the original Elementor Kit ID
	 */
	public function elementor_kit_translation( $value ) {

		$translation = null;
		// Is API REST '/wp-json/elementor/v1/globals'.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_SERVER['HTTP_REFERER'] ) ) {
			// Referrer is Elementor Editor?
			$referer = esc_url_raw( $_SERVER['HTTP_REFERER'] );
			wp_parse_str( wp_parse_url( $referer, PHP_URL_QUERY ), $query );

			if ( isset( $query['action'], $query['post'] ) && 'elementor' === $query['action'] ) {
				$translation = pll_get_post( $value, pll_get_post_language( absint( $query['post'] ) ) );
			}
		} elseif ( cpel_is_elementor_editor() ) {
			$post_id     = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
			$translation = $post_id ? pll_get_post( $value, pll_get_post_language( $post_id ) ) : null;

		} elseif ( ! is_admin() ) {

			$translation = pll_get_post( $value );

		}

		return $translation ? $translation : $value;

	}

	/**
	 * Change Elementor template with their translation for the current language (if exists).
	 *
	 * @link   https://github.com/pojome/elementor/issues/4839
	 *
	 * @since  2.0.0
	 *
	 * @uses   pll_get_post()
	 *
	 * @param  int $post_id ID of the current post.
	 * @return string Based translation, the translation ID, or the original Post ID
	 */
	public function template_id_translation( $post_id ) {

		$post_id           = pll_get_post( $post_id ) ?: $post_id; //phpcs:ignore WordPress.PHP.DisallowShortTernary
		$this->template_id = $post_id; // Save for check sub_id.

		return $post_id;

	}

	/**
	 * Filter Elementor sub_conditions system
	 *
	 * If is translated template that is based on term or post
	 *   return the translation ID of term or post.
	 *
	 * @since  2.0.0
	 *
	 * @uses   pll_get_post()
	 * @uses   pll_get_term()
	 *
	 * @param  int   $sub_id ID of the object in subcondition.
	 * @param  array $parsed_condition condition parts.
	 * @return int original sub ID or translated ID
	 */
	public function condition_sub_id_translation( $sub_id, $parsed_condition ) {

		if ( $sub_id && cpel_is_translation( $this->template_id ) ) {

			if ( in_array( $parsed_condition['sub_name'], get_post_types(), true ) ) {

				$sub_id = pll_get_post( $sub_id ) ?: $sub_id; //phpcs:ignore WordPress.PHP.DisallowShortTernary

			} else {

				$sub_id = pll_get_term( $sub_id ) ?: $sub_id; //phpcs:ignore WordPress.PHP.DisallowShortTernary

			}
		}

		return $sub_id;

	}

	/**
	 * Update Elementor conditions
	 *
	 * On change post_translations terms on Elementor Library trigger conditions regenerate.
	 *
	 * @since  2.0.0
	 *
	 * @param  mixed $post_id
	 * @param  mixed $terms
	 * @param  mixed $tt_ids
	 * @param  mixed $taxonomy
	 * @return void
	 */
	public function update_conditions_on_term_change( $post_id, $terms, $tt_ids, $taxonomy ) {

		if ( 'post_translations' === $taxonomy && 'elementor_library' === get_post_type( $post_id ) ) {

			$theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
			$theme_builder->get_conditions_manager()->get_cache()->regenerate();

		}

	}

	/**
	 * Hide language column info pre
	 *
	 * Wrap language info for Global Widgets with a hidden div (open)
	 *
	 * @since  2.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	public function hide_language_column_pre( $column, $post_id ) {

		if ( false !== strpos( $column, 'language_' ) && 'widget' === get_post_meta( $post_id, '_elementor_template_type', true ) ) {
			echo '<span aria-hidden="true">—</span><div class="hidden" aria-hidden="true">';
		}

	}

	/**
	 * Hide language column info pos
	 *
	 * Wrap language info for Global Widgets with a hidden div (close)
	 *
	 * @since  2.0.0
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	public function hide_language_column_pos( $column, $post_id ) {

		if ( false !== strpos( $column, 'language_' ) && 'widget' === get_post_meta( $post_id, '_elementor_template_type', true ) ) {
			echo '</div>';
		}

	}

	/**
	 * Show default language instances in translations
	 *
	 * (Also wrap "None" with a hidden div)
	 *
	 * @since  2.0.4
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	public function instances_column_pre( $column, $post_id ) {

		if ( 'instances' === $column && 'widget' !== get_post_meta( $post_id, '_elementor_template_type', true ) && cpel_is_translation( $post_id ) ) {

			$default_post  = pll_get_post( $post_id, pll_default_language() );
			$theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
			$instances     = $theme_builder->get_conditions_manager()->get_document_instances( $default_post );

			if ( empty( $instances ) ) {
				$instances = array( 'none' => esc_html__( 'None', 'connect-polylang-elementor' ) );
			}

			echo '<span style="opacity:.4">' . esc_html( implode( ', ', $instances ) ) . '</span><div class="hidden" aria-hidden="true">';
		}

	}

	/**
	 * Show default language instances in translations (close)
	 *
	 * @since  2.0.4
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	public function instances_column_pos( $column, $post_id ) {

		if ( 'instances' === $column && 'widget' !== get_post_meta( $post_id, '_elementor_template_type', true ) && cpel_is_translation( $post_id ) ) {
			echo '</div>';
		}

	}

	/**
	 * Don't copy '_elementor_css' meta on Polylang add new translation
	 *
	 * Without this meta Elementor generates the css for the new post.
	 *
	 * @since 2.0.0
	 *
	 * @param  mixed  $null
	 * @param  int    $post_id
	 * @param  string $meta_key
	 * @return mixed null or false
	 */
	public function prevent_elementor_css_meta( $null, $post_id, $meta_key ) {

		global $pagenow;

		return '_elementor_css' === $meta_key && 'post-new.php' === $pagenow
			&& isset( $_GET['from_post'], $_GET['new_lang'] )
			&& ! empty( absint( wp_unslash( $_GET['from_post'] ) ) )
			&& ! empty( sanitize_key( wp_unslash( $_GET['new_lang'] ) ) ) ? false : $null;

	}

	/**
	 * Delete '_elementor_css' meta on Polylang bulk translation
	 *
	 * Without this meta Elementor generates the css for the new post.
	 *
	 * @since 2.4.3
	 *
	 * @param  int    $post_id
	 * @param  int    $tr_id
	 * @param  string $lang
	 * @return void
	 */
	public function bulk_delete_elementor_css_meta( $post_id, $tr_id, $lang ) {

		delete_post_meta( $tr_id, '_elementor_css' );

	}

	/**
	 * Whitelist Elementor Pro home_url()
	 *
	 * Polylang add home_url() to whitelist for Elementor Pro
	 *   "Search Form" widget and "Site Url" dynamic tag.
	 *
	 * @since  2.0.0
	 *
	 * @param  array $white_list
	 * @return array
	 */
	public function elementor_home_url_white_list( $white_list ) {

		$white_list[] = array( 'file' => 'site-url.php' );

		return $white_list;

	}

	/**
	 * Language subdir add trailing slash
	 *
	 * @since  2.0.0
	 *
	 * @param  string $url
	 * @param  string $path
	 * @return string
	 */
	public function home_url_language_dir_slash( $url, $path ) {

		return empty( $path ) && ! is_admin() && get_option( 'home' ) !== $url
			&& function_exists( 'PLL' ) && 1 === PLL()->options['force_lang'] ? trailingslashit( $url ) : $url;

	}

	/**
	 * Replace home_url with correct language search url
	 *
	 * Only for Elementor Search Form that uses home_url() in form action.
	 *
	 * @since 2.0.6
	 *
	 * @param  string $url
	 * @param  string $path
	 * @return string
	 */
	public function search_form_home_url_filter( $url, $path ) {

		if ( ! function_exists( 'PLL' ) || ! is_a( PLL()->curlang, 'PLL_Language', true ) ) {
			return $url;
		}

		return method_exists( PLL()->curlang, 'get_search_url' ) ?
			PLL()->curlang->get_search_url() :
			PLL()->curlang->search_url;

	}

	/**
	 * Add home_url() filter before render Search Form
	 *
	 * @since 2.0.6
	 *
	 * @param  Element_Base $element
	 * @return void
	 */
	public function add_search_form_home_url_filter( $element ) {

		if ( 'search' === $element->get_name() || 'search-form' === $element->get_name() ) {
			add_filter( 'home_url', array( $this, 'search_form_home_url_filter' ), 10, 2 );
		}

	}

	/**
	 * Remove home_url() filter after render Search Form
	 *
	 * @since 2.0.6
	 *
	 * @param  Element_Base $element
	 * @return void
	 */
	public function remove_search_form_home_url_filter( $element ) {

		if ( 'search' === $element->get_name() || 'search-form' === $element->get_name() ) {
			remove_filter( 'home_url', array( $this, 'search_form_home_url_filter' ) );
		}

	}

	/**
	 * Elementor editor script
	 *
	 * Add script with links to translations on Elementor editor panel.
	 *
	 * @since  2.0.0
	 *
	 * @return void
	 */
	public function elementor_editor_script() {

		global $typenow, $post;

		// If is post type translatable.
		if ( pll_is_translated_post_type( $typenow ) ) {

			$languages    = pll_languages_list( array( 'fields' => '' ) );
			$translations = pll_get_post_translations( $post->ID );
			$current      = pll_get_post_language( $post->ID, 'name' );
			$use_emojis   = apply_filters( 'cpel/filter/use_emojis', true );

			$items = array();
			foreach ( $languages as $language ) {
				if ( $language->name === $current ) {
					$translation_id = $translations[ $language->slug ];

					$items[] = array(
						'name'     => 'cpel-current',
						'icon'     => 'eicon-document-file',
						'title'    => sprintf(
							'%s — %s',
							get_the_title( $translation_id ),
							$use_emojis ? cpel_flag_emoji( $language->flag_code ) : $language->name
						),
						'callback' => 'function(){}',
					);

				} elseif ( isset( $translations[ $language->slug ] ) ) {

					$translation_id = $translations[ $language->slug ];
					$link           = $this->fix_url_domain( get_edit_post_link( $translation_id, 'edit' ), $translation_id );

					if ( get_post_meta( $translation_id, '_elementor_edit_mode', true ) ) {
						$link = add_query_arg( 'action', 'elementor', $link );
					}

					$items[] = array(
						'name'  => "cpel-{$language->slug}",
						'icon'  => 'eicon-document-file',
						'title' => sprintf(
							'%s — %s',
							get_the_title( $translation_id ),
							$use_emojis ? cpel_flag_emoji( $language->flag_code ) : $language->name
						),
						'type'  => 'link',
						'link'  => $link,
					);
				} else {

					$args = array(
						'post_type' => $typenow,
						'from_post' => $post->ID,
						'new_lang'  => $language->slug,
						'_wpnonce'  => wp_create_nonce( 'new-post-translation' ),
					);

					$link = add_query_arg( $args, admin_url( 'post-new.php' ) );

					$items[] = array(
						'name'  => "cpel-{$language->slug}",
						'icon'  => 'eicon-plus',
						'title' => $use_emojis
							? sprintf( __( 'Add a translation — %s', 'connect-polylang-elementor' ), cpel_flag_emoji( $language->flag_code ) )
							: sprintf( __( 'Add a translation in %s', 'connect-polylang-elementor' ), $language->name ),
						'type'  => 'link',
						'link'  => $link,
					);
				}
			}

			$group = array(
				'name'  => 'cpel',
				'title' => __( 'Languages', 'connect-polylang-elementor' ),
				'items' => $items,
			);

			$script = 'jQuery(window).on("elementor:init", () => {
				window.elementor.on("panel:init", () => {
					setTimeout(() => { window.elementor.modules.layouts.panel.pages.menu.Menu.groups.add(' . wp_json_encode( $group ) . '); });
				});
			});';

			// Add after Elementor editor script.
			wp_add_inline_script( 'elementor-editor', $script );

		}

	}

	/**
	 * Elementor editor script
	 *
	 * Add script with links to translations on Elementor editor panel.
	 *
	 * @since  2.3.5
	 *
	 * @return void
	 */
	public function elementor_editor_style() {

		global $typenow;

		// If is post type translatable.
		if ( ! pll_is_translated_post_type( $typenow ) ) {
			return;
		}

		// New language switcher styles for Elementor 3.25.0+.
		if ( cpel_elementor_min_version( '3.25.0' ) ) {

			$style = '
.elementor-control-cpel-languages a {
	display: flex;
	gap: 8px;
	align-items: center;
	padding: 8px 5px;
	border: none;
	color: inherit;
	line-height: 1.25em;
	font-weight: 400;
	text-decoration: none;
	transition: none;
}

.elementor-control-cpel-languages a.current, .elementor-control-cpel-languages a:hover {
	background-color: var(--e-a-bg-hover);
	color: inherit;
}

.elementor-control-cpel-languages i {
	font-size: 16px;
}

.elementor-control-cpel-languages .flag {
	margin-left: auto;
	font-size: 16px;
}';

			wp_add_inline_style( 'elementor-editor', $style );

			return;
		}

		// Old language switcher styles for Elementor < 3.25.0
		$style = '' .
			".elementor-panel .elementor-panel-menu-item.elementor-panel-menu-item-cpel-current {\n" .
			"	background: #eceeef;\n" .
			"	cursor: default;\n" .
			'}';

		wp_add_inline_style( 'elementor-editor', $style );

		$ui_theme = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );

		if ( 'light' !== $ui_theme ) {
			$ui_theme_media_queries = 'auto' === $ui_theme ? '(prefers-color-scheme: dark)' : 'all';

			$dark = '' .
				"@media $ui_theme_media_queries {\n" .
				"	.elementor-panel .elementor-panel-menu-item.elementor-panel-menu-item-cpel-current {\n" .
				"		background: #7d7e82;\n" .
				"	}\n" .
				'}';

			wp_add_inline_style( 'elementor-editor-dark-mode', $dark );
		}

	}

	/**
	 * Elementor Site Editor template changes
	 *
	 * At 2.0.0 named "elementor_theme_editor_title"
	 *
	 * @since  2.0.4
	 *
	 * @param  array $data
	 * @return array
	 */
	public function elementor_site_editor_template( $data ) {

		$post_id = $data['id'];

		// Add lang info to title.
		if ( apply_filters( 'cpel/filter/use_emojis', true ) ) {
			$data['title'] = sprintf( '%s — %s', $data['title'], cpel_flag_emoji( pll_get_post_language( $post_id, 'flag_code' ) ) );
		} else {
			$data['title'] = sprintf( '%s — %s', $data['title'], pll_get_post_language( $post_id, 'name' ) );
		}

		// Show default language instances in translations (and recalc isActive).
		if ( cpel_is_translation( $post_id ) ) {

			$language = pll_default_language();

			$default_post  = pll_get_post( $post_id, $language );
			$theme_builder = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
			$instances     = $theme_builder->get_conditions_manager()->get_document_instances( $default_post );

			if ( empty( $instances ) ) {
				$instances = array( 'no_instances' => esc_html__( 'No instances', 'connect-polylang-elementor' ) );
				$is_active = false;
			} else {
				$is_active = 'publish' === $data['status'];
			}

			$data['instances'] = array( 'cpel' => sprintf( esc_html__( '(from %s)', 'connect-polylang-elementor' ), strtoupper( $language ) ) ) + $instances;
			$data['isActive']  = $is_active;
		}

		return $data;

	}

	/**
	 * Fix url domain
	 *
	 * @param  mixed $url current url.
	 * @param  mixed $post_id current post ID.
	 * @return string fixed domain url
	 */
	private function fix_url_domain( $url, $post_id ) {

		$current_host = wp_parse_url( pll_current_language( 'home_url' ) ?: trailingslashit( '//' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ), PHP_URL_HOST ); //phpcs:ignore WordPress.PHP.DisallowShortTernary
		$post_host    = wp_parse_url( pll_get_post_language( $post_id, 'home_url' ), PHP_URL_HOST );

		if ( $current_host !== $post_host ) {
			$url = str_replace( $current_host, $post_host, $url );
		}

		return $url;

	}

	/**
	 * Fix domain for Elementor edit links in posts table
	 *
	 * @param  array   $actions
	 * @param  WP_Post $post
	 * @return array
	 */
	public function fix_edit_link( $actions, $post ) {

		if ( ! empty( $actions['edit_with_elementor'] ) ) {
			// $actions['edit']                = $this->fix_url_domain( $actions['edit'], $post->ID );
			$actions['edit_with_elementor'] = $this->fix_url_domain( $actions['edit_with_elementor'], $post->ID );
		}

		return $actions;

	}

	/**
	 * Fix domain for Elementor edit links in Theme Builder
	 *
	 * @param  string                       $url
	 * @param  Elementor\Core\Base\Document $document
	 * @return string
	 */
	public function fix_elementor_edit_link( $url, $document ) {

		return $this->fix_url_domain( $url, $document->get_main_id() );

	}

	/**
	 * Register language switcher controls in Elementor's document settings panel.
	 * This function adds a "Languages" section where users can manage translations for the current post.
	 *
	 * @param \Elementor\Base\Document $document The Elementor document object.
	 * @since  2.5.0
	 */
	public function register_language_switcher_controls( $document ) {

		global $typenow, $post;

		// Exit if is not translatable.
		if ( ! pll_is_translated_post_type( $typenow ) ) {
			return;
		}

		// Get the current post ID being edited in Elementor.
		$post_id = $post->ID;

		// Retrieve available languages from Polylang.
		$languages    = pll_languages_list( array( 'fields' => '' ) );
		$translations = pll_get_post_translations( $post_id );
		$raw_html     = '';

		// Start adding a new section in Elementor settings panel.
		$document->start_controls_section(
			'cpel_language_section',
			array(
				'label' => esc_html__( 'Languages', 'connect-polylang-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		// Loop through each available language.
		foreach ( $languages as $language ) {
			$lang_parts = array(
				'class' => 'cpel-language',
				'href'  => '',
				'icon'  => '',
				'text'  => '',
				'flag'  => str_replace( '<img ', '<img class="flag" ', $language->flag ),
			);

			// Check if a translation exists for the current language.
			if ( isset( $translations[ $language->slug ] ) ) {
				// Get the post ID of the translated post.
				$translation_id = $translations[ $language->slug ];

				$lang_parts['icon'] = '<i class="eicon-document-file"></i>';
				$lang_parts['text'] = '<span class="text">' . esc_html( get_the_title( $translation_id ) ) . '</span>';

				if ( $translation_id === $post_id ) {
					$lang_parts['class'] .= ' current';
				} else {
					// Get edit link for the translated post.
					$edit_link = get_edit_post_link( $translation_id, 'edit' );

					// Modify edit link if it's built with Elementor.
					if ( get_post_meta( $translation_id, '_elementor_edit_mode', true ) ) {
						$edit_link = add_query_arg( 'action', 'elementor', $edit_link );
					}

					$lang_parts['href'] = $edit_link;
				}
			} else {
				// Generate the create translation link.
				$args        = array(
					'post_type' => get_post_type( $post_id ),
					'from_post' => $post_id,
					'new_lang'  => $language->slug,
					'_wpnonce'  => wp_create_nonce( 'new-post-translation' ),
				);
				$create_link = add_query_arg( $args, admin_url( 'post-new.php' ) );

				$lang_parts['class'] .= ' add-new';
				$lang_parts['icon']   = '<i class="eicon-plus"></i>';
				$lang_parts['href']   = $create_link;
				$lang_parts['text']   = '<span class="text">' . sprintf( esc_html__( 'Add a translation in %s', 'connect-polylang-elementor' ), strtolower( esc_html( $language->name ) ) ) . '</span>';
			}

			$raw_html .= sprintf(
				'<li><a class="%s" %s>%s %s %s</a></li>',
				$lang_parts['class'],
				$lang_parts['href'] ? sprintf( 'href="%s" target="_blank"', esc_url( $lang_parts['href'] ) ) : '',
				$lang_parts['icon'],
				$lang_parts['text'],
				$lang_parts['flag']
			);
		}

		$document->add_control(
			'cpel_languages',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => '<ul>' . $raw_html . '</ul>',
				'content_classes' => 'elementor-control-cpel-languages',
			)
		);

		// End the controls section.
		$document->end_controls_section();
	}
}
