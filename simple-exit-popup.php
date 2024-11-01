<?php
/*
Plugin Name: Simple exit popup
Plugin URI: http://www.gopiplus.com/work/2020/06/20/simple-exit-popup-wordpress-plugin/
Description: Simple exit pop up plugin that uses jQuery and the Animate style library to display the pop up box when users try to exit the browser window.
Author: Gopi Ramasamy
Version: 1.4
Author URI: http://www.gopiplus.com/work/about/
Donate link: http://www.gopiplus.com/work/2020/06/20/simple-exit-popup-wordpress-plugin/
Tags: exit, popup, plugin
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: simple-exit-popup
Domain Path: /languages
*/

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
	die('You are not allowed to call this page directly.');
}

if(!defined('SEPOPUP_DIR')) 
	define('SEPOPUP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if ( ! defined( 'SEPOPUP_ADMIN_URL' ) )
	define( 'SEPOPUP_ADMIN_URL', admin_url() . 'options-general.php?page=simple-exit-popup' );
	
if (!defined('SEPOPUP_URL')) 
	define('SEPOPUP_URL', plugins_url() . '/' . strtolower('simple-exit-popup') . '/');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'sepopups-register.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'sepopups-query.php');

function sepopups_textdomain() {
	  load_plugin_textdomain( 'simple-exit-popup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('wp_enqueue_scripts', 'sepopups_add_javascript_files');
add_shortcode( 'simple-exit-popup', array( 'sepopups_cls_shortcode', 'sepopups_shortcode' ) );
add_action('plugins_loaded', 'sepopups_textdomain');
add_action('admin_enqueue_scripts', array('sepopups_cls_registerhook', 'sepopups_adminscripts'));
add_action('admin_menu', array('sepopups_cls_registerhook', 'sepopups_addtomenu'));
add_filter( 'wp_head', array( 'sepopups_cls_registerhook', 'sepopups_popupstyle' ));

register_activation_hook(SEPOPUP_DIR . 'simple-exit-popup.php', array('sepopups_cls_registerhook', 'sepopups_activation'));
register_deactivation_hook(SEPOPUP_DIR . 'simple-exit-popup.php', array('sepopups_cls_registerhook', 'sepopups_deactivation'));
?>