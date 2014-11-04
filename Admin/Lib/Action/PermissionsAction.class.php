<?php

/**
 * 权限分配类
 *
 * @author Aoki
 */
class PermissionsAction  extends GlobalAction{

	function _initialize(){
		parent::_initialize();
		$this->_checkSecurity();
	}

	/**
	 +----------------------------------------------------------
	 * 显示权限分配表单
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function index() {
		$id = intval($_GET['id']);
		$this->assign('groupid',$intGroupId);

		$Node = D('Node');
		if(S('allNode')) {
			$allNode	=	S('allNode');
		}else{
			$allNode = $Node->getAllNode();
			S('allNode',$allNode,1);
		}

		$groupModel = M('Groups');
		$groups = $groupModel->select();
		if($id != 0){
			$Permissions = D('Permissions');
			$groupNode = $Permissions->getGroupNode($id);
			$menu	 = $Permissions->getGroupMenu($id);
		}

		$this->assign('groupNode',$groupNode);
		$this->assign('menu',$menu);
		$this->assign('groups',$groups);
		$this->assign('allNode',$allNode);
		$this->display();
	}

	public function doAdd() {
		$gid = intval($_POST['gid']);
		$perArr = $_POST['popedom'];

		$Permissions = M('Permissions');
		if($Permissions->where("gid = {$gid}")->find()){
			$Permissions->where("gid = {$gid}")->delete();
		}

		foreach($perArr as $key => $value){
			$menu[] = $key;
			foreach ($value as $k=>$val) {
				$list['gid']	= $gid;
				$list['nid']	= $k;
				$list['permissions']  = serialize($val);
				$Permissions->add($list);
			}
		}
		$list['gid']	= $gid;
		$list['nid']	= 0;
		$list['permissions']  = serialize($menu);
		$Permissions->add($list);
		$this->success('提交成功！');
	}

	public function getInfoList() {
		$type = $_POST['type'];
		$id = intval($_POST['id']);
		$Node = M('Node');

		switch ($type) {
			case 'modellist':
				$list = $Node->where("pid  = {$id}")->select();
				break;
			case 'actionlist':
				$list = $Node->where("pid  = {$id}")->select();
				$pNode= $Node->find($id);
				$this->assign('pNode',$pNode);
				break;
			default: break;
		}

		$this->assign('list',$list);
		$this->display();
	}
}