<?php

/**
 * Admin ajax functions to be tested
 */
require_once( ABSPATH . 'wp-admin/includes/ajax-actions.php' );

class ValidatedAjax extends WP_Ajax_UnitTestCase {

	/**
	 * Post ID
	 * @var int 
	 */
	var $pid;

	/**
	 * Set up the test fixture
	 */
	public function setUp() {
		parent::setUp();
		$this->pid = $this->factory->post->create( array(
			'post_type'		 => 'page',
			'post_title'	 => 'Test Post',
			'post_content'	 => 'Some text'
		) );

		// Become an administrator
		$this->_setRole( 'administrator' );

		// Set up a default request
		$_POST[ 'security' ] = wp_create_nonce( 'validated_security' );
		$_POST[ 'action' ]	 = 'validated';
		$_POST[ 'post_id' ]	 = $this->pid;
	}

	function tearDown() {
		wp_delete_post( $this->pid );
		parent::tearDown();
	}

	/**
	 * Test out validating http://example.org/?page_id=* as a public URL.
	 */
	function test_ajax() {


		// Make the request
		try {
			$this->_handleAjax( 'validated' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}
		$response = json_decode( $this->_last_response );

		$this->assertTrue( $response->success );
	}

	/**
	 * Test out validating http://example.org/?page_id=* as a private URL.
	 */
	function test_ajax_local() {
		// Activate Local Dev Testing
		if ( !defined( 'VALIDATED_LOCAL' ) ) {
			define( 'VALIDATED_LOCAL', true );
		}

		// Make the request
		try {
			$this->_handleAjax( 'validated' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response );
		$this->assertTrue( $response->success );
	}

}