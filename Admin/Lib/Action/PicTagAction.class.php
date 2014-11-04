<?php

class PicTagAction extends GlobalAction{

	function _initialize() {
		parent::_initialize();
		$this->setLikeFields(array('pt_name'));
	}

}
