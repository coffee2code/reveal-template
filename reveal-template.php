<?php
/*
Plugin Name: Reveal Template
Version: 0.9
Plugin URI: http://coffee2code.com/wp-plugins/reveal-template
Author: Scott Reilly
Author URI: http://www.coffee2code.com
Description: Reveal the theme template used to render the displayed page.  By default this appears in the site's footer.

Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being
utilized to render the currently displayed page in WordPress.  Sometimes page or category specific templates exist, or
a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain
templates, causing WordPress to fall back on others.

This plugin relieves the aggravation by assisting designers and developers by displaying the template being used to
render the currently displayed page in WordPress.  This typically appears in the site's footer (though only if the theme
calls the wp_footer() (as is generally recommended) at some point.  Also, obviously this can only universally apply to
the site if said footer is actually used on every page.

A template tag also exists which can be used to display the template.

	<?php reveal_template(); ?>

By default, reveal_template() will echo the template name.  To simply retrieve the template file rather than displaying it, do:

	<?php $template = reveal_template(false); ?>

The intent of the plugin is for it to be activated on an as-needed basis.

Compatible with WordPress 2.5+, 2.6+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://www.coffee2code.com/wp-plugins/reveal-template.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress on an as-needed basis.

TODO:
	* Admin option to control if default behavior should be to output template to footer.
	* And if so, how so (prcnt-sub string input) --  i.e. "<!--%template%-->" or "<p>%template%</p>"
	* Specify how much of template path is to be displayed. (fullpath, relativepath, theme+file, file)
		(either as a setting, an argument to reveal, or maybe both (the setting is the default and is what
		 is used by the footer display))
	* Reveal path to certain users -- so plugin could be left in and only a user check would be incurred unless
	  the user is one interested in template info
*/

/*
Copyright (c) 2008 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( !class_exists('RevealTemplate') ) :

class RevealTemplate {
	var $template = '';

	function RevealTemplate() {
	}

	function actions_and_filters() {
		$templates = array('404', 'archive', 'attachment', 'author', 'category', 'comments_popup',
							'date', 'home', 'page', 'search', 'single', 'tag', 'taxonomy');
		foreach ($templates as $template) {
			add_filter($template.'_template', array(&$this, 'template_handler'));
		}
		add_action('wp_footer', array(&$this, 'reveal'));
	}

	function template_handler($template) {
		$this->template = $template;
		return $template;
	}

	function reveal($echo = true, $in_footer = true) {
		$template = $this->template;
		// TODO: Handle customized output of template filename + path here.
		$template = basename($template);
		if ( $in_footer )
			echo "<p>Rendered template: $template</p>";
		elseif ( $echo )
			echo $template;
		return $template;
	}
} // end RevealTemplate

endif; // end if !class_exists()
if ( class_exists('RevealTemplate') ) :
	// Get the ball rolling
	global $reveal_template;
	$reveal_template = new RevealTemplate();
	add_action('plugins_loaded', array(&$reveal_template, 'actions_and_filters'));
	//
	// TEMPLATE FUNCTION
	//
	function reveal_template($echo = true) {
		global $reveal_template;
		return $reveal_template->reveal($echo, false);
	}
endif;
?>