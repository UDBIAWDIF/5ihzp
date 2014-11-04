<?php
/**
 * 图片评论模型
 *
 * @author UID
 * @date 2014.07.30
 */
class PicCommentModel extends BaseModel {

	protected $_map = array(
			'pic'		=> 'picc_pic_id',
			'comment'	=> 'picc_content',
	);

	protected $_auto = array(
		array('picc_pic_id', 'intval', 1, 'function'),
		array('picc_content', 'filterContent', 3, 'callback'),
		array('picc_user_id', 'getUid', 1, 'function'),
		array('picc_createtime', NOW_TIME, 1),
	);

	public function _initialize() {
		parent::_initialize();
		$this->_validate = array(
			array(
				'picc_pic_id', 'integer',
				'缺少图片ID', 1,
				'', 1
			),
			array('picc_content', 'checkContentLen',
				'评论内容过长', 0,
				'callback', 1
			),
		);
	}

	protected function checkContentLen($content) {
		return C('COMMENT_LEN_MAX') >= mb_strlen($content, 'UTF-8');
	}

	protected function filterContent($data) {
		return trim(filter_str($data));
	}

}
