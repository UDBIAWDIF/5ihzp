<?php
/**
 * 用户模型
 *
 * @author UID
 * @date 2014.07.23
 */
class UserModel extends BaseModel {

	public function register() {
		$runnable	= true;
		$regResult	= false;

		$regionId = intval($_POST['region_id']);
		if(empty($regionId)) {
			$runnable		= false;
			$this->error	= '请选择您所在的地区!';
		}

		if($runnable) {
			$password	= $_POST['password'];
			$repassword	= $_POST['repassword'];
			if($password != $repassword) {
				$runnable		= false;
				// $this->error	= L2('USER_REG.REPW_NOMATCH');
				$this->error	= '密码和重复密码不一致!';
			}
		}

		if($runnable && !checkPWStrength($password)) {
			$runnable		= false;
			// $this->error	= L2('USER_REG.WEAK');
			$this->error	= '密码强度不够!';
		}

		$username = '';
		if($runnable) {
			$username = trim(filter_str($_POST['username']));
			if(!checkUsername($username)) {
				$runnable		= false;
				// $this->error	= L2('USER_REG.USERNAME_UNRULE');
				$this->error	= '您输入的用户名不符合规则!';
			}
		}

		if($runnable) {
			if($this->_haveUser($username)) {
				$runnable		= false;
				$this->error	= '用户已存在!';
			}
		}

		$email = '';
		if($runnable) {
			$email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
			if(empty($email)) {
				$runnable		= false;
				// $this->error	= L2('USER_REG.EMAIL_UNRULE');
				$this->error	= '请正确输入邮箱!';
			}
		}

		if($runnable) {
			if($this->_haveEmail($email)) {
				$runnable		= false;
				$this->error	= '邮箱已存在!';
			}
		}

		$userheadId = 0;
		if($runnable) {
			$userhead = trim(filter_str($_POST['icon_path']));
			if(!empty($userhead) && is_file(UPLOAD_MAIN_DIR . $userhead)) {
				$userheadId = (int)D('Icon')->addUserHead($userhead);
			}
		}

		if($runnable) {
			$userInfo = array(
				'u_name'		=> $username,
				'u_password'	=> md5($password),
				'u_name_pinyin'	=> pinyin($username, 1),
				'u_icon_id'		=> $userheadId,
				'u_region_id'	=> $regionId,
				'u_email'		=> $email,
				'u_sign'		=> trim(filter_str($_POST['sign'])),	// TODO: 签名长度限制
				'u_createtime'	=> NOW_TIME,
				'u_reg_ip'		=> get_client_ip(1),
				'u_updatetime'	=> NOW_TIME,
				'u_group_limit'	=> C('PERSON_HAVE_GROUP_LIMIT'),
				'u_active'		=> 0,
				'u_status'		=> 0,
			);

			if(C('OPENING_REGISTER')) {
				$userInfo['u_active'] = 1;
				$userInfo['u_status'] = 1;
			}

			// TODO: 后期优化时使用自动验证与完成
			$userId = $this->add($userInfo);
			if(empty($userId)) {
				$runnable		= false;
				$this->error	= '因系统出现故障注册失败!';
			} else {
				D('Icon')->save(array(
					D('Icon')->getPk() => $userheadId,
					'icon_rel_id' => $userId,
				));
				$regResult = true;
				$this->_sendActiveMail($userId);
				session('email', $email);
				// $this->doLogin($username, $password);
			}
		}

		return $regResult;
	}

	public function modify() {
		$runnable		= true;
		$modifyResult	= false;
		$modifyInfo		= array(
			$this->getPk()	=> getUid(),
			'u_region_id'	=> intval($_POST['region_id']),
			'u_sign'		=> filter_str(trim($_POST['sign'])),
		);

		if($runnable) {
			$password		= $_POST['modifypassword'];
			$repassword		= $_POST['repassword'];
			$checkNoEmpty	= !empty($password) || !empty($repassword);
			if($checkNoEmpty) {
				if($password != $repassword) {
					$runnable		= false;
					$this->error	= '密码和重复密码不一致!';
				} else if(!checkPWStrength($password)) {
					$runnable		= false;
					$this->error	= '密码强度不够!';
				} else {
					$modifyInfo['u_password'] = md5($password);
				}
			}
		}

		if($runnable) {
			$handleEmail = true;
			$email = trim(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));

			if('' === trim($_POST['email'])) {
				$handleEmail	= false;
			}

			if($handleEmail && empty($email)) {
				$handleEmail	= false;
				$runnable		= false;
				$this->error	= '请正确输入邮箱!';
			}

			if($handleEmail && !$this->_ownEmail($email) && $this->_haveEmail($email)) {
				$handleEmail	= false;
				$runnable		= false;
				$this->error	= '邮箱已存在!';
			}

			if($handleEmail) {
				$modifyInfo['u_email'] = $email;
			}
		}

		$userheadId = 0;
		if($runnable) {
			$userhead = trim(filter_str($_POST['icon_path']));
			if(!empty($userhead) && is_file(UPLOAD_MAIN_DIR . $userhead)) {
				$userheadId = (int)D('Icon')->addUserHead($userhead);
				$modifyInfo['u_icon_id'] = $userheadId;
			}
		}

		if($runnable) {
			$modifyResult = $this->save($modifyInfo);
			if(false === $modifyResult) {
				$runnable		= false;
				$this->error	= '因系统出现故障注册失败!';
			} else {
				$modifyResult	= true;
				if(!empty($userheadId)) {
					D('Icon')->save(array(
						D('Icon')->getPk()	=> $userheadId,
						'icon_rel_id'		=> getUid(),
					));
				}
				$this->_saveUserToSession(getUid());
			}
		}

		return $modifyResult;
	}

	public function login() {
		$username	= trim(filter_str($_POST['username']));
		$password	= cookie('password');
		$isHash		= false;
		if($password === $_POST['password']) {
			$isHash		= true;
		} else {
			$password	= $_POST['password'];
		}

		return $this->doLogin($username, $password, $isHash);
	}

	//处理用户登录
	public function doLogin($username, $password, $isHash = false) {
		$runnable		= true;
		$loginResult	= false;
		if(empty($username)) {
			$runnable		= false;
			$this->error	= '用户名不能为空!';
		} elseif(empty($password)) {
			$runnable		= false;
			$this->error	= '密码不能为空!';
		}

		if($runnable) {
			$passwordHash = $isHash ? $password : md5($password);
			$map = array(
				'u_status'	=> 1,
				'u_name'	=> $username,
			);
			$user = $this->where($map)->find();
			if(empty($user)) {
				$runnable		= false;
				$this->error	= '用户不存在!';
			} elseif($user['u_password'] != $passwordHash) {
				$runnable		= false;
				$this->error	= '密码错误!';
			} elseif(empty($user['u_active'])) {
				$runnable		= false;
				$this->error	= '用户未激活!';
			}
		}

		if($runnable) {
			$this->_saveLogin($user);
			$this->_rememberLogin($username, $passwordHash);
			$loginResult = true;
		}

		return $loginResult;
	}

	public function logout() {
		session('user_info', null);
		return true;
	}

	public function activeUserById($p_userId) {
		$user = array(
			$this->getPk() => intval($p_userId),
			'u_active' => 1,
			'u_status' => 1,
		);
		return $this->save($user);
	}

	public function incOrDecData($p_uid, $p_field, $p_inc = true) {
		$uid		= intval($p_uid);
		$field		= trim($p_field);
		$setFunc	= $p_inc ? 'setInc' : 'setDec';
		empty($uid) && $uid = getUid();
		return $this
				->where(array(
					$this->getPk() => $uid,
				))
				->$setFunc($field);
	}

	public function checkGroupLimit($p_uid = 0) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();
		$groupLimit = $this
						->where(array($this->getPk() => $uid))
						->field('u_group_limit,u_group_count')
						->find();
		$checkResult = intval($groupLimit['u_group_count']) < intval($groupLimit['u_group_limit']);

		return $checkResult;
	}

	public function countGroup($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return D('Group')->countGroup($uid);
	}

	public function countJoinGroup($p_uid = 0) {
		$uid = intval($p_uid);
		$uid = empty($uid) ? getUid() : $uid;
		return D('Group')->countJoinGroup($uid);
	}

	public function queryFirends($p_uid = 0) {
		$uid = intval($p_uid);
		empty($uid) && $uid = getUid();
		$friendIds = D('LikeUser')
				->where(array(
					'lu_by_user_id'		=> $uid,
					'lu_like_user_id'	=> array('neq', $uid),
				))
				->getField('lu_like_user_id', true);

		return $friendIds;
	}

	public function getUname($p_uid) {
		$uid = intval($p_uid);
		if(empty($uid)) {
			return '';
		} else {
			return $this
					->where(array($this->getPk() => $uid))
					->getField('u_name');
		}
	}

	private function _top($p_field, $p_count) {
		$field	= trim($p_field);
		$count	= intval($p_count);
		return $this
				->where(array('u_status' => 1))
				->order($field . ' DESC')
				->getField($this->getPk() . ',' . $field);
	}

	/* 发送激活用户邮件 */
	private function _sendActiveMail($p_userId) {
		$runnable	= true;
		$sendResult	= false;
		$userId		= intval($p_userId);

		$userinfo	= $this->find($userId);
		if(empty($userinfo)) {
			$runnable = false;
		}

		$activeKey	= null;
		$email		= '';
		$expireMinutes = C('MAIL.ACTIVE_MAIL_EXPIRE');
		if($runnable) {
			$email = $userinfo['u_email'];
			$activeKey = array();
			$keys = md5(uniqid($email . mt_rand(), true));
			$activeKey['uak_user_id']	= $userId;
			$activeKey['uak_expire']	= NOW_TIME + $expireMinutes * 60;
			$activeKey['uak_state']		= 0;
			$activeKey['uak_keys']		= $keys;
			$runnable = (bool) M('UserActiveKey')->add($activeKey);
		}

		if($runnable) {
			$href	= U('User/active_user@' . $_SERVER['HTTP_HOST'], array('code' => $activeKey['uak_keys']));
			$hView	= Think::instance('View');
			$hView->assign('username',		$username);
			$hView->assign('email',			$email);
			$hView->assign('href',			$href);
			$hView->assign('expireMinutes',	$expireMinutes);
			$mailBody = $hView->fetch('user_active_mail');
			$hMail = getMailHandle();
			$hMail->addAddress($email);
			$hMail->Subject	= C('MAIL.USER_FINDPW_SUBJECT');
			$hMail->Body	= $mailBody;

			$sendResult = (bool) $hMail->send();
		}

		return $sendResult;
	}

	private function _haveUser($username) {
		// return $this->getFieldByUName($username, $ths->getPk());
		return $this->_infoIsUsed($username, 'u_name');
	}

	private function _haveEmail($email) {
		// return $this->getFieldByUEmail($email, $ths->getPk());
		return $this->_infoIsUsed($email, 'u_email');
	}

	private function _ownEmail($email) {
		return (bool) $this
						->where(array(
							$this->getPk() => getUid(),
							'u_email' => $email,
						))
						->field($this->getPk())
						->find();
	}

	private function _saveLogin($user) {
		$data = array(
			'u_id'					=> $user['u_id'],
			'u_last_logintime'		=> NOW_TIME,
			'u_last_loginip'		=> get_client_ip(1),
		);
		$this->save($data);
		$this->_saveUserToSession($user);
	}

	private function _saveUserToSession($user) {
		if(!is_array($user)) {
			if(is_numeric($user)) {
				$user = $this->find(intval($user));
			} else {
				$user = $this->where(array('u_name' => $user))->find();
			}
		}
		session('user_info', $user);
	}

	private function _infoIsUsed($p_value, $p_field) {
		$usedInfo	= $this
						->where(array($p_field => $p_value))
						->getField($this->getPk());

		return !empty($usedInfo);
	}

	private function _rememberLogin($username, $passwordHash) {
		if(!empty($_POST['chksavepwd'])) {
			cookie('username',		$username);
			cookie('password',		$passwordHash);
			cookie('chksavepwd',	1);
		} else {
			cookie('username',		null);
			cookie('password',		null);
			cookie('chksavepwd',	0);
		}
	}

}
