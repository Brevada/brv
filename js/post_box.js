function submitRating(post_id, ip, country, reviewer, doReload) {
	$("#rating_" + post_id).hide();
	$("#button_" + post_id).hide();
	
	var rating = $("#rating" + post_id).val();
	$.post("/overall/insert/insert_rating.php?post_id="+post_id+"&ipaddress="+ip+"&country="+country+"&reviewer="+reviewer, { value: rating }, function(){
		$("#thanks_" + post_id).css('display', 'block');
	});
	if(doReload){
	   location.reload(); 
	}
}