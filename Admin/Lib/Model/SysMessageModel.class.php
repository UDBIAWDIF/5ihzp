<?php

/**
 * 系统消息模型
 *
 * @author UID 2013.04.08
 *
 */
class SysMessageModel extends Model {

	protected $pk  = 'id';

	protected $_validate = array(
		array('title', 'require', '标题必填'),
		array('content', 'require', '内容'),
		array('rec_id', 'require', '关联ID'),
	);

	protected $_auto = array(
		array('status', '1'),
		array('ctime', 'time', 1, 'function'),
		array('utime', 'time', 3, 'function'),
		array('icon', 'trim', 3, 'function'),
		array('type', 'trim', 3, 'function'),
		array('title', 'trim', 3, 'function'),
		array('content', 'trim', 3, 'function'),
		array('pubtime_begin', 'trim', 3, 'function'),
		array('pubtime_end', 'trim', 3, 'function'),
	);

}
?>
