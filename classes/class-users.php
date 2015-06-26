<?php

class Users
{
	private $role_id = 'site_admin';
	private $role_name = 'Site Administrator';

	public function __construct() {
	}

	public function run() {
		add_filter( 'views_users', array( $this, 'modify_views_remove_administrator' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'remove_admin_js' ) );
	}


	public function remove_admin_js( $hook ) {
		if ( ! current_user_can( 'update_core' ) ) {
			wp_enqueue_script( 'fre-cli', plugin_dir_url( __DIR__ ) . 'js/remove_administrator.js' );
		}
	}


	/**
	 * Remove the 'Administrator' link from the links above the Users listing.
	 *
	 * This filter just edits the links above the table listing of Users.
	 * It doesn't change the rows in the listing of Users.
	 *
	 * @since 0.1
	 * @todo  write these descriptions
	 * @param  array $views it does something
	 * @return array
	 */
	function modify_views_remove_administrator( $views )
	{
		global $current_user;

		if ( ! current_user_can( 'update_core' ) ){
			unset( $views['administrator'] );
		}
		return $views;
	}

	public function add_new_role() {
		// Check if the role doesn't exist
		if ( NULL === get_role( $this->role_id ) ) {
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

			add_role( $this->role_id, $this->role_name, $capabilities );
		}

	}

	public function delete_roles() {
		remove_role( $this->role_id );
	}

}
