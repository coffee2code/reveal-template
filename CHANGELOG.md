# Changelog

## 3.5 _(2020-07-18)_

### Highlights:

This feature release adds an admin bar entry to reveal the current template, updates the plugin framework, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, and updates compatibility to be WP 4.9-5.4+.

### Details:

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

## 3.4.2 _(2019-12-27)_
* New: Unit tests: Add test to verify plugin hooks `plugins_loaded` action to initialize itself
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

## 3.4.1 _(2019-06-16)_
* Change: Return an empty string instead of null when the template path string shouldn't return anything
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Fix: Use explicit string value instead of referencing undefined variable
* Change: Note compatibility through WP 5.2+

## 3.4 _(2019-03-28)_

### Highlights:

* This release is a minor update that verifies compatibility through WordPress 5.1+ and makes minor behind-the-scenes improvements.

### Details:
* Change: Store setting name in class constant
* Change: Update plugin framework to 048
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
* Change: Update widget framework to 013
    * Add `get_config()` as a getter for config array
* Change: Update widget to 004
    * Update to use v013 of the widget framework
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Unit tests:
    * Fix: Use a different template as the directly assigned template to ensure it's one the unit test default theme has defined
    * Fix: Explicitly set 'twentyseventeen' as the theme to ensure testing against a known theme
    * New: Add unit test to ensure plugin doesn't save an option to database on activation
    * New: Add `set_option()` to facilitate setting of plugin options
    * New: Add unit test for setting defaults
    * New: Add a bunch of assertions for use of `reveal()` alongside uses of `c2c_reveal_template()`
    * Change: Improve unit test for deletion of option
* Change: Note compatibility through WP 5.1+
* Change: Add README.md link to plugin's page in Plugin Directory
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

## 3.3 _(2018-01-01)_

### Highlights:

* This release adds support for 'embed' and 'singular' templates, fixes recognition of the front page template, updates its plugin framework, and has some minor behind-the-scenes changes.

### Details:

* New: Add support for 'embed' and 'singular' templates
* Fix: Properly detect front page template
* Delete: Remove support for discontinued `comments_popup` template
* Change: Update plugin framework to 046
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`.
    * Note compatibility through WP 4.7+.
    * Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database.
    * 044:
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.
    * 043:
    * Disregard invalid lines supplied as part of hash option value.
    * 042:
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL.
    * Translate "Donate" in footer message.
* New: Add a unit test for uninstall
* New: Add README.md
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Drop compatibility with versions of WP older than 4.7
* Change: Update copyright date (2018)
* Change: Minor code reformatting
* Change: Minor dockblock reformatting

## 3.2 _(2016-03-15)_

### Highlights:

* This release adds support for language packs and has some minor behind-the-scenes changes.

### Details:

* Change: Update plugin framework to 041:
    * Change class name to `c2c_RevealTemplate_Plugin_041` to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Change admin page header from 'h2' to 'h1' tag.
    * Add `c2c_plugin_version()`.
    * Formatting improvements to inline docs.
* Change: Update widget to 003:
    * Explicitly declare `__construct()` public.
    * Add `register_widget()` and change to calling it when hooking `admin_init`.
    * Reformat config array.
* Change: Update widget framework to 012:
    * Go back to non-plugin-specific class name of c2c_Widget_012
    * Don't load textdomain
    * Declare class and `load_config()` and `widget_body()` as being abstract
    * Change class variable `$config` from public to protected
    * Discontinue use of `extract()`
    * Apply `widget_title` filter to widget title
    * Add more inline documentation
    * Minor code reformatting (spacing, bracing, Yoda-ify conditions)
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Add 'Text Domain' to plugin header.
    * Remove .pot file and /lang subdirectory.
* New: Add LICENSE file.
* New: Add empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Minor code reformatting.
* Change: Note compatibility through WP 4.4+.
* Change: Dropped compatibility with version of WP older than 4.1.
* Change: Update copyright date (2016).

## 3.1.1 _(2015-08-17)_
* Update: Discontinue use of PHP4-style constructor invocation of `WP_Widget` to prevent PHP notices in PHP7.
* Update: Update widget framework to 010.
* Update: Note compatibility through WP 4.3+.
* Add: Add unit test for widget version.

## 3.1 _(2015-02-20)_
* Update plugin framework to 039
* Update widget framework to 009
* Add more unit tests
* Explicitly declare `activation()` and `uninstall()` static
* Reformat plugin header
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 3.0 _(2013-12-28)_
* Add `Reveal Template` widget
* Add widget framework 008
* Add 'revealtemplate' shortcode
* Fix to recognize proper template names for hooks, `front_page` and `comments_popup` (they need underscores)
* Change arguments for object method `reveal()` (will break code using this method directly)
* Enhance `reveal()` with `format` args option to permit custom format string to be directly sent
* Enhance `reveal()` with `format_from_settings` args option to permit use of the format string defined via settings even if not being shown in footer
* Enhance `reveal()` with `admin_only` args option to control if output should be echoed for just admins or not
* Enhance `reveal()` with `return` args option to allow not returning a value for the function if the user isn't permitted to view the output
* Add optional `$args` argument to `c2c_reveal_template()` to feed into the identical arg for `reveal()`
* Add `reveal_to_current_user()` to contain logic for determining if the current user can be shown the template name/path
* Add `get_template_path_types()` to allow fetching of the recognized template path types and their descriptions
* Add `reveal_in_footer()` as function hooked to `wp_footer` (configures use of `reveal()` for the wp_footer context)
* Changed default template path type to 'theme-relative'
* Make class variable `$instance` private
* Add `get_instance()` class method to obtain singleton instance, creating one if it doesn't exist
* Add `get_default_template_path_type()` class method to obtain default template path type
* Remove long deprecated `reveal_template()`; use `c2c_reveal_template()` instead if you aren't already
* Update plugin framework to 036
* Add unit tests
* For `options_page_description()`, match method signature of parent class
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Add banner
* Add 'Screenshots' section to readme.txt
* Add screenshot of widget
* Updated existing screenshot
* Regenerate .pot
* Numerous readme.txt text and formatting tweaks
* Change donate link

## 2.3
* When set to echo or display in footer, only do so for logged in users with the `update_themes` capability
* Recognize 'frontpage' and 'index' templates
* Fix recognition of 'commentspopup' template
* Update plugin framework to 035
* Discontinue use of explicit pass-by-reference for objects
* Add check to prevent execution of code if file is directly accessed
* Regenerate .pot
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Minor code reformatting (spacing)
* Remove ending PHP close tag
* Move screenshot into repo's assets directory

## 2.2
* Update plugin framework to 031
* Remove support for `c2c_reveal_template` global
* Note compatibility through WP 3.3+
* Drop support for versions of WP older than 3.1
* Move .pot into lang/
* Regenerate .pot
* Add 'Domain Path' directive to top of main plugin file
* Update screenshot for WP 3.3
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 2.1
* Update plugin framework to v023
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $c2c_reveal_template to store instance
* Explicitly declare all functions as public
* Add `__construct()`, `activation()`, and `uninstall()`
* Note compatibility through WP 3.2+
* Drop compatibility with versions of WP older than 3.0
* Minor code formatting changes (spacing)
* Add plugin homepage and author links in description in readme.txt

## 2.0.4
* Fix bug with theme-relative template path output showing parent theme path instead of child theme path

## 2.0.3
* Update plugin framework to version 021
* Explicitly declare all class functions public
* Delete plugin options upon uninstallation
* Note compatibility through WP 3.1+
* Update copyright date (2011)

## 2.0.2
* Update plugin framework to version 017

## 2.0.1
* Update plugin framework to version 016
* Fix template tag name references in readme.txt to use renamed function name
* Add Template Tags section to readme.txt

## 2.0
* Re-implementation by extending `C2C_Plugin_013`, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Full localization support
* Add `c2c_reveal_template()`
* Deprecate `reveal_template()` in favor of `c2c_reveal_template()` (but retain for backward compatibility)
* Rename class from `RevealTemplate` to `c2c_RevealTemplate`
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

## 1.0.1
* Check for `manage_options` instead of `edit_posts` permission in order to edit settings
* Use `plugins_url()` instead of hard-coding path
* Tweak readme tags and donate link
* Note compatibility with WP 2.8+

## 1.0
* Initial release
