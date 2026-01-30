<?php
/**
 * Connect Polylang Elementor Helpers Class
 *
 * @package Connect_Polylang_Elementor
 * @since 2.5.6
 */

namespace ConnectPolylangElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class CPEL_Helpers
 *
 * Helper functions for Connect Polylang Elementor plugin.
 *
 * @since 2.5.6
 */
class CPEL_Helpers {

	/**
	 * Extract flag code from flag URL.
	 *
	 * @since 2.5.6
	 *
	 * @param string $flag_url The URL of the flag image.
	 * @return string|false The flag code if found, false otherwise.
	 */
	public static function get_flag_code( $flag_url ) {
		$flag_code = preg_match( '/polylang\/flags\/([a-z]+)\.(png|svg|jpg|jpeg)$/i', $flag_url, $matches ) ? $matches[1] : false;
		return $flag_code;
	}

	/**
	 * Get country flag HTML for a specific language.
	 *
	 * @since 2.5.6
	 *
	 * @param string $flag_url The URL of the flag image.
	 * @param string $lang     The language code.
	 * @return string The HTML markup for the flag.
	 */
	public static function get_country_flag( $flag_url, $lang ) {
		$country_code = self::get_flag_code( $flag_url );
		$flag         = [];

		if ( $country_code && class_exists( 'PLL_Language' ) && method_exists( 'PLL_Language', 'get_flag_html' ) ) {
			$plugin_url   = CPEL_Helpers::get_plugin_url();
			$flag['path'] = CPEL_DIR . 'assets/flags/' . esc_html( $country_code ) . '.svg';
			$flag['url']  = esc_url( $plugin_url . 'assets/flags/' . esc_html( $country_code ) . '.svg' );
			$flag['src']  = $flag['url'];
			$flag_html    = \PLL_Language::get_flag_html( $flag, '', $lang );
			return $flag_html;
		}

		$flag['src'] = $flag_url;
		$flag_html   = \PLL_Language::get_flag_html( $flag, '', $lang );
		return $flag_html;
	}

	/**
	 * Convert Polylang PNG flag URL to plugin's SVG flag URL.
	 * Consolidated method to avoid duplication across frontend and admin.
	 *
	 * @since 2.5.6
	 *
	 * @param string $polylang_flag_url Polylang flag URL.
	 * @return string Plugin's SVG flag URL or original if not found.
	 */
	public static function get_plugin_flag_url( $polylang_flag_url ) {
		if ( empty( $polylang_flag_url ) ) {
			return '';
		}
		
		// Get flag code from Polylang URL
		$flag_code = self::get_flag_code( $polylang_flag_url );
		
		if ( empty( $flag_code ) ) {
			return $polylang_flag_url; // Fallback to original
		}
		
		// Build path to plugin's SVG flag
		$plugin_url = CPEL_Helpers::get_plugin_url();
		return $plugin_url . 'assets/flags/' . $flag_code . '.svg';
	}

	/**
	 * Check if required plugin dependencies are active.
	 * Consolidated check to avoid repetition.
	 *
	 * @since 2.5.6
	 *
	 * @return bool True if Polylang and Elementor are active.
	 */
	public static function is_dependencies_active() {
		global $polylang;
		
		// Check if Polylang is loaded
		if ( ! isset( $polylang ) ) {
			return false;
		}
		
		// Check if Elementor is active
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
			return false;
		}
		
		return true;
	}

	/**
	 * Get language name based on display mode.
	 * Consolidated method to format language names consistently.
	 *
	 * @since 2.5.6
	 *
	 * @param array  $lang Language data from Polylang.
	 * @param string $mode Display mode: 'full', 'short', or 'none'.
	 * @return string Formatted language name.
	 */
	public static function get_language_name( $lang, $mode ) {
		switch ( $mode ) {
			case 'full':
				return $lang['name'];
			case 'short':
				return strtoupper( $lang['slug'] );
			case 'none':
			default:
				return '';
		}
	}

	/**
	 * Get AutoPoly plugin file path.
	 *
	 * @since 2.5.6
	 * @return string Plugin file path.
	 */
	public static function get_autopoly_plugin_file() {
		return 'automatic-translations-for-polylang/automatic-translation-for-polylang.php';
	}

    /**
     * Get plugin URL (cached)
     *
     * @since 2.5.6
     * @return string Plugin URL
     */
     public static function get_plugin_url() {
    static $url = null;
    if ( $url === null ) {
        $url = plugins_url( '/', CPEL_FILE );
    }
    return $url;
     }

	/**
	 * Check AutoPoly plugin status.
	 *
	 * @since 2.5.6
	 * @return array Status with 'installed' and 'active' booleans.
	 */
	public static function get_autopoly_status() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$plugin_file = self::get_autopoly_plugin_file();
		$all_plugins = get_plugins();
		
		return array(
			'installed' => isset( $all_plugins[ $plugin_file ] ),
			'active'    => is_plugin_active( $plugin_file ),
		);
	}

	/**
	 * Get button text based on AutoPoly status.
	 *
	 * @since 2.5.6
	 * @param array $status Status array from get_autopoly_status().
	 * @return string Button text.
	 */
	public static function get_autopoly_button_text( $status ) {
		if ( $status['active'] ) {
			return __( 'Go to Settings', 'connect-polylang-elementor' );
		}
		if ( $status['installed'] ) {
			return __( 'Activate', 'connect-polylang-elementor' );
		}
		return __( 'Install AutoPoly', 'connect-polylang-elementor' );
	}

	/**
	 * Get AutoPoly action URL (dashboard or install).
	 *
	 * @since 2.5.6
	 * @param bool $is_active Whether plugin is active.
	 * @return string URL.
	 */
	public static function get_autopoly_action_url( $is_active ) {
		return $is_active 
			? admin_url( 'admin.php?page=polylang-atfp-dashboard' )
			: 'plugin-install.php?s=autopoly&tab=search&type=term';
	}

	/**
	 * Render AutoPoly promotional box.
	 * Complete HTML rendering - no duplication needed.
	 *
	 * @since 2.5.6
	 * @param string $context Context identifier for UTM tracking.
	 */
	public static function render_autopoly_promo_box( $context = 'default' ) {
		$status = self::get_autopoly_status();
		$is_active = $status['active'];
		$button_text = self::get_autopoly_button_text( $status );
		$docs_url = 'https://docs.coolplugins.net/plugin/ai-translation-for-polylang/?utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=get_started';
		$action_url = self::get_autopoly_action_url( $is_active );
		$plugin_url = CPEL_Helpers::get_plugin_url();
		?>
		<div class="cpel-promo-box">
			<div class="cpel-promo-main">
				<div class="cpel-promo-image-section">
					<img
						class="cpel-promo-image"
						src="<?php echo esc_url( $plugin_url . 'admin/dashboard/assets/images/autopoly-ai-translation-for-polylang-pro.png' ); ?>"
						alt="<?php echo esc_attr__( 'AutoPoly logo', 'connect-polylang-elementor' ); ?>"
					>
				</div>

				<div class="cpel-promo-text-section">
					<div class="cpel-promo-header-row">
						<strong class="cpel-promo-title">
							<?php echo esc_html__( 'AutoPoly - AI Translation For Polylang', 'connect-polylang-elementor' ); ?>
						</strong>
					</div>

					<p class="cpel-promo-subtitle">
						<?php echo esc_html__( 'Automatically translate pages and posts built with Elementor or Gutenberg using AI in one click. Save time and effort.', 'connect-polylang-elementor' ); ?>
					</p>
				</div>
			</div>

			<div class="cpel-promo-actions">
				<?php if ( ! $is_active ) : ?>
					<button
						class="button button-primary cpel-promo-button cpel-autopoly-action-btn"
						type="button"
						data-context="<?php echo esc_attr( $context ); ?>"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'cpel_install_autopoly' ) ); ?>"
					>
						<?php echo esc_html( $button_text ); ?>
					</button>
				<?php else : ?>
					<a
						href="<?php echo esc_url( $action_url ); ?>"
						class="button button-primary cpel-promo-button"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php echo esc_html( $button_text ); ?>
					</a>
				<?php endif; ?>

				<a
					href="<?php echo esc_url( $docs_url ); ?>"
					class="button button-secondary cpel-promo-button-secondary"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?php esc_html_e( 'View Docs', 'connect-polylang-elementor' ); ?>
				</a>
			</div>
		</div>
		<?php
	}
}