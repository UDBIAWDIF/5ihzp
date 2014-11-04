<?php
// $config	= array(
	// 'LOAD_EXT_CONFIG' => 'db,rbac10,const,status,cache,image,upfile,debug,ftp,tags,uidtool,djgame,downiflog',	//加载扩展配置文件
	// 'URL_MODEL'=>1,
	// 'URL_PARAMS_BIND'=>false,
	// 'URL_HTML_SUFFIX'=>'',
// );

$config = include BASE_PATH . 'Common/Conf/config.php';

$extConfigs = array(
	'URL_CASE_INSENSITIVE'	=> false,
	'DEFAULT_THEME'	=> '5ihzp',
	'URL_MODEL'		=> 1,
);

$extConfigFiles = array(
	'const',
	'tags',
	'image',
	'debug',
	'rbac10',
	'status',
	'uidtool',
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
