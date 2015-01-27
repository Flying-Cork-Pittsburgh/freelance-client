<?php
/**
* description of package
*
* @package Freelance_Client
* @subpackage Widgets
* @author Andrew Woods <awoods>
*
*
*
*/
class Help_Widget extends WP_Widget {

	protected $title = '';
	protected $title_label = '';
	protected $content_label = '';

	protected $colors = array();

	function __construct() {
		$this->title = __( 'Customer Service', 'frecli' );
		$this->title_label = __( 'Customer Service by Awesome Studios', 'frecli' );
		$this->content_label = __( 'Content', 'frecli' );

		$widget_options = array();
		$widget_options['title'] = $this->title;
		$widget_options['description'] = __( 'Enter your contact information for the client', 'frecli' );

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_setup') );

		parent::__construct( NULL, __( $this->title ), $widget_options );
	}

	public function dashboard_setup() {
		$help_widget = 'frecli-help-dashboard-widget';

		wp_add_dashboard_widget(
			$help_widget,
			$this->title_label,
			array(&$this, 'help_dashboard_content')
		);

	 	// Globalize the metaboxes array, this holds all the widgets for wp-admin
		global $wp_meta_boxes;
		
		// Get the regular dashboard widgets array 
		// (which has our new widget already but at the end)
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		
		// Backup and delete our new dashboard widget from the end of the array
		$help_widget_backup = array( $help_widget => $normal_dashboard[$help_widget] );
		unset( $normal_dashboard[$help_widget] );
	 
		// Merge the two arrays together so our widget is at the beginning
		$sorted_dashboard = array_merge( $help_widget_backup, $normal_dashboard );
	 
		// Save the sorted array back into the original metaboxes 
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

	}


	public function help_dashboard_content() {
		$phone = get_option('frecli_help_phone', '206-555-1212');
		$email = get_option('frecli_help_email', 'help@example.org');
		?>
		<p>Need help with your website? It is developed and maintained by Awesome Studios!</p>
		<ul>
		<li><strong><?php echo _e('Phone'); ?></strong>: <?php echo $phone ?></li>
		<li><strong><?php echo _e('Email'); ?></strong>: <?php echo $email ?></li>
		</ul>
		<?php
	}

}


