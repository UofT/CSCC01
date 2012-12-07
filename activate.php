<?php
require_once 'core.php';
require_once 'services/common.php';
require_once 'classes/User.php';
session_start ();

$obj = new User ();

if ($_GET ['id'] != '') {
	$username = $obj->activate ( $_GET ['id'] );
	echo $username;
	if ($username != '') {
		$user = objectToArray ( $obj->getUser ( $username ) );
		
		$_SESSION ['userlogin'] = $username;
		$_SESSION ['authenticated'] = true;
		$_SESSION ['userrole'] = $user ['role'];
		$_SESSION ['username'] = $user ['firstname'] . " " . $user ['lastname'];
		
		header ( 'Location: main.php' );
	} else {
		$tpl->display ( 'error.tpl' );
	}
} else {
	$tpl->display ( 'error.tpl' );
}

?>