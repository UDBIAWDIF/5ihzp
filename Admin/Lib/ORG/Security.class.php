<?php


/**
 * 安全权限验证类
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author	Aoki
 * @version   $Id$
 */

class Security extends Think{
	//访问验证
	//accessDecision

	/**
	 * 用户登录登记认证
	 * @access public
	 * @param
	 * @return void
	 */
	static public function authenticate($admin) {
		$Permissions = D('Permissions');
		$menu = $Permissions->getGroupMenu($admin['adm_group_id']);
		$groupNode = $Permissions->getGroupNode($admin['adm_group_id']);
		session('adm_id',$admin['adm_id']);
		session('adm_name',$admin['adm_name']);
		session('adm_gid',$admin['adm_group_id']);
		session('menu',$menu);
		session('groupNode',$groupNode);
	}
	//保存权限列表
	//saveAccessList

	//登出销毁SESSION
	static public function destoryAccess() {
		//Session::destroy();
		session('[destroy]');
	}

	//获取权限列表
	//getAccessList
	static public function getModuleAccess($module,$action,$gid) {
		//C('SUPERVISOR_LIST');
		//如果用户名在超级管理员列表里,就直接获得所有操作权限
		if(false !== in_array(session('username'), C('SUPERVISOR_LIST'))) {
			return true;
		}
		//获取验证过滤规则
		$FILTER = C('FILTER');
		//过滤免验证模块
		if(!in_array($module,$FILTER['MODULE'])) {
			$Node = D('Node');
			$map = array(
				'name'=>$module,
				'level'=>2
			);
			//获取模块数据
			$mvo = $Node->where($map)->find();
			$breadcrumb = session('breadcrumb');

			if($mvo){
				$breadcrumb['current_action_name'] = $mvo['viewName'];
				$Permissions = D('Permissions');
				$map = array(
					'nid'=>$mvo['id'],
					'gid'=>$gid
				);
				//获取用户当前模块权限
				$vo = $Permissions->where($map)->find();

				if($vo){
					$name = $action;
					foreach($FILTER['ACTION'] as $key=>$value){
						if(split(',',$value)){
							$value = split(',',$value);
						}
						if(is_array($value)){
							foreach($value as $row){
								if($action == $row){
									$name = $key;
									break;
								}
							}
						}
						if(is_string($value)){
							if($action == $value){
								$name = $key;
								break;
							}
						}
					}
					$vo['permissions'] = unserialize($vo['permissions']);
					$map = array(
						'pid'=>$mvo['id'],
						'name'=>$name,
						'level'=>3
					);
					//获取当前操作节点数据
					$avo = $Node->where($map)->find();

					$breadcrumb['current_method_name'] = $avo['viewName'];
					session('breadcrumb',$breadcrumb);
					if(is_array($vo['permissions'])){
						if(in_array($avo['id'],$vo['permissions'])){
							return true;
						}
					}else if(is_string($vo['permissions'])){
						if($avo['id'] == $vo['permissions']){
							return true;
						}
					}
					//如果没有查到，也可能是此操作无需映射，再用ACTION_NAME查一次
					$map = array(
						'pid'=>$mvo['id'],
						'name'=>$action,
						'level'=>3
					);
					//获取当前操作节点数据
					$avo = $Node->where($map)->find();

					if(is_array($vo['permissions'])){
						if(in_array($avo['id'],$vo['permissions'])){
							return true;
						}
					}else if(is_string($vo['permissions'])){
						if($avo['id'] == $vo['permissions']){
							return true;
						}
					}
				}
			}
			return false;
		}
		return true;
	}

}

?>