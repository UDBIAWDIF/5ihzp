<?php
/**
 * 会话模型
 */
class SessModel extends BaseModel {

	//登录后保存本次登录的用户信息
	public function saveLogin($params) {
		$map = array('cu_id' => $params['uid']);
		$haveSess = $this->where($map)->field('cu_id')->find();
		$map['sess_id']			= $params['sessid'];
		$map['mobile_model']	= $params['model'];
		$map['mobile_imei']		= $params['imei'];
		$map['action_time']		= $_SERVER['REQUEST_TIME'];
		$saveResult = false;
		if(empty($haveSess)) {
			$map['ctime'] = $_SERVER['REQUEST_TIME'];
			$saveResult = $this->add($map);
		} else {
			$saveResult = $this->save($map);
		}
// echo $this->_sql();
		return $saveResult;
	}

	//退出后清除相关数据
	public function clearLogout($params) {
		$map = array('cu_id' => $params['uid']);
		return $this->where($map)->setField('sess_id', '');
	}

	//检查用户ID和会话ID是否匹配
	public function checkSessId($params) {
		$map = array(
					'cu_id'		=> $params['uid'],
					'sess_id'	=> $params['sessid']
				);
		$sess = $this->where($map)->find();

		//todo 优化三元运算
		return empty($sess) ? false : true;
	}

}
