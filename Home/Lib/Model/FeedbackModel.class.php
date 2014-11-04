<?php
/**
 * 评论
 */
class FeedbackModel extends BaseModel {

	protected $pk = 'feedback_id';

	public function addFB($params) {
		load('extend');
		$content = msubstr(
						$params['content'],
						0,
						C('DJGAME.FB_CONTENT_SUB_LENGTH'),
						'utf-8',
						false
					);
		$feedback = array(
			'feedback_aid'			=> $params['id'],
			'feedback_uid'			=> $params['uid'],	//TODO通过SESSION关联获取
			'feedback_username'		=> isset($params['contact']) ? $params['contact'] : '',
			'feedback_msg'			=> $content,
			'feedback_star'			=> $params['star'],
			'feedback_moblie_model'	=> $params['model'],
			'feedback_moblie_imei'	=> $params['imei'],
			'feedback_status'		=> 1,
			'feedback_added'		=> $_SERVER['REQUEST_TIME'],
			'feedback_type'			=> C('DJGAME.REFTYPE_APP'),
		);
		$addResult = $this->add($feedback);
		if($addResult) {
			M('App')->where("app_id = {$params['id']}")->setInc('app_fb_count');
		}
	}//end addFB()

	public function countFB($aid, $type) {
		return $this
				->where(array(
					'feedback_aid'	=> intval($aid),
					'feedback_type'	=> intval($type),
				))
				->count();
	}

}
