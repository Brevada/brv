$(document).ready(function(){
	$('.link').click(function(){
		window.location = '/'+$(this).data('link');
	});

	$('div.tokens > div.token').click(function(){
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
		} else {
			$(this).addClass('selected');
		}
		updateTokens();
	});
	
	$('#submit').click(function(){
		$('#frmAccount').submit();
	});
});

function updateTokens(){
	var tokens = [];
	$('div.tokens > div.token').each(function(){
		if($(this).hasClass('selected')){
			tokens.push($(this).data('tokenid'));
		}
	});
	$('#tokens').val(tokens.join(','));
}