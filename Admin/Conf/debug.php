<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: debug.php 2781 2012-02-24 05:31:47Z liu21st $

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 默认的调试模式配置文件
 *  如果项目有定义自己的调试模式配置文件，本文件无效
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id: debug.php 2781 2012-02-24 05:31:47Z liu21st $
 +------------------------------------------------------------------------------
 */
if (!defined('THINK_PATH')) exit();
// 调试模式下面默认设置 可以在项目配置目录下重新定义 debug.php 覆盖
return  array(
	'HTML_CACHE_ON' => false, 
    'TMPL_CACHE_ON'    => false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
// 	'SHOW_PAGE_TRACE'        =>true,
);