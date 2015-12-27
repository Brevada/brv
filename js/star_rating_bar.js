$(document).ready(function(){
	$(window).resize(function(){
		$('div.star').each(function(){
			$(this).css('height', $(this).outerWidth()+'px')
		});
	});
	$(window).resize();
});

function insertRating(val, id) {
	if(!$('#imdone').is(':visible')){
		$('#imdone').slideDown(125);
	}
	
	$.get("/overall/insert/insert_rating.php", { value : val, post_id : id });
    return false;
}
function disappearRating(post_id) {
	$("#aspect_"+post_id).addClass('rated');
}