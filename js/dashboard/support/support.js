/* Support Dashboard App */

bdff.create('support', function(canvas, face){
	canvas.children().not('div.message-container').remove();
	
	var support_form = $('<div>').addClass('support-form col-md-9').appendTo(canvas);
	
	$('<div class="dashboard-pod">\
		<textarea class="issue" placeholder="What can we help you with?"></textarea>\
 		<div class="submit">Submit</div>\
 		</div>\
	  ').appendTo(support_form);
	
	support_form.find('.submit').click(function(){
		var message = canvas.find('.issue').val();
		if (message) {
			// TODO: Submit the message through the API
			canvas.find('textarea, .submit').hide();
			bdff.face('aspects');
			bdff.notify('Your response has been recieved, you will be contacted shortly.', 'success');
		}
	});
	
	var support_resources = $('<div>');
 	support_resources.addClass('support-resources col-md-3').appendTo(canvas);

 	$('<div class="dashboard-pod">Dashboard Support, Tablet Support</div>').appendTo(support_resources);
});