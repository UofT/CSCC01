<?php

require_once ('Base.php');
require_once ('Notification.php');

/**
 *
 * @author pedrotobo
 *        
 *        
 */
class User extends Base {
	protected $tbl;
	
	/**
	 */
	public function __construct() {
		parent::__construct ();
		Zend_Db_Table::setDefaultAdapter ( $this->db );
		$this->tbl = new Zend_Db_Table ( 'Users' );
	}
	
	/**
	 * Create a new user
	 *
	 * @param string $username        	
	 * @param string $firstname        	
	 * @param string $lastname        	
	 * @param string $password        	
	 * @param string $role  
	 * @throws InvalidArgumentException       	
	 *
	 * @return boolean
	 */
	public function create($username, $firstname, $lastname, $password, $role) {
		if ($username != "") {
			if ($firstname != "") {
				if ($lastname != "") {	
					if (filter_var ( $username, FILTER_VALIDATE_EMAIL )) {
						if ($password != "") {
							if (preg_match ( '#[0-9]#', $password )) {
								if (preg_match ( '/[A-Za-z]/', $password )) {
									if (preg_match ( '/[A-Z]/', $password )) {
										if (strlen( $password ) >= 8) {
											if ($role != "") {
												if (($role == "st") or ($role == "tu")) {
													if ($this->getUser ( $username ) != null) {
														throw new InvalidArgumentException ( 'Userlogin already exists' );
													} else {
														$generatedKey = sha1 ( mt_rand ( 10000, 99999 ) . time () . $username );
														
														$this->tbl->insert ( array (
																'userlogin' => $username,
																'first_name' => $firstname,
																'last_name' => $lastname,
																'password' => md5 ( $password ),
																'role' => $role,
																'activation' => $generatedKey 
														) );
														
														if ($role == 'st') {
															Notification::activationNotice ( $username, $firstname, $generatedKey );
														} else if ($role == 'tu') {
															Notification::approvalNotice ( $username, $firstname );
														}
														
														return true;
													}
												} else {
													throw new InvalidArgumentException ( 'Invalid role' );
												}
											} else {
												throw new InvalidArgumentException ( 'Empty role' );
											}
										} else {
											throw new InvalidArgumentException ( 'Password must be at least 8 characters' );
										}	
									} else {
										throw new InvalidArgumentException ( 'Password must contain a capital letter' );
									}
								} else {
									throw new InvalidArgumentException ( 'Password must contain a letter' );
								}
							} else {
								throw new InvalidArgumentException ( 'Password must contain a number' );
							}
						} else {
							throw new InvalidArgumentException ( 'Empty password' );
						}
					} else {
						throw new InvalidArgumentException ( 'Invalid email' );
					}
				} else {
					throw new InvalidArgumentException ( 'Empty last name' );
				}
			} else {
				throw new InvalidArgumentException ( 'Empty first name' );
			}
		} else {
			throw new InvalidArgumentException ( 'Empty user name' );
		}
	}
	/**
	 *
	 * @param strign $key        	
	 * @throws Exception
	 * @return string
	 */
	public function activate($key) {
		if ($key != "") {
			$select = $this->db->select ()->from ( 'users' )->where ( 'activation = ?', $key );
			$rowset = $select->query ()->fetchAll ();
			
			if (count ( $rowset ) > 0) {
				foreach ( $rowset as $row ) {
					$data = array (
							'activation' => '' 
					);
					
					$where = $this->tbl->getAdapter ()->quoteInto ( 'userlogin = ?', $row ['userlogin'] );
					$this->tbl->update ( $data, $where );
					
					return $row ['userlogin'];
				}
			} else {
				return '';
			}
		} else {
			throw new InvalidArgumentException ( 'Empty key' );
		}
	}
	
	/**
	 * Get a user information
	 *
	 * @param string $username        	
	 * @throws Exception
	 *
	 * @return boolean null
	 */
	public function getUser($username) {
		if ($username != "") {
			$rowset = $this->tbl->find ( $username );
			
			if (count ( $rowset ) > 0) {
				$row = $rowset->current ();
				
				$res = new UserData ();
				
				$res->userlogin = $row ['userlogin'];
				$res->firstname = $row ['first_name'];
				$res->lastname = $row ['last_name'];
				$res->password = $row ['password'];
				$res->role = $row ['role'];
				$res->activation = $row ['activation'];
				
				return $res;
			} else {
				return null;
			}
		} else {
			throw new Exception ( 'Empty user name' );
		}
	}
	
	/**
	 * Get a tutor info
	 *
	 * @param string $username        	
	 * @throws Exception
	 *
	 * @return TutorData
	 */
	public function getTutor($username) {
		if ($username != "") {
			$select = $this->db->select ()->from ( 'tutors' )->where ( 'userlogin = ?', $username );
			$rowset = $select->query ()->fetchAll ();
			
			$rs = new TutorData ();
			
			foreach ( $rowset as $row ) {
				$rs->userlogin = $row ['userlogin'];
				$rs->firstname = $row ['first_name'];
				$rs->lastname = $row ['last_name'];
				$rs->description = $row ['description'];
			}
			
			return $rs;
		} else {
			throw new Exception ( 'Empty user name' );
		}
	}
	
	/**
	 * Get all students
	 *
	 * @return UserData[]
	 */
	public function showAllStudents() {
		$rowset = $this->tbl->fetchAll ( "role = 'st'" );
		
		$res [] = new UserData ();
		
		foreach ( $rowset as $row ) {
			$rs = new UserData ();
			
			$rs->userlogin = $row ['userlogin'];
			$rs->firstname = $row ['first_name'];
			$rs->lastname = $row ['last_name'];
			$rs->password = $row ['password'];
			$rs->role = $row ['role'];
			
			array_push ( $res, $rs );
		}
		
		array_shift ( $res );
		
		return $res;
	}
	
	/**
	 * Get all tutors by student courses
	 *
	 * @param string $username        	
	 *
	 * @return UserData[]
	 */
	public function showAllTutors($username = null) {
		if ($username != null) {
			$select = $this->db->select ()->from ( array (
					'c' => 'Course_Registration' 
			), array (
					'Course_Id' 
			) )->where ( 'userlogin = ?', $username );
			$rowset = $select->query ()->fetchAll ();
		} else {
			$rowset = $this->tbl->fetchAll ( "role = 'tu'" );
		}
		
		if (count ( $rowset ) > 0) {
			if ($username != null) {
				$select = $this->db->select ()->from ( 'tutor_ratings' )->where ( 'Course_Id IN(?)', $rowset );
				$rowset = $select->query ()->fetchAll ();
			}
			
			$res [] = new TutorData ();
			
			foreach ( $rowset as $row ) {
				$rs = new TutorData ();
				
				$rs->userlogin = $row ['userlogin'];
				$rs->firstname = $row ['first_name'];
				$rs->lastname = $row ['last_name'];
				
				if ($username != null) {
					$rs->description = $row ['description'];
					$rs->rate = $row ['Rate'];
					$rs->course = $row ['Course_Id'];
					$rs->coursename = $row ['Course_Name'];
				}
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		}
	}
	/**
	 *
	 * @return UserData
	 */
	public function showPendingApprovals() {
		$where = array (
				$this->tbl->getAdapter ()->quoteInto ( 'role = ?', 'tu' ),
				$this->tbl->getAdapter ()->quoteInto ( 'activation <> ?', '' ) 
		);
		
		$rowset = $this->tbl->fetchAll ( $where );
		
		if (count ( $rowset ) > 0) {
			$res [] = new UserData ();
			
			foreach ( $rowset as $row ) {
				$rs = new UserData ();
				
				$rs->userlogin = $row ['userlogin'];
				$rs->firstname = $row ['first_name'];
				$rs->lastname = $row ['last_name'];
				$rs->activation = $row ['activation'];
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		}
	}
	
	/**
	 * Update user info
	 * 
	 * @param string $username
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $password
	 * @param string $role
	 * @throws InvalidArgumentException
	 * @return boolean
	 */
	public function updateUser($username, $firstname, $lastname, $password, $role) {
		if ($username != "") {
			if ($firstname != "") {
				if ($lastname != "") {	
					if (filter_var ( $username, FILTER_VALIDATE_EMAIL )) {
						if ($password != "") {
							if (preg_match ( '#[0-9]#', $password )) {
								if (preg_match ( '/[A-Za-z]/', $password )) {
									if (preg_match ( '/[A-Z]/', $password )) {
										if (strlen( $password ) >= 8) {
											if ($role != "") {
												if (($role == "st") or ($role == "tu")) {
													if ($this->getUser ( $username ) == null) {
														throw new InvalidArgumentException ( 'User does not exists' );
													} else {
														$data = array (
																'userlogin' => $username,
																'first_name' => $firstname,
																'last_name' => $lastname,
																'password' => md5 ( $password ),
																'role' => $role 
														);
														
														$where = $this->tbl->getAdapter ()->quoteInto ( 'userlogin = ?', $username );
														$this->tbl->update ( $data, $where );
														return true;
													}
												} else {
													throw new InvalidArgumentException ( 'Invalid role' );
												}
											} else {
												throw new InvalidArgumentException ( 'Empty role' );
											}
										} else {
											throw new InvalidArgumentException ( 'Password must be at least 8 characters' );
										}
									} else {
										throw new InvalidArgumentException ( 'Password must contain a capital letter' );
									}
								} else {
									throw new InvalidArgumentException ( 'Password must contain a letter' );
								}
							} else {
								throw new InvalidArgumentException ( 'Password must contain a number' );
							}
						} else {
							throw new InvalidArgumentException ( 'Empty password' );
						}
					} else {
						throw new InvalidArgumentException ( 'Invalid email' );
					}
				} else {
					throw new InvalidArgumentException ( 'Empty last name' );
				}
			} else {
				throw new InvalidArgumentException ( 'Empty first name' );
			}
		} else {
			throw new InvalidArgumentException ( 'Empty user name' );
		}
	}
	
	/**
	 * Update tutor info
	 *
	 * @param string $username        	
	 * @param string $description        	
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function updateTutor($username, $description) {
		if (! $this->getUser ( $username )) {
			throw new Exception ( 'User does not exists' );
		} else {
			$tbl = new Zend_Db_Table ( 'Tutor_Descriptions' );
			
			$data = array (
					'description' => $description 
			);
			
			$where = $tbl->getAdapter ()->quoteInto ( 'userlogin = ?', $username );
			$tbl->update ( $data, $where );
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
		if (! $this->getUser ( $username )) {
			throw new Exception ( 'User does not exists' );
		} else {
			$where = $this->tbl->getAdapter ()->quoteInto ( 'userlogin = ?', $username );
			$this->tbl->delete ( array (
					$where 
			) );
			return true;
		}
	}
	
	/**
	 *
	 * @param string $username        	
	 * @param string $password        	
	 *
	 * @return boolean string
	 */
	public function authenticate($username, $password) {
		if ($username != '') {
			if ($password != '') {
				$auth = new Zend_Auth_Adapter_DbTable ( $this->db, 'Users', 'userlogin', 'password', "MD5(?)" ); // 'MD5(?)
				                                                                                                 // AND
				                                                                                                 // active
				                                                                                                 // =
				                                                                                                 // "TRUE"'
				
				$auth->setIdentity ( $username )->setCredential ( $password );
				
				$result = $auth->authenticate ();
				
				switch ($result->getCode ()) {
					
					case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND :
						return 'Invalid user name';
						break;
					
					case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID :
						return 'Invalid password';
						break;
					
					case Zend_Auth_Result::SUCCESS :
						return true;
						break;
					
					default :
						return 'Error';
						break;
				}
			} else {
				throw new InvalidArgumentException ( 'Empty password' );
			}
		} else {
			throw new InvalidArgumentException ( 'Empty user name' );
		}
	}
	
	/**
	 * Get a user information
	 *
	 * @param string $username        	
	 * @throws Exception
	 *
	 * @return string null
	 */
	public function getRole($username) {
		if ($username != "") {
			$row = $this->tbl->fetchRow ( "userlogin = '$username'" );
			
			if ($row != null) {
				return $row ['role'];
			} else {
				return null;
			}
		} else {
			throw new Exception ( 'Empty user name' );
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
				$tbl = new Zend_Db_Table ( 'Tutor_Registration' );
				
				$tbl->insert ( array (
						'tutorid' => $tutorid,
						'userlogin' => $username 
				) );
				
				return true;
			} else {
				throw new Exception ( 'Empty user id' );
			}
		} else {
			throw new Exception ( 'Empty tutor id' );
		}
	}
	
	/**
	 * Get a user courses registration
	 *
	 * @param string $username        	
	 * @throws Exception
	 *
	 * @return TutorData[]
	 */
	public function getUserTutors($username) {
		if ($username != "") {
			$select = $this->db->select ()->from ( 'tutor_registrations' )->where ( 'userlogin = ?', $username );
			$rowset = $select->query ()->fetchAll ();
			
			$res [] = new TutorData ();
			
			foreach ( $rowset as $row ) {
				$rs = new TutorData ();
				
				$rs->userlogin = $row ['tutorid'];
				$rs->firstname = $row ['first_name'];
				$rs->lastname = $row ['last_name'];
				$rs->description = $row ['description'];
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		} else {
			throw new Exception ( 'Empty user name' );
		}
	}
	
	/**
	 * Get a tutor students registration
	 *
	 * @param string $username        	
	 * @throws Exception
	 *
	 * @return UserData[]
	 */
	public function getTutorStudents($username) {
		if ($username != "") {
			$select = $this->db->select ()->from ( 'students_registration' )->where ( 'tutorid = ?', $username );
			$rowset = $select->query ()->fetchAll ();
			
			$res [] = new UserData ();
			
			foreach ( $rowset as $row ) {
				$rs = new UserData ();
				
				$rs->userlogin = $row ['userlogin'];
				$rs->firstname = $row ['first_name'];
				$rs->lastname = $row ['last_name'];
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		} else {
			throw new Exception ( 'Empty user name' );
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
		$select = $this->db->select ()->from ( 'Tutor_Registration' )->where ( 'tutorid = ?', $tutorid )->where ( 'userlogin = ?', $userlogin );
		
		$rowset = $select->query ()->fetchAll ();
		
		if (count ( $rowset ) > 0) {
			$row = $rowset [0];
			
			$res = new UserData ();
			
			$res->userlogin = $row ['tutorid'];
			$res->tutordesc = $row ['Comment'];
			$res->tutorrate = $row ['Tutor_Rate'];
		}
		
		return $res;
	}
	
	/**
	 * Create tutor schedule
	 *
	 * @param string $username        	
	 * @param string $title        	
	 * @param string $start        	
	 * @param strin $end        	
	 * @throws Exception
	 * @return int
	 */
	public function createSchedule($username, $title, $start, $end) {
		if ($username != "") {
			if ($title != "") {
				if ($start != "") {
					if ($end != "") {
						$tbl = new Zend_Db_Table ( 'Schedules' );
						
						$res = $tbl->insert ( array (
								'userlogin' => $username,
								'title' => $title,
								'start' => $start,
								'end' => $end 
						) );
						
						return $res;
					} else {
						throw new Exception ( 'Empty end date' );
					}
				} else {
					throw new Exception ( 'Empty start date' );
				}
			} else {
				throw new Exception ( 'Empty title' );
			}
		} else {
			throw new Exception ( 'Empty user name' );
		}
	}
	
	/**
	 * Update tutor schedule
	 *
	 * @param string $username        	
	 * @param string $title        	
	 * @param string $start        	
	 * @param strin $end        	
	 * @throws Exception
	 * @return boolean
	 */
	public function updateSchedule($id, $title, $start, $end) {
		if ($id != "") {
			if ($title != "") {
				if ($start != "") {
					if ($end != "") {
						$tbl = new Zend_Db_Table ( 'Schedules' );
						
						$data = array (
								'title' => $title,
								'start' => $start,
								'end' => $end 
						);
						
						$where = $tbl->getAdapter ()->quoteInto ( 'schedule_id = ?', $id );
						$tbl->update ( $data, $where );
						return true;
					} else {
						throw new Exception ( 'Empty end date' );
					}
				} else {
					throw new Exception ( 'Empty start date' );
				}
			} else {
				throw new Exception ( 'Empty title' );
			}
		} else {
			throw new Exception ( 'Empty id' );
		}
	}
	
	/**
	 * Laads a schedule detail
	 *
	 * @param int $id        	
	 * @throws Exception
	 * @return TutorSchedule
	 */
	public function loadSchedule($id) {
		if ($id != "") {
			$select = $this->db->select ()->from ( 'Schedules' )->where ( 'schedule_id = ?', $id );
			$rowset = $select->query ()->fetchAll ();
			
			$rs = new TutorSchedule ();
			
			foreach ( $rowset as $row ) {
				$rs->id = $row ['schedule_id'];
				$rs->userlogin = $row ['userlogin'];
				$rs->title = $row ['title'];
				$rs->start = $row ['start'];
				$rs->end = $row ['end'];
			}
			
			return $rs;
		} else {
			throw new Exception ( 'Empty id' );
		}
	}
	public function deleteSchedule($id) {
		if ($id != "") {
			$tbl = new Zend_Db_Table ( 'Schedules' );
			
			$where = $this->tbl->getAdapter ()->quoteInto ( 'schedule_id = ?', $id );
			$tbl->delete ( array (
					$where 
			) );
			
			return true;
		} else {
			throw new Exception ( 'Empty id' );
		}
	}
	
	/**
	 * Laads tutor's schedules
	 *
	 * @param string $username        	
	 * @throws Exception
	 * @return TutorSchedule
	 */
	public function loadSchedules($username) {
		if ($username != "") {
			$select = $this->db->select ()->from ( 'Schedules' )->where ( 'userlogin = ?', $username );
			$rowset = $select->query ()->fetchAll ();
			
			$res [] = new TutorSchedule ();
			
			foreach ( $rowset as $row ) {
				$rs = new TutorSchedule ();
				
				$rs->id = $row ['schedule_id'];
				$rs->userlogin = $row ['userlogin'];
				$rs->title = $row ['title'];
				$rs->start = $row ['start'];
				$rs->end = $row ['end'];
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		} else {
			throw new Exception ( 'Empty id' );
		}
	}
}

/**
 *
 * @author pedrotobo
 *        
 */
class UserData {
	/**
	 *
	 * @var string $userlogin
	 */
	public $userlogin;
	
	/**
	 *
	 * @var string $firstname
	 */
	public $firstname;
	
	/**
	 *
	 * @var string $lastname
	 */
	public $lastname;
	
	/**
	 *
	 * @var string $password
	 */
	public $password;
	
	/**
	 *
	 * @var string $role
	 */
	public $role;
	
	/**
	 *
	 * @var string $activation
	 */
	public $activation;
}

/**
 *
 * @author pedrotobo
 *        
 */
class TutorData {
	/**
	 *
	 * @var string $userlogin
	 */
	public $userlogin;
	
	/**
	 *
	 * @var string $firstname
	 */
	public $firstname;
	
	/**
	 *
	 * @var string $lastname
	 */
	public $lastname;
	
	/**
	 *
	 * @var string $description
	 */
	public $description;
	
	/**
	 *
	 * @var string $rate
	 */
	public $rate;
	
	/**
	 *
	 * @var string $course
	 */
	public $course;
	
	/**
	 *
	 * @var string $course
	 */
	public $coursename;
}
class TutorSchedule {
	/**
	 *
	 * @var int $id
	 */
	public $id;
	
	/**
	 *
	 * @var int $userlogin
	 */
	public $userlogin;
	
	/**
	 *
	 * @var string $title
	 */
	public $title;
	
	/**
	 *
	 * @var string $start
	 */
	public $start;
	
	/**
	 *
	 * @var string $end
	 */
	public $end;
}
?>