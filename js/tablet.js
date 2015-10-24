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
	//$.get("/overall/insert/insert_rating.php", { value : val, post_id : id });
	// send timestamp as well (if timestamp is within delta of server, accept, otherwise somethings wrong
	// corrupted connection / high latency.
	var payload = {
		k : globals.key,
		serial : globals.uuid,
		now : (new Date()).getTime(),
		rating : val,
		aspectID : id,
		batteryLevel : globals.battery.level,
		batteryIsPlugged : globals.battery.isPlugged,
		hash : ''
	};
	
	Checksum.forString(stringifyPayload(payload), function(hex){
		payload.hash = hex;
		
		$.ajax({
			url : globals.api+'feedback',
			cache : false,
			dataType : 'json',
			timeout : 5000,
			method : 'POST',
			data: payload,
			success : function(data){
				console.log(JSON.stringify(data));
				if(!data.hasOwnProperty('error') || data.error.length == 0){
					console.log("Rating submitted.");
				} else {
					app.failedSubmission(payload);
				}
			}
		}).fail(function(){
			console.log("Failed to post feedback.");
			app.failedSubmission(payload);
		});
	}, function(err){
		console.log("Failed to generate hash for feedback.");
		app.failedSubmission(payload);
	});
}

function disappearRating(post_id) {
	$("#aspect_"+post_id).addClass('rated');
}