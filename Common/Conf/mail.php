<?php
// 系统默认的核心行为扩展列表文件
return array(
	'MAIL' => array(
		'SENDER'		=> '5ihzp',	// 发送者名字
		'SMTPUSER'		=> '1985228834@qq.com',
		'SMTPPWD'		=> '13705084537',
		'SMTPSERVER'	=> 'smtp.qq.com',
		'MAILFROM'		=> 'admin@5ihzp.com',
		'SMTPPORT'		=> 25,
		'SMTPAUTH'		=> true,
		'ISHTML'		=> true,
		'CHARSET'		=> 'UTF-8',
		'MAILTYPE'		=> 'HTML',	// HTML 或者其它值(表示是文本)
		'USER_ACTIVE_SUBJECT'	=> '5ihzp 用户激活邮件',
		'USER_FINDPW_SUBJECT'	=> '5ihzp 用户重置密码邮件',

		'ACTIVE_MAIL_EXPIRE'	=> 1440,
		'FINDPW_MAIL_EXPIRE'	=> 10,
	),
);
