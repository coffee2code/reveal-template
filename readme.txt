=== Reveal Template ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: template, theme, debug, presentation, template, design, widget, shortcode, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.8
Stable tag: 3.7

Reveal the theme template file used to render the displayed page, via the admin bar, footer, widget, shortcode, and/or template tag.


== Description ==

Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being utilized to render the currently displayed page in WordPress. Sometimes page or category specific templates exist, or a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain templates causing WordPress to fall back to others.

This plugin relieves that aggravation by assisting designers and developers by displaying the template being used to render the currently displayed page in WordPress. This can be shown in one or more of the following methods:

= Admin bar =

By default, the theme-relative path to the theme template file used to render the page is shown in the admin bar. The settings page for the plugin, at 'Design' -> 'Reveal Template', allows you to control and configure this particular method. Note: even if enabled by the setting, the admin bar entry also only appears if the admin bar is showing and if the user has the ability to see the revealed template.

= Site footer =

By default, the theme-relative path to the theme template file used to render the page is shown in the site's footer. The settings page for the plugin, at 'Design' -> 'Reveal Template', allows you to control and configure this particular method. Note: even if enabled by the setting, the footer output only appears if the theme follows the recommended practice of calling the `wp_footer()` template tag at some point and the user has the ability to see the revealed template.

= Widget =

A widget named "Reveal Template" is made available which can be used to display the theme template file used to render the page in any widget location.

= Shortcode =

A shortcode called 'revealtemplate' is made available which can be used in post/page content to display the theme template file used to render the page. Currently it supports two possible attributes:

* 'admin' : Can either be 1 or 0 to indicate if the template name should be revealed to admins only. 1 means to admins only, 0 to all visitors. The default is 1.
* 'type' : The template path types. Must be one of 'absolute', 'filename', 'relative', or 'theme-relative'. Read the documentation for more information on what each of these mean.

Examples: `[revealtemplate type="absolute"]`, `[revealtemplate type="filename" admin="0"]`

= Template Tag =

A template tag is also provided which can be used to display the theme template file used to render the page.

`<?php c2c_reveal_template(); ?>`

By default, `c2c_reveal_template()` will echo the template name. To simply retrieve the template filename rather than displaying it:

`<?php $template = c2c_reveal_template( false ); ?>`

The template tag also takes a second argument which be can be one of the following: absolute, relative, theme-relative, filename. This determines the path style you'd like reported. If not specified, it uses the default defined in the plugin's settings page.

Examples of path types:

* "absolute" : /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php
* "relative" : wp-content/themes/yourtheme/single.php
* "theme-relative" : yourtheme/single.php
* "filename" : single.php

This plugin is primarily intended to be activated on an as-needed basis.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/reveal-template/) | [Plugin Directory Page](https://wordpress.org/plugins/reveal-template/) | [GitHub](https://github.com/coffee2code/reveal-template/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally customize the plugin's settings by clicking the plugin's 'Settings' link next to its 'Deactivate' link (still on the Plugins page), or click on the 'Design' -> 'Reveal Template' link, to go to the plugin's admin settings page. Or use the 'Reveal Template' widget. Or use the shortcode.


== Screenshots ==

1. The plugin's settings page.
2. The 'Reveal Template' widget.
3. The admin bar entry.


== Template Tags ==

The plugin provides one template tag for use in your theme templates, functions.php, or plugins.

= Functions =

* `<?php function c2c_reveal_template( $echo = true, $template_path_type = '' ) ?>`
Formats for output the template path info for the currently rendered template.

= Arguments =

* `$echo` (bool)
Optional. Echo the template info? Default is true.

* `$template_path_type` (string)
Optional. The style of the template's path for return. Accepts: 'absolute', 'relative', 'theme-relative', 'filename'. Default is '', which causes the function to use the template path type configured via the plugin's settings page.

= Examples =

* `<?php //Output the current template
c2c_reveal_template( true, 'theme-relative' );
?>`

* `<?php // Retrieve the value for use in code, so don't display/echo it.
$current_template = c2c_reveal_template( false, 'filename' );
if ( $current_template == 'category-12.php' ) {
   // Do something here
}
?>`


== Changelog ==

= 3.7 (2021-11-14) =
Highlights:

This minor release updates the plugin framework, notes compatibility through WP 5.8+, and reorganizes unit tests.

Details:

* Change: Note compatibility through WP 5.8+
* Change: Update plugin framework to 064
    * 064:
    * New: For checkbox settings, support a 'more_help' config option for defining help text to appear below checkbox and its label
    * Fix: Fix URL for plugin listing donate link
    * Change: Store donation URL as object variable
    * Change: Update strings used for settings page donation link
    * 063:
    * Fix: Simplify settings initialization to prevent conflicts with other plugins
    * Change: Remove ability to detect plugin settings page before current screen is set, as it is no longer needed
    * Change: Enqueue thickbox during `'admin_enqueue_scripts'` action instead of during `'init'`
    * Change: Use `is_plugin_admin_page()` in `help_tabs()` instead of reproducing its functionality
    * Change: Trigger a debugging warning if `is_plugin_admin_page()` is used before `'admin_init'` action is fired
    * 062:
    * Change: Update `is_plugin_admin_page()` to use `get_current_screen()` when available
    * Change: Actually prevent object cloning and unserialization by throwing an error
    * Change: Check that there is a current screen before attempting to access its property
    * Change: Remove 'type' attribute from `style` tag
    * Change: Incorporate commonly defined styling for inline_textarea
* Change: Tweak installation instruction
* Unit tests:
    * Change: Restructure unit test file structure
        * Change: Move `tests/` into `tests/phpunit/tests/`
        * Change: Move `tests/bootstrap.php` to `tests/phpunit/`
        * Change: Move `bin/` into `phpunit/`
        * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file
        * Change: In bootstrap, check for test installation in more places and exit with error message if not found
        * Change: Remove 'test-' prefix from unit test files
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Tweak some formatting in TODO.md
* New: Add a few more possible TODO items

= 3.6.1 (2021-04-14) =
* Fix: Update plugin framework to 061 to fix a bug preventing settings from getting saved.

= 3.6 (2021-04-01) =
Highlights:

* This minor release adds support for the 'privacypolicy' template, updates the plugin framework, improves some inline and UI help text, and notes compatibility through WP 5.7+.

Details:

* New: Add support for 'privacypolicy' template
* Change: Update plugin framework to 060
    * 060:
    * Rename class from `c2c_{PluginName}_Plugin_051` to `c2c_Plugin_060`
    * Move string translation handling into inheriting class making the plugin framework code plugin-agnostic
        * Add abstract function `get_c2c_string()` as a getter for translated strings
        * Replace all existing string usage with calls to `get_c2c_string()`
    * Handle WordPress's deprecation of the use of the term "whitelist"
        * Change: Rename `whitelist_options()` to `allowed_options()`
        * Change: Use `add_allowed_options()` instead of deprecated `add_option_whitelist()` for WP 5.5+
        * Change: Hook `allowed_options` filter instead of deprecated `whitelist_options` for WP 5.5+
    * New: Add initial unit tests (currently just covering `is_wp_version_cmp()` and `get_c2c_string()`)
    * Add `is_wp_version_cmp()` as a utility to compare current WP version against a given WP version
    * Refactor `contextual_help()` to be easier to read, and correct function docblocks
    * Don't translate urlencoded donation email body text
    * Add inline comments for translators to clarify purpose of placeholders
    * Change PHP package name (make it singular)
    * Tweak inline function description
    * Note compatibility through WP 5.7+
    * Update copyright date (2021)
    * 051:
    * Allow setting integer input value to include commas
    * Use `number_format_i18n()` to format integer value within input field
    * Update link to coffee2code.com to be HTTPS
    * Update `readme_url()` to refer to plugin's readme.txt on plugins.svn.wordpress.org
    * Remove defunct line of code
* Change: Move translation of all parent class strings into main plugin file
* Hardening: Escape URLs before being displayed
* Change: Include mention of the template also being revealed in the admin bar by default
* Change: Refactor how setting page explanatory text is output
* Change: Tweak help text for display_in_footer setting to reflect additional display methods
* Change: Fix typos in readme
* Change: Note compatibility through WP 5.7+
* Change: Update copyright date (2021)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/reveal-template/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 3.7 =
Minor release: updated plugin framework, noted compatibility through WP 5.8+, and reorganized unit tests.

= 3.6.1 =
Bugfix release: fixed a bug preventing settings from getting saved; updated plugin framework to v061

= 3.6 =
Minor release: added support for the 'privacypolicy' template, updated plugin framework, improved some inline and UI help text, noted compatibility through WP 5.7+, and updated copyright date (2021).

= 3.5 =
Feature release: added admin bar entry to reveal the current template, updated plugin framework, added TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, and updated compatibility to be WP 4.9-5.4+.

= 3.4.2 =
Trivial update: noted compatibility through WP 5.3+ and updated copyright date (2020)

= 3.4.1 =
Trivial update: minor change in `c2c_reveal_template()` to return an empty string where a null had been returned, modernized unit tests, noted compatibility through WP 5.2+

= 3.4 =
Minor update: tweaked plugin initialization, fixed some unit tests and added more, updates plugin framework to version 048, noted compatibility through WP 5.1+, created CHANGELOG.md to store historical changelog outside of readme.txt, and updated copyright date (2019)

= 3.3 =
Recommended update: adds support for 'embed' and 'singular' templates, fixes recognition of the front page template, updates plugin framework to version 046; compatibility is now with WP 4.7-4.9+; updated copyright date (2018).

= 3.2 =
Minor update: improved support for localization; minor widget code changes; verified compatibility through WP 4.4; dropped compatibility with WP older than 4.1; updated copyright date (2016)

= 3.1.1 =
Minor bugfix update: Prevented PHP notice under PHP7+ for widget; noted compatibility through WP 4.3+

= 3.1 =
Minor update: added more unit tests; updated plugin framework to 039; update widget framework to 009; noted compatibility through WP 4.1+; added plugin icon

= 3.0 =
Major update: added widget, shortcode, and unit tests; changed default initialization; removed deprecated reveal_template() template tag; compatibility now for WP 3.6-3.8+. Potential for incompatibility if the plugin is used by other code. (Normal users won't be affected.)

= 2.3 =
Recommended update. Highlights: only show in footer for admins; added support for 'front_page' and 'index' templates; updated plugin framework; noted WP 3.5+ compatibility; and more.

= 2.2 =
Recommended update. Highlights: updated plugin framework; noted compatibility with WP 3.3+; dropped compatibility with versions of WP older than 3.1.

= 2.1 =
Recommended update.  Noted WP 3.2 compatibility; dropped support for versions of WP older than 3.0; updated plugin framework; deprecate global variable.

= 2.0.4 =
Bugfix release: fixed bug with theme-relative template path output showing parent theme path instead of child theme path

= 2.0.3 =
Minor release: updated underlying plugin framework; noted compatibility with WP 3.1+ and updated copyright date.

= 2.0.2 =
Minor update.  Updated plugin framework to latest version (017).

= 2.0.1 =
Minor update.  Fixed and expanded readme.txt.  Updated plugin framework to latest version (016).

= 2.0 =
Recommended update. Highlights: re-implementation; full localization support; deprecated reveal_template() (use c2c_reveal_template() instead); misc non-functionality changes; verified WP 3.0 compatibility.
