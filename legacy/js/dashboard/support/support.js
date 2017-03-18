/* Support Dashboard App */

bdff.create('complete', function(canvas, face){
	canvas.children().not('div.message-container').remove();
	
	var support_form = $('<div>').addClass('support-form col-md-9').appendTo(canvas);
	
	$('<div class="dashboard-pod">\
		<textarea class="issue" placeholder="What can we help you with?" maxlength="1000"></textarea>\
 		<div class="submit">Submit</div>\
 		</div>\
	  ').appendTo(support_form);
	
	support_form.find('.submit').click(function(){
		var message = canvas.find('.issue').val();
		if (message) {
			canvas.find('textarea, .submit').hide();
			
			$.post('/api/v1/support/open', { 'message' : message, 'localtime' : Math.ceil(((new Date()).getTime()/1000)) }, function(data){
				bdff.face('aspects');
				bdff.notify('Thank you,', 'Your response has been received, you will be contacted shortly.', 'success');
			}).fail(function(){
				bdff.notify('Sorry,', 'There\'s been an error sending your message. Feel free to contact us over the phone.', 'error');
				canvas.find('textarea, .submit').fadeIn();
			});
		}
	});
	
	var support_resources = $('<div>');
 	support_resources.addClass('support-resources col-md-3').appendTo(canvas);

 	$('<div class="dashboard-pod">Dashboard Support, Tablet Support</div>').appendTo(support_resources);
});