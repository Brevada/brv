$(document).ready(function(){
	$('.link').css({'cursor' : 'pointer'}).click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('div.message-container div.close').click(function(){
		$(this).parent().slideUp();
	});
});

$(document).ready(function(){	
	$('.toggle-button').click(function(){
		bdff.face($(this).attr('data-id'));
	});
	
	bdff.callbacks.rendered = function(face){
		$('.toggle-button').removeClass('selected');
		$('.toggle-button[data-id=' + face.label + "]").addClass('selected');
		$(window).scrollTop(0);
	};
	
	bdff.canvas('#main-container');
	bdff.faces['hoverpod'].attach('body');
	
	//bdff.face('aspects');
	bdff.persistent('hoverpod');
});