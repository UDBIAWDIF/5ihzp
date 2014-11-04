<?php

class PicTagModel extends Model {
	// 自动验证设置
	protected $_validate = array(
		array('pt_name','require','名称必须！',0),
		array('pt_name','','名称已经存在！',0,'unique',1),
	);

	// 自动填充设置
	protected $_auto = array(
		array('pt_name','trim',3,'function'),
		array('pt_order','intval',3,'function'),
		array('pt_status',1,1),
	);

}
