<?php
/**
 * 用户点赞视图模型
 *
 * @author UID
 * @date 2014.07.31
 */
class ThumbupPicViewModel extends ViewModel {

	protected $viewFields = array(
		'ThumbupPic' => array(
			'tp_pic_id',
			'tp_user_id',
			'_type' => 'LEFT',
		),
		'User' => array(
			'u_id',
			'u_name',
			'u_icon_id',
			'u_region_id',
			'u_email',
			'_type' => 'LEFT',
			'_on' => 'ThumbupPic.tp_user_id = User.u_id',
		),
	);

}
