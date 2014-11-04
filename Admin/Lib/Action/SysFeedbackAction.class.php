<?php

/**
 * 系统反馈管理
 *
 * @author UID
 */
class SysFeedbackAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
	}

	public function _before_index(){
		$this->setLikeFields(array('name', 'contact', 'content', 'nickname', 'realname'));
	}

	public function add() {
		$this->display('edit');
	}

	public function _before_detail() {
		$id = intval($_REQUEST['id']);
		$mSFB = M('SysFeedback');
		$cuId = $mSFB->where(array($mSFB->getPk() => $id))->getField('cu_id');
		$customer = M('Customer')->find($cuId);
		$this->assign('customer', $customer);
	}

}
?>
