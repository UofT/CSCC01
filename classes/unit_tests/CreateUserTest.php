<?php

require_once 'classes/User.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * User test case.
 */
class CreateUserTest extends PHPUnit_Framework_TestCase {
	
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
		
		$this->User = new User ();
		$this->UserData = new UserData();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->User = null;
		$this->UserData = null;
		
		parent::tearDown ();
	}
	public function testUsernameEmptyValidation() {
		$username = '';
		$firstname = '';
		$lastname = '';
		$password = '';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testUsernameFormatValidation() {
		$username = 'user@test.com';
		$firstname = '';
		$lastname = '';
		$password = '';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testFirstnameEmptyValidation() {
		$username = 'user@test.com';
		$firstname = '';
		$lastname = '';
		$password = '';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testLastnameEmptyValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = '';
		$password = '';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordEmptyValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = '';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainNumberValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'abcdefgh';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainLetterValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = '12345678';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordMustContainCapitalLetterValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'abcd1234';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testPasswordLenghtValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd123';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testEmptyRoleValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd123';
		$role = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testInvalidRoleValidation() {
		$username = 'user@test.com';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'xx';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testExistingUserValidation() {
		$username = 'admin@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'st';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->create ( $username, $firstname, $lastname, $password, $role );
	}
	public function testCreateStudent() {
		$username = 'student@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'st';
		
		$this->assertTrue ( $this->User->create ( $username, $firstname, $lastname, $password, $role ) );
	}
	public function testCreateTutor() {
		$username = 'tutor@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'tu';
		
		$this->assertTrue ( $this->User->create ( $username, $firstname, $lastname, $password, $role ) );
	}
	public function testUserActivationEmptyKeyValidation() {
		$this->setExpectedException ( 'InvalidArgumentException' );
		
		$this->User->activate ( '' );
	}
	
	public function testUserActivationInvalidKeyValidation() {
		$this->assertEquals ( '', $this->User->activate ( 'invalidkey' ) );
	}
	
	/**
	 * @depends testCreateStudent
	 */
	public function testUserActivation() {
		$username = 'student@utoronto.ca';
		
		$userData = $this->User->getUser ( $username );
		
		$this->assertEquals ( $username, $this->User->activate ( $userData->activation ), $userData->activation );
	}
	
	public function testGetUserInfoIntegrity() {
		$username = 'student@utoronto.ca';
		$firstname = 'Joe';
		$lastname = 'Doe';
		$password = 'Abcd1234';
		$role = 'st';
		
		$this->UserData->userlogin = $username;
		$this->UserData->firstname = $firstname;
		$this->UserData->lastname = $lastname;
		$this->UserData->password = md5($password);
		$this->UserData->role = $role;
		$this->UserData->activation = '';
		
		$this->assertEquals ( $this->UserData, $this->User->getUser ( $username ) );
	}
	
	public function testAuthenticateEmptyUserValidation() {
		$username = '';
		$password = 'Abcd1234';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->authenticate($username, $password);
	}
	
	public function testAuthenticateEmptyPasswordValidation() {
		$username = 'student@utoronto.ca';
		$password = '';
		
		$this->setExpectedException ( 'InvalidArgumentException' );
	
		$this->User->authenticate($username, $password);
	}

	public function testAuthenticateInvalidUserValidation() {
		$username = 'invaliduser@utoronto.ca';
		$password = 'Abcd1234';
	
		$this->assertEquals ( 'Invalid user name', $this->User->authenticate($username, $password) );
	}
	
	/**
	 * @depends testCreateStudent
	 */
	public function testAuthenticateInvalidPasswordValidation() {
		$username = 'student@utoronto.ca';
		$password = 'Abcd';
	
		$this->assertEquals ( 'Invalid password', $this->User->authenticate($username, $password) );
	}
	
	/**
	 * @depends testCreateStudent
	 */
	public function testAuthenticateUser() {
		$username = 'student@utoronto.ca';
		$password = 'Abcd1234';
	
		$this->assertTrue ( $this->User->authenticate($username, $password) );
	}
	
	/**
	 * @depends testCreateStudent
	 */
	public function testStudentDelete() {
		$username = 'student@utoronto.ca';
	
		$this->assertTrue ( $this->User->delete ( $username) );
	}
	
	/**
	 * @depends testCreateTutor
	 */
	public function testTutorDelete() {
		$username = 'tutor@utoronto.ca';
	
		$this->assertTrue ( $this->User->delete ( $username) );
	}
}

