<?php
/**
 * Floating Switcher Frontend Renderer
 *
 * Handles the complete rendering and display of the floating language switcher
 * on the frontend. Manages asset loading, HTML generation, styling, and
 * integration with Polylang for language data.
 *
 * @package    Connect_Polylang_Elementor
 * @subpackage Connect_Polylang_Elementor/includes
 * @since      2.5.5
 */
namespace ConnectPolylangElementor;
if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

/**
 * CPEL Floating Switcher Frontend Class
 *
 * Renders the floating language switcher on the frontend with full
 * support for responsive design, accessibility, and custom styling.
 *
 * @since 2.5.6
 */
class CPEL_Floating_Lang_Switcher_Frontend
{
    
    /**
     * Switcher configuration array
     *
     * @since 2.5.6
     * @var   array|null
     */
    private $config;
    
    /**
     * Current viewport type (mobile or desktop)
     *
     * @since 2.5.6
     * @var   string
     */
    private $viewport;
    
    /**
     * Constructor
     *
     * Detects viewport type and registers WordPress hooks for
     * asset enqueuing and switcher rendering.
     *
     * @since 2.5.6
     */
    public function __construct()
    {
        // Detect viewport type (mobile or desktop)
        $this->viewport = wp_is_mobile() ? 'mobile' : 'desktop';
        
        // Enqueue frontend CSS and JavaScript
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_assets' ]);
        
        // Render switcher in footer (priority 99 for late rendering)
        add_action('wp_footer', [ $this, 'render_floater' ], 99);
    }
    
    /**
     * Get Switcher Configuration
     *
     * Retrieves the switcher configuration from database with lazy loading.
     * Caches the result in the instance property to avoid multiple database queries.
     *
     * @since  2.5.5
     * @return array Switcher configuration array
     */
    private function get_config()
    {
        if ($this->config === null ) {
            $this->config = get_option('cpel_floating_switcher_config', []);
        }
        return $this->config;
    }
    
    /**
     * Check if Floater is Enabled
     *
     * Determines whether the floating switcher should be displayed
     * based on the configuration settings.
     *
     * @since  2.5.5
     * @return bool True if enabled, false otherwise
     */
    private function is_enabled()
    {
        $config = $this->get_config();
        return ! empty($config['enabled']);
    }
    
    /**
     * Enqueue Frontend Assets
     *
     * Loads CSS and JavaScript files for the floating switcher.
     * Also adds custom CSS inline if enabled in settings.
     *
     * @since 2.5.6
     */
    public function enqueue_assets()
    {
        // Only enqueue if switcher is enabled
        if (! $this->is_enabled() ) {
            return;
        }
    
        $plugin_url = CPEL_Helpers::get_plugin_url();
        $version    = defined('CPEL_PLUGIN_VERSION') ? CPEL_PLUGIN_VERSION : '2.5.5';
    
        // Enqueue frontend CSS
        wp_enqueue_style(
            'cpel-floating-switcher-frontend',
            $plugin_url . 'assets/css/cpel-floating-switcher-frontend.min.css',
            [],
            $version
        );
    
        // Add custom CSS inline if enabled in settings
        $config = $this->get_config();
        if (! empty($config['enableCustomCss']) && ! empty($config['customCss']) ) {
            // Sanitize CSS content for security
            $custom_css = wp_strip_all_tags($config['customCss']);
            $custom_css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $custom_css);
        
            wp_add_inline_style('cpel-floating-switcher-frontend', $custom_css);
        }
    
        // Enqueue frontend JavaScript for interactions
        wp_enqueue_script(
            'cpel-floating-switcher-js',
            $plugin_url . 'assets/js/cpel-floating-switcher-frontend.min.js',
            [],
            $version,
            true // Load in footer
        );
    }
    
    /**
     * Render Floating Switcher
     *
     * Main rendering method that outputs the floating language switcher HTML
     * in the footer. Checks for Polylang availability and configuration,
     * then generates the complete switcher markup with styles.
     *
     * @since 2.5.6
     */
    public function render_floater()
    {
        // Only render if enabled
        if (! $this->is_enabled() ) {
            return;
        }
        
        // Check if Polylang functions are available
        if (! function_exists('pll_the_languages') || ! function_exists('pll_current_language') ) {
            return;
        }
        
        $config = $this->get_config();
        // Get layout config for current viewport (mobile/desktop)
        $layout = $config['layoutCustomizer'][ $this->viewport ] ?? $config['layoutCustomizer']['desktop'];
        
        // Get available languages from Polylang
        $languages = $this->get_polylang_languages($config, $layout);
        
        // Don't render if no languages available
        if (empty($languages) ) {
            return;
        }
        
        // Determine switcher type (dropdown or side-by-side)
        $is_dropdown = $config['type'] === 'dropdown';
        
        // Build inline CSS styles for the switcher
        $styles = $this->build_switcher_styles($config, $layout);
        
        // Determine position class based on layout
        $position_class = strpos($layout['position'], 'top') !== false 
            ? 'cpel-switcher-position-top' 
            : 'cpel-switcher-position-bottom';
        
        // Render the complete switcher HTML
        $this->render_switcher_html($languages, $config, $layout, $styles, $position_class, $is_dropdown);
        
        // Output custom CSS in footer if enabled
        if (! empty($config['enableCustomCss']) && ! empty($config['customCss']) ) {
            // Sanitize CSS: remove script tags and dangerous content for security
            $custom_css = wp_strip_all_tags($config['customCss']);
            $custom_css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $custom_css);
    
            // Output custom CSS safely in a style tag
            echo '<style id="cpel-floating-switcher-custom-css">';
            echo wp_kses(
                $custom_css, array(
                'style' => array(),
                ) 
            );
            echo '</style>';
        }
    }
    
    /**
     * Get Polylang Languages
     *
     * Retrieves and processes language data from Polylang.
     * Formats each language with code, name, URL, flag, and current status.
     * Places the current language first in the array.
     *
     * @since  2.5.5
     * @param  array $config Switcher configuration
     * @param  array $layout Layout configuration for current viewport
     * @return array Array of formatted language objects
     */
    private function get_polylang_languages( $config, $layout )
    {
        $current_lang = pll_current_language();
        $languages    = [];
        
        // Get raw language data from Polylang
        $raw_languages = pll_the_languages(
            [
            'raw'           => 1, // Return raw data
            'hide_if_empty' => 0, // Show all languages
            ]
        );
        
        if (empty($raw_languages) ) {
            return [];
        }
        
        // Process and format each language
        foreach ( $raw_languages as $lang ) {
            $lang_data = [
                'code'       => $lang['slug'],
                'name'       => $this->get_language_name($lang, $layout['languageNames']),
                'url'        => $lang['url'],
                'flag'       => $this->get_plugin_flag_url($lang['flag'] ?? ''),
                'is_current' => $lang['slug'] === $current_lang,
            ];
            
            // Put current language first in the array for proper display order
            if ($lang_data['is_current'] ) {
                array_unshift($languages, $lang_data);
            } else {
                $languages[] = $lang_data;
            }
        }
        
        return $languages;
    }
    
    /**
     * Get Language Name
     *
     * Returns the language name formatted according to the display mode setting.
     *
     * @since  2.5.5
     * @param  array  $lang Language data from Polylang
     * @param  string $mode Display mode ('full', 'short', or 'none')
     * @return string Formatted language name
     */
    private function get_language_name( $lang, $mode )
    {
        switch ( $mode ) {
        case 'full':
            return $lang['name'];
        case 'short':
            return strtoupper($lang['slug']);
        case 'none':
        default:
            return '';
        }
    }

    /**
     * Get Plugin Flag URL
     *
     * Extracts country code from Polylang flag URL and returns plugin's SVG flag URL.
     * Falls back to original Polylang flag if conversion fails.
     *
     * @since  2.5.5
     * @param  string $polylang_flag_url URL to Polylang's flag image
     * @return string URL to flag image
     */
    private function get_plugin_flag_url( $polylang_flag_url )
    {
        if (empty($polylang_flag_url) ) {
            return '';
        }
        
        // Extract country code from flag URL (e.g., "us.png" -> "us")
        $flag_basename = basename($polylang_flag_url);
        $country_code = pathinfo($flag_basename, PATHINFO_FILENAME);
        
        if ($country_code ) {
            // Return plugin's SVG flag URL
            $plugin_url = CPEL_Helpers::get_plugin_url();
            return $plugin_url . 'assets/flags/' . esc_attr($country_code) . '.svg';
        }
        
        // Fallback to original Polylang flag if conversion fails
        return $polylang_flag_url;
    }
    
    /**
     * Build Switcher Styles
     *
     * Generates inline CSS custom properties for the switcher based on
     * configuration settings.
     *
     * @since  2.5.5
     * @param  array $config Switcher configuration
     * @param  array $layout Layout configuration for current viewport
     * @return string CSS custom properties as inline style string
     */
    private function build_switcher_styles( $config, $layout )
    {
        $position = $layout['position'];
        $is_large = $config['size'] === 'large';
        
        // Parse position string into vertical and horizontal components
        $position_parts = explode('-', $position);
        $vertical       = $position_parts[0] ?? 'bottom';
        $horizontal     = $position_parts[1] ?? 'right';
        
        // Build position CSS variables
        $position_vars = [
            '--bottom' => $vertical === 'bottom' ? '0px' : 'auto',
            '--top'    => $vertical === 'top' ? '0px' : 'auto',
            '--right'  => $horizontal === 'right' ? '10%' : 'auto',
            '--left'   => $horizontal === 'left' ? '10%' : 'auto',
        ];
        
        // Merge all CSS custom properties
        $vars = array_merge(
            [
            '--bg'                  => $config['bgColor'],
            '--bg-hover'            => $config['bgHoverColor'],
            '--text'                => $config['textColor'],
            '--text-hover'          => $config['textHoverColor'],
            '--border-color'        => $config['borderColor'],
            '--border-width'        => $config['borderWidth'] . 'px',
            '--border-radius'       => $this->build_radius($config['borderRadius']),
            '--flag-radius'         => "{$config['flagRadius']}px",
            '--flag-size'           => $is_large ? '20px' : '18px',
            '--aspect-ratio'        => $config['flagShape'] === 'rect' ? '4/3' : '1',
            '--font-size'           => $is_large ? '16px' : '14px',
            '--switcher-width'      => $layout['width'] === 'custom' ? "{$layout['customWidth']}px" : 'auto',
            '--switcher-padding'    => $layout['padding'] === 'custom' ? "{$layout['customPadding']}px" : '0',
            '--transition-duration' => $config['enableTransitions'] ? '0.2s' : '0s',
            ], $position_vars 
        );

        // Build inline style string from CSS variables
        $style_pairs = [];
        foreach ( $vars as $key => $value ) {
            if (preg_match('/^--[a-z0-9-]+$/i', $key) ) {
                $style_pairs[] = $key . ':' . esc_attr($value);
            }
        }
        
        return implode(';', $style_pairs);
    }
    
    /**
     * Build Border Radius String
     *
     * Converts border radius array (4 corners) into CSS border-radius value.
     *
     * @since  2.5.5
     * @param  array $radius_array Array of 4 radius values [TL, TR, BR, BL]
     * @return string CSS border-radius value
     */
    private function build_radius( $radius_array )
    {
        if (! is_array($radius_array) ) {
            return '8px 8px 0 0';
        }
        return implode(
            ' ', array_map(
                function ( $r ) {
                    return intval($r) . 'px';
                }, $radius_array 
            ) 
        );
    }
    
    /**
     * Render Switcher HTML
     *
     * Outputs the complete HTML markup for the language switcher.
     *
     * @since 2.5.6
     */
    private function render_switcher_html( $languages, $config, $layout, $styles, $position_class, $is_dropdown )
    {
        $current = $languages[0] ?? null;
        $others  = array_slice($languages, 1);
        
        if (! $current ) {
            return;
        }
        
        $flag_position   = $layout['flagIconPosition'];
        $all_lang_names  = array_map(
            function ( $lang ) {
                return $lang['name']; 
            }, $languages
        );
        $lang_names_json = esc_attr(json_encode($all_lang_names));
        ?>
       <nav class="cpel-language-switcher cpel-floating-switcher <?php echo $is_dropdown ? 'cpel-ls-dropdown ' : 'cpel-ls-inline '; echo esc_attr($position_class); ?>"
     style="<?php echo esc_attr($styles); ?>"
     role="navigation"
     aria-label="<?php esc_attr_e('Website language selector', 'connect-polylang-elementor'); ?>"
     data-lang-names="<?php echo esc_attr($lang_names_json); ?>"
     data-no-translation>
            
            <?php if ($is_dropdown ) : ?>
                <div class="cpel-language-switcher-inner">
                    <?php 
                    $this->render_language_item($current, true, $flag_position, $config); 
                    ?>
                    
                    <?php if (! empty($others) ) : ?>
                        <div class="cpel-switcher-dropdown-list"
                             role="group"
                             aria-label="<?php esc_attr_e('Available languages', 'connect-polylang-elementor'); ?>"
                             hidden
                             inert>
                            <?php foreach ( $others as $lang ) : ?>
                                <?php $this->render_language_item($lang, false, $flag_position, $config); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="cpel-language-switcher-inner">
                    <?php 
                    foreach ( $languages as $lang ) :
                        $this->render_language_item($lang, false, $flag_position, $config, $lang['is_current']);
                    endforeach;
                    ?>
                </div>
            <?php endif; ?>
        </nav>
        <?php
    }
    
    /**
     * Render Language Item
     *
     * Outputs HTML for a single language link or button.
     *
     * @since 2.5.6
     */
    private function render_language_item( $lang, $as_control, $flag_position, $config, $is_current = false )
    {
        $classes = [ 'cpel-language-item' ];
        
        if ($as_control ) {
            $classes[] = 'cpel-language-item__current';
        }
        
        if ($is_current ) {
            $classes[] = 'cpel-language-item__default';
        }
        
        $tag       = $as_control ? 'div' : 'a';
        $flag_html = $this->get_flag_html($lang, $config);
        
        ?>
        <<?php echo esc_attr($tag); ?> 
            class="<?php echo esc_attr(implode(' ', $classes)); ?>"
            <?php if ($tag === 'a' ) : ?>
                href="<?php echo esc_url($lang['url']); ?>"
                title="<?php echo esc_attr($lang['name']); ?>"
            <?php else : ?>
                role="button"
                tabindex="0"
                aria-expanded="false"
                aria-label="<?php esc_attr_e('Change language', 'connect-polylang-elementor'); ?>"
            <?php endif; ?>
            data-no-translation>
            
            <?php if ($flag_position === 'before' && $flag_html ) : ?>
                <?php echo $flag_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
            
            <?php if (! empty($lang['name']) ) : ?>
                <span class="cpel-language-item-name"><?php echo esc_html($lang['name']); ?></span>
            <?php endif; ?>
            
            <?php if ($flag_position === 'after' && $flag_html ) : ?>
                <?php echo $flag_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
            <?php if ($as_control ) : ?>
            <svg class="cpel-dropdown-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                <path d="M1 6.5L6 1.5L11 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php endif; ?>
        </<?php echo esc_attr($tag); ?>>
        <?php
    }
    
    /**
     * Get Flag HTML
     *
     * Generates HTML markup for a language flag image.
     *
     * @since 2.5.6
     */
    private function get_flag_html( $lang, $config )
    {
        if (empty($lang['flag']) ) {
            return '';
        }
        
        $shape_class = '';
        if ($config['flagShape'] === 'square' ) {
            $shape_class = 'cpel-flag-square';
        } elseif ($config['flagShape'] === 'rounded' ) {
            $shape_class = 'cpel-flag-rounded';
        }
        
        return sprintf(
            '<img src="%s" class="cpel-flag-image %s" alt="%s" loading="lazy" decoding="async" />',
            esc_url($lang['flag']),
            esc_attr($shape_class),
            esc_attr($lang['name'])
        );
    }
}

/**
 * Initialize Floating Switcher Frontend
 *
 * @since 2.5.6
 */
new CPEL_Floating_Lang_Switcher_Frontend();