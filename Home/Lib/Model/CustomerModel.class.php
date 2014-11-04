<?php
/**
 * 用户模型
 */
class CustomerModel extends BaseModel {

	protected $pk = 'id';

	protected $_map = array(
			'username'	=> 'name',
			'uid'		=> 'id',
			'sign'		=> 'description',
			'imei'		=> 'mobile_imei',
			'model'		=> 'mobile_model',
	);

	protected $_auto = array(
		array('name', 'trim', 1, 'function'),
		array('password', 'md5', 1, 'function'),
		array('ctime', 'time', 1, 'function'),
		array('utime', 'time', 3, 'function'),
		array('nickname', 'trim', 2, 'function'),
		array('status', 1),
	);

	public function _initialize() {
		parent::_initialize();
		$this->_validate = array(
			//TODO 用户名为空时的错误码另外定
			array(
				'name', 'require',
				C('ERROR.USERNAME_NOT_INPUT'), 1,
				'', 1
			),
			array(
				'name', 'checkUsername',
				C('ERROR.USERNAME_INPUT_FAULT'), 1,
				'function', 1
			),
			array(
				'name', '',
				C('ERROR.USER_EXIST'), 1,
				'unique', 1
			),

			array(
				'nickname', 'checkNickname',
				C('ERROR.NICKNAME_INPUT_FAULT'), 0,
				'function', 2
			),
			array(
				'nickname', '',
				C('ERROR.NICKNAME_EXIST'), 0,
				'unique', 2
			),

			array('description', 'checkDescriptionLen',
				C('ERROR.USER_SET_DESCRIPTION_FAULT'), 0,
				'callback', 2
			),

			//TODO 密码长度限制，错误码另外定
			array(
				'password', 'require',
				C('ERROR.PASSWORD_NOT_INPUT'), 1,
				'', 1
			),
			array('password', 'checkPWStrength',
				C('ERROR.PASSWORD_INPUT_FAULT'), 1,
				'function', 1
			),
		);
	}

	public function genMsg($params) {
		$this->_addSysMsg($params);
	}

	protected function checkDescriptionLen($description) {
		return C('USERSIGN_LEN_MAX') >= mb_strlen($description, 'UTF-8');
	}

	// 添加系统消息
	private function _addSysMsg($params) {
		D('SysMessage')->addSysMsg($params['uid']);
	}

}
