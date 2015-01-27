<?php
/*
Plugin Name: Freelance Client
Description: Used for your client websites. 
Plugin URI: https://github.com/andrewwoods/freelance-client
Version: 0.1
Author: awoods
Author URI: http://andrewwoods.net
*/


/**
* Primary class for Freelance Client plugin 
*
* Hides the Adminstrator from non Adminstrator users, creates a new Site Adminstrator role
* to assign clients to for their protection. 
*
*
* @package  Freelance_Client
*/
class Freelance_Client {

	private static $instance = null;

	private static $role_id = 'site_admin';
	private static $role_name = 'Site Administrator';

	/**
	 * Constructor
	 *
	 * @since 0.1
	 * @todo  write these descriptions
	 *
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( 'Freelance_Manager', 'activation' ) );
		register_uninstall_hook( __FILE__, array( 'Freelance_Manager', 'uninstall' ) ); 

		add_filter( 'views_users', array( $this, 'modify_views_users_remove_administrator_conditionally' ) );
	}

	/**
	 * Short Description
	 *
	 * Long Description
	 *
	 * @since 0.1
	 * @todo  write these descriptions
	 * @param  array $views it does something
	 * @return array 
	 */
	function modify_views_users_remove_administrator_conditionally( $views )
	{
		// Manipulate $views
		if ( ! current_user_can( 'update_core' ) ){
			unset( $views['administrator'] );
		}
		return $views;
	}

	/**
	 * Performs these tasks when the plugin is activated.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @param  void
	 * @return void
	 */
	public static function activation(){
		self::add_new_role();
	}

	/**
	 * Performs these tasks when the plugin is uninstalled.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @param  void
	 * @return void
	 */
	public static function uninstall() {
		self::delete_roles();
	}

	/**
	* Disable the WordPress update nag for people without that capability
	*
	* Prevent the display of the WordPress update nag for people without that capability
	*
	* @since version 0.1
	*
	* @return void
	*/
	public function no_update_nag() {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}

	public function admin_init() {
		if ( ! current_user_can( 'update_core' ) ) {
			$this->no_update_nag();
		}
	}


	public function add_new_role() {
		// Check if the role doesn't exist
		if ( NULL === get_role( self::$role_id ) ) {
			// add the role 
			$admin_role = get_role( 'editor' ); 
			$capabilities = $admin_role->capabilities;

			$capabilities['activate_plugins'] = 0;
			$capabilities['create_users'] = 1;
			$capabilities['delete_plugins'] = 0;
			$capabilities['delete_themes'] = 0;
			$capabilities['delete_users'] = 1;
			$capabilities['edit_files'] = 1;
			$capabilities['edit_plugins'] = 0;
			$capabilities['edit_theme_options'] = 1;
			$capabilities['edit_themes'] = 0;
			$capabilities['edit_users'] = 1;
			$capabilities['export'] = 1;
			$capabilities['import'] = 0;

			$capabilities['install_plugins'] = 0;
			$capabilities['install_themes'] = 0;
			$capabilities['list_users'] = 1;
			$capabilities['manage_options'] = 1;
			$capabilities['promote_users'] = 1;
			$capabilities['remove_users'] = 1;
			$capabilities['switch_themes'] = 0;
			$capabilities['update_core'] = 0;
			$capabilities['update_plugins'] = 0;
			$capabilities['update_themes'] = 0;
			$capabilities['edit_dashboard'] = 1;

			add_role( self::$role_id, self::$role_name, $capabilities );
		}

	}

	public function delete_roles() {
		remove_role( self::$role_id );	
	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return Freelancer A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

/**
 * Class loader for Freelance Client classes
 *
 * Make it possible for developers to lazy load classes without having to hardcode require statements.
 * Transforms the class name into a file path that gets require_once'd
 *
 * @since 0.1
 *
 * @param  string $class_name  the name of the class to load
 * @return void
 */
function frecli_autoloader( $class_name ) {
    $slug = sanitize_title_with_dashes( $class_name, '', 'save' );
    $slug = str_replace('_', '-', $slug);

    $file = 'class-' . $slug . '.php';
    $file_path = plugin_dir_path( __FILE__ ) . 'classes/' . $file;

    if ( file_exists( $file_path ) ) {
        include_once $file_path;

        if ( WP_DEBUG ) {
            error_log( 'frecli_autoloader loaded filename=' . $file_path );
        }
    }
}

spl_autoload_register( 'frecli_autoloader' );



$freelance_client = new Freelance_Client();
$help_widget = new Help_Widget();

