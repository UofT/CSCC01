<?php

require_once 'classes/Course.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Course test case.
 */
class CourseTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Course
	 */
	private $Course;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated CourseTest::setUp()
		
		$this->Course = new Course(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated CourseTest::tearDown()
		$this->Course = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Course->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated CourseTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->Course->__construct(/* parameters */);
	}
	
	/**
	 * Tests Course->create()
	 */
	public function testCreate() {
		// TODO Auto-generated CourseTest->testCreate()
		$this->markTestIncomplete ( "create test not implemented" );
		
		$this->Course->create(/* parameters */);
	}
	
	/**
	 * Tests Course->getCourse()
	 */
	public function testGetCourse() {
		// TODO Auto-generated CourseTest->testGetCourse()
		$this->markTestIncomplete ( "getCourse test not implemented" );
		
		$this->Course->getCourse(/* parameters */);
	}
	
	/**
	 * Tests Course->getCourseRated()
	 */
	public function testGetCourseRated() {
		// TODO Auto-generated CourseTest->testGetCourseRated()
		$this->markTestIncomplete ( "getCourseRated test not implemented" );
		
		$this->Course->getCourseRated(/* parameters */);
	}
	
	/**
	 * Tests Course->search()
	 */
	public function testSearch() {
		// TODO Auto-generated CourseTest->testSearch()
		$this->markTestIncomplete ( "search test not implemented" );
		
		$this->Course->search(/* parameters */);
	}
	
	/**
	 * Tests Course->searchRated()
	 */
	public function testSearchRated() {
		// TODO Auto-generated CourseTest->testSearchRated()
		$this->markTestIncomplete ( "searchRated test not implemented" );
		
		$this->Course->searchRated(/* parameters */);
	}
	
	/**
	 * Tests Course->showAll()
	 */
	public function testShowAll() {
		// TODO Auto-generated CourseTest->testShowAll()
		$this->markTestIncomplete ( "showAll test not implemented" );
		
		$this->Course->showAll(/* parameters */);
	}
	
	/**
	 * Tests Course->showAllRated()
	 */
	public function testShowAllRated() {
		// TODO Auto-generated CourseTest->testShowAllRated()
		$this->markTestIncomplete ( "showAllRated test not implemented" );
		
		$this->Course->showAllRated(/* parameters */);
	}
	
	/**
	 * Tests Course->update()
	 */
	public function testUpdate() {
		// TODO Auto-generated CourseTest->testUpdate()
		$this->markTestIncomplete ( "update test not implemented" );
		
		$this->Course->update(/* parameters */);
	}
	
	/**
	 * Tests Course->delete()
	 */
	public function testDelete() {
		// TODO Auto-generated CourseTest->testDelete()
		$this->markTestIncomplete ( "delete test not implemented" );
		
		$this->Course->delete(/* parameters */);
	}
	
	/**
	 * Tests Course->register()
	 */
	public function testRegister() {
		// TODO Auto-generated CourseTest->testRegister()
		$this->markTestIncomplete ( "register test not implemented" );
		
		$this->Course->register(/* parameters */);
	}
	
	/**
	 * Tests Course->tutorRegister()
	 */
	public function testTutorRegister() {
		// TODO Auto-generated CourseTest->testTutorRegister()
		$this->markTestIncomplete ( "tutorRegister test not implemented" );
		
		$this->Course->tutorRegister(/* parameters */);
	}
	
	/**
	 * Tests Course->getUserCourses()
	 */
	public function testGetUserCourses() {
		// TODO Auto-generated CourseTest->testGetUserCourses()
		$this->markTestIncomplete ( "getUserCourses test not implemented" );
		
		$this->Course->getUserCourses(/* parameters */);
	}
	
	/**
	 * Tests Course->getTutorCourses()
	 */
	public function testGetTutorCourses() {
		// TODO Auto-generated CourseTest->testGetTutorCourses()
		$this->markTestIncomplete ( "getTutorCourses test not implemented" );
		
		$this->Course->getTutorCourses(/* parameters */);
	}
	
	/**
	 * Tests Course->updateCommentRate()
	 */
	public function testUpdateCommentRate() {
		// TODO Auto-generated CourseTest->testUpdateCommentRate()
		$this->markTestIncomplete ( "updateCommentRate test not implemented" );
		
		$this->Course->updateCommentRate(/* parameters */);
	}
}

