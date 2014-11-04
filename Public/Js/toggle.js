$(document).ready(function(){
	$(".toggle .thumb").live('click', function(){
		if($(this).parent().hasClass('On')){
			$(this).parent().removeClass('On');
			$(this).parent().find('input').val('0');
		}else{
			$(this).parent().addClass('On');
			$(this).parent().find('input').val('1');
		}
	});
});
