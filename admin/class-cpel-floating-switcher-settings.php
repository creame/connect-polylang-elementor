<?php
/**
 * Floating Language Switcher Settings
 *
 * Handles the admin settings page for the floating language switcher feature.
 *
 * @package    Connect_Polylang_Elementor
 * @subpackage Connect_Polylang_Elementor/admin
 * @since      2.5.5
 */

namespace ConnectPolylangElementor;

if (! defined('ABSPATH') ) {
    exit;
}

/**
 * CPEL Floating Switcher Settings Class
 *
 * @since 2.5.6
 */
class CPEL_Floating_Lang_Switcher_Settings
{
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct()
    {    
        // Enqueue admin assets
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_assets' ]);
        
        // Register AJAX handlers
        add_action('wp_ajax_cpel_save_floating_switcher', [ $this, 'ajax_save_settings' ]);
        add_action('wp_ajax_cpel_install_autopoly', [ $this, 'ajax_install_autopoly' ]);
    }
    
    
    /**
     * Enqueue Admin Assets
     */
    public function enqueue_assets( $hook )
    {   
    
        // Check if we're on the Get Started page with floating-switcher tab.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET used only for conditional asset loading; values sanitized, no state change.
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET used only for conditional asset loading; values sanitized, no state change.
        $tab  = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : '';

        if ( $page === 'cpel-get-started' && $tab === 'floating-switcher' ) {
            
            $plugin_url = CPEL_Helpers::get_plugin_url();
            
            // Enqueue WordPress React libraries
            wp_enqueue_script('wp-element');
            wp_enqueue_script('wp-i18n');
            
            // Enqueue admin stylesheet
            wp_enqueue_style(
                'cpel-floating-switcher-admin',
                $plugin_url . 'admin/dashboard/assets/css/cpel-floating-switcher-admin.min.css',
                [],
                CPEL_PLUGIN_VERSION
            );
        
            // Enqueue React app JavaScript
            wp_enqueue_script(
                'cpel-floating-switcher-app',
                $plugin_url . 'admin/dashboard/assets/js/cpel-floating-switcher-app.min.js',
                [ 'wp-element', 'wp-i18n' ],
                CPEL_PLUGIN_VERSION,
                true
            );
        
            // Pass data to JavaScript
            wp_localize_script(
                'cpel-floating-switcher-app',
                'cpelFloaterData',
                $this->get_localized_data()
            );
        
            // Set up translations
            wp_set_script_translations(
                'cpel-floating-switcher-app',
                'connect-polylang-elementor',
                CPEL_DIR . 'languages'
            );
        }
    }
    
    /**
     * Get Localized Data
     */
    private function get_localized_data()
    {
        $config    = $this->get_switcher_config();
        $languages = $this->get_polylang_languages();
        $autopoly_status = $this->check_autopoly_status();
        
        return [
        'config'        => $config,
        'languages'     => $languages,
        'nonce'         => wp_create_nonce('cpel_floating_switcher_save'),
        'installNonce'  => wp_create_nonce('cpel_install_autopoly'),
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'pluginUrl'     => plugins_url('/', CPEL_FILE),
        'autoPolyStatus' => $autopoly_status
        ];
    }

    /**
     * Check AutoPoly Plugin Status
     */
    private function check_autopoly_status()
    {
        return \ConnectPolylangElementor\CPEL_Helpers::get_autopoly_status();
    }
    
    /**
     * Get Polylang Languages
     */
    private function get_polylang_languages()
    {
        if (! function_exists('pll_languages_list') ) {
            return $this->get_default_languages();
        }
        
        $languages     = [];
        $pll_languages = pll_languages_list([ 'fields' => false ]);
        
        if (empty($pll_languages) ) {
            return $this->get_default_languages();
        }
        
        foreach ( $pll_languages as $lang ) {
            $languages[] = [
                'code'   => $lang->slug,
                'name'   => $lang->name,
                'flag'   => CPEL_Helpers::get_plugin_flag_url($lang->flag_url ?? ''),
                'locale' => $lang->locale,
            ];
        }
        
        return $languages;
    }

    /**
     * Get Default Languages
     */
    private function get_default_languages()
    {
        $plugin_url = CPEL_Helpers::get_plugin_url();
        
        return [
            [
                'code'   => 'en',
                'name'   => __('English', 'connect-polylang-elementor'),
                'flag'   => $plugin_url . 'assets/flags/us.svg',
                'locale' => 'en_US',
            ],
            [
                'code'   => 'fr',
                'name'   => __('Français', 'connect-polylang-elementor'),
                'flag'   => $plugin_url . 'assets/flags/fr.svg',
                'locale' => 'fr_FR',
            ],
        ];
    }
    
    /**
     * Get Switcher Configuration
     */
    public function get_switcher_config()
    {
        $saved    = get_option('cpel_floating_switcher_config', null);
        $defaults = $this->get_default_config();
        
        if (! is_array($saved) || empty($saved) ) {
            update_option('cpel_floating_switcher_config', $defaults);
            return $defaults;
        }
        
        return $this->deep_merge_defaults($saved, $defaults);
    }
    
    /**
     * Get Default Configuration
     */
    public function get_default_config()
    {
        $layout_defaults = [
            'desktop' => [
                'position'         => 'bottom-right',
                'width'            => 'default',
                'customWidth'      => 216,
                'padding'          => 'default',
                'customPadding'    => 0,
                'flagIconPosition' => 'before',
                'languageNames'    => 'full',
            ],
            'mobile'  => [
                'position'         => 'bottom-right',
                'width'            => 'default',
                'customWidth'      => 216,
                'padding'          => 'default',
                'customPadding'    => 0,
                'flagIconPosition' => 'before',
                'languageNames'    => 'full',
            ],
        ];
        
        return [
            'enabled'           => false,
            'type'              => 'dropdown',
            'bgColor'           => '#ffffff',
            'bgHoverColor'      => '#0000000d',
            'textColor'         => '#143852',
            'textHoverColor'    => '#1d2327',
            'borderColor'       => '#1438521a',
            'borderWidth'       => 1,
            'borderRadius'      => [ 8, 8, 0, 0 ],
            'size'              => 'normal',
            'flagShape'         => 'rect',
            'flagRadius'        => 2,
            'enableCustomCss'   => true,
            'customCss'         => '',
            'layoutCustomizer'  => $layout_defaults,
            'enableTransitions' => true,
        ];
    }
    
    /**
     * Deep Merge with Defaults
     */
    private function deep_merge_defaults( $saved, $defaults )
    {
        foreach ( $defaults as $key => $default_value ) {
            if (! array_key_exists($key, $saved) ) {
                $saved[ $key ] = $default_value;
            } elseif (is_array($default_value) && is_array($saved[ $key ]) ) {
                $saved[ $key ] = $this->deep_merge_defaults($saved[ $key ], $default_value);
            }
        }
        return $saved;
    }
    
    /**
     * AJAX Handler: Save Settings
     */
    public function ajax_save_settings()
    {
        if (! current_user_can('manage_options') ) {
            wp_send_json_error(__('Permission denied.', 'connect-polylang-elementor'), 403);
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'cpel_floating_switcher_save') ) {
            wp_send_json_error(__('Invalid nonce.', 'connect-polylang-elementor'), 403);
        }
        
        $config_raw = isset($_POST['config']) ? sanitize_text_field(wp_unslash($_POST['config'])) : '{}';
        $config     = json_decode($config_raw, true);
        
        if (! is_array($config) ) {
            wp_send_json_error(__('Invalid data.', 'connect-polylang-elementor'), 400);
        }
        
        $sanitized = $this->sanitize_config($config);
        
        update_option('cpel_floating_switcher_config', $sanitized);
        
        wp_send_json_success(__('Settings saved successfully.', 'connect-polylang-elementor'));
    }

    /**
     * AJAX Handler: Install AutoPoly Plugin
     */
    public function ajax_install_autopoly()
    {
        if (! current_user_can('install_plugins') ) {
            wp_send_json_error(
                [
                'message' => __('Sorry, you are not allowed to install plugins on this site.', 'connect-polylang-elementor')
                 ] 
            );
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'cpel_install_autopoly') ) {
            wp_send_json_error(
                [
                'message' => __('Invalid security token.', 'connect-polylang-elementor')
                 ] 
            );
        }
        
        $plugin_slug = 'automatic-translations-for-polylang';
        
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        $api = plugins_api(
            'plugin_information', [
            'slug'   => $plugin_slug,
            'fields' => [ 'sections' => false ],
             ] 
        );
        
        if (is_wp_error($api) ) {
            wp_send_json_error([ 'message' => $api->get_error_message() ]);
        }
        
        $install_status = install_plugin_install_status($api);
        
        if ($install_status['status'] === 'install' ) {
            ob_start();
            $skin     = new \WP_Ajax_Upgrader_Skin();
            $upgrader = new \Plugin_Upgrader($skin);
            $result   = $upgrader->install($api->download_link);
            ob_end_clean();
            
            if (is_wp_error($result) ) {
                wp_send_json_error([ 'message' => $result->get_error_message() ]);
            }
            
            $install_status = install_plugin_install_status($api);
        }
        
        if (current_user_can('activate_plugin', $install_status['file']) && is_plugin_inactive($install_status['file']) ) {
            $activation_result = activate_plugin($install_status['file'], '', false, true);
            
            if (is_wp_error($activation_result) ) {
                wp_send_json_error([ 'message' => $activation_result->get_error_message() ]);
            }
        }
        if(!get_option('cpel_autopoly_installed')) {
            update_option('cpel_autopoly_installed', 'installed_by_cpel');
        }
        
        wp_send_json_success(
            [
            'message' => __('AutoPoly plugin installed and activated successfully!', 'connect-polylang-elementor')
             ] 
        );
    }
    
    /**
     * Sanitize Configuration
     */
    private function sanitize_config( $config )
    {
        $sanitized = [];
        
        $sanitized['enabled'] = ! empty($config['enabled']);
        $sanitized['enableCustomCss'] = ! empty($config['enableCustomCss']);
        $sanitized['enableTransitions'] = ! empty($config['enableTransitions']);
        
        $sanitized['type'] = in_array($config['type'] ?? '', [ 'dropdown', 'inline', 'side-by-side' ], true) 
        ? $config['type'] 
        : 'dropdown';
        
        $color_fields = [ 'bgColor', 'bgHoverColor', 'textColor', 'textHoverColor', 'borderColor' ];
        foreach ( $color_fields as $field ) {
            $sanitized[ $field ] = $this->sanitize_color($config[ $field ] ?? '#ffffff');
        }
        
        $sanitized['borderWidth'] = absint($config['borderWidth'] ?? 1);
        $sanitized['flagRadius'] = absint($config['flagRadius'] ?? 2);
        
        if (isset($config['borderRadius']) && is_array($config['borderRadius']) ) {
            $sanitized['borderRadius'] = array_map('absint', array_slice($config['borderRadius'], 0, 4));
            $sanitized['borderRadius'] = array_pad($sanitized['borderRadius'], 4, 0);
        } else {
            $sanitized['borderRadius'] = [ 8, 8, 0, 0 ];
        }
        
        $sanitized['size'] = in_array($config['size'] ?? '', [ 'normal', 'large' ], true)
            ? $config['size']
            : 'normal';
        
        $sanitized['flagShape'] = in_array($config['flagShape'] ?? '', [ 'rect', 'square', 'rounded' ], true)
            ? $config['flagShape']
            : 'rect';
        
        $sanitized['customCss'] = '';
        if (! empty($config['customCss']) ) {
            $custom_css = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $config['customCss']);
            $custom_css = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $custom_css);
            $custom_css = wp_kses($custom_css, array());
            $sanitized['customCss'] = sanitize_textarea_field($custom_css);
        }
        
        $sanitized['layoutCustomizer'] = [];
        
        foreach ( [ 'desktop', 'mobile' ] as $device ) {
            if (isset($config['layoutCustomizer'][ $device ]) && is_array($config['layoutCustomizer'][ $device ]) ) {
                $layout = $config['layoutCustomizer'][ $device ];
                
                $valid_positions = [
                    'bottom-right', 'bottom-left', 'bottom-center',
                    'top-right', 'top-left', 'top-center'
                ];
                $sanitized['layoutCustomizer'][ $device ]['position'] = in_array($layout['position'] ?? '', $valid_positions, true)
                    ? $layout['position']
                    : 'bottom-right';
                
                $sanitized['layoutCustomizer'][ $device ]['width'] = in_array($layout['width'] ?? '', [ 'default', 'custom' ], true)
                    ? $layout['width']
                    : 'default';
                $sanitized['layoutCustomizer'][ $device ]['customWidth'] = absint($layout['customWidth'] ?? 216);
                
                $sanitized['layoutCustomizer'][ $device ]['padding'] = in_array($layout['padding'] ?? '', [ 'default', 'custom' ], true)
                    ? $layout['padding']
                    : 'default';
                $sanitized['layoutCustomizer'][ $device ]['customPadding'] = absint($layout['customPadding'] ?? 0);
                
                $sanitized['layoutCustomizer'][ $device ]['flagIconPosition'] = in_array($layout['flagIconPosition'] ?? '', [ 'before', 'after', 'hide' ], true)
                ? $layout['flagIconPosition']
                : 'before';
                
                $sanitized['layoutCustomizer'][ $device ]['languageNames'] = in_array($layout['languageNames'] ?? '', [ 'full', 'short', 'none' ], true)
                    ? $layout['languageNames']
                    : 'full';
            } else {
                $sanitized['layoutCustomizer'][ $device ] = $this->get_default_config()['layoutCustomizer'][ $device ];
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize Color Value
     */
    private function sanitize_color( $color )
    {
        $color = trim($color);
        
        if (strtolower($color) === 'transparent' ) {
            return 'transparent';
        }
        
        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color) ) {
            return $color;
        }
        
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+\s*)?\)$/', $color) ) {
            return $color;
        }
        
        return '#ffffff';
    }
}