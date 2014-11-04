<?php

/**
 * 权限分配模型
 *
 * @author Aoki
 * @date 2010年6月17日
 */
class PermissionsModel extends Model {

	public function getGroupMenu($gid) {
		$vo = $this->where("gid = {$gid} and nid = 0")->order('nid asc')->select();
		foreach($vo as &$row){
			$row['permissions']  = unserialize($row['permissions']);
		}
		return $row['permissions'];
	}

	public function getGroupNode($gid) {
		$groupNode = $this->where("gid = {$gid} and nid <> 0 ")->order('nid asc')->select();

		$new = array();
		foreach($groupNode as &$row){
			$row['permissions']  = unserialize($row['permissions']);
			$new[$row['nid']] = $row['permissions'];
		}
		return $new;
	}

}
?>
