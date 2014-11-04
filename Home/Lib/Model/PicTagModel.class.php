<?php
/**
 * 图片标签模型
 *
 * @author UID
 * @date 2014.07.20
 */
class PicTagModel extends BaseModel {

	final public function queryList($p_count) {
		$count = intval($p_count);
		$this->where('pt_status = 1');
		$this->order('pt_order DESC');
		$this->field($this->getPk() . ',pt_name');
		!empty($count) && $this->limit($count);
		return $this->select();
	}

}
