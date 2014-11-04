<?php
/**
 * 群模型
 *
 * @author UID
 * @date 2014.07.30
 */
class GroupModel extends BaseModel {

	protected $_auto = array(
		array('g_user_id', 'getUid', 1, 'function'),
		array('g_name', 'trim', 3, 'function'),
		array('g_name_pinyin', '_getNamePinyin', 3, 'callback'),
		array('g_icon_id', '_getIcon', 3, 'callback'),
		array('g_region_id', 'intval', 3, 'function'),
		array('g_description', 'trim', 3, 'function'),
		array('g_createtime', NOW_TIME, 1),
		array('g_updatetime', NOW_TIME, 1),
		array('g_updatetime', NOW_TIME, 2),
		array('g_createip', '_getIp', 1, 'callback'),
		array('g_status', 1, 1),
		array('g_user_count', 1, 1),
		array('g_user_limit', 5000, 1),
	);

	protected $_validate = array(
		array(
			'g_id', 'number',
			'未指定群', 1,
			'', 2
		),
		array(
			'g_name', 'require',
			'请填写名称', 1,
			'', 3
		),
	);

	final public function isOwner($p_id, $p_uid) {
		$id		= intval($p_id);
		$uid	= intval($p_uid);
		$where	= array(
			$this->getPk()	=> $id,
			'g_user_id'	=> $uid,
			// 'g_status'	=> 1,
		);

		return (bool)$this->where($where)->getField($this->getPk());
	}

	final public function invite($p_id, $p_uids) {
		$inviteResult = false;
		$runnable = true;
		$id = intval($p_id);
		$uids = idsTointArray($p_uids);

		if(empty($id)) {
			$runnable = false;
		}

		if($runnable && empty($uids)) {
			$runnable = false;
		}

		if($runnable) {
			$mMsg = D('Msg');
			// 可优化为 ADDALL
			foreach($uids as $uid) {
				$mMsg->inviteJoinGroup($id, $uid);
			}
			$inviteResult = true;
		}

		return $inviteResult;
	}

	final public function getOwnerId($p_id) {
		return (int)$this->getFieldByPkId($p_id, 'g_user_id', true);
	}

	final public function getName($p_id) {
		return $this->getFieldByPkId($p_id, 'g_name', true);
	}

	final public function userIdIsIn($p_id, $p_uid) {
		$isIn	= false;
		$id		= intval($p_id);
		$uid	= intval($p_uid);
		$runnable = true;

		if($this->isOwner($id, $uid)) {
			$isIn = true;
			$runnable = false;
		}

		if($runnable) {
			$isIn = D('GroupUser')->userIdIsInGroup($id, $uid);
		}

		return $isIn;
	}

	final public function disable($p_id) {
		$id		= intval($p_id);
		$group	= array(
			$this->getPk()	=> $id,
			'g_status'	=> 0,
		);
		$disableResult = $this->save($group);

		if($disableResult) {
			$uid = $this->where(array($this->getPk() => $id))->getField('g_user_id');
			$uid = intval($uid);
			!empty($uid) && D('User')->incOrDecData($uid, 'u_group_count', false);
			$this->_removeRelationData($id);
		}

		return $disableResult;
	}

	final public function notJoinFriends($p_id) {
		$id					= intval($p_id);
		$ownerId			= $this->getOwnerId($id);
		$friendIds			= D('User')->queryFirends($ownerId);
		$groupUserIds		= D('GroupUser')->queryUserIds($id);
		$notJoinFriendIds	= array_diff($friendIds, $groupUserIds);
		// TODO: 用户模型封装个方法来获取好友列表,把条件加上
		$notJoinFriends		= D('User')
								->where(array(
									D('User')->getPk() => array('IN', $notJoinFriendIds),
								))
								->order('u_name_pinyin ASC')
								->select();

		return $notJoinFriends;
	}

	final public function countGroup($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return $this
				->where(array(
					'g_user_id'	=> $uid,
					'g_status'	=> 1,
				))
				->count();
	}

	final public function sumGroupFriend($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return (int)$this
				->where(array(
					'g_user_id'	=> $uid,
					'g_status'	=> 1,
				))
				->sum('g_user_count');
	}

	final public function userAskForJoin($p_id, $p_uid = 0, $p_reason = '') {
		$runnable	= true;
		$joinResult	= false;

		$id			= intval($p_id);
		if(empty($id)) {
			$runnable		= false;
			$this->error	= '未指定群';
		}

		if($runnable) {
			$uid		= intval($p_uid);
			$uid		= empty($uid) ? getUid() : $uid;
			$reason		= filter_str(trim($p_reason));
			$joinResult	= D('Msg')->userAskForJoinGroup($id, $uid, $reason);
			if(!$joinResult) $this->error = D('Msg')->getError();
		}

		return $joinResult;
	}

	final public function countJoinGroup($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return D('GroupUser')->countJoinGroup($uid);
	}

	final public function friendIdList($p_uid = 0) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();
		return D('GroupUser')->friendIdList($uid);
	}

	final public function userJoinToGroup($p_uid, $p_gid) {
		$uid = intval($p_uid);
		$gid = intval($p_gid);
		if(!empty($uid) && !empty($gid)) {
			D('GroupUser')->userJoinToGroup($uid, $gid);
			$this->updateUserCount($gid);
		}
	}

	final public function updateUserCount($p_gid) {
		$gid = intval($p_gid);
		if(!empty($gid)) {
			$userCount = D('GroupUser')->userCount($gid);
			$this
				->where(array($this->getPk() => $gid))
				->save(array('g_user_count' => $userCount));
		}
	}

	final public function topGroup($p_count = 10) {
		return $this
				->where('g_status = 1')
				->field(true)
				//->cache(3600)
				->order('g_user_count DESC')
				->limit(intval($p_count))
				->select();
	}

	protected function _getIp() {
		return get_client_ip(1);
	}

	protected function _getIcon() {
		$logoId	= 0;
		$logo	= trim(filter_str($_POST['icon_path']));
		if(!empty($logo) && is_file(UPLOAD_MAIN_DIR . $logo)) {
			$gpId	= intval($_POST[$this->getPk()]);
			$logoId	= (int)D('Icon')->addGroupIcon($logo, $gpId);
		}
		return $logoId;
	}

	protected function _getNamePinyin() {
		$name	= trim(filter_str($_POST['g_name']));
		$pinyin	= pinyin($name, 1);
		$pinyin	= rtrim($pinyin, '_');
		return $pinyin;
	}

	private function _removeRelationData($p_id) {
		$id = intval($p_id);
		D('GroupUser')->where(array('gu_group_id'		=> $id))->delete();
		D('GroupTagRel')->where(array('gtr_group_id'	=> $id))->delete();
	}

}
