<?php
/**
 * 消息模型
 *
 * @author UID
 * @date 2014.08.29
 */
class MsgModel extends BaseModel {

	const APPLY_JOIN_GROUP			= 'apply_join_group';			// 申请入群
	const INVITE_JOIN_GROUP			= 'invite_join_group';			// 邀请入群
	const PASS_INVITE_JOIN_GROUP	= 'pass_invite_join_group';		// 邀请入群通过
	const PASS_APPLY_JOIN_GROUP		= 'pass_apply_join_group';		// 申请入群通过
	const REJECT_APPLY_JOIN_GROUP	= 'reject_apply_join_group';	// 申请入群被拒
	const THUMBUP_YOUR_PIC			= 'thumbup_your_pic';			// 有人对你照片点赞
	const LIKE_YOUR					= 'like_your';					// 有人关注你
	const COMMENT_YOUR_PIC			= 'comment_your_pic';			// 有人评论你照片

	protected $_auto = array(
		array('msg_user_id', 'intval', 1, 'function'),
		array('msg_type', 'trim', 1, 'function'),
		array('msg_content', 'filter_str', 1, 'function'),
		array('msg_rel_id', 'intval', 1, 'function'),
		array('msg_sender_id', 'intval', 1, 'function'),
		array('msg_is_read', 0, 1),
		array('msg_createtime', NOW_TIME, 1),
		array('msg_status', 1, 1),
	);

	protected $_validate = array(
		array(
			'msg_user_id', 'number',
			'未指定用户', 1,
			'', 1
		),
		array(
			'msg_rel_id', 'number',
			'未指定关联数据项', 1,
			'', 1
		),
		array(
			'msg_type', 'require',
			'未指定消息类型', 1,
			'', 1
		),
	);

	// TODO:	1. 群是否可用
	//			2. 控制同一用户对同一群发起申请的时间间隔,比如在10分钟内只能存在三个申请
	final public function userAskForJoinGroup($p_gpId, $p_uid = 0, $p_reason = '') {
		$runnable	= true;
		$addResult	= false;

		$gpId = intval($p_gpId);
		empty($gpId) && $runnable = false;

		if($runnable) {
			$uid = intval($p_uid);
			empty($uid) && $uid = getUid();
			$addMsg = array(
				'msg_rel_id'		=> $gpId,
				'msg_user_id'		=> D('Group')->getOwnerId($gpId),
				'msg_sender_id'		=> $uid,
				'msg_type'			=> self::APPLY_JOIN_GROUP,
				'msg_content'		=> $p_reason,
			);
			$this->_addMsgCommonInfo($addMsg);

			$createResult	= $this->create($addMsg);
			$runnable		= $createResult;
		}

		if($runnable) {
			$addResult = $this->add();
		}

		return $addResult;
	}

	// 邀请入群
	final public function inviteJoinGroup($p_gpId, $p_uid = 0) {
		$runnable	= true;
		$addResult	= false;

		$gpId = intval($p_gpId);
		empty($gpId) && $runnable = false;

		$uid = intval($p_uid);
		if($runnable) {
			empty($uid) && $runnable = false;
		}

		if($runnable) {
			$addMsg = array(
				'msg_rel_id'		=> $gpId,
				'msg_user_id'		=> $uid,
				'msg_sender_id'		=> D('Group')->getOwnerId($gpId),
				'msg_type'			=> self::INVITE_JOIN_GROUP,
				'msg_content'		=> getUname()
									. '邀请您加入群: '
									. getGroupName($gpId),
			);
			$this->_addMsgCommonInfo($addMsg);

			$createResult	= $this->create($addMsg);
			$runnable		= $createResult;
		}

		if($runnable) {
			$addResult = $this->add();
		}

		return $addResult;
	}

	final public function userThumbupPic($p_picId, $p_uid = 0) {
		$runnable	= true;
		$addResult	= false;
		$picId		= intval($p_picId);
		empty($picId) && $runnable = false;

		$uid		= intval($p_uid);
		if($runnable) {
			empty($uid) && $uid = getUid();
			empty($uid) && $runnable = false;
		}

		$addMsg = array();
		if($runnable) {
			$addMsg = array(
				'msg_rel_id'		=> $picId,
				'msg_user_id'		=> D('Pic')->getOwnerId($picId),
				'msg_sender_id'		=> $uid,
				'msg_type'			=> self::THUMBUP_YOUR_PIC,
			);
			$runnable = !(bool)$this
								->where($addMsg)
								->getField($this->getPk());
		}

		if($runnable) {
			$addMsg['msg_content'] = getUname() . '赞了你的照片！';
			$this->_addMsgCommonInfo($addMsg);
			$createResult	= $this->create($addMsg);
			$runnable		= $createResult;
		}

		if($runnable) {
			$addResult = $this->add();
		}

		return $addResult;
	}

	final public function likeUser($p_uid = 0, $p_byUid = 0) {
		$runnable	= true;
		$addResult	= false;
		$uid		= intval($p_uid);
		$byUid		= intval($p_byUid);

		empty($uid) && $runnable = false;
		if($runnable) {
			empty($uid) && $uid = getUid();
			empty($uid) && $runnable = false;
		}

		$addMsg = array();
		if($runnable) {
			$addMsg = array(
				'msg_user_id'		=> $uid,
				'msg_sender_id'		=> $byUid,
				'msg_type'			=> self::LIKE_YOUR,
			);
			$runnable = !(bool)$this
								->where($addMsg)
								->getField($this->getPk());
		}

		if($runnable) {
			$addMsg['msg_rel_id']	= $uid;
			$addMsg['msg_content']	= getUname() . '关注了你！';
			$this->_addMsgCommonInfo($addMsg);
			$createResult	= $this->create($addMsg);
			$runnable		= $createResult;
		}

		if($runnable) {
			$addResult = $this->add();
		}

		return $addResult;
	}

	private function _addMsgCommonInfo(&$addMsg) {
		$addMsg['msg_is_read']		= 0;
		$addMsg['msg_status']		= 1;
		$addMsg['msg_createtime']	= NOW_TIME;
	}

}
