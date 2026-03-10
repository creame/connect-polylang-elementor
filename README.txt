=== Connect Polylang for Elementor ===
Contributors: creapuntome, pacotole, daveshine, narinder-singh, p4fbradjohnson
Donate link: https://github.com/sponsors/creame
Tags: elementor, polylang, multilingual, language switcher, dynamic tags
Requires at least: 5.4
Tested up to: 6.9
Requires PHP: 5.6
Stable tag: 2.5.5
License: GPL-2.0-or-later
License URI: https://opensource.org/licenses/GPL-2.0

Connect Polylang with Elementor: translated templates, language switcher widget, language visibility conditions and more

== Description ==

Connect Polylang multilingual plugin with Elementor Page Builder: **Translate your Elementor templates** and show them in the correct language, native customizable **Language Switcher Elementor widget**, **Visibility Conditions** of widgets and **Dynamic Tags** by language and much more…

### What the Plugin Does

#### 📍 Template translation and show in the correct language
Create translations for your Elementor templates especially for header, footer or archive templates with [**Elementor Pro Theme Builder**](https://be.elementor.com/visit/?bta=61741&brand=elementor&utm_campaign=wporg) *(affiliated link)*.

* Automatically selects the correct translation for ["Template Widget"](https://elementor.com/help/template-widget-pro/).
* Automatically selects the correct translation for `[elementor-template id="1234"]` shortcodes.
* Allow Elementor Kit translation for different settings for each language.

#### 🔄 Language Switcher
A native Elementor Widget to easily build a nice and fully customizable language switcher:

* Show or hide flags, language name, language code.
* Vertical list, Horizontal list or **styled dropdown**.
* **SVG scalable flags**.

https://www.youtube.com/watch?v=7DUh-bggJBs&feature=youtu.be


#### 👁️ Language Visibility Conditions
Allow to **show or hide every widget**, section or column **by language**.

#### 🏷️ Language Dynamic Tags
Use language dynamic tags (on Elementor Pro) that you can set with an specific language or the "current" one. Available tags are:

* **Language Code** tag type text
* **Language Name** tag type text
* **Language Url** tag type url
* **Language Flag** tag type image

#### ✏️ Elementor Editor integration
Now you can view the language of the current template and change to its translations or create them **from the Elementor editor**.

#### 🔍 Elementor Finder integration
From Elementor Finder you can access to Polylang settings pages and go the site in the selected language.

#### 🔨 Plugins integration fixes and tweaks

* Automatically generate display conditions on new template translation.
* Automatically generate Elementor CSS styles on create new template translation.
* Fix home url to the current language on site-url Dynamic Tag and Search Form widget.
* Add language url trailing slash.
* Hide language on wp-admin for global widgets.
* Other integration fixes.


[AI-powered translation for Polylang](https://coolplugins.net/product/autopoly-ai-translation-for-polylang/?ref=creame&utm_source=cpel_plugin&utm_medium=readme&utm_campaign=get_pro&utm_content=bottom_link) — AutoPoly instantly translates your Elementor, Gutenberg, and ACF content in one click while keeping your design exactly the same. [Check it now (affiliated link)](https://coolplugins.net/product/autopoly-ai-translation-for-polylang/?ref=creame&utm_source=cpel_plugin&utm_medium=readme&utm_campaign=get_pro&utm_content=bottom_link)


### How to use
You can manage translated templates in two ways:

* **(recommended)** create linked translations for a template and only set display conditions on the default language.
* create separated translations for a template with their own display conditions.

See an example:

`
Site languages:
 - EN (main)
 - ES
 - IT

- Option 1 (linked translations only main language has display conditions):
╔═ Archive Template A (EN) -> condition category is "Red (EN)"
╠═ Archive Template A (ES) -> none (in front checks if category is ES translation of "Red (EN)")
╚═ Archive Template A (IT) -> none (in front checks if category is IT translation of "Red (EN)")

- Option 2 (not linked translations, all languages has display conditions):
══ Archive Template B (EN) -> condition category is "Red (EN)"
══ Archive Template B (ES) -> condition category is "Rojo (ES)"
══ Archive Template B (IT) -> condition category is "Rosso (IT)"
`

### Support
* [**Plugin's support forum**](https://wordpress.org/support/plugin/connect-polylang-elementor) here on WordPress.org
* [Github plugin repo](https://github.com/creame/connect-polylang-elementor)
* [Polylang User Community Group at Facebook](https://www.facebook.com/groups/polylang.users/)
* **What is covered by our support?** - All regarding **THIS plugin** ("Connect Polylang for Elementor"), plus the relation to *Polylang*, *Polylang Pro*, *Elementor* and *Elementor Pro* of course.
* **What is NOT covered by support?** - Anything general regarding multilingual topics and WordPress. Explicitely we **DO NOT** offer any "WPML", "WPBakery" or "Visual Composer" support, and also not for your theme etc.!!!

### Translations
We have used the strings of Elementor and Polylang whenever possible to take advantage of the fact that they are translated into many languages. For the other strings a `.pot` file (`connect-polylang-elementor.pot`) for translators is also always included :)

You can collaborate with your language translations in [Translating WordPress](https://translate.wordpress.org/projects/wp-plugins/connect-polylang-elementor)

### Credits
The following code/classes are licensed under the GPL.

* v2. refactor and upgrade by [Pacotole](https://profiles.wordpress.org/pacotole/) at [Creame](https://crea.me)
* v2.1 Cross domain fixes based on work of [Jory Hogeveen](https://profiles.wordpress.org/keraweb/) and [nicmare](https://github.com/nicmare)
* Support [Brad Johnson](https://profiles.wordpress.org/p4fbradjohnson/)
* v1. released by [David Decker](https://profiles.wordpress.org/daveshine/)
* v1. Polylang Switcher class (Elementor Widget) and its CSS based on widget from plugin "Language Switcher for Elementor" by Solitweb (GPLv2 or later)
* SVG flags from [FlagKit](https://github.com/madebybowtie/FlagKit) and [Wikipedia](https://wikipedia.org/)

Please, send your suggestions and feedback - Thank you for using or trying out this plugin!



== Installation ==

= Minimum Requirements =

* WordPress version 4.7 or higher
* [Elementor](https://wordpress.org/plugins/elementor/) and [Polylang](https://wordpress.org/plugins/polylang/) plugins - free versions from WordPress.org Plugin Directory
* **Recommended:** [**Elementor Pro**](https://be.elementor.com/visit/?bta=61741&brand=elementor&utm_campaign=wporg) *(affiliate link)* which is needed for Theme Building possibilities (header, footer, 404, archive templates, etc.) and Dynamic Tags.

= Installation =

1. Install using the WordPress built-in Plugin installer (via **Plugins > Add New** - search for `connect polylang elementor`), or extract the ZIP file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
4. Assign languages to your pages/posts/Elementor templates
5. For Elementor templates: set display conditions in Elementor for the main language template only, templates in the other languages will then get loaded via this plugin magically! :)
6. Additionally use the native Elementor Widget: Language Switcher



== Frequently Asked Questions ==

= Recommended Workflow? =

1. Setup Polylang at first, add at least TWO languages, one of them make your default language (in Polylang)
2. Add content to your default Polylang language - if Polylang asks you to apply existing content to it, you should most likely click "ok" and proceed - it works really fine :)
3. After that setup an Elementor Theme Builder template - assign it to your Polylang default language, apply conditions in Elementor
4. Next, setup a translation template for the template of 3.) -- assign your second Polylang language, DO NOT apply conditions in Elementor


= Where is the Elementor Widget to be found? =
It's in the widget category "General Elements" with the name **"Language switcher"**. Plus, if Elementor Pro is active and you're editing a template, the widget additionally appears in the "Site" widget category.

*Always via search:* When searching for widgets type "polylang" or "languages" and it will show up immediately! ;-)


= Where is the plugin's settings page? =
This plugin has NO settings page, as it does not need one. All it does works just under the hood. Activate the plugin. Done.


= Is Elementor Pro required? =
Technically, Elementor Pro is not required **but highly recommended**.

[**Elementor Pro**](https://be.elementor.com/visit/?bta=61741&brand=elementor&utm_campaign=wporg) *(affiliate link)* is required for these features of the plugin:

* Translate Elementor Theme Builder Templates (header, footer, 404, page, archives).
* Translate Elementor Popups.
* Dynamic Tags, additionally added by the plugin.

For the other features of the plugin the free version of Elementor is sufficient - so the native Elementor language switcher widget will work ;-)


= Is Polylang Pro required? =
Polylang Pro is not required.

All features of "Connect Polylang for Elementor" work with both, *Polylang* and *Polylang Pro*.


= Are custom flags supported? =
In general, [custom flags](https://polylang.pro/doc/can-i-use-my-own-flags-for-the-language-switcher/) are supported in the Polylang Switcher Elementor widget and in the Dynamic Tag (Current Language Flag).
However, the default flags in Polylang are sized `16px` wide and `11px` high, this automatically applies to custom flags - as it is fully handled internally by Polylang.

To use a different size for custom flags we are trying to find ways to implement this for Elementor in future versions of this plugin.


= Typography and uppercase settings? =
In the Polylang Switcher Elementor widget there are typography settings available for the Switcher menu items. These settings are CSS based of course, and applied "globally" to the various states they are for: normal, hover, active (current language). The available toggles for uppercase are done code-wise and therefore have a lower priority - this means the CSS always takes over, if wanted. That way you are most flexible with quick settings (toggles) but have all styling options at hand if needed.


= Other recommended plugins for multilingual websites? =
There are quite a few:

* [**Polylang Pro** (Premium)](https://polylang.pro/downloads/polylang-pro/) - The official premium version with more features, plus premium support
* [**Polylang for WooCommerce** (Premium)](https://polylang.pro/downloads/polylang-for-woocommerce/) - Makes WooCommerce multilingual - official Polylang Add-On
* [**Polylang Slug**](https://github.com/grappler/polylang-slug) - Allows same slug for multiple languages in Polylang
* [**Lingotek Translation**](https://wordpress.org/plugins/lingotek-translation/) - Native Polylang integration - Lingotek brings convenient cloud-based localization and translation for WordPress
* [**Elementor Datepicker Localization**](https://github.com/creame/elementor-datepicker-localization) - Allow form datetime fields in your site language


= How can I translate Polylang pages automatically? =
Use [AutoPoly ](https://coolplugins.net/product/autopoly-ai-translation-for-polylang/?ref=creame&utm_source=cpel_plugin&utm_medium=readme&utm_campaign=get_pro&utm_content=bottom_link) to auto-translate your Elementor and Gutenberg pages with AI in one click. [Check it now (affiliated link)](https://coolplugins.net/product/autopoly-ai-translation-for-polylang/?ref=creame&utm_source=cpel_plugin&utm_medium=readme&utm_campaign=get_pro&utm_content=bottom_link)

= Where do I report security bugs found in this plugin? =
Please report security bugs found in the source code of the undefined plugin through the [Patchstack Vulnerability Disclosure  Program](https://patchstack.com/database/vdp/ad44f834-19c4-470b-860f-d19657e7902a). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

== Screenshots ==
1. Add the language switcher in Elementor and customize its appearance.
2. Create and link translated pages directly inside Elementor.
3. Preview how the Polylang–Elementor language switcher looks on the frontend.

== Changelog ==

= 2.5.5 =
* Tweaks: Enhanced editor language switcher control
* Tweaks: Remove load_plugin_textdomain
* Compatibility: Tested with Elementor 3.34.0

= 2.5.4 =
* Added: Registered with Patchstack Vulnerability Disclosure Program
* Added: New screenshots
* Tweaks: Added affiliated links.
* Tweaks: Minor text improvements.
* Compatibility: Tested with WordPress 6.9, Elementor v3.33.2 and Polylang v3.7.5.
* Tweaks: Updated readme video and made small textual adjustments.
* Removed: Old screenshots and outdated links

= 2.5.3 =
* Fix language visibility don't work with cache.

= 2.5.2 =
* Fix SVG flags use base64 encode.

= 2.5.1 =
* Fix New Search widget (Elem. >= 3.24) use correct search url (thanks to [@giovanni-caprilli](https://github.com/giovanni-caprilli))

= 2.5.0 =
* **NEW editor language switcher controls** for Elementor Editor Top Bar 3.25.0 and later (not v4).
Now can switch between pages & templates translations from "Page Settings" and in settings panel, on "Languages" section.
This utilizes Elementor 3.25.0's updated approach to managing Page Settings Panel Controls.
(thanks to [@narinder9](https://github.com/narinder9) for the [main work](https://github.com/creame/connect-polylang-elementor/pull/21)).
* Fix Language Switcher controls in page settings not working in Elementor 3.25.0 and greater.
* Bump tested versions WordPress 6.8 & Elementor 3.30.2

= 2.4.7 =
* Quick fix for fatal error with Polylang >=3.7 [Issue](https://wordpress.org/support/topic/critical-error-on-this-website-due-to-plugin-conflict/) (thanks to [@narinder9](https://github.com/narinder9)
 for the [fix](https://github.com/creame/connect-polylang-elementor/commit/1d0d20a60ee8ac8c9486f5fcc03e28134769033c)).

= 2.4.6 =
* Bump tested versions WordPress 6.7 & Elementor 3.27.

= 2.4.5 =
* Bump tested versions WordPress 6.6 & Elementor 3.24.
* NEW Elementor Pro custom Thank You page for WooCommerce translation.

= 2.4.4 =
* NEW WordPress Widget "Elementor Library" translation (suggested by [@lennertdaniels](https://github.com/creame/connect-polylang-elementor/issues/14#issue-1970978478)).
* Fix PHP errors (thanks to [ChristianSchaffner](https://github.com/creame/connect-polylang-elementor/issues/16#issue-2044749726) and [brandon-hatch](https://github.com/creame/connect-polylang-elementor/issues/17#issue-2102975661)).
* Fix remove deprecated Elementor's classes.

= 2.4.3 =
* Automatically generate CSS file on bulk translation (thanks to @Elisa3232).
* Fix PHP error when PLL()->curlang not defined (thanks to @timsgr).

= 2.4.2 =
* Fix deprecation notices for Polylang 3.4.
* Bump tested versions WordPress 6.3 & Elementor 3.13.

= 2.4.1 =
* Fix plugin submission to wordpress.org.

= 2.4.0 =
* NEW language visibility conditions now also works for Flexbox containers.
* NEW Flag emojis on Elementor Editor and Theme Builder.
* Change languages styles on Elementor Editor.
* Change code improvements.

= 2.3.5 =
* NEW Elementor Widget Template translation.
* Change better languages links on Elementor Editor.

= 2.3.4 =
* Fix Elementor 3.9 hard deprecations.
* Fix JetMenu with domains as language (thanks to @mortenitp).

= 2.3.3 =
* Fix PHP warning on multidomain image undefined srcset.
* Bump tested versions WordPress 6.1 & Elementor 3.8.

= 2.3.2 =
* Fix dynamic tag image language flag don't return svg scalable file.
* Fix Language Switcher dropdown styles with many languages.
* Fix PHP error undefined array key "HTTP_REFERER" (thanks to @nicomollet).

= 2.3.1 =
* Fix Widget initialization on Elementor 3.7

= 2.3.0 =
* Added dropdown controls to show on mouse hover and drop to up.
* Added Elementor Kit translation support.

= 2.2.0 =
* Added Elementor Pro template shortcode translation.

= 2.1.1 =
* Enable Elementor cross domain url fixes only for multidomain configurations.
* Added Elementor tested headers.

= 2.1.0 =
* Fixed cross domain issues with Polylang setup with multiple domains.
* Fix flag SVG inline with some themes.

= 2.0.8 =
* Don't load integrations if Polylang languages are not defined.

= 2.0.7 =
* Fixed deprecated message with Elementor 3.6.
* More restricted filter for lang home urls with trailing slash.

= 2.0.6 =
* Fixed Elementor Search Form url with correct language.

= 2.0.5 =
* Use Polylang custom flags.

= 2.0.4 =
* Added better info of template instances for translations on Theme Editor and WP admin list.
* Minor tweaks on Language Switcher dropdown styles.

= 2.0.3 =
* Updated language switcher dropdown animated & toggle on mobile.
* Added Elementor icon in posts list status.
* Fixed language switcher underline style.
* Fixed SVG flags if encoded is disabled.
* SVG flags data URIs don't need base64 and save some bytes.

= 2.0.2 =
Fixed fatal error if call home_url() before initialize Polylang.

= 2.0.1 =
Fixed critical error loading plugin classes when there are uppercase letters in the path.

= 2.0.0 =
**Fully rewrited and upgraded!!**

* New language switcher with SVG scalable flags and dropdown style.
* New language visibility conditions for widgets, sections and columns.
* New dynamic tags for language name, language code, language flag (icon or svg scalable) and language url.
* Better Polylang/Elementor integration:
  * Automatically generate display conditions on new template translation.
  * Automatically generate CSS file on new template translation.
  * Update display conditions un change template language.
  * Fix home and search links to point to the current language.
  * Hide langue on wp-admin for global widgets.

= 1.0.6 - 2021-06-05 =
* Fixed PHP notice undefined 'post_type'.

= 1.0.5 - 2021-05-17 =
* Fixed Elementor editor don't load with Global Widgets in secondary language.

= 1.0.4 - 2021-04-30 =
* Translated templates with conditions for categories/tags also works with translated categories/tags.
* Fixed PHP notice on activation

= 1.0.3 - 2021-04-27 =
* Ensure Theme Builder conditions for all languages (prev. version only saves main language conditions)
* When a template is a translation override with empty conditions

= 1.0.2 - 2021-04-26 =
* Fix Elementor template display conditions reset
* Fix wp-cli error languages undefined
* Fix Elementor deprecations
* Added Elementor Landing Pages CPT translatable
* Added JetEngine Listing CPT translatable
* WordPress PHP Coding Standards

= 1.0.1 - 2020-06-17 =
* Stability update for edge cases were experiencing fatal errors
* Thanks to @sebastienserre for correct fatal unknow pll_the_languages();

= 1.0.0 - 2018-11-28 =
* Official public release on WordPress.org

= 0.9.1 - 2018-11-27 =
* *First Release Candidate (RC) version*
* New: Automatic enabling of Elementor My Templates post type for Polylang support
* Tweak: Code improvements throughout
* Tweak: Inline documentation and PHP doc improvements

= 0.9.0 - 2018-11-26 =
* *Second beta version*
* New: More settings for Polylang Switcher widget
* Tweak: Improved Dynamic Tags additions
* Tweak: Improved Elementor Finder integration

= 0.8.0 - 2018-11-25 =
* *First beta version*
* New: Added native Elementor Widget - Polylang Language Switcher
* New: Added Dynamic Tags for Polylang (requires Elementor Pro)

= 0.7.0 - 2018-11-24 =
* *Second alpha version*
* New: Added Elementor Finder integration for Polylang plugin links and resources
* New: Make plugin translatable - added German translations
* New: Added Readme file, plus `composer.json`
* New: First public alpha release on GitHub

= 0.5.0 - 2018-10-30 =
* *Plugin idea by Brad*
* *First alpha version by David*
* New: Template tweaks work in form of plugin code - coding standards improvements


== Upgrade Notice ==

= 2.0.4 =
**Warning** breaking changes with v1. Language Switcher and Dynamic Tags has been renamed and need to re-added again in your templates.

= 1.0.2 =
Fix Elementor template display conditions reset and other minor fixes.

= 1.0.1 =
A fix for edge users were in certain cases Elementor Pro would not load.

= 1.0.0 =
Just released into the wild.
