<?php
/**
 * 系统消息模型
 */
class SysMessageModel extends BaseModel {

	// 添加系统消息
	public function addSysMsg($p_uid) {
		/*
		 1. 找出所有在发布期内的系统消息
		 2. 查找消息表里有没有 消息ID + 用户ID 对应的记录
		 3. 没有时,往消息表里添加消息记录
		 */
		$uid = intval($p_uid);
		$validList = $this->queryValidSysMsg();
		if(empty($validList)) {
			return false;
		}

		$mMsg = M('Message');
		$where = array(
			'type'	=> C('MSG_TYPE.SYS_MSG'),
			'cu_id'	=> $uid,
		);
		foreach($validList as $sysmsg) {
			$where['rec_id'] = intval($sysmsg[$this->getPk()]);
			$msg = $mMsg->where($where)->find();
			if(empty($msg)) {
				$msgData = array(
					'cu_id'		=> $uid,
					'type'		=> C('MSG_TYPE.SYS_MSG'),
					'rec_id'	=> $where['rec_id'],
					'title'		=> $sysmsg['title'],
					'icon'		=> $sysmsg['icon'],
					'content'	=> $sysmsg['content'],
					'ctime'		=> $_SERVER['REQUEST_TIME'],
					'utime'		=> $_SERVER['REQUEST_TIME'],
					'is_read'	=> 0,
				);
				$mMsg->add($msgData);
			}
		}
	}

	private function queryValidSysMsg() {
		$todayDateTime = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		return $this
					->where(
						array(
							'status'		=> 1,
							'pubtime_begin'	=> array('elt',	$todayDateTime),
							'pubtime_end'	=> array('gt',	$todayDateTime),
						)
					)
					->select();
	}

}
