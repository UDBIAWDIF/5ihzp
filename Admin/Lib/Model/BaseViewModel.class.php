<?php

/**
 * 视图模型基类
 *
 * @author UID
 */
class BaseViewModel extends ViewModel {

	final public function getFieldByPkId($p_id, $p_field, $cache = false) {
		$id = intval($p_id);
		$field = filter_str(trim($p_field));
		$value = null;

		if(!empty($id) && !empty($field)) {
			$value = $this
					->where(array($this->getPk() => $id))
					->cache($cache)
					->getField($field);
		}

		return $value;
	}

	public function getOrderField(){
		return 'ctime';
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

	public function create($data='', $type='') {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($data, $type);
		$this->_model_false_save_debug($result, $data);
		return $result;
	}

	public function add($data='', $options = array(), $replace = false) {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($data, $options, $replace);
		$this->_model_false_save_debug($result, $options);
		return $result;
	}

	public function save($data='', $options = array()) {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($data, $options);
		$this->_model_false_save_debug($result, $options);
		return $result;
	}

	public function delete($options = array()) {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($options);
		$this->_model_false_save_debug($result, null);
		return $result;
	}

	public function find($options = array()) {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($options);
		$this->_model_false_save_debug($result, null);
		return $result;
	}

	public function select($options = array()) {
		$thisMethod	= $this->_getMethodName(__METHOD__);
		$result		= parent::$thisMethod($options);
		$this->_model_false_save_debug($result, null);
		return $result;
	}

	protected function _model_false_save_debug($result, $data) {
		if(empty($result) && C('MODEL_FALSE_SAVE_DEBUG')) {
			debugVar($this->_sql(), 'Model False');
		}
	}

	private function _getMethodName($fullMethodName) {
		$methodName = explode('::', $fullMethodName);
		return $methodName[1];
	}

}
