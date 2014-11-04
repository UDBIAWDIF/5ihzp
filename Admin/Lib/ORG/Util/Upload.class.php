<?php
/**
 +------------------------------------------------------------------------------
 * 文件上传类
 +------------------------------------------------------------------------------
 * @category   Tool
 * @package  Tool
 * @subpackage  Tool
 * @author    huanggy <290205102@qq.com>
 * @version   $Id: Upload.class.php 2568 2012-03-22 09:06:45Z huanggy $
 +------------------------------------------------------------------------------
 */
class Upload {//类定义开始

	
	private $save_path;
	private $umask = 0700;

    /**
     +----------------------------------------------------------
     * 构造函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct($save_path='') {
        if(!empty($save_path)) {
            $this->setSavePath($save_path);
        }
    }

    /**
     +----------------------------------------------------------
     * 上传文件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $src_path  文件本地路径
     +----------------------------------------------------------
	 * @param string $dest_path	文件保存路径
	 +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function upload($src_path, $dest_path) {
		if(!is_file($src_path)) return false;
		if(stripos($dest_path, '/') !== false) {
			$dest_path = str_replace($this->save_path, '', $dest_path);
			mkdirs($this->save_path.substr($dest_path, 0,  strrpos($dest_path, '/')));
		}
		
		if(move_uploaded_file($src_path, $this->save_path.$dest_path)) {
			return $dest_path; 
		} else {
			return false;
		}
    }



    /**
     +----------------------------------------------------------
     * 设置上传文件保存目录
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $save_path 目录
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function setSavePath($save_path) {
		if(empty($save_path)) return false;
		$save_path = rtrim($save_path, "/").'/';
		$this->save_path = $save_path;
		mkdirs($this->save_path);
    }
}