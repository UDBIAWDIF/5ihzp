<?php
/**
 +------------------------------------------------------------------------------
 * 模块配置分配类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author	Aoki
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

class Config extends Think{
	const TYPE_RADIO		=3;
	const TYPE_SELECT		=4;
	const TYPE_CHECKBOX		=5;

	//配置动态赋值
	static public function assignConfig() {
		$slist = S('ConfigList');

		if(!$slist){
			$config = M('Setting');
			$slist = $config->select();
			//S('ConfigList',$slist,3600);
		}

		foreach($slist as $row){
			if($row['type'] == self::TYPE_RADIO | $row['type'] == self::TYPE_SELECT | $row['type'] == self::TYPE_CHECKBOX ){
				$row['value'] = unserialize($row['value']);
			}
			$name = strtoupper($row['name']);
			C($name,$row['value']);
		}
		return;
	}
}
?>