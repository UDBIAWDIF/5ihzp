<?php

class MemberModel extends Model{

	/* 用户模型自动完成 */
	protected $_auto = array(
		array('login', 0, self::MODEL_INSERT),
		// array('email', 'trim', self::MODEL_INSERT, 'function'),
		array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('reg_time_str', 'datetime', self::MODEL_INSERT, 'function'),
		array('last_login_ip', 'get_client_ip', self::MODEL_BOTH, 'function', 1),
		array('last_login_time', NOW_TIME, self::MODEL_BOTH),
		array('last_login_time_str', 'datetime', self::MODEL_BOTH, 'function'),
		array('update_time', NOW_TIME),
		array('status', 1, self::MODEL_INSERT),
	);

	/**
	 * 登录指定用户
	 * @param	integer	$uid 用户ID
	 * @return	boolean	true-登录成功，false-登录失败
	 */
	public function login($uid) {
		$this->autoLogin($user);
		return true;
	}

	/**
	 * 注销当前用户
	 * @return void
	 */
	public function logout(){
		session('user_auth', null);
		session('user_auth_sign', null);
	}

	/**
	 * 手机号是否被其它人占用
	 * @param	string	$p_phone 手机号
	 * @return	boolean	true-占用, false-未占用
	 */
	public function phoneIsUsedByOther($p_phone_area, $p_phone) {
		$phone_area	= trim($p_phone_area);
		$phone		= trim($p_phone);
		$isUsed		= false;
		$userId		= $this
						->where(array(
							'phone_area'	=> $phone_area,
							'phone'			=> $phone,
						))
						->getField($this->getPk());
		if(!empty($userId)) {
			if(getUid() != intval($userId)) {
				$isUsed = true;
			}
		}

		return $isUsed;
	}

	/**
	 * 手机号是否被占用
	 * @param	string	$p_phone 手机号
	 * @return	boolean	true-占用, false-未占用
	 */
	public function phoneIsUsed($p_phone) {
		return $this->_infoIsUsed($p_phone, 'phone');
	}

	/**
	 * 邮箱是否被占用
	 * @param	string	$p_email
	 * @return	boolean	true-占用, false-未占用
	 */
	public function emailIsUsed($p_email) {
		return $this->_infoIsUsed($p_email, 'email');
	}

	/* 修改用户信息(本系统数据库) */
	public function change_info($p_newInfo) {
		$newInfo = $p_newInfo;
		$newInfo[$this->getPk()] = getUid();
		$result = $this->save($newInfo);
		$result = false !== $result;

		if($result) {
			$newInfo = array_merge(session('user_info'), $newInfo);
			session('user_info', $newInfo);
		}

		return $result;
	}

	public function getAuthor($uid) {
		return $this->getFieldById(intval($uid), 'nickname');
	}

	/**
	 * 自动登录用户
	 * @param  integer $user 用户信息数组
	 */
	private function autoLogin($user){
		/* 更新登录信息 */
		$data = array(
			'uid'					=> $user['uid'],
			'login'					=> array('exp', '`login`+1'),
			'last_login_time'		=> NOW_TIME,
			'last_login_time_str'	=> datetime('', NOW_TIME),
			'last_login_ip'			=> get_client_ip(1),
		);
		$this->save($data);

		/* 记录登录SESSION和COOKIES */
		$auth = array(
			'uid'				=> $user['uid'],
			'username'			=> get_username($user['uid']),
			'last_login_time'	=> $user['last_login_time'],
		);

		$info = M('Member')->find($user['uid']);

		session('user_info', $info);
		session('user_auth', $auth);
		session('user_auth_sign', data_auth_sign($auth));
	}

	private function _infoIsUsed($p_value, $p_field) {
		$value		= trim($p_value);
		$usedInfo	= $this
						->where(array($p_field => $value))
						->getField($this->getPk());

		return !empty($usedInfo);
	}

}
