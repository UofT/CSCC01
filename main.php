<?php
/** Load application parameters  */
require_once 'core.php';
session_start ();

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

$tpl->assign ( 'javascript', $xjx->getJavascript () );

if (isset ( $_SESSION ['authenticated'] ) and ($_SESSION ['authenticated'] == true)) {
	if ($_SESSION ['userrole'] == 'ad') {
		
		$tpl->assign ( 'userfirstname', $_SESSION ['username'] );
		
		$tpl->display ( 'admin-main.tpl' );
	} elseif ($_SESSION ['userrole'] == 'st') {
		
		$tpl->assign ( 'userfirstname', $_SESSION ['username'] );
		$tpl->assign ( 'userlogin', $_SESSION ['userlogin'] );
		
		$tpl->display ( 'student-main.tpl' );
	} elseif ($_SESSION ['userrole'] == 'tu') {
		
		$tpl->assign ( 'userfirstname', $_SESSION ['username'] );
		$tpl->assign ( 'userlogin', $_SESSION ['userlogin'] );
		$tpl->assign ( 'todaydate', date ( 'm/d/Y' ) );
		
		$tpl->display ( 'tutor-main.tpl' );
	}
} else if (isset ( $_SESSION ['activation'] )) {
	$tpl->display ( 'activation.tpl' );
} else if (isset ( $_SESSION ['approval'] )) {
	$tpl->display ( 'approval.tpl' );
} else {
	header ( "HTTP/1.0 403 Forbidden" );
}

?>