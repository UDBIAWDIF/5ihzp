<?php

/**
 * 系统版本管理
 *
 * @author UID
 */
class SysVersionAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
	}

	public function _before_index(){
		$this->setLikeFields(array('ver'));
	}

	public function _before_add() {
		$this->_before_edit();
	}

	public function _before_edit() {
		$channel = M('Channel')->where('status != 0')->select();
		$this->assign('channel', $channel);
	}

	public function add() {
		$this->display('edit');
	}

	public function upSoft() {
		$up_result = $this->_upFile(C('FILE_UP_TYPE.APP_SOFT'), true);
		$this->_json_response($up_result);
	}

}
?>
