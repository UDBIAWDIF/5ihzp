/**
 *	@Author: Aoki
 *  @Date: 2010年5月
 *
 */
$(document).ready(function(){
	$(".navs li a").click(function(){
		var node = $(this);
		$(".navs li a").each(function() {
			$(this).removeClass("current");
		});
		node.addClass("current");
	});
});
