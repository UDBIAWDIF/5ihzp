<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

class MonthPage {
	// 分页栏每页显示的页数
	public $rollPage = 5;
	// 页数跳转时要带的参数
	public $parameter;
	// 默认列表每页显示行数
	public $listRows = 20;
	// 起始行数
	public $firstRow;
	// 输入页码的输入框ID
	public $inputId = 'MonthPage';

	// 月份模型
	public $monthModel = 'PicMonth';
	// 用户ID
	public $userId = null;
	// 用户ID字段
	public $userIdField = 'pm_user_id';
	// 月份字段
	public $monthField = 'pm_date';
	// 关联表的月份字段
	public $relationMonthField = 'pic_format_createtime';
	// 分布不使用LIMIT
	public $disableLimit = true;

	// 当前月
	protected $curMonth = '0000-00';
	// 分页总页面数
	protected $totalPages;
	// 总行数
	protected $totalRows;
	// 当前页数
	protected $nowPage;
	// 分页的栏的总页数
	protected $coolPages;
	// 分页显示定制
	protected $config =	array('prev'=>'<','next'=>'>','first'=>'第一页','last'=>'最后一页','input'=>'页码:',
					'theme'=>' %nowPage%/%totalPage% 页 %first%  %prePage%  %upPage% %linkPage% %downPage%  %nextPage%  %end% %inputPage%');
	// 默认分页变量名
	protected $varPage;

	/**
	 +----------------------------------------------------------
	 * 架构函数
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param Int $userId		用户ID
	 * @param Int $listRows		每页显示记录数
	 * @param Mix $parameter	分页跳转的参数
	 +----------------------------------------------------------
	 */
	public function __construct($userId, $listRows='',$parameter='') {
		// 初始化操作只做属性赋值, 不进行其它操作
		$this->userId = intval($userId);
		$this->init($listRows, $parameter);
	}

	public function init($listRows='', $parameter='') {
		$this->listRows		= intval($listRows);
		$this->parameter	= $parameter;
		$this->varPage		= C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
	}

	public function condition() {
		// 需要提供当前月的 LIKE 语句
		return array(
			trim($this->relationMonthField) => array('LIKE', $this->curMonth . '%'),
		);
	}

	// 计算分页数据
	public function calcPages() {
		// 根据用户ID统计出总页数
		$model = $this->_model();
		$userId = intval($this->userId);
		if(empty($userId)) {
			$model->group($this->monthField);
		} else {
			$map = array(trim($this->userIdField) => $userId);
			$model->where($map);
		}
		$this->totalPages	= $model->count();
		$this->coolPages	= ceil($this->totalPages/$this->rollPage);
		$this->nowPage		= !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
		if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
			$this->nowPage	= $this->totalPages;
		}
		$this->firstRow		= $this->listRows*($this->nowPage-1);

		// 根据 $this->nowPage 找到当前页码代表的月份
		if(empty($userId)) {
			$model->group($this->monthField);
		} else {
			$map = array(trim($this->userIdField) => $userId);
			$model->where($map);
		}
		$model->order($this->monthField . ' DESC');
		$model->field($this->monthField);
		$model->limit($this->nowPage - 1, 1);
		$month = $model->select();	// find() 不支持 limit(), 因为在基类里 find() 再次调用 limit(),所以覆盖了
		$this->curMonth = substr($month[0][$this->monthField], 0, 7);
	}

	public function pageNo() {
		return str_replace('-', '年', $this->curMonth) . '月';
	}

	public function setConfig($name,$value) {
		if(isset($this->config[$name])) {
			$this->config[$name]	=   $value;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 分页显示输出
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 */
	public function show() {
		if(0 == $this->totalPages) return '';
		$p = $this->varPage;
		$nowCoolPage	  = ceil($this->nowPage/$this->rollPage);
		$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
		$parse = parse_url($url);
		if(isset($parse['query'])) {
			parse_str($parse['query'],$params);
			unset($params[$p]);
			$url   =  $parse['path'].'?'.http_build_query($params);
		}
		//上下翻页字符串
		$upRow   = $this->nowPage-1;
		$downRow = $this->nowPage+1;
		if ($upRow>0){
			$upPage="<a href='".$url."&".$p."=$upRow'>".$this->config['prev']."</a>";
		}else{
			$upPage="";
		}

		if ($downRow <= $this->totalPages){
			$downPage="<a href='".$url."&".$p."=$downRow'>".$this->config['next']."</a>";
		}else{
			$downPage="";
		}
		// << < > >>
		if($nowCoolPage == 1){
			$theFirst = "";
			$prePage = "";
		}else{
			$preRow =  $this->nowPage-$this->rollPage;
			$prePage = "<a href='".$url."&".$p."=$preRow' >上".$this->rollPage."页</a>";
			$theFirst = "<a href='".$url."&".$p."=1' >".$this->config['first']."</a>";
		}
		if($nowCoolPage == $this->coolPages){
			$nextPage = "";
			$theEnd="";
		}else{
			$nextRow = $this->nowPage+$this->rollPage;
			$theEndRow = $this->totalPages;
			$nextPage = "<a href='".$url."&".$p."=$nextRow' >下".$this->rollPage."页</a>";
			$theEnd = "<a href='".$url."&".$p."=$theEndRow' >".$this->config['last']."</a>";
		}
		// 1 2 3 4 5
		$linkPage = "";
		for($i=1;$i<=$this->rollPage;$i++){
			$page=($nowCoolPage-1)*$this->rollPage+$i;
			if($page!=$this->nowPage){
				if($page<=$this->totalPages){
					$linkPage .= "&nbsp;<a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a>";
				}else{
					break;
				}
			}else{
				if($this->totalPages != 1){
					$linkPage .= "&nbsp;<span class='current'>".$page."</span>";
				}
			}
		}
		//直接输入页码
		$inputPage = '';
		$inputPage .= $this->config['input'] . '<input class="input-mini" size=3 type=text id="' . $this->inputId .'" />';
		$inputPage .= "<a href=# id='{$this->inputId}Button'>Go</a>";
		$inputPage .= <<< jQuery

<SCRIPT LANGUAGE="JavaScript">
	var {$this->inputId}_url = "{$url}&" + "{$p}=";
	$(function() {
		$("#{$this->inputId}Button").bind('click', function() {
			window.location.href = {$this->inputId}_url + $("#{$this->inputId}").val();
		});
	});
</SCRIPT>

jQuery;
		$inputPage	= '';

		$pageStr	= str_replace(
			array('%nowPage%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%','%inputPage%'),
			array($this->nowPage,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd,$inputPage),$this->config['theme']);
		return $pageStr;
	}

	protected function _model() {
		if(is_string($this->monthModel)) {
			$this->monthModel = D($this->monthModel);
		}
		return $this->monthModel;
	}

}