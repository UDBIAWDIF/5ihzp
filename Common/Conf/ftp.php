<?php
if (!defined('THINK_PATH')) exit();

/* $returnCfg = checkCfgFile(LOC_CFG_PATH . 'ftp.php');
if(empty($returnCfg)) {
	$returnCfg = array(
		'IMG_FTP' => array(
							array(
								'FTP_HOST'	=> '192.168.241.72',
								'FTP_PORT'	=> '21',
								'FTP_USER'	=> 'ftptest',
								'FTP_PASS'	=> 'ftptest',
							),
						),

		'SOFT_FTP' => array(
							array(
								'FTP_HOST'	=> '192.168.241.72',
								'FTP_PORT'	=> '21',
								'FTP_USER'	=> 'ftptest',
								'FTP_PASS'	=> 'ftptest',
							),
						),

		'FTP_IMG_PATH'		=> 'image',
		'FTP_SOFT_PATH'		=> 'soft',
		'IMAGE_HOST'		=> 'http://bdftptest.nd/',
		'SOFT_HOST'			=> 'http://bdftptest.nd/',

		'FTP_IMG_PATH_91PLAY'	=> 'kuplay',
		'FTP_SOFT_PATH_91PLAY'	=> 'soft_kuplay',
		'IMAGE_HOST_91PLAY'		=> 'http://image.ku.91rb.com/',
		'SOFT_HOST_91PLAY'		=> 'http://ku.91rb.com/',
	);
}

return $returnCfg; */
if($_SERVER['HTTP_HOST'] == 'game-api.91.com') {
	return array(
		'IMG_FTP' => array(
							array(
								'FTP_HOST'              =>  '10.1.240.131',
								'FTP_PORT'              =>  '21',
								'FTP_USER'              =>  '',
								'FTP_PASS'              =>  '',
							),
						),

		'SOFT_FTP' => array(
							array(
								'FTP_HOST'              =>  '10.1.240.131',
								'FTP_PORT'              =>  '21',
								'FTP_USER'              =>  '',
								'FTP_PASS'              =>  '',
							),
						),

		'FTP_IMG_PATH'    =>  '91gameadmin',
		'FTP_SOFT_PATH'   =>  '91gameadmin',
		'IMAGE_HOST'      =>  'http://attach.91.com/',
		'SOFT_HOST'       =>  'http://attach.91.com/',

		'FTP_IMG_PATH_91PLAY'	=> '91gameadmin',
		'FTP_SOFT_PATH_91PLAY'	=> '91gameadmin',
		'IMAGE_HOST_91PLAY'		=> 'http://attach.91.com/',
		'SOFT_HOST_91PLAY'		=> 'http://attach.91.com/',
	);
} else {
	return array(
		'IMG_FTP' => array(
							array(
								'FTP_HOST'	=> '192.168.241.72',
								'FTP_PORT'	=> '21',
								'FTP_USER'	=> 'ftptest',
								'FTP_PASS'	=> 'ftptest',
							),
						),

		'SOFT_FTP' => array(
							array(
								'FTP_HOST'	=> '192.168.241.72',
								'FTP_PORT'	=> '21',
								'FTP_USER'	=> 'ftptest',
								'FTP_PASS'	=> 'ftptest',
							),
						),

		'FTP_IMG_PATH'		=> '',
		'FTP_SOFT_PATH'		=> '',
		'IMAGE_HOST'		=> 'http://192.168.241.72/',
		'SOFT_HOST'			=> 'http://192.168.241.72/',

		'FTP_IMG_PATH_91PLAY'	=> 'kuplay',
		'FTP_SOFT_PATH_91PLAY'	=> 'soft_kuplay',
		'IMAGE_HOST_91PLAY'		=> 'http://image.ku.91rb.com/',
		'SOFT_HOST_91PLAY'		=> 'http://ku.91rb.com/',
	);
}

?>
