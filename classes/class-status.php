<?php

/**
 * Provides an class to interact with Message statuses
 *
 * @version 0.1
 *
 * @package  Freelance_Manager
 */
class Status
{
	// Message Status
	const SENT     = 'sent';
	const NOT_SENT = 'not_sent';
	const READ     = 'read';
	const NOT_READ = 'not_read';

	protected $available = [];


	/**
	 * Constructor
	 *
	 * Initialize some instance variables
	 *
	 * @return void
	 */
	public function __construct() {
		$this->available[ self::SENT ] = __( 'Sent', 'fremgr');
		$this->available[ self::NOT_SENT ] = __( 'Not Sent', 'fremgr');
		$this->available[ self::READ ] = __( 'Read', 'fremgr');
		$this->available[ self::NOT_READ ] = __( 'Not Read', 'fremgr');
	}


	public function is_valid( $value ) {
		return isset( $this->available[ $value ] );
	}
}




