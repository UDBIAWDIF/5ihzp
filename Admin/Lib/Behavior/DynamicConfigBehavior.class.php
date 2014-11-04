<?php
/**
 * 自定义配置类
 */
class DynamicConfigBehavior extends Behavior {
	const TYPE_RADIO		=3;
	const TYPE_SELECT		=4;
	const TYPE_CHECKBOX		=5;

	protected $options = array();

	public function run(&$params) {
		$slist = S('ConfigList5ihzpAdmin');

		if(!$slist){
			$config = M('Setting');
			$slist = $config->select();
			S('ConfigList5ihzpAdmin', $slist, 3600);
		}

		foreach($slist as $row) {
			if($row['type'] == self::TYPE_RADIO || $row['type'] == self::TYPE_SELECT || $row['type'] == self::TYPE_CHECKBOX ) {
				$row['value'] = unserialize($row['value']);
			}
			$name = strtoupper($row['name']);
			C($name, $row['value']);
		}

		return;
	}

}
?>
