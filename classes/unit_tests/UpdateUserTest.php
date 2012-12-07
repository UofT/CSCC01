<?php

require_once 'classes/User.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * User test case.
 */
class UpdateUserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var User
	 */
	private $User;
	
	/**
	 *
	 * @var UserData
	 */
	private $UserData;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$_SERVER["SERVER_PORT"] = "80";
		$_SERVER["SERVER_NAME"] = 'localhost';
		$_SERVER["REQUEST_URI"] = '/cscc01/services/User.php';
		
		$this->User = new User();
		$this->UserData = new UserData();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->User = null;
		
		parent::tearDown ();
	}
	
	public function testUsernameEmptyValidation() {
		$username = '';
		$firstname = '';
		$lastname = '';
		$password = '';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testFirstnameEmptyValidation() {
		$username = 'student@utoronto.ca';
		$firstname = '';
		$lastname = '';
		$password = '';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testLastnameEmptyValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = '';
		$password = '';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordEmptyValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = '';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainNumberValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'abcdefgh';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainLetterValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = '12345678';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainCapitalLetterValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'abcd1234';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordLenghtValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd123';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testEmptyRoleValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd123';
		$role = '';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testInvalidRoleValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'xx';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testUpdateNotExistingUserValidation() {
		$username = 'notexisting@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'st';
	
		$this->setExpectedException ( 'InvalidArgumentException' );
		$this->User->updateUser ( $username, $firstname, $lastname, $password, $role );
	}
	public function testUpdateUser() {
		$username = 'student@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'st';
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
		$userData = $this->User->getUser ( $username );
		$this->User->activate ( $userData->activation );
		
		$username = 'student@utoronto.ca';
		$firstname = 'New Joe';
		$lastname = 'New Doe';
		$password = '1234Abcd';
		$role = 'tu';
	
		$this->assertTrue ( $this->User->updateUser ( $username, $firstname, $lastname, $password, $role ) );
	}
	/**
	 * @depends testUpdateUser
	 */
	public function testUpdateUserIntegrity() {
		$username = 'student@utoronto.ca';
		$firstname = 'New Joe';
		$lastname = 'New Doe';
		$password = '1234Abcd';
		$role = 'tu';
	
		$this->UserData->userlogin = $username;
		$this->UserData->firstname = $firstname;
		$this->UserData->lastname = $lastname;
		$this->UserData->password = md5($password);
		$this->UserData->role = $role;
		$this->UserData->activation = '';
	
		$this->assertEquals ( $this->UserData, $this->User->getUser ( $username ) );
		
		$this->User->delete ( $username );
	}
}

