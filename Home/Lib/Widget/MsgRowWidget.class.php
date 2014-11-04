<?php

// 单行消息挂件, 根据对应的消息类型显示一行消息
class MsgRowWidget extends Action {

	// $msg 为一行消息
	public function render($msg) {
		$this->assign('msg', $msg);
		return $this->fetch("Msg:row_{$msg['msg_type']}");
	}

}
