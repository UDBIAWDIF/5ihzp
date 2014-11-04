<?php
/**
 * 群标签模型
 *
 * @author UID
 * @date 2014.08.17
 */
class GroupTagModel extends BaseModel {

	final public function queryList($p_count) {
		$count = intval($p_count);
		$this->where('gt_status = 1');
		$this->order('gt_order DESC');
		$this->field($this->getPk() . ',gt_name');
		!empty($count) && $this->limit($count);
		return $this->select();
	}

}
