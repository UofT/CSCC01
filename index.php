<?php
/** Load application parameters  */
require_once 'core.php';

$xjx->register(XAJAX_FUNCTION, "signUp");
$xjx->register(XAJAX_FUNCTION, "accountExists");
$xjx->register(XAJAX_FUNCTION, "logIn");

$tpl->assign('javascript', $xjx->getJavascript());
$tpl->display('login.tpl');

?>