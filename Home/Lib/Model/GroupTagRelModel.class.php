<?php
/**
 * 群与标签关系模型
 *
 * @author UID
 * @date 2014.08.25
 */
class GroupTagRelModel extends BaseModel {

	final public function addByPost($p_gpId) {
		$gpId		= intval($p_gpId);
		$this->where(array('gtr_group_id' => $gpId))->delete();
		$tagInfo	= array();
		foreach($_POST['gptag'] as $gpTag) {
			$gpTag	= intval($gpTag);
			if($gpTag) {
				$tagInfo[] = array(
					'gtr_group_id'	=> $gpId,
					'gtr_tag_id'	=> $gpTag,
				);
			}
		}

		return $this->addAll($tagInfo);
	}

	final public function queryTagIdListByGroup($p_gpId, $toString = false) {
		$gpId	= intval($p_gpId);
		$gpTags	= $this
					->where(array('gtr_group_id' => $gpId))
					->field('gtr_tag_id')
					->select();

		if($toString && !empty($gpTags)) {
			$gtTagsToString = ',';
			foreach($gpTags as $gpTag) {
				$gtTagsToString .= $gpTag['gtr_tag_id'] . ',';
			}
			$gpTags = $gtTagsToString;
		}

		return $gpTags;
	}

}
