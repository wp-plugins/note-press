<?php
/**
 * Note_Press.
 *
 * @package   Note_Press
 * @author    datainterlock <postmaster@datainterlock.com>
 * @license   GPL-3.0+
 * @link      http://www.datainterlock.com
 * @Copyright (C) 2015 Rod Kinnison postmaster@datainterlock.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-Note_Press-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 */
class Note_Press {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '0.1.0';

	/**
	 * @TODO - Rename "Note_Press" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'notepress';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		global $wpdb,$Note_Press_db_version;
		// Load plugin text domain	
		add_action( 'init', array( $this, 'Note_Pressload_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'Note_Pressactivate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'Note_Pressenqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'Note_Pressenqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_option("Note_Press_db_version", $Note_Press_db_version, "", "no");

		add_action( 'admin_menu', array( $this, 'Note_Pressadd_plugin_main_menu' ) );
		//add_filter( '@TODO', array( $this, 'filter_method_name' ) );

		$table_name = $wpdb->prefix . "Note_Press";
		$sql="
		CREATE TABLE IF NOT EXISTS `$table_name` (
		  `ID` int(11) NOT NULL auto_increment,
		  `Icon` varchar(50) NOT NULL,
		  `Title` varchar(255) NOT NULL,
		  `Content` mediumtext NOT NULL,
		  `Date` datetime NOT NULL,
		  `AddedBy` varchar(255) NOT NULL,
		  `ViewLevel` varchar(20) NOT NULL,
		  `Category` varchar(100) NOT NULL,
		  PRIMARY KEY  (`ID`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
		";
		$wpdb->query($sql);

	}
	/**
	 * Filter to replace the [caption] shortcode text with HTML5 compliant code
	 *
	 * @return text HTML content describing embedded figure
	 **/
	
	public function Note_Pressadd_plugin_main_menu()
	{
		$icon_url = plugin_dir_url(__FILE__) . 'images/Note_Pressicon.png';
		add_menu_page('Note Press', 'Note Press', 'manage_options', 'Note_Press-Main-Menu', array($this, 'Note_Pressplugin_main_menu'), $icon_url);

	}


	public function Note_Pressplugin_main_menu()
	{
		include_once( 'Note_Pressmenu.php' );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function Note_Pressget_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function Note_Pressget_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function Note_Pressactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::Note_Presssingle_activate();
				}

				restore_current_blog();

			} else {
				self::Note_Presssingle_activate();
			}

		} else {
			self::Note_Presssingle_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function Note_Pressdeactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::Note_Pressget_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::Note_Presssingle_deactivate();

				}

				restore_current_blog();

			} else {
				self::Note_Presssingle_deactivate();
			}

		} else {
			self::Note_Presssingle_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function Note_Pressactivate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::Note_Presssingle_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function Note_Pressget_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function Note_Presssingle_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function Note_Presssingle_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function Note_Pressload_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( 'Note_Press', trailingslashit( WP_LANG_DIR ) . $domain .'/' . $domain . '-' . $locale . '.mo' );
		$loaded = load_plugin_textdomain( 'Note_Press', FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
/*	    if ( ! $loaded ){
               echo "<hr/>";
			   echo "Error: the mo file was not found! ";
			   exit();

        }else{

               echo "<hr/>Debug info:<br/>";
               echo "WPLANG: ". WPLANG;
               echo "<br/>";
               echo "translate test: ". __('Some text','Note_Press');
               exit();
       }
*/	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function Note_Pressenqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function Note_Pressenqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function Note_Pressaction_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function Note_Pressfilter_method_name() {
		// @TODO: Define your filter hook callback here
	}
}



