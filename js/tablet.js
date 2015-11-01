$("#imdone").click(function() { 
	$('#aspects, .fixed-toolbar').fadeOut(300, function () {
		$('#email_connect').show();
	});
});

$('div.star').each(function(){
	$(this).css('height', $(this).outerWidth()+'px')
});

$('#reset').click(function(){
	console.log("Reset clicked!");
	$('#email_connect').hide();
	$('.rated').removeClass('rated');
	$('#aspects, .fixed-toolbar').fadeIn(300);
});

function insertRating(val, id) {
	var payload = {
		serial : globals.uuid,
		now : Math.floor((new Date()).getTime()/1000),
		rating : val,
		aspectID : id,
		batteryLevel : globals.battery.level,
		batteryIsPlugged : globals.battery.isPlugged.toString()
	};
	
	app.sendPayload(payload);
}

function disappearRating(post_id) {
	$("#aspect_"+post_id).addClass('rated');
}