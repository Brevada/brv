$(document).ready(function(){
	$('.link').css({'cursor' : 'pointer'});

	$('.link').click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('div.message-container div.close').click(function(){
		$(this).parent().slideUp();
	});
	
	$('.pod .graph').each(function(){
		var percent = $(this).data('percent');
		var original = $(this).height();
		var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
		$(this).animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
	});
});