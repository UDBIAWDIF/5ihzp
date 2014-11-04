<?php
/**
 * 图片点赞模型
 *
 * @author UID
 * @date 2014.07.30
 */
class ThumbupPicModel extends BaseModel {

	protected $_auto = array(
		array('tp_user_id', 'getUid', 1, 'function'),
		array('tp_pic_id', 'intval', 1, 'function'),
		array('tp_createtime', NOW_TIME, 1),
	);

	protected $_validate = array(
		array(
			'tp_user_id,tp_pic_id', 'alreadyThumbup',
			'ALREADY_THUMBUP', 1,
			'callback', 3
		),	// 您已经赞过此照片
	);

	public function topThumbupAtMonth($p_count = 10) {
		// return $this->_top('u_pic_thumpup_count', intval($p_count));
		$monthDuration['tp_createtime'] = array('between', array(NOW_TIME - 259200, NOW_TIME));
		$topThumbup = $this
				->where($monthDuration)
				->join(
					C('DB_PREFIX')
					. 'user ON '
					. C('DB_PREFIX')
					. 'user.u_id ='
					. C('DB_PREFIX')
					. 'thumbup_pic.tp_pic_user_id'
				)
				->field(array(
					'count(tp_pic_id)'	=> 'thumbup_count',
					'tp_pic_user_id'	=> 'u_id',
					'u_name',
				))
				->limit(intval($p_count))
				->cache(true, 86400)
				->group('tp_pic_user_id')
				->order('thumbup_count DESC')
				->select();

		return $topThumbup;
	}

	protected function alreadyThumbup($map) {
		return !(bool)$this->where($map)->find();
	}

}
