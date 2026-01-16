/**
 * AutoPoly Helper - Centralized JavaScript for AutoPoly actions
 * Handles install/activate button clicks across all admin pages
 * 
 * @package Connect_Polylang_Elementor
 * @since   1.0.0
 */

(function ($) {
    'use strict';
    
    /**
     * AutoPoly configuration - centralized URLs and settings
     */
    var AutoPolyConfig = {
        proUrlBase: 'https://coolplugins.net/product/autopoly-ai-translation-for-polylang/',
        proUrlParams: '?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=get_pro',
        
        /**
         * Get PRO URL with context
         */
        getProUrl: function (context) {
            return this.proUrlBase + this.proUrlParams + '&utm_content=' + (context || 'default');
        },
        
        /**
         * Get processing text based on button text
         */
        getProcessingText: function (buttonText) {
            if (buttonText.toLowerCase().indexOf('activate') !== -1) {
                return 'Activating...';
            }
            return 'Installing...';
        }
    };
    
    /**
     * AutoPoly Action Handler
     * Single handler for all Install/Activate buttons
     */
    var AutoPolyHandler = {
        
        /**
         * Initialize event handlers
         */
        init: function () {
            this.bindEvents();
        },
        
        /**
         * Bind click events to buttons
         */
        bindEvents: function () {
            $(document).on('click', '.cpel-autopoly-action-btn', this.handleAction);
        },
        
        /**
         * Handle Install/Activate action
         */
        handleAction: function (e) {
            e.preventDefault();
            
            var $btn = $(this);
            var originalText = $btn.text();
            var context = $btn.data('context') || 'default';
            var nonce = $btn.data('nonce');
            var processingText = AutoPolyConfig.getProcessingText(originalText);
            
            // Update button state
            $btn.text(processingText).prop('disabled', true).css('opacity', '0.6');
            
            // Send AJAX request
            $.ajax(
                {
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cpel_install_autopoly',
                        nonce: nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            AutoPolyHandler.handleSuccess($btn, context, response);
                        } else {
                            AutoPolyHandler.handleError($btn, originalText, response);
                        }
                    },
                    error: function () {
                        AutoPolyHandler.handleNetworkError($btn, originalText);
                    }
                }
            );
        },
        
         /**
          * Handle successful installation/activation
          */
        handleSuccess: function ($btn, context, response) {
            // Show success message briefly
            $btn.text('Activated').prop('disabled', true).css('opacity', '1');
            
            // Trigger custom event for other scripts to listen
            // This will cause the notice to be dismissed automatically
            $(document).trigger('autopoly-success', [context, response]);
            
            // Hide the notice immediately (no PRO button shown)
            setTimeout(
                function () {
                    $btn.closest('.notice, .e-notice').fadeOut();
                }, 1000
            ); // Brief delay to show success message
        },
        
        /**
         * Handle error response
         */
        handleError: function ($btn, originalText, response) {
            $btn.text(originalText).prop('disabled', false).css('opacity', '1');
            
            var errorMsg = (response.data && response.data.message) 
                ? response.data.message 
                : 'Installation failed. Please try again.';
            
            AutoPolyHandler.showMessage(errorMsg, 'error');
        },
        
        /**
         * Handle network error
         */
        handleNetworkError: function ($btn, originalText) {
            $btn.text(originalText).prop('disabled', false).css('opacity', '1');
            AutoPolyHandler.showMessage('Network error. Please check your connection.', 'error');
        },
        
        /**
         * Show message to user
         */
        showMessage: function (message, type) {
            alert(message);
        }
    };
    
    // Initialize on document ready
    $(document).ready(
        function () {
            AutoPolyHandler.init();
        }
    );
    
    // Expose config globally for other scripts if needed
    window.AutoPolyConfig = AutoPolyConfig;
    
})(jQuery);