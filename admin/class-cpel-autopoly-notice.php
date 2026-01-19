<?php
/**
 * AutoPoly Admin Notice
 * Displays promotional notice using Elementor's notice system
 * 
 * @package Connect_Polylang_Elementor
 * @since   2.5.6
 */
namespace ConnectPolylangElementor;

if (! defined('ABSPATH') ) {
    exit;
}

class CPEL_AutoPoly_Notice
{
    
    private static $instance = null;
    
    /**
     * Unique notice ID for Elementor's dismissal system
     */
    const NOTICE_ID = 'cpel-autopoly-promotion';
    
    public static function get_instance()
    {
        if (self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        // Use in_admin_header instead of admin_notices for Elementor notices
        add_action('in_admin_header', [ $this, 'display_elementor_notice' ]);
        // Add custom dismissal handler
        add_action('wp_ajax_cpel_dismiss_autopoly_notice', [ $this, 'dismiss_notice_ajax' ]);
    }

    /**
 * Handle AJAX dismissal of notice
 */
    public function dismiss_notice_ajax()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }
        check_ajax_referer('cpel_dismiss_notice', 'nonce');
        
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'cpel_autopoly_notice_dismissed', '1');
        
        wp_send_json_success();
    }
    
    /**
     * Check if we should display the notice on current page
     */
    private function should_display_notice()
    {
        // Check if Elementor is active and has admin notices component
        if (! did_action('elementor/loaded') ) {
            return false;
        }
        
        // Check if user already dismissed this notice
        $user_id = get_current_user_id();
        $dismissed = get_user_meta($user_id, 'cpel_autopoly_notice_dismissed', true);
        if ($dismissed === '1') {
            return false;
        }
        
        // Check if AutoPoly is already active
        $status = CPEL_Helpers::get_autopoly_status();
        if ($status['active'] ) {
            return false;
        }
        
        // Get current screen
        $screen = get_current_screen();
        if (! $screen ) {
            return false;
        }
        
        // Check if we're on target pages
        $show_on_screens = [
        'edit-elementor_library',
        'plugins',
        ];
        
        // Check for Polylang pages (contain mlang) or target screens
        if (strpos($screen->id, 'mlang') !== false  
            || strpos($screen->base, 'mlang') !== false 
            || in_array($screen->id, $show_on_screens, true) 
        ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Display notice using Elementor's admin notice component
     */
    public function display_elementor_notice()
    {
        if (! $this->should_display_notice() ) {
            return;
        }
        
        if (! class_exists('\Elementor\Plugin') ) {
            return;
        }
        
        try {
            $admin_notices = \Elementor\Plugin::$instance->admin->get_component('admin-notices');
            
            // Get AutoPoly status for button text
            $status = CPEL_Helpers::get_autopoly_status();
            $button_text = CPEL_Helpers::get_autopoly_button_text($status);
            
            // Build notice options
            $notice_options = [
            'id' => self::NOTICE_ID,
            'icon' => '',
            'type' => 'info',
            'description' => esc_html__('Translate your pages and posts with one click using AutoPoly. Save time and effort.', 'connect-polylang-elementor'),
            'button' => [
            'text' => esc_html($button_text),
            'url' => '#',
            'new_tab' => false,
            'type' => 'cta',
            'classes' => [ 'cpel-autopoly-action-btn' ],
            ],
            ];
            
            $admin_notices->print_admin_notice($notice_options);
            
            $this->enqueue_notice_scripts();
            
        } catch ( \Exception $e ) {
            return;
        }
    }
    
    /**
     * Enqueue scripts for notice interactions
     */
    private function enqueue_notice_scripts()
    {
        // Enqueue the centralized AutoPoly helper
        wp_enqueue_script(
            'cpel-autopoly-helper',
            CPEL_Helpers::get_plugin_url() . 'admin/dashboard/assets/js/autopoly-helper.min.js',
            [ 'jquery' ],
            CPEL_PLUGIN_VERSION,
            true
        );
        
        // Add custom script for Elementor notice integration
        wp_add_inline_script('cpel-autopoly-helper', $this->get_elementor_notice_script());
        
    }
    
    /**
     * Get inline script for Elementor notice integration
     */
    private function get_elementor_notice_script()
    {
        $nonce = wp_create_nonce('cpel_install_autopoly');
        
        ob_start();
        ?>
        jQuery(document).ready(function($) {
                var $installBtn = $('.cpel-autopoly-action-btn');
                if(!$installBtn.length) {
                    return;
                }

                $installBtn.data('nonce', '<?php echo esc_js($nonce); ?>');
                $installBtn.data('context', 'elementor_notice');
                
            
                // Auto-dismiss on success
                $(document).on('autopoly-success', function() {
                    setTimeout(function() {
                        // Send custom AJAX to save dismissal
                        $.post(ajaxurl, {
                            action: 'cpel_dismiss_autopoly_notice',
                            nonce: '<?php echo esc_js(wp_create_nonce('cpel_dismiss_notice')); ?>'
                        }).fail(function() {
                            console.error('Failed to dismiss notice');
                        });
                        
                        var $notice = $installBtn.closest('.e-notice');
                        var $dismissBtn = $notice.find('.e-notice__dismiss');
                        if ($dismissBtn.length) {
                            $dismissBtn.trigger('click');
                        } else {
                            $notice.fadeOut();
                        }
                    }, 2000);
                });

                $installBtn.closest('.e-notice').find('.e-notice__dismiss').on('click', function() {
                    $.post(ajaxurl, {
                        action: 'cpel_dismiss_autopoly_notice',
                        nonce: '<?php echo esc_js(wp_create_nonce('cpel_dismiss_notice')); ?>'
                    }).fail(function() {
                        console.error('Failed to dismiss notice');
                    });
                });
        });
        <?php
        return ob_get_clean();
    }

}

// Initialize
\ConnectPolylangElementor\CPEL_AutoPoly_Notice::get_instance();