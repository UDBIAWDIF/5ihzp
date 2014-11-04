<?php
/**
 * 评论
 */
class FeedbackViewModel extends BaseViewModel {

	protected $pk = 'feedback_id';

	protected $viewFields = array(
		'Feedback' => array(
			'feedback_id',
			'feedback_aid',
			'feedback_uid',
			'feedback_username',
			'feedback_moblie_model',
			'feedback_moblie_imei',
			'feedback_arctitle',
			'feedback_ip',
			'feedback_added',
			'feedback_mid',
			'feedback_msg',
			'feedback_status',
			'feedback_admin',
			'feedback_quote',
			'feedback_star',
			'feedback_good',
			'feedback_type',
			'_type' => 'LEFT',
		),
		'Customer' => array(
			//'`id`',
			'name',
			//'`from`',
			'nickname',
			'realname',
			//'password',
			'head',
			'sex',
			//'birthday',
			//'score',
			//'comment',
			//'reply',
			//'collect',
			//'attention',
			//'description',
			//'mobile_imei',
			//'mobile_model',
			//'ctime',
			//'utime',
			//'status',
			'_on' => 'Feedback.feedback_uid = Customer.id',
		),
	);

	//获取评论列表
	public function queryList($params) {
		$where = $this->_makeListWhere($params);
		$field = $this->makeFieldByParams($params);
		$limit = $this->_makeListLimit($params, C('DJGAME.FEEDBACK_LIST_DEFAULT_COUNT'));
		$this->where($where);
		$this->field($field);
		$this->limit($limit);
		$this->order('feedback_added DESC');
		$this->cache(true, C('DJGAME.CACHE_SHORT_TIME'));
		$list = $this->select();

		return $list;
	}//end queryList()

	//获取评论列表总数
	public function countList($params) {
		$where = $this->_makeListWhere($params);
		$count = $this->where($where)->cache(true, C('DJGAME.CACHE_SHORT_TIME'))->count();
		return $count;
	}//end countList()

	private function _makeListWhere($params) {
		return array(
					'feedback_aid'		=> $params['id'],
					'feedback_type'		=> $params['feedback_type'],
					'feedback_status'	=> 1,
				);
	}//end _makeListWhere()

	private function _makeListLimit($params, $defaultCount) {
		$start = isset($params['start']) ? $params['start'] : 0;
		$count = isset($params['count']) ? $params['count'] : $defaultCount;
		return "{$start},{$count}";
	}//end _makeListLimit()

}
