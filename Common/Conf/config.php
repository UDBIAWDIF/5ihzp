<?php
$commonConfig = array(
	'URL_MODEL' => 2,
	'URL_HTML_SUFFIX' => 'html',
	'URL_CASE_INSENSITIVE' => true,
	'SETTING_INPUT_TYPE'=>array(
		'1' => '文本',
		'2' => '开关',
		'3' => '单选',
		'4' => '多选框',
		'5' => '下拉框',
		'6' => '时间',
	),
	'SESSION_EXPIRE' => 3600000,
	'VAR_SESSION_ID' => 'PHPSESSID',	//sessionID的提交变量

	'INDEX_ROWS'		=> 10,
	'CLONE_MAX_ROWS'	=> 5,
	'INDEX_MODULE'		=> 'Index',
	'INDEX_URL'			=> 'Index',
	'FILE_UP_PATH'		=> 'uploads/',	//文件上传的总目录
	'PERSON_HAVE_GROUP_LIMIT' => 10,

	'PW_LEN_MIN'		=> 6,
	'PW_LEN_MAX'		=> 16,
	'USERNAME_LEN_MIN'	=> 4,
	'USERNAME_LEN_MAX'	=> 18,
	'NICKNAME_LEN_MIN'	=> 2,
	'NICKNAME_LEN_MAX'	=> 8,
	'USERSIGN_LEN_MAX'	=> 180,
	'COMMENT_LEN_MAX'	=> 300,
);

$extConfigs = array();
$extConfigFiles = array(
	'db',
	'cache',
	'ftp',
	'image',
	'tpl',
	'upfile',
	'mail',
	'error',
);
$localPath = str_replace('\\' , '/' , (dirname(__FILE__))) . '/';

foreach($extConfigFiles as $extConfigFile) {
	$extConfig = null;
	$extConfigFileFullPath = $localPath . $extConfigFile . '.php';
	if(is_file($extConfigFileFullPath)) {
		$extConfig = include $extConfigFileFullPath;
	}
	if(is_array($extConfig)) {
		$extConfigs = array_merge($extConfigs, $extConfig);
	}
}

return array_merge($commonConfig, $extConfigs);
