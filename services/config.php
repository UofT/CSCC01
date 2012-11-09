<?php
return array(
		'appdir' => getcwd(),
		'url' => 'http://localhost:10088/cscc01/',
		'database' => array(
				'adapter' => 'Mysqli', 
				'params' => array(
							'host' => 'localhost',
							'username' => 'root', 
							'password' => 'calabaza',
							'dbname' => 'cscc01',
							'port' => '3306'
						)
		)
	);
?>