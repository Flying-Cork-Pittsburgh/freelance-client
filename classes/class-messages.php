<?php
/**
 *
 * @package Freelance_Client
 *
 */
class Messages
{
	private $widget_id;

	public function __construct() {
		$this->widget_id = 'frecli_messages_widget';
	}

	/**
	 * Add the actions and filters associated with Messages
	 *
	 * @since 0.2
	 */
	public function run() {
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup') );
		add_action( 'wp_ajax_nopriv_new_message', array( $this, 'new_message' ) );
	}

	/**
	 * Create a new message in the inbox
	 *
	 * Accepts a message if the client ID matches, and adds it to the "inbox", which is displayed
	 * in the dashboard.
	 *
	 * @param string $_POST['action'] The action to be taken by the client
	 * @param int $_POST['id'] The id of the message from the manager site, needed for future operations
	 * @param string $_POST['message'] The content of the message
	 * @param string $_POST['client_sha'] The SHA1 representing this client
	 * @param string $_POST['manager_sha'] The SHA1 representing the manager of this site
	 * @return json
	 */
	public function new_message() {

		if ( isset( $_POST["action"] ) ) {

			$data = array();
			$data['action']        = wp_kses_data( $_POST['action'] );
			$data['id']            = intval( $_POST['id'] );
			$data['message']       = wp_kses_data( $_POST['message'] );
			$data['client_sha']    = wp_kses_data( $_POST['client_sha'] );
			$data['manager_sha']   = wp_kses_data( $_POST['manager_sha'] );

			if ( FRECLI_CLIENT_ID === $data['client_sha'] ){
				$inbox = get_option( 'frecli_inbox', array() );

				$message = array();
				$message['content'] = $data['message'];
				$message['status']  = 'unread';

				$inbox[ $data['id'] ] = $message;

				update_option( 'frecli_inbox', $inbox );

				status_header( 200 );
				wp_send_json_success( $data );
			} else {
				$error = array();
				$error['message'] = 'ID Mismatch';

				status_header( 401 );
				wp_send_json_error( $error );
			}

			die();
		}
	}



	/**
	 * Creates a dashboard widget and pushses it to the top
	 *
	 * Setup this dashboard help widget, and move it to the top of the page.
	 * and allow it to be editable
	 *
	 * @since 0.2
	 * @global array $wp_meta_boxes
	 *
	 * @return void
	 */
	public function dashboard_setup() {

		wp_add_dashboard_widget(
			$this->widget_id,
			__('Available Messages', 'frecli'),
			array( $this, 'display_widget' )
		);

		// Globalize the metaboxes array, this holds all the widgets for wp-admin
		global $wp_meta_boxes;

		// Get the regular dashboard widgets array
		// (which has our new widget already but at the end)
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

		// Backup and delete our new dashboard widget from the end of the array
		$help_widget_backup = array( $this->widget_id => $normal_dashboard[$this->widget_id] );
		unset( $normal_dashboard[$this->widget_id] );

		// Merge the two arrays together so our widget is at the beginning
		$sorted_dashboard = array_merge( $help_widget_backup, $normal_dashboard );

		// Save the sorted array back into the original metaboxes
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * display web developer messages
	 *
	 * display messages from the website developer in a dashboard widget
	 * Uses stub data.
	 *
	 * @since 0.2
	 *
	 * @return void
	 */
	public function display_widget() {
		?>
		<table class="widefat">
		<thead>
		<tr>
			<th><?php _e( 'Status', 'frecli' ); ?></th>
			<th><?php _e( 'Message', 'frecli' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$inbox = get_option( 'frecli_inbox', array() );
		foreach ( $inbox AS $id => $message ):
		?>
		<tr>
			<td><?php echo $message['status']; ?></td>
			<td><?php echo $message['content']; ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<?php
	}

}


