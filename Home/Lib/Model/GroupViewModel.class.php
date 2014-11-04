<?php

/**
 * 群视图模型
 *
 * @author UID
 * @date 2014.07.30
 */
class GroupViewModel extends BaseViewModel {

	protected $pk = 'g_id';

	protected $viewFields = array(
		'Group' => array(
			'g_id',
			'g_user_id',
			'g_name',
			'g_name_pinyin',
			'g_icon_id',
			'g_region_id',
			'g_user_count',
			'g_user_limit',
			'g_description',
			'g_createtime',
			'g_createip',
			'g_updatetime',
			'g_status',
			'_as'	=>'Groups',
			'_type'	=> 'LEFT',
		),
		'Region' => array(
			'region_id',
			'parent_id',
			'region_name',
			'region_level',
			'_on' => 'Groups.g_region_id = Region.region_id',
			'_type'	=> 'LEFT',
		),
		'User' => array(
			'u_name',
			'u_name_pinyin',
			'_on' => 'Groups.g_user_id = User.u_id',
		),
	);

	final public function queryList($p_start = 0, $p_count = 10, $p_userId = 0, $orderField = 'g_createtime') {
		$start	= intval($p_start);
		$count	= intval($p_count);
		$userId	= intval($p_userId);
		$where	= array('g_status' => 1);
		!empty($userId) && $where['g_user_id'] = $userId;
		$this->where($where);
		$this->limit($start, $count);
		$this->order($orderField . ' DESC');
		return $this->select();
	}

	final public function queryListByCurUser() {
		// TODO: LIMIT VALUE
		return $this->queryList(0, 10, getUid());
	}

	final public function queryUserJoinList($p_uid, $p_start = 0, $p_count = 10) {
		$uid	= intval($p_uid);
		$start	= intval($p_start);
		$count	= intval($p_count);
		empty($uid) && $uid = getUid();

		$oldViewFields = $this->viewFields;
		$this->viewFields['User']['_type'] = 'LEFT';
		$this->viewFields['GroupUser'] = array(
			'_on' => 'Groups.g_id = GroupUser.gu_group_id',
		);
		$list = $this
					->where(array(
						'gu_user_id' => $uid,
					))
					->limit($start, $count)
					->order('g_createtime DESC')
					// ->group('gu_group_id')
					->select();

		$this->viewFields = $oldViewFields;
		return $list;
	}

	final public function getOrderField() {
		return 'g_createtime';
	}

}
