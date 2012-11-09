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

define('LIB_DIR', str_replace("\\", "/", getcwd()) . '/libs/');

/**
 * Set ajax controller class
 */
require_once(LIB_DIR . 'xajax_core/xajax.inc.php');

$xjx = new xajax();

/**
 * 
 */
require_once 'classes/User.php';
require_once 'classes/Course.php';

/**
 *  Functions
 */

function signUp($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['firstname'] == '') {
		$bError = true;
	}

	if ($aFormValues['lastname'] == '') {
		$bError = true;
	}

	if ($aFormValues['email'] == '') {
		$bError = true;
	}

	if ($aFormValues['passwd'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			$clt->_cookies = $_SESSION['cookies'];*/
			$clt = new User();

			$val = $clt
					->create($aFormValues['email'], $aFormValues['firstname'],
							$aFormValues['lastname'], $aFormValues['passwd'],
							$aFormValues['acct-type']);

			if ($val) {
				$objResponse->alert('Account created!!!');
				$_SESSION['userlogin'] = $aFormValues['email'];
				$_SESSION['username'] = $aFormValues['firstname'];
				$_SESSION['userrole'] = $aFormValues['acct-type'];
				$_SESSION['authenticated'] = true;
				$_SESSION['cookies'] = $clt->_cookies;

				$objResponse->script('document.location.href="main.php"');
			}

		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function accountExists($email) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($email == '') {
		$bError = true;
	}

	if (!$bError) {
		session_start();

		try {
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			$clt->_cookies = $_SESSION['cookies'];*/
			$clt = new User();

			$val = objectToArray($clt->getUser($email));

			if ($val['userlogin'] != null) {
				$objResponse->script('signUpAccountExists = true;');
			} else {
				$objResponse->script('signUpAccountExists = false;');
			}
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function logIn($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['userlogin'] == '') {
		$bError = true;
	}

	if ($aFormValues['password'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();

			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new User();

			$val = $clt
					->authenticate($aFormValues['userlogin'],
							$aFormValues['password']);

			if (!is_string($val)) {
				$_SESSION['userlogin'] = $aFormValues['userlogin'];
				$_SESSION['authenticated'] = true;
				//$_SESSION['cookies'] = $clt->_cookies;

				$val = objectToArray($clt->getUser($aFormValues['userlogin']));

				$_SESSION['userrole'] = $val['role'];
				$_SESSION['username'] = $val['firstname'] . " "
						. $val['lastname'];

				$objResponse->script('document.location.href="main.php"');
			} else {
				$objResponse->alert($val);
			}

		} catch (Exception $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function logOut() {
	$objResponse = new xajaxResponse();

	session_destroy();
	$objResponse->script('document.location.href="index.php"');

	return $objResponse;
}

function showCoursesAdmin() {
	$objResponse = new xajaxResponse();
	$bError = false;

	if (!$bError) {
		try {
			session_start();

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

			$clt = new Course();

			$vals = objectToArray($clt->showAll());

			foreach ($vals as $val) {
				$html .= '<tr>
                <td>' . $val['courseid'] . '</td>
                <td>' . $val['coursename'] . '</td>
                <td>' . $val['coursedesc'] . '</td>
            </tr>';
			}

			$html .= '</tbody>
    </table>
</div></div>
<button id="create-course">Create new course</button>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#create-course" )
            .button()
            .click(function() {
                $( "#dialog-course-form" ).dialog( "open" );
            });
			$( "#course-search-form" ).submit(function() {
  				xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
				return false;
			});');
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}
	return $objResponse;
}

function createCourse($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['course_id'] == '') {
		$bError = true;
	}

	if ($aFormValues['course_name'] == '') {
		$bError = true;
	}

	if ($aFormValues['course_desc'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			$clt->_cookies = $_SESSION['cookies'];*/
			$clt = new Course();

			$val = $clt
					->create($aFormValues['course_id'],
							$aFormValues['course_name'],
							$aFormValues['course_desc']);

			if ($val) {
				$objResponse->script('xajax_showCoursesAdmin();');
			}

		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function searchCourse($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['course_criteria'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();

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

			$clt = new Course();

			$vals = objectToArray($clt->search($aFormValues['course_criteria']));

			foreach ($vals as $val) {
				$html .= '<tr>
                <td>' . $val['courseid'] . '</td>
                <td>' . $val['coursename'] . '</td>
                <td>' . $val['coursedesc'] . '</td>
            </tr>';
			}

			$html .= '</tbody>
    </table>
</div></div>
<button id="create-course">Create new course</button>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#create-course" )
            .button()
            .click(function() {
                $( "#dialog-course-form" ).dialog( "open" );
            });
			$( "#course-search-form" ).submit(function() {
  				xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
				return false;
			});');
		} catch (Exception $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function showCoursesStudent() {
	$objResponse = new xajaxResponse();
	$bError = false;

	if (!$bError) {
		try {
			session_start();

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

			$clt = new Course();

			$vals = objectToArray($clt->showAllRated());

			foreach ($vals as $val) {
				$html .= '<tr>
				<td>' . $val['courseid'] . '</td>
				<td>' . $val['coursename'] . '</td>
				<td>' . $val['coursedesc'] . '</td>				
				<td><div id="star' . $val['courseid']
						. '"></div></td>			
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_courseRegistration(\''
						. $_SESSION['userlogin'] . '\',\'' . $val['courseid']
						. '\')"></td>
				</tr>';

				$scripts .= '$(\'#star' . $val['courseid']
						. '\').raty({ readOnly : true, score : '
						. $val['courserate'] . '});';
			}

			$html .= '</tbody>
			</table>
			</div></div>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#course-search-form" ).submit(function() {
					xajax_searchCourseStudent(xajax.getFormValues(\'course-search-form\'));
					return false;});');
			$objResponse->script($scripts);
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}
	return $objResponse;
}

function searchCourseStudent($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['course_criteria'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();

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

			$clt = new Course();

			$vals = objectToArray(
					$clt->searchRated($aFormValues['course_criteria']));

			foreach ($vals as $val) {
				$html .= '<tr>
				<td>' . $val['courseid'] . '</td>
				<td>' . $val['coursename'] . '</td>
				<td>' . $val['coursedesc'] . '</td>				
				<td><div id="star' . $val['courseid']
						. '"></div></td>			
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_courseRegistration(\''
						. $_SESSION['userlogin'] . '\',\'' . $val['courseid']
						. '\')"></td>
				</tr>';

				$scripts .= '$(\'#star' . $val['courseid']
						. '\').raty({ readOnly : true, score : '
						. $val['courserate'] . '});';
			}

			$html .= '</tbody>
			</table>
			</div></div>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#course-search-form" ).submit(function() {
					xajax_searchCourseStudent(xajax.getFormValues(\'course-search-form\'));
					return false;});');
			$objResponse->script($scripts);
		} catch (Exception $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function courseRegistration($email, $courseid) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($email == '') {
		$bError = true;
	}

	if ($courseid == '') {
		$bError = true;
	}

	if (!$bError) {
		session_start();

		try {
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new Course();

			$val = objectToArray($clt->register($email, $courseid));

			if ($val == null) {
				$objResponse->alert($val);
			} else {
				$objResponse->alert('Registered for ' . $courseid);
			}
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function showMyCourses($email) {
	$objResponse = new xajaxResponse();
	$bError = false;

	if ($email == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();

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

			$clt = new Course();

			$vals = objectToArray($clt->getUserCourses($email));

			foreach ($vals as $val) {
				$html .= '<tr onDblClick="showCourse(\'' . $val['courseid']
						. '\');">
				<td>' . $val['courseid'] . '</td>
				<td>' . $val['coursename'] . '</td>
				<td>' . $val['coursedesc'] . '</td>
				</tr>';
			}

			$html .= '</tbody>
			</table>
			</div></div>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#course-search-form" ).submit(function() {
					xajax_searchCourse(xajax.getFormValues(\'course-search-form\'));
					return false;
		});');
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}
	return $objResponse;
}

function showMyTutors($email) {
	$objResponse = new xajaxResponse();
	$bError = false;

	if ($email == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();

			$html = '<div id="table-contain" class="ui-widget">
			<form id="tutor-search-form">
			<div align="right"> <label for="course_criteria">Search</label>
			<input style="width: 100px" name="course_criteria" type="text" id="course_criteria">
			</div>
			</form>
			<table id="tutors" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Description</th>
			</tr>
			</thead>
			<tbody>';

			$clt = new User();

			$vals = objectToArray($clt->getUserTutors($_SESSION['userlogin']));

			foreach ($vals as $val) {
				$html .= '<tr>
				<td onDblClick="showTutor(\'' . $val['userlogin'] . '\');">'
						. $val['userlogin'] . '</td>
				<td>' . $val['lastname'] . ', ' . $val['firstname']
						. '</td>
				<td>' . $val['tutordesc'] . '</td>
				</tr>';
			}

			$html .= '</tbody>
			</table>
			</div></div>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#tutor-search-form" ).submit(function() {
					xajax_searchTutor(xajax.getFormValues(\'tutor-search-form\'));
					return false;});');
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}
	return $objResponse;
}

function saveCourseCommentRate($aFormValues) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($aFormValues['course_rating'] == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new Course();

			$val = $clt
					->updateCommentRate($aFormValues['course_id'],
							$aFormValues['course_comment'],
							$aFormValues['course_rating']);

			if ($val == true) {
				$objResponse->script('$(\'#course_id\').val(\'\');');
				$objResponse->script('$(\'#course_comment\').val(\'\');');
				$objResponse->script('$(\'#course_rating\').val(\'0\');');

				$objResponse->script('$(\'#star\').raty(\'score\', 0);');

				$objResponse
						->script(
								'$(\'#dialog-course-form\').dialog(\'close\');');
			} else {
				$objResponse->alert("Error saving information!!!");
			}

		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function loadCourseCommentRate($courseid) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($courseid == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new Course();

			$val = objectToArray($clt->getCourseRated($courseid));

			if ($val) {
				if (is_null($val['courserate'])) {
					$val['courserate'] = 0;
				}

				$objResponse
						->script(
								'$(\'#course_id\').val(\'' . $val['courseid']
										. '\');');
				$objResponse
						->script(
								'$(\'#course_comment\').val(\''
										. $val['coursecomment'] . '\');');
				$objResponse
						->script(
								'$(\'#course_rating\').val(\''
										. $val['courserate'] . '\');');

				$objResponse
						->script(
								'$(\'#star\').raty(\'score\', '
										. $val['courserate'] . ');');

				$objResponse
						->script('$(\'#dialog-course-form\').dialog(\'open\');');
			}

		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function showTutors() {
	$objResponse = new xajaxResponse();
	$bError = false;

	if (!$bError) {
		try {
			session_start();

			$html = '<div id="table-contain" class="ui-widget">
			<form id="tutor-search-form">
			<div align="right"> <label for="course_criteria">Search</label>
			<input style="width: 100px" name="course_criteria" type="text" id="course_criteria">
			</div>
			</form>
			<table id="tutors" class="ui-widget ui-widget-content">
			<thead>
			<tr class="ui-widget-header ">
			<th>Email</th>
			<th>Name</th>
			<th>Description</th>
			<th>Rating</th>
			<th>Ask for contact</th>
			</tr>
			</thead>
			<tbody>';

			$clt = new User();

			$vals = objectToArray($clt->showAllTutors($_SESSION['userlogin']));

			foreach ($vals as $val) {
				$html .= '<tr>
				<td>' . $val['userlogin'] . '</td>
				<td>' . $val['lastname'] . ', ' . $val['firstname']
						. '</td>
				<td>' . $val['tutordesc'] . '</td>
				<td><div id="star'
						. str_replace('.', '',
								str_replace('@', '', $val['userlogin']))
						. '"></div></td>
				<td><img src="images/register_icon.gif" width="20" height="20" onDblClick="xajax_tutorContact(\''
						. $_SESSION['userlogin'] . '\',\'' . $val['userlogin']
						. '\')"></td>
				</tr>';

				$scripts .= '$(\'#star'
						. str_replace('.', '',
								str_replace('@', '', $val['userlogin']))
						. '\').raty({ readOnly : true, score : '
						. $val['tutorrate'] . '});';
			}

			$html .= '</tbody>
			</table>
			</div></div>';

			$objResponse->assign('main', 'innerHTML', $html);
			$objResponse
					->script(
							'$( "#tutor-search-form" ).submit(function() {
					xajax_searchTutor(xajax.getFormValues(\'tutor-search-form\'));
					return false;});');
			$objResponse->script($scripts);
		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}
	return $objResponse;
}

function loadTutorCommentRate($tutorid) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($tutorid == '') {
		$bError = true;
	}

	if (!$bError) {
		try {
			session_start();
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new User();

			$val = objectToArray(
					$clt->getTutorRated($tutorid, $_SESSION['userlogin']));

			if ($val) {
				if (is_null($val['tutorrate'])) {
					$val['tutorrate'] = 0;
				}

				$objResponse
						->script(
								'$(\'#tutor_id\').val(\'' . $val['tutorid']
										. '\');');
				$objResponse
						->script(
								'$(\'#tutor_comment\').val(\''
										. $val['tutorcomment'] . '\');');
				$objResponse
						->script(
								'$(\'#tutor_rating\').val(\''
										. $val['tutorrate'] . '\');');

				$objResponse
						->script(
								'$(\'#startutor\').raty(\'score\', '
										. $val['tutorrate'] . ');');

				$objResponse
						->script('$(\'#dialog-tutor-form\').dialog(\'open\');');
			}

		} catch (SoapFault $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

function tutorContact($email, $tutorid) {
	$objResponse = new xajaxResponse();

	$bError = false;

	if ($email == '') {
		$bError = true;
	}

	if ($tutorid == '') {
		$bError = true;
	}

	if (!$bError) {
		session_start();

		try {
			/*$clt = new SoapClient(WEB_SER_SVR . 'user.php?wsdl');
			 $clt->_cookies = $_SESSION['cookies'];*/
			$clt = new User();

			$val = objectToArray($clt->contact($email, $tutorid));

			if ($val == null) {
				$objResponse->alert($val);
			} else {
				$objResponse->alert('Tutor contacted');
			}
		} catch (Exception $e) {
			$objResponse->alert($e->getCode() . ': ' . $e->getMessage());
		}
	}

	return $objResponse;
}

/**
 *
 * Convert an object to an array
 *
 * @param object $object The object to convert
 * @return array
 *
 */
function objectToArray($object) {
	if (!is_object($object) && !is_array($object)) {
		return $object;
	}
	if (is_object($object)) {
		$object = get_object_vars($object);
	}
	return array_map('objectToArray', $object);
}

/**
 * Functions registration
 */
$xjx->register(XAJAX_FUNCTION, "signUp");
$xjx->register(XAJAX_FUNCTION, "accountExists");
$xjx->register(XAJAX_FUNCTION, "logIn");
$xjx->register(XAJAX_FUNCTION, "logOut");
$xjx->register(XAJAX_FUNCTION, "showCoursesAdmin");
$xjx->register(XAJAX_FUNCTION, "createCourse");
$xjx->register(XAJAX_FUNCTION, "searchCourse");
$xjx->register(XAJAX_FUNCTION, "showCoursesStudent");
$xjx->register(XAJAX_FUNCTION, "searchCourseStudent");
$xjx->register(XAJAX_FUNCTION, "courseRegistration");
$xjx->register(XAJAX_FUNCTION, "showMyCourses");
$xjx->register(XAJAX_FUNCTION, "showMyTutors");
$xjx->register(XAJAX_FUNCTION, "saveCourseCommentRate");
$xjx->register(XAJAX_FUNCTION, "loadCourseCommentRate");
$xjx->register(XAJAX_FUNCTION, "showTutors");
$xjx->register(XAJAX_FUNCTION, "tutorContact");
$xjx->register(XAJAX_FUNCTION, "loadTutorCommentRate");

$xjx->processRequest();
?>