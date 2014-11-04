<?php
if (!defined('THINK_PATH')) exit();
//信息类型
return array(
	//程序中要组成图片完整的路径,范例如下:
	//C('FILE_UP_PATH') . C('FILE_UP_SUB_DIR') . '/' . C('FILE_UPLOAD_CONFIG_1.savePath')

	'FILE_UP_SUB_DIR' => 'files',	//图片上传目录的目录名

	'FILE_UP_TYPE' => array(
		'APP_SOFT' => 1,	//安装软件
		'KE_ATTACH' => 100,	//KindEditor编辑时上传的文件(附件、图片等)
	),

	'FILE_UP_FIELD_NAME' => 'file',	//上传图片时的表单字段名

	//配置: 安装软件
	'FILE_UPLOAD_CONFIG_1' => array(
		'maxSize' => 100000000,	//上传文件大小限制
		'allowExts' => explode(',', 'apk,pxl,ipa,jar,deb'),	//上传文件类型限制
		'savePath' => 'soft',	//上传目录的目录名
		//'saveRule' => 'uniqid',	//上传规则
		'hashType' => 'trim',
		'uploadReplace' => true,
	),

	//配置: KindEditor编辑时上传的文件(附件、图片等)
	'FILE_UPLOAD_CONFIG_100' => array(
		'maxSize' => 100000000,	//上传文件大小限制
		'allowExts' => null,	//上传文件类型限制
		'savePath' => 'attach',	//上传目录的目录名
		'saveRule' => 'uniqid',	//上传规则
		'hashType' => 'md5_file',
		'uploadReplace' => true,
	),

);
?>
