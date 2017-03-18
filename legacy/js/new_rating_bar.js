$(document).ready(function(){
	$("div.ratingsquare").hover(function(){
		$(this).find(".text_holder").show();
		$(this).find(".overholder").show();
		$(this).find(".text_holder").text($(this).attr('squarenum'));
	}, function(){
		$(this).find(".text_holder").hide();
		$(this).find(".overholder").hide();
		$(this).find(".text_holder").text('');
	});
});

function insertRating(val, id, ip, country, user_id, reviewer) {
	$.get("/overall/insert/insert_rating.php?value=" + val + "&post_id=" + id + "&ipaddress=" + ip + "&country=" + country + "&user_id=" + user_id + "&reviewer=" + reviewer);
    return false;
}
function disappearRating(post_id) {
	$("#holder"+post_id).hide();
	$("#appear"+post_id).show();
}