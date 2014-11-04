<?php

/**
 * 手机操作系统模型
 *
 * @author UID 2013.04.09
 *
 */
class SysVersionModel extends Model {

	protected $pk  = 'id';

	protected $_validate = array(
		array('ver', 'require', '版本号必填！'),
		array('channel_name', 'require', '渠道名必填！'),
		array('ver,channel_name','','版本号已经存在！',1,'unique',3),
		array('description', 'require', '升级描述必填！'),
		array('url', 'require', '必须提供下载地址！'),
		array('release_date', 'require', '发布日期必填！'),
	);

	protected $_auto = array(
		array('status', '1'),
		array('ctime', 'time', 1, 'function'),
		array('utime', 'time', 3, 'function'),
		array('ver', 'trim', 3, 'function'),
		array('channel_name', 'trim', 3, 'function'),
		array('url', 'trim', 3, 'function'),
		array('release_date', 'trim', 3, 'function'),
	);

}
?>
