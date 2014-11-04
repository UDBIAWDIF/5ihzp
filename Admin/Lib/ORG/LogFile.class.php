<?php
class LogFile extends Think{
	public function traverse($dir = '', $filetype = 'file', $exts = false, $recursive = false){
		static $fileArray = array();
		if (!is_dir($dir) || !is_readable($dir)) {
			die($dir.'目录无法打开或者权限错误！');
		}
		$dirhandle = opendir($dir) or die("fail to open the dir");
		while(($file = readdir($dirhandle)) !== false){
			if(in_array($file,array('..','.')))continue;
			$fullName = $dir.'/'.$file;
			if(is_file($fullName) && $filetype != 'dir'){
				if(!$exts){
					$fileArray[] = $fullName;
				}else{
					$fileInfo = pathinfo($fullName);
					if(in_array($fileInfo['extension'],$exts)){
						$fileArray[] = $fullName;
					}
				}
			}elseif(is_dir($fullName)){
				if($filetype != 'file'){
					$fileArray[] = $fullName;
				}
				if($recursive){
					self::traverse($fullName,$filetype,$exts,$recursive);
				}
			}
		}
		closedir($dirhandle);
		return $fileArray;
	}
	
	public function open($path = ''){
		$handle = fopen($path,"rb");
		if ($handle == NULL){
			exit($path.'无法打开！');
		}
		return $handle;
	}
	
	public function read($handle = '', $start = 0, $rows = 100){
		@fseek($handle, $start, SEEK_SET);		
		for($i = 0; $i < $rows; $i++){
			$fgetsRow = str_replace("\n","",fgets($handle));
			if(!empty($fgetsRow))$buf[] = $fgetsRow;
		}
		return $buf;
	}
	
	public function offset($handle=''){		
		return ftell($handle);
	}
	
	public function isEnd($handle=''){
		return feof($handle);
	}
	
	public function close($handle=''){
		fclose($handle);
	}
}