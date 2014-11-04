<?php

/**
 * 图片模块
 *
 * @author UID
 * @date 2014.07.19
 */
class PicAction extends BaseAction{

	public function manage1() {
		$this->_noLoginDeny();
		$mPicMonth	= D('PicMonth');
		$month		= $_REQUEST['month'];
		if(!$mPicMonth->checkMonthFormat($month)) {
			$month = $mPicMonth->getCurMonth();
		}

		$this->assign('month', $month);
		$this->assign('piclist', D('PicView')->queryMonthListByUser($month, getUid()));
		$this->assign('monthlist', $mPicMonth->queryAllByUser(getUid()));
		$this->assign('user', getUser());
		$this->display();
	}

	public function manage() {
		import('@.ORG.MonthPage');
		$this->_pageHandle = new MonthPage(getUid());
		$this->index(null, 'manage');
	}

	public function addpicture() {
		$this->_noLoginDeny();
		$mPic	= D('Pic');
		$pic	= $mPic->create();
		if(false === $pic) {
			$this->error($mPic->getError());
		}
		$picId	= $mPic->add($pic);

		if($picId) {
			D('User')->incOrDecData(getUid(), 'u_pic_count');
			D('PicTagRel')->addByPost($picId);
			$mPicMonth	= D('PicMonth');
			$picMonth	= array(
				'pm_user_id'	=> getUid(),
				'pm_date'		=> $mPicMonth->getCurMonth(),
			);
			if($mPicMonth->create($picMonth)) {
				$mPicMonth->add();
			}
			$this->success('操作成功');
		} else {
			$this->error('数据提交失败！');
		}
	}

	public function show() {
		$this->assign('pic', D('PicView')->getInfo($this->_getId()));
		$this->assign(
			'commentlist',
			D('PicCommentView')
				->queryListByPic(
					$this->_getId(),
					0
				)
		);
		$this->display();
	}

	public function modify() {
		$this->_noLoginDeny();
		$this->_checkAuth();
		if(!IS_POST) {
			$this->display();
			die;
		}
	}

	public function delete() {
		$this->_noLoginDeny();
		$this->_checkAuth();
		D('Pic')->disable($this->_getId());
		$this->success('删除成功!');
	}

	public function queryComment() {
		$picId	= intval($_REQUEST['id']);
		$start	= intval($_REQUEST['start']);
		$commentlist = D('PicCommentView')->queryListByPic($picId, $start);
		$this->assign('commentlist', $commentlist);
		$this->display('comment');
	}

	protected function _checkAuth($p_id = 0) {
		if(!parent::_checkAuth($p_id)) {
			$this->error('您没有操作此照片的权限!');
			die;
		}
	}

}
