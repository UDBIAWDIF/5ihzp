<?php

/**
 * 系统设置
 *
 * @author Aoki
 */
class SettingAction  extends GlobalAction{

	public function _before_index() {
		$settingModel = D('Setting');
		$modules = $settingModel->getModules();
		$this->assign('modules',$modules);
	}

	public function _before_configList() {
		$settingModel = D('Setting');
		$modules = $settingModel->getModules();
		$this->assign('modules',$modules);
	}

	protected function _search($name = '') {
		$map = parent::_search($name);

		return $map;
	}

	public function configList() {
		parent::index();
	}


	public function update() {
		$setting = D('Setting');

		if($_POST['act']=='single'){
			$vo = $setting->create();
			if($vo) $r = $setting->save($vo);
			if(!$r && $r != 0) $this->error('更新出错!'.$setting->getError());
		}else{
			$i = 0;
			foreach($_POST as $key=>$value){
				if($key != 'Submit' && $key != '__hash__' && $key != 'type' && $key != 'id'){
					$vo = array(
						'name'=>$key,
						'value'=>$value,
						'type'=>$_POST['type'][$i],
						'id'=>$_POST['id'][$i]
					);
					$i++;
					$r = $setting->save($vo);
					if(!$r && $r != 0) $this->error($key.'更新出错!'.$setting->getError());
				}
			}
		}
		$this->success('更新成功！');
	}

}