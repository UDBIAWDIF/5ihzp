/**
 *	@Author: Aoki
 *  @Date: 2010年5月
 *
 */

var contentDialog = '';
var isShiftKeyDown = false;
var isCtrlKeyDown = false;
function success(content){
	msgShow(1,content,true);
}

function error(content){
	msgShow(0,content,true);
}

function msgShow(status,content,autoClose,id){
	var timer;
	var icon ='success.gif';
	if(status != 1){
		var icon = 'error.gif';
	}
	$.dialog({
		icon: icon,
		content: content,
		id: id || false,
		init: function () {
			var that = this, i = 5;
			var aotuclosefn = function () {
				that.title(i + '秒后关闭');
				!i && that.close();
				i --;
			};
			if(autoClose) {
				timer = setInterval(aotuclosefn, 1000);
				aotuclosefn();
			}
		},
		close: function () {
			clearInterval(timer);
		}
	});
}//end msgShow()

function clearCache(action){
	action = action || '/Public/clearCache';
	// if(!action) {action = '/Public/clearCache';}
	$.dialog.confirm('确定要清除吗？', function(){
		$.ajax({
			type: 'GET',
			url: APP + action,
			dataType: "json",
			beforeSend:loading,
			success: function(msg){
				tips.close();
				if(msg.status){
					var message = '清除成功！';
					if(msg.info) {message = msg.info;}
					success(message);
				}
			}
		});
	}, function(){

	});
}

function serializeSubmitForm(formobj, event, successMsg) {
	var r = $(formobj).checkForm(true);
	if(r){
		$.ajax({
			type: $(formobj).attr('method'),
			url: $(formobj).attr('action'),
			data: $(formobj).serialize(),
			dataType: "json",
			beforeSend:loading,
			success: function(msg){
				tips.close();
				if(msg.status){
					if(successMsg) {
						success(successMsg);
					} else {
						if($(formobj).hasClass('noReflesh')) {
							var _triggerfn = $(formobj).attr('triggerfn');
							if(_triggerfn) {
								eval(_triggerfn + '()');
							}
							success('提交成功！');
							parent.success('提交成功！');
							parent.contentDialog.close();
						} else {
							if(msg.url){
								parent.location = msg.url;
							}else{
								parent.location.reload();
							}
						}
					}
				}else{
					error(msg.info);
				}
			}
		});
	}
	event.preventDefault();
}

//在keditor不为空时写入详细描述
function writeAppDetail(ke, content) {
	if(ke.isEmpty()) {
		ke.text(content);
	}
}//end writeAppDetail()

//判断是否可能是个标准URL链接
function isUrl(url) {
	var key = '://';
	var position = url.indexOf(key);
	if(position != -1 && position < 6) {
		return true;
	} else {
		return false;
	}
}

function toKB(num) {
	num = num / 1024;
	return num.toFixed(2);
}

//如果 url 不是标准的 url ,就加上域名
function realUrl(url) {
	var type = arguments[1] ? arguments[1] : 'img';
	url = url.toString();
	if(!isUrl(url)) {
		switch (type) {
			case 'soft' :
				url = thisSoftServerUrl + url;
				break;
			case 'img' :
				url = thisImgServerUrl + url;
				break;
		}
	}
	return url;
}

var tips = '';
function loading(){
	tips = $.dialog.tips('数据加载中...',600,'loading.gif');
}

$(document).ready(function(){
	$(document).keydown(function(event) {
		//event.ctrlKey，event.shiftKey，event .altKey
		switch(event.keyCode) {
			case 16:
				isShiftKeyDown = true;
				break;
			case 17:
				isCtrlKeyDown = true;
				break;
		}
	}).keyup(function(event) {
		switch(event.keyCode) {
			case 16:
				isShiftKeyDown = false;
				break;
			case 17:
				isCtrlKeyDown = false;
				break;
		}
	});

	//$("#ps").show().click(function(){$("#ps").fadeOut("slow");});
	$("a").attr("hidefocus",true);
	//表格相关
	$("tbody tr:odd").addClass("odd");
	$("tbody tr").hover(
		function(){
			$(this).addClass("hover");
		}, function(){
			$(this).removeClass("hover");
		}
	);

	/*
	$(".grid tr").click(function(){
		var node = $(this);
		if (node.find("input[name='id[]']").attr("checked")) {
			node.find("input[name='id[]']").attr("checked", false);
			node.removeClass("checked");
		} else {
			node.find("input[name='id[]']").attr("checked", true);
			node.addClass("checked");
		}
	});
	*/

	$("input[name='id[]']").click(function(){
		if($(this).attr("checked")){
			$(this).attr("checked", true);
			$(this).parent().parent().addClass("checked");
		}else{
			$(this).attr("checked", false);
			$(this).parent().parent().removeClass("checked");
		}
	});

	$("#submitButton").click(function(){
		return confirm('确定提交吗');
	});

	$(".confirm").click(function(e){
		var $handle = $(this);
		$.dialog.confirm('确定要删除吗？删除后将不能恢复！', function(){
			$.ajax({
				type: 'GET',
				url: $handle.attr('href'),
				dataType: "json",
				beforeSend:loading,
				success: function(msg){
					tips.close();
					if(msg.status){
						if(msg.url){
							location = msg.url;
						}else{
							location.reload();
						}
					}else{
						error(msg.info);
					}
				}
			});
		}, function(){

		});
		return false;
	});

	//表格相关 ----  全选操作
	$("#selectAll").click(function() {
		if ($(this).attr("checked")) {
			$("input[name='id[]']").each(function() {
				$(this).attr("checked", true);
				$(this).parent().parent().addClass("checked");
			});
		}else {
			$("input[name='id[]']").each(function() {
				$(this).attr("checked", false);
				$(this).parent().parent().removeClass("checked");
			});
		}
	});

	$("td:contains('*') :input").parent().css('color','red');
	$("th:contains('*') :input").parent().css('color','red');
	$(".Wdate").attr('autocomplete','off');

	/*对话框*/
	$(".dialog").live('click',function(e){
		if(isCtrlKeyDown || isShiftKeyDown) {
			isCtrlKeyDown = false; isShiftKeyDown = false;
			return true;
		}

		var title= $(this).val();
		var content= 'url:'+$(this).attr('href');
		var width=$(this).attr('w');
		var height=$(this).attr('h');
		var top = $(this).attr('t')||'30%';
		var left = $(this).attr('l')||'30%';
		var isMax = $(this).attr('isMax')||false;
		if(isMax){
			contentDialog = $.dialog({id: 'appcontent',title:title, content:content, lock:true}).max();
		}else{
			contentDialog = $.dialog({id: 'appcontent',title:title, content:content, lock:true, left:left, top:top,width:width,height:height});
		}
		e.preventDefault();
	});

	$("a.ajaxTodo").click(function(){
		$.dialog({
			time: 2,
			icon: 'success.gif',
			content: '我可以定义消息图标哦'
		});
		return false;
	});
	$('#mainform').checkForm();
	$('#mainform').submit(function(event) {
		serializeSubmitForm(this, event);
	});

	$('.btnClose').click(function(){
		parent.contentDialog.close();
		return false;
	});

	/* 2012-08-09 table th*/
	var _sort = $('tr.sortable').attr('sort');
	var _sortImg = $('tr.sortable').attr('sortImg');
	var _currentOrder = $('tr.sortable').attr('currentOrder');
	var _params = $('tr.sortable').attr('params');
	var _action_name =  $('tr.sortable').attr('action_name');
	if(('undefined' == typeof(_action_name)) || !_action_name) {
		_action_name = SELF;
	}
	_action_name += '/';
	var _sortThHtml = '<a href="'+_action_name + '"><span class="fl"></span><span class="sort_by asc"><i class="icon-chevron-up"></i></span><span class="sort_by desc"><i class="icon-chevron-down"></i></span></a>';

	$('tr.sortable th').each(function(i,n){
		var _order = $(n).attr('order');
		if(_order){
			var _name = $(n).html();
			$(n).html(_sortThHtml);
			$(n).find('.fl').html(_name);
			var _href = $(n).find('a').attr('href');
			_href+= '_order/'+_order+'/_sort/'+_sort+'/_params/'+_params;
			$(n).find('a').attr('href',_href);
			if(_order == _currentOrder){
				$(n).find('.sort_by.'+_sortImg).show();
			}
		}
	});

	//ajax 设置排序值
	var src_order_value;
	$(".ajaxOrder").keydown(function(event) {
		var keyCode = event.which;
		if ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 37 || keyCode == 39 || keyCode == 46 || (keyCode >= 96 && keyCode <= 105)) {
			return true;
		} else { return false }
	}).focus(function() {
		src_order_value = $(this).val();
		this.style.imeMode = 'disabled';
	}).blur(function() {
		var cur_value = $(this).val();
		if(src_order_value != cur_value) {
			$.get($(this).attr('setOrderUrl') + "/id/" + $(this).attr('refId') + "/order/" + cur_value);
		} else {
			// console.log('no change!');
		}
	});

});