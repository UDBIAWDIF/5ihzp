<?php

/*
 * 名称：debugVar()
 * 功能：保存运行时的各种变量
 * 使用方法：debugVar($var,'var');
 * $var：需要保存的变量
 * $var_name：备注说明
 *
 * */
function debugVar($var, $var_name) {
	$debug_file = RUNTIME_PATH . 'Debug/debug';

	$debug_dir = dirname($debug_file);
	if(!is_dir($debug_dir)) {
		@mk_dir($debug_dir);
	}

	if(false === $var_name) {
		file_put_contents($debug_file, '');
		return;
	}

	$debug_filesize = filesize($debug_file);
	clearstatcache();
	$text = '';
	if(empty($debug_filesize)) {
		$text .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>' . "\r\n";
	}
	$text .= '<div style="color:red">Client: ' . get_client_ip() .' Time: ' . date('Y-m-d H:i:s') . '</div><b>Var Name: ' . $var_name . '</b>' . "\r\n";
	$debug = $text . dump($var, $echo=false) . "\r\n\r\n";
	file_put_contents($debug_file, $debug, FILE_APPEND);
}//end debugVar()
