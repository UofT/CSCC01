<?php

if (!extension_loaded('mysqli')) {
	dl('mysqli.so');
} 

if (!extension_loaded('soap')) {
	dl('soap.so');
}

function getCurrentURL() {
	$URL = 'http';

	if (!empty($_SERVER['HTTPS'])) {
		$URL .= "s";
	}  

	$URL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$URL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]
				. $_SERVER["REQUEST_URI"];
	} else {
		$URL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}

	return $URL;
}

error_reporting(0);
$old_error_handler = set_error_handler("userErrorHandler");

function userErrorHandler ($errno, $errmsg, $filename, $linenum,  $vars)
{
	$time=date("d M Y H:i:s");
	// Get the error type from the error number
	$errortype = array (1    => "Error",
			2    => "Warning",
			4    => "Parsing Error",
			8    => "Notice",
			16   => "Core Error",
			32   => "Core Warning",
			64   => "Compile Error",
			128  => "Compile Warning",
			256  => "User Error",
			512  => "User Warning",
			1024 => "User Notice");
	$errlevel=$errortype[$errno];

	//Write error to log file (CSV format)
	$errfile=fopen("errors.txt","a");
	fputs($errfile,"\"$time\",\"$filename:
			$linenum\",\"($errlevel) $errmsg\"\r\n");
	fclose($errfile);

	if($errno!=2 && $errno!=8) {
		//Terminate script if fatal error
		die("A fatal error has occurred. Script execution has been aborted");
	}
}
?>