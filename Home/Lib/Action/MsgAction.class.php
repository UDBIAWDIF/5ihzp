<?php

/**
 * 消息地区模块
 *
 * @author UID
 * @date 2014.08.31
 */
class MsgAction extends BaseAction{

	final public function usermsg() {
		$noReadList	= D('MsgView')->queryNoReadList(getUid());
		$readList	= D('MsgView')->queryReadList(getUid());
		empty($noReadList)	&& $noReadList	= null;
		empty($readList)	&& $readList	= null;
		$this->assign('noReadList',	$noReadList);
		$this->assign('readList',	$readList);
		$this->display();
	}

	final public function pass() {
		$this->success('已通过');
	}

	final public function checked() {
		$this->success('已查看');
	}

}
