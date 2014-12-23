<?php
/*
Plugin Name: Mins To Read
Plugin URI: http://www.think201.com
Description: Mins To Read is a plugin which calculates the read time of a blog post based on words present in it.
Author: Think201, Vivek Pandey, Anurag Rath
Version: 1.2
Author URI: http://www.think201.com
License: GPL v1

Mins To Read Plugin
Copyright (C) 2014, Think201 - think201.com@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * @package Main
 */

//start session
if (session_id() == '') {
	session_start();
}

if(version_compare(PHP_VERSION, '5.2', '<' )) 
{
	if (is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX )) 
	{
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		deactivate_plugins( __FILE__ );
		wp_die( sprintf( __( 'Mins To Read requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself.', 'Mins To Read' ), '<a href="http://wordpress.org/">', '</a>' ));
	} 
	else 
	{
		return;
	}
}

if ( !defined( 'MTR_PATH' ) )
define( 'MTR_PATH', plugin_dir_path( __FILE__ ) );

if ( !defined( 'MTR_BASENAME' ) )
define( 'MTR_BASENAME', plugin_basename( __FILE__ ) );

if ( !defined( 'MTR_VERSION' ) )
define('MTR_VERSION', '1.0.5' );

if ( !defined( 'MTRPLUGIN_DIR' ) )
define('MTRPLUGIN_DIR', dirname(__FILE__) );

if ( ! defined( 'MTR_LOAD_JS' ) )
define( 'MTR_LOAD_JS', true );

if ( ! defined( 'MTR_LOAD_CSS' ) )
define( 'MTR_LOAD_CSS', true );

if ( ! defined( 'MTR_MINS_READ' ) )
define( 'MTR_MINS_READ', 'minstoread' );

if ( ! defined( 'MTR_DEFAULTCLASS' ) )
define( 'MTR_DEFAULTCLASS', 'minstoread' );	

if ( ! defined( 'MTR_DEFFLTPOSITION' ) )
define( 'MTR_DEFFLTPOSITION', 'Top Right' );	

if ( ! defined( 'MTR_MINS_READ_DEFLT_VALUE' ) )
define( 'MTR_MINS_READ_DEFLT_VALUE', serialize(array('mtr_reading_speed' => '200')));	


require_once MTRPLUGIN_DIR .'/includes/mtr-install.php';

require_once MTRPLUGIN_DIR .'/includes/mtr-admin.php';
require_once MTRPLUGIN_DIR .'/includes/mtr.php';

register_activation_hook( __FILE__, array('MTR_Install', 'activate') );
register_deactivation_hook( __FILE__, array('MTR_Install', 'deactivate') );
register_uninstall_hook(    __FILE__, array('MTR_Install', 'delete') );

add_action( 'plugins_loaded', 'MinsToReadStart' );

function MinsToReadStart()
{
	$initObj = MTRAdmin::get_instance();
	$initObj->init();

	$mtrObj = MinsToRead::get_instance();
	$mtrObj->init();
}

function mtr_print($Id)
{
	if(!empty($Id) AND get_post_type( $Id ) === 'post')
	{
		MTRView::print_mtr($Id);
	}
}

?>