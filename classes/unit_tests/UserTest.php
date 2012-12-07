<?php

require_once 'classes/User.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * User test case.
 */
class UserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var User
	 */
	private $User;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated UserTest::setUp()
		
		$this->User = new User(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated UserTest::tearDown()
		$this->User = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests User->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated UserTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->User->__construct(/* parameters */);
	}
	
	/**
	 * Tests User->create()
	 */
	public function testCreate() {
		// TODO Auto-generated UserTest->testCreate()
		$this->markTestIncomplete ( "create test not implemented" );
		
		$this->User->create(/* parameters */);
	}
	
	/**
	 * Tests User->activate()
	 */
	public function testActivate() {
		// TODO Auto-generated UserTest->testActivate()
		$this->markTestIncomplete ( "activate test not implemented" );
		
		$this->User->activate(/* parameters */);
	}
	
	/**
	 * Tests User->getUser()
	 */
	public function testGetUser() {
		// TODO Auto-generated UserTest->testGetUser()
		$this->markTestIncomplete ( "getUser test not implemented" );
		
		$this->User->getUser(/* parameters */);
	}
	
	/**
	 * Tests User->getTutor()
	 */
	public function testGetTutor() {
		// TODO Auto-generated UserTest->testGetTutor()
		$this->markTestIncomplete ( "getTutor test not implemented" );
		
		$this->User->getTutor(/* parameters */);
	}
	
	/**
	 * Tests User->showAllStudents()
	 */
	public function testShowAllStudents() {
		// TODO Auto-generated UserTest->testShowAllStudents()
		$this->markTestIncomplete ( "showAllStudents test not implemented" );
		
		$this->User->showAllStudents(/* parameters */);
	}
	
	/**
	 * Tests User->showAllTutors()
	 */
	public function testShowAllTutors() {
		// TODO Auto-generated UserTest->testShowAllTutors()
		$this->markTestIncomplete ( "showAllTutors test not implemented" );
		
		$this->User->showAllTutors(/* parameters */);
	}
	
	/**
	 * Tests User->showPendingApprovals()
	 */
	public function testShowPendingApprovals() {
		// TODO Auto-generated UserTest->testShowPendingApprovals()
		$this->markTestIncomplete ( "showPendingApprovals test not implemented" );
		
		$this->User->showPendingApprovals(/* parameters */);
	}
	
	/**
	 * Tests User->updateUser()
	 */
	public function testUpdateUser() {
		// TODO Auto-generated UserTest->testUpdateUser()
		$this->markTestIncomplete ( "updateUser test not implemented" );
		
		$this->User->updateUser(/* parameters */);
	}
	
	/**
	 * Tests User->updateTutor()
	 */
	public function testUpdateTutor() {
		// TODO Auto-generated UserTest->testUpdateTutor()
		$this->markTestIncomplete ( "updateTutor test not implemented" );
		
		$this->User->updateTutor(/* parameters */);
	}
	
	/**
	 * Tests User->delete()
	 */
	public function testDelete() {
		// TODO Auto-generated UserTest->testDelete()
		$this->markTestIncomplete ( "delete test not implemented" );
		
		$this->User->delete(/* parameters */);
	}
	
	/**
	 * Tests User->authenticate()
	 */
	public function testAuthenticate() {
		// TODO Auto-generated UserTest->testAuthenticate()
		$this->markTestIncomplete ( "authenticate test not implemented" );
		
		$this->User->authenticate(/* parameters */);
	}
	
	/**
	 * Tests User->getRole()
	 */
	public function testGetRole() {
		// TODO Auto-generated UserTest->testGetRole()
		$this->markTestIncomplete ( "getRole test not implemented" );
		
		$this->User->getRole(/* parameters */);
	}
	
	/**
	 * Tests User->contact()
	 */
	public function testContact() {
		// TODO Auto-generated UserTest->testContact()
		$this->markTestIncomplete ( "contact test not implemented" );
		
		$this->User->contact(/* parameters */);
	}
	
	/**
	 * Tests User->getUserTutors()
	 */
	public function testGetUserTutors() {
		// TODO Auto-generated UserTest->testGetUserTutors()
		$this->markTestIncomplete ( "getUserTutors test not implemented" );
		
		$this->User->getUserTutors(/* parameters */);
	}
	
	/**
	 * Tests User->getTutorStudents()
	 */
	public function testGetTutorStudents() {
		// TODO Auto-generated UserTest->testGetTutorStudents()
		$this->markTestIncomplete ( "getTutorStudents test not implemented" );
		
		$this->User->getTutorStudents(/* parameters */);
	}
	
	/**
	 * Tests User->getTutorRated()
	 */
	public function testGetTutorRated() {
		// TODO Auto-generated UserTest->testGetTutorRated()
		$this->markTestIncomplete ( "getTutorRated test not implemented" );
		
		$this->User->getTutorRated(/* parameters */);
	}
	
	/**
	 * Tests User->createSchedule()
	 */
	public function testCreateSchedule() {
		// TODO Auto-generated UserTest->testCreateSchedule()
		$this->markTestIncomplete ( "createSchedule test not implemented" );
		
		$this->User->createSchedule(/* parameters */);
	}
	
	/**
	 * Tests User->updateSchedule()
	 */
	public function testUpdateSchedule() {
		// TODO Auto-generated UserTest->testUpdateSchedule()
		$this->markTestIncomplete ( "updateSchedule test not implemented" );
		
		$this->User->updateSchedule(/* parameters */);
	}
	
	/**
	 * Tests User->loadSchedule()
	 */
	public function testLoadSchedule() {
		// TODO Auto-generated UserTest->testLoadSchedule()
		$this->markTestIncomplete ( "loadSchedule test not implemented" );
		
		$this->User->loadSchedule(/* parameters */);
	}
	
	/**
	 * Tests User->deleteSchedule()
	 */
	public function testDeleteSchedule() {
		// TODO Auto-generated UserTest->testDeleteSchedule()
		$this->markTestIncomplete ( "deleteSchedule test not implemented" );
		
		$this->User->deleteSchedule(/* parameters */);
	}
	
	/**
	 * Tests User->loadSchedules()
	 */
	public function testLoadSchedules() {
		// TODO Auto-generated UserTest->testLoadSchedules()
		$this->markTestIncomplete ( "loadSchedules test not implemented" );
		
		$this->User->loadSchedules(/* parameters */);
	}
}

