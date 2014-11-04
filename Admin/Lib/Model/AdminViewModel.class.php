<?php

import('ViewModel');
class AdminViewModel extends ViewModel {

	protected $viewFields = array(
		'Admin'=>array('adm_id','adm_group_id','adm_name','adm_email','adm_realname','adm_penname','adm_description','adm_createtime','adm_updatetime','adm_status','_type'=>'LEFT'),
		'AdminGroup'=>array('ag_name','ag_description','_on'=>'Admin.adm_group_id = AdminGroup.ag_id'),
	);

	public function getPk(){
		return 'Admin.adm_id';
	}

	public function getOrderField(){
		return 'adm_id';
	}

	public function getDbFields(){
		$fields = array();
		foreach($this->viewFields as $table){
			foreach($table as $field){
				$fields[] = $field;
			}
		}
		return $fields;
	}

	public function countByGroup($condition) {
		$map = array(
			'AdminGroup.ag_id as ag_id',
			'AdminGroup.name as groupName',
			'count(Admin.adm_id) as userCount'
		);
		return $this->field($map)->where($condition)->group('AdminGroup.ag_id')->select();
	}

}
