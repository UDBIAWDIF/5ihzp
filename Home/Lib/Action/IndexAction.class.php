<?php

/**
 * Index
 *
 * @author UID
 * @date 2014.07.19
 */
class IndexAction extends BaseAction{

	protected $_listRows	= 3;

	private $_indexCateFuncs = array(
		'thumbup'	=> '_indexGetThumbupCondition',
		'like'		=> '_indexGetLikeCondition',
		'group'		=> '_indexGetGroupCondition',
	);

	public function index($model = null, $tpl = null) {
		$map	= array('pic_status' => 1);
		$this->_indexCate($map);
		$this->setLimit($map);
		$this->assign('picTagList', D('PicTag')->queryList());
		$this->assign('topThumbupList', D('ThumbupPic')->topThumbupAtMonth());
		$this->assign('topFansList', D('LikeUser')->topFansAtMonth());
		$this->assign('topGroupList', D('Group')->topGroup());
		$mPic = D('PicView');
		$this->_picModelTrigger($mPic);
		$this->_setOrder();
		parent::index($mPic, $tpl);
	}

	public function morepic() {
		$lastPicId = intval($_REQUEST['lastpic']);
		$map = array(
			'pic_status'	=> 1,
			'pic_id'		=> array('lt', $lastPicId),
		);
		$this->_indexCate($map);
		$this->setLimit($map);
		$mPic = D('PicView');
		$this->_picModelTrigger($mPic);
		parent::index($mPic, 'index_piclist');
	}

	private function _picModelTrigger($mPic) {
		$mPic->withComment();
		if(!empty($_REQUEST['tag'])) {
			$mPic->withPicTag();
		}
	}

	private function _indexCate(&$map) {
		if(!empty($_REQUEST['indcate'])) {
			$this->_noLoginDeny();
			$indcate = trim($_REQUEST['indcate']);
			if(!empty($this->_indexCateFuncs[$indcate])) {
				$func = $this->_indexCateFuncs[$indcate];
				$this->$func($map);
			}
		}
	}

	private function _indexGetThumbupCondition(&$map) {
		$map['pic_thumbup_count'] = array('NEQ', 0);
	}

	private function _indexGetLikeCondition(&$map) {
		$map['pic_user_id'] = array('IN', D('LikeUser')->likeUidList(getUid()));
	}

	private function _indexGetGroupCondition(&$map) {
		$map['pic_user_id'] = array('IN', D('Group')->friendIdList(getUid()));
	}

	private function _setOrder() {
		$_REQUEST['_order'] = 'pic_order DESC, pic_createtime';
	}

}
