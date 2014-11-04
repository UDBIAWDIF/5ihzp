<?php

/**
 * 系统反馈视图模型
 *
 *
 */
class SysFeedbackViewModel extends ViewModel {

	protected $pk = 'id';

	protected $viewFields = array(
		'SysFeedback' => array(
			'id',
			'cu_id',
			'contact',
			'content',
			'ctime',
			'status',
			'_type' => 'LEFT',
		),
		'Customer' => array(
			'name',
			'nickname',
			'realname',
			'head',
			'_on' => 'Customer.id = SysFeedback.cu_id',
		),
	);

	public function getDbFields(){
		$fields = array();
		foreach($this->viewFields as $table){
			foreach($table as $field){
				$fields[] = $field;
			}
		}
		return $fields;
	}//end getDbFields()

}
?>
