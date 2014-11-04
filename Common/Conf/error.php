<?php
return array(
	'ERROR' => array(
		'NO_ERROR'	=> 0,
		'SUCCESS'	=> 0,

		'REQUEST_NUM_OVERFLOW' => 101,	//列表数超过限定

		//取图片列表的错误码段
		'APP_LIST_FUNC_NO_FOUND' => 201,	//没有匹配的获取应用的方法

		'APPCATE_ERROR' => 301,	//分类参数错误

		'USERNAME_INPUT_FAULT'			=> 401,	//用户名输入错误(账号只能由4-18个字母或数字组成，请重新输入！)
		'PASSWORD_INPUT_FAULT'			=> 402,	//密码输入错误(密码只能由6-16个字符组成，请重新输入！)
		'USER_EXIST'					=> 403,	//用户已经存在(该账号已被注册，换一个试试吧！)
		'USER_NOT_EXIST'				=> 404,	//用户不存在
		'USER_NOT_LOGIN'				=> 405,	//用户未登录
		'USER_LOGIN_OTHER_WAY'			=> 406,	//用户在其它设备上登录
		'USER_SET_INFO_FAULT'			=> 407,	//设置信息时发生错误
		'USER_SET_HEAD_FAULT'			=> 408,	//设置头像时发生错误
		'USER_SET_DESCRIPTION_FAULT'	=> 409,	//设置签名时发生错误
		'NICKNAME_INPUT_FAULT'			=> 410,	//昵称输入错误(昵称只能由2-8个汉字，字母、数字组成，请重新输入！)
		'NICKNAME_EXIST'				=> 411,	//昵称已经存在(该昵称被人抢注了，换一个试试吧！)
		'USERNAME_NOT_INPUT'			=> 412,	//用户名为空(请填写账号和密码！)
		'PASSWORD_NOT_INPUT'			=> 413,	//密码为空(请填写账号和密码！)
		'NICKNAME_NOT_INPUT'			=> 414,	//昵称为空(请输入昵称！)
		'USERNAME_INPUT_FAULT_IN_LOGIN'	=> 415,	//登录时用户名输入错误
		'PASSWORD_INPUT_FAULT_IN_LOGIN'	=> 416,	//登录时密码输入错误

		'PERSON_HAVE_GROUP_LIMIT_OUT'	=> 501,

		//用户反馈的错误码段
		'NO_FB_CONTACT' => 10001,	//没有联系方式
		'NO_FB_CONTENT' => 10002,	//没有内容

		'UNKOWN_ERROR' => 99998,	//未知错误
		'NODATA' => 99999,	//没有相应数据
		'NO_ACT' => 100000,	//访问了不存在的功能
	),
	// 'TMPL_EXCEPTION_FILE' => TMPL_PATH . C('DEFAULT_THEME') . '/Public/noact.html',
);
?>
