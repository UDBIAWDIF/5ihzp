<?php

function dumpe($data, $die = true) {
	header('Content-type: text/html; charset=UTF-8');
	dump($data);
	$die && die;
}

function dlog($data, $title) {
	if(empty($title)) {$title = date('Y-m-d H:i:s');}
	file_put_contents('dumplog.txt',
		'-==============' . $title . '==============-' . PHP_EOL .
		var_export($data, true) . PHP_EOL,
		FILE_APPEND
	);
}

function isUrl($p_file) {
	$url_prefixs = array(
		'http://',
		'https://',
		'ftp://',
		'ftps://',
	);

	$is_url = false;
	$file = trim($p_file);
	foreach($url_prefixs as $url_prefix) {
		if(0 == strcasecmp($url_prefix, substr($file, 0, strlen($url_prefix)))) {
			$is_url = true;
			break;
		}
	}

	return $is_url;
}

function getImgUrlOnSite($p_file) {
	$file = trim($p_file);
	$img_link_separator = C('IMG_LINK_SEPARATOR');

	if(empty($file)) {
		return '';
	}

	if(isUrl($file)) {
		return $file;
	} else {
		//多图连接时,后续图也要加域名前缀
		if(false !== stripos($file, $img_link_separator)) {
			$file = str_replace(
							$img_link_separator,
							$img_link_separator . C('IMAGE_SERVER_URL'),
							$file
						);
		}
		return C('IMAGE_SERVER_URL') . $file;
	}
}

function getSmallImgUrl($p_imgUrl) {
	$imgUrl = trim($p_imgUrl);
	return dirname($imgUrl) . '/s_' . basename($imgUrl);
}

// 保存相对路径的文件,输出本站文件存储位置的URL前缀,暂时是图片的FTP
function getFileUrlOnSite($p_file, $type = 'IMG') {
	$file = trim($p_file);
	$img_link_separator = C('IMG_LINK_SEPARATOR');
	$type = strtoupper(trim($type));
	$hostPathByType = array(
		'IMG' => C('IMAGE_HOST') . C('FTP_IMG_PATH') . '/',
		'APP' => C('SOFT_HOST') . C('FTP_SOFT_PATH') . '/',
	);

	if(empty($file)) {
		return '';
	}
	if(isUrl($file)) {
		return $file;
	} else {
		//多图连接时,后续图也要加域名前缀
		if(false !== stripos($file, $img_link_separator)) {
			$file = str_replace(
							$img_link_separator,
							$img_link_separator . $hostPathByType[$type],
							$file
						);
		}
		return $hostPathByType[$type] . $file;
	}
}

function getUserHeadById() {
	return call_user_func_array('getIconUrl', func_get_args());
}

function getIconUrl($p_iconId) {
	$iconId		= intval($p_iconId);
	$iconPath	= M('Icon')
					->where(array(
						// 'icon_rel_type'	=> 'userhead',
						'icon_id'		=> $iconId,
					))
					->getField('icon_path');
	return getImgUrlOnSite($iconPath);
}

function getGroupName($p_gpId) {
	$runnable = true;
	$groupName = '';
	$gpId = intval($p_gpId);
	empty($gpId) && $runnable = false;
	$runnable && $groupName = D('Group')->getName($gpId);
	return $groupName;
}

// 获取文件的 目录名、文件名(不带扩展名)、扩展名
function getFilenamePart($p_file) {
	$file_part = array(
		'dirname' => '',
		'filename' => '',
		'extname' => '',
	);

	$file_part['dirname'] = dirname($p_file);
	$file_part['extname'] = substr(strrchr($p_file, '.'), 1);
	$file_part['filename'] = basename($p_file, '.' . $file_part['extname']);

	return $file_part;
}//end getFilenamePart()

function datetime($format = 'dt', $time = null) {
	$datetimeFormat = array(
		'dt'	=> 'Y-m-d H:i:s',
		'd'		=> 'Y-m-d',
		't'		=> 'H:i:s',
	);

	if(is_numeric($time)) {
		$time = intval($time);
	} else {
		$time = $_SERVER['REQUEST_TIME'];
	}

	$format = strtolower($format);
	if(!array_key_exists($format, $datetimeFormat)) {
		$format = 'dt';
	}

	if(0 === $time) return '(Null)';
	return date($datetimeFormat[$format], $time);
}

function getMailHandle() {
	static $hMail = null;
	if(null === $hMail) {
		import('Common.ORG.PHPMailer.PHPMailer', BASE_PATH);
		$hMail = new PHPMailer;
		$hMail->isSMTP();
		$hMail->CharSet		= C('MAIL.CHARSET');
		$hMail->Host		= C('MAIL.SMTPSERVER');
		$hMail->SMTPAuth	= C('MAIL.SMTPAUTH');
		$hMail->Username	= C('MAIL.SMTPUSER');
		$hMail->Password	= C('MAIL.SMTPPWD');
		$hMail->From		= C('MAIL.MAILFROM');
		$hMail->FromName	= C('MAIL.SENDER');
		$hMail->addReplyTo(C('MAIL.MAILFROM'), C('MAIL.SENDER'));
		$hMail->isHTML(C('MAIL.ISHTML'));
	}

	return $hMail;
}

/**
 *  获取拼音信息
 *
 * @access    public
 * @param     string  $str  字符串
 * @param     int  $ishead  是否为首字母
 * @return    string
 */
function pinyin($str, $ishead = 0) {
	static $pinyins;
	$restr = '';
	$str = trim($str);
	$slen = strlen($str);
	if($slen < 2) {
		return $str;
	}

	if(count($pinyins) == 0) {
		$fp = fopen(BASE_PATH . '/Common/Datafile/pinyin.dat', 'r');
		while(!feof($fp)) {
			$line = trim(fgets($fp));
			$pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
		}
		fclose($fp);
	}

	$str = iconv('utf-8', 'gbk//ignore', $str);
	for($i = 0; $i < $slen; $i++) {
		if(ord($str[$i]) > 0x80) {
			$c = $str[$i] . $str[$i + 1];
			$i++;
			if(isset($pinyins[$c])) {
				if($ishead == 0) {
					$restr .= $pinyins[$c];
				} else {
					$restr .= $pinyins[$c][0];
				}
			} else {
				$restr .= '_';
			}
		} else if(preg_match('/[a-z0-9]/i', $str[$i])) {
			$restr .= $str[$i];
		} else {
			$restr .= '_';
		}
	}

	return $restr;
}

function upImg($img_type, $checksize = false, $up_to_ftp = false, $trigger = null) {
	$m_file = D('File');	// TODO: File做成公共工具类
	$img_path = $m_file->upload_img($img_type, $checksize, $up_to_ftp, $trigger);
	return $img_path;
}

function filter_str($str) {
	load('extend');
	$str = addslashes($str);
	$str = strip_tags($str);
	$str = remove_xss($str);
	return $str;
}

// 获取邮箱登录地址
if(!function_exists('emailsp_get_login_url')) {
	function emailsp_get_login_url($email) {
		$email_suffix = strstr($email, '@');
		$email_suffix = substr($email_suffix, 1);

		$emailsp_login_urls = array(
			'gmail.com'     => 'http://www.gmail.com/',

			'qq.com'        => 'http://mail.qq.com/',
			'vip.qq.com'    => 'http://mail.qq.com/',
			'foxmail.com'   => 'http://www.foxmail.com/',

			'163.com'       => 'http://mail.163.com/',
			'vip.163.com'   => 'http://vip.163.com/',
			'126.com'       => 'http://www.126.com/',
			'vip.126.com'   => 'http://vip.126.com/',
			'yeah.net'      => 'http://www.yeah.net/',
			'188.com'       => 'http://mail.188.com',
			'vip.188.com'   => 'http://mail.188.com',

			'sina.com'      => 'http://mail.sina.com.cn/',
			'vip.sina.com'  => 'http://vip.sina.com.cn/',

			'yahoo.com.cn'  => 'http://mail.cn.yahoo.com/',
			'yahoo.cn'      => 'http://mail.cn.yahoo.com/',
			'yahoo.com'     => 'http://mail.yahoo.com/',

			'139.com'       => 'http://mail.10086.cn/',
			'189.cn'        => 'http://www.189.cn/',
			'wo.com.cn'     => 'http://mail.wo.com.cn/',

			'sohu.com'      => 'http://mail.sohu.com/',
			'vip.sohu.com'  => 'http://vip.sohu.com/',
			'sogou.com'     => 'http://mail.sogou.com',

			'21cn.com'      => 'http://mail.21cn.com/',
			'21cn.net'      => 'http://mail.21cn.com/net/',

			'tom.com'       => 'http://mail.tom.com/',
			'vip.tom.com'   => 'http://vip.tom.com/',

			'263.net'       => 'http://mail.263.net/',
			'263.net.cn'    => 'http://mail.263.net/',
			'x263.net'      => 'http://mail.263.net/',

			'eyou.com'      => 'http://www.eyou.com/',
			'vip.eyou.com'  => 'http://vip.eyou.com/',

			'hotmail.com'   => 'http://mail.live.com/',
			'live.com'      => 'http://mail.live.com/',
			'live.cn'       => 'http://mail.live.cn/',
		);

		if (isset($emailsp_login_urls[$email_suffix])) return $emailsp_login_urls[$email_suffix];

		return 'http://mail.'.$email_suffix.'/';
	}
}

/*
 * 名称：debugVar()
 * 功能：保存运行时的各种变量
 * 使用方法：debugVar($var,'var');
 * $var：需要保存的变量
 * $var_name：备注说明
 *
 * */
function debugVar($var, $var_name) {
	$debug_file = RUNTIME_PATH . 'Debug/debug';

	$debug_dir = dirname($debug_file);
	if(!is_dir($debug_dir)) {
		@mk_dir($debug_dir);
	}

	if(false === $var_name) {
		file_put_contents($debug_file, '');
		return;
	}

	$debug_filesize = filesize($debug_file);
	clearstatcache();
	$text = '';
	if(empty($debug_filesize)) {
		$text .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>' . "\r\n";
	}
	$text .= '<div style="color:red">Client: ' . get_client_ip() .' Time: ' . date('Y-m-d H:i:s') . '</div><b>Var Name: ' . $var_name . '</b>' . "\r\n";
	$debug = $text . dump($var, $echo=false) . "\r\n\r\n";
	file_put_contents($debug_file, $debug, FILE_APPEND);
}

// 统计数据列表指定字段的值总和
function listSumByField($dataList, $p_field) {
	$sum	= 0;
	$field	= trim($p_field);
	if(is_array($dataList)) {
		foreach($dataList as $data) {
			if(!isset($data[$field])) {
				break;
			}
			$sum += intval($data[$field]);
		}
	}

	return $sum;
}

// 把 ID 连成的字符串变成由 ID 组成的数组, 可帮助过滤注入, 并方便模型操作
function idsToIntArray($p_ids, $separator = ',') {
	$ids = is_array($p_ids) ? $p_ids : explode($separator, $p_ids);
	$ids = array_map('intval', $ids);
	$ids = array_filter($ids);
	$ids = array_filter($ids, 'is_int');
	$ids = array_values($ids);
	return $ids;
}
