<?php
/**
 * 照片月份模型
 *
 * @author UID
 * @date 2014.08.04
 */
class PicMonthModel extends BaseModel {

	protected $_auto = array(
		array('pm_user_id', 'getUid', 1, 'function'),
		array('pm_date', 'getCurMonth', 1, 'callback'),
	);

	protected $_validate = array(
		array(
			'pm_user_id,pm_date', 'require',
			'本月份已经存在', 1,
			'unique', 3
		),
	);

	public function getCurMonth() {
		return substr(datetime('d'), 0, -2) . '00';
	}

	public function countByUser($p_uid) {
		$uid = intval($p_uid);
		return $this
				->where(array('pm_user_id' => $uid))
				->count();
	}

	public function getFirstByUser($p_uid) {
		$uid = intval($p_uid);
		return $this
				->where(array('pm_user_id' => $uid))
				->order('pm_date DESC')
				// ->first();
				->find();
	}

	public function queryAllByUser($p_uid) {
		$uid = intval($p_uid);
		return $this
				->where(array('pm_user_id' => $uid))
				->field('pm_date')
				->order('pm_date DESC')
				->select();
	}

	public function checkMonthFormat($p_month) {
		$month = trim($p_month);
		return (bool)preg_match('/^\d{4}-\d{2}$/', $month);
	}

}
