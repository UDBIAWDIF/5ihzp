<?php
return array(
	'DB_TYPE'	=> 'mysql',
	'DB_HOST'	=> !empty($_SERVER['DB_HOST'])
					? $_SERVER['DB_HOST']	: '127.0.0.1',
	'DB_NAME'	=> !empty($_SERVER['DB_NAME'])
					? $_SERVER['DB_NAME']	: '5ihzp',
	'DB_USER'	=> !empty($_SERVER['DB_USER'])
					? $_SERVER['DB_USER']	: 'UID',
	'DB_PWD'	=> !empty($_SERVER['DB_PWD'])
					? $_SERVER['DB_PWD']	: '1',
	'DB_PORT'	=> !empty($_SERVER['DB_PORT'])
					? $_SERVER['DB_PORT']	: '3306',
	'DB_PREFIX'	=> !empty($_SERVER['DB_PREFIX'])
					? $_SERVER['DB_PREFIX']	: 'hzp_',
);
