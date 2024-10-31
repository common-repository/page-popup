<?php
/**
 * Plugin Name: Page Popup
 * Version: 1.0.0
 * Description: Page popup
 * Author: Creativeencode
 * Author URI: https://www.creativeencode.com/
 * Text Domain: page-popup-plugin
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-wordpress-popup-plugin.php';
require_once 'includes/class-wordpress-popup-plugin-settings.php';
require_once 'includes/wordpress-popup-plugin-metabox.php';

require_once 'includes/lib/class-wordpress-popup-plugin-admin-api.php';
require_once 'includes/lib/class-wordpress-popup-plugin-post-type.php';
require_once 'includes/lib/class-wordpress-popup-plugin-taxonomy.php';

// Load plugin template files.
require_once 'template/popup_temp.php';

function wordpress_popup_plugin() {
	$instance = WordPress_Popup_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WordPress_Popup_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

wordpress_popup_plugin();