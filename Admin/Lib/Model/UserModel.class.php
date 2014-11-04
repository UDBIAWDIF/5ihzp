<?php

class UserModel extends Model {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('username','require','用户名必须！',0),
		array('username','','帐号名称已经存在！',0,'unique',1),
		array('password','require','密码必须！',0),
		array('password2','password','确认密码不正确',0,'confirm'),
	);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1'),
		array('password','md5',1,'function'),
		array('username','trim',1,'function'),
		array('realname','trim',1,'function'),
		array('penname','trim',3,'function'),
		array('birthday','strtotime',3,'function'),
		array('entrytime','strtotime',3,'function'),
	);

	/**
	 *
	 * 修改管理员花费
	 * @param unknown_type $uinfo
	 */
	function adminPayUp($uinfo){
		$user = $this->find($uinfo['uid']);
		$user['pay'] = $uinfo['pay'];
		return $this->save($user);
	}

}
