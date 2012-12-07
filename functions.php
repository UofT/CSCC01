<?php
/**
 * PHP WEB CLIENT 
 * Version 1.0.0
 * 
 * Written by Pedro Tobo
 * email: ptobo@nashgroup.com
 * http://www.nashgroup.com/
 * 
 * Copyright (c) 2000-2010 Nash Group
 * 
 * @category UI
 * @package NashWebClient
 * @author Pedro Tobo
 * @copyright (c) 2009-2010 Nash Group
 * @license http://www.nashgroup.com/license.html
 * @version 1.0.0
 * @link http://www.nashgroup.com/
 */

/**
 * Load application parameters
 */
require_once 'setup.php';
require_once 'services/common.php';

define ( 'LIB_DIR', str_replace ( "\\", "/", getcwd () ) . '/libs/' );

/**
 * Set ajax controller class
 */
require_once (LIB_DIR . 'xajax_core/xajax.inc.php');

$xjx = new xajax ();

/**
 */
require_once 'classes/User.php';
require_once 'classes/Course.php';

/**
 * Functions
 */
function signUp($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['firstname'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['lastname'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['email'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['passwd'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$val = $clt->create ( $aFormValues ['email'], $aFormValues ['firstname'], $aFormValues ['lastname'], $aFormValues ['passwd'], $aFormValues ['acct-type'] );
			
			if ($val) {
				$_SESSION ['userlogin'] = $aFormValues ['email'];
				$_SESSION ['username'] = $aFormValues ['firstname'];
				$_SESSION ['userrole'] = $aFormValues ['acct-type'];
				
				if ($aFormValues ['acct-type'] == 'st') {
					$_SESSION ['activation'] = true;
				} else if ($aFormValues ['acct-type'] == 'tu') {
					$_SESSION ['approval'] = true;
				}
				
				$objResponse->script ( 'document.location.href="main.php"' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function accountExists($email) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$val = objectToArray ( $clt->getUser ( $email ) );
			
			if ($val ['userlogin'] != null) {
				$objResponse->script ( 'signUpAccountExists = true;' );
			} else {
				$objResponse->script ( 'signUpAccountExists = false;' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function logIn($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['userlogin'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['password'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$val = $clt->authenticate ( $aFormValues ['userlogin'], $aFormValues ['password'] );
			
			if (! is_string ( $val )) {
				$_SESSION ['userlogin'] = $aFormValues ['userlogin'];
				$_SESSION ['authenticated'] = true;
				// $_SESSION['cookies'] = $clt->_cookies;
				
				$val = objectToArray ( $clt->getUser ( $aFormValues ['userlogin'] ) );
				
				$_SESSION ['userrole'] = $val ['role'];
				$_SESSION ['username'] = $val ['firstname'] . " " . $val ['lastname'];
				
				$objResponse->script ( 'document.location.href="main.php"' );
			} else {
				$objResponse->alert ( $val );
			}
		} catch ( Exception $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function logOut() {
	$objResponse = new xajaxResponse ();
	
	session_destroy ();
	$objResponse->script ( 'document.location.href="index.php"' );
	
	return $objResponse;
}
function showCoursesAdmin() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<form id="course-search-form">
  <div align="right"> <label for="course_id">Search</label>
    <input style="width: 100px" name="course_criteria" type="text" id="course_criteria">
  </div>
</form>
			<table id="courses" class="ui-widget ui-widget-content">
        <thead>
            <tr class="ui-widget-header ">
                <th>Course ID</th>
                <th>Name</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->showAll () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
                <td>' . $val ['id'] . '</td>
                <td>' . $val ['name'] . '</td>
                <td>' . $val ['description'] . '</td>
                <td><button onClick="delCourse(\'' . $val ['id'] . '\');">Delete</button></td>
            </tr>';
			}
			
			$html .= '</tbody>
    </table>
</div></div>
<button id="create-course">Create new course</button>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#create-course" )
            .button()
            .click(function() {
                $( "#dialog-course-form" ).dialog( "open" );
            });
			$( "#course-search-form" ).submit(function() {
  				xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
				return false;
			});' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function deleteCourseAdmin($course) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($course == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new Course ();
			
			$clt->delete ( $course );
			
			$objResponse->script ( 'xajax_showCoursesAdmin();' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showStudentsAdmin() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<form id="student-search-form">
  <div align="right"> <label for="student_criteria">Search</label>
    <input style="width: 100px" name="course_criteria" type="text" id="student_criteria">
  </div>
</form>
			<table id="students" class="ui-widget ui-widget-content">
        <thead>
            <tr class="ui-widget-header ">
                <th>Login</th>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->showAllStudents () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
                <td>' . $val ['userlogin'] . '</td>
                <td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
                <td><button onClick="delStudent(\'' . $val ['userlogin'] . '\');">Delete</button></td>
            </tr>';
			}
			
			$html .= '</tbody>
    </table>
</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#student-search-form" ).submit(function() {
  				xajax_searchStudent(xajax.getFormValues(\'student-search-form\'));
				return false;
			});' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function deleteStudentAdmin($student) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($student == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$clt->delete ( $student );
			$objResponse->script ( 'xajax_showStudentsAdmin();' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showPendingApprovals() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="approvals" class="ui-widget ui-widget-content">
        <thead>
            <tr class="ui-widget-header ">
                <th>Login</th>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->showPendingApprovals () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
                <td>' . $val ['userlogin'] . '</td>
                <td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
                <td><button onClick="approveTutor(\'' . $val ['activation'] . '\');">Approve</button></td>
            </tr>';
			}
			
			$html .= '</tbody>
    </table>
</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function activateUser($key) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($key == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			$clt = new User ();
			
			if ($clt->activate ( $key )) {
				$objResponse->script ( 'xajax_showPendingApprovals();' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showTutorsAdmin() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<form id="tutor-search-form">
  <div align="right"> <label for="tutor_criteria">Search</label>
    <input style="width: 100px" name="tutor_criteria" type="text" id="tutor_criteria">
  </div>
</form>
			<table id="tutors" class="ui-widget ui-widget-content">
        <thead>
            <tr class="ui-widget-header ">
                <th>Login</th>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->showAllTutors () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
                <td>' . $val ['userlogin'] . '</td>
                <td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
                <td><button onClick="delTutor(\'' . $val ['userlogin'] . '\');">Delete</button></td>
            </tr>';
			}
			
			$html .= '</tbody>
    </table>
</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#tutor-search-form" ).submit(function() {
  				xajax_searchStudent(xajax.getFormValues(\'tutor-search-form\'));
				return false;
			});' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function deleteTutorAdmin($tutor) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($tutor == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$clt->delete ( $tutor );
			
			$objResponse->script ( 'xajax_showTutorsAdmin();' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function createCourse($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['course_id'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['course_name'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['course_desc'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new Course ();
			
			$val = $clt->create ( $aFormValues ['course_id'], $aFormValues ['course_name'], $aFormValues ['course_desc'] );
			
			if ($val) {
				$objResponse->script ( 'xajax_showCoursesAdmin();' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function searchCourse($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['course_criteria'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<form id="course-search-form">
  <div align="right"> <label for="course_id">Search</label>
    <input style="width: 100px" name="course_criteria" type="text" id="course_criteria">
  </div>
</form>
			<table id="courses" class="ui-widget ui-widget-content">
        <thead>
            <tr class="ui-widget-header ">
                <th>Course ID</th>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->search ( $aFormValues ['course_criteria'] ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
                <td>' . $val ['id'] . '</td>
                <td>' . $val ['name'] . '</td>
                <td>' . $val ['description'] . '</td>
            </tr>';
			}
			
			$html .= '</tbody>
    </table>
</div></div>
<button id="create-course">Create new course</button>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#create-course" )
            .button()
            .click(function() {
                $( "#dialog-course-form" ).dialog( "open" );
            });
			$( "#course-search-form" ).submit(function() {
  				xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
				return false;
			});' );
		} catch ( Exception $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showCoursesStudent() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget" style="z-index:-1">
			<table id="courses" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Course ID</th>
			<th>Name</th>
			<th>Description</th>
			<th>Rating</th>
			<th>Tutors</th>
			<th>Register</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->showAllRated () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td>' . $val ['id'] . '</td>
				<td>' . $val ['name'] . '</td>
				<td>' . $val ['description'] . '</td>				
				<td><div id="star' . $val ['id'] . '"></div></td>	
				<td>' . $val ['tutors'] . '</td>					
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_courseRegistration(\'' . $_SESSION ['userlogin'] . '\',\'' . $val ['id'] . '\')"></td>
				</tr>';
				
				$scripts .= '$(\'#star' . $val ['id'] . '\').raty({ readOnly : true, score : ' . $val ['rate'] . '});';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( $scripts );
			$objResponse->script ( 'tblCourses = $("#courses").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function searchCourseStudent($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['course_criteria'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<form id="course-search-form">
			<div align="right"> <label for="course_id">Search</label>
			<input style="width: 100px" name="course_criteria" type="text" id="course_criteria">
			</div>
			</form>
			<table id="courses" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Course ID</th>
			<th>Name</th>
			<th>Description</th>
			<th>Rating</th>
			<th>Register</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->searchRated ( $aFormValues ['course_criteria'] ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td>' . $val ['id'] . '</td>
				<td>' . $val ['name'] . '</td>
				<td>' . $val ['description'] . '</td>				
				<td><div id="star' . $val ['id'] . '"></div></td>			
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_courseRegistration(\'' . $_SESSION ['userlogin'] . '\',\'' . $val ['id'] . '\')"></td>
				</tr>';
				
				$scripts .= '$(\'#star' . $val ['id'] . '\').raty({ readOnly : true, score : ' . $val ['rate'] . '});';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#course-search-form" ).submit(function() {
					xajax_searchCourseStudent(xajax.getFormValues(\'course-search-form\'));
					return false;});' );
			$objResponse->script ( $scripts );
		} catch ( Exception $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function courseRegistration($email, $courseid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if ($courseid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new Course ();
			
			$val = objectToArray ( $clt->register ( $email, $courseid ) );
			
			if ($val == null) {
				$objResponse->alert ( $val );
			} else {
				$objResponse->alert ( 'Registered for ' . $courseid );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showMyCourses($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="courses" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Course ID</th>
			<th>Name</th>
			<th>Description</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->getUserCourses ( $email ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr onDblClick="showCourse(\'' . $val ['id'] . '\');">
				<td>' . $val ['id'] . '</td>
				<td>' . $val ['name'] . '</td>
				<td>' . $val ['description'] . '</td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#course-search-form" ).submit(function() {
					xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
					return false;
		});' );
			$objResponse->script ( 'tblCourses = $("#courses").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showMyTutors($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="tutors" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Description</th>
			<th>Schedule</th>
			<th>Contact</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->getUserTutors ( $_SESSION ['userlogin'] ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td onDblClick="showTutor(\'' . $val ['userlogin'] . '\');">' . $val ['userlogin'] . '</td>
				<td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
				<td>' . $val ['description'] . '</td>
				<td><button onClick="showTutorSchedule(\'' . $val ['userlogin'] . '\');">Check It</button></td> 
				<td><button onClick="showContactTutor(\'' . $val ['userlogin'] . '\');">Contact</button></td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( 'tblTutors = $("#tutors").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function saveCourseCommentRate($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['course_rating'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new Course ();
			
			$val = $clt->updateCommentRate ( $aFormValues ['course_id'], $aFormValues ['course_comment'], $aFormValues ['course_rating'] );
			
			if ($val == true) {
				$objResponse->script ( '$(\'#course_id\').val(\'\');' );
				$objResponse->script ( '$(\'#course_comment\').val(\'\');' );
				$objResponse->script ( '$(\'#course_rating\').val(\'0\');' );
				
				$objResponse->script ( '$(\'#star\').raty(\'score\', 0);' );
				
				$objResponse->script ( '$(\'#dialog-course-form\').dialog(\'close\');' );
			} else {
				$objResponse->alert ( "Error saving information!!!" );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function loadCourseCommentRate($courseid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($courseid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new Course ();
			
			$val = objectToArray ( $clt->getCourseRated ( $courseid ) );
			
			if ($val) {
				if (is_null ( $val ['rate'] )) {
					$val ['rate'] = 0;
				}
				
				$objResponse->script ( '$(\'#course_id\').val(\'' . $val ['id'] . '\');' );
				$objResponse->script ( '$(\'#course_comment\').val(\'' . $val ['comment'] . '\');' );
				$objResponse->script ( '$(\'#course_rating\').val(\'' . $val ['rate'] . '\');' );
				
				$objResponse->script ( '$(\'#star\').raty(\'score\', ' . $val ['rate'] . ');' );
				
				$objResponse->script ( '$(\'#dialog-course-form\').dialog(\'open\');' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showTutorSchedule($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div style="padding-left: 70px"><div id="calendar"></div></div>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->loadSchedules ( $email ) );
			
			$events = "events: [";
			$sep = "";
			
			foreach ( $vals as $val ) {
				$events .= $sep . "{
					id: " . $val ['id'] . ",
					title: '" . $val ['title'] . "',
					start: " . strtotime ( $val ['start'] ) . ",
					end: " . strtotime ( $val ['end'] ) . ",
					allDay: false
				}";
				
				if ($sep == "") {
					$sep = ",";
				}
			}
			
			$events .= "],";
			
			$objResponse->assign ( 'tutor-schedule', 'innerHTML', $html );
			$objResponse->script ( "var calendar = $('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					height: 390,
					defaultView: 'agendaWeek',
					allDaySlot: false,
					" . $events . "
					selectHelper: true
			});" );
			
			$objResponse->script ( '$(\'#dialog-schedule\').dialog(\'open\');' );
			$objResponse->script ( "$('#calendar').fullCalendar('today');" );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showContactTutor($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$objResponse->script ( '$(\'#div-to\').show();' );
			$objResponse->script ( '$(\'#message_reciever\').html(\'' . $email . '\');' );
			$objResponse->script ( '$(\'#message_to\').val(\'' . $email . '\');' );
			$objResponse->script ( '$(\'#dialog-message\').dialog(\'open\');' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function sendMesage($aFormValues) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($aFormValues ['message_from'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['message_to'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['message_subject'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['message_body'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			Notification::sendMessage ( $aFormValues ['message_from'], $aFormValues ['message_to'], $aFormValues ['message_subject'], $aFormValues ['message_body'] );
			
			$objResponse->script ( '$(\'#div-to\').hide();' );
			$objResponse->script ( '$(\'#dialog-message\').dialog(\'close\');' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showMyMessages($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="messages" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>From</th>
			<th>Date</th>
			<th>Subject</th>
			</tr>
			</thead>
			<tbody>';
			
			$vals = objectToArray ( Notification::getMessages ( $email ) );
			
			foreach ( $vals as $val ) {
				if ($val ['status'] == 'r') {
					$html .= '<tr onClick="showMessage(\'' . $val ['id'] . '\');">';
				} else {
					$html .= '<tr onClick="showMessage(\'' . $val ['id'] . '\');" style="font-weight: bold;">';
				}
				
				$html .= '<td>' . $val ['from'] . '</td>
				<td>' . $val ['date'] . '</td>
				<td>' . $val ['subject'] . '</td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( 'tblMessages = $("#messages").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showMessage($messageid) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($messageid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$val = objectToArray ( Notification::getMessage ( $messageid ) );
			Notification::markRead ( $messageid );
			
			$objResponse->script ( '$(\'#div-from\').show();' );
			$objResponse->script ( '$(\'#message_sender\').html(\'' . $val ['from'] . ' - ' . $val ['date'] . '\');' );
			$objResponse->script ( '$(\'#message_subject\').val(\'' . $val ['subject'] . '\');' );
			$objResponse->script ( '$(\'#message_body\').val(\'' . $val ['message'] . '\');' );
			$objResponse->script ( '$(\'#message_id\').val(\'' . $val ['id'] . '\');' );
			$objResponse->script ( '$(\'#dialog-message\').dialog(\'open\');' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function deleteMesage($aFormValues) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($aFormValues['message_id'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$val = objectToArray ( Notification::deleteMesage ( $aFormValues['message_id'] ) );
			
			$objResponse->script ( '$(\'#div-from\').show();' );
			$objResponse->script ( '$(\'#message_sender\').html(\'\');' );
			$objResponse->script ( '$(\'#message_subject\').val(\'\');' );
			$objResponse->script ( '$(\'#message_body\').val(\'\');' );
			$objResponse->script ( '$(\'#message_id\').val(\'\');' );
			$objResponse->script ( "xajax_showMyMessages('" . $_SESSION ['userlogin'] . "')" );
			$objResponse->script ( '$(\'#dialog-message\').dialog(\'close\');' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showTutors() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="tutors" class="ui-widget ui-widget-content" width="100%">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Description</th>
			<th>Course</th>
			<th>Rating</th>
			<th>Ask for contact</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->showAllTutors ( $_SESSION ['userlogin'] ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td>' . $val ['userlogin'] . '</td>
				<td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
				<td>' . $val ['description'] . '</td>
				<td>' . $val ['course'] . ' - ' . $val ['coursename'] . '</td>
				<td><div id="star' . str_replace ( '.', '', str_replace ( '@', '', $val ['userlogin'] ) ) . $val ['course'] . '"></div></td>
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_tutorContact(\'' . $_SESSION ['userlogin'] . '\',\'' . $val ['userlogin'] . '\')"></td>
				</tr>';
				
				$scripts .= '$(\'#star' . str_replace ( '.', '', str_replace ( '@', '', $val ['userlogin'] ) ) . $val ['course'] . '\').raty({ readOnly : true, score : ' . $val ['rate'] . '});';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#tutor-search-form" ).submit(function() {
					xajax_searchTutor(xajax.getFormValues(\'tutor-search-form\'));
					return false;});' );
			$objResponse->script ( 'tblTutors = $("#tutors").dataTable({
        "bJQueryUI": true,
		"fnDrawCallback": function ( oSettings ) {
            if ( oSettings.aiDisplay.length == 0 )
            {
                return;
            }
             
            var nTrs = $("#tutors tbody tr");
            var iColspan = nTrs[0].getElementsByTagName("td").length;
            var sLastGroup = "";
            for ( var i=0 ; i<nTrs.length ; i++ )
            {
                var iDisplayIndex = oSettings._iDisplayStart + i;
                var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[3];
                if ( sGroup != sLastGroup )
                {
                    var nGroup = document.createElement( "tr" );
                    var nCell = document.createElement( "td" );
                    nCell.colSpan = iColspan;
                    nCell.className = "group";
                    nCell.innerHTML = sGroup;
                    nGroup.appendChild( nCell );
                    nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                    sLastGroup = sGroup;
                }
            }
        },
        "aoColumnDefs": [
            { "bVisible": false, "aTargets": [ 3 ] }
        ],
        "aaSortingFixed": [[ 3, "asc" ]],
    });' );
			$objResponse->script ( $scripts );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function loadTutorCommentRate($tutorid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($tutorid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$val = objectToArray ( $clt->getTutorRated ( $tutorid, $_SESSION ['userlogin'] ) );
			
			if ($val) {
				if (is_null ( $val ['rate'] )) {
					$val ['rate'] = 0;
				}
				
				$objResponse->script ( '$(\'#tutor_id\').val(\'' . $val ['tutorid'] . '\');' );
				$objResponse->script ( '$(\'#tutor_comment\').val(\'' . $val ['tutorcomment'] . '\');' );
				$objResponse->script ( '$(\'#tutor_rating\').val(\'' . $val ['rate'] . '\');' );
				
				$objResponse->script ( '$(\'#startutor\').raty(\'score\', ' . $val ['rate'] . ');' );
				
				$objResponse->script ( '$(\'#dialog-tutor-form\').dialog(\'open\');' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function tutorContact($email, $tutorid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if ($tutorid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			/*
			 * $clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 * $clt->_cookies = $_SESSION['cookies'];
			 */
			$clt = new User ();
			
			$val = objectToArray ( $clt->contact ( $email, $tutorid ) );
			
			if ($val == null) {
				$objResponse->alert ( $val );
			} else {
				$objResponse->alert ( 'Tutor contacted' );
			}
		} catch ( Exception $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showCoursesTutor() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="courses" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Course ID</th>
			<th>Name</th>
			<th>Description</th>
			<th>Rating</th>
			<th>Students</th>
			<th>Register</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->showAllRated () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td>' . $val ['id'] . '</td>
				<td>' . $val ['name'] . '</td>
				<td>' . $val ['description'] . '</td>
				<td><div id="star' . $val ['id'] . '"></div></td>
				<td>' . $val ['students'] . '</td>
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_courseTutorRegistration(\'' . $_SESSION ['userlogin'] . '\',\'' . $val ['id'] . '\')"></td>
				</tr>';
				
				$scripts .= '$(\'#star' . $val ['id'] . '\').raty({ readOnly : true, score : ' . $val ['rate'] . '});';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#course-search-form" ).submit(function() {
					xajax_searchCourseStudent(xajax.getFormValues(\'course-search-form\'));
					return false;});' );
			$objResponse->script ( $scripts );
			$objResponse->script ( 'tblCourses = $("#courses").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showStudents() {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="student" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Ask for contact</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->showAllStudents () );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td>' . $val ['userlogin'] . '</td>
				<td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_tutorContact(\'' . $_SESSION ['userlogin'] . '\',\'' . $val ['userlogin'] . '\')"></td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( 'tblStudent = $("#student").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function courseTutorRegistration($email, $courseid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if ($courseid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		session_start ();
		
		try {
			$clt = new Course ();
			
			$val = objectToArray ( $clt->tutorRegister ( $email, $courseid ) );
			
			if ($val == null) {
				$objResponse->alert ( $val );
			} else {
				$objResponse->alert ( 'Registered for ' . $courseid );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showMyTutorCourses($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="courses" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Course ID</th>
			<th>Name</th>
			<th>Description</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new Course ();
			
			$vals = objectToArray ( $clt->getTutorCourses ( $email ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr onDblClick="showCourse(\'' . $val ['id'] . '\');">
				<td>' . $val ['id'] . '</td>
				<td>' . $val ['name'] . '</td>
				<td>' . $val ['description'] . '</td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( '$( "#course-search-form" ).submit(function() {
					xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
					return false;
		});' );
			$objResponse->script ( 'tblCourses = $("#courses").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showMyStudents($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div id="table-contain" class="ui-widget">
			<table id="students" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Contact</th>
			</tr>
			</thead>
			<tbody>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->getTutorStudents ( $_SESSION ['userlogin'] ) );
			
			foreach ( $vals as $val ) {
				$html .= '<tr>
				<td onDblClick="showTutor(\'' . $val ['userlogin'] . '\');">' . $val ['userlogin'] . '</td>
				<td>' . $val ['lastname'] . ', ' . $val ['firstname'] . '</td>
				<td><button onClick="showContactStudent(\'' . $val ['userlogin'] . '\');">Contact</button></td>
				</tr>';
			}
			
			$html .= '</tbody>
			</table>
			</div></div>';
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( 'tblStudents = $("#students").dataTable({ "bJQueryUI": true });' );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function showMyProfile($tutorid) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($tutorid == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$clt = new User ();
			
			$val = objectToArray ( $clt->getTutor ( $tutorid ) );
			
			if ($val) {
				$objResponse->script ( '$(\'#tutor_id\').val(\'' . $val ['userlogin'] . '\');' );
				$objResponse->script ( '$(\'#tutor_comment\').val(\'' . str_replace ( "'", "\'", $val ['description'] ) . '\');' );
				
				$objResponse->script ( '$(\'#dialog-profile-form\').dialog(\'open\');' );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function saveTutorDescription($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['tutor_comment'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$clt = new User ();
			
			$val = $clt->updateTutor ( $aFormValues ['tutor_id'], $aFormValues ['tutor_comment'] );
			
			if ($val == true) {
				$objResponse->script ( '$(\'#tutor_id\').val(\'\');' );
				$objResponse->script ( '$(\'#tutor_comment\').val(\'\');' );
				
				$objResponse->script ( '$(\'#dialog-profile-form\').dialog(\'close\');' );
			} else {
				$objResponse->alert ( "Error saving information!!!" );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function showMySchedule($email) {
	$objResponse = new xajaxResponse ();
	$bError = false;
	
	if ($email == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$html = '<div style="padding-left: 70px"><div id="calendar"></div></div>';
			
			$clt = new User ();
			
			$vals = objectToArray ( $clt->loadSchedules ( $email ) );
			
			$events = "events: [";
			$sep = "";
			
			foreach ( $vals as $val ) {
				$events .= $sep . "{
					id: " . $val ['id'] . ",
					title: '" . $val ['title'] . "',
					start: " . strtotime ( $val ['start'] ) . ",
					end: " . strtotime ( $val ['end'] ) . ",
					allDay: false
				}";
				
				if ($sep == "") {
					$sep = ",";
				}
			}
			
			$events .= "],";
			
			$objResponse->assign ( 'main', 'innerHTML', $html );
			$objResponse->script ( "var calendar = $('#calendar').fullCalendar({ 
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					defaultView: 'agendaWeek',
					allDaySlot: false,
					selectable: true,
					selectHelper: true,
					select: function(start, end, allDay) {
						var currentDate = new Date();	
					
						if(start >= currentDate){
							$( '#dialog-event-form' ).dialog('open');
							var startdate = (start.getMonth() + 1) + '/' + start.getDate() + '/' + start.getFullYear();
							var starttime = start.getHours() + ':' + start.getMinutes();
							var enddate = (end.getMonth() + 1) + '/' + end.getDate() + '/' + end.getFullYear();
							var endtime = end.getHours() + ':' + end.getMinutes();
						
							$('#event_start_date').val(startdate);
							$('#event_start_time').val(starttime);
							$('#event_end_date').val(enddate);
							$('#event_end_time').val(endtime);
					
							calendar.fullCalendar('unselect');
						}
					},
					editable: true,
					" . $events . "
					eventClick: function(event) {
				        xajax_loadTutorSchedule(event.id);				
				    }
			});" );
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	return $objResponse;
}
function saveTutorSchedule($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['event_title'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['event_start_date'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['event_end_date'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['event_start_time'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['event_end_time'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$clt = new User ();
			
			if ($aFormValues ['event_id'] != '') {
				$val = $clt->updateSchedule ( $aFormValues ['event_id'], $aFormValues ['event_title'], date ( 'Y-m-d h:i:s', strtotime ( $aFormValues ['event_start_date'] . " " . $aFormValues ['event_start_time'] ) ), date ( 'Y-m-d h:i:s', strtotime ( $aFormValues ['event_end_date'] ) . " " . $aFormValues ['event_end_time'] ) );
			} else {
				$val = $clt->createSchedule ( $aFormValues ['userlogin'], $aFormValues ['event_title'], date ( 'Y-m-d h:i:s', strtotime ( $aFormValues ['event_start_date'] . " " . $aFormValues ['event_start_time'] ) ), date ( 'Y-m-d h:i:s', strtotime ( $aFormValues ['event_end_date'] ) . " " . $aFormValues ['event_end_time'] ) );
			}
			
			if ($val != null) {
				$objResponse->script ( '$(\'#event_title\').val(\'\');' );
				$objResponse->script ( '$(\'#event_id\').val(\'\');' );
				$objResponse->script ( '$(\'#dialog-event-form\').dialog(\'close\');' );
				
				if ($aFormValues ['event_id'] != '') {
					$objResponse->script ( "xajax_showMySchedule('" . $aFormValues ['userlogin'] . "');" );
				} else {
					$objResponse->script ( "$('#calendar').fullCalendar('renderEvent',{
							id: " . $val . ",
							title: '" . $aFormValues ['event_title'] . "',
							start: " . strtotime ( $aFormValues ['event_start_date'] . " " . $aFormValues ['event_start_time'] ) . ",
							end: " . strtotime ( $aFormValues ['event_end_date'] . " " . $aFormValues ['event_end_time'] ) . ",
							allDay: false }, true);" );
				}
			} else {
				$objResponse->alert ( "Error saving information!!!" );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function loadTutorSchedule($id) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($id == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$clt = new User ();
			
			$val = objectToArray ( $clt->loadSchedule ( $id ) );
			
			if ($val != null) {
				$objResponse->script ( '$(\'#event_id\').val(\'' . $val ['id'] . '\');' );
				$objResponse->script ( '$(\'#event_title\').val(\'' . $val ['title'] . '\');' );
				$objResponse->script ( '$(\'#event_start_date\').val(\'' . date ( 'm/d/Y', strtotime ( $val ['start'] ) ) . '\');' );
				$objResponse->script ( '$(\'#event_start_time\').val(\'' . date ( 'h:i', strtotime ( $val ['start'] ) ) . '\');' );
				$objResponse->script ( '$(\'#event_end_date\').val(\'' . date ( 'm/d/Y', strtotime ( $val ['end'] ) ) . '\');' );
				$objResponse->script ( '$(\'#event_end_time\').val(\'' . date ( 'h:i', strtotime ( $val ['end'] ) ) . '\');' );
				$objResponse->script ( '$(\'#dialog-event-form\').dialog(\'open\');' );
			} else {
				$objResponse->alert ( "Error loading information!!!" );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
function deleteTutorSchedule($aFormValues) {
	$objResponse = new xajaxResponse ();
	
	$bError = false;
	
	if ($aFormValues ['event_id'] == '') {
		$bError = true;
	}
	
	if ($aFormValues ['userlogin'] == '') {
		$bError = true;
	}
	
	if (! $bError) {
		try {
			session_start ();
			
			$clt = new User ();
			
			$val = objectToArray ( $clt->deleteSchedule ( $aFormValues ['event_id'] ) );
			
			if ($val == true) {
				$objResponse->script ( '$(\'#event_title\').val(\'\');' );
				$objResponse->script ( '$(\'#event_id\').val(\'\');' );
				$objResponse->script ( '$(\'#dialog-event-form\').dialog(\'close\');' );
				$objResponse->script ( "xajax_showMySchedule('" . $aFormValues ['userlogin'] . "');" );
			} else {
				$objResponse->alert ( "Error loading information!!!" );
			}
		} catch ( SoapFault $e ) {
			$objResponse->alert ( $e->getCode () . ': ' . $e->getMessage () );
		}
	}
	
	return $objResponse;
}
/**
 * Functions registration
 */
$xjx->register ( XAJAX_FUNCTION, "signUp" );
$xjx->register ( XAJAX_FUNCTION, "accountExists" );
$xjx->register ( XAJAX_FUNCTION, "logIn" );
$xjx->register ( XAJAX_FUNCTION, "logOut" );
$xjx->register ( XAJAX_FUNCTION, "showCoursesAdmin" );
$xjx->register ( XAJAX_FUNCTION, "deleteCourseAdmin" );
$xjx->register ( XAJAX_FUNCTION, "showStudentsAdmin" );
$xjx->register ( XAJAX_FUNCTION, "deleteStudentAdmin" );
$xjx->register ( XAJAX_FUNCTION, "showPendingApprovals" );
$xjx->register ( XAJAX_FUNCTION, "activateUser" );
$xjx->register ( XAJAX_FUNCTION, "showTutorsAdmin" );
$xjx->register ( XAJAX_FUNCTION, "deleteTutorAdmin" );
$xjx->register ( XAJAX_FUNCTION, "createCourse" );
$xjx->register ( XAJAX_FUNCTION, "searchCourse" );
$xjx->register ( XAJAX_FUNCTION, "showCoursesStudent" );
$xjx->register ( XAJAX_FUNCTION, "searchCourseStudent" );
$xjx->register ( XAJAX_FUNCTION, "courseRegistration" );
$xjx->register ( XAJAX_FUNCTION, "showMyCourses" );
$xjx->register ( XAJAX_FUNCTION, "showMyTutors" );
$xjx->register ( XAJAX_FUNCTION, "saveCourseCommentRate" );
$xjx->register ( XAJAX_FUNCTION, "loadCourseCommentRate" );
$xjx->register ( XAJAX_FUNCTION, "showTutorSchedule" );
$xjx->register ( XAJAX_FUNCTION, "showContactTutor" );
$xjx->register ( XAJAX_FUNCTION, "sendMesage" );
$xjx->register ( XAJAX_FUNCTION, "showMyMessages" );
$xjx->register ( XAJAX_FUNCTION, "showMessage" );
$xjx->register ( XAJAX_FUNCTION, "deleteMesage" );
$xjx->register ( XAJAX_FUNCTION, "showTutors" );
$xjx->register ( XAJAX_FUNCTION, "tutorContact" );
$xjx->register ( XAJAX_FUNCTION, "loadTutorCommentRate" );
$xjx->register ( XAJAX_FUNCTION, "showCoursesTutor" );
$xjx->register ( XAJAX_FUNCTION, "showStudents" );
$xjx->register ( XAJAX_FUNCTION, "courseTutorRegistration" );
$xjx->register ( XAJAX_FUNCTION, "showMyTutorCourses" );
$xjx->register ( XAJAX_FUNCTION, "showMyStudents" );
$xjx->register ( XAJAX_FUNCTION, "showMyProfile" );
$xjx->register ( XAJAX_FUNCTION, "saveTutorDescription" );
$xjx->register ( XAJAX_FUNCTION, "showMySchedule" );
$xjx->register ( XAJAX_FUNCTION, "saveTutorSchedule" );
$xjx->register ( XAJAX_FUNCTION, "loadTutorSchedule" );
$xjx->register ( XAJAX_FUNCTION, "deleteTutorSchedule" );

$xjx->processRequest ();
?>