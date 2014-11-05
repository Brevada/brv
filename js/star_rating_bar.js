$(document).ready(function(){
	$(window).resize(function(){
		$('div.star').each(function(){
			$(this).css('height', $(this).outerWidth()+'px')
		});
	});
	$(window).resize();
});

function insertRating(val, id, ip, country, user_id, reviewer) {
	$.get("/overall/insert/insert_rating.php?value=" + val + "&post_id=" + id + "&ipaddress=" + ip + "&country=" + country + "&user_id=" + user_id + "&reviewer=" + reviewer);
    return false;
}
function disappearRating(post_id) {
	$("#holder"+post_id).hide();
	$("#appear"+post_id).show();
}