<?php

/**
 * 节点管理
 *
 * @author Aoki
 */
class NodeAction  extends GlobalAction{

	/**
	 +----------------------------------------------------------
	 * 节点列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function index() {
		$Node = D('Node');
		import("ORG.Util.Page");

		if(S('allNode')) {
			$allNode	=	S('allNode');
		}else{
			$allNode = $Node->getAllNode();
			S('allNode',$allNode,1);
		}
		$countAll = $Node->count();
		//如果有选择节点，则查看当前节点下级的节点
		$id=intval($_GET["id"]);
		if (!$id) {
			$count = $Node->where('pid = 0')->count();
			$p = new Page($count, 10);
			$list = $Node->where("pid = 0")
						 ->limit($p->firstRow.','.$p->listRows)
						 ->order('orders asc,id asc')
						 ->group('id')
						 ->select();
			$page = $p->show();
		}else{
			$currentNode = $Node->find($id);
			$count = $Node->where("pid = {$id}")->count();
			$p = new Page($count, 10);
			$list = $Node->where("pid = {$id}")
						 ->limit($p->firstRow.','.$p->listRows)
						 ->order('orders asc,id asc')
						 ->group('id')
						 ->select();
			$page = $p->show();

			$this->assign('currentNode',$currentNode);
			$this->assign('currentCount',$count);
		}
		$this->assign('allNode',$allNode);
		$this->assign( "page", $page );
		$this->assign('list',$list);
		$this->assign('count',$countAll);
		$this->display();

	}

	/**
	 +----------------------------------------------------------
	 * 添加节点页面
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function add(){
		$Node	= D("Node");

		if(S('allNode')) {
			$allNode	=	S('allNode');
		}else{
			$allNode = $Node->getAllNode();
			S('allNode',$allNode,1);
		}
		$this->assign('list',$allNode);
		$this->display();
	}

	/**
	 +----------------------------------------------------------
	 * 节点编辑界面
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function edit(){
		$id=intval($_GET["id"]);
		if (!$id) $this->error(L('_SELECT_NOT_EXIST_'));

		$Node	= D("Node");

		if(S('allNode')) {
			$allNode	=	S('allNode');
		}else{
			$allNode = $Node->getAllNode();
			S('allNode',$allNode,1);
		}
		$vo=$Node->find($id);
		if (!$vo) $this->error('未找到该节点！');

		$this->assign('list',$allNode);
		$this->assign('vo',$vo);
		$this->display();
	}

	public function insert() {
		$Node	= D("Node");
		$id = $this->_add($Node);
		//保存成功
		if($id) {
			$vo=$Node->find($id);
			$this->assign('jumpUrl',__URL__.'/index/id/'.$vo['pid']);
			$this->success('数据提交成功！');
		}else {
			$this->error('数据提交失败！');
		}
	}

	public function update() {
		$vo = $this->_save();
		//保存成功
		if($vo) {
			$this->assign('jumpUrl',__URL__.'/index/id/'.$vo['pid']);
			$this->success('数据提交成功！');
		}else {
			$this->error('数据提交失败！');
		}
	}

}

?>