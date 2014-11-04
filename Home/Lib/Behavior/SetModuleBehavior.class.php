<?php
/**
 * 设置MODULE, 把POST的MODULE名设置到GET,再传入TP系统
 */
class SetModuleBehavior extends Behavior {

	public function run(&$params) {
		if($_POST[C('VAR_MODULE')]) {
			$_GET[C('VAR_MODULE')] = $_POST[C('VAR_MODULE')];
		}

		return;
	}

}
?>
