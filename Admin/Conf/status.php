<?php
return array(
	'ARTICLE_STATUS' =>array(	//文章状态码表
		'ARTICLE_STATUS_FIELD'=>'art_status',	//状态在数据库里的字段
		'ARTICLE_NORMAL_STATUS'=>'1',	//上架
		'ARTICLE_CANCEL_STATUS'=>'0',	//下架
		'ARTICLE_BATCH_CANCEL_STATUS'=>0,	//批量下架
		'ARTICLE_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),
	'ARTICLECATEGORY_STATUS' =>array(	//文章分类状态码表
			'ARTICLECATEGORY_STATUS_FIELD'=>'art_cg_status',	//状态在数据库里的字段
			'ARTICLECATEGORY_NORMAL_STATUS'=>'1',	//上架
			'ARTICLECATEGORY_CANCEL_STATUS'=>'0',	//下架
			'ARTICLECATEGORY_BATCH_CANCEL_STATUS'=>0,	//批量下架
			'ARTICLECATEGORY_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),
	'APPCATEGORY_STATUS' =>array(	//文章分类状态码表
			'APPCATEGORY_STATUS_FIELD'=>'app_cg_status',	//状态在数据库里的字段
			'APPCATEGORY_NORMAL_STATUS'=>'1',	//上架
			'APPCATEGORY_CANCEL_STATUS'=>'0',	//下架
			'APPCATEGORY_BATCH_CANCEL_STATUS'=>0,	//批量下架
			'APPCATEGORY_RECOVER_STATUS'=>1,	//批量上架
	),
	'SHARING_STATUS' =>array(	//分享状态码表
			'SHARING_STATUS_FIELD'=>'art_status',	//状态在数据库里的字段
			'SHARING_NORMAL_STATUS'=>'1',	//上架
			'SHARING_CANCEL_STATUS'=>'0',	//下架
			'SHARING_BATCH_CANCEL_STATUS'=>0,	//批量下架
			'SHARING_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),
	'SHARINGCATEGORY_STATUS' =>array(	//分享分类状态码表
			'SHARINGCATEGORY_STATUS_FIELD'=>'sh_cg_status',	//状态在数据库里的字段
			'SHARINGCATEGORY_NORMAL_STATUS'=>'1',	//上架
			'SHARINGCATEGORY_CANCEL_STATUS'=>'0',	//下架
			'SHARINGCATEGORY_BATCH_CANCEL_STATUS'=>0,	//批量下架
			'SHARINGCATEGORY_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),

	'APP_STATUS' => array(	//应用状态码表
		'APP_STATUS_FIELD'=>'app_status',	//状态在数据库里的字段
		'APP_NORMAL_STATUS'=>'1',	//上架
		'APP_CANCEL_STATUS'=>'0',	//下架
		'APP_BATCH_CANCEL_STATUS'=>0,	//批量下架
		'APP_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),

	'FW_STATUS' => array(	//应用状态码表
		'FW_STATUS_FIELD'=>'fw_status',	//状态在数据库里的字段
		'FW_NORMAL_STATUS'=>'1',	//上架
		'FW_CANCEL_STATUS'=>'0',	//下架
		'FW_BATCH_CANCEL_STATUS'=>0,	//批量下架
		'FW_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),

	'RECOMMEND_STATUS' => array(	//应用状态码表
		'RECOMMEND_STATUS_FIELD'=>'rm_status',	//状态在数据库里的字段
		'RECOMMEND_NORMAL_STATUS'=>'1',	//上架
		'RECOMMEND_CANCEL_STATUS'=>'0',	//下架
		'RECOMMEND_BATCH_CANCEL_STATUS'=>0,	//批量下架
		'RECOMMEND_BATCH_RECOVER_STATUS'=>1,	//批量上架
	),

);
?>
