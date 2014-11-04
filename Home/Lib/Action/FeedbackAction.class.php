<?php

/**
 * 点金游戏中心接口 - 评论模块
 *
 * @author UID
 */
class FeedbackAction extends BaseAction{

	public function _initialize() {
		parent::_initialize();
		$this->_checkListLimit(C('DJGAME.QUERY_FEEDBACK_ROWS_LIMIT'), C('DJGAME.FEEDBACK_LIST_ACTION'));
	}//end _initialize()

	public function index() {
		$mFB = D('FeedbackView');
		$count = $mFB->countList($this->params);
		$list = $mFB->queryList($this->params);
		if(is_array($list)) {
			$code	= C('ERROR.SUCCESS');
			$picFields = array(
				'head',
			);
			getFileUrlOnSiteByList91Play($list, $picFields, 'IMG');
		} else if(!empty($list)){
			$code = $list;
			$list = '';
		} else {
			$code = C('ERROR.NODATA');
			$list = '';
		}

		$this->_outputData($code, $list, $count);
	}//end index()

	public function app() {
		$this->params['feedback_type'] = C('DJGAME.REFTYPE_APP');
		$this->index();
	}

	public function thumbup() {
		$mFB = M('Feedback');
		$mFB->where(array($mFB->getPk() => $this->params['id']))->setInc('feedback_good');
		$this->_outputData(C('ERROR.SUCCESS'));
	}//end thumbup()

}
?>
