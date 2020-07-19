=== Reveal Template ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: template, theme, debug, presentation, template, design, widget, shortcode, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.4
Stable tag: 3.5

Reveal the theme template file used to render the displayed page, via the admin bar, footer, widget, shortcode, and/or template tag.


== Description ==

Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being utilized to render the currently displayed page in WordPress. Sometimes page or category specific templates exist, or a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain templates causing WordPress to fall back to others.

This plugin relieves that aggravation by assisting designers and developers by displaying the template being used to render the currently displayed page in WordPress. This can be shown in one or more of the following methods:

= Admin bar =

By default, the theme-relative path to the theme template file used to render the page is shown in the admin bar. The settings page for the plugin, at 'Design' -> 'Reveal Template', allows you to control and configure this particular method. Note: even if enabled by the setting, the admin bar entry also only appears if the admin bar is showing and if the user has the ability to see the revealed templated.

= Site footer =

By default, the theme-relative path to the theme template file used to render the page is shown in the site's footer. The settings page for the plugin, at 'Design' -> 'Reveal Template', allows you to control and configure this particular method. Note: even if enabled by the setting, the footer output only appears if the theme follows the recommended practice of calling the `wp_footer()` template tag) at some point and the user and the user has the ability to see the revealed template.

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
1. Unzip `reveal-template.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
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

= 3.5 (2020-07-18) =
Highlights:

* This feature release adds an admin bar entry to reveal the current template, updates the plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, and updates compatibility to be WP 4.9-5.4+.

Details:

* New: Add integration with admin bar
    * New: Add admin bar entry for revealing template
    * New: Add setting to control if admin bar entry should appear (defaulted to true)
    * New: Add `can_show_in_admin_bar()` for determining if the admin bar item should be shown
    * New: Add new screenshot
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add items to it)
* Fix: Correct typo on plugin settings page
* Change: Update plugin framework to 050
    * 050:
    * Allow a hash entry to literally have '0' as a value without being entirely omitted when saved
    * Output donation markup using `printf()` rather than using string concatenation
    * Update copyright date (2020)
    * Note compatibility through WP 5.4+
    * Drop compatibility with version of WP older than 4.9
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
* Change: Note compatibility through WP 5.4+
* Change: Drop compatibility with version of WP older than 4.9
* Change: Update links to coffee2code.com to be HTTPS
* Change: Update screenshot
* Unit tests:
    * New: Add tests for `register_filters()`, `options_page_description()`
    * New: Add test for setting name
    * Change: Store plugin instance in test object to simplify referencing it
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests

= 3.4.2 (2019-12-27) =
* New: Unit tests: Add test to verify plugin hooks `plugins_loaded` action to initialize itself
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

= 3.4.1 (2019-06-16) =
* Change: Return an empty string instead of null when the template path string shouldn't return anything
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Fix: Use explicit string value instead of referencing undefined variable
* Change: Note compatibility through WP 5.2+

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/reveal-template/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

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
