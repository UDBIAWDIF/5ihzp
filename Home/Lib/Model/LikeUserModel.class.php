<?php
/**
 * 用户关注模型
 *
 * @author UID
 * @date 2014.07.30
 */
class LikeUserModel extends BaseModel {

	protected $_auto = array(
		array('lu_by_user_id', 'getUid', 1, 'function'),
		array('lu_like_user_id', 'intval', 1, 'function'),
		array('lu_createtime', NOW_TIME, 1),
	);

	protected $_validate = array(
		array(
			'lu_by_user_id,lu_like_user_id', 'alreadyLike',
			'您已经关注过此人', 1,
			'callback', 3
		),
	);

	final public function addPicOwner($p_picId) {
		$picId	= intval($p_picId);
		$user	= D('Pic')->getOwner($picId);
		return $this->like($user['u_id']);
	}

	final public function like($p_uid = 0, $p_byUid = 0) {
		$uid		= intval($p_uid);
		$byUid		= intval($p_byUid);
		empty($byUid) && $byUid = getUid();
		$likeResult	= false;

		$alreadyLike = $this->_alreadyLike($uid, $byUid);
		if($alreadyLike) {
			$likeInfo = array(
				'lu_by_user_id'		=> $byUid,
				'lu_like_user_id'	=> $uid,
			);
			$likeResult = $this->where($likeInfo)->delete();
		} else {
			$likeInfo = array(
				'lu_by_user_id'		=> $byUid,
				'lu_like_user_id'	=> $uid,
				'lu_createtime'		=> NOW_TIME,
			);
			if($this->create($likeInfo)) {
				$likeResult = $this->add();
			}
		}

		if($likeResult) {
			$this->caclLikeUser($byUid);
			$this->caclFans($uid);
			D('Msg')->likeUser($uid, $byUid);
		}

		return array(
			'status'	=> $likeResult,		// 是否成功执行
			'addordel'	=> !$alreadyLike,	// 关注还是取消(怕 key 太长所以写在这名字), 真 为 add
		);
	}

	final public function isLike($p_byId, $p_likeId) {
		return (bool) $this
					->where(array(
						'lu_by_user_id'		=> intval($p_byId),
						'lu_like_user_id'	=> intval($p_likeId),
					))
					->find();
	}

	final public function isBeLike($p_likeId, $p_byId) {
		return (bool) $this
					->where(array(
						'lu_by_user_id'		=> intval($p_byId),
						'lu_like_user_id'	=> intval($p_likeId),
					))
					->find();
	}

	final public function caclLikeUser($p_uid) {
		$uid = intval($p_uid);
		$likeCount = $this
						->where(array('lu_by_user_id' => $uid))
						->count();
		$mUser = D('User');

		return $mUser->save(array(
			$mUser->getPk() => $uid,
			'u_like_user_count' => $likeCount,
		));
	}

	final public function caclFans($p_uid) {
		$uid = intval($p_uid);
		$fansCount = $this
						->where(array('lu_like_user_id' => $uid))
						->count();
		$mUser = D('User');

		return $mUser->save(array(
			$mUser->getPk() => $uid,
			'u_fans_count' => $fansCount,
		));
	}

	final public function likeUidList($p_uid = 0) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();

		$likeUidList = $this
					->where('lu_by_user_id = ' . $uid)
					->getField('lu_like_user_id', true);
		if(is_array($likeUidList)) {
			$likeUidList = array_map('intval', $likeUidList);
		}

		return $likeUidList;
	}

	final public function topFansAtMonth($p_count = 10) {
		// return $this->_top('u_fans_count', intval($p_count));
		$monthDuration['lu_createtime'] = array('between', array(NOW_TIME - 259200, NOW_TIME));
		$topFans = $this
				->where($monthDuration)
				->join(
					C('DB_PREFIX')
					. 'user ON '
					. C('DB_PREFIX')
					. 'user.u_id ='
					. C('DB_PREFIX')
					. 'like_user.lu_like_user_id'
				)
				->field(array(
					'count(lu_like_user_id)' => 'like_count',
					'u_id',
					'u_name',
				))
				->limit(intval($p_count))
				->cache(true, 86400)
				->group('lu_like_user_id')
				->order('like_count DESC')
				->select();

		return $topFans;
	}

	protected function alreadyLike($map) {
		return !(bool)$this->where($map)->find();
	}

	private function _alreadyLike($p_uid = 0, $p_byUid = 0) {
		$uid	= intval($p_uid);
		$byUid	= intval($p_byUid);
		empty($byUid) && $byUid = getUid();
		return (bool)$this
					->where(array(
						'lu_by_user_id'		=> $byUid,
						'lu_like_user_id'	=> $uid,
					))
					->getField('lu_by_user_id');
	}

}
