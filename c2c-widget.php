<?php
/**
 * C2C_Widget widget code.
 *
 * Copyright (c) 2010-2017 by Scott Reilly (aka coffee2code)
 *
 * @package c2c_Widget_013
 * @author  Scott Reilly
 * @version 013
 */

defined( 'ABSPATH' ) or die();

if ( class_exists( 'WP_Widget' ) && ! class_exists( 'c2c_Widget_013' ) ) :

abstract class c2c_Widget_013 extends WP_Widget {

	/**
	 * Widget ID.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $widget_id = '';

	/**
	 * Widget file.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $widget_file = '';

	/**
	 * Widget title.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $title = '';

	/**
	 * Widget description.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $description = '';

	/**
	 * Prefix for hooks.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $hook_prefix = '';

	/**
	 * Widget configuration.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $config = array();

	/**
	 * Widget default configuration.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $defaults = array();

	/**
	 * Returns version of the widget library.
	 *
	 * @since 010
	 *
	 * @return string
	 */
	public static function version() {
		return '013';
	}


	/**
	 * Constructor.
	 *
	 * @param string $widget_id   Unique identifier for plugin, lowercased and underscored.
	 * @param string $widget_file The sub-class widget file (__FILE__).
	 * @param array  $control_ops Array of options to control appearance of widget: width, height, id_base.
	 */
	public function __construct( $widget_id, $widget_file, $control_ops = array() ) {
		$this->widget_id   = $widget_id;
		$this->widget_file = $widget_file;

		$this->load_config();

		// input can be 'checkbox', 'multiselect', 'select', 'short_text', 'text', 'textarea', 'hidden', or 'none'
		// datatype can be 'array' or 'hash'
		// can also specify input_attributes
		$this->config = apply_filters( $this->get_hook( 'config' ), $this->config );

		if ( empty( $this->hook_prefix ) ) {
			$this->hook_prefix = $this->widget_id;
		}

		foreach ( $this->config as $key => $value ) {
			$this->defaults[ $key ] = isset( $value['default'] ) ? $value['default'] : '';
		}

		$widget_ops = array(
			'classname'   => 'widget_' . $this->widget_id,
			'description' => $this->description,
		);

		$widget_ops  = apply_filters( $this->get_hook( 'widget_ops' ), $widget_ops );
		$control_ops = apply_filters( $this->get_hook( 'control_ops' ), $control_ops );

		parent::__construct( $this->widget_id, $this->title, $widget_ops, $control_ops );
	}

	/**
	 * Returns the config array.
	 *
	 * @since 013
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Outputs the widget.
	 *
	 * Simply override this function if you want full control over widget. Otherwise, you can hook into just the body.
	 *
	 * @param array $args     Widget args.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		/* Settings */
		$settings = array();
		foreach ( array_keys( $this->config ) as $key ) {
			// Check for existence since key may be newly introduced since widget was last saved.
			if ( ! isset( $instance[ $key ] ) ) {
				$instance[ $key ] = '';
			}
			$settings[ $key ] = apply_filters( $this->get_hook( 'config_item_'.$key ), $instance[ $key ], $this );
		}

		$body = trim( $this->widget_body( $args, $instance, $settings ) );

		// If the widget is empty, don't output anything.
		if ( ! $body ) {
			return;
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title',  empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo $body;
		echo $args['after_widget'];
	}

	/**
	 * Save and validate updates to widget values.
	 *
	 * @param array  $new_instance New instance.
	 * @param array  $old_instance Old instance.
	 * @return array Updated instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		foreach ( array_keys( $this->config ) as $key ) {
			$instance[ $key ] = isset( $new_instance[ $key ] ) ? $new_instance[ $key ] : '';
		}
		return $this->validate( $instance );
	}

	/**
	 * Draws the widget input form.
	 *
	 * @param array      $instance        Widget instance
	 * @param array|null $exclude_options Optional. The options that should not be drawn in the form.
	 */
	public function form( $instance, $exclude_options = null ) {
		$exclude_options = (array) apply_filters( $this->get_hook( 'excluded_form_options' ), $exclude_options );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$i = $j = 0;
		foreach ( $instance as $opt => $value ) {
			if ( 'submit' == $opt || in_array( $opt, $exclude_options ) ) {
				continue;
			}

			foreach ( array( 'datatype', 'default', 'help', 'input', 'input_attributes', 'label', 'no_wrap', 'options' ) as $attrib ) {
				if ( ! isset( $this->config[ $opt ][ $attrib ] ) ) {
					$this->config[ $opt ][ $attrib ] = '';
				}
			}

			$input = $this->config[ $opt ]['input'];
			$label = $this->config[ $opt ]['label'];

			if ( 'none' == $input ) {
				if ( 'more' == $opt ) {
					$i++; $j++;
					echo "<p><a style='display:none;' class='widget-group-link widget-group-link-$i' href='#'>$label &raquo;</a></p>";
					echo "<div class='widget-group widget-group-$i'>";
				} elseif ( 'endmore' == $opt ) {
					$j--;
					echo '</div>';
				}
				continue;
			}

			if ( 'multiselect' == $input ) {
				// Do nothing since it needs the values as an array
				$value = (array) $value;
			} elseif ( 'array' == $this->config[ $opt ]['datatype'] ) {
				if ( ! is_array( $value ) ) {
					$value = '';
				} else {
					$value = implode( ( 'textarea' == $input ? "\n" : ', ' ), $value );
				}
			} elseif ( 'hash' == $this->config[ $opt ]['datatype'] ) {
				if ( ! is_array( $value ) ) {
					$value = '';
				} else {
					$new_value = '';
					foreach ( $value AS $shortcut => $replacement ) {
						$new_value .= "$shortcut => $replacement\n";
					}
					$value = $new_value;
				}
			}

			echo '<p>';

			$input_id   = $this->get_field_id( $opt );
			$input_name = $this->get_field_name( $opt );

			if ( 'multiselect' == $input ) {
				$input_name .= '[]';
			}

			$attribs = sprintf(
				"name='%s' id='%s'",
				esc_attr( $input_name ),
				esc_attr( $input_id )
			);
			// Presumes input_attributes has already been escaped.
			if ( $this->config[ $opt ]['input_attributes'] ) {
				$attribs .= ' ' . $this->config[ $opt ]['input_attributes'];
			}

			if ( $label && ( 'multiselect' != $input ) ) {
				printf(
					"<label for='%s'>%s:</label> ",
					esc_attr( $input_id ),
					$label
				);
			}
			if ( ! $input ) {
				// Output nothing.
			}
			elseif ( 'textarea' == $input ) {
				echo "<textarea $attribs class='widefat'>" . $value . '</textarea>';
			}
			elseif ( 'select' == $input ) {
				echo "<select $attribs>";
				foreach ( (array) $this->config[ $opt ]['options'] as $sopt ) {
					echo "<option value='" . esc_attr( $sopt ) . "' " . selected( $value, $sopt, false ) . ">$sopt</option>";
				}
				echo "</select>";
			}
			elseif ( 'multiselect' == $input ) {
				echo '<fieldset style="border:1px solid #ccc; padding:2px 8px;">';
				if ( $label ) {
					echo "<legend>$label: </legend>";
				}
				foreach ( (array) $this->config[ $opt ]['options'] as $sopt ) {
					echo "<input type='checkbox' $attribs value='$sopt' " . checked( in_array( $sopt, $value ), true, false ) . ">$sopt</input><br />";
				}
				echo '</fieldset>';
			}
			elseif ( 'checkbox' == $input ) {
				echo "<input type='$input' $attribs value='1' " . checked( $value, 1, false ) . " />";
			}
			else {
				if ( 'short_text' == $input ) {
					$tclass = '';
					$tstyle = 'width:25px;';
				} else {
					$tclass = 'widefat';
					$tstyle = '';
				}
				echo "<input type='text' $attribs value='" . esc_attr( $value ) . "' class='$tclass' style='$tstyle' />";
			}
			if ( $this->config[ $opt ]['help'] ) {
				echo "<br /><span style='color:#888; font-size:x-small;'>{$this->config[ $opt ]['help']}</span>";
			}

			echo "</p>\n";
		}
		// Close any open divs
		for ( ; $j > 0; $j-- ) { echo '</div>'; }
	}

	/**
	 * Returns the full plugin-specific name for a hook.
	 *
	 * @param string  $hook The name of a hook, to be made plugin-specific.
	 * @return string The plugin-specific version of the hook name.
	 */
	public function get_hook( $hook ) {
		return $this->hook_prefix . '_' . $hook;
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * MUST Be OVERRIDDEN IN SUB-CLASS
	 *
	 * Two class variables containing localized strings should be set in this function, in addition to the config array.
	 *
	 * e.g.
	 *   $this->title = __( 'My Plugin Widget', 'text-domain' );
	 *   $this->description = __( 'Description of this widget.', 'text-domain' );
	 *   $this->config = array( ... );
	 */
	abstract public function load_config();

	/**
	 * Outputs the body of the widget.
	 *
	 * MUST BE OVERRIDDEN IN SUB-CLASS
	 *
	 * @param array $args     Widget args
	 * @param array $instance Widget instance
	 * @param array $settings Widget settings
	 */
	abstract public function widget_body( $args, $instance, $settings );

	/**
	 * Validates widget instance values.
	 *
	 * Intended to be overridden by sub-class, if needed.
	 *
	 * @param array  $instance Array of widget instance values
	 * @return array The filtered array of widget instance values
	 */
	public function validate( $instance ) {
		return $instance;
	}
} // end class

endif; // end if !class_exists()
