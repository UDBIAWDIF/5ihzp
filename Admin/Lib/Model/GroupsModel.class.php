<?php

/**
 * 用户组模型
 *
 * @author Aoki
 * @date 2010年5月21日
 */
class GroupsModel extends Model {

	// 自动验证设置
	protected $_validate	 =	 array(
		array('groupName','require','组名必须！'),
		array('groupName','','组名已经存在！',0,'unique',self::MODEL_INSERT),
	);

	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1')
	);

}
?>
