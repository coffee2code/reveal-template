<?php
/**
 * @package Reveal_Template
 * @author Scott Reilly
 * @version 2.2
 */
/*
Plugin Name: Reveal Template
Version: 2.2
Plugin URI: http://coffee2code.com/wp-plugins/reveal-template/
Author: Scott Reilly
Author URI: http://coffee2code.com
Domain Path: /lang/
Description: Reveal the theme template file used to render the displayed page, via the footer and/or template tag.

Compatible with WordPress 3.1+, 3.2+, 3.3+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/reveal-template/

TODO:
	* Change default of template_path to theme-relative? (to differeniate b/w parent and child themes)
	* Widget
*/

/*
Copyright (c) 2008-2012 by Scott Reilly (aka coffee2code)

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

if ( !class_exists( 'c2c_RevealTemplate' ) ) :

require_once( 'c2c-plugin.php' );

class c2c_RevealTemplate extends C2C_Plugin_031 {

	public static $instance;

	private $template = '';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->c2c_RevealTemplate();
	}

	public function c2c_RevealTemplate() {
		// Be a singleton
		if ( ! is_null( self::$instance ) )
			return;

		parent::__construct( '2.2', 'reveal-template', 'c2c', __FILE__, array( 'settings_page' => 'themes' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 2.5
	 *
	 * @return void
	 */
	public function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * This can be overridden.
	 *
	 * @since 2.5
	 *
	 * @return void
	 */
	public function uninstall() {
		delete_option( 'c2c_reveal_template' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	public function load_config() {
		$this->name      = __( 'Reveal Template', $this->textdomain );
		$this->menu_name = __( 'Reveal Template', $this->textdomain );

		$this->config = array(
			'display_in_footer' => array( 'input' => 'checkbox', 'default' => true,
					'label' => __( 'Reveal in footer?', $this->textdomain ),
					'help' => __( 'To be precise, this displays where <code>&lt;?php wp_footer(); ?></code> is called.  If you uncheck this, you\'ll have to use the template tag to display the template.', $this->textdomain ) ),
			'format' => array( 'input' => 'long_text', 'default' => __( '<p>Rendered template: %template%</p>', $this->textdomain ),
					'label' => __( 'Output format', $this->textdomain ), 'required' => true,
					'help' => __( 'Only used for the footer display.  Use %template% to indicate where the template name should go.', $this->textdomain ) ),
			'template_path' => array( 'input' => 'select', 'datatype' => 'hash', 'default' => 'filename',
					'label' => __( 'Template path', $this->textdomain ),
					'options' => array(
						'absolute' => __( 'Absolute path, e.g. /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php', $this->textdomain ),
						'relative' => __( 'Relative path, e.g. wp-content/themes/yourtheme/single.php', $this->textdomain ),
						'theme-relative' => __( 'Path relative to themes directory, e.g. yourtheme/single.php', $this->textdomain ),
						'filename' => __( 'Filename, e.g. single.php', $this->textdomain )
					),
					'help' => __( 'How much of the template path do you want reported?  Applies directory to footer display, and is the default for the template tag usage (though can be overridden via an argument to <code>reveal_template()</code>)', $this->textdomain ) )
		);
	}

	/**
	 * Override the plugin framework's register_filters() to actually actions against filters.
	 *
	 * @return void
	 */
	public function register_filters() {
		$options = $this->get_options();
		$templates = array( '404', 'archive', 'attachment', 'author', 'category', 'comments_popup',
							'date', 'home', 'page', 'search', 'single', 'tag', 'taxonomy' );
		foreach ( $templates as $template )
			add_filter( $template.'_template', array( &$this, 'template_handler' ) );

		if ( $options['display_in_footer'] )
			add_action( 'wp_footer', array( &$this, 'reveal' ) );
	}

	/**
	 * Outputs the text above the setting form
	 *
	 * @return void (Text will be echoed.)
	 */
	public function options_page_description() {
		$options = $this->get_options();
		parent::options_page_description( __( 'Reveal Template Settings', $this->textdomain ) );
		echo '<p>' . __( 'Reveal the theme template used to render the displayed page.  By default this appears in the site\'s footer.', $this->textdomain ) . '</p>';
		echo '<p>' . sprintf( __( 'Please refer to this plugin\'s <a href="%s" title="readme">readme.txt</a> file for documentation and examples.', $this->textdomain ), $this->readme_url() ) . '</p>';
	}

	/**
	 * Stores the name of the template being rendered
	 *
	 * @param string $template The template name
	 * @return string The unmodified template name
	 */
	public function template_handler( $template ) {
		$this->template = $template;
		return $template;
	}

	/**
	 * Formats for output the template path info for the currently rendered template.
	 *
	 * @param bool $echo (optional) Echo the template info? Default is true
	 * @param string $template_path_type (optional) The style of the template's path for return. Accepts: 'absolute', 'relative', 'theme-relative', 'filename'
	 * @param bool $in_footer (optional) Should the path info be output in the footer? Default is true
	 * @return string The path info for the currently rendered template
	 */
	public function reveal( $echo = true, $template_path_type = '', $in_footer = true ) {
		$template = $this->template;
		$options = $this->get_options();
		// Handle customized output of template filename + path
		if ( empty( $template_path_type ) )
			$template_path_type = $options['template_path'];

		switch ( $template_path_type ) {
			case 'absolute':
				// Do nothing; already have the absolute path
				break;
			case 'relative':
				$template = str_replace( ABSPATH,'', $template );
				break;
			case 'theme-relative':
				$template = basename( dirname( $template ) ) . '/' . basename( $template );
				break;
			case 'filename':
			default:
				$template = basename( $template );
				break;
		}

		if ( $in_footer ) {
			// Should this check to see if user defined %template%, and if not, go ahead and display template?
			if ( $options['format'] )
				$display = str_replace( '%template%', $template, $options['format'] );
			else
				$display = $template;
			echo $display;
		} elseif ( $echo ) {
			echo $template;
		}

		return $template;
	}
} // end c2c_RevealTemplate

// To access plugin object instance use: c2c_RevealTemplate::$instance
new c2c_RevealTemplate();

//
// TEMPLATE FUNCTION
//

	/**
	 * Formats for output the template path info for the currently rendered template.
	 *
	 * If $template_path_type argument is not specified, then the default value
	 * configured via the plugin's settings page will be used.
	 *
	 * @since 2.0
	 *
	 * @param bool $echo (optional) Echo the template info? Default is true
	 * @param string $template_path_type (optional) The style of the template's path for return. Accepts: 'absolute', 'relative', 'theme-relative', 'filename'
	 * @return string The path info for the currently rendered template
	 */
	if ( ! function_exists( 'c2c_reveal_template' ) ) :
		function c2c_reveal_template( $echo = true, $template_path_type = '' ) {
			return c2c_RevealTemplate::$instance->reveal( $echo, $template_path_type, false );
		}
	endif;

	/**
	 * @deprecated 2.0 Use c2c_reveal_template() instead
	 */
	if ( ! function_exists( 'reveal_template' ) ) :
		function reveal_template( $echo = true, $template_path_type = '' ) {
			_deprecated_function( 'reveal_template', '2.0', 'c2c_reveal_template' );
			return c2c_reveal_template( $echo, $template_path_type );
		}
	endif;

endif; // end if !class_exists()
?>