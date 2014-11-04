<?php

/**
 * 群模块
 *
 * @author UID
 * @date 2014.07.30
 */
class GroupAction extends BaseAction{

	public function _before_index() {
		$commonLimit = array('g_status' => 1);
		$nameSearch = filter_str($_REQUEST['namesearch']);
		!empty($nameSearch)
			&& $commonLimit['g_name|u_name|g_name_pinyin|u_name_pinyin']
			= array('like', "%{$nameSearch}%");

		$this->setLimit($commonLimit);
	}

	public function manage() {
		$this->assign('grouplist', D('GroupView')->queryListByCurUser());
		$this->display();
	}

	public function joinlist() {
		$this->assign('grouplist', D('GroupView')->queryUserJoinList());
		$this->display();
	}

	public function show() {
		$this->display();
	}

	public function modify() {
		$this->_checkAuth();
		if(!IS_POST) {
			$id = $this->_getId();
			$this->assign(
				'group',
				D('Group')->find($id)
			);
			$this->_assignRegion();
			$this->assign('gptaglist', D('GroupTag')->queryList());
			$this->assign(
				'checkedGroupTag',
				D('GroupTagRel')->queryTagIdListByGroup($id, true)
			);
			$this->display();
			die;
		}

		$mGroup	= D('Group');
		$group	= $mGroup->create();
		if(false === $group) {
			$this->error($mGroup->getError());
		}

		if(empty($group['g_icon_id'])) {unset($group['g_icon_id']);}
		$modifyResult = $mGroup->save($group);
		if($modifyResult) {
			$gpId = $group[$mGroup->getPk()];
			D('GroupTagRel')->addByPost($gpId);
			$this->success('操作成功');
		} else {
			$this->error('数据提交失败！');
		}
	}

	public function invite() {
		$this->_checkAuth();
		if(!IS_POST) {
			$id = $this->_getId();
			$this->assign(
				'group',
				D('Group')->find($id)
			);
			$this->assign(
				'friends',
				D('Group')->notJoinFriends($id)
			);
			$this->display();
			die;
		}

		$inviteResult = D('Group')->invite($id = $this->_getId(), $this->_request('userids'));
		if($inviteResult) {
			$this->success('邀请成功');
		} else {
			$this->error('邀请失败！');
		}
	}

	public function delete() {
		$this->_checkAuth();
		D('Group')->disable($this->_getId());
		$this->success('删除成功!');
	}

	public function setIcon() {
		$up_result = upImg(C('IMG_UP_TYPE.GROUP_ICON'));
		$this->_json_response($up_result);
	}

	public function addgroup() {
		$this->_noLoginDeny();
		$this->_checkGroupLimitAndDeny();
		$mGroup	= D('Group');
		$group	= $mGroup->create();
		if(false === $group) {
			$this->error($mGroup->getError());
		}

		$gpId	= $mGroup->add($group);
		if($gpId) {
			D('User')->incOrDecData(getUid(), 'u_group_count');
			D('Group')->userJoinToGroup(getUid(), $gpId);
			D('GroupTagRel')->addByPost($gpId);
			$iconId	= $mGroup->field('g_icon_id')->find($gpId);
			$icon	= array(
				'icon_id'		=> intval($iconId['g_icon_id']),
				'icon_rel_id'	=> $gpId,
			);
			D('Icon')->save($icon);
			$this->success('操作成功');
		} else {
			$this->error('数据提交失败！');
		}
	}

	protected function _checkAuth($p_id = 0) {
		if(!parent::_checkAuth($p_id)) {
			$this->error('您没有操作此群的权限!');
			die;
		}
	}

	protected function _checkGroupLimitAndDeny() {
		if(!D('User')->checkGroupLimit()) {
			$message = '您创建的群已达上限!';
			if(IS_AJAX) {
				$data = array(
					'info'		=> $message,
					'status'	=> 0,
					'url'		=> '',
					'code'		=> C('ERROR.PERSON_HAVE_GROUP_LIMIT_OUT'),
				);
				$this->ajaxReturn($data);
			} else {
				$this->error($message);
			}
			die;
		}
	}

}
