<?php
// $config	= array(
	// 'LOAD_EXT_CONFIG'	=> 'tpl,image,debug,tags,msgtype',	//加载扩展配置文件
	// 'LOAD_EXT_FILE'		=> 'debug',	//加载扩展的函数库
	// 'URL_MODEL' => 1,
	// 'URL_PARAMS_BIND' => false,
// );

$config = include BASE_PATH . 'Common/Conf/config.php';

$config['COMMENT_DELAY'] = 10;

$extConfigs = array();
$extConfigFiles = array(
	'tags',
	'msgtype',
	'image',
	'debug',
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

return array_merge($config, $extConfigs);
