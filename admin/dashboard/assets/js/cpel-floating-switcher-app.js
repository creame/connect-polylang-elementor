/**
 * CPEL Floating Switcher
 *
 * Main application component for managing the floating language switcher
 * settings in the admin dashboard.
 *
 * @package    Connect_Polylang_Elementor
 * @subpackage Connect_Polylang_Elementor/admin/dashboard/includes/js
 * @since     2.5.5
 */

(function () {
    "use strict";
  
    // Import WordPress element and i18n utilities
    const { createElement: h, render, Component } = wp.element;
    const { __, sprintf } = wp.i18n;
  
    /**
     * FloaterApp Component
     *
     * Main React component that handles the entire floating switcher configuration interface.
     */
    class FloaterApp extends Component {
      constructor(props) {
        super(props);
  
        // Load data from global window object passed from PHP
        const data = window.cpelFloaterData || {};
        
        // Initialize component state
        this.state = {
          config: data.config || this.getDefaultConfig(),
          languages: data.languages || [],
          currentDevice: "desktop",
          isSaving: false,
          hasChanges: false,
          originalConfig: JSON.stringify(data.config || this.getDefaultConfig()),
          showColorPicker: null,
          showPresetConfirm: null,
        };
  
        this.presets = this.getPresets();
      }
  
      getDefaultConfig() {
        return {
          enabled: false,
          type: "dropdown",
          bgColor: "#ffffff",
          bgHoverColor: "#0000000d",
          textColor: "#143852",
          textHoverColor: "#1d2327",
          borderColor: "#1438521a",
          borderWidth: 1,
          borderRadius: [8, 8, 0, 0],
          size: "normal",
          flagShape: "rect",
          flagRadius: 2,
          enableCustomCss: true,
          customCss: "",
          enableTransitions: true,
          layoutCustomizer: {
            desktop: {
              position: "bottom-right",
              width: "default",
              customWidth: 216,
              padding: "default",
              customPadding: 0,
              flagIconPosition: "before",
              languageNames: "full",
            },
            mobile: {
              position: "bottom-right",
              width: "default",
              customWidth: 216,
              padding: "default",
              customPadding: 0,
              flagIconPosition: "before",
              languageNames: "full",
            },
          },
        };
      }
  
      getPresets() {
        return [
          {
            name: __("Default", "connect-polylang-elementor"),
            config: {
              bgColor: "#ffffff",
              bgHoverColor: "#0000000d",
              textColor: "#143852",
              textHoverColor: "#1d2327",
              borderColor: "#1438521a",
            },
            background: "rgb(219, 219, 219)",
          },
          {
            name: __("Dark", "connect-polylang-elementor"),
            config: {
              bgColor: "#000000",
              bgHoverColor: "#444444",
              textColor: "#ffffff",
              textHoverColor: "#eeeeee",
              borderColor: "transparent",
            },
            background: "rgb(219, 219, 219)",
          },
          {
            name: __("Border", "connect-polylang-elementor"),
            config: {
              bgColor: "#FFFFFF",
              bgHoverColor: "#000000",
              textColor: "#143852",
              textHoverColor: "#ffffff",
              borderColor: "#143852",
            },
            background: "rgb(219, 219, 219)",
          },
          {
            name: __("Transparent", "connect-polylang-elementor"),
            config: {
              bgColor: "#FFFFFFB2",
              bgHoverColor: "#0000000D",
              textColor: "#000000",
              textHoverColor: "#000000",
              borderColor: "transparent",
            },
            background:
              "linear-gradient(145.41deg, rgb(34, 113, 177) 20.41%, rgb(211, 180, 218) 96.59%)",
          },
        ];
      }
  
      updateConfig(updates) {
        this.setState((prevState) => {
          const newConfig = { ...prevState.config, ...updates };
          return {
            config: newConfig,
            hasChanges: JSON.stringify(newConfig) !== prevState.originalConfig,
          };
        });
      }
  
      updateLayoutConfig(device, updates) {
        this.setState((prevState) => {
          const newConfig = {
            ...prevState.config,
            layoutCustomizer: {
              ...prevState.config.layoutCustomizer,
              [device]: {
                ...prevState.config.layoutCustomizer[device],
                ...updates,
              },
            },
          };
          return {
            config: newConfig,
            hasChanges: JSON.stringify(newConfig) !== prevState.originalConfig,
          };
        });
      }
  
      showPresetConfirmation(preset) {
        this.setState({ showPresetConfirm: preset });
      }
  
      applyPreset(preset) {
        this.updateConfig(preset.config);
        this.setState({ showPresetConfirm: null });
      }
  
      cancelPresetConfirmation() {
        this.setState({ showPresetConfirm: null });
      }
  
      isPresetActive(preset) {
        const { config } = this.state;
        const presetConfig = preset.config;
        return Object.keys(presetConfig).every((key) => {
          return config[key] === presetConfig[key];
        });
      }
  
      revertChanges() {
        const original = JSON.parse(this.state.originalConfig);
        this.setState({
          config: original,
          hasChanges: false,
        });
      }
  
      async saveSettings() {
        this.setState({ isSaving: true });
  
        const data = new FormData();
        data.append("action", "cpel_save_floating_switcher");
        data.append("nonce", window.cpelFloaterData.nonce);
        data.append("config", JSON.stringify(this.state.config));
  
        try {
          const response = await fetch(window.cpelFloaterData.ajaxUrl, {
            method: "POST",
            body: data,
            credentials: "same-origin",
          });
  
          const result = await response.json();
  
          if (result.success) {
            this.setState({
              originalConfig: JSON.stringify(this.state.config),
              hasChanges: false,
              isSaving: false,
            });
            this.showNotice(
              "success",
              __("Settings saved successfully!", "connect-polylang-elementor")
            );
          } else {
            throw new Error(
              result.data || __("Failed to save settings", "connect-polylang-elementor")
            );
          }
        } catch (error) {
          this.showNotice(
            "error",
            error.message || __("Failed to save settings", "connect-polylang-elementor")
          );
          this.setState({ isSaving: false });
        }
      }
  
      showNotice(type, message) {
        const notice = document.createElement("div");
        notice.className = `notice notice-${type} is-dismissible`;
        notice.innerHTML = `<p>${message}</p>`;
  
        const wrap = document.querySelector(".wrap");
        if (wrap) {
          wrap.insertBefore(notice, wrap.firstChild);
          setTimeout(() => notice.remove(), 3000);
        }
      }
  
      toggleCollapsible(event) {
        const box = event.currentTarget.closest(".cpel-settings-box");
        if (box && box.classList.contains("cpel-collapsible")) {
          box.classList.toggle("open");
        }
      }
  
      render() {
          return h(
          "div",
          { className: "cpel-floater-app-container" },
          h(
              "main",
              { className: "cpel-ls-view" },
              h(
              "div",
              { className: "cpel-floater-settings__wrapper" },
              this.renderLeftColumn(),
              this.renderRightColumn()
              )
          )
          );
      }
  
      renderRightColumn() {
        return h(
          "div",
          { className: "cpel-floater-settings__left" },
          h(
            "div",
            { className: "cpel-sticky-box" },
            this.renderActionButtons(),
            this.renderPreviewBox(),
            this.renderAutoPolyPromo()
          )
        );
      }
  
      renderPreviewBox() {
        const { config, currentDevice } = this.state;
      
        return h(
          "div",
          { className: "cpel-settings-box" },
          h(
            "header",
            { className: "cpel-header" },
            h(
              "span",
              { className: "cpel-title" },
              __("Switcher Preview", "connect-polylang-elementor")
            )
          ),
          h(
            "section",
            { className: "cpel-body" },
            
            !config.enabled ? h(
              "div",
              { 
                className: "cpel-preview-disabled-message",
                style: {
                  textAlign: "center",
                  padding: "60px 20px",
                  color: "#646970"
                }
              },
              h(
                "svg",
                {
                  width: "48",
                  height: "48",
                  viewBox: "0 0 24 24",
                  fill: "none",
                  style: { margin: "0 auto 16px", display: "block", opacity: "0.5" }
                },
                h("path", {
                  d: "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z",
                  fill: "#646970"
                })
              ),
              h(
                "p",
                { style: { margin: "0 0 8px", fontSize: "14px", fontWeight: "500" } },
                __("Switcher is Disabled", "connect-polylang-elementor")
              ),
              h(
                "p",
                { style: { margin: "0", fontSize: "13px", opacity: "0.8" } },
                __("Enable the floating switcher to see the preview", "connect-polylang-elementor")
              )
            ) : 
            [
              config.enableCustomCss &&
                config.customCss &&
                h("style", null, config.customCss),
      
              h(
                "div",
                {
                  className: "cpel-language-switcher-preview__container",
                  style: {
                    "--cpel-preview-bg": `url(${window.cpelFloaterData.pluginUrl}assets/images/preview-bg.png)`,
                  },
                },
                h(
                  "div",
                  { className: "cpel-language-switcher-preview-box" },
                  this.renderSwitcherPreview()
                )
              ),
      
              h(
                "span",
                {
                  className:
                    "cpel-language-switcher-preview-text cpel-description-text",
                },
                __(
                  "Hover over the language switcher to see it in action!",
                  "connect-polylang-elementor"
                )
              )
            ]
          )
        );
      }
  
  renderAutoPolyPromo() {
    
    const autoPolyStatus = window.cpelFloaterData?.autoPolyStatus || { installed: false, active: false };
    const isInstalled = autoPolyStatus.installed;
    const isActive = autoPolyStatus.active;
    
    const buttonText = isActive 
      ? __("Upgrade to PRO", "connect-polylang-elementor")
      : (isInstalled 
        ? __("Activate", "connect-polylang-elementor")
        : __("Try AutoPoly", "connect-polylang-elementor"));
    
    const proUrl = window.AutoPolyConfig 
      ? window.AutoPolyConfig.getProUrl('floating_switcher')
      : (window.cpelFloaterData?.autoPolyProUrl || "https://coolplugins.net/product/autopoly-ai-translation-for-polylang/?ref=creame&utm_source=cpel_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=floating_switcher");
    
    const actionUrl = isActive 
      ? "admin.php?page=polylang-atfp-dashboard" 
      : "plugin-install.php?s=autopoly&tab=search&type=term";
    
    return h(
      "div",
      { className: "cpel-promo-box" },
      h(
        "div",
        { className: "cpel-promo-text-section" },
        h("strong", null, __("AutoPoly - AI Translation For Polylang", "connect-polylang-elementor")),
        h("span", { className: "cpel-promo-subtitle" }, __("Translate pages instantly with one-click.", "connect-polylang-elementor")),
        
        !isActive ? h(
          "button",
          {
            className: "button button-primary cpel-promo-button",
            type: "button",
            onClick: this.handleInstallAutoPoly.bind(this)
          },
          buttonText
        ) : h(
          "a",
          {
            href: proUrl,
            className: "button button-primary cpel-promo-button cpel-promo-button-upgrade",
            target: "_blank",
            rel: "noopener noreferrer"
          },
          buttonText
        )
      ),
      h(
        "div",
        { className: "cpel-promo-image-section" },
        h(
          "a",
          { 
            href: actionUrl, 
            target: "_blank",
            rel: "noopener noreferrer"
          },
          h("img", {
            className: "cpel-promo-image",
            src: window.cpelFloaterData?.pluginUrl ? `${window.cpelFloaterData.pluginUrl}admin/dashboard/assets/images/autopoly-ai-translation-for-polylang-pro.png` : "",
            alt: "AutoPoly logo"
          })
        )
      )
    );
  }
  
  handleInstallAutoPoly(e) {
    e.preventDefault();
    
    const button = e.target;
    const originalText = button.textContent;
    const context = 'floating_switcher';
    
    const processingText = window.AutoPolyConfig 
      ? window.AutoPolyConfig.getProcessingText(originalText)
      : (originalText.toLowerCase().indexOf('activate') !== -1 ? __("Activating...", "connect-polylang-elementor") : __("Installing...", "connect-polylang-elementor"));
    
    button.textContent = processingText;
    button.disabled = true;
    button.style.opacity = "0.6";
    button.style.cursor = "not-allowed";
    
    const restoreButton = () => {
      button.textContent = originalText;
      button.disabled = false;
      button.style.opacity = "1";
      button.style.cursor = "pointer";
    };
    
    const formData = new FormData();
    formData.append("action", "cpel_install_autopoly");
    formData.append("nonce", window.cpelFloaterData.installNonce);
    
    fetch(window.cpelFloaterData.ajaxUrl, {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const proUrl = window.AutoPolyConfig 
          ? window.AutoPolyConfig.getProUrl(context)
          : window.cpelFloaterData.autoPolyProUrl;
        
        const upgradeLink = document.createElement("a");
        upgradeLink.href = proUrl;
        upgradeLink.className = "button button-primary cpel-promo-button cpel-promo-button-upgrade";
        upgradeLink.target = "_blank";
        upgradeLink.rel = "noopener noreferrer";
        upgradeLink.textContent = __("Upgrade to PRO", "connect-polylang-elementor");
        button.parentNode.replaceChild(upgradeLink, button);
        
        this.showInstallMessage("success", data.data.message || __("Plugin installed and activated successfully!", "connect-polylang-elementor"));
      } else {
        restoreButton();
        this.showInstallMessage("error", data.data.message || __("Failed to install plugin. Please try again.", "connect-polylang-elementor"));
      }
    })
    .catch(error => {
      restoreButton();
      this.showInstallMessage("error", __("Network error. Please check your connection and try again.", "connect-polylang-elementor"));
    });
  }
  
  showInstallMessage(type, message) {
    const promoBox = document.querySelector(".cpel-promo-box");
    if (!promoBox) return;
    
    const existingMessage = promoBox.querySelector(".cpel-install-message");
    if (existingMessage) {
      existingMessage.remove();
    }
    
    const messageDiv = document.createElement("div");
    messageDiv.className = `cpel-install-message cpel-install-message--${type}`;
    messageDiv.textContent = message;
    
    const divider = promoBox.querySelector(".cpel-promo-divider");
    if (divider) {
      divider.insertAdjacentElement("afterend", messageDiv);
    }
  }
  
      renderSwitcherPreview() {
        const { config, languages, currentDevice } = this.state;
        const layoutConfig = config.layoutCustomizer[currentDevice];
      
        const styles = this.buildPreviewStyles();
        
        const positionClass = layoutConfig.position.includes("bottom")
          ? "cpel-switcher-position-bottom"
          : "cpel-switcher-position-top";
      
        const isDropdown = config.type === "dropdown";
        const isSideBySide = config.type === "side-by-side";
        
        let allLangs = languages.length > 0
          ? languages
          : [
              { code: "en", name: "English", flag: "" },
              { code: "fr", name: "French", flag: "" },
            ];
        
        const maxLanguages = isSideBySide ? 3 : 5;
        const sampleLangs = allLangs.slice(0, maxLanguages);
      
        const current = sampleLangs[0];
        const others = sampleLangs.slice(1);
      
        return h(
          "div",
          {
            className: `cpel-language-switcher cpel-floating-switcher cpel-ls-${
              isDropdown ? "dropdown" : "inline"
            } ${positionClass}`,
            style: styles,
          },
          h(
            "div",
            { className: "cpel-language-switcher-inner" },
            isSideBySide
              ? sampleLangs.map((lang, index) =>
                  this.renderLanguageItem(lang, index === 0, layoutConfig, isDropdown)
                )
              : [
                  this.renderLanguageItem(current, true, layoutConfig, isDropdown),
                  others.length > 0 &&
                    h(
                      "div",
                      {
                        className:
                          "cpel-switcher-dropdown-list cpel-preview-expanded",
                      },
                      others.map((lang) =>
                        this.renderLanguageItem(lang, false, layoutConfig, isDropdown)
                      )
                    ),
                ]
          )
        );
      }
  
      renderLanguageItem(lang, isDefault, layoutConfig, isDropdown) {
        const flagUrl =
          lang.flag || `${window.cpelFloaterData.flagsPath}${lang.code}.png`;
        let displayName = "";
        if (layoutConfig.languageNames === "full") {
          displayName = lang.name;
        } else if (layoutConfig.languageNames === "short") {
          displayName = lang.code.toUpperCase();
        }
        return h(
          "a",
          {
            className: `cpel-language-item ${
              isDefault ? "cpel-language-item__default" : ""
            }`,
            onClick: (e) => e.preventDefault(),
          },
          layoutConfig.flagIconPosition === "before" &&
            h("img", {
              src: flagUrl,
              className: "cpel-flag-image",
              loading: "lazy",
              alt: lang.name,
            }),
          layoutConfig.languageNames !== "none" &&
            h(
              "span",
              {
                className: "cpel-language-item-name",
              },
              displayName
            ),
          layoutConfig.flagIconPosition === "after" &&
            h("img", {
              src: flagUrl,
              className: "cpel-flag-image",
              loading: "lazy",
              alt: lang.name,
            }),
            isDefault && isDropdown && h(
              "svg",
              {
                className: "cpel-dropdown-arrow",
                width: "12",
                height: "8",
                viewBox: "0 0 12 8",
                fill: "none",
                "aria-hidden": "true"
              },
              h("path", {
                d: "M1 6.5L6 1.5L11 6.5",
                stroke: "currentColor",
                "stroke-width": "2",
                "stroke-linecap": "round",
                "stroke-linejoin": "round"
              })
            )
          );
        }
  
      buildPreviewStyles() {
        const { config, currentDevice } = this.state;
        const layoutConfig = config.layoutCustomizer[currentDevice];
  
        const position = layoutConfig.position || "bottom-right";
        const [vertical, horizontal] = position.split("-");
  
        return {
          "--bg": config.bgColor,
          "--bg-hover": config.bgHoverColor,
          "--text": config.textColor,
          "--text-hover": config.textHoverColor,
          "--border-color": config.borderColor,
          "--border-radius": config.borderRadius.map((r) => r + "px").join(" "),
          "--font-size": config.size === "large" ? "16px" : "14px",
          "--flag-size": config.size === "large" ? "20px" : "18px",
          "--flag-radius": config.flagRadius + "px",
          "--aspect-ratio": config.flagShape === "rect" ? "4/3" : "1",
          "--transition-duration": config.enableTransitions ? "0.2s" : "0s",
          "--switcher-width":
            layoutConfig.width === "custom"
              ? layoutConfig.customWidth + "px"
              : "auto",
          "--switcher-padding":
            layoutConfig.padding === "custom"
              ? layoutConfig.customPadding + "px"
              : "0px 0px",
          "--border-width": config.borderWidth + "px",
          "--bottom": vertical === "bottom" ? "0px" : "auto",
          "--top": vertical === "top" ? "0px" : "auto",
          "--right": horizontal === "right" ? "14px" : "auto",
          "--left": horizontal === "left" ? "14px" : "auto",
        };
      }
  
      renderActionButtons() {
        const { hasChanges, isSaving } = this.state;
  
        return h(
          "div",
          { className: "cpel-settings-actions" },
          h(
            "button",
            {
              className: "cpel-submit-btn",
              onClick: () => this.saveSettings(),
              disabled: !hasChanges || isSaving,
            },
            h(
              "span",
              null,
              isSaving
                ? __("Saving...", "connect-polylang-elementor")
                : __("Save changes", "connect-polylang-elementor")
            )
          ),
          h(
            "button",
            {
              className: "cpel-button-secondary",
              onClick: () => this.revertChanges(),
              disabled: !hasChanges,
              title: __(
                "Revert to last saved values",
                "connect-polylang-elementor"
              ),
            },
            h(
              "svg",
              {
                width: 14,
                height: 14,
                viewBox: "0 0 14 14",
                fill: "none",
                style: { marginRight: "6px", verticalAlign: "middle" },
              },
              h("path", {
                d: "M7.1752 0.713867C10.7452 0.713867 13.3002 3.54187 13.3002 7.01387C13.3002 10.4859 10.7452 13.3139 7.1752 13.3139C4.9352 13.3139 2.9612 12.2009 1.7992 10.5209L3.6122 9.45687C4.3822 10.5069 5.6142 11.2139 7.0002 11.2139C9.3102 11.2139 11.2002 9.26087 11.2002 7.01387C11.2002 4.76687 9.3102 2.81387 7.0002 2.81387C5.6212 2.81387 4.3962 3.51387 3.6262 4.55687L4.9002 5.61387L0.700195 7.01387V2.11387L2.0232 3.21987C3.2062 1.70087 5.0752 0.713867 7.1752 0.713867Z",
                fill: "#2271B1",
              })
            ),
            __("Revert changes", "connect-polylang-elementor")
          )
        );
      }
  
      renderLeftColumn() {
        return h(
          "div",
          { className: "cpel-floater-settings__right" },
          this.renderEnableAndType(),
          this.renderPresets(),
          this.renderCustomizeLayout(),
          this.renderCustomizeDesign()
        );
      }
  
      renderEnableAndType() {
        const { config } = this.state;
  
        return h(
          "div",
          { className: "cpel-settings-box" },
          h(
            "header",
            { className: "cpel-header" },
            h(
              "span",
              { className: "cpel-title" },
              __("Floating Language Switcher Settings", "connect-polylang-elementor")
            )
          ),
          h(
            "section",
            { className: "cpel-body" },
  
            h(
              "div",
              {
                className: "cpel-field cpel-field--row",
                style: { marginBottom: "20px" },
              },
              h(
                "span",
                { className: "cpel-field__label cpel-primary-text-bold" },
                __("Enable Floating Language Switcher", "connect-polylang-elementor")
              ),
              this.renderToggleField(
                "enabled",
                config.enabled,
                config.enabled
                  ? __("Switcher is enabled", "connect-polylang-elementor")
                  : __("Switcher is disabled", "connect-polylang-elementor"),
                null
              )
            ),
  
            h("div", { className: "cpel-separator" }),
  
            h(
              "div",
              {
                className: "cpel-field cpel-field--row",
                style: { gap: "12px" },
              },
              h(
                "span",
                { className: "cpel-field__label cpel-primary-text-bold" },
                __("Switcher Type", "connect-polylang-elementor")
              ),
              h(
                "div",
                { className: "cpel-lc-mode-toggle" },
                h(
                  "button",
                  {
                    className: `cpel-lc-mode-button ${
                      config.type === "dropdown" ? "active" : ""
                    }`,
                    type: "button",
                    onClick: () => this.updateConfig({ type: "dropdown" }),
                  },
                  h("span", null, __("Dropdown", "connect-polylang-elementor"))
                ),
                h(
                  "button",
                  {
                    className: `cpel-lc-mode-button ${
                      config.type === "side-by-side" ? "active" : ""
                    }`,
                    type: "button",
                    onClick: () => this.updateConfig({ type: "side-by-side" }),
                  },
                  h("span", null, __("Side by Side", "connect-polylang-elementor"))
                )
              )
            )
          )
        );
      }
  
      renderPresets() {
        return h(
          "div",
          { className: "cpel-settings-box" },
          h(
            "header",
            { className: "cpel-header" },
            h(
              "span",
              { className: "cpel-title" },
              __("Apply a Preset", "connect-polylang-elementor")
            )
          ),
          h(
            "section",
            { className: "cpel-body" },
            h(
              "div",
              { className: "cpel-preset-applier" },
              this.presets.map((preset) => this.renderPresetCard(preset))
            )
          )
        );
      }
  
      renderPresetCard(preset) {
        const { languages, showPresetConfirm } = this.state;
        const { config } = this.state;
        const isDropdown = config.type === "dropdown";
        const isSideBySide = config.type === "side-by-side";
      
        let allLangs = languages.length > 0
          ? languages
          : [
              { code: "en", name: "English", flag: "" },
              { code: "fr", name: "French", flag: "" },
            ];
        
        const maxLanguages = isSideBySide ? 3 : 5;
        const sampleLangs = allLangs.slice(0, maxLanguages);
      
        const presetStyles = {
          "--bg": preset.config.bgColor,
          "--bg-hover": preset.config.bgHoverColor,
          "--text": preset.config.textColor,
          "--text-hover": preset.config.textHoverColor,
          "--border-color": preset.config.borderColor,
          "--border-radius": "8px",
          "--font-size": "14px",
          "--flag-size": "18px",
          "--flag-radius": "2px",
          "--aspect-ratio": "4/3",
          "--transition-duration": "0.2s",
        };
  
        const current = sampleLangs[0];
        const others = sampleLangs.slice(1);
        
        const isConfirming =
          showPresetConfirm && showPresetConfirm.name === preset.name;
  
        return h(
          "div",
          {
            className: `cpel-preset-card${
              this.isPresetActive(preset) ? " cpel-preset-card-active" : ""
            }`,
            style: { ...presetStyles, position: "relative" },
          },
  
          isConfirming &&
            h(
              "div",
              {
                className: "cpel-preset-confirm-overlay",
              },
              h(
                "div",
                { className: "cpel-preset-confirm-content" },
                h(
                  "p",
                  { className: "cpel-preset-confirm-title" },
                  __(
                    "Are you sure you want to apply the ",
                    "connect-polylang-elementor"
                  ),
                  h("strong", null, preset.name),
                  __(" preset?", "connect-polylang-elementor")
                ),
                h(
                  "p",
                  { className: "cpel-preset-confirm-warning" },
                  __(
                    "It will override your current settings.",
                    "connect-polylang-elementor"
                  )
                ),
                h(
                  "div",
                  { className: "cpel-preset-confirm-actions" },
                  h(
                    "button",
                    {
                      className:
                        "cpel-preset-confirm-btn cpel-preset-confirm-btn-primary",
                      onClick: () => this.applyPreset(preset),
                    },
                    __("Apply Preset", "connect-polylang-elementor")
                  ),
                  h(
                    "button",
                    {
                      className:
                        "cpel-preset-confirm-btn cpel-preset-confirm-btn-secondary",
                      onClick: () => this.cancelPresetConfirmation(),
                    },
                    __("Cancel", "connect-polylang-elementor")
                  )
                )
              )
            ),
  
          h(
            "div",
            {
              className: "cpel-preview-rect",
              style: { background: preset.background },
            },
            h(
              "div",
              {
                className: `cpel-preset-switcher-preview cpel-language-switcher cpel-floating-switcher cpel-ls-${
                  isDropdown ? "dropdown" : "inline"
                } cpel-switcher-position-bottom`,
              },
              h(
                "div",
                { className: "cpel-language-switcher-inner" },
                isSideBySide
                  ? sampleLangs.map((lang, index) =>
                      h(
                        "a",
                        {
                          className: `cpel-language-item ${
                            index === 0 ? "cpel-language-item__current" : ""
                          }`,
                          onClick: (e) => e.preventDefault(),
                        },
                        h("img", {
                          src:
                            lang.flag ||
                            `${window.cpelFloaterData.flagsPath}${lang.code}.png`,
                          className: "cpel-flag-image",
                          loading: "lazy",
                          alt: lang.name,
                        }),
                        h(
                          "span",
                          { className: "cpel-language-item-name" },
                          lang.name
                        )
                      )
                    )
                  : [
                      h(
                        "a",
                        {
                          className:
                            "cpel-language-item cpel-language-item__default",
                          onClick: (e) => e.preventDefault(),
                        },
                        h("img", {
                          src:
                            current.flag ||
                            `${window.cpelFloaterData.flagsPath}${current.code}.png`,
                          className: "cpel-flag-image",
                          loading: "lazy",
                          alt: current.name,
                        }),
                        h(
                          "span",
                          { className: "cpel-language-item-name" },
                          current.name
                        )
                      ),
                      others.length > 0 &&
                        h(
                          "div",
                          { className: "cpel-switcher-dropdown-list" },
                          others.map((lang) =>
                            h(
                              "a",
                              {
                                className: "cpel-language-item",
                                onClick: (e) => e.preventDefault(),
                              },
                              h("img", {
                                src:
                                  lang.flag ||
                                  `${window.cpelFloaterData.flagsPath}${lang.code}.png`,
                                className: "cpel-flag-image",
                                loading: "lazy",
                                alt: lang.name,
                              }),
                              h(
                                "span",
                                { className: "cpel-language-item-name" },
                                lang.name
                              )
                            )
                          )
                        ),
                    ]
              )
            )
          ),
          h(
            "button",
            {
              className: `cpel-apply-btn${
                this.isPresetActive(preset) ? " cpel-apply-btn-active" : ""
              }`,
              onClick: () => this.showPresetConfirmation(preset),
              disabled: this.isPresetActive(preset),
            },
            this.isPresetActive(preset)
              ? __("Applied", "connect-polylang-elementor")
              : sprintf(
                  __("Apply %s Preset", "connect-polylang-elementor"),
                  preset.name
                )
          )
        );
      }
  
      renderCustomizeDesign() {
        const { config } = this.state;
  
        return h(
          "div",
          {
            className: "cpel-settings-box cpel-collapsible",
            style: { "--cpel-field-label-width": "190px" },
          },
          h(
            "header",
            {
              className: "cpel-header",
              onClick: (e) => this.toggleCollapsible(e),
            },
            h(
              "span",
              { className: "cpel-title" },
              __("Customize Design", "connect-polylang-elementor")
            ),
            this.renderChevron()
          ),
          h(
            "section",
            { className: "cpel-body" },
  
            this.renderColorField(
              "bgColor",
              __("Background color", "connect-polylang-elementor"),
              config.bgColor
            ),
            this.renderColorField(
              "bgHoverColor",
              __("Background hover color", "connect-polylang-elementor"),
              config.bgHoverColor
            ),
            this.renderColorField(
              "textColor",
              __("Text color", "connect-polylang-elementor"),
              config.textColor
            ),
            this.renderColorField(
              "textHoverColor",
              __("Text hover color", "connect-polylang-elementor"),
              config.textHoverColor
            ),
            this.renderColorField(
              "borderColor",
              __("Switcher border color", "connect-polylang-elementor"),
              config.borderColor
            ),
  
            this.renderNumberField(
              "borderWidth",
              __("Switcher border width", "connect-polylang-elementor"),
              config.borderWidth
            ),
  
            this.renderBorderRadiusField(),
  
            h("div", { className: "cpel-separator" }),
  
            this.renderToggleField(
              "enableTransitions",
              config.enableTransitions,
              __("Switcher animations", "connect-polylang-elementor"),
              null
            ),
  
            h("div", { className: "cpel-separator" }),
  
            this.renderRadioGroup(
              "size",
              config.size,
              [
                {
                  value: "normal",
                  label: __("Normal", "connect-polylang-elementor"),
                },
                {
                  value: "large",
                  label: __("Large", "connect-polylang-elementor"),
                },
              ],
              __("Flag and text size", "connect-polylang-elementor"),
              "column"
            ),
  
            h("div", { className: "cpel-separator" }),
  
            this.renderRadioGroup(
              "flagShape",
              config.flagShape,
              [
                {
                  value: "rect",
                  label: __("Rectangle (4:3)", "connect-polylang-elementor"),
                },
                {
                  value: "square",
                  label: __("Square (1:1)", "connect-polylang-elementor"),
                },
              ],
              __("Flag icons shape", "connect-polylang-elementor"),
              "column"
            ),
  
            this.renderNumberField(
              "flagRadius",
              __("Flag icons border radius", "connect-polylang-elementor"),
              config.flagRadius
            ),
  
            h("div", { className: "cpel-separator" }),
  
            this.renderToggleField(
              "enableCustomCss",
              config.enableCustomCss,
              __("Enable custom CSS", "connect-polylang-elementor"),
              null
            ),
  
            config.enableCustomCss && this.renderCustomCssField()
          )
        );
      }
  
      renderCustomizeLayout() {
        const { config, currentDevice } = this.state;
        const layoutConfig = config.layoutCustomizer[currentDevice];
  
        return h(
          "div",
          { className: "cpel-settings-box cpel-collapsible" },
          h(
            "header",
            {
              className: "cpel-header",
              onClick: (e) => this.toggleCollapsible(e),
            },
            h(
              "span",
              { className: "cpel-title" },
              __("Customize Layout", "connect-polylang-elementor")
            ),
            this.renderChevron()
          ),
          h(
            "section",
            { className: "cpel-body" },
            h(
              "div",
              {
                className:
                  "cpel-layout-customizer-field cpel-field cpel-field--column cpel-field cpel-field--row",
              },
              h(
                "div",
                { className: "cpel-lc-mode-toggle" },
                h(
                  "button",
                  {
                    className: `cpel-lc-mode-button ${
                      currentDevice === "desktop" ? "active" : ""
                    }`,
                    type: "button",
                    onClick: () => this.setState({ currentDevice: "desktop" }),
                  },
                  this.renderDesktopIcon(),
                  h("span", null, __("Desktop", "connect-polylang-elementor"))
                ),
                h(
                  "button",
                  {
                    className: `cpel-lc-mode-button ${
                      currentDevice === "mobile" ? "active" : ""
                    }`,
                    type: "button",
                    onClick: () => this.setState({ currentDevice: "mobile" }),
                  },
                  this.renderMobileIcon(),
                  h("span", null, __("Mobile", "connect-polylang-elementor"))
                )
              ),
  
              h(
                "div",
                { className: "cpel-lc-settings-panel" },
                h(
                  "div",
                  { className: "cpel-lc-section" },
                  h(
                    "div",
                    { className: "cpel-lc-subfield" },
                    this.renderLayoutRadioGroup(
                      "position",
                      [
                        {
                          value: "bottom-right",
                          label: __("Bottom Right", "connect-polylang-elementor"),
                        },
                        {
                          value: "bottom-left",
                          label: __("Bottom Left", "connect-polylang-elementor"),
                        },
                        {
                          value: "top-right",
                          label: __("Top Right", "connect-polylang-elementor"),
                        },
                        {
                          value: "top-left",
                          label: __("Top Left", "connect-polylang-elementor"),
                        },
                      ],
                      __("Switcher Position", "connect-polylang-elementor")
                    )
                  ),
  
                  h(
                    "div",
                    { className: "cpel-lc-subfield" },
                    this.renderLayoutRadioGroup(
                      "width",
                      [
                        {
                          value: "default",
                          label: __("Default", "connect-polylang-elementor"),
                        },
                        {
                          value: "custom",
                          label: __("Custom", "connect-polylang-elementor"),
                        },
                      ],
                      __("Switcher Width", "connect-polylang-elementor")
                    )
                  ),
  
                  layoutConfig.width === "custom" &&
                    h(
                      "div",
                      { className: "cpel-lc-subfield" },
                      this.renderLayoutNumberField(
                        "customWidth",
                        __("Custom Width", "connect-polylang-elementor"),
                        layoutConfig.customWidth
                      )
                    ),
  
                  h(
                    "div",
                    { className: "cpel-lc-subfield" },
                    this.renderLayoutRadioGroup(
                      "padding",
                      [
                        {
                          value: "default",
                          label: __("Default", "connect-polylang-elementor"),
                        },
                        {
                          value: "custom",
                          label: __("Custom", "connect-polylang-elementor"),
                        },
                      ],
                      __("Switcher Padding", "connect-polylang-elementor")
                    )
                  ),
  
                  layoutConfig.padding === "custom" &&
                    h(
                      "div",
                      { className: "cpel-lc-subfield" },
                      this.renderLayoutNumberField(
                        "customPadding",
                        __("Custom Padding", "connect-polylang-elementor"),
                        layoutConfig.customPadding
                      )
                    ),
  
                  h(
                    "div",
                    { className: "cpel-lc-subfield" },
                    this.renderLayoutRadioGroup(
                      "flagIconPosition",
                      [
                        {
                          value: "before",
                          label: __("Before Language", "connect-polylang-elementor"),
                        },
                        {
                          value: "after",
                          label: __("After Language", "connect-polylang-elementor"),
                        },
                        {
                          value: "hide",
                          label: __("Hide Icons", "connect-polylang-elementor"),
                        },
                      ],
                      __("Flag Icons Position", "connect-polylang-elementor")
                    )
                  ),
  
                  h(
                    "div",
                    { className: "cpel-lc-subfield" },
                    this.renderLayoutRadioGroup(
                      "languageNames",
                      [
                        {
                          value: "full",
                          label: __("Full Names", "connect-polylang-elementor"),
                        },
                        {
                          value: "short",
                          label: __("Short Names", "connect-polylang-elementor"),
                        },
                        {
                          value: "none",
                          label: __("No Names", "connect-polylang-elementor"),
                        },
                      ],
                      __("Language Names", "connect-polylang-elementor")
                    )
                  )
                )
              )
            )
          )
        );
      }
  
      renderToggleField(key, value, label, description) {
        return h(
          "div",
          { className: "cpel-toggle-status-field cpel-field cpel-field--row" },
          h("span", { className: "cpel-primary-text" }, label),
          h(
            "div",
            { className: "cpel-toggle-wrapper" },
            h(
              "div",
              { className: "cpel-toggle-inner" },
              h("input", {
                type: "checkbox",
                className: "cpel-toggle-input",
                checked: value,
                onChange: (e) => this.updateConfig({ [key]: e.target.checked }),
              }),
              h("span", { className: "cpel-toggle-slider" })
            )
          )
        );
      }
  
      renderRadioGroup(key, value, options, title = null, layout = "column") {
        return h(
          "div",
          {
            className: `cpel-radio-group__wrapper cpel-field cpel-field--${layout}`,
          },
          title &&
            h(
              "span",
              { className: "cpel-field__label cpel-primary-text-bold" },
              title
            ),
          h(
            "div",
            { className: "cpel-radio-group" },
            options.map((option) =>
              h(
                "div",
                { className: "cpel-radio-option", key: option.value },
                h(
                  "label",
                  { className: "cpel-radio-label" },
                  h("input", {
                    type: "radio",
                    name: key,
                    checked: value === option.value,
                    value: option.value,
                    onChange: (e) => this.updateConfig({ [key]: e.target.value }),
                  }),
                  h("span", null, option.label)
                )
              )
            )
          )
        );
      }
  
      renderLayoutRadioGroup(key, options, title) {
        const { currentDevice, config } = this.state;
        const value = config.layoutCustomizer[currentDevice][key];
  
        return h(
          "div",
          { className: "cpel-radio-group__wrapper" },
          h(
            "span",
            { className: "cpel-field__label cpel-primary-text-bold" },
            title
          ),
          h(
            "div",
            { className: "cpel-radio-group" },
            options.map((option) =>
              h(
                "div",
                { className: "cpel-radio-option", key: option.value },
                h(
                  "label",
                  { className: "cpel-radio-label" },
                  h("input", {
                    type: "radio",
                    name: `${currentDevice}-${key}`,
                    checked: value === option.value,
                    value: option.value,
                    onChange: (e) =>
                      this.updateLayoutConfig(currentDevice, {
                        [key]: e.target.value,
                      }),
                  }),
                  h("span", null, option.label)
                )
              )
            )
          )
        );
      }
  
      renderColorField(key, label, value) {
        const displayValue =
          value && value.length > 7 ? value.substring(0, 7) : value;
        const isTransparent = value === "transparent";
  
        return h(
          "div",
          { className: "cpel-field cpel-field--row" },
          h(
            "span",
            { className: "cpel-field__label cpel-primary-text-bold" },
            label
          ),
          h(
            "div",
            { className: "cpel-color__wrapper" },
            h("input", {
              type: "color",
              className: "cpel-color-input",
              value: isTransparent ? "#ffffff" : displayValue,
              onChange: (e) => this.updateConfig({ [key]: e.target.value }),
              title: __("Pick a color", "connect-polylang-elementor"),
            }),
            h(
              "span",
              {
                className: "cpel-color-code cpel-primary-text",
                style: { cursor: isTransparent ? "pointer" : "default" },
                onClick: isTransparent
                  ? () => this.updateConfig({ [key]: "#000000" })
                  : null,
              },
              value.toUpperCase()
            )
          )
        );
      }
  
      renderNumberField(key, label, value, min = 0) {
        return h(
          "div",
          { className: "cpel-field cpel-field--row" },
          h(
            "span",
            { className: "cpel-field__label cpel-primary-text-bold" },
            label
          ),
          h(
            "div",
            { className: "cpel-number__wrapper" },
            h("input", {
              type: "number",
              className: "cpel-number-input",
              min: min,
              value: value,
              onChange: (e) =>
                this.updateConfig({ [key]: parseInt(e.target.value) || 0 }),
            }),
            h("span", { className: "cpel-primary-text" }, "px")
          )
        );
      }
  
      renderLayoutNumberField(key, label, value) {
        const { currentDevice } = this.state;
  
        return h(
          "div",
          { className: "cpel-field cpel-field--row" },
          h(
            "span",
            { className: "cpel-field__label cpel-primary-text-bold" },
            label
          ),
          h(
            "div",
            { className: "cpel-number__wrapper" },
            h("input", {
              type: "number",
              className: "cpel-number-input",
              min: 0,
              value: value,
              onChange: (e) =>
                this.updateLayoutConfig(currentDevice, {
                  [key]: parseInt(e.target.value) || 0,
                }),
            }),
            h("span", { className: "cpel-primary-text" }, "px")
          )
        );
      }
  
      renderBorderRadiusField() {
        const { config } = this.state;
        
        const corners = [
          __("Top Left", "connect-polylang-elementor"),
          __("Top Right", "connect-polylang-elementor"),
          __("Bottom Right", "connect-polylang-elementor"),
          __("Bottom Left", "connect-polylang-elementor"),
        ];
  
        return h(
          "div",
          { className: "cpel-field cpel-field--column" },
          h(
            "span",
            { className: "cpel-field__label cpel-primary-text-bold" },
            __("Switcher border radius", "connect-polylang-elementor")
          ),
          h(
            "div",
            { className: "cpel-quad-grid" },
            corners.map((corner, index) =>
              h(
                "div",
                { className: "cpel-quad-radius-corner", key: corner },
                h(
                  "span",
                  { className: "cpel-primary-text cpel-corner-label" },
                  corner
                ),
                h(
                  "div",
                  { className: "cpel-number__wrapper" },
                  h("input", {
                    type: "number",
                    className: "cpel-number-input",
                    min: 0,
                    value: config.borderRadius[index],
                    onChange: (e) => {
                      const newRadius = [...config.borderRadius];
                      newRadius[index] = parseInt(e.target.value) || 0;
                      this.updateConfig({ borderRadius: newRadius });
                    },
                  }),
                  h("span", { className: "cpel-primary-text" }, "px")
                )
              )
            )
          )
        );
      }
  
      renderCustomCssField() {
        const { config } = this.state;
  
        return h(
          "div",
          {
            className: "cpel-custom-css-editor cpel-field cpel-field--row",
            style: { display: config.enableCustomCss ? "block" : "none" },
          },
          h("textarea", {
            placeholder: __("Write custom CSS here...", "connect-polylang-elementor"),
            value: config.customCss,
            onChange: (e) => this.updateConfig({ customCss: e.target.value }),
            style: {
              width: "100%",
              minHeight: "200px",
              fontFamily: '"Courier New", monospace',
              fontSize: "13px",
            },
          })
        );
      }
  
      renderChevron() {
        return h(
          "svg",
          {
            className: "cpel-chevron open",
            viewBox: "0 0 20 20",
            width: 20,
            height: 20,
          },
          h("path", {
            d: "M5 6L10 11L15 6L17 7L10 14L3 7L5 6Z",
            fill: "#9CA1A8",
          })
        );
      }
  
      renderDesktopIcon() {
        return h(
          "svg",
          {
            width: 20,
            height: 20,
            viewBox: "0 0 20 20",
            fill: "none",
          },
          h("path", {
            fillRule: "evenodd",
            clipRule: "evenodd",
            d: "M3 2H17C17.55 2 18 2.45 18 3V13C18 13.55 17.55 14 17 14H12V16H14C14.55 16 15 16.45 15 17V18H5V17C5 16.45 5.45 16 6 16H8V14H3C2.45 14 2 13.55 2 13V3C2 2.45 2.45 2 3 2ZM16 11V4H4V11H16Z",
            fill: "#1D2327",
          })
        );
      }
  
      renderMobileIcon() {
        return h(
          "svg",
          {
            width: 20,
            height: 20,
            viewBox: "0 0 20 20",
            fill: "none",
          },
          h("path", {
            fillRule: "evenodd",
            clipRule: "evenodd",
            d: "M6 2H14C14.55 2 15 2.45 15 3V17C15 17.55 14.55 18 14 18H6C5.45 18 5 17.55 5 17V3C5 2.45 5.45 2 6 2ZM13 14V4H7V14H13Z",
            fill: "#1D2327",
          })
        );
      }
    }
  
    /**
     * Initialize the App
     */
    document.addEventListener("DOMContentLoaded", function () {
      const root = document.getElementById("cpel-floater-app-root");
      
      if (root && typeof wp !== "undefined" && wp.element) {
        const { render, createElement: h } = wp.element;
        render(h(FloaterApp), root);
      }
    });
  })();