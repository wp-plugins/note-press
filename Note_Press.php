<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Note_Press
 * @author    datainterlock <postmaster@datainterlock.com>
 * @license   GPL-3.0+
 * @link      http://www.datainterlock.com
 * @Copyright (C) 2015 Rod Kinnison postmaster@datainterlock.com
 *
 * @wordpress-plugin
 * Plugin Name:       Note Press
 * Plugin URI:        http://www.datainterlock.com
 * Description:       Add, edit and delete multiple notes and display them with icons on the Admin page.
 * Version:           0.1.0
 * Author:            datainterlock
 * Author URI:        http://www.datainterlock.com
 * Text Domain:       Note_Press
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate]
(https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project. Thanks Tom!

Many of the features of the Boilerplate, such as the admin settings, css and js are included 
in this plugin yet are not used in this version. I've went ahead and included them as I do have
plans to use them in the future.
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$Note_Press_db_version = "1.0";

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-Note_Press.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-Note_Press.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Note_Press with the name of the class defined in
 *   `class-Note_Press.php`
 */
register_activation_hook( __FILE__, array( 'Note_Press', 'Note_Pressactivate' ) );
register_deactivation_hook( __FILE__, array( 'Note_Press', 'Note_Pressdeactivate' ) );

/*
 * @TODO:
 *
 * - replace Note_Press with the name of the class defined in
 *   `class-Note_Press.php`
 */
add_action( 'plugins_loaded', array( 'Note_Press', 'Note_Pressget_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-Note_Press-admin.php` with the name of the plugin's admin file
 * - replace Note_Press_Admin with the name of the class defined in
 *   `class-Note_Press-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) 
{
// enable these to add the settings menu option
//	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-Note_Press-admin.php' );
//	add_action( 'plugins_loaded', array( 'Note_Press_Admin', 'get_instance' ) );

}
