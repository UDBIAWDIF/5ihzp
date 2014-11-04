<?php

/**
 * 上传附件模块
 *
 * @author UID 2012.09.03
 */
class UpAttachAction extends GlobalAction {

	public function upKeditor() {
		// $this->_json_response($this->_upFile(C('FILE_UP_TYPE.KE_ATTACH'), true));
		$up_result = $this->_upImg(C('IMG_UP_TYPE.KE_IMG'), false, true);
		if(empty($up_result['error'])) {
			$up_result['url'] = C('IMAGE_HOST') . C('FTP_IMG_PATH') . '/' . $up_result['url'];
		}
		$this->_json_response($up_result);
		return;
	}//end upKeditor()

}

?>
