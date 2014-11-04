<?php
//项目配置
if (!defined('THINK_PATH')) exit();
return array(
	//无需认证的超级管理员列表
	'SUPERVISOR_LIST'=> array(
		'UID',
		'voodoo',
	),
	//节点级别
	'NODELEVEL'=>array(
		1=>'Module',
		2=>'Action',
		3=>'function'
	),
	//安全验证过滤
	'FILTER'=>array(
		//模块，即对应的ACTION类
		'MODULE'=>array('Index','Menu','Message'),
		//行为，即对应ACTION下的FUNCTION, 映射页面与行为,key为页面,value为行为
		'ACTION'=>array(
			//exec 权限
			'index'=>'act,exec',
			'add'=>'doAdd,upImgToggleMergeWater',
			'edit'=>'doEdit,updateProduct,addProductImg,updateProductImg,upImgToggleMergeWater',
			'addCategory'=>'insertCategory',
			'editCategory'=>'updateCategory',
			'collection'=>'collect',
			'resetPassword'=>'reset'
		),
	)
);
?>