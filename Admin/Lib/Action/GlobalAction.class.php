<?php

/**
 * 全局类，具体的功能模块都继承于此类
 * 方便做初始化和权限验证
 * 包含公共操作
 *
 * @author Aoki
 */
class GlobalAction extends Action {

	private $gid;
	private $uid;
	private $userName;
	private $limit;
	private $likeFields;
	private $complex;

	function _initialize() {
		import('ORG.Util.Session');
		$this->gid = getCurrentGroupId();
		$this->uid = getCurrentUserId();
		$this->userName = Session::get(APP_SESSION_FLAG.'username');
		C('LOGINUSERNAME', $this->userName);

		if($this->uid == false || $this->gid == false) {
			if(MODULE_NAME == C('DEFAULT_MODULE') && ACTION_NAME == C('DEFAULT_ACTION')) {
				redirect(U('Public/login'));
			} else if(MODULE_NAME == 'Index' && ACTION_NAME == 'login') {
				$this->display('Public:login');
			} else {
				$this->assign('jumpUrl',U('Public/login'));
				$this->display('Public:logout');
			}
			exit;
		}

		$this->_checkSecurity();

		$this->assign('thisSoftServerUrl', C('SOFT_HOST') . C('FTP_SOFT_PATH') . '/');
		$this->assign('thisImgServerUrl', C('IMAGE_HOST') . C('FTP_IMG_PATH') . '/');
		$this->assign('uid',$this->getUid());
	}

	public function isSupervisor() {
		$supervisor_gid = C('SUPERVISOR_GID');
		if(empty($supervisor_gid)) {
			$supervisor_gid = array(1);	//如果没有配置超级管理员组,就默认1组为超级管理员
		}
		$result = in_array($this->getGid(), $supervisor_gid)===false?false:true;
		return $result;
	}//end isSupervisor()

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

	/**
	 * 获取使用like查询的字段
	 * @access protected
	 * @return int
	 */
	Protected function getUid() {
		return $this->uid;
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
	 * 获取用户组ID
	 * @access protected
	 * @return int
	 */
	Protected function getGid() {
		return $this->gid;
	}

	/**
	 * 获取用户名
	 * @access protected
	 * @return string
	 */
	Protected function getName() {
		return $this->userName;
	}

	/**
	 * 检查用户组权限
	 * @param string $action 要操作的模块
	 * @access protected
	 * @return void
	 */
	protected function _checkSecurity() {
		if (!$this->uid) {
			$this->error('非法用户');
		}

		return;

		import('@.ORG.Security');
		//检查用户权限
		$module = Security::getModuleAccess(MODULE_NAME, ACTION_NAME, $this->gid);
		if (!$module) {
			//非法权限跳转页面
			$this->assign('waitSecond', 10);
			$this->error('当前位置: ' . MODULE_NAME . ' 下的 ' . ACTION_NAME .' 无访问权限!');
		}
	}

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
// dump($param);dump($_REQUEST);die;
				$_REQUEST = array_merge($_REQUEST, $param);
			}
		}

		$param = array();
		$likeFields = $this->getLikeFields();
		foreach($model->getDbFields() as $val) {
			$currentRequest = '';
			if(isset($_REQUEST[$val])) {
				$currentRequest = trim($_REQUEST[$val]);
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
//dump($_REQUEST);
		import("@.ORG.Util.Cookie");
		//排序字段 默认为主键名
		if (isset($_REQUEST['_order'])) {
			$order = $_REQUEST['_order'];
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
		if (isset($_REQUEST ['_sort'])) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where($map)->count('*');
// echo $model->_sql(),'<br />';die;
		if ($count > 0) {
			import("@.ORG.Util.PageWithInput");
			//创建分页对象
			$listRows = intval($_COOKIE['listRows']);
//dump($_COOKIE);
//echo 'Set $_COOKIE[listRows] ...',$listRows,'<br />';
			if(empty($listRows)) {
				$listRows = C('INDEX_ROWS');
//echo 'Set C(INDEX_ROWS) ...',$listRows,'<br />';
			}
			if(isset($_REQUEST['listRows'])) {
				$_REQUEST['listRows'] = intval($_REQUEST['listRows']);
				if (!empty($_REQUEST['listRows'])) {
					$listRows = $_REQUEST['listRows'];
//echo 'Set $_REQUEST[listRows] ...',$listRows,'<br />';
					//setcookie('listRows', $listRows, $_SERVER['REQUEST_TIME']+60*60*24*30);
					Cookie::set('listRows', $listRows);

					$_COOKIE['listRows'] = $listRows;
				} else {
//echo 'Clean cookie ...';
					//setcookie('listRows', '', $_SERVER['REQUEST_TIME'] - 1);
					Cookie::delete('listRows');
					$_COOKIE['listRows'] = 0;
				}
			}
			if(empty($listRows)) {
//echo 'Set default 10 ...',$listRows,'<br />';
				$listRows = '10';
			}
			$p = new PageWithInput($count, $listRows);
			//分页查询数据
			$voList = $model->where($map)->order($order . " " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
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
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign('count', $count);
			$this->assign('list', $voList);
			$this->assign('sort', $sort);
			$this->assign('order', $order);
			// $this->assign('param', base64_encode(serialize($map)));
			$this->assign('sortImg', $sortImg);
			$this->assign('sortType', $sortAlt);
			$this->assign('page', $page);
		}
//		import("@.ORG.Util.Cookie");
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
	 * 默认的添加页面
	 * TODO index \ add \ edit 放置前
	 * @return void
	 */
	public function add($tpl = null) {
		$this->display($tpl);
	}

	/**
	 * 插入记录
	 * @access protected
	 * @param Model $model 数据对象
	 * @return void
	 */
	public function doAdd($model = MODULE_NAME) {
		//检测模型
		$model = $this->_checkModel($model);

		$vo = $model->create();

		if (false === $vo) {
			$this->error($model->getError());
		}

		if (!empty($_FILES)) {
			if (!empty($_FILES['attachment']['name'][0])) {
				$files = $this->_upload();
				$vo['attachment'] = $files[0]['savename'];
			}
		}

		//保存当前Vo对象
		$id = $model->add($vo);
		//保存成功
		if ($id) {
			//数据保存触发器
			if (method_exists($this, ACTION_NAME . '_trigger')) {
				$action = ACTION_NAME . '_trigger';
				$this->$action($vo, $id);
			}

			$this->assign('jumpUrl', __URL__);
			$this->success("操作成功");
		} else {
			$this->error('数据提交失败！');
		}
	}

	/**
	 * 默认的编辑页面
	 *
	 * @return void
	 */
	public function edit($model = null, $tpl = null) {
		$id = $this->_get('id');

		$model = $this->_checkModel($model);

		$map = array(
			$model->getPk() => $id
		);

		$vo = $model->where($map)->find();
		$vo = $model->parseFieldsMap($vo);	//解析字段映射 By UID
		$this->assign('vo', $vo);
		$this->display(trim($tpl));
	}

	/**
	 * 默认的查看页面
	 *
	 * @return void
	 */
	public function detail() {
		$id = $this->_get('id');

		$name = $this->getActionName();
		$model = D($name);

		$map = array(
			$model->getPk() => $id
		);

		$vo = $model->where($map)->find();

		$this->assign('vo', $vo);
		$this->display();
	}

	/**
	 * 更新一个数据对象
	 * @access protected
	 * @param Model $model 数据对象
	 * @return void
	 */
	public function doEdit($model = null, $return_url = null) {
		$model = $this->_checkModel($model);
		$vo = $model->create('', Model::MODEL_UPDATE);
		if(!$vo) {
			$this->error($model->getError());
		}

		//添加关联模型自动create  by Aoki 2012年10月19日
		if($model instanceof RelationModel) {
			foreach ($model->_link as $className => $relation) {
				if(isset($relation['class_name'])){
					$relationModel = D($relation['class_name']);
				}else{
					$relationModel = D($className);
				}

				$relationVo = $relationModel->create();
				if($relationVo){
					$vo[$className]=$relationVo;
				}
			}
		}

		if(!empty($_FILES)) {
			if (!empty($_FILES['attachment']['name'][0])) {
				$files = $this->_upload();
				$vo['attachment'] = $files[0]['savename'];
			}
		}
		$id = is_array($vo) ? $vo[$model->getPk()] : $vo->{$model->getPk()};

		//添加关联模型自动关联
		if($model instanceof RelationModel) {
			$result = $model->relation(true)->save($vo);
		}else{
			$result = $model->save($vo);
		}

		$result = $model->save($vo);
		if(false !== $result) {
			//数据保存触发器
			if(method_exists($this, ACTION_NAME . '_trigger')) {
				$action = ACTION_NAME . '_trigger';
				$this->$action($vo, $id);
			}
			$return_url = empty($return_url) ? __URL__ : trim($return_url);
			$this->assign('jumpUrl', $return_url);
			$this->success('更新成功！');
		} else {
			$this->error($model->getError());
		}
	}

	/**
	 * 执行批量操作
	 * 删除/锁定/推荐/置顶/审核/移动
	 * @param string $model 要操作的模型
	 * @access protected
	 * @return void
	 */
	public function exec($model = null) {
		//检测模型
		$model = $this->_checkModel($model);
		$model_up_name = strtoupper($model->getModelName());
		$ids = $_REQUEST['id'];
		$act = trim($_REQUEST['act']);
		//处理参数
		if (is_array($ids)) {
			$id = implode(',', $ids);
		} else if (is_numeric($ids)) {
			$id = $ids;
		} else {
			$this->error('数据错误！');
		}
		//检测操作类型
		if (!$act) {
			$this->error('操作类型必须选择');
		}
		switch ($act) {
			case 'lock': {
					//先获取配置里有无当前模块的批量操作配置,有就用配置的值,没有就用默认的状态值
					$currentStatusCfg = C($model_up_name . '_STATUS');
					$statusToSet = 0;
					$statusField = 'status';
					if(!empty($currentStatusCfg)) {
						if(isset($currentStatusCfg[$model_up_name . '_BATCH_CANCEL_STATUS'])) {
							$statusToSet = intval($currentStatusCfg[$model_up_name . '_BATCH_CANCEL_STATUS']);
						}
						if(isset($currentStatusCfg[$model_up_name . '_STATUS_FIELD'])) {
							$statusField = trim($currentStatusCfg[$model_up_name . '_STATUS_FIELD']);
						}
					}
					$result = $model->where(' ' . $model->getPk() . " IN({$id})")->setField($statusField, $statusToSet);
					$actName = '锁定';
					break;
				}
			case 'unlock': {
					//先获取配置里有无当前模块的批量操作配置,有就用配置的值,没有就用默认的状态值
					$currentStatusCfg = C($model_up_name . '_STATUS');
					$statusToSet = 1;
					$statusField = 'status';
					if(!empty($currentStatusCfg)) {
						if(isset($currentStatusCfg[$model_up_name . '_BATCH_RECOVER_STATUS'])) {
							$statusToSet = intval($currentStatusCfg[$model_up_name . '_BATCH_RECOVER_STATUS']);
						}
						if(isset($currentStatusCfg[$model_up_name . '_STATUS_FIELD'])) {
							$statusField = trim($currentStatusCfg[$model_up_name . '_STATUS_FIELD']);
						}
					}
					$result = $model->where(' ' . $model->getPk() . " IN({$id})")->setField($statusField, $statusToSet);
					$actName = '解锁';
					break;
				}
			case 'delete': {
					$result = $model->where(' ' . $model->getPk() . " IN({$id})")->delete();
					$actName = '删除';
					break;
				}
			case 'toggle': {
					$result = $model->where(' ' . $model->getPk() . " IN({$id})")->setField($_GET['field'], $_GET['value']);
					$actName = '开关';
					break;
				}
			default:
				if(method_exists($this, $act)) {
					$this->$act();
					die;
				} else {
					$this->error('未知操作类型');
				}
				break;
		}
		if ($result) {
			$this->success('操作成功！');
		} else {
			$this->error('操作失败！');
		}
	}//end exec()

	// 小图上传到FTP
	protected function upSmallPicToFtp($up_result) {
		if(!$up_result['error']) {
			$picFile = BASE_PATH . $up_result['url'];
			if(file_exists($picFile)) {
				$file_part = getFilenamePart($picFile);
				$smallPic = $file_part['dirname'] . '/s_' .$file_part['filename'] . '.' . $file_part['extname'];
				$smallPic = substr($smallPic, strlen(BASE_PATH));
				if(file_exists($smallPic)) {
					D('File')->uploadFtpFile($smallPic, 'IMG_FTP');
				}
			}
		}
	}

	//切换：是否打水印
	public function upImgToggleMergeWater() {
		$mergeWaterField = C('MERGE_WATER_REQUEST_FIELD');
		$editTokenKey = $this->getEditTokenKey($_REQUEST['editToken']);
		if(isset($_REQUEST[$mergeWaterField]) && !empty($_REQUEST[$mergeWaterField])) {
			$editToken = trim($_REQUEST['editToken']);
			S($editTokenKey, true, 7200);
echo "set Tokey:{$_REQUEST['editToken']}, key:{$editTokenKey}, set to ";
dump(S($editTokenKey));
		} else {
			S($editTokenKey, false, 7200);
echo "set Tokey:{$_REQUEST['editToken']}, key:{$editTokenKey}, set to ";
dump(S($editTokenKey));
		}

		return;
	}//end upImgToggleMergeWater()

	//输出编辑令牌到编辑页面,每个页面都会有一个唯一编辑标志
	protected function assignEditToken() {
		$randStr = session_id() . mt_rand() . '_' . mt_rand() . '_' . mt_rand();
		$editToken = md5($randStr);
		$this->assign('editToken', $editToken);
	}

	//根据编辑标志生成缓存Key,这么做其实没必要,
	//只是为了防止直接使用页面传的值造成漏洞,所以还是多做一步安全一点
	protected function getEditTokenKey($editToken) {
		return md5(session_id() . trim($editToken));
	}

	protected function _upImg($img_type, $checksize = false, $up_to_ftp = false, $trigger = null) {
		$m_file = D('File');
		$img_path = $m_file->upload_img($img_type, $checksize, $up_to_ftp, $trigger);
// $img_path['waterkey'] = $this->getEditTokenKey($_REQUEST['editToken']);
// $img_path['water'] = S($this->getEditTokenKey($_REQUEST['editToken']));
		if(empty($img_path['error']) && isset($_REQUEST['editToken']) && S($this->getEditTokenKey($_REQUEST['editToken']))) {
			$waterFile = TMPL_PATH . C('WATER_IMAGE');
			if(file_exists($waterFile)) {
				/* import('@.ORG.Image');
				if(false !== Image::water(BASE_PATH . $img_path['url'], $waterFile, '')) {
					$m_file->uploadFtpFile($img_path['url'], 'IMG_FTP');
				} */
				import('@.ORG.WaterMark');
				if(false !== WaterMark::waterMarkGD(BASE_PATH . $img_path['url'], 9, $waterFile, '')) {
					$m_file->uploadFtpFile($img_path['url'], 'IMG_FTP');
				}
			}
		}

		return $img_path;
	}//end _upImg()

	protected function _upFile($file_type, $up_to_ftp = false) {
		$m_file = D('File');
		return $m_file->upload_file($file_type, $up_to_ftp);
	}//end _upFile()

	protected function _json_response($data) {
		$this->_json_response_merge_data($data);
		$this->_json_response_flush();
	}//end _json_response()

	protected function jrmd($data) {
		$this->_json_response_merge_data($data);
	}

	protected function _json_response_merge_data($data) {
		if(!is_array($this->jsonData)) {
			$this->jsonData = array();
		}
		if(is_array($data)) {
			$this->jsonData = array_merge($this->jsonData, $data);
		}
	}//end _json_response_merge_data()

	protected function _setOrder($orderField = 'order', $modelName) {
		$id = intval($_REQUEST['id']);
		if(!$id) $this->error(L('_SELECT_NOT_EXIST_'));

		$set_result = array(
			'status' => 1,
			'message' => '',
		);

		$order_num = intval($_REQUEST['order']);

		if(empty($modelName)) {$modelName = MODULE_NAME;}
		$model = M($modelName);
		$save_result = $model->save(array(
				$model->getPk()		=> $id,
				$orderField			=> $order_num,
		));

		if(false === $save_result) {
			$set_result['status'] = 0;
			$set_result['message'] = '排序更新失败！';
		}

		echo json_encode($set_result);
		return;
	}

	private function _json_response_flush() {
		header('Content-type: text/html; charset=UTF-8');
		echo(json_encode($this->jsonData));
	}//end _json_response_flush()

}

?>