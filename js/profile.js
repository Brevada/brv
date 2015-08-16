$(document).ready(function(){
	$("#imdone").click(function() { 
		$('#aspects, .fixed-toolbar').fadeOut(300, function () {
			$('#email_connect').show();
		});
	});
});