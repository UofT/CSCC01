<?php
/** Load application parameters  */
require_once 'core.php';
session_start();

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

$tpl->assign('javascript', $xjx->getJavascript());

if (isset($_SESSION['authenticated'])) {
	if ($_SESSION['userrole'] == 'ad') {
		
		$tpl->assign('userfirstname', $_SESSION['username']);

		$tpl->display('admin-main.tpl');
	} elseif ($_SESSION['userrole'] == 'st') {
		
		$tpl->assign('userfirstname', $_SESSION['username']);
		$tpl->assign('userlogin', $_SESSION['userlogin']);

		$tpl->display('student-main.tpl');
	}
} else {
	header("HTTP/1.0 403 Forbidden");
}

?>