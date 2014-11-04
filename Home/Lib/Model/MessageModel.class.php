<?php
/**
 * 消息模型
 * @author	UID
 * @date	2013.03.29
 */
class MessageModel extends BaseModel {

	protected $_whereMap = array(
		'id'		=> 'id',
		'uid'		=> 'cu_id',
		'isread'	=> 'is_read',
	);

	protected $_order = array(
		'random'	=> 'RAND()',
	);

	//获取消息列表
	public function queryList($params, $fixurl = true) {
		$appList = null;
		$where = $this->makeWhereByParams($params);
		$field = $this->makeFieldByParams($params);
		$order = $this->makeOrderByParams($params);
		$limit = $this->makeListLimit($params, C('DJGAME.MSG_LIST_DEFAULT_COUNT'));
		$this->where($where);
		$this->field($field);
		$this->limit($limit);
		$this->order($order);
		$list = $this->cache(true, C('DJGAME.CACHE_MIDDLE_TIME'))->select();
		if($fixurl) {
			$this->fixUrl($list);
		}
// echo 'params:';dump($params);
// echo $this->_sql(),'<br /><br />';
		return $list;
	}

	public function countReadList($p_params) {
		$params = $p_params;
		$params['isread'] = 1;
		return $this->countList($params);
	}

	public function countNoReadList($p_params) {
		$params = $p_params;
		$params['isread'] = 0;
		return $this->countList($params);
	}

	//获取消息列表总数
	public function countList($params) {
		$where = $this->makeWhereByParams($params);
		$count = $this->where($where)->cache(true, C('DJGAME.CACHE_MIDDLE_TIME'))->count();
		return $count;
	}

	public function picFields() {
		return array(
			'icon',
		);
	}

	//根据请求,组装查询条件
	/* protected function makeWhereByParams($params) {
		$where = array();
		foreach($this->_whereMap as $request => $field) {
			if(isset($params[$request])) {
				$where[$field] = $params[$request];
			}
		}

		return $where;
	} */

}