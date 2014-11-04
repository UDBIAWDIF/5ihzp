<?php

/**
 * 基类
 *
 * @author UID
 */
class BaseAction  extends Action{

	protected $params;
	protected $_listRows	= 10;
	protected $_pageHandle	= null;

	private $gid;
	private $uid;
	private $userName;
	private $limit;
	private $likeFields;
	private $complex;

	public function _initialize() {
		$fieldMap = array(
			//KEY:字段名 VAL1:方法名 VAL2:方法参数
			'id'		=> array('_request',	'intval'),
			'uid'		=> array('_request',	'intval'),
			'sessid'	=> array('_fInput',		'POST'),
			'star'		=> array('_request',	'intval'),
			'start'		=> array('_request',	'intval'),
			'count'		=> array('_request',	'intval'),
			'cate'		=> array('_request',	'intval'),
			'noread'	=> array('_request',	'intval'),
			'isnetapp'	=> array('_request',	'intval'),
			'network'	=> array('_fInput',		'POST'),
			'name'		=> array('_fInput',		'POST'),
			'order'		=> array('_fInput',		'POST'),
			'package'	=> array('_fInput',		'POST'),
			'current'	=> array('_fInput',		'POST'),
			'keyword'	=> array('_fInput',		'POST'),
			'contact'	=> array('_fInput',		'POST'),
			'content'	=> array('_fInput',		'POST'),
			'model'		=> array('_fInput',		'POST'),
			'imei'		=> array('_fInput',		'POST'),
			'username'	=> array('_fInput',		'POST'),
			'nickname'	=> array('_fInput',		'POST'),
			'realname'	=> array('_fInput',		'POST'),
			'channel'	=> array('_fInput',		'POST'),
			'sign'		=> array('_fInput',		'POST'),
		);
		foreach($fieldMap as $field => $filterMethod) {
			if($this->_isset($field)) {
				if(!isset($filterMethod[1])) {
					$filterMethod[1] = null;
				}
				if(function_exists($filterMethod[0])) {
					$this->params[$field] = $filterMethod[0]($field);
				} else {
					$this->params[$field] = $this->$filterMethod[0]($field, $filterMethod[1]);
				}
			}
		}
		if($this->_isset('password', 'POST')) {
			$this->params['password'] = $_POST['password'];
		}
		if(isset($this->params['keyword'])) {
			load('extend');
			$this->params['keyword']	= msubstr($this->params['keyword'], 0, C('DJGAME.KEYWORD_SUB_LENGTH'), 'utf-8', false);
		}

		$this->assign('thisServerImgPre', C('IMAGE_SERVER_URL'));
	}

	public function blank() {}

	public function index($model = null, $tpl = null) {
		$model = $this->_checkModel($model);
		//列表过滤器，生成查询Map对象
		$map = $this->_search($model);
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if (!empty($model)) {
			$this->_list($model, $map);
		}

		$this->display(trim($tpl));
	}

	public function delete() {
		$this->_checkAuth();
		D(MODULE_NAME)->disable($this->_getId());
		$this->success('删除成功!');
	}

	/**
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * @access protected
	 * @param string $name 数据对象名称
	 * @return HashMap
	 * @throws ThinkExecption
	 */
	//TODO getSearchCondition
	protected function _search($model) {
		//生成查询条件
		$model = $this->_checkModel($model);
		$map = array();

		//如果有 $_REQUEST['_param'] 就把它解出来合并 $_REQUEST ,用它来做查询条件
		if(!empty($_REQUEST['_param'])) {
			$param = unserialize(base64_decode(trim($_REQUEST['_param'])));
			unset($_REQUEST['_param']);
			if(!empty($param)) {
				$_REQUEST = array_merge($_REQUEST, $param);
			}
		}

		$param = array();
		$likeFields = $this->getLikeFields();
		foreach($model->getDbFields() as $val) {
			$currentRequest = '';
			if(isset($_REQUEST[$val])) {
				$currentRequest = filter_str(trim($_REQUEST[$val]));
			}
			if('' != $currentRequest) {
				$param[$val] = $currentRequest;
				if(!empty($likeFields) && is_array($likeFields) && in_array($val, $likeFields)) {
					$map[$val] = array('like', '%' . $currentRequest . '%');
				} else {
					$map[$val] = $currentRequest;
				}
			}
		}

		$complex = $this->getComplex();
		if(!empty($complex)) $map['_complex'] = $this->getComplex();

		$limit = $this->getLimit();
		if(!empty($limit)) {	//现在的限制条件只限于相等,以后有必要时再进行扩展其它条件
			foreach($limit as $k => $v) {
				$map[$k] = $v;
			}
		}

		$this->assign('param', base64_encode(serialize($param)));
		return $map;
	}

	/**
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * @access protected
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
	 * @return void
	 * @throws ThinkExecption
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		import('ORG.Util.Cookie');
		//排序字段 默认为主键名
		if (isset($_REQUEST['_order'])) {
			$order = filter_str($_REQUEST['_order']);
		} else {
			$order = !empty($sortBy) ? $sortBy : (method_exists($model, 'getOrderField') ? $model->getOrderField() : $model->getPk());
		}
		//防止 order 关键字冲突
		$order = trim($order);
		if(strtolower($order) === 'order') {
			$order = '`' . $order . '`';
		}

		//排序方式默认按照倒序排列
		//接受 sort参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST['_sort'])) {
			$sort = filter_str($_REQUEST['_sort']) ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where($map)->count('*');
		if ($count > 0) {
			$listRows = 0;
			if(isset($_REQUEST['listRows'])) {
				$listRows = intval($_REQUEST['listRows']);
			}
			if(empty($listRows)) {
				$listRows = $this->_listRows;
			}
			if(empty($listRows)) {
				$listRows = C('INDEX_ROWS');
			}

			$p = $this->_pageHandle;
			if(empty($p)) {
				import('ORG.Util.PageWithInput', BASE_PATH . 'Common');
				$p = new PageWithInput($count, $listRows);
			} else {
				$p->init($listRows);
				$p->calcPages();
			}

			$pWhere = array();
			if(method_exists($p, 'condition')) {
				$pWhere = $p->condition();
				$pWhere = is_array($pWhere) ? $pWhere : array();
			}

			//分页查询数据
			$model
				->where(array_merge($map, $pWhere))
				->order("$order $sort");
			if(empty($p->disableLimit)) {
				$model->limit($p->firstRow . ',' . $p->listRows);
			}
			if(method_exists($model, 'listTrigger')) {
				$model->listTrigger();
			}
			$voList = $model->select();

			//分页跳转的时候保证查询条件
			foreach($map as $key => $val) {
				$p_field = '';
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
				if(is_array($val)) {
					switch($val[0]) {
						case 'like':
							$p_field = "$key=" . urlencode(trim($val[1], '%')) . "&";
							break;
						default:
					}
				}
				$p->parameter .= $p_field;
			}
			//分页显示
			$page = $p->show();
			//列表排序显示
			$sortImg	= $sort; //排序图标
			$sortAlt	= $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort		= $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign('list',		$voList);
			$this->assign('sort',		$sort);
			$this->assign('order',		$order);
			$this->assign('sortImg',	$sortImg);
			$this->assign('sortType',	$sortAlt);
			$this->assign('page',		$page);
			if(method_exists($p, 'pageNo')) {
				$this->assign('pageNo', $p->pageNo());
			}
		}
		Cookie::set('_currentUrl_', __SELF__);
	}

	/**
	 * 检查所操作的Model
	 * @param string $model 要操作的Model
	 * @access protected
	 * @return void
	 */
	private function _checkModel($model) {
		if(empty($model)) {
			try {
				if(strtolower(ACTION_NAME) == 'index') {
					if (class_exists(MODULE_NAME . 'ViewModel')) {
						return D(MODULE_NAME . 'View');
					}
				}
				return D(MODULE_NAME);
			} catch (ThinkException $e) {
				$this->error('操作失败！<br/>未定义的模型或者是代码错误！请联系开发者！');
			}
		}
		if(is_string($model)) {
			return D($model);
		} else {
			return $model;
		}
	}

	/**
	 * 获取限制列表(数据库查询条件限制)
	 * @access protected
	 * @return array
	 */
	Protected function getLimit() {
		return $this->limit;
	}

	/**
	 * 获取限制列表(数据库查询条件限制)
	 * @access protected
	 * @return void
	 */
	Protected function setLimit($limit) {
		$this->limit = $limit;
	}

	/**
	 * 设置复合查询条件
	 * @access protected
	 * @return void
	 * Example:
		$whereField = $this->_request('name');
		$where['mem_name']      = array('like', "%{$whereField}%");
		$where['mem_nickname']  = array('like', "%{$whereField}%");
		$where['_logic'] = 'or';
		$this->setComplex($where);
	 */
	Protected function setComplex($complex) {
		$this->complex = $complex;
	}

	/**
	 * 获取复合查询条件
	 * @access protected
	 * @return array
	 */
	Protected function getComplex() {
		return $this->complex;
	}

	/**
	 * 获取用户ID
	 * @access protected
	 * @return array
	 */
	Protected function getLikeFields() {
		return $this->likeFields;
	}

	/**
	 * 设置使用like查询的字段
	 * @access protected
	 * @return void
	 */
	Protected function setLikeFields($likeFields) {
		if(is_string($likeFields)) {
			$likeFields = explode(',' , $likeFields);
		}
		$this->likeFields = $likeFields;
	}

	public function getCuId() {
		return $this->params['uid'];
	}

	//组装好需要用于返回的数据成数组
	protected function _responseDataToArray($code = 0, $count = 0, $datatype = '', $data = '') {
		return array(
			'code'		=> $code,
			'count'		=> $count,
			'datatype'	=> $datatype,
			'data'		=> $data,
		);
	}//end _responseDataToArray()

	protected function _outputData($code, $data = '', $count = 0, $msg = '', $exit = false) {
		$outData = array(
						'code'	=> $code,
						'count'	=> $count,
						'data'	=> $data,
						'msg'	=> $msg,
					);
		if(isset($_REQUEST[C('WEB_TEST_FIELD_NAME')])) {
			header('Content-type: text/html; charset=UTF-8');
			dump($outData);
			$exit && exit;
		} else {
			$this->_json_response($outData, $exit);
		}
	}//end _outputData()

	protected function _json_response($data, $exit = false) {
		header('Content-type: text/html; charset=UTF-8');
		echo(json_encode($data));
		$exit && exit;
	}//end _json_response()

	//过滤输入
	protected function _fInput($field, $method = 'REQUEST') {
		$request = '';
		switch(strtoupper($method)) {
			case 'POST' :
				$request = &$_POST;
				break;
			case 'GET' :
				$request = &$_GET;
				break;
			case 'REQUEST' :
				;
			default:
				$request = &$_REQUEST;
		}
		$value = $this->_filter_str($request[$field]);

		return $value;
	}

	//字符串型的过滤
	protected function _filter_str($str) {
		return filter_str($str);
	}

	//判断图片条件限制, 防止服务器被暴
	protected function _checkListLimit($limit, $actionList) {
		if(is_string($actionList)) {
			$actionList = explode('|', $actionList);
		}
		if(in_array(ACTION_NAME, $actionList)) {
			if($this->params['count'] > $limit) {
				$this->_json_response($this->_responseDataToArray(C('ERROR.REQUEST_NUM_OVERFLOW')), true);
			}
		}

		return true;
	}

	protected function checkSecurity() {
		// 检查步骤:
		// 1.是否传SESSID 、 UID
		if(empty($this->params['uid']) || empty($this->params['sessid'])) {
			$this->_outputData(C('ERROR.USER_NOT_LOGIN') ,'', 0, '', true);
		}

		// 2.数据库中UID对应的SESSID是否与传的匹配
		if(!D('Sess')->checkSessId($this->params)) {
			$this->_outputData(C('ERROR.USER_LOGIN_OTHER_WAY') ,'', 0, '', true);
		}
	}

	protected function getLoginBaseInfo() {
		return array(
					'uid'		=> $this->params['uid'],
					'sessid'	=> getMobileSessId(),
					'model'		=> $this->params['model'],
					'imei'		=> $this->params['imei'],
				);
	}

	protected function _getId() {
		$id = intval($_REQUEST['id']);
		empty($id) && $id = intval($_REQUEST[M(MODULE_NAME)->getPk()]);
		return $id;
	}

	protected function _checkAuth($p_id = 0) {
		$id = intval($p_id);
		empty($id) && $id = $this->_getId();
		return D(MODULE_NAME)->isOwner($id, getUid());
	}

	protected function _noLoginDeny() {
		if(!getUid()) {
			$message = '用户未登录!';
			if(IS_AJAX) {
				$data = array(
					'info'		=> $message,
					'status'	=> 0,
					'url'		=> '',
					'code'		=> C('ERROR.USER_NOT_LOGIN'),
				);
				$this->ajaxReturn($data);
			} else {
				$this->error($message);
			}
			die;
		}
	}

	protected function _notPostDisplay() {
		if(!IS_POST) {
			$this->display();
			die;
		}
	}

	protected function _assignRegion() {
		$regionLevel1 = D('Region')
							->where(array(
								'region_level' => 1,
							))
							->select();
		$this->assign('regionLevel1', $regionLevel1);
	}

	//判断是否有提交指定字段数据
	private function _isset($field, $method = 'REQUEST') {
		$request = '';
		switch($method) {
			case 'POST' :
				$request = &$_POST;
				break;
			case 'GET' :
				$request = &$_GET;
				break;
			case 'REQUEST' :
				;
			default:
				$request = &$_REQUEST;
		}

		$isset = false;
		// if(isset($request[$field]) && !empty($request[$field])) {
		if(isset($request[$field])) {
			$isset = true;
		}

		return $isset;
	}//end _isset()

}
?>
