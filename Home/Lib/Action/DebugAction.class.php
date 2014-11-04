<?php

class DebugAction {

	public function _construct() {
		define('IN_SAFE', TRUE);
	}

	public function index() {
		echo file_get_contents(RUNTIME_PATH . 'Debug/debug');
	}

	public function clear($echoResult = true) {
		debugVar('', false);
		if($echoResult) echo 'Clear Debug Success!';
		else echo 'Clear Debug Failed!';
	}

	public function log() {
		echo str_replace(array("\r\n"), '<br/>', file_get_contents(LOG_PATH.date('y_m_d').'.log'));
	}

	public function clearLog(){
		file_put_contents(LOG_PATH.date('y_m_d').'.log', '');
		echo 'Clear Log Success!';
	}

}
