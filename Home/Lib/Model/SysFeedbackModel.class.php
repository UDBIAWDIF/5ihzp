<?php
/**
 * 对本软件评论
 */
class SysFeedbackModel extends BaseModel {

	protected $pk = 'id';

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
			'cu_id'		=> isset($params['uid'])		? $params['uid']		: 0,
			'contact'	=> isset($params['contact'])	? $params['contact']	: '',
			'content'	=> $content,
			'ctime'		=> $_SERVER['REQUEST_TIME'],
			'status'	=> 1,
		);
		$addResult = $this->add($feedback);

		return $addResult;
	}//end addFB()

}
