<?php
namespace ConnectPolylangElementor;
if (! defined('ABSPATH') ) {
    exit;
}
?>
<div class="cpel-get-started-content">
    <div class="cpel-content-wrapper">
        <div class="cpel-content-left">
            <h3><?php esc_html_e('Quick Start Guide', 'connect-polylang-elementor'); ?></h3>
            <p><?php esc_html_e('Thank you for installing Connect Polylang for Elementor.', 'connect-polylang-elementor'); ?></p>
            <p><?php esc_html_e('This plugin helps you build and manage a multilingual Elementor website and add a powerful language switcher using Polylang.', 'connect-polylang-elementor'); ?></p>
            <p><?php esc_html_e('Follow the steps below to get started:', 'connect-polylang-elementor'); ?></p>
            
            <h4>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library&tabs_group=theme')); ?>" style="text-decoration: underline; color: inherit;">
                    <?php esc_html_e('Step 1: Add the Language Switcher Widget', 'connect-polylang-elementor'); ?>
                </a>
            </h4>
            <ul>
                <li><?php esc_html_e('Edit your header template with Elementor Editor.', 'connect-polylang-elementor'); ?></li>
                <li><?php esc_html_e('From the Elementor widgets panel, search for "Language Switcher".', 'connect-polylang-elementor'); ?></li>
                <li><?php esc_html_e('Drag and drop the Language Switcher.', 'connect-polylang-elementor'); ?></li>
                <li>
                    <?php esc_html_e('From the widget settings, customize:', 'connect-polylang-elementor'); ?>
                    <ul style="margin-top: 8px;">
                        <li><?php esc_html_e('Layout: vertical, horizontal, or dropdown.', 'connect-polylang-elementor'); ?></li>
                        <li><?php esc_html_e('Language Display: names, flags, or both.', 'connect-polylang-elementor'); ?></li>
                        <li><?php esc_html_e('Style: Alignment, colors, and typography.', 'connect-polylang-elementor'); ?></li>
                    </ul>
                </li>
                <li><?php esc_html_e('Once added, the switcher will automatically display all languages configured in Polylang.', 'connect-polylang-elementor'); ?></li>
            </ul>
            <p style="margin-top: 16px;">
            <a href="https://docs.coolplugins.net/doc/translate-elementor-templates/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=get_started" 
            class="button button-primary" 
            target="_blank" 
            rel="noopener noreferrer">
                <?php esc_html_e('Learn More', 'connect-polylang-elementor'); ?>
            </a>
            </p>
            
            <h4>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>" style="text-decoration: underline; color: inherit;">
                    <?php esc_html_e('Step 2: Translate Elementor Templates', 'connect-polylang-elementor'); ?>
                </a>
            </h4>
            <ul>
                <li>
                    <?php esc_html_e('From your WordPress Dashboard, navigate to', 'connect-polylang-elementor'); ?> 
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>" style="color: #1bb95a; text-decoration: none; font-weight: 600;">
                        <?php esc_html_e('Templates > Saved Templates', 'connect-polylang-elementor'); ?>
                    </a>.
                </li>
                <li><?php esc_html_e('Create a new Elementor template or edit an existing one.', 'connect-polylang-elementor'); ?></li>
                <li><?php esc_html_e('Use Polylang\'s language options to add translations for the template.', 'connect-polylang-elementor'); ?></li>
                <li><?php esc_html_e('Translate the content and save the template.', 'connect-polylang-elementor'); ?></li>
                <li><?php esc_html_e('Each translated template will be linked automatically, ensuring the correct language version is displayed on your website.', 'connect-polylang-elementor'); ?></li>
            </ul>
            <p style="margin-top: 16px;">
            <a href="https://docs.coolplugins.net/doc/translate-elementor-templates/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=docs&utm_content=get_started" 
            class="button button-primary" 
            target="_blank" 
            rel="noopener noreferrer">
                <?php esc_html_e('Learn More', 'connect-polylang-elementor'); ?>
            </a>
            </p>
            
            <?php 
            // AutoPoly promotional box
            if (class_exists('ConnectPolylangElementor\CPEL_Helpers')) {
                \ConnectPolylangElementor\CPEL_Helpers::render_autopoly_promo_box('get_started');
            } 
            ?>
        </div>
        
        <div class="cpel-content-right">
            <div class="cpel-video-section">
                <div class="cpel-video-container">
                    <iframe 
                        width="100%" 
                        height="500px" 
                        src="https://www.youtube.com/embed/HyM0woo9Cg0" 
                        title="<?php echo esc_attr__('Connect Polylang for Elementor Tutorial', 'connect-polylang-elementor'); ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
