<?php

/**
 * 用户模块
 *
 * @author UID
 * @date 2014.07.19
 */
class UserAction extends BaseAction{

	public function index() {
		$this->display();
	}

	public function reglogin() {
		$this->_assignRegion();
		$this->display();
	}

	/* 注册页面 */
	public function register() {
		$this->_notPostDisplay();

		$mUser		= D('User');
		$regResult	= $mUser->register();
		if($regResult) {
			$this->assign('jumpUrl', U('goto_active'));
			$this->success('注册成功！');
		} else {
			$this->error($mUser->getError());
		}
	}

	/* 修改资料 */
	public function modify() {
		$this->_noLoginDeny();

		$this->_assignRegion();
		$this->assign('user', session('user_info'));
		$this->_notPostDisplay();

		$mUser		= D('User');
		$regResult	= $mUser->modify();
		if($regResult) {
			$this->success('用户资料修改成功！');
		} else {
			$this->error($mUser->getError());
		}
	}

	public function login() {
		$this->_notPostDisplay();

		$mUser			= D('User');
		$loginResult	= $mUser->login();
		if($loginResult) {
			$this->assign('jumpUrl', U('Index/index'));
			$this->success('登录成功！');
		} else {
			$this->error($mUser->getError());
		}
	}

	public function logout() {
		D('User')->logout();
		$this->assign('jumpUrl', U('Index/index'));
		$this->success('退出成功！');
	}

	/* 前往邮箱激活页面 */
	public function goto_active() {
		$email		= session('email');
		$email_site	= emailsp_get_login_url($email);
		$this->assign('email',		$email);
		$this->assign('email_site',	$email_site);
		$this->display('regsuccess');
	}

	public function active_user() {
		$runnable	= true;
		$errMsg		= '';
		$mUserActiveKey = M('UserActiveKey');

		$activeKey = null;
		if($runnable) {
			$keys		= $this->_request('code');
			$activeKey	= $mUserActiveKey
							->where(array('uak_keys' => $keys, 'uak_state' => 0))
							->find();
			if(empty($activeKey)) {
				$runnable	= false;
				// $errMsg		= L2('USER_FINDPW.KEY_INVALID');
				$errMsg		= '代码不正确!';
			}
		}

		if($runnable) {
			if($activeKey['uak_expire'] < NOW_TIME) {
				$runnable	= false;
				// $errMsg		= L2('USER_FINDPW.KEY_EXPIRE');
				$errMsg		= '代码已失效!';
			}
		}

		$mUser		= D('User');
		$userinfo	= null;
		if($runnable) {
			$userinfo = $mUser->find($activeKey['uak_user_id']);
			if(!is_array($userinfo)) {
				$runnable	= false;
				// $errMsg		= L2('USER_FINDPW.USER_GONE');
				$errMsg		= '用户不存在!';
			}
		}

		if($runnable) {
			$activeKey['uak_state'] = 1;
			$mUserActiveKey->save($activeKey);
			$mUser->activeUserById($userinfo[$mUser->getPk()]);
		}

		if($runnable) {
			$mUser->doLogin($userinfo['u_name'], $userinfo['u_password'], true);
			$this->redirect('Index/index');
		} else {
			// $this->assign('msg', $errMsg);
			$this->error($errMsg);
		}
	}

	public function setHead() {
		// $this->_noLoginDeny();
		$up_result	= upImg(C('IMG_UP_TYPE.USER_HEAD'));
		$this->_json_response($up_result);
	}

	public function addpicture() {
		$this->_noLoginDeny();
		$this->assign('pictaglist', D('PicTag')->queryList());
		$this->_notPostDisplay();
		$up_result = upImg(C('IMG_UP_TYPE.PICTURE'));
		$this->_json_response($up_result);
	}

	public function addgroup() {
		$this->_noLoginDeny();
		$this->_checkGroupLimitAndDeny();
		$this->_assignRegion();
		$this->assign('gptaglist', D('GroupTag')->queryList());
		$this->display();
	}

	public function joingroup() {
		$this->_noLoginDeny();
		$gpId	= intval($_POST['g_id']);
		$reason	= filter_str(trim($_POST['reason']));
		$joinResult = D('Group')->userAskForJoin($gpId, getUid(), $reason);

		if($joinResult) {
			$this->success('申请发送成功！');
		} else {
			$this->error('申请发送失败! 原因: ' . D('Group')->getError());
		}
	}

	public function commentpic() {
		$this->_noLoginDeny();
		$picId			= intval($_REQUEST['pic']);
		$this->_commentDelay($picId);
		$commentResult	= false;
		$mComment		= D('PicComment');
		if($mComment->create()) {
			$commentResult = $mComment->add();
		}

		if($commentResult) {
			D('Pic')->incOrDecData($picId, 'pic_comment_count');
			$comment = D('PicCommentView')->find($commentResult);
			$this->assign('commentlist', array($comment));
			$this->success($this->fetch('Pic:comment'));
		} else {
			$this->error('评论失败! 原因: ' . $mComment->getError());
		}
	}

	public function thumbuppic() {
		$this->_noLoginDeny();
		$picId			= intval($_REQUEST['pic']);
		$mPic			= D('Pic');
		$thumbResult	= $mPic->thumbup($picId);
		if($thumbResult) {
			if(IS_AJAX) {
				$this->ajaxReturn($thumbResult, '', 1);
			} else {
				if($thumbResult > 0) {
					$this->success('点赞成功!');
				} else {
					$this->success('取消赞成功!');
				}
			}
		} else {
			$this->error('点赞失败! 原因: ' . $mPic->getError());
		}
	}

	public function like() {
		$this->_noLoginDeny();
		$uid		= intval($_REQUEST['uid']);
		$mLikeUser	= D('LikeUser');
		$likeResult	= $mLikeUser->like($uid);
		if($likeResult['status']) {
			$addOrDel = $likeResult['addordel'] ? 'add' : 'del';
			$this->success($addOrDel);
		} else {
			$this->error('关注失败! 原因: ' . $mPic->getError());
		}
	}

	public function regsuccess() {
		$this->_notPostDisplay();
	}

	public function piclist() {
		$uid = intval($_REQUEST['uid']);
		$this->setLimit(array('u_id' => $uid));
		$this->assign('user', getUser($uid));
		parent::index(D('PicView'));
	}

	public function sysmsg() {
		$this->_notPostDisplay();
	}

	protected function _checkGroupLimitAndDeny() {
		if(!D('User')->checkGroupLimit()) {
			die('您创建的群已达上限!');
		}
	}

	/* 发送激活用户邮件 */
	private function _sendActiveMail($p_userId) {
		$runnable	= true;
		$sendResult	= false;
		$uid		= intval($p_userId);

		$userinfo	= M('User')->find($uid);
		if(empty($userinfo)) {
			$runnable = false;
		}

		$activeKey	= null;
		$email		= '';
		$expireMinutes = C('MAIL.ACTIVE_MAIL_EXPIRE');
		if($runnable) {
			$email = $userinfo[2];
			$activeKey = array();
			$keys = md5(uniqid($email . mt_rand(), true));
			$activeKey['uak_user_id']	= $userinfo['u_name'];
			$activeKey['uak_expire']	= NOW_TIME + $expireMinutes * 60;
			$activeKey['uak_state']		= 0;
			$activeKey['uak_keys']		= $keys;
			$runnable = (bool) M('UserActiveKey')->add($activeKey);
		}

		if($runnable) {
			$href = U('User/active_user@' . $_SERVER['HTTP_HOST'], array('code' => $activeKey['keys']));
			$this->assign('username',		$username);
			$this->assign('email',			$email);
			$this->assign('href',			$href);
			$this->assign('expireMinutes',	$expireMinutes);
			$mailBody = $this->fetch('user_active_mail');
			$hMail = getMailHandle();
			$hMail->addAddress($email);
			$hMail->Subject	= C('MAIL.USER_FINDPW_SUBJECT');
			$hMail->Body	= $mailBody;

			$sendResult = (bool) $hMail->send();
		}

		return $sendResult;
	}

	private function _commentDelay($p_picId) {
		$picId = intval($p_picId);
		$sessionKey = 'COMMENT_PIC' . $picId . '_BY_USER_' . getUid();
		$lastTime = session($sessionKey);
		if(!empty($lastTime) && $lastTime + C('COMMENT_DELAY') >= NOW_TIME) {
			$this->error('请休息下再评论！');
		}
		session($sessionKey, NOW_TIME);
	}

}
