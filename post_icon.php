<?php
/*
Plugin Name: Post Icon
Description: Add icon to post title
Author: Kir Braslavsky
License: GPL2
Version: 1.0
*/ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define ( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if( is_admin() ){
	include_once( plugin_dir_path( __FILE__ ) . 'classes/PostIconSettingsPage.php' );
	new PostIconSettingsPage;
}

include_once( plugin_dir_path( __FILE__ ) . 'classes/PostIcon.php');

new PostIcon;
?>