<?php
/**
 * 图片模型
 *
 * @author UID
 * @date 2014.07.30
 */
class PicModel extends BaseModel {

	const WHOLIKE_CACHE_PRE = 'WHOLIKEPICID_';

	protected $_auto = array(
		array('pic_user_id', 'getUid', 1, 'function'),
		array('pic_path', 'trim', 1, 'function'),
		array('pic_width', 'getWidth', 1, 'callback'),
		array('pic_height', 'getHeight', 1, 'callback'),
		array('pic_size', 'getSize', 1, 'callback'),
		array('pic_region_detail', 'trim', 3, 'function'),
		array('pic_region_longitude', 'floatval', 3, 'function'),
		array('pic_region_latitude', 'floatval', 3, 'function'),
		array('pic_region_id', 'getRegionId', 3, 'callback'),
		array('pic_createtime', NOW_TIME, 1),
		array('pic_format_createtime', 'datetime', 1, 'function'),
		array('pic_thumbup_count', 0, 1),
		array('pic_detail', 'trim', 3, 'function'),
		array('pic_status', 1, 1),
	);

	protected $_validate = array(
		array(
			'pic_path', 'isFile',
			'请上传照片', 1,
			'callback', 3
		),
	);

	final public function isOwner($p_id, $p_uid) {
		$id		= intval($p_id);
		$uid	= intval($p_uid);
		$where	= array(
			$this->getPk()	=> $id,
			'pic_user_id'	=> $uid,
		);

		return (bool)$this
					->where($where)
					->cache(true)
					->getField($this->getPk());
	}

	final public function disable($p_id) {
		$id		= intval($p_id);
		$pic	= array(
			$this->getPk()	=> $id,
			'pic_status'	=> 0,
		);
		$disableResult = $this->save($pic);

		if($disableResult) {
			$uid = $this->where(array($this->getPk() => $id))->getField('pic_user_id');
			$uid = intval($uid);
			!empty($uid) && D('User')->incOrDecData($uid, 'u_pic_count', false);
		}

		return $disableResult;
	}

	final public function thumbup($p_id) {
		// 1:赞成功; -1:取消赞成功; false:操作失败
		$thumbResult = false;
		$id = intval($p_id);

		$ownerId = $this->getOwnerId($id);
		$mThumbupPic = D('ThumbupPic');
		$thumbup = array(
			'tp_user_id'		=> getUid(),
			'tp_pic_id'			=> $id,
			'tp_pic_user_id'	=> $ownerId,
			'tp_createtime'		=> NOW_TIME,
		);
		if($mThumbupPic->create($thumbup)) {
			$thumbResult = $mThumbupPic->add();
			// $thumbResult && $thumbResult = 1;
			if($thumbResult) {
				$thumbResult = 1;
				D('Msg')->userThumbupPic($id, getUid());
			}
		} else if('ALREADY_THUMBUP' == $mThumbupPic->getError()) {	// 已赞过的话, 取消赞
			$thumbupToDel = array(
				'tp_user_id'	=> $thumbup['tp_user_id'],
				'tp_pic_id'		=> $thumbup['tp_pic_id'],
			);
			$thumbResult = $mThumbupPic->where($thumbupToDel)->delete();
			if(false !== $thumbResult) {
				$thumbResult = -1;
			}
		}

		if($thumbResult) {
			$inc = false;
			if($thumbResult > 0) {
				$inc = true;
				D('LikeUser')->like($ownerId, getUid());
			} else {
				// 清 赞了照片的人 缓存
				S(self::WHOLIKE_CACHE_PRE . $id, null);
			}
			$this->incOrDecData($id, 'pic_thumbup_count', $inc);
		} else {
			$this->error = $mThumbupPic->getError();
		}

		return $thumbResult;
	}

	final public function incOrDecData($p_id, $p_field, $p_inc = true) {
		$id			= intval($p_id);
		$field		= trim($p_field);
		$setFunc	= $p_inc ? 'setInc' : 'setDec';
		$picUserFieldMap = array(
			'pic_thumbup_count'	=> 'u_pic_thumpup_count',
		);

		$incResult	= $this
				->where(array(
					$this->getPk() => $id,
				))
				->$setFunc($field);
		if($incResult && isset($picUserFieldMap[$field])) {
			$uid	= (int)$this
						->where(array(
							$this->getPk() => $id,
						))
						->cache(true)
						->getField('pic_user_id');
			D('User')->incOrDecData($uid, $picUserFieldMap[$field], $p_inc);
		}

		return $incResult;
	}

	final public function getOwner($p_picId) {
		$picId	= intval($p_picId);
		$userId	= $this->getOwnerId($picId);
		$user	= D('User')->find($userId);
		return $user;
	}

	final public function getOwnerId($p_picId) {
		$picId	= intval($p_picId);
		$userId	= $this->field('pic_user_id')->cache(true)->find($picId);
		return intval($userId['pic_user_id']);
	}

	final public function whoLike($p_picId) {
		$picId		= intval($p_picId);
		$cacheKey	= self::WHOLIKE_CACHE_PRE . $picId;
		$likeLimit	= 10;	// TODO: 写成配置
		$whoLike	= S($cacheKey);
		if(empty($whoLike) || count($whoLike) < $likeLimit) {
			$whoLike = D('ThumbupPicView')
						->where(array(
							'tp_pic_id' => $picId,
						))
						->limit($likeLimit)
						->select();
			S($cacheKey, $whoLike);
		}

		return $whoLike;
	}

	protected function isFile($data) {
		$file = UPLOAD_MAIN_DIR . trim($data);
		return is_file($file);
	}

	protected function getSize() {
		$file = UPLOAD_MAIN_DIR . trim($_POST['pic_path']);
		return filesize($file);
	}

	protected function getWidth() {
		$file		= UPLOAD_MAIN_DIR . trim($_POST['pic_path']);
		$picSize	= getimagesize($file);
		return $picSize[0];
	}

	protected function getHeight() {
		$file		= UPLOAD_MAIN_DIR . trim($_POST['pic_path']);
		$picSize	= getimagesize($file);
		return $picSize[1];
	}

	protected function getRegionId() {
		$runnable	= true;
		$regionId	= 0;

		$region		= trim($_POST['pic_region_detail']);
		if(empty($region)) {
			$runnable = false;
		}

		if($runnable) {
			$region = explode(',', trim($_POST['pic_region_detail']));
			if(empty($region[2])) {
				$runnable = false;
			}
		}

		if($runnable) {
			$regionId = D('Region')->getIdByName($region[2]);
		}

		return (int)$regionId;
	}

}
