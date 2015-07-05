$(document).ready(function(){
	$('.link').click(function(){
		window.location = '/'+$(this).data('link');
	});

	/*
	$(window).resize(function(){
		correctAspectPositioning();
	});
	$(window).resize();
	*/
	
	$('.pod .graph').each(function(){
		var percent = $(this).data('percent');
		var original = $(this).height();
		var target = (parseFloat(percent)/100)*($(this).parent().height() - original);
		$(this).animate({ height : Math.min(Math.floor(original+target), $(this).parent().height()) }, 1500);
	});
});

/* Perhaps replace with a few HTML aspect-container's. Since it will only be 1 or 2 fillers with the current max-width. */
/*
function correctAspectPositioning(){
	var rowWidth = $('div.aspect-area > div.row').width();
	var aspectWidth = $('div.aspect-container').outerWidth(true);
	var maxAspectsPerRow = Math.floor(rowWidth / aspectWidth);
	var numOfFillers = $('div.aspect-container').length % maxAspectsPerRow;
	numOfFillers = maxAspectsPerRow - numOfFillers - $('div.aspect-filler').length;
	for(var i = 0; i < numOfFillers; i++){
		$('div.aspect-area > div.row').append($('<div class="aspect-container aspect-filler">'));
	}
}
*/