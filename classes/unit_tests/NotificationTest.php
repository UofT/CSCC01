<?php

require_once 'classes/Notification.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Notification test case.
 */
class NotificationTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Notification
	 */
	private $Notification;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated NotificationTest::setUp()
		
		$this->Notification = new Notification(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated NotificationTest::tearDown()
		$this->Notification = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Notification::activationNotice()
	 */
	public function testActivationNotice() {
		// TODO Auto-generated NotificationTest::testActivationNotice()
		$this->markTestIncomplete ( "activationNotice test not implemented" );
		
		Notification::activationNotice(/* parameters */);
	}
	
	/**
	 * Tests Notification::approvalNotice()
	 */
	public function testApprovalNotice() {
		// TODO Auto-generated NotificationTest::testApprovalNotice()
		$this->markTestIncomplete ( "approvalNotice test not implemented" );
		
		Notification::approvalNotice(/* parameters */);
	}
	
	/**
	 * Tests Notification::sendMessage()
	 */
	public function testSendMessage() {
		// TODO Auto-generated NotificationTest::testSendMessage()
		$this->markTestIncomplete ( "sendMessage test not implemented" );
		
		Notification::sendMessage(/* parameters */);
	}
	
	/**
	 * Tests Notification::getMessage()
	 */
	public function testGetMessage() {
		// TODO Auto-generated NotificationTest::testGetMessage()
		$this->markTestIncomplete ( "getMessage test not implemented" );
		
		Notification::getMessage(/* parameters */);
	}
	
	/**
	 * Tests Notification::getMessages()
	 */
	public function testGetMessages() {
		// TODO Auto-generated NotificationTest::testGetMessages()
		$this->markTestIncomplete ( "getMessages test not implemented" );
		
		Notification::getMessages(/* parameters */);
	}
	
	/**
	 * Tests Notification::markRead()
	 */
	public function testMarkRead() {
		// TODO Auto-generated NotificationTest::testMarkRead()
		$this->markTestIncomplete ( "markRead test not implemented" );
		
		Notification::markRead(/* parameters */);
	}
	
	/**
	 * Tests Notification::deleteMesage()
	 */
	public function testDeleteMesage() {
		// TODO Auto-generated NotificationTest::testDeleteMesage()
		$this->markTestIncomplete ( "deleteMesage test not implemented" );
		
		Notification::deleteMesage(/* parameters */);
	}
}

