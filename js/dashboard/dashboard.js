$(document).ready(function(){
	$('.link').css({'cursor' : 'pointer'}).click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('body').on('click', 'div.message-container div.close', function(){
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
	bdff.faces['live'].attach();
	bdff.faces['milestones'].attach();
	bdff.faces['aspects'].attach();
	
	bdff.persistent('hoverpod');
	bdff.face('aspects');
});