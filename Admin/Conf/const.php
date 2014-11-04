<?php
if (!defined('THINK_PATH')) exit();
define('APP_DOMAIN', 'http://'.$_SERVER['HTTP_HOST'].'/');	//后台域名
define('UPLOAD_PATH', THINK_PATH.'../uploads/');				//附件上传本地目录
define('UPLOAD_URL',  APP_DOMAIN.'uploads/');				//附件访问地址-加域名的完全地址
define('UPLOAD_LOCAL_URL', '/uploads/');						//附件访问地址-不加域名的相对地址
?>
