<?php

/**
 * 地区模块
 *
 * @author UID
 * @date 2014.08.09
 */
class RegionAction extends BaseAction{

	public function queryChildren() {
		$model	= D(MODULE_NAME);
		$list	= $model
					->where(array(
						'parent_id' => intval($_REQUEST['id']),
					))
					->select();
		echo json_encode($list);
	}

}
