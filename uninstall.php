<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Note_Press
 * @author    datainterlock <postmaster@datainterlock.com>
 * @license   GPL-3.0+
 * @link      http://www.datainterlock.com
 * @Copyright (C) 2015 Rod Kinnison postmaster@datainterlock.com
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
if (is_multisite()) 
{
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	delete_option("Note_Press_db_version");
	if ($blogs) 
	{
		foreach($blogs as $blog) 
		{
			switch_to_blog($blog['blog_id']);
			delete_option("Note_Press_db_version");
			$table_name = $wpdb->prefix . "Note_Press";
			$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."$table_name`");
			$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
			restore_current_blog();
		}
	}
}
else
{
	delete_option("Note_Press_db_version");
	$table_name = $wpdb->prefix . "Note_Press";
	$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."$table_name`");
	$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
}