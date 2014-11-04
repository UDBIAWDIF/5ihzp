<?php

/**
 * 用户组管理
 *
 * @author Aoki
 */

class GroupsAction extends GlobalAction {

	public function index() {
		$Group	= D("GroupsView");

		import("ORG.Util.Page"); //导入分页类

		$count = M('Groups')->count();	//计算用户组总数
		$p = new Page( $count, C('INDEX_ROWS') );
		$list=$Group->limit($p->firstRow.','.$p->listRows)
					->group('Groups.id')
					->order('id asc')
					->select();
		$page = $p->show ();

		$this->assign( "page", $page );
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}

	/**
	 +----------------------------------------------------------
	 * 修改组界面
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function edit(){
		$id=intval($_GET["id"]);
		if (!$id) $this->error(L('_SELECT_NOT_EXIST_'));
		//if (in_array($id,array(1,2),true))$this->error('内置用户组不可编辑');
		$Group=M("Groups");
		$list=$Group->find($id);
		if (!$list) $this->error('未找到该组！');
		$this->assign('vo',$list);
		$this->display();
	}

}

?>