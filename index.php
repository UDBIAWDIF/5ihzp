<?php

include 'const.php';

//define('RUNTIME_ALLINONE', true);

define('APP_DEBUG', true);

//$appName = str_replace('.php' , '' , phpfile());
$appName = 'Home';
define('APP_NAME', $appName);
define('APP_PATH', BASE_PATH . $appName . '/');

//设置 session 标志作为前缀,避免同一目录下太多程序用相同 APP_NAME 产生的冲突
define('APP_SESSION_FLAG', 'UID_' . md5(APP_PATH));

// 加载框架入口文件
require(THINK_PATH . 'ThinkPHP.php');
