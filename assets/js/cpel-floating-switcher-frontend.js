/**
 * Floating Language Switcher - Frontend JavaScript
 *
 * Handles the interactive behavior of the floating language switcher on the frontend.
 * Provides dropdown functionality, keyboard navigation, hover interactions, and
 * accessibility features for a smooth user experience.
 *
 * @package    Connect_Polylang_Elementor
 * @subpackage Connect_Polylang_Elementor/includes/js
 * @since      2.5.5
 */

(function () {
    'use strict';

    /**
     * FloatingSwitcher Class
     *
     * Manages the behavior and interactions of a single floating language switcher instance.
     * Handles dropdown toggle, keyboard navigation, mouse interactions, and dynamic width calculation.
     *
     * @class
     * @since 2.5.6
     */
    class FloatingSwitcher {
        /**
         * Constructor
         *
         * Initializes the floating switcher with necessary DOM elements and state.
         * Calculates initial width and sets up event listeners if dropdown exists.
         *
         * @since 2.5.6
         * @param {HTMLElement} element - The root switcher element
         */
        constructor(element) {
            this.switcher = element;
            this.dropdown = element.querySelector('.cpel-switcher-dropdown-list');
            this.currentItem = element.querySelector('.cpel-language-item__current[role="button"]');
            this.isOpen = false;
            this.closeTimeout = null;

            // Calculate and set fixed width to prevent layout shift
            this.setFixedWidth();

            // Initialize event listeners if required elements exist
            if (this.currentItem && this.dropdown) {
                this.init();
            }
        }

        /**
         * Set Fixed Width
         *
         * Calculates and sets the switcher width based on the currently selected language.
         * This prevents layout jerking when the dropdown opens/closes.
         *
         * @since 2.5.6
         */
        setFixedWidth() {
            const currentWidth = getComputedStyle(this.switcher).getPropertyValue('--switcher-width').trim();

            if (currentWidth && currentWidth !== 'auto') {
                return;
            }

            const currentLangItem = this.switcher.querySelector('.cpel-language-item__current') ||
                this.switcher.querySelector('.cpel-language-item__default');

            if (!currentLangItem) return;

            try {
                const measurer = document.createElement('div');
                measurer.style.cssText = 'position:absolute;visibility:hidden;white-space:nowrap;';
                measurer.className = 'cpel-language-item';
                document.body.appendChild(measurer);

                const styles = window.getComputedStyle(currentLangItem);
                measurer.style.fontSize = styles.fontSize;
                measurer.style.fontFamily = styles.fontFamily;
                measurer.style.fontWeight = styles.fontWeight;
                measurer.style.padding = styles.padding;
                measurer.style.gap = styles.gap;

                const langName = currentLangItem.querySelector('.cpel-language-item-name');
                const langFlag = currentLangItem.querySelector('.cpel-flag-image');

                if (langName) {
                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'cpel-language-item-name';
                    nameSpan.textContent = langName.textContent;
                    measurer.appendChild(nameSpan);
                }

                if (langFlag) {
                    const flagClone = langFlag.cloneNode(true);
                    if (langFlag.parentElement.firstChild === langFlag) {
                        measurer.insertBefore(flagClone, measurer.firstChild);
                    } else {
                        measurer.appendChild(flagClone);
                    }
                }

                const calculatedWidth = measurer.offsetWidth + 11.5;
                document.body.removeChild(measurer);

                if (calculatedWidth > 0) {
                    this.switcher.style.setProperty('--switcher-width', Math.ceil(calculatedWidth + 10) + 'px');
                }
            } catch (e) {
                // Silently fail
            }
        }

        /**
         * Initialize Event Listeners
         *
         * Sets up all event listeners for user interactions.
         *
         * @since 2.5.6
         */
        init() {
            // Click handler
            this.currentItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });

            // Hover handlers (desktop only)
            if (window.matchMedia('(min-width: 768px)').matches) {
                this.switcher.addEventListener('mouseenter', () => {
                    clearTimeout(this.closeTimeout);
                    this.open();
                });

                this.switcher.addEventListener('mouseleave', () => {
                    this.closeTimeout = setTimeout(() => this.close(), 200);
                });
            }

            // Keyboard navigation
            this.currentItem.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggle();
                } else if (e.key === 'Escape') {
                    this.close();
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.open();
                    this.focusFirstItem();
                }
            });

            // Keyboard navigation for dropdown items
            const items = this.dropdown.querySelectorAll('.cpel-language-item');
            items.forEach((item, index) => {
                item.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const nextItem = items[index + 1];
                        if (nextItem) nextItem.focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (index === 0) {
                            this.currentItem.focus();
                        } else {
                            items[index - 1].focus();
                        }
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        this.close();
                        this.currentItem.focus();
                    }
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.switcher.contains(e.target)) {
                    this.close();
                }
            });
        }

        /**
         * Toggle Dropdown
         *
         * @since 2.5.6
         */
        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        /**
         * Open Dropdown
         *
         * @since 2.5.6
         */
        open() {
            if (this.isOpen) return;

            this.isOpen = true;
            this.switcher.classList.add('is-transitioning');
            this.switcher.classList.add('is-open');
            this.switcher.setAttribute('aria-expanded', 'true');
            this.dropdown.removeAttribute('hidden');
            this.dropdown.removeAttribute('inert');

            setTimeout(() => {
                this.switcher.classList.remove('is-transitioning');
            }, 200);
        }

        /**
         * Close Dropdown
         *
         * @since 2.5.6
         */
        close() {
            if (!this.isOpen) return;

            this.isOpen = false;
            this.switcher.classList.add('is-transitioning');
            this.switcher.classList.remove('is-open');
            this.switcher.setAttribute('aria-expanded', 'false');

            setTimeout(() => {
                this.dropdown.setAttribute('hidden', '');
                this.dropdown.setAttribute('inert', '');
                this.switcher.classList.remove('is-transitioning');
            }, 200);
        }

        /**
         * Focus First Item
         *
         * @since 2.5.6
         */
        focusFirstItem() {
            const firstItem = this.dropdown.querySelector('.cpel-language-item');
            if (firstItem) firstItem.focus();
        }
    }

    /**
     * Initialize Floating Switchers
     *
     * @since 2.5.6
     */
    function initFloatingSwitchers() {
        const switchers = document.querySelectorAll('.cpel-floating-switcher.cpel-ls-dropdown');
        
        switchers.forEach(switcher => {
            new FloatingSwitcher(switcher);
        });
    }

    /**
     * Initialize on DOM Ready
     *
     * @since 2.5.6
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFloatingSwitchers);
    } else {
        initFloatingSwitchers();
    }

})();