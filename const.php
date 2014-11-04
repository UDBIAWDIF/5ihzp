<?php
//定义项目名称和路径
//define('RUNTIME_ALLINONE', true);
function phpfile() {	//取得当前PHP文件名
	if (strtoupper(substr(PHP_OS,0,3)) == 'WIN') {
		$rpos = strrpos(__FILE__,'\\') + 1;
		$filename = substr(__FILE__,$rpos);
	} else {
		$rpos = strrpos(__FILE__,'/') + 1;
		$filename = substr(__FILE__,$rpos);
	}
	return $filename;
}

$sDir = str_replace('\\' , '/' , (dirname(__FILE__))) . '/';
define('BASE_PATH', $sDir);
define('THINK_PATH', $sDir . 'ThinkPHP/');
define('UPLOAD_MAIN_DIR', !empty($_SERVER['UPLOAD_MAIN_DIR']) ? $_SERVER['UPLOAD_MAIN_DIR'] : BASE_PATH);
define('UPLOAD_SUB_DIR', 'uploads/');
define('UPLOAD_DIR', UPLOAD_MAIN_DIR . UPLOAD_SUB_DIR);
