(function($){
	$.fn.tip = function(opt){
		var options = $.extend({}, $.fn.tip.defaults, opt||{});
		init(options);
		this.mouseover(function(e){
			this.selftip=this.title;
			this.title="";
			if(this.selftip!="")
			showTip(e,this.selftip,options.opacity);
		}).mouseout(function(){
			this.title = this.selftip;
			tipdiv.hide().children(":first").html("");
		}).mousemove(function(e){
			showTip(e);
		});
	};

	$.fn.tip.defaults={
		maxwidth: 300,							//最大宽度
		opacity: 0.8,							//透明度
		background: "#006CDB",					//背景样式
		border: "#FEFFD4 solid 1px",			//边框样式
		contentcolor: "#FFFFFE",				//正文色
		font: "12px verdana,arial,sans-serif"	//字体样式
	};

	var tipdiv=null;

	function showTip(evt,selftip,opacity){
		if(selftip){//仅用于over方法
		tipdiv.children(":first").html(selftip);
		tipdiv.css({
			"opacity":opacity,
			"top":(evt.pageY+20)+"px",
			"left":(evt.pageX+10)+"px"
		}).show();
		}else{
			tipdiv.css({
				"top":(evt.pageY+20)+"px",
				"left":(evt.pageX+10)+"px"
			});
		}
	}

	function init(options){
		var tipobj=$("#jq_bbf_tipdiv");
		if(tipobj.length==0){
			tipobj=[];
			tipobj.push("<div id=\"jq_bbf_tipdiv\" style=\"display:none;position:absolute;z-index:1000;max-width:");
			tipobj.push(options.maxwidth);
			tipobj.push("px;background:");
			tipobj.push(options.background);
			tipobj.push(";width:auto!important;width:auto;border:");
			tipobj.push(options.border);
			tipobj.push(";text-align:left;padding:3px;\"><p style=\"margin:0;padding:0;color:");
			tipobj.push(options.contentcolor);
			tipobj.push(";font:");
			tipobj.push(options.font);
			tipobj.push(";\"></p></div>");
			tipobj=$(tipobj.join("")).appendTo("body");
		}
		tipdiv=tipobj;
		tipobj=null;
	};
})(jQuery);

//通用弹出提示文本信息
jQuery.extend(jQuery, {

	// jQuery UI alert弹出提示
	jqalert: function (text, title, fn) {
		var html =
	'<div class="dialog" id="dialog-message">' +
	'	<p>' +
	'		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 0 0;"></span>' + text +
	'	</p>' +
	'</div>';
		return $(html).dialog({
			//autoOpen: false,
			resizable: false,
			modal: true,
			show: {
				effect: 'fade',
				duration: 300
			},
			title: title || "提示信息",
			buttons: {
				"确定": function () {
					var dlg = $(this).dialog("close");
					fn && fn.call(dlg);
				}
			}
		});
	},

	// jQuery UI alert弹出提示,一定间隔之后自动关闭
	jqtimeralert: function (text, title, fn, timerMax) {
		var dd = $(
	'<div class="dialog" id="dialog-message">' +
	'	<p>' +
	'		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 0 0;"></span>' + text +
	'	</p>' +
	'</div>');
		dd[0].timerMax = timerMax || 3;
		return dd.dialog({
			//autoOpen: false,
			resizable: false,
			modal: true,
			show: {
				effect: 'fade',
				duration: 300
			},
			open: function (e, ui) {
				var me = this,
			dlg = $(this),
			btn = dlg.parent().find(".ui-button-text").text("确定(" + me.timerMax + ")");
				--me.timerMax;
				me.timer = window.setInterval(function () {
					btn.text("确定(" + me.timerMax + ")");
					if (me.timerMax-- <= 0) {
						dlg.dialog("close");
						fn && fn.call(dlg);
						window.clearInterval(me.timer); // 时间到了清除计时器
					}
				}, 1000);
			},
			title: title || "提示信息",
			buttons: {
				"确定": function () {
					var dlg = $(this).dialog("close");
					fn && fn.call(dlg);
					window.clearInterval(this.timer); // 清除计时器
				}
			},
			close: function () {
				window.clearInterval(this.timer); // 清除计时器
			}
		});
	},

	// jQuery UI confirm弹出确认提示
	jqconfirm: function (text, title, fn1, fn2) {
		var html =
	'<div class="dialog" id="dialog-confirm">' +
	'	<p>' +
	'		<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>' + text +
	'	</p>' +
	'</div>';
		return $(html).dialog({
			//autoOpen: false,
			resizable: false,
			modal: true,
			show: {
				effect: 'fade',
				duration: 300
			},
			title: title || "提示信息",
			buttons: {
				"确定": function () {
					var dlg = $(this).dialog("close");
					fn1 && fn1.call(dlg, true);
				},
				"取消": function () {
					var dlg = $(this).dialog("close");
					fn2 && fn2(dlg, false);
				}
			}
		});
	},

	// jQuery UI 弹出iframe窗口
	jqopen: function (url, options) {
		var html =
	'<div class="dialog" id="dialog-window" title="提示信息">' +
	' <iframe src="' + url + '" frameBorder="0" style="border: 0; " scrolling="auto" width="100%" height="100%"></iframe>' +
	'</div>';
		return $(html).dialog($.extend({
			modal: true,
			closeOnEscape: false,
			draggable: false,
			resizable: false,
			close: function (event, ui) {
				$(this).dialog("destroy"); // 关闭时销毁
			}
		}, options));
	},

	// jQuery UI confirm提示
	confirm: function (evt, text, title) {
		evt = $.event.fix(evt);
		var me = evt.target;
		if (me.confirmResult) {
			me.confirmResult = false;
			return true;
		}
		jQuery.jqconfirm(text, title, function (e) {
			me.confirmResult = true;
			if (e) {
				if (me.href && $.trim(me.href).indexOf("javascript:") == 0) {
					$.globalEval(me.href);
					me.confirmResult = false;
					return;
				}
				var t = me.type && me.type.toLowerCase();
				if (t && me.name && (t == "image" || t == "submit" || t == "button")) {
					__doPostBack(me.name, "");
					me.confirmResult = false;
					return;
				}
				if (me.click) me.click(evt);
			}
			return false;
		});
		return false;
	}
});

var tips = '';
var triggerFuncs = {};

function serializeSubmitForm(formobj, event, successMsg) {
	var checkResult		= $(formobj).checkForm(true);
	var submitResult	= false;
	if(checkResult) {
		$.ajax({
			type:		$(formobj).attr('method'),
			url:		$(formobj).attr('action'),
			data:		$(formobj).serialize(),
			dataType:	"json",
			beforeSend:	loading,
			success:	function(msg) {
				tips.close();
				submitResult = msg.status;
				if(submitResult) {
					var successtip = $(formobj).attr('successtip');
					if(successtip) {
						alert(successtip);
					} else if(!$(formobj).attr('notip')){
						alert('操作成功!');
					}

					var runnable = true;
					var jumpUrl = $(formobj).attr('jumpurl');
					var isParentJump = $(formobj).attr('parentjump');
					if(jumpUrl) {
						if(isParentJump) {
							window.parent.location = jumpUrl;
						} else {
							window.location = jumpUrl;
						}
						runnable = false;
					}

					if(runnable && $(formobj).attr('freshown')) {
						window.location.reload();
						runnable = false;
					}

					if(runnable && $(formobj).attr('parentrefresh')) {
						window.parent.location.reload();
						runnable = false;
					}

					var successFunc = $(formobj).attr('successfunc');
					if(runnable && successFunc) {
						triggerFuncs[successFunc]();
						runnable = false;
					}
				} else {
					alert(msg.info);
				}
			}
		});
	}
	event.preventDefault();
	return submitResult;
}

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

function checkNologinAndHandle(response) {
	if(noLoginCode == response.code) {
		$('#showreglogin').triggerHandler('click');
		return false;
	}
	return true;
}

function loading(){
	tips = $.dialog.tips('数据加载中...',600,'loading.gif');
}

function getInviteUserIds(jqselect, inputname) {
	var userIds = '';
	$(jqselect).each(function(idx) {
		userIds += $(this).attr('uid') + ',';
	});
	$('input[name="' + inputname + '"]').val(userIds);
}

function animateHide(objToHide) {
	objToHide.animate(
		{height: 'toggle', opacity: 'toggle'},
		1000,
		'linear',
		function() {
			objToHide.detach();
		}
	);
}

// 整理拼音分组
function userSortToPyGroup() {
	var parnEn = /[A-Z]/;
	$('.listzi').each(function() {
		userPinYin = $(this).attr('upy');
		if(userPinYin.length && parnEn.exec(userPinYin[0])) {
			$(this).parents('.groupul').find('.pygroup[pygroup="' + userPinYin[0] + '"]').after(this);
		} else {
			$(this).parents('.groupul').find('.pygroup[pygroup="Other"]').after(this);
		}
	});

	$('.pygroup').each(function() {
		var thisObj = $(this);
		var nextObj = thisObj.next();
console.log(thisObj.attr('pygroup'));
console.log(nextObj.length);
console.log(nextObj.hasClass('listzi'));
console.log(nextObj.is(':visible'));
console.log('-===========================================-');
		if(!nextObj.length || !nextObj.hasClass('listzi') || !nextObj.is(':visible')) {
			thisObj.hide();
		} else {
			thisObj.show();
		}
	});
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

	$('.ajaxForm').submit(function(event) {
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

	$('.piclistparent').on('click', '.clickheart', function(event) {
		var thumbObj = $(event.currentTarget);
		$.ajax({
			type:		'post',
			url:		thumbObj.attr('href'),
			data:		{},
			dataType:	"json",
			success:	function(msg) {
				if(msg.status) {
					var addNum		= msg.data;
					var countArea	= thumbObj.find('label');
					var oldCount	= countArea.text();
					countArea.text(parseInt(oldCount) + addNum);

					var	left	= parseInt(thumbObj.offset().left)+10,
						top		= parseInt(thumbObj.offset().top)-10;
					var tipNum = addNum == 1 ? '+1' : '-1';
					thumbObj.append('<div class="thumbupanimate"><b>' + addNum + '<\/b></\div>');
					thumbObj
						.find('.thumbupanimate')
						.css({'position':'absolute', 'z-index':'1', 'color':'#C30', 'left':left+'px', 'top':top+'px'})
						.animate({top:top-10,left:left+10},2000,function() {
							$(this).fadeIn('fast').remove();
						});
				} else {
					checkNologinAndHandle(msg);
				}
			}
		});
		event.preventDefault();
		return false;
	});

	$('.piclistparent,.headgroup').on('click', '.likeuser', function(event) {
		var likeObj = $(event.currentTarget);
		var likeUid = likeObj.attr('uid');
		$.ajax({
			type:		'post',
			url:		likeObj.attr('href'),
			data:		{},
			dataType:	"json",
			success:	function(msg) {
				if(msg.status) {
					$focusText = 'add' == msg.info ? '已关注' : '关 注';
					// likeObj.text($focusText);
					// likeObj.removeAttr('href');
					$('.likeuser[uid=' + likeUid + ']').text($focusText);
				} else {
					checkNologinAndHandle(msg);
				}
			}
		});
		event.preventDefault();
		return false;
	});

	$('.piclistparent').on('submit', '.commentform', function(event) {
		var formobj = $(event.currentTarget);
		$.ajax({
			type:		formobj.attr('method'),
			url:		formobj.attr('action'),
			data:		formobj.serialize(),
			dataType:	"json",
			beforeSend:	loading,
			success:	function(data) {
				tips.close();
				if(data.status) {
					var commentlist = '.commentlist[picid="' + formobj.attr('picid') + '"]';
					$(commentlist).prepend(data.info);
				} else {
					alert(data.info);
				}
			}
		});
		event.preventDefault();
		return false;
	});

	var groupul = $('.groupul');
	var enLetterStart = 'A'.charCodeAt();
	if(groupul.length) {
		// 拼音转大写方便匹配
		$('.listzi').each(function() {
			userPinYin = $(this).attr('upy').toLocaleUpperCase();
			$(this).attr('upy', userPinYin);
		});

		// 添加拼音分组标签
		for(enLetter = 0; enLetter < 26; enLetter++) {
			var enChar = String.fromCharCode(enLetter + enLetterStart);
			var enLetterLi = '<li class="paddingLR10 pygroup" pygroup="' + enChar + '"><label class="fontcolor4c9ed9">' + enChar + '</label></li>';
			$('.groupul').append(enLetterLi);
			// break;
		}
		$('.groupul').append('<li class="paddingLR10 pygroup" pygroup="Other"><label class="fontcolor4c9ed9">Other</label></li>');
		userSortToPyGroup();
	}

	$('.friendsselect').on('dblclick', '.listzi', function(event) {
		var userli = $(event.currentTarget);
		$('.inviteselect').append(userli);
		getInviteUserIds('.inviteselect > .listzi', 'userids');
		userSortToPyGroup();
	});

	$('.inviteselect').on('dblclick', '.listzi', function(event) {
		var userli = $(event.currentTarget);
		$('.friendsselect').append(userli);
		getInviteUserIds('.inviteselect > .listzi', 'userids');
		userSortToPyGroup();
	});

	$('.inputsearch').keyup(function(event) {
		var inputObj = $(this);
		var input = inputObj.val().toLocaleUpperCase();
		var userListObj = inputObj.parent().parent().next('ul');
		var userLi = userListObj.children('.listzi');
		userLi.each(function() {
			userPinYin = $(this).attr('upy');
			if(-1 != userPinYin.indexOf(input)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		userSortToPyGroup();
	});

	var regionLevel1 = $('.regionlevel[level="1"]');
	// 根据 IP 选定省级列表
	if(typeof(userLocationByIp) != "undefined") {
		var city = userLocationByIp.split(' ');
		var region1_id = 0;
		regionLevel1.find('option').each(function(optionIdx) {
			if(!region1_id && city[0].indexOf($(this).text())>=0) {
				region1_id = $(this).attr("value");
			}
		});
		if(region1_id) {
			regionLevel1.val(region1_id);
		}
	}
	regionLevel1.change(function() {
		var thisObj = $(this);
		var regionId = thisObj.val();
		var option = '<option value="0">请选择城市</option>';
		var regionLevel2 = $('.regionlevel[level="2"]');
		if('0' != regionId) {
			$.get(
				thisObj.attr('action'),
				{id: regionId},
				function(data) {
					for(i = 0; i< data.length; i++) {
						option += '<option value="' + data[i].region_id + '">' + data[i].region_name + '</option>';
					}
					regionLevel2.html(option);
					if(typeof(userRegionId) != "undefined") {
						regionLevel2.val(userRegionId);
						// regionLevel2[0].value = userRegionId;
					}

					// 根据 IP 选定市级列表
					if(typeof(userLocationByIp) != "undefined") {
						var city = userLocationByIp.split(' ');
						var region2_id = 0;
						regionLevel2.find('option').each(function(optionIdx) {
							if(!region2_id && city[0].indexOf($(this).text())>=0) {
								region2_id = $(this).attr("value");
							}
						});
						if(region2_id) {
							regionLevel2.val(region2_id);
						}
					}
				},
				'json'
			);
		} else {
			regionLevel2.html(option);
		}
	});
	regionLevel1.triggerHandler('change');

	if(typeof(checkedTag) != "undefined") {
		$('input[name="' + checkboxTagName + '"]').each(function(idx) {
			$match = ',' + this.value + ',';
			if(checkedTag.indexOf($match) >= 0) {
				this.checked = true;
			}
		});
	}

	// $(".usermsg, .messageCount").colorbox({
		// width: "650px"
	// });

	$('.msgchecked, .msgpass').click(function() {
		var thisObj = $(this);
		$.getJSON(
			thisObj.attr('apilink'),
			{},
			function(data) {
				animateHide(thisObj.parent());
			}
		);
	});

	$(window).scroll(function() {
		var offset = $(document).scrollTop();
		var offsetBottom = offset + ($(window).height() - 500);
		var index_cate_div = $('#index_cate_div');
		index_cate_div.animate({
			top: offsetBottom
		},
		{
			duration: 500,
			queue: false
		});
	});

});
