<?php

class GroupTagAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
		$this->setLikeFields(array('gt_name'));
	}

}
