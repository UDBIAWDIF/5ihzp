<?php
/**
 * 图标模型
 *
 * @author UID
 * @date 2014.07.27
 */
class IconModel extends BaseModel {

	const ICON_USER_HEAD	= 'userhead';
	const ICON_GROUP_ICON	= 'groupicon';

	// protected $_validate = array();

	/* protected $_auto = array(
		array('icon_rel_id', 'getUid', 1, 'function'),
		array('icon_rel_type', 'trim', 1, 'function'),
		array('icon_path', 'trim', 1, 'callback'),
		array('icon_width', 'getIconWidth', 1, 'callback'),		// 传 icon_path 项到此
		array('icon_height', 'getIconHeight', 1, 'callback'),	// 传 icon_path 项到此
		array('icon_size', 'getIconSize', 1, 'callback'),	// 传 icon_path 项到此
		array('icon_createtime', NOW_TIME, 1),
		array('icon_status', 1),
	); */

	public function addUserHead($p_iconPath, $userId = 0) {
		return $this->_addIconByType($p_iconPath, self::ICON_USER_HEAD, $userId);
	}

	public function addGroupIcon($p_iconPath, $groupId = 0) {
		return $this->_addIconByType($p_iconPath, self::ICON_GROUP_ICON, $groupId);
	}

	private function _addIconByType($p_iconPath, $type, $relId = 0) {
		//list($width, $height, $type, $attr) = getimagesize($srcFile);
		$iconPath		= trim($p_iconPath);
		$iconFullPath	= UPLOAD_MAIN_DIR . $iconPath;
		$iconSize		= getimagesize($iconFullPath);
		$iconInfo		= array(
			'icon_rel_id'		=> intval($relId),
			'icon_rel_type'		=> trim($type),
			'icon_path'			=> $iconPath,
			'icon_width'		=> $iconSize[0],
			'icon_height'		=> $iconSize[1],
			'icon_size'			=> filesize($iconFullPath),
			'icon_createtime'	=> NOW_TIME,
			'icon_status'		=> 1,
		);

		// TODO: 检查图片是否已经存在于数据库中
		return $this->add($iconInfo);
	}

}
