<?php
/**
 * 图片与标签关系模型
 *
 * @author UID
 * @date 2014.07.20
 */
class PicTagRelModel extends BaseModel {

	final public function addByPost($p_picId) {
		$picId		= intval($p_picId);
		$this->where(array('ptr_pic_id' => $picId))->delete();

		$tagInfo	= array();
		foreach($_POST['pictag'] as $picTag) {
			$picTag	= intval($picTag);
			if($picTag) {
				$tagInfo[] = array(
					'ptr_pic_id'	=> $picId,
					'ptr_tag_id'	=> $picTag,
				);
			}
		}

		return $this->addAll($tagInfo);
	}

}
