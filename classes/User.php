<?php

require_once('Base.php');

/**
 * @author pedrotobo
 *
 *
 */

class User extends Base {
	protected $tbl;

	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
		Zend_Db_Table::setDefaultAdapter($this->db);
		$this->tbl = new Zend_Db_Table('Users');
	}

	/**
	 * Create a new user
	 * 
	 * @param string $username
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $password
	 * @param string $role
	 * 
	 * @return boolean
	 */
	public function create($username, $firstname, $lastname, $password, $role) {
		if ($username != "") {
			if ($password != "") {
				if ($role != "") {
					if ($this->getUser($username) != null) {
						throw new Exception('Userlogin already exists');
					} else {
						$this->tbl
								->insert(
										array('userlogin' => $username,
												'first_name' => $firstname,
												'last_name' => $lastname,
												'password' => md5($password),
												'role' => $role));

						return true;
					}
				} else {
					throw new Exception('Empty role');
				}
			} else {
				throw new Exception('Empty password');
			}
		} else {
			throw new Exception('Empty user name');
		}
	}

	/**
	 * Get a user information
	 * 
	 * @param string $username
	 * @throws Exception
	 * 
	 * @return boolean|null
	 */
	public function getUser($username) {
		if ($username != "") {
			$rowset = $this->tbl->find($username);

			if (count($rowset) > 0) {
				$row = $rowset->current();

				$res = new UserData();

				$res->userlogin = $row['userlogin'];
				$res->firstname = $row['first_name'];
				$res->lastname = $row['last_name'];
				$res->password = $row['password'];
				$res->role = $row['role'];

				return $res;
			} else {
				return null;
			}
		} else {
			throw new Exception('Empty user name');
		}
	}

	/**
	 * Get all students
	 * 
	 * @return UserData[]
	 */
	public function showAllStudents() {
		$rowset = $this->tbl->fetchAll("role = 'st'");

		$res[] = new UserData();

		foreach ($rowset as $row) {
			$rs = new UserData();

			$rs->userlogin = $row['userlogin'];
			$res->firstname = $row['first_name'];
			$res->lastname = $row['last_name'];
			$rs->password = $row['password'];
			$rs->role = $row['role'];

			array_push($res, $rs);
		}

		array_shift($res);

		return $res;
	}

	/**
	 * Get all tutors by student courses
	 * 
	 * @param string $username
	 * 
	 * @return UserData[]
	 */ 
	public function showAllTutors($username) {
		$select = $this->db->select()
				->from(array('c' => 'Course_Registration'), array('Course_Id'))
				->where('userlogin = ?', $username);

		$rowset = $select->query()->fetchAll();

		if (count($rowset) > 0) {
			$select = $this->db->select()->from('tutor_ratings')
					->where('Course_Id IN(?)', $rowset);

			$rowset = $select->query()->fetchAll();

			$res[] = new UserData();

			foreach ($rowset as $row) {
				$rs = new UserData();

				$rs->userlogin = $row['userlogin'];
				$rs->firstname = $row['first_name'];
				$rs->lastname = $row['last_name'];
				$rs->tutordesc = $row['description'];
				$rs->tutorrate = $row['Rate'];

				array_push($res, $rs);
			}

			array_shift($res);

			return $res;
		}
	}

	/**
	 * Update user info
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $role
	 * @throws Exception
	 * 
	 * @return boolean
	 */
	public function updateUser($username, $firstname, $lastname, $password,
			$role) {
		if (!$this->getUser($username)) {
			throw new Exception('User does not exists');
		} else {
			$data = array('userlogin' => $username, 'first_name' => $firstname,
					'last_name' => $lastname, 'password' => md5($password),
					'role' => $role);

			$where = $this->tbl->getAdapter()
					->quoteInto('userlogin = ?', $username);
			$this->tbl->update($data, $where);
			return true;
		}
	}

	/**
	 * Delete user
	 * 
	 * @param string $username
	 * @throws Exception
	 * 
	 * @return boolean
	 */
	public function delete($username) {
		if (!$this->getUser($username)) {
			throw new Exception('User does not exists');
		} else {
			$this->tbl->delete(array('userlogin' => $username));
			return true;
		}
	}

	/**
	 * 
	 * @param string $username
	 * @param string $password
	 * 
	 * @return boolean|string
	 */
	public function authenticate($username, $password) {
		$auth = new Zend_Auth_Adapter_DbTable($this->db, 'Users', 'userlogin',
				'password', 'MD5(?)' //'MD5(?) AND active = "TRUE"'
		);

		$auth->setIdentity($username)->setCredential($password);

		$result = $auth->authenticate();

		switch ($result->getCode()) {

		case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
			return 'Invalid user name';
			break;

		case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
			return 'Invalid password';
			break;

		case Zend_Auth_Result::SUCCESS:
			return true;
			break;

		default:
			return 'Error';
			break;
		}
	}

	/**
	 * Get a user information
	 *
	 * @param string $username
	 * @throws Exception
	 *
	 * @return string|null
	 */
	public function getRole($username) {
		if ($username != "") {
			$row = $this->tbl->fetchRow("userlogin = '$username'");

			if ($row != null) {
				return $row['role'];
			} else {
				return null;
			}
		} else {
			throw new Exception('Empty user name');
		}
	}

	/**
	 * Contact a tutor
	 *
	 * @param string $tutorid
	 * @param string $username
	 *
	 * @return boolean
	 */
	public function contact($username, $tutorid) {
		if ($tutorid != "") {
			if ($username != "") {
				$tbl = new Zend_Db_Table('Tutor_Registration');

				$tbl
						->insert(
								array('tutorid' => $tutorid,
										'userlogin' => $username));

				return true;

			} else {
				throw new Exception('Empty user id');
			}
		} else {
			throw new Exception('Empty tutor id');
		}
	}

	/**
	 * Get a user courses registration
	 *
	 * @param string $username
	 * @throws Exception
	 *
	 * @return UserData[]
	 */
	public function getUserTutors($username) {
		if ($username != "") {
			$select = $this->db->select()->from('tutor_registrations')
					->where('userlogin = ?', $username);
			$rowset = $select->query()->fetchAll();

			$res[] = new UserData();

			foreach ($rowset as $row) {
				$rs = new UserData();

				$rs->userlogin = $row['tutorid'];
				$rs->firstname = $row['first_name'];
				$rs->lastname = $row['last_name'];
				$rs->tutordesc = $row['description'];

				array_push($res, $rs);
			}

			array_shift($res);

			return $res;
		} else {
			throw new Exception('Empty user name');
		}
	}

	/**
	 * Get tutor rated
	 *
	 * @param string $tutorid
	 * @param string $userlogin
	 *
	 * @return UserData[]
	 */
	public function getTutorRated($tutorid, $userlogin) {
		$select = $this->db->select()->from('Tutor_Registration')
				->where('tutorid = ?', $tutorid)->where('userlogin = ?', $userlogin);

		$rowset = $select->query()->fetchAll();

		if (count($rowset) > 0) {
			$row = $rowset[0];
			
			$res = new UserData();

			$res->userlogin = $row['tutorid'];
			$res->tutordesc = $row['Comment'];
			$res->tutorrate = $row['Tutor_Rate'];
		}

		return $res;
	}
}

/**
 * @author pedrotobo
 *
 */
Class UserData {
	/**
	 * @var string $userlogin 
	 */
	public $userlogin;

	/**
	 * @var string $firstname
	 */
	public $firstname;

	/**
	 * @var string $lastname
	 */
	public $lastname;

	/**
	 * @var string $password
	 */
	public $password;

	/**
	 * @var string $role
	 */
	public $role;

	/**
	 * @var string $tutorrate
	 */
	public $tutorrate;

	/**
	 * @var string $tutordesc
	 */
	public $tutordesc;
}
?>