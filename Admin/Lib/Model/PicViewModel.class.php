<?php

class PicViewModel extends BaseViewModel {

	protected $pk				= 'pic_id';

	protected $_listWithComment	= false;
	protected $_listWithPicTag	= false;

	protected function _initialize() {
		parent::_initialize();
		$this->_initViewField();
	}

	final public function getInfo($p_id) {
		$id = intval($p_id);
		return $this->find($id);
	}

	final public function getOrderField() {
		return 'pic_id';
	}

	final public function withPicTag() {
		$this->viewFields['Region']['_type'] = 'LEFT';
		$this->viewFields['PicTagRel'] = array(
			'ptr_pic_id',
			'ptr_tag_id' => 'tag',
			'_on' => 'Pic.pic_id = PicTagRel.ptr_pic_id',
		);
		$this->_listWithPicTag = true;
	}

	public function select($options=array()) {
		if($this->_listWithPicTag) {
			// $this->group('ptr_pic_id');
			$this->_listWithPicTag = false;
		}
		$list = parent::select($options);
		$this->_initViewField();

		return $list;
	}

	private function _initViewField() {
		$this->viewFields = array(
			'Pic' => array(
				'pic_id',
				'pic_user_id',
				'pic_path',
				'pic_width',
				'pic_height',
				'pic_region_detail',
				'pic_region_longitude',
				'pic_region_latitude',
				'pic_region_id',
				'pic_createtime',
				'pic_format_createtime',
				'pic_thumbup_count',
				'pic_detail',
				'pic_status',
				'_type' => 'LEFT',
			),
			'User' => array(
				'u_id',
				'u_name',
				'u_password',
				'u_name_pinyin',
				'u_icon_id',
				'u_region_id',
				'u_email',
				'u_sign',
				'u_group_limit',
				'u_group_count',
				'u_pic_count',
				'u_pic_thumpup_count',
				'u_like_user_count',
				'u_fans_count',
				'u_comment_count',
				'u_createtime',
				'u_reg_ip',
				'u_updatetime',
				'u_last_logintime',
				'u_last_loginip',
				'u_status',
				'_type' => 'LEFT',
				'_on' => 'Pic.pic_user_id = User.u_id',
			),
			'Region' => array(
				'region_id',
				'parent_id',
				'region_name',
				'region_level',
				'_on' => 'Pic.pic_region_id = Region.region_id',
			),
		);
		$this->_listWithPicTag = false;
	}

}
