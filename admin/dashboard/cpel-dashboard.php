<?php
namespace ConnectPolylangElementor;

if (!defined('ABSPATH')) {
    exit;
} 

/**
 * This is the main class for creating dashboard addon page and all submenu items
 * 
 * Do not call or initialize this class directly, instead use the function mentioned at the bottom of this file
 */

class CPEL_Polylang_Addons
{

    /**
     * None of these variables should be accessable from the outside of the class
     */
    private static $instance;
    private $addon_dir = __DIR__; 

    /**
     * initialize the class and create dashboard page only one time
     */
    public static function init( )
    {
                    
        if(empty(self::$instance) ) {
            return self::$instance = new self;
        }
                return self::$instance;

    }


    /**
     * Initialize the dashboard with specific plugins as per plugin tag
     */
    public function show_plugins()
    {
        add_action('admin_menu', array($this, 'init_plugins_dashboard_page'), 10);
        add_action('wp_ajax_cpel_plugins_activate_'. CPEL_BASENAME, array($this, 'cpel_plugins_activate'));
        add_action('admin_enqueue_scripts', array($this,'enqueue_required_scripts'));
        add_action('admin_head', array($this, 'remove_admin_notices'), 1);
        add_action('admin_post_cpel_set_default_language_elementor_library', array($this, 'handle_set_default_language_elementor_library'));
        add_action('wp_ajax_cpel_ajax_set_default_language_elementor_library', array($this, 'ajax_handle_set_default_language_elementor_library'));
    }

    /**
     * Remove admin notices on plugin pages to prevent interference
     */
    public function remove_admin_notices() {
        // Get current screen
        $screen = get_current_screen();
        if (!$screen) {
            return;
        }
        
        // List of your plugin's admin page IDs
        $plugin_pages = [
            'languages_page_cpel-get-started'
        ];
        
        // Check if we're on one of your plugin pages
        if (in_array($screen->id, $plugin_pages, true)) {
            // Remove all admin notices
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }

    /**
     * Handle ajax request for activating plugin from dashboard
     */
    public function cpel_plugins_activate()
    {
        if(current_user_can('activate_plugins')) {
                   
            $plugin_slug = isset($_POST['polylang_activate_slug']) ? sanitize_text_field(wp_unslash($_POST['polylang_activate_slug'])) : '';
                
            $wp_nonce = 'polylang-plugins-activate-' . $plugin_slug ;
            if(!empty($plugin_slug)) {
                if (! check_ajax_referer($wp_nonce, 'wp_nonce', false) ) {
                    wp_send_json_error('Invalid security token sent.');
                    wp_die();
                }
                    $pluginBase = ( isset($_POST['polylang_activate_pluginbase']) && !empty($_POST['polylang_activate_pluginbase']) )? sanitize_text_field(wp_unslash($_POST['polylang_activate_pluginbase'])) : null;
                
                    $plugin_base_arr=explode("/", $pluginBase);
                if(isset($plugin_base_arr[0]) && $plugin_base_arr[0]==$plugin_slug ) {
                    activate_plugin($pluginBase, '', false, true);
                  
                }else{
                    wp_send_json_error('Something wrong with plugin path.');
                    wp_die();
                }
            }else{
                 wp_send_json_error('Plugin slug is missing.');
                 wp_die();  
            }
        }else{
            wp_send_json_error('You have no permission to do this action.');
            wp_die();  
        }
    }


    /**
     * Initialize the main dashboard page for all plugins
     */
    public function init_plugins_dashboard_page()
    {
        add_submenu_page(
            'mlang',
            __('Connect Polylang', 'connect-polylang-elementor'),
            __('Connect Polylang', 'connect-polylang-elementor'),
            'manage_options',
            'cpel-get-started',
            array($this, 'displayPluginAdminDashboard')
        );
    }

    /**
     * This function will render and create the HTML display of dashboard page.
     * All the HTML can be located in other template files.
     * Avoid using any HTML here or use nominal HTML tags inside this function.
     */
    public function displayPluginAdminDashboard()
    {
        // Get current tab from URL.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET used only for tab display; value sanitized, no state change.
        $current_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'getting-started';
                
        echo '<div class="wrap cpel-dashboard-wrap">';
                
               // Header Section
               echo '<div class="cpel-dashboard-header">';
                   echo '<div class="cpel-header-content">';
                   echo '<div class="cpel-header-logo">';
                   echo '<img src="' . esc_url(CPEL_Helpers::get_plugin_url() . 'admin/dashboard/assets/images/connect-polylang-icon.png') . '" alt="Connect Polylang for Elementor" />';
                   echo '<h1 class="cpel-header-title">' . esc_html__('Connect Polylang for Elementor', 'connect-polylang-elementor') . '</h1>';
               echo '</div>';
               echo '<div class="cpel-header-actions">';
                   echo '<a href="' . esc_url('https://wordpress.org/support/plugin/connect-polylang-elementor/#new-topic-0') . '" class="button button-secondary" target="_blank">' . esc_html__('Get Support', 'connect-polylang-elementor') . '</a>';
                   echo '<a href="' . esc_url('https://docs.coolplugins.net/doc/connect-polylang-for-elementor/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard_header') . '" class="button button-secondary" target="_blank">' . esc_html__('Documentation', 'connect-polylang-elementor') . '</a>';
                   echo '<a href="' . esc_url('https://wordpress.org/support/plugin/connect-polylang-elementor/reviews/#new-post') . '" class="button button-primary" target="_blank">' . esc_html__('★★★★★ Rate Now', 'connect-polylang-elementor') . '</a>';
               echo '</div>';
           echo '</div>';
        echo '</div>';
                
        // Navigation Tabs - Get Started and Floating Switcher
        echo '<h2 class="nav-tab-wrapper">';
           echo '<a href="?page=cpel-get-started&tab=getting-started" class="nav-tab ' . ($current_tab == 'getting-started' ? 'nav-tab-active' : '') . '">' . esc_html__('Get Started', 'connect-polylang-elementor') . '</a>';
           echo '<a href="?page=cpel-get-started&tab=floating-switcher" class="nav-tab ' . ($current_tab == 'floating-switcher' ? 'nav-tab-active' : '') . '">' . esc_html__('Floating Language Switcher', 'connect-polylang-elementor') . '</a>';
        echo '</h2>';
                
        // Tab Content
        echo '<div class="cpel-tab-content-wrapper">';
        if ($current_tab === 'floating-switcher' ) {
            $this->floating_switcher_content();
        } else {
            $this->get_started_content();
        }
        echo '</div>';
        echo '</div>'; 
    }
            
    /**
     * Get started content
     */
    public function get_started_content()
    {
        include $this->addon_dir . '/includes/get-started-content.php';
    }
            
    /**
     * Floating switcher content
     */
    public function floating_switcher_content()
    {
        // Check Polylang
        $cpel_languages = function_exists('pll_languages_list') ? pll_languages_list() : [];
                
        if (empty($cpel_languages) ) :
            ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php echo esc_html__('No languages configured!', 'connect-polylang-elementor'); ?></strong><br>
               <?php echo esc_html__('Please configure at least two languages in Polylang settings.', 'connect-polylang-elementor'); ?>
                    </p>
                </div>
            <?php 
        endif;
        ?>
            <div id="cpel-floater-app-root"></div>
        <?php
    }

    /**
     * Lets enqueue all the required CSS & JS
     */

    public function enqueue_required_scripts()
    {
        // Get current screen
        $screen = get_current_screen();
        if (!$screen) {
            return;
        }
        
        // Define pages where CSS should load
        $load_css_on_pages = [
            'languages_page_cpel-get-started',  // Getting Started
            'languages_page_cpel-floating-switcher',  // Floating Switcher
        ];
        
        // Pages where AutoPoly helper JS should load (promo box present)
        $load_autopoly_js_on_pages = [
            'languages_page_cpel-get-started',  // Has promo box
        ];
        
        // Check if we should load CSS
        $should_load_css = false;
        
        // 1. Always load CSS on plugin's own pages
        if (in_array($screen->id, $load_css_on_pages, true)) {
            $should_load_css = true;
        }
        
        // 2. Check if the notice will be shown (for both CSS and JS)
        $show_notice = $this->should_show_autopoly_notice($screen);
        if ($show_notice) {
            $should_load_css = true;
        }
        
        // Enqueue CSS if needed
        if ($should_load_css) {
            wp_enqueue_style(
                'cpel-plugins-polylang-addon', 
                CPEL_Helpers::get_plugin_url() . 'admin/dashboard/assets/css/styles.min.css', 
                array(), 
                CPEL_PLUGIN_VERSION, 
                'all'
            );
        }
        
        // Enqueue AutoPoly helper JS where needed
        // Load on: notice pages OR plugin pages with promo box
        if ($show_notice || in_array($screen->id, $load_autopoly_js_on_pages, true)) {
            wp_enqueue_script(
                'cpel-autopoly-helper',
                CPEL_Helpers::get_plugin_url() . 'admin/dashboard/assets/js/autopoly-helper.min.js',
                array('jquery'),
                CPEL_PLUGIN_VERSION,
                true
            );
        }

        // Get Started page: Apply Now button (AJAX + fade out)
        if ( $screen->id === 'languages_page_cpel-get-started' ) {
            wp_localize_script(
                'cpel-autopoly-helper',
                'cpelGetStartedApply',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'action'   => 'cpel_ajax_set_default_language_elementor_library',
                    'applying' => __( 'Applying...', 'connect-polylang-elementor' ),
                    'applied' => __( 'Applied', 'connect-polylang-elementor' ),
                    'success'  => __( 'Language assigned successfully.', 'connect-polylang-elementor' ),
                )
            );
        }
    }

    /**
     * Assign default Polylang language to all Elementor templates (`elementor_library`)
     * that do not yet have a language.
     *
     * Triggered from the Get Started page button via admin-post.php.
     *
     * @return void
     */
    public function handle_set_default_language_elementor_library() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to perform this action.', 'connect-polylang-elementor' ) );
        }

        check_admin_referer( 'cpel_set_default_language_elementor_library', '_wpnonce_cpel_set_default_language_elementor_library' );

        $status = $this->process_set_default_language_elementor_library();
        wp_safe_redirect( admin_url( 'admin.php?page=cpel-get-started&cpel_lang_set=' . $status ) );
        exit;
    }

    /**
     * AJAX handler for assigning default language to Elementor templates.
     * Returns JSON for use by the Get Started "Apply Now" button.
     *
     * @return void
     */
    public function ajax_handle_set_default_language_elementor_library() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this action.', 'connect-polylang-elementor' ) ) );
        }

        check_ajax_referer( 'cpel_set_default_language_elementor_library', '_wpnonce_cpel_set_default_language_elementor_library' );

        $status = $this->process_set_default_language_elementor_library();
        wp_send_json_success( array( 'status' => $status ) );
    }

    /**
     * Process assigning default Polylang language to Elementor templates.
     *
     * @return int 0 on failure, 1 on success (updated), 2 when nothing to update.
     */
    private function process_set_default_language_elementor_library() {
        if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_get_post_language' ) ) {
            return 0;
        }

        $model = PLL()->model;
        $lang  = $model->get_default_language();

        if ( ! $lang ) {
            return 0;
        }

        $query = new \WP_Query(
            array(
                'post_type'      => 'elementor_library',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );

        $ids = array();

        if ( $query->posts ) {
            foreach ( $query->posts as $post_id ) {
                if ( ! pll_get_post_language( $post_id ) ) {
                    $ids[] = $post_id;
                }
            }
        }

        if ( ! empty( $ids ) ) {
            $model->post->set_language_in_mass( $ids, $lang );
            return 1;
        }

        return 2; // Nothing to update.
    }
    /**
     * Check if AutoPoly notice should be shown
     * Replicates logic from CPEL_AutoPoly_Notice::should_display_notice()
     * 
     * @param object $screen Current admin screen
     * @return bool
     */
    private function should_show_autopoly_notice($screen)
    { 
        // Check if user already dismissed this notice
        $user_id = get_current_user_id();
        $dismissed = get_user_meta($user_id, 'cpel_autopoly_notice_dismissed', true);
        if ($dismissed === '1') {
            return false;
        }
        
        // Check if AutoPoly is already active
        $status = CPEL_Helpers::get_autopoly_status();
        if ($status['active']) {
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
            || in_array($screen->id, $show_on_screens, true)) {
            return true;
        }
        
        return false;
    }   

}

/**
 * Initialize the main dashboard class with all required parameters
 */
function cpel_polylang_addon_settings_page()
{
    $polylang_page = CPEL_Polylang_Addons::init();
    $polylang_page->show_plugins();
}