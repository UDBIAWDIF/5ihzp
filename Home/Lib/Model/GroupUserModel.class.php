<?php
/**
 * 群用户模型
 *
 * @author UID
 * @date 2014.09.04
 */
class GroupUserModel extends BaseModel {

	final public function queryUserIds($p_gpId) {
		$userIds = $this
					->where(array('gu_group_id' => intval($p_gpId)))
					->getField('gu_user_id', true);
		return $userIds;
	}

	final public function countJoinGroup($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return $this->where(array('gu_user_id' => $uid))->count();
	}

	final public function userIdIsInGroup($p_gpId, $p_uid) {
		$gpId	= intval($p_gpId);
		$uid	= intval($p_uid);
		$where	= array(
			'gu_group_id'	=> $gpId,
			'gu_user_id'	=> $uid,
		);

		return (bool)$this->where($where)->getField('gu_group_id');
	}

	final public function userJoinToGroup($p_uid, $p_gid) {
		$uid = intval($p_uid);
		$gid = intval($p_gid);
		if(!empty($uid) && !empty($gid)) {
			if(!$this->userIdIsInGroup($gid, $uid)) {
				$this->add(array(
					'gu_group_id'	=> $gid,
					'gu_user_id'	=> $uid,
					'gu_createtime'	=> NOW_TIME,
					'gu_createip'	=> get_client_ip(1),
				));
			}
		}
	}

	final public function userCount($p_gid) {
		$userCount = 0;
		$gid = intval($p_gid);
		if(!empty($gid)) {
			$userCount = $this
							->where(array('gu_group_id' => $gid))
							->count('gu_group_id');
		}
		return intval($userCount);
	}

	final public function friendIdList($p_uid = 0) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();
		$joinGroupList = $this
							->where(array('gu_user_id' => $uid))
							->getField('gu_group_id', true);
		$joinGroupList = array_map('intval', $joinGroupList);

		$friendIdList = $this
						->where(array('gu_group_id' => array('IN', $joinGroupList)))
						->group('gu_user_id')
						->getField('gu_user_id', true);

		return array_map('intval', $friendIdList);
	}

}
