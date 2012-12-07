<?php

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'classes/unit_tests/CreateUserTest.php';

require_once 'classes/unit_tests/UpdateUserTest.php';

/**
 * Static test suite.
 */
class UserFeaturesSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$_SERVER["SERVER_PORT"] = "80";
		$_SERVER["SERVER_NAME"] = 'localhost';
		$_SERVER["REQUEST_URI"] = '/cscc01/services/User.php';
		
		$this->setName ( 'UserFeaturesSuite' );
		
		$this->addTestSuite ( 'CreateUserTest' );
		
		$this->addTestSuite ( 'UpdateUserTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

