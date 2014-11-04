<?php
if (!defined('THINK_PATH')) exit();
//信息类型
return array(
	'IMG_LINK_SEPARATOR' => '@@@',
	//程序中要组成图片完整的路径,范例如下:
	//C('FILE_UP_PATH') . C('IMG_UP_SUB_DIR') . '/' . C('IMG_UPLOAD_CONFIG_1.savePath')

	'IMG_UP_SUB_DIR' => 'images',	//图片上传目录的目录名
	'WATER_IMAGE' => 'Public/images/wyl_water_white.png',	//图片水印图片,相对于模板路径( TMPL_PATH . C('WATER_IMAGE'))
	'MERGE_WATER_REQUEST_FIELD' => 'img_merge_water',	//是否打水印标志的请求字段名

	'IMG_UP_TYPE' => array(
		'APP_COVER'			=> 1,		//软件封面
		'APP_ICON'			=> 2,		//软件ICON图
		'APP_SCREENSHOT'	=> 3,		//软件截图
		'APP_CG_COVER'		=> 4,		//软件分类封面
		'APP_CG_ICON'		=> 5,		//软件分类ICON图

		'ART_COVER'			=> 100,		//文章封面
		'ART_ICON'			=> 101,		//文章ICON图

		'SH_COVER'			=> 102,		//达人分享封面

		'RM_ICON'			=> 10,		//推荐图标
		'RM_COVER'			=> 12,		//推荐封面

		'RD_ICON'			=> 11,		//合辑应用图标
		'KE_IMG'			=> 1000,	//KindEditor上传图片
	),

	'IMG_UP_FIELD_NAME' => 'image',	//上传图片时的表单字段名

	//上传图片时的配置
	'IMG_CLASS_PATH' => '@.ORG.Image',	//图片类库包路径

	//配置: 软件封面
	'IMG_UPLOAD_CONFIG_1' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg'),	//上传文件类型限制
		'savePath' => 'app_cover',	//上传目录的目录名
		'thumb' => true,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '360',	//缩略图最大宽度
		'thumbMaxHeight' => '175',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
		// 'maxWidth' => '250',
		// 'thumbMaxSize' => '40000',
	),

	//配置: 软件ICON图
	'IMG_UPLOAD_CONFIG_2' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg,icon'),	//上传文件类型限制
		'savePath' => 'app_icon',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '200',	//缩略图最大宽度
		'thumbMaxHeight' => '200',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 软件截图
	'IMG_UPLOAD_CONFIG_3' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg'),	//上传文件类型限制
		'savePath' => 'app_screenshot',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '40',	//缩略图最大宽度
		'thumbMaxHeight' => '40',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
		'thumbMaxSize' => '70000',
	),

	//配置: 软件分类封面
	'IMG_UPLOAD_CONFIG_4' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg'),	//上传文件类型限制
		'savePath' => 'app_cover',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		// 'thumbMaxWidth' => '360',	//缩略图最大宽度
		// 'thumbMaxHeight' => '175',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
		// 'maxWidth' => '250',
		// 'thumbMaxSize' => '40000',
	),

	//配置: 软件分类ICON图
	'IMG_UPLOAD_CONFIG_5' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg,icon'),	//上传文件类型限制
		'savePath' => 'app_icon',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		// 'thumbMaxWidth' => '200',	//缩略图最大宽度
		// 'thumbMaxHeight' => '200',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 文章封面
	'IMG_UPLOAD_CONFIG_100' => array(
			'maxSize' => 200000,	//上传文件大小限制
			'allowExts' => explode(',', 'jpg,png,jpeg'),	//上传文件类型限制
			'savePath' => 'art_cover',	//上传目录的目录名
			'thumb' => false,	//是否生成缩略图
			//------ 无用段 Begin ------
			//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
			'thumbPrefix' => 's_',	//生产2张缩略图
			'thumbMaxWidth' => '40',	//缩略图最大宽度
			'thumbMaxHeight' => '40',	//缩略图最大高度
			//------ 无用段 End ------
			'saveRule' => 'uniqid',	//上传规则
			'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 文章icon
	'IMG_UPLOAD_CONFIG_101' => array(
			'maxSize' => 200000,	//上传文件大小限制
			'allowExts' => explode(',', 'jpg,png,jpeg'),	//上传文件类型限制
			'savePath' => 'art_icon',	//上传目录的目录名
			'thumb' => false,	//是否生成缩略图
			//------ 无用段 Begin ------
			//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
			'thumbPrefix' => 's_',	//生产2张缩略图
			'thumbMaxWidth' => '40',	//缩略图最大宽度
			'thumbMaxHeight' => '40',	//缩略图最大高度
			//------ 无用段 End ------
			'saveRule' => 'uniqid',	//上传规则
			'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 分享封面
	'IMG_UPLOAD_CONFIG_102' => array(
			'maxSize' => 200000,	//上传文件大小限制
			'allowExts' => explode(',', 'jpg,png,jpeg'),	//上传文件类型限制
			'savePath' => 'sh_cover',	//上传目录的目录名
			'thumb' => false,	//是否生成缩略图
			//------ 无用段 Begin ------
			//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
			'thumbPrefix' => 's_',	//生产2张缩略图
			'thumbMaxWidth' => '40',	//缩略图最大宽度
			'thumbMaxHeight' => '40',	//缩略图最大高度
			//------ 无用段 End ------
			'saveRule' => 'uniqid',	//上传规则
			'thumbRemoveOrigin' => false,	//是否删除原图
			'maxWidth' => '250',
	),

	//配置: 推荐ICON图
	'IMG_UPLOAD_CONFIG_10' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg,icon'),	//上传文件类型限制
		'savePath' => 'rm_icon',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '200',	//缩略图最大宽度
		'thumbMaxHeight' => '200',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 合辑应用ICON图
	'IMG_UPLOAD_CONFIG_11' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg,icon'),	//上传文件类型限制
		'savePath' => 'rd_icon',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '200',	//缩略图最大宽度
		'thumbMaxHeight' => '200',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: 推荐封面
	'IMG_UPLOAD_CONFIG_12' => array(
			'maxSize' => 200000,	//上传文件大小限制
			'allowExts' => explode(',', 'jpg,png,jpeg'),	//上传文件类型限制
			'savePath' => 'sh_cover',	//上传目录的目录名
			'thumb' => false,	//是否生成缩略图
			//------ 无用段 Begin ------
			//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
			'thumbPrefix' => 's_',	//生产2张缩略图
			'thumbMaxWidth' => '40',	//缩略图最大宽度
			'thumbMaxHeight' => '40',	//缩略图最大高度
			//------ 无用段 End ------
			'saveRule' => 'uniqid',	//上传规则
			'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//配置: KindEditor上传图片
	'IMG_UPLOAD_CONFIG_1000' => array(
		'maxSize' => 200000,	//上传文件大小限制
		'allowExts' => explode(',', 'jpg,gif,png,jpeg,icon'),	//上传文件类型限制
		'savePath' => 'ke_img',	//上传目录的目录名
		'thumb' => false,	//是否生成缩略图
		//------ 无用段 Begin ------
		//'imageClassPath' => '@.ORG.Image',	//图片类库包路径
		'thumbPrefix' => 's_',	//生产2张缩略图
		'thumbMaxWidth' => '200',	//缩略图最大宽度
		'thumbMaxHeight' => '200',	//缩略图最大高度
		//------ 无用段 End ------
		'saveRule' => 'uniqid',	//上传规则
		'thumbRemoveOrigin' => false,	//是否删除原图
	),

	//图片大图转小图的对照表, 小图尺寸 => 原始尺寸s
	'IMG_RESIZE_MAP' => array(
		'320_480' => array('640_960'),
		'480_320' => array('960_640'),
		'360_480' => array('768_1024', '1536_2048'),
		'480_360' => array('1024_768', '2048_1536'),
		'334_480' => array('640_920'),
		'367_480' => array('768_1004', '1536_2008'),
		'480_300' => array('960_600'),
		'480_351' => array('1024_748', '2048_1496'),
		'175_175' => array('1024_1024', '512_512'),
		'307_512' => array('480_800'),
		'512_307' => array('800_480'),
		'288_512' => array('480_854', '720_1280'),
		'512_288' => array('854_480', '1280_720'),
		'512_320' => array('1280_800'),
		'320_512' => array('800_1280'),
		'480_270' => array('1136_640'),
		'270_480' => array('640_1136'),
		'280_480' => array('640_1096'),
		'720_350' => array('720_350'),
	),

	//图片检测大小的结果状态表
	'IMG_SIZE_CHECKED_STATUS' => array(
		'NO_ALLOW' => 0,	//不允许此尺寸的图片
		'CHANGE' => 1,	//此尺寸的图片要处理(改变)
		'NO_CHANGE' => 2,	//此尺寸图片可直接使用
	),
);
?>
