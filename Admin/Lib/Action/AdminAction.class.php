<?php

class AdminAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
		$this->setLikeFields(array('adm_name','adm_email','adm_penname','adm_realname'));
	}

	public function _before_add(){
		$groupModel = D('AdminGroup');
		$groups = $groupModel->select();
		$this->assign('list',$groups);
	}

	public function _before_edit(){
		$groupModel = D('AdminGroup');
		$groups = $groupModel->select();
		$this->assign('list',$groups);
	}

	public function resetPassword() {
		$this->display();
	}

	public function reset() {
		$_POST['id'] = intval($_POST['id']);
		$mUsers = D('Admin');

		$_POST['password'] = md5($_POST['password']);
		$_POST['password2'] = md5($_POST['password2']);
		if($_POST['password'] !== $_POST['password2']) {
			$this->error('两次输入的密码不一样！');
			return 1;
		} else {
			unset($_POST['password2']);
		}

		$mUsers->create();
		$vo = $mUsers->save();
		if($vo) {
			$this->success('用户资料修改成功！');
		}else {
			$this->error('数据提交失败！');
		}
	}

}
?>
