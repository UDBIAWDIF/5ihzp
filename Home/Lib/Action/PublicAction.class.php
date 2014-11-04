<?php

/**
 * 公共模块
 *
 * @author UID
 */
class PublicAction extends BaseAction{

	public function _initialize() {
		parent::_initialize();
	}

	public function sign() {
		$code = C('ERROR.SUCCESS');
		$sessInfo = '';
		$mCustomer = D('Customer');
		$Customer = $mCustomer->create($this->params);
		if(empty($Customer)) {
			$code = $mCustomer->getError();
		} else {
			$mCustomer->add();
		}

		$this->_outputData($code);
	}//end sign()

	public function login() {
		session_regenerate_id();
		// 1. 判断是否输入用户名、密码
		$username = isset($this->params['username']) ? trim($this->params['username']) : '';
		// $is_name = checkUsername($username);
		// if(empty($username) || !$is_name) {
		if(empty($username)) {
			$this->_outputData(C('ERROR.USERNAME_INPUT_FAULT_IN_LOGIN'), '', 0, '', true);
		}
		$password = isset($this->params['password']) ? md5($this->params['password']) : '';
		if(empty($password)) {
			$this->_outputData(C('ERROR.PASSWORD_INPUT_FAULT_IN_LOGIN'), '', 0, '', true);
		}

		// 2. 判断用户是否存在、密码是否正确
		$mCustomer = D('Customer');
		$customer = $mCustomer->where(array('name' => $username))->find();
		if(false === $customer){
			$this->_outputData(C('ERROR.USER_NOT_EXIST'), '', 0, '', true);
		} elseif($customer['password'] != $password) {
			$this->_outputData(C('ERROR.PASSWORD_INPUT_FAULT_IN_LOGIN'), '', 0, '', true);
		}
		unset($customer['password']);

		// 3. 添加或更新 sess 表里的用户sessid、登录时间等
		$this->params['uid'] = intval($customer[$mCustomer->getPk()]);
		$loginInfo = $this->getLoginBaseInfo();
		$saveResult = D('Sess')->saveLogin($loginInfo);
		if(false === $saveResult) {
			$this->_outputData(C('ERROR.UNKOWN_ERROR'), '', 0, '', true);
		}

		// 4 生成当前用户的消息等数据
		$mCustomer->genMsg($this->params);

		// 5. 返回用户id、sessid
		$customer['head'] = getFileUrlOnSite($customer['head']);
		$loginInfo = array_merge($loginInfo, $customer);
		$this->_outputData(C('ERROR.SUCCESS'), $loginInfo);
	}//end login()

	public function logout() {
		//先检查用户ID和会话ID是否匹配
		//再删除 sess 表里用户数据(清除sessid就好)
		if(empty($this->params['uid']) || empty($this->params['sessid'])) {
			$this->_outputData(C('ERROR.USER_NOT_LOGIN'), '', 0, '', true);
		}

		$mSess = D('Sess');
		$sessMatch = $mSess->checkSessId($this->params);
		if(false == $sessMatch) {
			$this->_outputData(C('ERROR.USER_LOGIN_OTHER_WAY'), '', 0, '', true);
		}

		$clearResult = $mSess->clearLogout($this->params);
		if(empty($clearResult)) {
			$this->_outputData(C('ERROR.UNKOWN_ERROR'), '', 0, '', true);
		}

		$this->_outputData(C('ERROR.SUCCESS'));
	}//end logout()

}
?>
