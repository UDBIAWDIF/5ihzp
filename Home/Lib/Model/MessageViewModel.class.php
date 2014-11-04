<?php
/**
 * 消息视图模型
 * @author	UID
 * @date	2013.03.28
 */
class MessageViewModel extends BaseViewModel {

	protected $pk = 'id';

	protected $viewFields = array(
		'Message' => array(
			'id',
			'cu_id',
			'type',
			'rec_id',
			'sender',
			'title',
			'icon',
			'content',
			'ctime',
			'utime',
			'is_read',
			'_type' => 'LEFT',
		),
		'SysMessage' => array(
			'type'	=> 'sysmsg_type',
			'_on'	=> 'Message.rec_id = SysMessage.id',
			'_type'	=> 'LEFT',
		),
		'App' => array(
			'app_id',
			'app_userid',
			'app_mod_userid',
			'app_name',
			'app_from',
			'app_url',
			'app_store',
			'app_upload_time',
			'app_cover',
			'app_icon',
			'app_screenshot',
			'app_size',
			'app_os',
			'app_format',
			'app_ver_code',
			'app_ver_name',
			'app_ver_num',
			'app_fw_id',
			'app_ios_type',
			'app_itunes_id',
			'app_package',
			'app_is_network',
			'app_cg_id',
			'app_chinese_type',
			'app_developer',
			'app_keywords',
			'app_homepage',
			'app_email',
			'app_description',
			'app_detail',
			'app_editor_comment',
			'app_video_url',
			'app_evaluate_url',
			'app_grade',
			'app_star_level',
			'app_pageview',
			'app_day_downloads',
			'app_week_downloads',
			'app_downloads',
			'app_is_recommend',
			'app_is_top',
			'app_is_authorize',
			'app_utime',
			'app_ctime',
			'app_status',
			'app_order',
			'app_alias_name',
			'app_thumb_up',
			'app_fb_count',
			'app_last_ver',
			'app_last_ver_num',
			'app_network_disk',
			'`from`',
			'_on' => 'SysMessage.rec_id = App.app_id',
		),
	);

	protected $_whereMap = array(
		'id'		=> 'id',
		'isread'	=> 'is_read',
		'uid'		=> 'cu_id',
	);

	protected $_order = array();

	protected $_likeFields = array();

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
// dump($params);
// echo $this->_sql(),'<br /><br />';
		return $list;
	}

	//读取一条消息
	public function readOne($p_params) {
		//一期直接获取 关联系统消息->关联应用
		//后续版本判断消息TYPE,调用相应的获取消息的方法(type => 方法 做成 MAP)
		$params = $p_params;
		$params['count'] = 1;
		$msg = $this->queryList($params);
		if(is_array($msg)) {
			$msg = $msg[0];
			$this->_setIsRead($msg[$this->getPk()]);
		}

		return $msg;
	}

	//获取消息列表总数
	public function countList($params) {
		$where = $this->makeWhereByParams($params);
		$count = $this->where($where)->cache(true, C('DJGAME.CACHE_MIDDLE_TIME'))->count();
		return $count;
	}

	public function picFields() {
		return array(
			'app_cover',
			'app_icon',
			'app_screenshot',
		);
	}

	public function appFields() {
		return array(
			'app_store',
		);
	}

	//根据请求,组装查询条件
	protected function makeWhereByParams($params) {
		$where = array(
				'app_status'	=> 1,
				'app_os'		=> self::$android_id,
			);
		foreach($this->_whereMap as $request => $field) {
			if(isset($params[$request])) {
				$where[$field] = $params[$request];
			}
		}
		$whereLike = array();
		foreach($this->_likeFields as $request => $fields) {
			if(isset($params[$request])) {
				if(is_array($fields)) {
					$fields = implode('|', $fields);
				}
				$whereLike[$fields] = array('like', "%{$params[$request]}%");
			}
		}
		if(!empty($whereLike)) {
			$whereLike['_logic'] = 'AND';
			$where['_complex'] = $whereLike;
		}

		return $where;
	}

	protected function makeOrderByParams($params, $defaultOrder = 'ctime', $defaultSort = 'DESC') {
		$order = '';
		if('random' == $params['order']) {
			$order = 'RAND()';
		} else {
			if(!empty($params['order'])) {
				if(isset($this->_order[$params['order']])) {
					$order = $this->_order[$params['order']];
				}
			}
			$order  = empty($order) ? $defaultOrder : $order;
			$order .= " {$defaultSort}";
		}

		return $order;
	}

	private function _setIsRead($p_id) {
		$id = intval($p_id);
		M('Message')->where(array($this->getPk() => $id))->setField('is_read', 1);
	}

}