=== Reveal Template ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: template, theme, debug, presentation, template, design, coffee2code
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 2.0
Version: 2.0

Reveal the theme template file used to render the displayed page, via the footer and/or template tag.


== Description ==

Reveal the theme template file used to render the displayed page, via the footer and/or template tag.

Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being utilized to render the currently displayed page in WordPress.  Sometimes page or category specific templates exist, or a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain templates causing WordPress to fall back to others.

This plugin relieves the aggravation by assisting designers and developers by displaying the template being used to render the currently displayed page in WordPress.  This typically appears in the site's footer (though only if the theme follows the recommended practice of calling the wp_footer() template tag) at some point.  Also, obviously this can only universally apply to the site if said footer is actually used on every page.

A template tag is also provided which can be used to display the template file.

`<?php reveal_template(); ?>`

By default, reveal_template() will echo the template name.  To simply retrieve the template filename rather than displaying it:

`<?php $template = reveal_template(false); ?>`

The template tag also takes a second argument which be can be one of the following: absolute, relative, template-relative, filename.  This determines the path style you'd like reported.  If not specified, it uses the default defined in the plugin's settings page.

Examples of path types:

* "absolute" : /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php
* "relative" : wp-content/themes/yourtheme/single.php
* "template-relative" : yourtheme/single.php
* "filename" : single.php

This plugin is primarily intended to be activated on an as-needed basis.


== Installation ==

1. Unzip `reveal-template.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally customize the plugin's settings by click the plugin's 'Settings' link next to its 'Deactivate' link (still on the Plugins page), or click on the'Design' -> 'Reveal Template' link, to go to the plugin's admin settings page.


== Changelog ==

= 2.0 =
* Re-implementation by extending C2C_Plugin_013, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Full localization support
* Add c2c_reveal_template()
* Deprecate reveal_template() in favor of c2c_reveal_template() (but retain for backward compatibility)
* Rename class from 'RevealTemplate' to 'c2c_RevealTemplate'
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Change description
* Add package info to top of plugin file
* Add PHPDoc documentation
* Note compatibility with WP 2.9+, 3.0+
* Drop support for versions of WP older than 2.8
* Minor tweaks to code formatting (spacing)
* Add Changelog and Upgrade Notice sections to readme.txt
* Update copyright date
* Remove trailing whitespace in header docs
* Update screenshot
* Add .pot file


= 1.0.1 =
* Check for 'manage_options' instead of 'edit_posts' permission in order to edit settings
* Use plugins_url() instead of hard-coding path
* Tweak readme tags and donate link
* Note compatibility with WP 2.8+

= 1.0 =
* Initial release


== Upgrade Notice ==

= 2.0 =
Recommended update. Highlights: re-implementation; full localization support; deprecated reveal_template() (use c2c_reveal_template() instead); misc non-functionality changes; verified WP 3.0 compatibility.