<?php

/**
 * 系统信息推送管理
 *
 * @author UID
 */
class SysMessageAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
	}

	public function _before_index(){
		$this->setLikeFields(array('title', 'rec_id'));
	}

	public function add() {
		$this->display('edit');
	}

}
?>
