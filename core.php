<?php
require_once 'setup.php';

define('LIB_DIR', str_replace("\\", "/", getcwd()) . '/libs/');

/**
 * Set template controller class
 */
require_once(LIB_DIR . 'Smarty.class.php');

$tpl = new Smarty();

$tpl
		->setTemplateDir(
				str_replace("\\", "/", getcwd()) . '/templates/templates/');
$tpl
		->setCompileDir(
				str_replace("\\", "/", getcwd()) . '/templates/templates_c/');
$tpl->setConfigDir(str_replace("\\", "/", getcwd()) . '/templates/configs/');
$tpl->setCacheDir(str_replace("\\", "/", getcwd()) . '/templates/cache/');

$tpl->debugging = false;

/**
 * Set ajax controller class
 */
require_once(LIB_DIR . 'xajax_core/xajax.inc.php');

$xjx = new xajax("functions.php");

$xjx->configure('javascript URI', 'libs/');
$xjx->configure('debug', true);
?>
