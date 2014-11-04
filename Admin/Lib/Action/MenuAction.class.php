<?php

/**
 * 菜单类
 *
 * @author Aoki
 */
class MenuAction  extends GlobalAction{

	/**
	 * 侧边栏菜单
	 * @access public
	 * @return void
	 */
	public function menu(){
		$action=$_REQUEST['action'];
		$Node = D("Node");
		$childNode = $Node->getMenuChildNode($action);

		$menu = session('menu');
		$groupNode = session('groupNode');

		if(array_key_exists($action,$childNode)){
			switch($action){
				case 'Home':
					$url=__APP__.'/Index/main';
					break;
				default:
					if(!empty($childNode)){
						$url = __APP__.'/';
						foreach($childNode as $row){
							$url.=$row['name'];
							break;
						}
					}
					break;
			}
		}else{
			if(!$action){
				foreach($childNode as $row){
					if($row['id'] != $menu[0]){
						continue;
					}
					$managerName = $row['name'];
					foreach($row['child'] as $modules){
						//dump($modules);
						$moduleName .= $modules['name'];
						foreach($modules['child'] as $actions){
							$actionName .= $actions['name'];
							break;
						}
						break;
					}
					break;
				}
				$url= U($moduleName.'/'.$actionName);
				$childNode = $Node->getMenuChildNode($managerName);
			}else{
				foreach($childNode as $modules){
					//dump($modules);
					$moduleName .= $modules['name'];
					foreach($modules['child'] as $actions){
						$actionName .= $actions['name'];
						break;
					}
					break;
				}
				$url= U($moduleName.'/'.$actionName);
			}
		}
		$this->assign('menu',$menu);
		$this->assign('groupNode',$groupNode);
		$this->assign('url',$url);
		$this->assign('action',$action);
		$this->assign('childNode',$childNode);
		$this->display("Public:menu");
	}


	/**
	 * 顶栏菜单
	 * @access public
	 * @return void
	 */
	public function topmenu(){
		$Node = M("Node");
		
		$menu = session('menu');
		$groupNode = session('groupNode');
		$list = $Node->where('pid = 0 AND type = 1')->order('orders ASC,id asc')->select();
		$topmenu = array();
		foreach($list as $row){
			if(in_array($row['id'],$menu)){
				$topmenu[] = $row;
			}
		}
		$this->assign('list',$topmenu);
		$this->assign('menu',$menu);
		$this->assign('uid',$this->getUid());
		$this->display("Public:topmenu");
	}

}
?>