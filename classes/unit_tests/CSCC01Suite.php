<?php

require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'classes/unit_tests/CourseTest.php';

require_once 'classes/unit_tests/NotificationTest.php';

require_once 'classes/unit_tests/UserTest.php';

/**
 * Static test suite.
 */
class CSCC01Suite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'CSCC01Suite' );
		
		$this->addTestSuite ( 'CourseTest' );
		
		$this->addTestSuite ( 'NotificationTest' );
		
		$this->addTestSuite ( 'UserTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

