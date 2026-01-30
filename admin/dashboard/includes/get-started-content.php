<?php
namespace ConnectPolylangElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cpel-get-started-content">
	<?php
	$cpel_show_assign_language = false;

	/**
	 * Check if Polylang is active and the function to get the post language exists.
	 */
	if ( function_exists( 'PLL' ) && function_exists( 'pll_get_post_language' ) ) {
		$cpel_query = new \WP_Query(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		if ( $cpel_query->posts ) {
			foreach ( $cpel_query->posts as $cpel_post_id ) {
				if ( ! pll_get_post_language( $cpel_post_id ) ) {
					$cpel_show_assign_language = true;
					break;
				}
			}
		}
		wp_reset_postdata();
	}
	?>

	<div class="cpel-content-wrapper">
		<div class="cpel-content-left">
			<header class="cpel-gs-header">
				<h2 class="cpel-gs-title">
					<?php esc_html_e( 'Quick Start Guide', 'connect-polylang-elementor' ); ?>
				</h2>
				<p class="cpel-gs-subtitle">
					<?php
					printf(
						/* translators: %s: plugin name. */
						esc_html__( 'Thanks for using %s.', 'connect-polylang-elementor' ),
						'<strong>Connect Polylang for Elementor</strong>'
					);
					?>
				</p>
				<p class="cpel-gs-subtitle">
					<?php esc_html_e( 'Build a multilingual Elementor website, manage templates, and add an easy-to-use language switcher with Polylang.', 'connect-polylang-elementor' ); ?>
				</p>
			</header>

			<section class="cpel-gs-card cpel-gs-card--step">
				<div class="cpel-gs-card-header">
					<div class="cpel-gs-step-badge">1</div>
					<div class="cpel-gs-card-header-content">
						<h3 class="cpel-gs-card-title">
							<?php esc_html_e( 'Translate Elementor Templates', 'connect-polylang-elementor' ); ?>
						</h3>
					</div>
				</div>

				<?php if ( $cpel_show_assign_language ) : ?>
					<div class="cpel-gs-notice">
						<div class="cpel-gs-notice-main">
							<span class="cpel-gs-notice-icon dashicons dashicons-info-outline" aria-hidden="true"></span>
							<p class="cpel-gs-notice-text">
								<?php esc_html_e( 'Notice: Assign default language to pre-published Elementor Templates.', 'connect-polylang-elementor' ); ?>
							</p>
						</div>
						<div class="cpel-gs-notice-actions">
							<form class="cpel-gs-notice-apply-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="cpel_set_default_language_elementor_library" />
								<?php wp_nonce_field( 'cpel_set_default_language_elementor_library', '_wpnonce_cpel_set_default_language_elementor_library' ); ?>
								<button type="submit" class="button button-primary cpel-gs-notice-apply">
									<?php esc_html_e( 'Apply Now', 'connect-polylang-elementor' ); ?>
								</button>
							</form>
						</div>
					</div>
				<?php endif; ?>

				<ul class="cpel-gs-list">
					<li>
						<?php
						/* translators: admin navigation path. */
						printf(
							'%1$s <strong>%2$s</strong>',
							esc_html__( 'From your WordPress Dashboard, Navigate to', 'connect-polylang-elementor' ),
							esc_html__( 'Templates > Saved Templates.', 'connect-polylang-elementor' )
						);
						?>
					</li>
					<li><?php esc_html_e( 'Create a new Elementor template or edit an existing one.', 'connect-polylang-elementor' ); ?></li>
					<li><?php esc_html_e( 'Use Polylang\'s language options to add translations for the template.', 'connect-polylang-elementor' ); ?></li>
					<li><?php esc_html_e( 'Translate the content and save the template.', 'connect-polylang-elementor' ); ?></li>
					<li><?php esc_html_e( 'Each translated template will be linked automatically, ensuring the correct language version is displayed on your website.', 'connect-polylang-elementor' ); ?></li>
				</ul>

				<div class="cpel-gs-actions">
					<a
						href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>"
						class="button button-primary"
					>
						<?php esc_html_e( 'Go to Template Settings', 'connect-polylang-elementor' ); ?>
					</a>
					<a
						href="https://docs.coolplugins.net/doc/translate-elementor-templates/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=get_started"
						class="button button-secondary"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'View Docs', 'connect-polylang-elementor' ); ?>
					</a>
				</div>
			</section>

			<section class="cpel-gs-card cpel-gs-card--step cpel-gs-card--muted">
				<div class="cpel-gs-card-header">
					<div class="cpel-gs-step-badge">2</div>
					<div class="cpel-gs-card-header-content">
						<h3 class="cpel-gs-card-title">
							<?php esc_html_e( 'Add Language Switcher Widget', 'connect-polylang-elementor' ); ?>
						</h3>
					</div>
				</div>
				<ul class="cpel-gs-list">
					<li><?php esc_html_e( 'Edit your header template with Elementor Editor.', 'connect-polylang-elementor' ); ?></li>
					<li>
						<?php
						printf(
							/* translators: search keyword. */
							esc_html__( 'Search for %s in the widgets panel.', 'connect-polylang-elementor' ),
							'<strong>"' . esc_html__( 'Language Switcher', 'connect-polylang-elementor' ) . '"</strong>'
						);
						?>
					</li>
					<li><?php esc_html_e( 'Customize layout (dropdown, flags, etc.) and styling in the widget settings.', 'connect-polylang-elementor' ); ?></li>
				</ul>
				<div class="cpel-gs-actions">
					<a
						href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=theme' ) ); ?>"
						class="button button-primary"
					>
						<?php esc_html_e( 'Go to Widget Settings', 'connect-polylang-elementor' ); ?>
					</a>
					<a
						href="https://docs.coolplugins.net/doc/add-language-switcher-elementor/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=get_started"
						class="button button-secondary"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php esc_html_e( 'View Docs', 'connect-polylang-elementor' ); ?>
					</a>
				</div>
			</section>
		</div>

		<div class="cpel-content-right">
			<section class="cpel-gs-card cpel-gs-card--video">
				<header class="cpel-gs-video-header">
					<h3 class="cpel-gs-video-title">
						<?php esc_html_e( 'Video Tutorial', 'connect-polylang-elementor' ); ?>
					</h3>
				</header>

				<div class="cpel-gs-video-body">
					<div class="cpel-video-container">
						<iframe
							width="100%"
							height="260"
							src="https://www.youtube.com/embed/7DUh-bggJBs"
							title="<?php echo esc_attr__( 'Connect Polylang for Elementor Tutorial', 'connect-polylang-elementor' ); ?>"
							frameborder="0"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen>
						</iframe>
					</div>

					<a
						href="https://www.youtube.com/watch?v=7DUh-bggJBs"
						class="button button-secondary cpel-gs-video-cta"
						target="_blank"
						rel="noopener noreferrer"
					>
						<span class="cpel-gs-video-cta-icon" aria-hidden="true"></span>
						<span class="cpel-gs-video-cta-label">
							<?php esc_html_e( 'Watch on YouTube', 'connect-polylang-elementor' ); ?>
						</span>
					</a>
				</div>
			</section>

			<?php
			// AutoPoly promotional box stays in the sidebar to match the new layout.
			if ( class_exists( 'ConnectPolylangElementor\CPEL_Helpers' ) ) {
				\ConnectPolylangElementor\CPEL_Helpers::render_autopoly_promo_box( 'get_started' );
			}
			?>
		</div>
	</div>
</div>
