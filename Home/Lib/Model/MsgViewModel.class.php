<?php

/**
 * 消息视图模型
 *
 * @author UID
 * @date 2014.08.31
 */
class MsgViewModel extends BaseViewModel {

	protected $pk = 'msg_id';

	public function _initialize() {
		parent::_initialize();
		$this->viewFields = array(
			'Msg' => array(
				'msg_id',
				'msg_user_id',
				'msg_type',
				'msg_content',
				'msg_rel_id',
				'msg_sender_id',
				'msg_is_read',
				'msg_createtime',
				'msg_status',
				'_type'		=> 'LEFT',
			),
			'Sender' => array(
				'_table'	=> C('DB_PREFIX') . 'user',
				'u_id'		=> 'sender_id',
				'u_icon_id'	=> 'sender_icon_id',
				'u_name'	=> 'sender_name',
				'_on'		=> 'Msg.msg_sender_id = Sender.u_id',
				'_type'		=> 'LEFT',
			),
			'User' => array(
				'u_id',
				'u_name',
				'_on'		=> 'Msg.msg_user_id = User.u_id',
			),
		);
	}

	final public function countNoRead($p_uid = 0) {
		$count = $this
				->where($this->_listWhere($p_uid, false))
				->count();
		return $count;
	}

	final public function queryNoReadList($p_uid = 0) {
		return $this->queryListByUserId($p_uid, false);
	}

	final public function queryReadList($p_uid = 0) {
		return $this->queryListByUserId($p_uid, true);
	}

	final public function queryListByUserId($p_uid = 0, $isRead = true) {
		return $this
				->where($this->_listWhere($p_uid, $isRead))
				->select();
	}

	final public function getOrderField() {
		return 'msg_createtime';
	}

	private function _listWhere($p_uid, $isRead = true) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();
		return array(
					'msg_user_id'	=> $uid,
					'msg_is_read'	=> $isRead ? 1 : 0,
					'msg_status'	=> 1,
				);
	}

}
