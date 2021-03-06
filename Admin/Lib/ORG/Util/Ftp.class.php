<?php
/********************************************
* MODULE:FTP类
*******************************************/
class FTP {
	public $off;                          // 返回操作状态(成功/失败)
	public $conn_id;                      // FTP连接
	//public $debug = false;
	public $errMsg = '';
	//public $err = false;

	/**
	* 方法：FTP连接
	* @FTP_HOST -- FTP主机
	* @FTP_PORT -- 端口
	* @FTP_USER -- 用户名
	* @FTP_PASS -- 密码
	*/
	public function __construct($FTP_HOST = null, $FTP_PORT = null, $FTP_USER = null, $FTP_PASS = null, $FTP_PASV = true) {
		$this->off = false;
		$FTP['host'] = $FTP_HOST ? $FTP_HOST : C('FTP_HOST');
		$FTP['port'] = $FTP_PORT ? $FTP_PORT : C('FTP_PORT');
		$FTP['user'] = $FTP_USER ? $FTP_USER : C('FTP_USER');
		$FTP['pass'] = $FTP_PASS ? $FTP_PASS : C('FTP_PASS');
		$err = false;
		$this->conn_id = @ftp_connect($FTP['host'],$FTP['port']);
		if($this->conn_id) {
			$err = !@ftp_login($this->conn_id,$FTP['user'],$FTP['pass']);
		} else {
			$this->errMsg = 'FTP服务器无法连接';
			return false;
		}
		if(!$err) {
			if($FTP_PASV) {
				@ftp_pasv($this->conn_id,1); // 打开被动模拟
			}
			$this->off = true;
			// return true;
		} else {
			$this->errMsg = 'FTP服务器无法登录';
			return false;
		}
	}

	/**
	* 方法：上传文件
	* @path    -- 本地路径
	* @newpath -- 上传路径
	* @type    -- 若目标目录不存在则新建
	*/
	function up_file($path, $newpath, $type=true) {
		if($type) $this->dir_mkdirs($newpath);
		$this->off = @ftp_put($this->conn_id, $newpath, $path, FTP_BINARY);

$ftpCfg = C('SOFT_FTP');
$remoteFile = C('SOFT_HOST') . C('FTP_SOFT_PATH') . '/' . trim($newpath);
error_log(date('Y-m-d H:i:s:')
		. var_export($this->off, true)
		. '-' . print_r($path, true)
		. '- '. print_r($remoteFile, true)
		. PHP_EOL,
	3,
	'./FUCK.LOG'
);

error_log('local size:' . filesize($path) . PHP_EOL
		. 'ftp size:' . ftp_size($this->conn_id, $newpath) . PHP_EOL
		. 'remote size:' . getHttpSize($remoteFile) . PHP_EOL
		. '------------------------------' . PHP_EOL,
	3,
	'./FUCK.LOG'
);

		$is_success = false;
		if($this->off) {
			$ftp_size = ftp_size($this->conn_id, $newpath);
			$loc_size = filesize($path);
			if(!empty($ftp_size) && (-1 !== $ftp_size) && ($ftp_size == $loc_size)) {
				$is_success = true;
			} else {
				$this->errMsg = '文件上传失败, FTP上的文件大小不对！';
			}
		} else {
			$this->errMsg = '文件上传失败,请检查权限及路径是否正确！';
		}

		return $is_success;
	}

	/**
	* 方法：移动文件
	* @path    -- 原路径
	* @newpath -- 新路径
	* @type    -- 若目标目录不存在则新建
	*/
	function move_file($path,$newpath,$type=true) {
		if($type) $this->dir_mkdirs($newpath);
		$this->off = @ftp_rename($this->conn_id,$path,$newpath);
		if(!$this->off) $this->errMsg = "文件移动失败,请检查权限及原路径是否正确！";
		return $this->off;
	}

	/**
	* 方法：复制文件
	* 说明：由于FTP无复制命令,本方法变通操作为：下载后再上传到新的路径
	* @path    -- 原路径
	* @newpath -- 新路径
	* @type    -- 若目标目录不存在则新建
	*/
	function copy_file($path,$newpath,$type=true) {
		$downpath = "c:/tmp.dat";
		$this->off = @ftp_get($this->conn_id,$downpath,$path,FTP_BINARY);// 下载
		if(!$this->off) $this->errMsg = '文件复制失败,请检查权限及原路径是否正确！';
		$this->up_file($downpath,$newpath,$type);
		return $this->off;
	}

	/**
	* 方法：删除文件
	* @path -- 路径
	*/
	function del_file($path) {
		if(!is_array($path)){
			$path = array('0'=>$path);
		}
		for($i=0;$i<count($path);$i++){
			$this->off = @ftp_delete($this->conn_id,trim($path[$i]));
			if(!$this->off) $this->errMsg = $path[$i].'文件删除失败,请检查权限及路径是否正确！';
		}
		return $this->off;
	}

	/**
	* 方法：生成目录
	* @path -- 路径
	*/
	function dir_mkdirs($path) {
		$path_arr  = explode('/',$path);              // 取目录数组
		$file_name = array_pop($path_arr);            // 弹出文件名
		$path_div  = count($path_arr);                // 取层数

		foreach($path_arr as $val) {                  // 创建目录
			if(@ftp_chdir($this->conn_id,$val) == FALSE) {
				$tmp = @ftp_mkdir($this->conn_id,$val);
				if($tmp == FALSE) {
					$this->errMsg = '目录创建失败,请检查权限及路径是否正确！';
					return false;
				}
				@ftp_chdir($this->conn_id,$val);
			}
		}

		for($i=1;$i<=$path_div;$i++) {                // 回退到根
			@ftp_cdup($this->conn_id);
		}

		return true;
	}

	/**
	* 方法：关闭FTP连接
	*/
	function close() {
		@ftp_close($this->conn_id);
	}

}
// class class_ftp end

/************************************** 测试 ***********************************/
//$ftp = new ftp('idcshk1.18490.com',21,'cccxxx','X280e60e3c2f6');          // 打开FTP连接

//$ftp->up_file('./cover.jpg','web/test/13548957217/aa.jpg');         // 上传文件
//$ftp->move_file('aaa/aaa.php','aaa.php');                // 移动文件
//$ftp->copy_file('aaa.php','aaa/aaa.php');                // 复制文件
//$ftp->del_file('aaa.php');                               // 删除文件
//$ftp->close();                                             // 关闭FTP连接
/******************************************************************************/