<?php

class AdminModel extends Model {
	// 自动验证设置
	protected $_validate = array(
		array('adm_name','require','用户名必须！',0),
		array('adm_name','','帐号名称已经存在！',0,'unique',1),
		array('adm_password','require','密码必须！',0),
		array('password2','adm_password','确认密码不正确',0,'confirm'),
	);

	// 自动填充设置
	protected $_auto = array(
		array('adm_password','md5',1,'function'),
		array('adm_name','trim',1,'function'),
		array('adm_realname','trim',3,'function'),
		array('adm_penname','trim',3,'function'),
		array('adm_description','trim',3,'function'),
		array('adm_createtime','time',1,'function'),
		array('adm_updatetime','time',3,'function'),
		array('adm_status',1,1),
		array('adm_group_id',2,1),
	);

}
