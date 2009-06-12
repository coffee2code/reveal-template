<?php
/*
Plugin Name: Reveal Template
Version: 1.0.1
Plugin URI: http://coffee2code.com/wp-plugins/reveal-template
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Reveal the theme template file used to render the displayed page.  By default this appears in the site's footer.

Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being
utilized to render the currently displayed page in WordPress.  Sometimes page or category specific templates exist, or
a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain
templates causing WordPress to fall back to others.

This plugin relieves the aggravation by assisting designers and developers by displaying the template being used to
render the currently displayed page in WordPress.  This typically appears in the site's footer (though only if the theme
follows the recommended practice of calling the wp_footer() template tag) at some point.  Also, obviously this can only
universally apply to the site if said footer is actually used on every page.

A template tag is also provided which can be used to display the template file.

	<?php reveal_template(); ?>

By default, reveal_template() will echo the template name.  To simply retrieve the template filename rather than displaying it:

	<?php $template = reveal_template(false); ?>

The template tag also takes a second argument which be can be one of the following: absolute, relative, template-relative, filename.
This determines the path style you'd like reported.  If not specified, it uses the default defined in the plugin's settings page.

Examples of path types:
* "absolute" : /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php
* "relative" : wp-content/themes/yourtheme/single.php
* "template-relative" : yourtheme/single.php
* "filename" : single.php

This plugin is primarily intended to be activated on an as-needed basis.

Compatible with WordPress 2.6+, 2.7+, 2.8+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/reveal-template.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress on an as-needed basis.
3. Optionally customize the plugin's settings by click the plugin's 'Settings' link next to its 'Deactivate' link (still on the
Plugins page), or click on the 'Design' -> 'Reveal Template' link, to go to the plugin's admin settings page.

*/

/*
Copyright (c) 2008-2009 by Scott Reilly (aka coffee2code)

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
	var $admin_options_name = 'c2c_reveal_template';
	var $nonce_field = 'update-reveal_template';
	var $plugin_name = 'Reveal Template';
	var $show_admin = true;	// Change this to false if you don't want the plugin's admin page shown.
	var $config = array(
		'display_in_footer' => array('input' => 'checkbox', 'default' => true,
				'label' => 'Reveal in footer?',
				'help' => 'To be precise, this displays where <code>&lt;?php wp_footer(); ?></code> is called.  If you uncheck this, you\'ll have to use the template tag to display the template.'),
		'format' => array('input' => 'text', 'default' => '<p>Rendered template: %template%</p>',
				'label' => 'Output format',
				'input_attributes' => 'style="width:95%;"',
				'help' => 'Only used for the footer display.  Use %template% to indicate where the template name should go.'),
		'template_path' => array('input' => 'select', 'default' => 'filename',
				'label' => 'Template path',
				'options' => array(
					'absolute' => 'Absolute path, e.g. /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php',
					'relative' => 'Relative path, e.g. wp-content/themes/yourtheme/single.php',
					'theme-relative' => 'Path relative to themes directory, e.g. yourtheme/single.php',
					'filename' => 'Filename, e.g. single.php'
				),
				'help' => 'How much of the template path do you want reported?  Applies directory to footer display, and is
							the default for the template tag usage (though can be overridden via an argument to <code>reveal_template()</code>)')
	);
	var $options = array(); // Don't use this directly
	var $template = '';

	function RevealTemplate() {
		$this->plugin_name = __($this->plugin_name);
		if ( is_admin() )
			add_action('admin_menu', array(&$this, 'admin_menu'));
	}

	function install() {
		$options = $this->get_options();
		update_option($this->admin_options_name, $options);
	}

	function actions_and_filters() {
		$options = $this->get_options();
		$templates = array('404', 'archive', 'attachment', 'author', 'category', 'comments_popup',
							'date', 'home', 'page', 'search', 'single', 'tag', 'taxonomy');
		foreach ($templates as $template) {
			add_filter($template.'_template', array(&$this, 'template_handler'));
		}
		if ( $options['display_in_footer'] )
			add_action('wp_footer', array(&$this, 'reveal'));
	}

	function admin_menu() {
		static $plugin_basename;
		if ( $this->show_admin ) {
			global $wp_version;
			if ( current_user_can('manage_options') ) {
				$plugin_basename = plugin_basename(__FILE__); 
				if ( version_compare( $wp_version, '2.6.999', '>' ) )
					add_filter( 'plugin_action_links_' . $plugin_basename, array(&$this, 'plugin_action_links') );
				add_theme_page($this->plugin_name, $this->plugin_name, 9, $plugin_basename, array(&$this, 'options_page'));
			}
		}
	}

	function plugin_action_links( $action_links ) {
		static $plugin_basename;
		if ( !$plugin_basename ) $plugin_basename = plugin_basename(__FILE__); 
		$settings_link = '<a href="themes.php?page='.$plugin_basename.'">' . __('Settings') . '</a>';
		array_unshift( $action_links, $settings_link );

		return $action_links;
	}

	function get_options() {
		// Derive options from the config
		$options = array();
		foreach (array_keys($this->config) as $opt) {
			$options[$opt] = $this->config[$opt]['default'];
		}
        $existing_options = get_option($this->admin_options_name);
        if ( !empty($existing_options) ) {
            foreach ($existing_options as $key => $option)
                $options[$key] = $option;
        }            
        return $options;
	}

	function options_page() {
		static $plugin_basename;
		if ( !$plugin_basename ) $plugin_basename = plugin_basename(__FILE__); 
		$options = $this->get_options();
		// See if user has submitted form
		if ( isset($_POST['submitted']) ) {
			check_admin_referer($this->nonce_field);

			foreach (array_keys($options) AS $opt) {
				$options[$opt] = stripslashes($_POST[$opt]);
				$input = $this->config[$opt]['input'];
				if ( ($input == 'checkbox') && !$options[$opt] )
					$options[$opt] = 0;
				if ( $this->config[$opt]['datatype'] == 'array' ) {
					if ( $input == 'text' )
						$options[$opt] = explode(',', str_replace(array(', ', ' ', ','), ',', $options[$opt]));
					else
						$options[$opt] = array_map('trim', explode("\n", trim($options[$opt])));
				}
				elseif ( $this->config[$opt]['datatype'] == 'hash' ) {
					if ( !empty($options[$opt]) ) {
						$new_values = array();
						foreach (explode("\n", $options[$opt]) AS $line) {
							list($shortcut, $text) = array_map('trim', explode("=>", $line, 2));
							if ( !empty($shortcut) ) $new_values[str_replace('\\', '', $shortcut)] = str_replace('\\', '', $text);
						}
						$options[$opt] = $new_values;
					}
				}
			}
			// Remember to put all the other options into the array or they'll get lost!
			update_option($this->admin_options_name, $options);

			echo "<div id='message' class='updated fade'><p><strong>" . __('Settings saved') . '</strong></p></div>';
		}

		$action_url = $_SERVER[PHP_SELF] . '?page=' . $plugin_basename;
		$logo = plugins_url() . '/' . basename($_GET['page'], '.php') . '/c2c_minilogo.png';

		echo <<<END
		<div class='wrap'>
			<div class="icon32" style="width:44px;"><img src='$logo' alt='A plugin by coffee2code' /><br /></div>
			<h2>{$this->plugin_name} Settings</h2>
			<p>Reveal the theme template used to render the displayed page.  By default this appears in the site's footer.</p>

			<p>Designers and developers know that it can sometimes be confusing and frustrating to determine the exact template being
			utilized to render the currently displayed page in WordPress.  Sometimes page or category specific templates exist, or
			a page/post has been set by the post author to use a particular template, or the current theme doesn't employ certain
			templates, causing WordPress to fall back on others.</p>

			<p>This plugin relieves the aggravation by assisting designers and developers by displaying the template being used to
			render the currently displayed page in WordPress.  This typically appears in the site's footer (though only if the theme
			calls the wp_footer() (as is generally recommended) at some point.  Also, obviously this can only universally apply to
			the site if said footer is actually used on every page.</p>

			<p>A template tag also exists which can be used to display the template.</p>

			<blockquote><code>&lt;?php reveal_template(); ?></code></blockquote>

			<p>By default, reveal_template() will echo the template name.  To simply retrieve the template file rather than displaying it, do:</p>

			<blockquote><code>&lt;?php \$template = reveal_template(false); ?></code></blockquote>

			<p>The template tag also takes a second argument which be can be one of the following: absolute, relative, template-relative, filename.
			This determines the path style you'd like reported.  If not specified, it uses the default defined in the plugin's settings page.</p>
			
			<p>This plugin is primarily intended to be activated on an as-needed basis.</p>

			<form name="reveal_template" action="$action_url" method="post">	
END;
				wp_nonce_field($this->nonce_field);
		echo '<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform form-table">';
				foreach (array_keys($options) as $opt) {
					$input = $this->config[$opt]['input'];
					if ( $input == 'none' ) continue;
					$label = $this->config[$opt]['label'];
					$value = $options[$opt];
					if ( $input == 'checkbox' ) {
						$checked = ($value == 1) ? 'checked=checked ' : '';
						$value = 1;
					} else {
						$checked = '';
					};
					if ( $this->config[$opt]['datatype'] == 'array' ) {
						if ( !is_array($value) )
							$value = '';
						else {
							if ( $input == 'textarea' || $input == 'inline_textarea' )
								$value = implode("\n", $value);
							else
								$value = implode(', ', $value);
						}
					} elseif ( $this->config[$opt]['datatype'] == 'hash' ) {
						if ( !is_array($value) )
							$value = '';
						else {
							$new_value = '';
							foreach ($value AS $shortcut => $replacement) {
								$new_value .= "$shortcut => $replacement\n";
							}
							$value = $new_value;
						}
					}
					echo "<tr valign='top'>";
					if ( $input == 'textarea' ) {
						echo "<td colspan='2'>";
						if ( $label ) echo "<strong>$label</strong><br />";
						echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . $value . '</textarea>';
					} else {
						echo "<th scope='row'>$label</th><td>";
						if ( $input == "inline_textarea" )
							echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . $value . '</textarea>';
						elseif ( $input == 'select' ) {
							echo "<select name='$opt' id='$opt'>";
							foreach ($this->config[$opt]['options'] as $sopt => $sdesc) {
								$selected = $value == $sopt ? " selected='selected'" : '';
								if ( !$sdesc ) $sdesc = $sopt;
								echo "<option value='$sopt'$selected>$sdesc</option>";
							}
							echo "</select>";
						} else
							echo "<input name='$opt' type='$input' id='$opt' value='$value' $checked {$this->config[$opt]['input_attributes']} />";
					}
					if ( $this->config[$opt]['help'] ) {
						echo "<br /><span style='color:#777; font-size:x-small;'>";
						echo $this->config[$opt]['help'];
						echo "</span>";
					}
					echo "</td></tr>";
				}
		echo <<<END
			</table>
			<input type="hidden" name="submitted" value="1" />
			<div class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></div>
		</form>
			</div>
END;
		echo <<<END
		<style type="text/css">
			#c2c {
				text-align:center;
				color:#888;
				background-color:#ffffef;
				padding:5px 0 0;
				margin-top:12px;
				border-style:solid;
				border-color:#dadada;
				border-width:1px 0;
			}
			#c2c div {
				margin:0 auto;
				padding:5px 40px 0 0;
				width:45%;
				min-height:40px;
				background:url('$logo') no-repeat top right;
			}
			#c2c span {
				display:block;
				font-size:x-small;
			}
		</style>
		<div id='c2c' class='wrap'>
			<div>
			This plugin brought to you by <a href="http://coffee2code.com" title="coffee2code.com">Scott Reilly, aka coffee2code</a>.
			<span><a href="http://coffee2code.com/donate" title="Please consider a donation">Did you find this plugin useful?</a></span>
			</div>
		</div>
END;
	}

	function template_handler( $template ) {
		$this->template = $template;
		return $template;
	}

	function reveal( $echo = true, $template_path_type = '', $in_footer = true ) {
		$template = $this->template;
		$options = $this->get_options();
		// Handle customized output of template filename + path
		if ( empty($template_path_type) )
			$template_path_type = $options['template_path'];

		switch ($template_path_type) {
			case 'absolute':
				// Do nothing; already have the absolute path
				break;
			case 'relative':
				$template = str_replace(ABSPATH,'', $template) . '/' . basename($template);
				break;
			case 'theme-relative':
				$template = get_template() . '/' . basename($template);
				break;
			case 'filename':
			default:
				$template = basename($template);
				break;
		}

		if ( $in_footer ) {
			if ( $options['format'] ) {
				// Should this check to see if user defined %template%, and if not, go ahead and display template?
				$display = str_replace('%template%', $template, $options['format']);
			} else {
				$display = $template;
			}
			echo $display;
		} elseif ( $echo ) {
			echo $template;
		}

		return $template;
	}
} // end RevealTemplate

endif; // end if !class_exists()
if ( class_exists('RevealTemplate') ) :
	$reveal_template = new RevealTemplate();
	add_action('plugins_loaded', array(&$reveal_template, 'actions_and_filters'));

	//
	// TEMPLATE FUNCTION
	//

	// $template_path_type can be one of the following: absolute, relative, template-relative, filename
	// If $template_path_type is not specified, the default configured via the plugin's settings page is used.
	function reveal_template( $echo = true, $template_path_type = '' ) {
		global $reveal_template;
		return $reveal_template->reveal($echo, $template_path_type, false);
	}
endif;
?>