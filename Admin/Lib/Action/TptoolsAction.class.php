<?php

/**
 * 用户管理
 *
 * @author UID
 * @date   2013.02.27
 */
class TptoolsAction  extends GlobalAction{

	private static $mTools;
	private static $thisLinks = array();
	private static $sampleTplDir = '';

	public function _initialize() {
		self::$mTools = D('TpTools');
		self::$sampleTplDir = TMPL_PATH . 'Public/sampleTpl/';
	}//end _initialize()

	public function index() {
		self::$thisLinks['createTpl'] = '生成模板';
		self::$thisLinks['ViewModel'] = '生成视图模型';
		$this->_displayLinks();
	}//end index()

	public function createTpl() {
		if(!isset($_REQUEST['module']) || empty($_REQUEST['module'])) {
			$this->_createTplInputModule();
		} else {
			$module = ucfirst(trim($_REQUEST['module']));
			$srcDelim = array('L' => C('TMPL_L_DELIM'), 'R' => C('TMPL_R_DELIM'));
			$isToken = C('TOKEN_ON');
			C('TMPL_L_DELIM', '{<');
			C('TMPL_R_DELIM', '>}');
			C('TOKEN_ON', false);

			$hModule = A($module);
			$createTplDir = TMPL_PATH . "{$module}/";
			@mkdir($createTplDir);
			$tplIndexData = $hModule->tplIndexData();
			$tplIndexData['name_field'] = $tplIndexData['request_name'];
			$tplIndexData['request_name'] = '{$_REQUEST[\'' . $tplIndexData['request_name'] . '\']}';
			$this->assign($tplIndexData);
			$tplIndexContent = $this->fetch(self::$sampleTplDir . 'index.html');
			$this->_fetchTpl($tplIndexContent);
			file_put_contents($createTplDir. "index.html", $tplIndexContent);

			$tplEditData = $hModule->tplEditData();
			$this->assign($tplEditData);
			$tplEditContent = $this->fetch(self::$sampleTplDir . 'edit.html');
			$this->_fetchTpl($tplEditContent);
			file_put_contents($createTplDir. "edit.html", $tplEditContent);

			C('TOKEN_ON', $isToken);
			C('TMPL_R_DELIM', $srcDelim['R']);
			C('TMPL_L_DELIM', $srcDelim['L']);

			$this->success('生成模板 - 操作完成！');
		}
	}//end createTpl()

	/* private function _tplIndexData() {
		return array(
					'title' => '数据',
					'request_name' => 'name',
					'id' => 'a_id',
					'status' => array('a_status', '正常', '非法'),
					'fields' => array(
						array('name', '名称', '20'),
						array('desc', '简介'),
						array('pv', '查阅次数'),
						array('ctime', '添加时间', '10', 'date=\'Y-m-d\', ###'),
					),
				);
	}//end _TplIndexData() */

	/* private function _tplEditData() {
		return array(
					'title' => '数据',
					'id' => 'a_id',
					'fields' => array(
						array('name', '名称', '20'),
						array('desc', '简介'),
						array('pv', '查阅次数'),
						array('ctime', '添加时间', '0', 'date=\'Y-m-d\', ###'),
						array('status', '状态'),
					),
				);
	}//end _tplEditData() */

	private function _fetchTpl(&$content) {
		$content = str_replace('<--', '<', $content);
		$content = str_replace('__-', '__', $content);
		$content = str_replace('<TPL_PUBLIC>', '../Public', $content);
	}//end _fetchTplIndex()

	public function ViewModel() {
		$tModel = self::$mTools;
		//表信息
		$tables = $tModel->getDbTablesAndIsStandard();
		//ViewModel列表
		$viewModels = $tModel->getViewModelList();

		$this->assign('viewModels', $viewModels);
		$this->assign('tables', $tables);
		$this->display();
	}//end ViewModel()

	public function createViewModel() {
		;
	}//end createViewModel()

	private function _displayLinks() {
		$this->_displayHtmlHeader();
		foreach(self::$thisLinks as $action => $title) {
			echo '<a href=\'' . __URL__ . "/{$action}" . "'>{$title}</a><br /><br />";
		}
		$this->_displayHtmlFooter();
	}//end _displayLinks()

	private function _createTplInputModule() {
		$this->_displayHtmlHeader();
		echo '<form>',
			'请输入模块名：<input type="text" name="module" />',
			'<input type="submit" value="提交操作" />',
			'</form>';
		$this->_displayHtmlFooter();
	}

	private function _displayHtmlHeader() {
		header('Content-type: text/html; charset=UTF-8');
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'<html xmlns="http://www.w3.org/1999/xhtml">',
			'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>',
			'<body>';
	}

	private function _displayHtmlFooter() {
		echo '</body></html>';
	}

}
?>
