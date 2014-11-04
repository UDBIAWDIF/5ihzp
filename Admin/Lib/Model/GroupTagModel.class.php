<?php

class GroupTagModel extends Model {
	// 自动验证设置
	protected $_validate = array(
		array('gt_name','require','名称必须！',0),
		array('gt_name','','名称已经存在！',0,'unique',1),
	);

	// 自动填充设置
	protected $_auto = array(
		array('gt_name','trim',3,'function'),
		array('gt_order','intval',3,'function'),
		array('gt_status',1,1),
	);

}
