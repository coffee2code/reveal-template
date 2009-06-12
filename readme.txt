=== Reveal Template ===
Contributors: Scott Reilly
Donate link: http://coffee2code.com/donate
Tags: template, theme, debug, presentation, coffee2code
Requires at least: 2.6
Tested up to: 2.8
Stable tag: 1.0.1
Version: 1.0.1

Reveal the theme template file used to render the displayed page (for debugging purposes).

== Description ==

Reveal the theme template file used to render the displayed page (for debugging purposes).  By default this appears in the site's footer.

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

1. Unzip `reveal-template.zip` inside the `/wp-content/plugins/` directory for your site
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally customize the plugin's settings by click the plugin's 'Settings' link next to its 'Deactivate' link (still on the Plugins page), or click on the'Design' -> 'Reveal Template' link, to go to the plugin's admin settings page.

