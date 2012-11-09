<?php
/** Application directory */
define('APP_DIR', getcwd());

/** Web services server hostname */
define('WEB_SER_SVR',
		'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"]
				. '/cscc01/services/');

/** Site URL */
//SITEURL is modified for the new path
define('SITE_URL',
		'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"]
				. '/cscc01/');

?>