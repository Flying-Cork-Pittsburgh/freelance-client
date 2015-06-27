<?php
/**
* Help Widget to Display Customer Service information on the dashboard
*
* @package Freelance_Client
* @subpackage Widgets
*
*/
class Help_Widget {

	protected $widget_id = '';
	protected $title = '';
	protected $title_label = '';
	protected $content_label = '';

	/**
	 * Constructor
	 *
	 * Initialize the widget with options
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	function __construct() {
		$this->widget_id = 'frecli-help-widget';
		$this->title = __( 'WordPress Customer Service', 'frecli' );
		$this->title_label = __( 'WordPress Customer Service', 'frecli' );
		$this->content_label = __( 'Content', 'frecli' );

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup') );
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
	 * @return type it does something
	 */
	public function dashboard_setup() {

		wp_add_dashboard_widget(
			$this->widget_id,
			$this->title_label,
			array( $this, 'help_dashboard_content' ),
			array( $this, 'config' )
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
	 * display the content of the dashboard widget
	 *
	 * Retrieve the options from the database and display the contnet.
	 *
	 * @since 0.1
	 * @uses {get_option()}
	 *
	 * @param  option $frecli_help_phone The phone number of the freelancer
	 * @param  option $frecli_help_email The email of the of freelancer
	 * @return void
	 */
	public function help_dashboard_content() {
		$phone =  $this->get_dashboard_widget_option($this->widget_id, 'frecli_help_phone', '206-555-1024');
		$email =  $this->get_dashboard_widget_option($this->widget_id, 'frecli_help_email');
		?>
		<p>Need help with your website? It is developed and maintained by Awesome Studios!</p>
		<ul>
		<li><strong><?php echo _e( 'Phone' ); ?></strong>: <?php echo $phone; ?></li>
		<li><strong><?php echo _e( 'Email' ); ?></strong>: <?php echo $email; ?></li>
		</ul>
		<?php
	}

	/**
	 * Update the options
	 *
	 * Render a form to allow the website developer to enter their contact information
	 *
	 */
	public function config(){
		$phone =  $this->get_dashboard_widget_option($this->widget_id, 'frecli_help_phone', '206-555-1024' );
		$email =  $this->get_dashboard_widget_option($this->widget_id, 'frecli_help_email' );

		if ( isset( $_POST['submit'] ) ) {
			if ( isset( $_POST['frecli_help_phone'] ) ){
				$phone = $_POST['frecli_help_phone'];
			}
			if ( isset( $_POST['frecli_help_email'] ) ) {
				$email = sanitize_email( $_POST['frecli_help_email'] );
			}
		}

		$this->update_options(
			$this->widget_id,
			array(
				'frecli_help_phone' => $phone,
				'frecli_help_email' => $email,
			)
		);

		?>
		<p>Enter the contact information where your client can reach you!</p>
		<p>
		<label for="frecli_help_phone">Phone</label>
		<input type="tel" name="frecli_help_phone" value="<?php echo $phone; ?>"
		placeholder="(206) 555-1234" />
		</p>

		<p>
		<label>Email</label>
		<input type="email" name="frecli_help_email" value="<?php echo $email; ?>"
		placeholder="user@example.com" />
		</p>
		<?php
	}


	/**
	 * Gets the options for a widget of the specified name.
	 *
	 * @param string $widget_id Optional. If provided, will only get options for the specified widget.
	 * @return array An associative array containing the widget's options and values. False if no opts.
	 */
	public function get_dashboard_widget_options( $widget_id = '' )
	{
		$opts = get_option( 'frecli_help_options' );

		// If no widget is specified, return everything
		if ( empty( $widget_id ) ){
			return $opts;
		}

		// If we request a widget and it exists, return it
		if ( isset( $opts[ $widget_id ] ) ){
			return $opts[ $widget_id ];
		}

		// Something went wrong...
		return false;
	}

	/**
	 * Gets one specific option for the specified widget.
	 *
	 * @param string $widget_id
	 * @param string $option
	 * @param string $default Optional. default is null
	 *
	 * @return string|bool
	 */
	public function get_dashboard_widget_option( $widget_id, $option, $default=NULL ) {

		$opts = $this->get_dashboard_widget_options($widget_id);

		// If widget opts dont exist, return false
		if ( ! $opts ){
			return false;
		}

		// Otherwise fetch the option or use default
		if ( isset( $opts[ $option ] ) && ! empty( $opts[ $option ] ) ){
			return $opts[ $option ];
		} else {
			return ( isset( $default ) ) ? $default : false;
		}

	}

	/**
	 * Saves an array of options for a single dashboard widget to the database.
	 * Can also be used to define default values for a widget.
	 *
	 * @param string $widget_id The name of the widget being updated
	 * @param array $args An associative array of options being saved.
	 * @param bool $add_only If true, options will not be added if widget options already exist
	 */
	public function update_options( $widget_id , $args = array(), $add_only = false )
	{
		$opts = $this->get_dashboard_widget_options( 'frecli_help_options' );

		$w_opts = ( isset( $opts[ $widget_id ] ) ) ? $opts[ $widget_id ] : array();

		if ( $add_only ) {
			// Flesh out any missing options (existing ones overwrite new ones)
			$opts[ $widget_id ] = array_merge( $args, $w_opts );
		} else {
			// Merge new options with existing ones, and add it back to the widgets array
			$opts[ $widget_id ] = array_merge( $w_opts, $args );
		}

		// Save the entire widgets array back to the db
		return update_option( 'frecli_help_options', $opts );
	}
}


