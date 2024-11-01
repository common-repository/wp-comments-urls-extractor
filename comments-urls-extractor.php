<?php
/*
Plugin Name: WP Comments URLs Extractor
Plugin URI: http://www.wpmize.com/wordpress-plugins/wordpress-plugin-wp-comments-url-extractor/
Description: Extract URLs from the text field and from the author website field of comments.
Author: WPMize
Author URI: http://www.wpmize.com/
Version: 1.0
Text Domain: wp-comments-urls-extractor
*/

require_once 'comments-urls-extractor-class.php';

$CUE = new CUE();
register_activation_hook( __FILE__, array( $CUE, 'install' ) );
register_deactivation_hook( __FILE__, array( $CUE, 'uninstall' ) );
add_action( 'admin_menu', array( $CUE, 'initialize' ) );
?>