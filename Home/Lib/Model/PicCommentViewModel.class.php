<?php

/**
 * 图片评论视图模型
 *
 * @author UID
 * @date 2014.07.19
 */
class PicCommentViewModel extends BaseViewModel {

	protected $pk = 'picc_id';

	protected $viewFields = array(
		'PicComment' => array(
			'picc_id',
			'picc_user_id',
			'picc_pic_id',
			'picc_content',
			'picc_createtime',
			'_type' => 'LEFT',
		),
		'User' => array(
			'u_id',
			'u_name',
			'u_icon_id',
			'_type' => 'LEFT',
			'_on' => 'PicComment.picc_user_id = User.u_id',
		),
	);

	final public function queryListByPic($p_picId, $p_start = 0, $p_count = 100) {
		$picId	= intval($p_picId);
		$start	= intval($p_start);
		$count	= intval($p_count);
		$where	= array('picc_pic_id' => $picId);
		$this->where($where);
		$this->limit($start, $count);
		$this->order('picc_createtime DESC');
		return $this->select();
	}

}
