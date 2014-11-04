/*
版权说明：

作者：Plant
类名：PageWin

创建：2014-02-12
版本号：1.0.0.0
*/

function PageWin(ParentContainer) {
    var _root = this;
    var pagewincover = null;
    var pagewinborder = null;
    var pagewin = null;
    var pagewintitle = null;
    var pagewinclose = null;
    var pagewincon = null;
    var pagewininfocover = null;
    var pagewininfo = null;

    this.IsDisposed = function () {
        return pagewin == null;
    }

    this.Config = {
        PageName: '窗口名称',
        WinWidth: 510,
        WinHeight: 460,
        WinImage: '',
        ConObject: null
    };

    this.Events = {
        Hide: function () { }
    }

    this.Show = function () {
        if (pagewin == null) {//height:" + (_root.Config.WinHeight - 2) + "px;
            pagewin = $(ParentContainer).append("<div class='PageWinCover'></div><div class='PageWinBorder' style='display:none;width:" + (_root.Config.WinWidth + 16) + "px; height:" + (_root.Config.WinHeight + 16) + "px;'></div>" +
                "<div class='PageWin' style='width:" + (_root.Config.WinWidth - 2) + "px; height:" + (_root.Config.WinHeight - 2) + "px;'>" +
                "<div class='PageWinTitle' style='background:url(" + _root.Config.WinImage + ") left center no-repeat; background-color:#f7f7f8; padding-left:10px;'>" + _root.Config.PageName + "</div>" +
                "<a href='javascript:void(0)' class='PageWinClose'></a>" +
                "<div class='PageWinCon' style='height:" + (_root.Config.WinHeight - 0) + "px;'></div>" +
                "<div class='PageWinInfoBoxCover' style='display:none;'></div><div class='PageWinInfoBox'style='display:none;'><div class='PageWinInfo'></div></div>" +
                "</div>").find(".PageWin:last");

            pagewincover = $(ParentContainer).find(".PageWinCover:last");
            pagewinborder = $(ParentContainer).find(".PageWinBorder:last");
            pagewintitle = $(ParentContainer).find(".PageWinTitle:last");
            pagewinclose = $(ParentContainer).find(".PageWinClose:last");
            pagewincon = $(ParentContainer).find(".PageWinCon:last");
            pagewininfocover = $(ParentContainer).find(".PageWinInfoBoxCover:last");
            pagewininfo = $(ParentContainer).find(".PageWinInfo:last");

            var ConObject = _root.Config.ConObject;
            if (ConObject != null) {
                var _pagewincon = ConObject.html();
                ConObject.html("");
                pagewincon.html(_pagewincon);
            }

            pagewinclose.bind("click", function () {
                _root.Hide();
                $("body").css("overflow", "auto");
                $(".bodydiv").css("padding-right", "0px");
                $(".topbody").css("display", "block");
            });

            $(window).bind("resize", this.OnResize);
            $(window).bind("scroll", this.OnResize);

            setTimeout(function () {
                pagewincon.find("input:first").focus();
            }, 500);

            this.OnResize();
        }
    }

    this.OnResize = function () {
        if (pagewin) {
            var WindowSize = {
                Width: $(window).width(),
                Height: $(window).height(),
                bHeight: $(window).height() < $(document).height() ? $(document).height() : $(window).height()
            };
            if (ParentContainer == "body") {
                pagewincover.css("left", 0);
                pagewincover.css("top", 0);
                pagewincover.css("width", WindowSize.Width);
                pagewincover.css("height", WindowSize.bHeight);
                pagewinborder.css("top", (WindowSize.Height - _root.Config.WinHeight) / 2 - 8);
                pagewinborder.css("left", (WindowSize.Width - _root.Config.WinWidth) / 2 - 8);
                //pagewin.css("top", (WindowSize.Height - _root.Config.WinHeight) / 2);
                pagewin.css("left", (WindowSize.Width - _root.Config.WinWidth) / 2);
            } else {
                pagewincover.css("left", _root.Config.Margin.Left);
                pagewincover.css("top", _root.Config.Margin.Top);
                pagewincover.css("width", $(ParentContainer).attr("clientWidth"));
                pagewincover.css("height", $(ParentContainer).attr("clientHeight"));
                pagewinborder.css("top", ($(ParentContainer).attr("clientHeight") - _root.Config.WinHeight) / 2 - 8);
                pagewinborder.css("left", ($(ParentContainer).attr("clientWidth") - _root.Config.WinWidth) / 2 - 8);
                //pagewin.css("top", ($(ParentContainer).attr("clientHeight") - _root.Config.WinHeight) / 2);
                pagewin.css("left", ($(ParentContainer).attr("clientWidth") - _root.Config.WinWidth) / 2);
            }
        }
    }

    this.Hide = function () {
        if (pagewin) {
            $(window).unbind("resize", this.OnResize);

            var ConObject = _root.Config.ConObject;
            if (ConObject != null) {
                ConObject.html(pagewincon.html());
            }

            pagewin.remove();
            pagewin = null;
            pagewincover.remove();
            pagewincover = null;
            pagewinborder.remove();
            pagewinborder = null;
            _root.Events.Hide();
        }
    }

    this.ShowInfo = function (info) {
        if (pagewininfo) {
            pagewininfo.html(info);
            pagewininfo.parent().show();
            pagewininfocover.show();
            setTimeout(function () {
                pagewininfocover.hide();
                pagewininfo.parent().hide();
            }, 1000);
        }
    }
}

function openForm(id, title, width, height, image) {

    $("body").css("overflow", "hidden");
    $(".bodydiv").css("padding-right", "16px");
    $(".topbody").css("display", "none");
    //提示窗口，格式： openForm('divForm','系统提示',400,280,'../images/title_lock.png')    
    var pagewin = new PageWin("body");
    pagewin.Config.ConObject = $("#" + id);
    pagewin.Config.PageName = title;
    pagewin.Config.WinWidth = width
    pagewin.Config.WinHeight = height;
    pagewin.Config.WinImage = image;
    pagewin.Show();

    var PageWinTop = $(".PageWin").css("top");
    PageWinTop = Number(PageWinTop.substring(0, PageWinTop.indexOf("p"))); //截取pagewintop值
    var top = document.documentElement.scrollTop || document.body.scrollTop; //当前滚动条值
    var height = $(window).height(); //当前浏览器高度
    $(".PageWin").css("top", PageWinTop + top + (height - 540) / 2 + "px"); //使pagewin保持在浏览器垂直居中
}


function openFormGroup(id, title, width, height, image) {

    $("body").css("overflow", "hidden");
    //$(".bodydiv").css("padding-right", "16px");
    $(".topbody").css("display", "none");
    //提示窗口，格式： openForm('divForm','系统提示',400,280,'../images/title_lock.png')    
    var pagewin = new PageWin("body");
    pagewin.Config.ConObject = $("#" + id);
    pagewin.Config.PageName = title;
    pagewin.Config.WinWidth = width
    pagewin.Config.WinHeight = height;
    pagewin.Config.WinImage = image;
    pagewin.Show();

    var PageWinTop = $(".PageWin").css("top");
    PageWinTop = Number(PageWinTop.substring(0, PageWinTop.indexOf("p"))); //截取pagewintop值
    var top = document.documentElement.scrollTop || document.body.scrollTop; //当前滚动条值
    var height = $(window).height(); //当前浏览器高度
    $(".PageWin").css("top", PageWinTop + top + (height - 540) / 2 + "px"); //使pagewin保持在浏览器垂直居中
}