<?php
/**
 * 文件操作模型
 *
 * @author UID
 * @date 2012年08月10日
 */
class FileModel extends Model {

	protected $autoCheckFields = false;

	private static $imageClassPath;	//图片类库包路径
	private static $h_upload;	//文件上传类句柄
	private static $ipa_util_loaded = false;	//是否已装载解IPA文件所需的类库文件

//	private $upload_path;

	protected function _initialize() {
		parent::_initialize();
		self::$imageClassPath = C('IMG_CLASS_PATH');
		$commonPath = BASE_PATH . 'Common';
		import('ORG.UploadFile', $commonPath);
		import('ORG.Util.Ftp', $commonPath);
		self::$h_upload = new UploadFile();
	}

	/*
	 * 生成圆角ICON
	 *
	 * @p_src_img  PNG源图(绝对路径)
	 * @p_dst_img  PNG生成新图(绝对路径)
	 * @ext_rename 是否重命名图片的扩展名;要重命名时测试扩展名如果不是 .png 就改扩展名
	 *
	 *
	 */
	public function roundedCorner($p_src_img, $p_dst_img, $ext_rename = true){
		if(!file_exists($p_src_img)) {
			return false;
		}

		$process_result = array(
			'error' => 0,
			'url' => '',
		);

		$p_dst_img = empty($p_dst_img) ? $p_src_img : $p_dst_img;

		$file_part = getFilenamePart($p_dst_img);
		if($ext_rename && 'png' != $file_part['extname']) {
			$p_dst_img = $file_part['dirname'] . '/' . $file_part['filename'] . '.png';
		}

		$img_size = getimagesize($p_src_img);
		$rounder = ceil(min($img_size[0], $img_size[1]) * (97/512));
		import('ORG.ImageRoundedCorner', BASE_PATH . 'Common');
		$image_roundedcorner_obj = new ImageRoundedCorner($p_src_img, $rounder);
		$image_roundedcorner_obj->round_it($p_dst_img);

		$process_result['url'] = $p_dst_img;

		return $process_result;
	}//end roundedCorner()

	/*
	 * 批量获取网站图片,并按照图片作用进行处理
	 *
	 * @a_img_url 图片链接
	 * @img_type  图片各类(icon、截图之类的)
	 * @trigger   获取后执行的操作
	 * @up_to_ftp 是否上传到FTP
	 *
	 * @return array 相对于网站根目录的本地存放路径;如果上传到FTP,返回图片服务器的链接
	 */
	public function getRemoteImgs($a_img_url, $img_type, $trigger = null, $up_to_ftp = false, $proxy = null) {
		$down_result = array(
			'error' => 0,
			'url' => array(),
			'message' => '',
		);

		$img_type = intval($img_type);
		if(false === in_array($img_type, C('IMG_UP_TYPE'))) {	//图片操作动作不存在
			$down_result['error'] = 1;
			$down_result['message'] = '图片操作动作不存在';
			unset($down_result['url']);
			return $down_result;
		}

		if(is_string($a_img_url)) {
			$a_img_url = array($a_img_url);
		}

		set_time_limit(60 * count($a_img_url));
		$file_base_dir = $this->_genUpImgPath($img_type, true);
		$hander = curl_init();
		curl_setopt($hander, CURLOPT_HEADER, 0);
		curl_setopt($hander, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($hander, CURLOPT_TIMEOUT, 60);
		//curl_setopt($hander, CURLOPT_RETURNTRANSFER, true);
		foreach($a_img_url as $img_url) {
			$ext_name = substr(strrchr($img_url, '.'), 1);
			// todo 根据 image_type_to_extension 定扩展名
			if(strlen($ext_name) < 2 || strlen($ext_name) > 4 || empty($ext_name)) $ext_name = 'jpg';
			$save_file = $file_base_dir . md5($img_url) . '.' . $ext_name;
			$fp = fopen($save_file, 'wb');
			curl_setopt($hander, CURLOPT_URL, $img_url);
			curl_setopt($hander, CURLOPT_FILE, $fp);
			curl_setopt($hander, CURLOPT_PROXY, $proxy);
/*
			$is_ssl = false;	//文件名是否是URL路径
			$img_url = trim($img_url);
			foreach(self::$SSL_URL_PREFIX as $v) {
				if(0 == strcasecmp($v, substr($img_url, 0, strlen($v)))) {
					$is_ssl = true;
					break;
				}
			}
			if($is_ssl) {
				curl_setopt($hander, CURLOPT_SSL_VERIFYPEER, false); // 阻止对证书的合法性的检查
				curl_setopt($hander, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
			}
 */
			curl_setopt($hander, CURLOPT_SSL_VERIFYPEER, false); // 阻止对证书的合法性的检查
			curl_setopt($hander, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
			$curl_success = curl_exec($hander);
			fclose($fp);
			if($curl_success) {
				//$save_file = $this->_renameByImgSize($save_file);
				//$base_save_file = substr($save_file, strlen(BASE_PATH));
				$save_file = $this->_imgExtRename($save_file);
				$resize_file = $this->_resizeImg($save_file);
				/* if($resize_file) {
					$down_result['url'][] = substr($resize_file, strlen(BASE_PATH));
				} */
				$save_file = empty($resize_file) ? $save_file : $resize_file;

				$this->_imgTrigger($trigger, $save_file);

				$save_file = substr($save_file, strlen(BASE_PATH));

				if($up_to_ftp) {
					$up_ftp_result = $this->uploadFtpFile($save_file, 'IMG_FTP');
/* 					if($up_ftp_result) {
						$save_file = C('IMAGE_HOST') . C('FTP_IMG_PATH') . '/' . $save_file;
					} */
					//根据需求改成FTP失败就认为抓图失败
					//继续下一个抓图操作
					//成功时也只返回相对路径,而不是URL
					if(!$up_ftp_result['success']) {
						$this->_unlink($save_file);
						continue;
					}
				}

				$down_result['url'][] = $save_file;
			}
		}
		curl_close($hander);

		if(!empty($down_result['url'])) {
			$down_result['error'] = 0;
			unset($down_result['message']);
		} else {
			$down_result['error'] = 1;
			$down_result['message'] = '图片获取失败';
			unset($down_result['url']);
		}
		return $down_result;
	}

	public function upload_img($img_type, $checksize = false, $up_to_ftp = false, $trigger = null) {
		$up_result = array(
			'error' => 0,
			'url' => '',
			'message' => '',
		);

		$img_type = intval($img_type);
		if(false === in_array($img_type, C('IMG_UP_TYPE'))) {	//图片操作动作不存在
			$up_result['error'] = 1;
			$up_result['message'] = '图片操作动作不存在';
			return $up_result;
		}

		$this->_setUpImgConfig($img_type);
		if (!self::$h_upload->upload()) {	//上传失败
			$up_result['error'] = 1;
			$up_result['message'] = self::$h_upload->getErrorMsg();
		} else {
			$upload_file_info = self::$h_upload->getUploadFileInfo();
			$file_full_path = $upload_file_info[0]['savepath'] . $upload_file_info[0]['savename'];
			// $file_full_path = realpath($file_full_path);
			$file_full_path = $this->_imgExtRename($file_full_path);
			$up_result['error'] = 0;
			$up_result['url'] = substr($file_full_path, strlen(UPLOAD_MAIN_DIR));
			$img_size = getimagesize($file_full_path);

			if(self::$h_upload->maxWidth && self::$h_upload->maxWidth < $img_size[0]){
				$up_result['error'] = 1;
				$up_result['message'] = '图片宽度不要超过'.self::$h_upload->maxWidth.'px！';
			}else if($checksize) {
				$check_result = $this->imgSizeIsAllow($file_full_path, true);
				//检测结果改成状态码
				switch($check_result) {
					case C('IMG_SIZE_CHECKED_STATUS.NO_ALLOW') :
						$up_result['error'] = 1;
						$up_result['message'] = '不允许上传此尺寸的图片，请修改后再试！';
						break;
					case C('IMG_SIZE_CHECKED_STATUS.CHANGE') :
						$resize_file = $this->_resizeImg($file_full_path);
						if($resize_file) {
							$up_result['url'] = substr($resize_file, strlen(UPLOAD_MAIN_DIR));
						}
						break;
					case C('IMG_SIZE_CHECKED_STATUS.NO_CHANGE') :
						;
						break;
				}
			}

			if(self::$h_upload->thumbMaxSize && filesize($file_full_path)>self::$h_upload->thumbMaxSize){
				import('ORG.Image', BASE_PATH . 'Common');
				$resize_file = str_ireplace('.png', '.jpg', $file_full_path);
				Image::thumb($file_full_path, $resize_file, '', $img_size[0], $img_size[1]);
				$up_result['url'] = substr($resize_file, strlen(UPLOAD_MAIN_DIR));
			}

			if(empty($up_result['error'])) {
				$process_result = $this->_imgTrigger($trigger, UPLOAD_DIR . $up_result['url']);
				if(empty($process_result['error']) && !empty($process_result['url'])) {
					$up_result['url'] = substr($process_result['url'], strlen(UPLOAD_MAIN_DIR));
				}
			}

			if($up_to_ftp && empty($up_result['error'])) {
				$up_ftp_result = $this->uploadFtpFile($up_result['url'], 'IMG_FTP');
				if(!$up_ftp_result['success']) {
					$up_result['error'] = 1;
					$up_result['message'] = "上传到FTP失败！原因:\r\n{$up_ftp_result['message']}";
					$this->_unlink($up_result['url']);
				}
			}
		}

		return $up_result;
	}

	/* 判断图片的尺寸是否在允许列表里
	 *
	 * 在最终尺寸的列表(Key列表)里时,图片不需要处理,直接允许上传;
	 * 在需要改变尺寸的列表(Value列表)里时,
	 *
	 */
	public function imgSizeIsAllow($p_img, $unlink = true) {
		$img_size = getimagesize($p_img);
		$img_size = $img_size[0] . '_' . $img_size[1];

		$allow_and_change_size = S('IMG_UP_ALLOW_CHANGE_SIZE');	//允许上传并且需要改变大小的尺寸
		$allow_and_no_change_size = S('IMG_UP_ALLOW_NO_CHANGE_SIZE');	//允许上传并且不要改变大小的尺寸
		if(empty($allow_and_change_size)) {
			$allow_and_change_size = array();
			$allow_and_no_change_size = array();
			foreach(C('IMG_RESIZE_MAP') as $k => $v) {
				$allow_and_change_size = array_merge($allow_and_change_size, $v);
				$allow_and_no_change_size[] = $k;
			}
			S('IMG_UP_ALLOW_CHANGE_SIZE', $allow_and_change_size);
			S('IMG_UP_ALLOW_NO_CHANGE_SIZE', $allow_and_no_change_size);
		}

		// $is_allow = (false === in_array($img_size, $allow_and_change_size)) ? false : C('IMG_SIZE_CHECKED_STATUS.CHANGE');
		// $is_allow = (false === in_array($img_size, $allow_and_no_change_size)) ? false : C('IMG_SIZE_CHECKED_STATUS.NO_CHANGE');
		if(false !== in_array($img_size, $allow_and_change_size)) {
			$is_allow = C('IMG_SIZE_CHECKED_STATUS.CHANGE');
		} elseif(false !== in_array($img_size, $allow_and_no_change_size)) {
			$is_allow = C('IMG_SIZE_CHECKED_STATUS.NO_CHANGE');
		} else {
			$is_allow = C('IMG_SIZE_CHECKED_STATUS.NO_ALLOW');
		}

		if(($is_allow === C('IMG_SIZE_CHECKED_STATUS.NO_ALLOW')) && $unlink) {
			unlink($p_img);
		}

		return $is_allow;
	}//end imgSizeIsAllow()

	/*
	 * 上传安装文件
	 *
	 *
	 */
	public function upload_file($file_type, $up_to_ftp = false) {
		$up_result = array(
			'error' => 0,
			'url' => '',
			'message' => '',
		);

		$file_type = intval($file_type);
		if(false === in_array($file_type, C('FILE_UP_TYPE'))) {	//文件操作动作不存在
			$up_result['error'] = 1;
			$up_result['message'] = '文件操作动作不存在';
			return $up_result;
		}
		$this->_setUpFileConfig($file_type);

		$upload_file_info = null;
		if (self::$h_upload->upload()) {	//上传成功
			$upload_file_info = self::$h_upload->getUploadFileInfo();
			$up_result['error'] = 0;
			$up_result['url'] = substr($upload_file_info[0]['savepath'], strlen(BASE_PATH)) . $upload_file_info[0]['savename'];

			if($up_to_ftp) {
				$up_ftp_result = $this->uploadFtpFile($up_result['url'], 'SOFT_FTP');
/* 				if($up_ftp_result) {
					$up_result['url'] = C('SOFT_HOST') . C('FTP_SOFT_PATH') . '/' . $up_result['url'];
				} */
				//根据需求改成FTP失败就认为上传失败
				//继续下一个上传操作
				//成功时也只返回相对路径,而不是URL
				if(!$up_ftp_result['success']) {
					$up_result['error'] = 1;
					$up_result['message'] = "上传到FTP失败！原因:\r\n{$up_ftp_result['message']}";
					// $this->_unlink($up_result['url']);
				}
			}
		} else {
			$up_result['error'] = 1;
			$up_result['message'] = self::$h_upload->getErrorMsg();
		}

		return $up_result;
	}//end upload_file()

	/*
	 * 上传本地文件到FTP上
	 * @p_file    相对于网站根目录的本地文件
	 * @$p_config FTP配置文件,直接用 C($p_config) 装载
	 *
	 * @return bool 只要有一个FTP上传成功,就返回 true
	 */
	public function uploadFtpFile($p_file, $p_config) {
		$up_result = array(
			'success' => false,
			'message' => '',
		);

		$file = BASE_PATH . $p_file;
		foreach(C($p_config) as $v) {
			$ftp = new FTP(
				$v['FTP_HOST'],
				$v['FTP_PORT'],
				$v['FTP_USER'],
				$v['FTP_PASS']
			);
			if($ftp->up_file($file, $p_file)) {
				$up_result['success'] = true;
			} else {
				$up_result['message'] = $ftp->errMsg;
			}
			$ftp->close();
		}

		return $up_result;
	}

	/*
	 * 返回存放文件的路径
	 *
	 */
	public function getFilePath($file_type) {
		return C('FILE_UP_PATH') . C('FILE_UP_SUB_DIR') . '/' . C('FILE_UPLOAD_CONFIG_' . $file_type . '.savePath') . '/';
	}//end getFilePath()

	/*
	 * 返回存放图片的路径
	 *
	 */
	public function getImgPath($img_type) {
		return C('IMG_UP_SUB_DIR') . '/' . C('IMG_UPLOAD_CONFIG_' . $img_type . '.savePath') . '/';
	}

	/*
	 * 移动不需要的文件到垃圾目录
	 *
	 */
	public function moveTrash($p_file) {
		return;
	}//end moveTrash()

	/*
	 *	根据上传的类型设置上传类的配置
	 *
	 */
	private function _setUpImgConfig($img_type) {
		$img_config = C('IMG_UPLOAD_CONFIG_' . $img_type);

		self::$h_upload->maxSize = $img_config['maxSize'];
		self::$h_upload->allowExts = $img_config['allowExts'];

		//保存相对于网站主目录的目录路径,子目录名根据当前时间来确定
		self::$h_upload->savePath = $this->_genUpImgPath($img_type);

		self::$h_upload->thumb = $img_config['thumb'];
		self::$h_upload->thumbPrefix = $img_config['thumbPrefix'];
		self::$h_upload->thumbMaxWidth = $img_config['thumbMaxWidth'];
		self::$h_upload->thumbMaxHeight = $img_config['thumbMaxHeight'];
		self::$h_upload->saveRule = $img_config['saveRule'];
		self::$h_upload->thumbRemoveOrigin = $img_config['thumbRemoveOrigin'];
		self::$h_upload->imageClassPath = self::$imageClassPath;
		self::$h_upload->maxWidth = $img_config['maxWidth'];
		self::$h_upload->thumbMaxSize = $img_config['thumbMaxSize'];

		return;
	}//end _setUpImgConfig()

	private function _genUpImgPath($img_type, $mkdir = false) {
		$date_format = date('YmdH', $_SERVER['REQUEST_TIME']);
		$year = substr($date_format, 0, 4) . '/';
		$month = substr($date_format, 4, 2) . '/';
		$day = substr($date_format, 6, 2) . '/';
		$hour = substr($date_format, 8) . '/';
		$dir = UPLOAD_DIR . $this->getImgPath($img_type) . $year . $month . $day . $hour;
		if($mkdir) mk_dir($dir);
		return $dir;
	}//end _genUpImgPath()

	/*
	 *	根据上传的类型设置上传类的配置
	 *
	 */
	private function _setUpFileConfig($file_type) {
		$file_config = C('FILE_UPLOAD_CONFIG_' . $file_type);

		self::$h_upload->maxSize = $file_config['maxSize'];
		self::$h_upload->allowExts = $file_config['allowExts'];

		//保存相对于网站主目录的目录路径,子目录名根据当前时间来确定
		self::$h_upload->savePath = $this->_genUpFilePath($file_type);
		self::$h_upload->hashType = $file_config['hashType'];
		self::$h_upload->saveRule = $file_config['saveRule'];
		self::$h_upload->uploadReplace = $file_config['uploadReplace'];

		return;
	}//end _setUpFileConfig()

	private function _genUpFilePath($file_type, $mkdir = false) {
		$date_format = date('YmdH', $_SERVER['REQUEST_TIME']);
		$year = substr($date_format, 0, 4) . '/';
		$month = substr($date_format, 4, 2) . '/';
		$day = substr($date_format, 6, 2) . '/';
		$hour = substr($date_format, 8) . '/';
		$dir = BASE_PATH . $this->getFilePath($file_type) . $year . $month . $day . $hour;
		if($mkdir) mk_dir($dir);
		return $dir;
	}//end _genUpFilePath()

	/*
	 * 根据图片尺寸生成相应的小图
	 *
	 * @return string 新文件名: 原文件名_宽_高.扩展名
	 */
	private function _resizeImg($p_img) {
		$img_info = getimagesize($p_img);
		$size_str = $img_info[0] . '_' . $img_info[1];
		$resize_to = '';
		foreach(C('IMG_RESIZE_MAP') as $k => $v) {
			if(false !== in_array($size_str, $v)) {
				if($size_str == $k) {	//相同尺寸就不需要转换,只需要改名
					break;
				} else {
					$resize_to = $k;
					break;
				}
			}
		}

		//标志: 是否生成新尺寸的图片;不用生成新图片时,只需要把原文件改名就行了
		$need_resize = true;
		if(empty($resize_to)) {
			$need_resize = false;
			$resize_to = $size_str;
		}

		$new_size = explode('_', $resize_to);
		$file_part = getFilenamePart($p_img);
		$new_name = $file_part['dirname'] . '/' . $file_part['filename'] . '_' . $resize_to . '.' . $file_part['extname'];

		if($need_resize) {
			// resizeImg($p_img, $new_name, $new_size[0], $new_size[1]);
			import('ORG.Image', BASE_PATH . 'Common');
			Image::thumb($p_img, $new_name, '', $new_size[0], $new_size[1], true, false);
		} else {
			rename($p_img, $new_name);
		}
		return $new_name;
	}//end _resizeImg()

	/*
	 * 根据图片尺寸改变文件名
	 *
	 * @return string 新文件名: 原文件名_宽_高.扩展名
	 */
	private function _renameByImgSize($p_img) {
		$img_size = getimagesize($p_img);
		$ext_name = substr(strrchr($p_img, '.'), 1);
		$name = substr($p_img, 0, -(strlen($ext_name) +1));
		$new_name = $name . '_' . $img_size[0] . '_' . $img_size[1] . '.' . $ext_name;
		rename($p_img, $new_name);
// $rename_success = rename($p_img, $new_name);
// echo $rename_success ? 'Success!' : "Failed!<br />$p_img<br />$new_name";
		return $new_name;
	}//end _renameByImgSize()

	// 根据图片的信息重命名扩展名
	private function _imgExtRename($p_img) {
		$known_replacements = array(
			'jpeg' => '.jpg',
			'tiff' => '.tif',
		);

		$img_info = getimagesize($p_img);
		if(false == $img_info) {
			return false;
		}
		$ext_name = image_type_to_extension($img_info[2]);
		$ext_name_no_dot = substr($ext_name, 1);
		if(array_key_exists($ext_name_no_dot, $known_replacements)) {
			$ext_name = $known_replacements[$ext_name_no_dot];
		}

		$file_part = getFilenamePart($p_img);
		$new_name = $file_part['dirname'] . '/' . $file_part['filename'] . $ext_name;

		$rename_success = rename($p_img, $new_name);
		return $rename_success ? $new_name : $p_img;
	}//end _imgExtRename()

	/*
	 * 调用图片的处理方法
	 *
	 * @func 方法名
	 * @img  图片全路径
	 *
	 * @return
	 */
	private function _imgTrigger($func, $img) {
		if($func && method_exists($this, $func)) {
			return $this->$func($img);
		}

		return array('error'=>1, 'message'=>'请求的方法不存在！');
	}//end _imgTrigger()

	/*
	 * 删除指定文件
	 *
	 * @p_file      要删除的文件
	 * @is_ab_path  文件路径是否绝对路径;相对路径是从网站根目录开始
	 *
	 * @return
	 */
	private function _unlink($p_file, $is_ab_path = false) {
		if(!$is_ab_path) {
			$p_file . BASE_PATH . $p_file;
		}

		//@unlink($p_file);

		return;
	}//end _unlink()

}
?>
