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

	private $user = '';

	/**
	 * Constructor
	 *
	 * @since 0.1
	 * @todo  write these descriptions
	 *
	 */
	public function __construct() { }

	public function run() {
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

		add_action( 'init', array( $this, 'admin_init') );
	}

	public function set_user( $user ) {
		$this->user = $user;
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
	public function activation(){
		$this->user->add_new_role();
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
	public function uninstall() {
		$this->user->delete_roles();
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
		remove_action( 'admin_notices', array( $this, 'update_nag'), 3 );
	}

	public function admin_init() {
		if ( ! current_user_can( 'update_core' ) ) {
			$this->no_update_nag();
		}
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

    }
}

spl_autoload_register( 'frecli_autoloader' );



$freelance_client = new Freelance_Client();

$user = new Users();
$user->run();

$freelance_client->set_user( $user );
$freelance_client->run();

$help_widget = new Help_Widget();

