<?php

class IndexAction extends GlobalAction {

	public function index() {
		$this->assign('date', NOW_TIME);
		$this->display();
	}

	public function main() {
		$User = D('UserView');
		$map=array('User.id'=>$this->getUid());

		$vo = $User->where($map)->find();
		$this->assign('vo', $vo);
		$this->display();
	}

	/**
	 * 当前用户个人资料
	 * @access public
	 * @return void
	 */
	public function modify(){
		if(!$_GET['id']){
			$_GET['id']=getCurrentUserId();
		}
		parent::edit('Users','modify');
	}

	public function resetPassword() {
		$this->assign('id',$this->getUid());
		$this->display();
	}

	public function doEdit(){
		parent::doEdit('Users');
	}
	public function reset() {
		$_POST['id'] = intval($this->getUid());
		$mUsers = M('Users');
		$oldPW = $mUsers->where('id=' . $_POST['id'])->field('password')->find();
		if($oldPW['password'] !== md5($_POST['oldpassword'])) {
			$this->error('旧密码错误！');
			return 1;
		} else {
			unset($_POST['oldpassword']);
		}

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
			$this->assign('jumpUrl', __APP__.'/' . MODULE_NAME . '/main');
			$this->success('用户资料修改成功！');
		}else {
			$this->error('数据提交失败！');
		}
	}

}

?>