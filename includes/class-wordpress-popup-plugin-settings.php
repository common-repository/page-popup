<?php
/**
 * Settings class file.
 *
 * @package Popup/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class WordPress_Popup_Plugin_Settings {

	/**
	 * The single instance of WordPress_Popup_Plugin_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpp_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {

		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings() {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'Popup Plugin Settings', 'wordpress-popup-plugin' ),
				'menu_title'  => __( 'Popup Plugin Settings', 'wordpress-popup-plugin' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->_token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings( $settings = array() ) {
		return $settings;
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below.
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field.
		// If you're not including an image upload then you can leave this function call out.
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-validator-js', $this->parent->assets_url . 'js/jquery.validate.min.js', array( 'farbtastic', 'jquery' ), '1.0.0', true );
		wp_enqueue_script( $this->parent->_token . '-validator-js' );
		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0', true );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'wordpress-popup-plugin' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'title'       => __( 'Popup Plugin Settings', 'wordpress-popup-plugin' ),
			'description' => __( 'Popup Display Settings', 'wordpress-popup-plugin' ),
			'fields'      => array(
				array(
					'id'          => 'title',
					'label'       => __( 'Title*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Title.', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Title', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'title_colour',
					'label'       => __( 'Title color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Title Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#ffffff',
				),
				array(
					'id'          => 'description',
					'label'       => __( 'Description*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Description.', 'wordpress-popup-plugin' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( 'Description', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'description_colour',
					'label'       => __( 'Description color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Description Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#ffffff',
				),
				array(
					'id'          => 'button_text',
					'label'       => __( 'Button text*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Button Text.', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Button text', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'button_link',
					'label'       => __( 'Button link*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Button Link.', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Button link', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'button_colour',
					'label'       => __( 'Button color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Button Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#000000',
				),
				array(
					'id'          => 'button_text_colour',
					'label'       => __( 'Button text color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Button Text Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#ffffff',
				),
				array(
					'id'          => 'background_colour',
					'label'       => __( 'Background color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Background Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#21759B',
				),
				array(
					'id'          => 'background_image',
					'label'       => __( 'Background image', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Background Image.', 'wordpress-popup-plugin' ),
					'type'        => 'image',
					'default'     => '',
					'placeholder' => '',
				),
				array(
					'id'          => 'popup_timeout',
					'label'       => __( 'Popup Timeout*', 'wordpress-popup-plugin' ),
					'description' => __( 'Enter time in millisecond (1 Second = 1000 Millisecond).', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Popup Timeout', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'popup_width',
					'label'       => __( 'Popup Width', 'wordpress-popup-plugin' ),
					'description' => __( 'Enter Popup Width in % or px.', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Popup Width', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'popup_height',
					'label'       => __( 'Popup Height', 'wordpress-popup-plugin' ),
					'description' => __( 'Enter Popup Height in % or px.', 'wordpress-popup-plugin' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'Popup Height', 'wordpress-popup-plugin' ),
				),
				array(
					'id'          => 'display_single',
					'label'       => __( 'Display Popup one time only?', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Display only first time.', 'wordpress-popup-plugin' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'popup_effect',
					'label'       => __( 'Popup Effect', 'wordpress-popup-plugin' ),
					'description' => __( 'Select Popup Effect', 'wordpress-popup-plugin' ),
					'type'        => 'select',
					'options'     => array(
						'md-effect-1'    => 'Fade in',
						'md-effect-2'    => 'Slide in (right)',
						'md-effect-3'    => 'Slide in (bottom)',
						'md-effect-4'    => 'Newspaper',
						'md-effect-5'    => 'Fall',
						'md-effect-6'    => 'Side Fall',
						'md-effect-7'    => 'Sticky Up',
						'md-effect-8'    => '3D Flip (horizontal)',
						'md-effect-9'    => '3D Flip (vertical)',
						'md-effect-10'   => '3D Sign',
						'md-effect-11'   => 'Super Scaled',
						'md-effect-12'   => 'Just Me',
						'md-effect-13'   => '3D Slit',
						'md-effect-14'   => '3D Rotate Bottom',
						'md-effect-15'   => '3D Rotate In Left',
						'md-effect-16'   => 'Blur',
						'md-effect-17'   => 'Let me in',
						'md-effect-18'   => 'Make way!',
						'md-effect-19'   => 'Slip from top',
					),
					'default'     => 'md-effect-1',
				),
				array(
					'id'          => 'overlay_colour',
					'label'       => __( 'Overlay color*', 'wordpress-popup-plugin' ),
					'description' => __( 'Popup Overlay Colour.', 'wordpress-popup-plugin' ),
					'type'        => 'color',
					'default'     => '#000000',
				),
				array(
					'id'          => 'overlay_opacity',
					'label'       => __( 'Overlay Opacity', 'wordpress-popup-plugin' ),
					'description' => __( 'Select Overlay opacity', 'wordpress-popup-plugin' ),
					'type'        => 'select',
					'options'     => array(
						'0.1'	  => '0.1',
						'0.2'     => '0.2',
						'0.3' 	  => '0.3',
						'0.4' 	  => '0.4',
						'0.5' 	  => '0.5',
						'0.6' 	  => '0.6',
						'0.7' 	  => '0.7',
						'0.8' 	  => '0.8',
						'0.9' 	  => '0.9',
						'1.0' 	  => '1.0',
					),
					'default'     => '1.0',
				),
			),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			//phpcs:disable
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}
			//phpcs:enable

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; //phpcs:ignore
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html      = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";

			$tab = '';
		//phpcs:disable
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}
		//phpcs:enable

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) { //phpcs:ignore
					if ( 0 === $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) { //phpcs:ignore
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) { //phpcs:ignore
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . sanitize_text_field( $class ) . '">' . sanitize_text_field( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}

			$html .= '<form method="post" action="options.php" class="wpp_form" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . sanitize_text_field( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . sanitize_text_field( __( 'Save Settings', 'wordpress-popup-plugin' ) ) . '" />' . "\n";
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";

		echo $html; //phpcs:ignore
	}

	/**
	 * Main WordPress_Popup_Plugin_Settings Instance
	 *
	 * Ensures only one instance of WordPress_Popup_Plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WordPress_Popup_Plugin()
	 * @param object $parent Object instance.
	 * @return object WordPress_Popup_Plugin_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, sanitize_text_field( __( 'Cloning of WordPress_Popup_Plugin_API is forbidden.' ) ), sanitize_text_field( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, sanitize_text_field( __( 'Unserializing instances of WordPress_Popup_Plugin_API is forbidden.' ) ), sanitize_text_field( $this->parent->_version ) );
	} // End __wakeup()

}
