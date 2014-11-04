<?php

include BASE_PATH . 'Common/Common/functions.php';

if (!defined('CAL_GREGORIAN'))
	define('CAL_GREGORIAN',1);

//输出节点等级的名
function echoNodeLevel($level) {
	$name = C('NODELEVEL');
	return $name[$level];
}

function getCurrentUserId() {
	$user = session('user_info');
	return intval($user['u_id']);
}

function getCurrentUserName() {
	$user = session('user_info');
	return trim($user['u_name']);
}

function getCurrentUserHead() {
	$user = session('user_info');
	$iconId = intval($user['u_icon_id']);
	return getIconUrl($iconId);
}

function userCountJoinGroup() {
	return D('Group')->countJoinGroup();
}

function userCountGroup() {
	return D('Group')->countGroup();
}

function userSumGroupFriend() {
	return D('Group')->sumGroupFriend();
}

function isInGroup($p_gpId, $p_uid = 0) {
	$isIn = false;
	$runnable = true;

	$gpId = intval($p_gpId);
	empty($gpId) && $runnable = false;

	$uid = intval($p_uid);
	if($runnable) {
		empty($uid) && $uid = getUid();
		empty($uid) && $runnable = false;
	}

	if($runnable) {
		$isIn = D('Group')->userIdIsIn($gpId, $uid);
	}

	return $isIn;
}

function getFileSuffix($filename) {
	return substr($filename,strripos(strtolower($filename),'.')+1);
}

function orderAuth($oid) {
	$mOrder = D('Order');
	return $mOrder->orderAuth($oid);
}//end orderAuth()

function diftime($time) {
	$ctime = time();
	$y = intval(date('Y',$time));
	$cy = intval(date('Y',$ctime));
	$w = intval(date('W',$time));
	$cw = intval(date('W',$ctime));
	$m = intval(date('m',$time));
	$cm = intval(date('m',$ctime));
	$d = intval(date('d',$time));
	$cd = intval(date('d',$ctime));
	$dif = $ctime - $time;
	switch($dif){
		case $dif <0:
			if($dif > -172800){
				if($dif > -86400){
					$title ='明天';
				}else{
					$title ='后天';
				}
				$day = intval(abs($dif)/(86400))+1;
				$type =1;
				$msg = '剩余'.$day.'天结束';
			}
			break;
		case $dif <3600://小于一个小时
			if($dif <60){
				$type =1;
				$title ='今天';
				$msg = intval($dif).'秒之前';
			}else{
			$type =1;
			$title ='今天';
			$msg = intval($dif/60).'分钟之前';
			}
			break;
		case $dif < 86400://小于一天
			$type =1;
			$title ='今天';
			$msg = intval($dif/3600).'小时之前';
			break;
		case $dif < 604800://小于一周
			if($w == $cw){
				$type =2;
				$title ='本周';
				if($y == $cy){
					$msg = date('m-d',$time);
				}else{
					$msg = date('Y-m-d',$time);
				}
			}else{
				$type =3;
				$title ='上周';
				if($y == $cy){
					$msg = date('m-d',$time);
				}else{
					$msg = date('Y-m-d',$time);
				}
			}
			break;
		case $dif < 2419200://小于一个月
			if($m == $cm){
				$type =4;
				$title ='本月';
				if($y == $cy){
					$msg = date('m-d',$time);
				}else{
					$msg = date('Y-m-d',$time);
				}
			}else{
				$type =5;
				$title = '更早';
				if($y == $cy){
					$msg = date('m-d',$time);
				}else{
					$msg = date('Y-m-d',$time);
				}
			}
			break;
		default:
			$type =5;
			$title = '更早';
			if($y == $cy){
				$msg = date('m-d',$time);
			}else{
				$msg = date('Y-m-d',$time);
			}
		break;
	}
	return array(
		'type'=>$type,
		'title'=>$title,
		'msg'=>$msg
	);
}

function checkExpired($time) {
	$ctime = time();
	$y = intval(date('Y',$time));
	$cy = intval(date('Y',$ctime));
	$w = intval(date('W',$time));
	$cw = intval(date('W',$ctime));
	$m = intval(date('m',$time));
	$cm = intval(date('m',$ctime));
	$d = intval(date('d',$time));
	$cd = intval(date('d',$ctime));
	$dif = $ctime - $time;
	switch($dif){
		case $dif <0:
			if($dif > -172800){
				if($dif > -86400){
					$title ='明天';
				}else{
					$title ='后天';
				}
				$day = intval(abs($dif)/(86400))+1;
				$type =1;
				$msg = '剩余'.$day.'天结束';
			}
			break;
		default:
			$type =1;
			$title = '过期';
			$day = intval(abs($dif)/(86400));
			$msg = $title.$day.'天';
		break;
		}
	return array(
		'type'=>$type,
		'title'=>$title,
		'msg'=>$msg
	);
}

function getCurrentDate(){
	return date('Y-m-d H:i:s',time());
}

function getUserNameById($id) {
	static $user_list;
	if($id == 0){
		return '';
	}

	if(!$user_list[$id]){
		$model = M('Users');

		if(empty($id)){
			$id = getCurrentUserId();
		}
		$user = $model->field('userName')->find($id);
		$user_list[$id] = $user['userName'];
	}
	return $user_list[$id];
}
function cutExpressName($field,$value) {
	$arr= split(',',$value);
	if($arr) return $arr[$field];
	return $value;
}
function valueUnPack($value) {
	$value = unserialize($value);
	foreach($value as $key=>$value){
		$arr[] = $key.':'.$value;
	}
	return implode(chr(13),$arr);
}
function getCurrencySymbol($key) {
	if(S('clist')){
		$clist = S('clist');
	}else{
		$currencies = M('Currencies');
		$clist = $currencies->field('id,symbol_left,code')->findAll();
		S('clist',$clist,3600);
	}
	foreach($clist as $row){
		if($row['id']==$key){
			return $row['symbol_left'];
		}
	}
	return null;
}
function checkTimeout($time) {
	$time = strtotime($time);
	$now = time();

	if(($now-$time)>172800){
		return false;
	}else{
		return true;
	}
}

function checkDeliveryTime($time) {
	$time = strtotime($time);
	$now = time();
	$tz = date('z',$time);
	$nz = date('z',$now);

	if($tz == $nz){
		return '今天';
	}else{
		if($nz>$tz && ($nz-$tz) == 1){
			return '昨天';
		}
		return '';
	}

}

function toggle($id,$field,$value) {
	if($value==1){
		$html = '<a href="'.__URL__.'/exec/act/toggle/id/'.$id.'/field/'.$field.'/value/0"><img src="../Public/images/icon/icon_green_on.gif" alt="Status - Enabled" title=" Status - Enabled " border="0" /></a>';
	}else{
		$html = '<a href="'.__URL__.'/exec/act/toggle/id/'.$id.'/field/'.$field.'/value/1"><img src="../Public/images/icon/icon_red_on.gif" alt="Status - Disabled" title=" Status - Disabled " border="0" /></a>';
	}
	echo $html;
}

function getProductCount($id) {
	$list = S('productCount');
	if(!$list){
		$Categories = D("Categories");
		$list = $Categories->getProductCount();
		S('productCount',$list,3600);
	}
	return $list[$id];
}


function getDatetime(){
	return date('Y-m-d H:i:s',time());
}

function getUid(){
	return getCurrentUserId();
}

function getUname($p_uid = 0) {
	$uid = intval($p_uid);
	if(empty($uid)) {
		return getCurrentUserName();
	} else {
		return D('User')->getUname($uid);
	}
}

function getUser($p_uid) {
	$uid = intval($p_uid);
	empty($uid) && $uid = getUid();
	return M('User')->find($uid);
}

function array_require( $array ){
	if($array){
		return true;
	}else{
		return false;
	}
}

function getStatus($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '禁用';
			$showImg = '<IMG SRC="__PUBLIC__/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<IMG SRC="__PUBLIC__/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<IMG SRC="__PUBLIC__/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			break;
		case 1 :
		default :
			$showText = '正常';
			$showImg = '<IMG SRC="__PUBLIC__/Images/icon_approve.png" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';

	}
	return ($imageShow === true) ?  $showImg  : $showText;

}

/**
 * Curl版本
 * 使用方法：
 * $post_string = "app=request&version=beta";
 * request_by_curl('http://facebook.cn/restServer.php',$post_string);
 */
function request_by_curl($remote_server, $post_string)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote_server);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "91 Ads");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function getConfigValue($configName,$key){
	$config = C($configName);
	return $config[$key];
}

function regularEscape($string){
	$find = array("\\","-","/",".");
	foreach($find as $key => $value){
		$replace[] = "\\".$value;
	}
	return str_replace($find, $replace, $string);
}

function getPushModeStartDate($mode,$args){
	if ($mode == 1) {
		return date('Y-m-d',  strtotime($args['push_time']));
	} else if ($mode == 2) {
		return $args['push_date'];
	} else if ($mode == 3) {
		return $args['start_date'];
	}
}

function getPushModeFinishDate($mode,$args){
	if ($mode == 3) {
		return $args['finish_date'];
	}
}


/**
 *
 *  过滤sql的条件语句
 * @param 已有的条件句 $wh
 * @param 字段名 $field
 * @param 值 $value
 * @param 是否模糊查询 $is_like
 */
function getSqlWhere($wh, $field, $value, $is_like = FALSE) {
	if ($is_like) {
		$result = ! empty ( $value ) ? (empty ( $wh ) ? "$field LIKE '%$value%'" : " AND $field LIKE '%$value%'") : "";
	} else {
		$result = ! empty ( $value ) ? (empty ( $wh ) ? "$field='$value'" : " AND $field='$value'") : "";
	}

	return $result;
}


/**
 *
 * 根据传进来的年月 获取下个月的年份
 * @param unknown_type $y
 * @param unknown_type $m
 */
function getNewYear($y, $m) {
	if ($m == 12) {
		$new_y = $y + 1;
	} else {
		$new_y = $y;
	}
	return $new_y;
}
/**
 *
 * 获取下个月
 * @param unknown_type $m
 */
function getNextMon($m) {
	if ($m == 12) {
		$new_m = 1;
	} else {
		$new_m = $m + 1;
	}
	return $new_m;
}
/**
 * 判断是否周末
 * @param unknown_type $timestamp
 */
function isWeekEnd($timestamp) {
	$time = getdate ( $timestamp );
	if ($time ['weekday'] == 'Saturday' || $time ['weekday'] == 'Sunday') {
		return true;
	}
	return false;
}
/**
 * 获取月份的天数
 * @param unknown_type $y
 * @param unknown_type $m
 */
function getMonDays($y, $m) {
	return date('t',mktime(0,0,0,$m,1,$y));
	//return cal_days_in_month( CAL_GREGORIAN, $m, $y );
}
/**
 * 判断循环时间是否在广告生效时间内
 * @param unknown_type $t1
 * @param unknown_type $t2
 */
function diggTime($t1, $year, $mon, $num, $day, $t2) {

	if ($mon + $num > 12) {
		$year = $year + 1;
		$mon = $mon + $num - 12;
	} else {
		$mon = $mon + $num;
	}
	$time = $year . '-' . $mon . '-' . $day;
	return strtotime ( $time ) >= strtotime ( $t1 ) && strtotime ( $time ) <= strtotime ( $t2 );
	//	$t_arr = explode('-',$t1);
//	if($t_arr[0]==$year&&$t_arr[1]==$mon){
//		return $t_arr[2];
//	}
//	return false;
}
/**
 *
 * 获取两个日期间差距的天数
 * @param unknown_type $d1
 * @param unknown_type $d2
 */
function getDaysByTwoDate($d1, $d2) {

	return round ( ceil ( strtotime ( $d1 ) - strtotime ( $d2 ) ) / (24 * 60 * 60) );

}

/**
 *
 * 获取表格对应的时间
 * @param unknown_type $year
 * @param unknown_type $mon
 * @param unknown_type $add_mon
 * @param unknown_type $day
 */
function getTableTime($year, $mon, $add_mon, $day) {
	if ($mon + $add_mon > 12) {
		$year = $year + 1;
		$mon = $mon + $add_mon - 12;
	} else {
		$mon = $mon + $add_mon;
	}
	$time = $year . '-' . str_pad ( $mon, 2, "0", STR_PAD_LEFT ) . '-' . str_pad ( $day, 2, "0", STR_PAD_LEFT );
	return $time;
}


/**
 * 生成随即颜色
 *
 * @param num 生成颜色的数量
 * @param isHex 生成颜色格式，是否是16进制，默认RGB
 * @return array
 */
function randcolor($num, $isHex = false) {
	$arr = array ();
	if (is_int ( $num ) && $num >

	0) {
		for($i = 0; $i < $num; $i ++) {
			$r = rand ( 1, 254 );
			$g = rand ( 1, 254 );
			$b = rand ( 1, 254 );
			if ($isHex) {
				$s = (substr ( "00" . dechex ( $r ), - 2 )) . (substr ( "00" . dechex ( $g ), - 2 )) . (substr ( "00" . dechex ( $b ), - 2 ));
			} else {
				$s = 'rgb(' . $r . ',' . $g . ',' . $b . ')';
			}
			if (in_array ( $s, $arr )) {
				$i;
				continue;
			}

			$arr [] = $s;
		}
	}

	return $arr;
}


function ssetcookie($var, $value, $life = 0, $prefix = 1) {
	global $timestamp, $_SERVER;

	$tablepre = C( 'DB_PREFIX' );
	setcookie ( ($prefix ? $tablepre : '') . $var, $value, $life ? $timestamp + $life : 0, get_app_inf ( 'cookiepath' ), get_app_inf ( 'cookiedomain' ), $_SERVER ['SERVER_PORT'] == 443 ? 1 : 0 );
}
/**
 * 根据日期条件过滤数组
 *
 * @param 时间数组 $resarr
 * @param 格式'2012-01-02' $stime
 * @param 格式'2012-01-02' $etime
 * @return unknown
 */
function arr_fiter($resarr, $stime, $etime) {
	if (! empty ( $stime )) {
		$arr = array ();
		foreach ( $resarr as $v ) {
			if (strtotime ( $v ) >= strtotime ( $stime )) {
				$arr [] = $v;
			}
		}
		$resarr = $arr;
	}
	if (! empty ( $etime )) {
		$arr = array ();
		foreach ( $resarr as $v ) {
			if (strtotime ( $v ) <= strtotime ( $etime )) {
				$arr [] = $v;
			}
		}
		$resarr = $arr;
	}
	return $resarr;
}
/**
 * 输入列数获取EXCEL列标题如输入0获取A
 *
 * @param $num从0开始代表第一列
 * @return   String
 */
function getColumnName($num) {
	$num = intval ( $num ) + 1;
	if ($num <= 0)
		return false;
	$letterArr = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
	$letter = '';
	do {
		$key = ($num - 1) % 26;
		$letter = $letterArr [$key] . $letter;
		$num = floor ( ($num - $key) / 26 );
	} while ( $num > 0 );
	return $letter;

}
/**
 * 计算excel时间。返回年-月
 *
 * @param unknown_type $days
 * @return unknown
 */
function excelTime($days) {
	if (is_numeric ( $days )) {
		//based on 1900-1-1
		$jd = GregorianToJD ( 1, 1, 1970 );
		$gregorian = JDToGregorian ( $jd + intval ( $days ) - 25569 );
		$myDate = explode ( '/', $gregorian );
		$myDateStr = str_pad ( $myDate [2], 4, '0', STR_PAD_LEFT ) . "-" . str_pad ( $myDate [0], 2, '0', STR_PAD_LEFT );
		return $myDateStr;
	}
	return $days;
}

/**
 * 正则获取字符串中后面带的数字,成功返回 array(0=>数字之前的字符串, 1=>后面的数字);字符串后无数字就返回False
 *
 * @param unknown_type $days
 * @return unknown
 */
function strSubfixNum($str) {
	$str = trim($str);
	$patten = '/\d+$/';
	$return = preg_split($patten, $str);
	if(count($return)<2) {
		return false;
	}
	$return[1] = substr($str, strlen($return[0]));
	return $return;
}//end strSubfixNum()

/**
 * 字符串后面的数字加上一定的数字
 *
 * @param unknown_type $days
 * @return unknown
 */
function strAddNum($str, $num) {
	$num = intval($num);
	if(is_numeric($str)) {
		return $str + $num;
	}
	$splitArray = strSubfixNum($str);
	if(false === $splitArray) {
		return $str;
	}
	$numLength = strlen($splitArray[1]);
	$splitArray[1] += $num;
	$splitArray[1] = str_pad($splitArray[1], $numLength, 0, STR_PAD_LEFT);
	return $splitArray[0] . $splitArray[1];
}//end strAddNum()
/**
 * 获取两个时间之间的时间字符串逗号分开
 *
 * @param unknown_type $stime
 * @param unknown_type $etime
 * @return unknown
 */
function getDateStr($stime,$etime){
	$length=abs(getDaysByTwoDate($stime,$etime));
	$timeArr=array();
	for($i=0;$i<=$length;$i++){
		$timeArr[]=date('Y-m-d',strtotime("+$i days",strtotime($stime)));
	}
	return implode(',',$timeArr);
}

/**
 * @功能：获取图片后缀名
 */
function getImageSuffix($p_image_file){
	$ex = explode('.', $p_image_file);
	return '.'.end($ex);
}

/**
 * @功能：产生规律性文件名
 */
function getAttachName($p_suffix, $p_basedir=''){
	$time = time();
	$hash = md5($time.mt_rand(1000,9999).$p_suffix);
	$path = $p_basedir.($p_basedir ? '' : '/').date('Y/m/d', $time).'/'.$hash.$p_suffix;

	return $path;
}

/**
 * @功能：上传图片至FTP服务器
 *
 * @param string $p_src_path 上传文件本地绝对地址
 * @param string $p_dest_path 文件欲放置的相对地址
 * @param boolean $p_upload_ftp 是否上传至FTP服务器
 * @return mixed
 */
function uploadImg($p_src_path, $p_dest_path, $p_upload_ftp=true){
	import('@.ORG.Util.Upload');
	$upload = new Upload();
	if($upload->upload($p_src_path, UPLOAD_PATH.$p_dest_path)) {
		if($p_upload_ftp) {
			import('@.Tool.Ftp');
			$ftp = new Ftp();
			$ret = $ftp->up_file(UPLOAD_PATH.$p_dest_path, $p_dest_path);

			return $ret ?  (C('IMAGE_PATH').$p_dest_path) : false;
		}

		return true;
	}

	return false;
}

/**
 * @功能：创建文件夹
 */
function mkdirs($path){
	if(empty($path)) return false;

	$path_arr = split('[\/]', $path);
	$path_create = IS_WIN ? '' : '/';
	while(!empty($path_arr)) {
		$path_create .= array_shift($path_arr).'/';
		if(!is_dir($path_create)) {
			if(!mkdir($path_create, C('FILE_UMASK'))){
				return false;
			}
		}
	}

	return true;
}

function genCgCheckBoxTree($p_list,$p_prefix='',$p_info=null,$p_chk=array(),$p_level=0){
	$str = '';
	$ids_arr = empty($p_info[$p_prefix.'_cg_ids'])? $p_chk : explode(',',substr($p_info[$p_prefix.'_cg_ids'],1,-1));
	foreach($p_list as $val){
		$checked = in_array($val[$p_prefix.'_cg_id'],$ids_arr) ? 'checked':'';
		$str .= '<input name="cg_ids[]" type="checkbox" value="'.$val[$p_prefix.'_cg_id'].'" '.$checked.' />';
		if(!empty($p_level)){
			for($i=0;$i<$p_level;$i++){
				$str .= '&nbsp;&nbsp;&nbsp;';
			}
			$str .= '└';
		}
		$str .= '&nbsp;'.$val[$p_prefix.'_cg_name'].'<br />';
		if(empty($val['child'])){
			continue;
		}else{
			foreach($val['child'] as $child){
				$arr = explode(',',$child[$p_prefix.'_cg_path']);
				$level = count($arr)-1;
				break;
			}
			$str .= genCgCheckBoxTree($val['child'],$p_prefix,$p_info,$p_id,$level);
		}
	}
	return $str;
}
function genCgTree($p_list,$p_prefix='',$p_info=null,$p_id=0,$p_not_cg=0,$p_level=0){
	$str = '';
	$id = empty($p_info)? $p_id : (empty($p_not_cg) ? $p_info[$p_prefix.'_cg_parid'] : $p_info[$p_prefix.'_cg_id']);
	foreach($p_list as $val){
		if($p_info[$p_prefix.'_cg_id']==$val[$p_prefix.'_cg_id'] && empty($p_not_cg)){
			continue;
		}
		echo $id;
		$select = ($id==$val[$p_prefix.'_cg_id']) ? 'selected': '';
		$str .= '<option value="'.$val[$p_prefix.'_cg_id'].'" '.$select.'>';
		if(!empty($p_level)){
			for($i=0;$i<$p_level;$i++){
				$str .= '&nbsp;&nbsp;&nbsp;';
			}
			$str .= '└';
		}
		$str .= $val[$p_prefix.'_cg_name'].'</option>';
		if(empty($val['child'])){
			continue;
		}else{
			foreach($val['child'] as $child){
				$arr = explode(',',$child[$p_prefix.'_cg_path']);
				$level = count($arr)-1;
				break;
			}
			$str .= genCgTree($val['child'],$p_prefix,$p_info,$p_id,$p_not_cg,$level);
		}
	}
	return $str;
}

function showCategory($p_cglist,$p_ids){
	if(empty($p_ids)) return '&nbsp;';
	$id_arr = explode(',',substr($p_ids,1,-1));

}

// 生成新尺寸的图像
function resizeImg($srcFile, $dstFile, $newWidth, $newHeight, $unlink = false) {
	//list($width, $height, $type, $attr) = getimagesize($srcFile);
	$imgOld = imagecreatefromjpeg($srcFile);
	$imgObj = imagecreatetruecolor($newWidth, $newHeight);

	$imageCopyFunc = 'imagecopyresampled';
	if(!function_exists($imageCopyFunc)) {
		$imageCopyFunc = 'imagecopyresized';
	}
	$imageCopyFunc($imgObj,$imgOld,0,0,0,0,$newWidth,$newHeight,imagesx($imgOld),imagesy($imgOld));

	imagedestroy($imgOld);
	if($unlink) @unlink($srcFile);
	imagejpeg($imgObj, $dstFile, 100);
	@chmod($dstFile, 0777);
	imagedestroy($imgObj);
}//end resizeImg()

//生成静态页面url
function NDU($p_module, $p_device='all', $p_action='index', $p_id=0, $p_page=0){
	$domain = $_SERVER['HTTP_HOST'];
	$suffix = '.html';
	$url   =  dirname(__APP__).$p_module.'/'.$p_device;
	if($p_id){
		$url .= '/'.$p_id;
	}
	$url .= '/'.$p_action;

	if($p_page){
		$url .= '-'.$p_page;
	}
	$url .= $suffix;
	$url   =  'http://'.$domain.$url;
	return $url;
}

function genOsTypeCheck($p_vo,$p_name){
	$p_values = $p_vo[$p_name];
	$type_arr = array(1=>'iphone',2=>'ipad',3=>'android');
	$p_type = array();
	if(!empty($p_values)){
		$arr = explode(',',$p_values);
		foreach($arr as $value){
			if(empty($value)){
				continue;
			}
			$p_type[] = $value;
		}
	}
	$str = '';
	foreach($type_arr as $key=>$val){
		$chk = '';
		if(in_array($key,$p_type)){
			$chk = 'checked';
		}
		$str .= '<input type="checkbox" name="'.$p_name.'[]" value="'.$key.'" '.$chk.' />&nbsp;'.$val.'&nbsp;';
	}
	return $str;
}

//项目需要,将版本号各域前面填充0变成四位,然后连接成一串新的数字表示版本
function verCodeFill0AndToNum($p_verCode, $boundMax = 4, $fieldMax = 4) {
	//echo str_pad($input, 10, "-=", STR_PAD_LEFT);
	$verNumArray = explode('.', trim($p_verCode));
	$bound = count($verNumArray);

	if($bound < $boundMax) {
		$verNumArray = array_merge($verNumArray, array_fill($bound, $boundMax - $bound, ''));
	}

	foreach($verNumArray as $k => $v) {
		$verNumArray[$k] = str_pad($v, $fieldMax, '0', STR_PAD_LEFT);
	}

	return implode('', $verNumArray);
}//end verCodeFill0AndToNum()

//遍历指定目录下的所有文件
function listAllFile($p_dir = './', $onlyFile = true) {
	$list = array();
	if($handle = opendir($p_dir)) {
		while(false !== ($file = readdir($handle))) {
			$fileFullPath = $p_dir . $file;
			if($file == '.' || $file == '..') {
				continue;
			} elseif(is_dir($fileFullPath)) {
				if(!$onlyFile) {
					$list[] = array('dirname' => $fileFullPath);
				}
				$sub_files = listAllFile($fileFullPath . '/');
				$list = array_merge($list, $sub_files);
			} else {
				$list[] = $p_dir .$file;
			}
		}
		closedir($handle);
	}

	return $list;
}//end listAllFile()

// 根据图片尺寸类型号,获取图片尺寸的描述字符串
function getSizeStrByType($p_type) {
	$sizes = C('PIC_SIZES');
	return $sizes[intval($p_type)];
}//eng getSizeStrByType()

// 根据图片分类路径,获取分类标题的路径显示
function getPicCatePath($p_path) {
	$pathids = trim(trim($p_path), ',');
	$pathids = explode(',', $pathids);
	$path = '';
	foreach($pathids as $id) {
		$path .= M('PicCate')->where(array('id' => intval($id)))->getField('title') . ' > ';
	}

	return $path;
}//end getPicCatePath()

function getPicCatePathByID($p_id) {
	$mPicCate = M('PicCate');
	$path = $mPicCate->where(array($mPicCate->getPk() => $p_id))->getField('path') . $p_id;

	return getPicCatePath($path);
}//end getPicCatePathByID()

function getFileUrlOnSiteByList(&$list, $fields, $type) {
	if(!is_array($fields)) {
		$fields = array($fields);
	}

	foreach($list as $key => $row) {
		foreach($fields as $field) {
			if(!empty($row[$field])) {
				$list[$key][$field] = getFileUrlOnSite($row[$field], $type);
			}
		}
	}

	return;
}//end getFileUrlOnSiteByList()

function getFileUrlOnSiteByList91Play(&$list, $fields, $type, $fromField = 'from') {
	if(!is_array($fields)) {
		$fields = array($fields);
	}

	foreach($list as $key => $row) {
		if(1 == intval($row[$fromField])) {
			$FTP_IMG_PATH = C('FTP_IMG_PATH');
			$FTP_SOFT_PATH = C('FTP_SOFT_PATH');
			$IMAGE_HOST = C('IMAGE_HOST');
			$SOFT_HOST = C('SOFT_HOST');
			C('FTP_IMG_PATH', C('FTP_IMG_PATH_91PLAY'));
			C('FTP_SOFT_PATH', C('FTP_SOFT_PATH_91PLAY'));
			C('IMAGE_HOST', C('IMAGE_HOST_91PLAY'));
			C('SOFT_HOST', C('SOFT_HOST_91PLAY'));
		}
		foreach($fields as $field) {
			if(!empty($row[$field])) {
				$list[$key][$field] = getFileUrlOnSite($row[$field], $type);
			}
		}
		if(1 == intval($row[$fromField])) {
			C('FTP_IMG_PATH', $FTP_IMG_PATH);
			C('FTP_SOFT_PATH', $FTP_SOFT_PATH);
			C('IMAGE_HOST', $IMAGE_HOST);
			C('SOFT_HOST', $SOFT_HOST);
		}
	}

	return;
}//end getFileUrlOnSiteByList91Play()

// 循环创建目录
if(!function_exists('mk_dir')) {
	function mk_dir($dir, $mode = 0777) {
		if (is_dir($dir) || @mkdir($dir, $mode))
			return true;
		if (!mk_dir(dirname($dir), $mode))
			return false;
		return @mkdir($dir, $mode);
	}
}

/**
 * 比较appKey的大小
 * newVer 大于 oldVer 返回true,否则返回false
 * @param  $oldVer
 * @param  $newVer
 * @return boolean
 */
function appKeycmp($p_oldVer, $p_newVer) {
	$oldVer = verCodeFill0AndToNum($p_oldVer);
	$newVer = verCodeFill0AndToNum($p_newVer);
	return $newVer > $oldVer;
}

/* function appKeycmp($oldVer, $newVer) {
	$oldArray = str_split($oldVer);
	$newArray = str_split($newVer);
	$oldCount = count($oldArray);
	$newCount = count($newArray);
	if($oldCount > $newCount) {
		$bigArray = $oldArray;
	} else {
		$bigArray = $newArray;
	}
	$count = count($bigArray);
	$diffOld = '';
	$diffNew = '';
	for($i = 0; $i < $count; ++$i) {
		$vOld = isset($oldArray[$i]) ? $oldArray[$i] : '';
		$vNew = isset($newArray[$i]) ? $newArray[$i] : '';
		if ($vOld != $vNew) {
			$diffOld .= $vOld;
			$diffNew .= $vNew;
		}
	}
	$diff_old = explode('.', $diffOld);
	$diff_new = explode('.', $diffNew);
	if(!is_numeric($diff_old[0]) || !is_numeric($diff_new[0])) {
		return false;
	}
	if((int)$diff_old[0] < (int)$diff_new[0] || count($diff_old) < count($diff_new)) {
		return true;
	} else {
		return false;
	}
} */

//检查密码强度
function checkPWStrength($pw) {
	$lenMin = C('PW_LEN_MIN');
	if($lenMin > strlen($pw)) {
		return false;
	} else {
		return true;
	}
}

//检查用户名合法性
function checkUsername($username) {
	$username = trim($username);
	load('extend');
	$lenMin = C('USERNAME_LEN_MIN');
	$lenMax = C('USERNAME_LEN_MAX');
	$strlen = mb_strlen($username, 'UTF-8');
	if($lenMin > $strlen || $lenMax < $strlen) {
		return false;
	} else {
		return true;
	}
}

function checkNickname($nickname) {
	$nickname = trim($nickname);
	load('extend');
	$lenMin = C('NICKNAME_LEN_MIN');
	$lenMax = C('NICKNAME_LEN_MAX');
	$strlen = mb_strlen($nickname, 'UTF-8');
	if($lenMin > $strlen || $lenMax < $strlen) {
		return false;
	} elseif(!preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $nickname)) {
		return false;
	} else {
		return true;
	}
}

// 获取手机端的会话ID
function getMobileSessId() {
	return session_id();
}

function clearDir($p_path, $cacheTime = 0) {
	clearstatcache();
	$pathToClear = trim($p_path);
	if(is_dir($pathToClear)) {
		$clearTime = $_SERVER['REQUEST_TIME'] - $cacheTime;
		$fileList = listAllFile($pathToClear, true);
		foreach($fileList as $file) {
			if(filectime($file) <= $clearTime){
				@unlink($file);
			}
		}
	}
}

function getHttpSize($p_url) {
	$url = trim($p_url);
	$headers = get_headers($url);
	foreach($headers as $val){
		if(strstr($val, 'Content-Length')){
			$content_length = $val;
			break;
		}
	}
	$length = explode(':', $content_length);

	return intval($length[1]);
}

function getFtpSize($p_store, $p_config) {
	import('@.ORG.Util.Ftp');
	foreach(C($p_config) as $v) {
		$config = $v;
		break;
	}
	$ftp = new FTP(
		$config['FTP_HOST'],
		$config['FTP_PORT'],
		$config['FTP_USER'],
		$config['FTP_PASS']
	);
	$ftp_size = 0;
	if($ftp->off) {
		$ftp_size = ftp_size($ftp->conn_id, trim($p_store));
	}

	return $ftp_size;
}

/*
 * 上传本地文件到FTP上
 * @p_file    相对于网站根目录的本地文件
 * @$p_config FTP配置文件,直接用 C($p_config) 装载
 * @$singleFtp 只传一台FTP, 因为有时FTP集群可自动同步
 * @$singleSuccess 只要一台成功就认为是成功
 *
 * @return bool 只要有一个FTP上传成功,就返回 true
 */
function uploadFtpFile($p_file, $p_config, $singleFtp = false, $singleSuccess = true) {
	import('@.ORG.Util.Ftp');
	$up_result = array(
		'success' => false,
		'message' => '',
	);
	$hasSuccess	= false;
	$hasFault	= false;

	$file = BASE_PATH . trim($p_file);
	foreach(C($p_config) as $v) {
		$ftp = new FTP(
			$v['FTP_HOST'],
			$v['FTP_PORT'],
			$v['FTP_USER'],
			$v['FTP_PASS']
		);
		if($ftp->off && $ftp->up_file($file, $p_file)) {
			$hasSuccess	= true;
		} else {
			$hasFault	= true;
			$up_result['message'] = $ftp->errMsg;
		}
		$ftp->close();

		if(!$singleSuccess && $hasFault) {
			break;
		}
	}

	if($singleSuccess) {
		$up_result['success'] = $hasSuccess;
	} else {
		$up_result['success'] = !$hasFault;
	}

	return $up_result;
}

function downUrl($url, $saveFile, $offset = 0, $proxy = '', $httpHeader = '') {
	$h_curl = curl_init();
	$h_file = fopen($saveFile, 'wb');
	curl_setopt($h_curl, CURLOPT_HEADER, 0);
	curl_setopt($h_curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($h_curl, CURLOPT_TIMEOUT, 60);
	curl_setopt($h_curl, CURLOPT_URL, $url);
	curl_setopt($h_curl, CURLOPT_FILE, $h_file);
	curl_setopt($h_curl, CURLOPT_PROXY, $proxy);
	curl_setopt($h_curl, CURLOPT_SSL_VERIFYPEER, false); // 阻止对证书的合法性的检查
	curl_setopt($h_curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
	curl_setopt($h_curl, CURLOPT_RESUME_FROM, intval($offset));
	//curl_setopt($h_curl, CURLOPT_RETURNTRANSFER, true);
	if(!empty($httpHeader) && is_array($httpHeader)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
	}
	$curl_success = curl_exec($h_curl);

	fclose($h_file);
	curl_close($h_curl);
	return $curl_success;
}

//合并两个文件,保存到$mainFile
function mergeFile($mainFile, $partFile) {
	$h_mainFile = fopen($mainFile, 'ab+');
	$h_partFile = fopen($partFile, 'rb');
	$content = fread($h_partFile, filesize($partFile));
	fclose($h_partFile);
	fwrite($h_mainFile, $content);
	fclose($h_mainFile);
}

function getHttpStatus($p_url) {
	$h_curl = curl_init();
	$url = trim($p_url);
	curl_setopt($h_curl, CURLOPT_URL, $url);
	curl_setopt($h_curl, CURLOPT_HEADER, 1); //获取Header
	curl_setopt($h_curl, CURLOPT_NOBODY,true); //Body不要
	curl_setopt($h_curl, CURLOPT_RETURNTRANSFER, 1); //数据存到字符串，别直接输出到屏幕
	$data = curl_exec($h_curl);
	$statCode = curl_getinfo($h_curl, CURLINFO_HTTP_CODE); //HTTPSTAT码
	curl_close($h_curl);

	return $statCode;
}

// 登录者是否关注用户
function isLike($p_uid) {
	$uid = intval($p_uid);
	if(getUid() == $uid) {
		return true;
	}
	return D('LikeUser')->isLike(getUid(), $uid);
}

// 是否互相关注
function isBeLike($p_uid) {
	$uid = intval($p_uid);
	if(getUid() == $uid) {
		return true;
	}
	return D('LikeUser')->isBeLike(getUid(), $uid);
}

// 关注状态
// 0: 未关注; 1: 已关注(单向); 2: 互相关注;
// 状态不多，所以没有用位计算
function likeStatus($p_uid) {
	$status		= 0;
	$uid		= intval($p_uid);
	$isLike		= isLike($uid);
	$isBeLike	= isBeLike($uid);

	if($isLike) {
		$status = 1;
	}

	if(1 == $status && $isBeLike) {
		$status = 2;
	}

	return $status;
}

// 谁喜欢此图(点赞)
function whoLikePic($p_picId) {
	return D('Pic')->whoLike(intval($p_picId));
}

function getPicOwner($p_picId) {
	return D('Pic')->getOwner(intval($p_picId));
}

function getRegionParentId($p_id) {
	$parentId = D('Region')->getRegionParentId($p_id);
	return $parentId;
}

function msgcount() {
	return (int)D('MsgView')->countNoRead(getUid());
}

function getCityByIp($p_ip = '') {
	$ip = trim($p_ip);
	empty($ip) && $ip = get_client_ip();
	import('Common.ORG.IpCity', BASE_PATH);
	$city = new IpCity(BASE_PATH . 'Common/Datafile/qqwry.dat');
	$addr = $city->getCity($ip);
	return $addr;
}
