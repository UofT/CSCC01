<?php

require_once('Base.php');

/** 
 * @author pedrotobo
 * 
 * 
 */

class Course extends Base {
	protected $tbl;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		Zend_Db_Table::setDefaultAdapter($this->db);
		$this->tbl = new Zend_Db_Table('Courses');
	}

	/**
	 * Create a new course
	 *
	 * @param string $courseid
	 * @param string $coursename
	 * @param string $coursedesc
	 *
	 * @return boolean
	 */
	public function create($courseid, $coursename, $coursedesc) {
		if ($courseid != "") {
			if ($coursename != "") {
				if ($this->getCourse($courseid) != null) {
					throw new Exception('Course id already exists');
				} else {
					$this->tbl
							->insert(
									array('course_id' => $courseid,
											'course_name' => $coursename,
											'course_description' => $coursedesc));

					return true;
				}
			} else {
				throw new Exception('Empty course name');
			}
		} else {
			throw new Exception('Empty user course id');
		}
	}

	/**
	 * Get a course information
	 *
	 * @param string $courseid
	 * @throws Exception
	 *
	 * @return boolean|null
	 */
	public function getCourse($courseid) {
		if ($courseid != "") {
			$rowset = $this->tbl->find($courseid);

			if (count($rowset) > 0) {
				$row = $rowset->current();

				$res = new CourseData();

				$res->courseid = $row['Course_Id'];
				$res->coursename = $row['Course_Name'];
				$res->coursedesc = $row['Course_Description'];

				return $res;
			} else {
				return null;
			}
		} else {
			throw new Exception('Empty course id');
		}
	}
	
	/**
	 * Get a rated courses
	 *
	 *@param string $courseid
	 *
	 * @return CourseData
	 */
	public function getCourseRated($courseid) {
		$select = $this->db->select()->from('Course_Registration')
				->where('Course_Id = ?', $courseid);
		$rowset = $select->query()->fetchAll();
	
		if (count($rowset) > 0) {
			$row = $rowset[0];
		
			$rs = new CourseData();
	
			$rs->courseid = $row['Course_Id'];
			$rs->coursecomment = $row['Comment'];
			$rs->courserate = $row['Course_Rate'];
		}
	
		return $rs;
	}

	/**
	 * Search for a course
	 * 
	 * @param string $criteria
	 *
	 * @return CourseData[]
	 */
	public function search($criteria) {
		$rowset = $this->tbl
				->fetchAll(
						$this->tbl->select()
								->where('Course_Id LIKE ?',
										'%' . $criteria . '%')
								->orWhere('Course_Name LIKE ?',
										'%' . $criteria . '%'));

		$res[] = new CourseData();

		foreach ($rowset as $row) {
			$rs = new CourseData();

			$rs->courseid = $row['Course_Id'];
			$rs->coursename = $row['Course_Name'];
			$rs->coursedesc = $row['Course_Description'];

			array_push($res, $rs);
		}

		array_shift($res);

		return $res;
	}

	/**
	 * Search for a course with rate
	 *
	 * @param string $criteria
	 *
	 * @return CourseData[]
	 */
	public function searchRated($criteria) {
		$select = $this->db->select()->from('course_ratings')
				->where('Course_Id LIKE ?', '%' . $criteria . '%')
				->orWhere('Course_Name LIKE ?', '%' . $criteria . '%');
		$rowset = $select->query()->fetchAll();

		$res[] = new CourseData();

		foreach ($rowset as $row) {
			$rs = new CourseData();

			$rs->courseid = $row['Course_Id'];
			$rs->coursename = $row['Course_Name'];
			$rs->coursedesc = $row['Course_Description'];
			$rs->courserate = $row['Rate'];

			array_push($res, $rs);
		}

		array_shift($res);

		return $res;
	}

	/**
	 * Get all courses
	 *
	 * @return CourseData[]
	 */
	public function showAll() {
		$rowset = $this->tbl->fetchAll();

		$res[] = new CourseData();

		foreach ($rowset as $row) {
			$rs = new CourseData();

			$rs->courseid = $row['Course_Id'];
			$rs->coursename = $row['Course_Name'];
			$rs->coursedesc = $row['Course_Description'];

			array_push($res, $rs);
		}

		array_shift($res);

		return $res;
	}

	/**
	 * Get all rated courses
	 *
	 * @return CourseData[]
	 */
	public function showAllRated() {
		$select = $this->db->select()->from('course_ratings');
		$rowset = $select->query()->fetchAll();

		$res[] = new CourseData();

		foreach ($rowset as $row) {
			$rs = new CourseData();

			$rs->courseid = $row['Course_Id'];
			$rs->coursename = $row['Course_Name'];
			$rs->coursedesc = $row['Course_Description'];
			$rs->courserate = $row['Rate'];

			array_push($res, $rs);
		}

		array_shift($res);

		return $res;
	}

	/**
	 * Update course info
	 *
	 * @param string $courseid
	 * @param string $coursename
	 * @param string $coursedesc
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function update($courseid, $coursename, $coursedesc) {
		if (!$this->getCourse($courseid)) {
			throw new Exception('Course does not exists');
		} else {
			$data = array('Course_Id' => $courseid,
					'Course_Name' => $coursename,
					'Course_Description' => $coursedesc);

			$where = $this->tbl->getAdapter()
					->quoteInto('Course_Id = ?', $courseid);
			$this->tbl->update($data, $where);
			return true;
		}
	}

	/**
	 * Delete course
	 *
	 * @param string $courseid
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function delete($courseid) {
		if (!$this->getCourse($courseid)) {
			throw new Exception('Course does not exists');
		} else {
			$where = $this->tbl->getAdapter()
					->quoteInto('Course_Id = ?', $courseid);
			$this->tbl->delete($where);
			return true;
		}
	}

	/**
	 * Register a student in the course
	 *
	 * @param string $courseid
	 * @param string $coursename
	 * @param string $coursedesc
	 *
	 * @return boolean
	 */
	public function register($username, $courseid) {
		if ($courseid != "") {
			if ($username != "") {
				$tbl = new Zend_Db_Table('Course_Registration');

				$tbl
						->insert(
								array('Course_Id' => $courseid,
										'userlogin' => $username));

				return true;

			} else {
				throw new Exception('Empty user name');
			}
		} else {
			throw new Exception('Empty user course id');
		}
	}

	/**
	 * Get a user courses registration
	 *
	 * @param string $username
	 * @throws Exception
	 *
	 * @return CourseData[]
	 */
	public function getUserCourses($username) {
		if ($username != "") {
			$select = $this->db->select()->from('course_registrations')
					->where('userlogin = ?', $username);
			$rowset = $select->query()->fetchAll();

			$res[] = new CourseData();

			foreach ($rowset as $row) {
				$rs = new CourseData();

				$rs->courseid = $row['Course_Id'];
				$rs->coursename = $row['Course_Name'];
				$rs->coursedesc = $row['Course_Description'];

				array_push($res, $rs);
			}

			array_shift($res);

			return $res;
		} else {
			throw new Exception('Empty user name');
		}
	}

	/**
	 * Update course info
	 *
	 * @param string $courseid
	 * @param string $coursename
	 * @param string $coursedesc
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function updateCommentRate($courseid, $coursecomment, $courserate) {
		if (!$this->getCourse($courseid)) {
			throw new Exception('Course does not exists');
		} else {
			$tbl = new Zend_Db_Table('Course_Registration');

			$data = array('Course_Rate' => $courserate,
					'Comment' => $coursecomment);

			$where = $tbl->getAdapter()->quoteInto('Course_Id = ?', $courseid);
			$tbl->update($data, $where);
			return true;
		}
	}
}

/**
 * @author pedrotobo
 *
 */
Class CourseData {
	/**
	 * @var string $courseid
	 */
	public $courseid;

	/**
	 * @var string $coursename
	 */
	public $coursename;

	/**
	 * @var string $coursedesc
	 */
	public $coursedesc;

	/**
	 * @var float $courserate
	 */
	public $courserate;
	
	/**
	 * @var string $coursecomment
	 */
	public $coursecomment;
}

?>