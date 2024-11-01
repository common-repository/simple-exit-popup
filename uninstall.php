<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('simple-exit-popup');
 
// for site options in Multisite
delete_site_option('simple-exit-popup');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sepopups");