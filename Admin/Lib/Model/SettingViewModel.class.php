<?php

/**
 * 配置选项视图模型
 *
 * @author Aoki
 * @date 2010年5月21日
 */
import('ViewModel');
class SettingViewModel extends ViewModel {
	protected $viewFields = array(
		'Setting'=>array('id','type','title','name','value','module','order'=>'orders','description'),
		'Node'=>array('name'=>'nodeName','viewName', '_on'=>'Setting.node=Node.id'),
	);

	public function countByGroup($condition) {
		$map = array(
			'Node.id as id',
			'Node.viewName as viewName',
			'count(Setting.id) as count'
		);
		return $this->field($map)->where($condition)->group('Node.id')->select();
	}
}
?>
