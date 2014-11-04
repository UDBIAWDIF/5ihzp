<?php
/**
 * 地区模型
 *
 * @author UID
 * @date 2014.08.03
 */
class RegionModel extends BaseModel {

	final public function getIdByName($p_name) {
		$name = trim($p_name);
		return $this
				->where('region_name like "' . $name . '%"')
				->getField($this->getPk());
	}

	function getRegionParentId($p_id) {
		return (int)$this
					->where(array(
						$this->getPk() => intval($p_id),
					))
					->getField('parent_id');
	}

}
