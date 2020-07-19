<?php
/**
 * Plugin Name: Reveal Template
 * Version:     3.5
 * Plugin URI:  https://coffee2code.com/wp-plugins/reveal-template/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reveal-template
 * Description: Reveal the theme template file used to render the displayed page, via the footer, widget, shortcode, and/or template tag.
 *
 * Compatible with WordPress 4.9+ through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/reveal-template/
 *
 * @package Reveal_Template
 * @author  Scott Reilly
 * @version 3.5
 */

/*
	Copyright (c) 2008-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_RevealTemplate' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'reveal-template.widget.php' );

final class c2c_RevealTemplate extends c2c_RevealTemplate_Plugin_050 {

	/**
	 * Name of plugin's setting.
	 *
	 * @since 3.4
	 * @var string
	 */
	const SETTING_NAME = 'c2c_reveal_template';

	/**
	 * The one true instance.
	 *
	 * @var c2c_RevealTemplate
	 * @since 3.0
	 */
	private static $instance;

	/**
	 * The default template path type.
	 *
	 * @var string
	 * @since 3.0
	 */
	private $default_template_path_type = 'theme-relative';

	/**
	 * The shortcode name.
	 *
	 * @var string
	 * @since 3.0
	 */
	private $shortcode = 'revealtemplate';

	/**
	 * The template being used.
	 * @var string
	 */
	private $template = '';

	/**
	 * The memoized template path types array.
	 *
	 * @var array
	 * @since 3.0
	 */
	private static $template_path_types;

	/**
	 * Get singleton instance.
	 *
	 * @since 3.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '3.5', 'reveal-template', 'c2c', __FILE__, array( 'settings_page' => 'themes' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 2.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 *
	 * @since 2.1
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	public function load_config() {
		$this->name      = __( 'Reveal Template', 'reveal-template' );
		$this->menu_name = $this->name;

		$this->config = array(
			'display_in_footer' => array(
				'input'    => 'checkbox',
				'default'  => true,
				'label'    => __( 'Reveal in footer?', 'reveal-template' ),
				'help'     => __( 'To be precise, this displays where <code>&lt;?php wp_footer(); ?></code> is called. If you uncheck this, you\'ll have to use the widget or the template tag to display the template.', 'reveal-template' ),
			),
			'display_in_admin_bar' => array(
				'input'    => 'checkbox',
				'default'  => true,
				'label'    => __( 'Reveal in admin bar?', 'reveal-template' ),
				'help'     => __( 'Adds a "Reveal Template" admin bar entry that only appears on the front-end of the site.', 'reveal-template' ),
			),
			'format' => array(
				'input'    => 'long_text',
				'default'  => __( '<p>Rendered template: %template%</p>', 'reveal-template' ),
				'label'    => __( 'Output format', 'reveal-template' ),
				'required' => true,
				'help'     => __( 'Only used for the footer display. Use %template% to indicate where the template name should go.', 'reveal-template' ),
			),
			'template_path' => array(
				'input'    => 'select',
				'datatype' => 'hash',
				'default'  => $this->get_default_template_path_type(),
				'label'    => __( 'Template path', 'reveal-template' ),
				'options'  => self::get_template_path_types(),
				'help'     => __( 'How much of the template path do you want reported? Applies directly to admin bar and footer displays, and is the default for the template tag usage (though can be overridden via an argument to <code>c2c_reveal_template()</code>)', 'reveal-template' ),
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to actually register
	 * actions against filters.
	 */
	public function register_filters() {
		$options = $this->get_options();

		$templates = array(
			'404',
			'archive',
			'attachment',
			'author',
			'category',
			'date',
			'embed',
			'frontpage',
			'home',
			'index',
			'page',
			'paged',
			'search',
			'single',
			'singular',
			'tag',
			'taxonomy',
		);

		foreach ( $templates as $template ) {
			add_filter( $template . '_template', array( $this, 'template_handler' ) );
		}

		if ( $options['display_in_footer'] ) {
			add_action( 'wp_footer', array( $this, 'reveal_in_footer' ) );
		}

		if ( $this->can_show_in_admin_bar() ) {
			add_action( 'wp_before_admin_bar_render', array( $this, 'output_admin_bar_styles' ) );
			add_action( 'admin_bar_menu', array( $this, 'add_to_admin_bar' ), 100 );
		}

		add_shortcode( $this->shortcode, array( $this, 'shortcode' ) );
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text Optional. Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		$options = $this->get_options();
		parent::options_page_description( __( 'Reveal Template Settings', 'reveal-template' ) );
		echo '<p>' . __( 'Reveal the theme template used to render the displayed page. By default this appears in the site\'s footer and only for logged in users with the "update_themes" capability (such as an admin).', 'reveal-template' ) . '</p>';
		echo '<p>' . sprintf( __( 'Also note that the plugin provides a "Reveal Template" <a href="%s">widget</a> that can be used to reveal the current template.', 'reveal-template' ), admin_url( 'widgets.php' ) ) . '</p>';
		echo '<p>' . sprintf( __( 'Please refer to this plugin\'s <a href="%s" title="readme">readme.txt</a> file for documentation and examples.', 'reveal-template' ), $this->readme_url() ) . '</p>';
	}

	/**
	 * Stores the name of the template being rendered.
	 *
	 * @param string  $template The template name.
	 * @return string The unmodified template name.
	 */
	public function template_handler( $template ) {
		$this->template = $template;
		return $template;
	}

	/**
	 * Returns types of, and descriptions for, the valid template path types.
	 *
	 * @since 3.0
	 *
	 * @return array Keys are the template path types, values are the translated descriptions
	 */
	public function get_template_path_types() {
		if ( ! self::$template_path_types ) {
			self::$template_path_types = array(
				'absolute'       => __( 'Absolute path, e.g. /usr/local/www/yoursite/wp-content/themes/yourtheme/single.php', 'reveal-template' ),
				'relative'       => __( 'Relative path, e.g. wp-content/themes/yourtheme/single.php', 'reveal-template' ),
				'theme-relative' => __( 'Path relative to themes directory, e.g. yourtheme/single.php', 'reveal-template' ),
				'filename'       => __( 'Filename, e.g. single.php', 'reveal-template' ),
			);
		}

		return self::$template_path_types;
	}

	/**
	 * Gets the default template path type.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_default_template_path_type() {
		return $this->default_template_path_type;
	}

	/**
	 * Determines if the current user can be shown the template name/path.
	 *
	 * @since 3.0
	 */
	public function reveal_to_current_user() {
		return current_user_can( 'update_themes' );
	}

	/**
	 * Handles the shortcode.
	 *
	 * @since 3.0
	 *
	 * @param array  $atts    The shortcode attributes parsed into an array.
	 * @param string $content The content between opening and closing shortcode tags.
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$defaults = array(
			'type'  => $this->get_default_template_path_type(),
			'admin' => '1',
		);
		$a = shortcode_atts( $defaults, $atts );

		// Validate attributes
		if ( ! in_array( $a['type'], array_keys( $this->get_template_path_types() ) ) ) {
			$a['type'] = $this->get_default_template_path_type();
		}

		$args = array(
			'admin_only' => ( '0' === $a['admin'] ? false : true ),
			'echo'       => false,
			'return'     => false,
		);

		return $this->reveal( $a['type'], $args );
	}

	/**
	 * Invokes the reveal intended to be shown in the site's footer.
	 *
	 * @since 3.0
	 */
	public function reveal_in_footer() {
		$options = $this->get_options();
		return $this->reveal( $options['template_path'], array( 'format_from_settings' => true ) );
	}

	/**
	 * Determines if the admin bar entry should be shown.
	 *
	 * @since 3.5
	 *
	 * @return bool True if the admin bar entry can be shown, else false.
	 */
	public function can_show_in_admin_bar() {
		// Don't show if in admin or is admin bar isn't even showing.
		if ( is_admin() || ! is_admin_bar_showing() ) {
			return false;
		}

		$options = $this->get_options();

		// Is feature enabled via settings and user is permitted to see it?
		return $options['display_in_admin_bar'] && $this->reveal_to_current_user();
	}

	/**
	 * Outputs CSS for the admin bar menu item.
	 *
	 * @since 3.5
	 */
	public function output_admin_bar_styles() {
		echo '<style>#wpadminbar #wp-admin-bar-reveal-template .ab-icon::before { content: "\f100"; top: 2px; } </style>' . "\n";
	}

	/**
	 * Adds an admin menu bar node that reveals the template.
	 *
	 * @since 3.5
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
	 */
	public function add_to_admin_bar( $wp_admin_bar ) {
		$wp_admin_bar->add_node(
			array(
				'id'     => 'reveal-template',
				'parent' => 'top-secondary',
				'title'  => '<span class="ab-icon"></span><span class="ab-label">' . __( 'Reveal Template', 'reveal-template' ) . '</span>',
				'meta'   => array(
					'tabindex' => 0,
				),
			)
		);

		$options = $this->get_options();
		$path = $this->reveal( $options['template_path'], array( 'echo' => false, 'format_from_settings' => false ) );

		$wp_admin_bar->add_node(
			array(
				'parent' => 'reveal-template',
				'id'     => 'revealed-template',
				'title'  => $path,
			)
		);
	}

	/**
	 * Formats for output the template path info for the currently rendered template.
	 *
	 * @param string $template_path_type The style of the template's path for return. Accepts: 'absolute', 'relative',
	 *                                   'theme-relative', 'filename'
	 * @param array  $args               {
	 *     Optional. Additional arguments.
	 *
	 *     @type bool   $admin_only           Only show for an admin? Default is true.
	 *     @type bool   $echo                 Echo the output? Default is true.
	 *     @type string $format               Output string format. Uses '%template%' as placeholder for template path.
	 *     @type bool   $format_from_settings Use format string specified via the plugin's settings page? Default false.
	 *     @type bool   $return               Return the value regardless of the admin_only value and check? Default true.
	 * }
	 * @return string   The path info for the currently rendered template, unless $args['return'] is false AND user
	 *                  wouldn't be shown the output
	 */
	public function reveal( $template_path_type, $args = array() ) {
		$template = $this->template;

		if ( ! $template ) {
			return '';
		}

		$defaults = array(
			'admin_only'           => true,
			'echo'                 => true,
			'format'               => '',
			'format_from_settings' => false,
			'return'               => true,
		);
		$args = wp_parse_args( $args, $defaults );

		$return = $args['return'];

		// Format the template path string based on type.
		switch ( $template_path_type ) {
			case 'absolute':
				// Do nothing; already have the absolute path
				break;
			case 'filename':
				$template = basename( $template );
				break;
			case 'relative':
				$template = str_replace( ABSPATH, '', $template );
				break;
			case 'theme-relative':
			default:
				$template = basename( dirname( $template ) ) . '/' . basename( $template );
				break;
		}

		// Check if current user can be shown the template.
		$is_allowed = ( false === $args['admin_only'] || $this->reveal_to_current_user() );

		if ( $return || $is_allowed ) {
			// Format the template path string according to format string.
			if ( $args['format_from_settings'] ) {
				$options = $this->get_options();
				$format = $options['format'];
			} else {
				$format = $args['format'];
			}
			$display = $format ? str_replace( '%template%', $template, $format ) : $template;

			if ( $is_allowed && $display && $args['echo'] ) {
				echo $display;
			}

			return $display;
		}

		return '';
	}

} // end c2c_RevealTemplate

add_action( 'plugins_loaded', array( 'c2c_RevealTemplate', 'get_instance' ) );

//
// TEMPLATE FUNCTION
//

	/**
	 * Formats the template path info for the currently rendered template for output.
	 *
	 * If $template_path_type argument is not specified, then the default value
	 * configured via the plugin's settings page will be used.
	 *
	 * @since 2.0
	 *
	 * @param bool   $echo               Optional. Echo the template info? Default true
	 * @param string $template_path_type Optional. The style of the template's path for return. Accepts: 'absolute',
	 *                                   'relative', 'theme-relative', 'filename'
	 * @param array  $args               Optional. Additional configuration. See c2c_RevealTemplate::reveal() for
	 *                                   documentation.
	 * @return string The path info for the currently rendered template
	 */
	if ( ! function_exists( 'c2c_reveal_template' ) ) :
		function c2c_reveal_template( $echo = true, $template_path_type = '', $args = array() ) {
			// See (and possibly override) 'echo' value in $args with value passed as the $echo argument.
			$args['echo'] = $echo;
			return c2c_RevealTemplate::get_instance()->reveal( $template_path_type, $args );
		}
	endif;

endif; // end if !class_exists()
