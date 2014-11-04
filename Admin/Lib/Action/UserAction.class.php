<?php

class UserAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
		$this->setLikeFields(array('u_name','u_name_pinyin'));
	}

	public function resetPassword() {
		$this->display();
	}

	public function reset() {
		$_POST['id'] = intval($_POST['id']);
		$mUser = D('User');

		$_POST['password'] = md5($_POST['password']);
		$_POST['password2'] = md5($_POST['password2']);
		if($_POST['password'] !== $_POST['password2']) {
			$this->error('两次输入的密码不一样！');
			return 1;
		} else {
			unset($_POST['password2']);
		}

		$mUser->create();
		$vo = $mUser->save();
		if($vo) {
			$this->success('用户资料修改成功！');
		}else {
			$this->error('数据提交失败！');
		}
	}

}
?>
