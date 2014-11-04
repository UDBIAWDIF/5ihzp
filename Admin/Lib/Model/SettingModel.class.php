<?php

/**
 * 设置
 * @author Aoki
 */
class SettingModel extends Model {
	const SYS_SETTING_CACHE_NAME = 'Sys_Setting';

	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','标题必须！',0),
		array('name','require','字段名必须！',0,),
	);
	// 自动填充设置
	protected $_auto	 =	 array(
	);

	public function getModules(){
		return $this->field('module')->group('module')->select();
	}

	public function add($data='',$options=array()) {
		$this->_serializeData($data);
		return  parent::add($data,$options);
	}

	public function save($data='',$options=array()) {
		$this->_serializeData($data);
		return  parent::save($data,$options);
	}

	public function getSetting(){
		$this->cache = Cache::getInstance();
		$setting = $this->cache->get(self::SYS_SETTING_CACHE_NAME);
		if($setting === false){
			$rs = $this->field('name,value')->select();
			$setting = array();
			foreach($rs as $val){
				$setting[$val['name']] = $val['value'];
			}
			$setting = $this->cache->set(self::SYS_SETTING_CACHE_NAME, $setting);
		}
		return $setting;
	}

	public function saveRecommendAlbum($value) {
		$albumName = 'RECOMMEND_ALBUM';
		$rmpk = $this->getPk();
		$where = array('name' => $albumName);
		$settingId = $this->where($where)->getField($rmpk);
		$saveResult = false;
		$recommendAlbumSetting = array(
				'type'		=> 4,
				'title'		=> '推荐专辑列表',
				'value'		=> serialize($value),
				'module'	=> '系统',
			);
		if(!empty($settingId)) {
			$recommendAlbumSetting[$rmpk] = $settingId;
			$saveResult = parent::save($recommendAlbumSetting);
		} else {
			$recommendAlbumSetting = array_merge($recommendAlbumSetting, $where);
			$saveResult = parent::add($recommendAlbumSetting);
		}

		return $saveResult;
	}

	private function _serializeData(&$data) {
		if($data['type'] == 3 |$data['type'] == 4 |$data['type'] == 5 ){
			$data['value'] = split(chr(13),$data['value']);
			foreach($data['value'] as &$row){
				if(!empty($row)){
					$temp = split(':',trim($row));
					$arr[$temp[0]]=$temp[1];
				}
			}
			$data['value']=serialize($arr);
		}

		$this->cache = Cache::getInstance();
		$this->cache->rm(self::SYS_SETTING_CACHE_NAME);
	}
}
?>
