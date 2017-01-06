var timer;
var rated = false;

$(document).ready(function(){
	$(document.body).bind("mousemove keypress touchstart", function(){
		if(typeof timer !== 'undefined'){
			clearTimeout(timer);
			timer = setTimeout('refresh()', 3000);
		}
	});
});

function refresh() {
	if(!rated){
		timer = setTimeout('refresh()', 3000);
		return;
	}
	window.location.reload(true);
}

function message_show() {
	$("#message_box").show();
	$("#message_button").hide();
}

function rate(val, postid, ip, countr, userid){
	rated = true;
	$("#buttons"+postid).hide();
	$("#thanks_"+postid).show();
	$.post("/overall/insert/insert_rating.php", {
		value: val,
		post_id: postid,
		ipaddress: ip,
		country: countr,
		user_id: userid,
	});
	
	if(typeof timer === 'undefined'){
		timer = setTimeout('refresh()', 3000);
	}
}