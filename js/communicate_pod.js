$(document).ready(function(){
	$("#reset").click(function() { 
		location.reload(true);
	});	
	
	$('#emailTie').on('keyup click focus', function(e){
		var value = $(this).val();
		if(value.length == 0){
			$('#email-submit').addClass('disabled');
			$('#email-submit').attr("disabled", true);
			$('#basic-addon1').css({'color' : '#333333'});
			$('#basic-addon1').html('Email: ');
		} else if(!validateEmail(value)){
			$('#email-submit').addClass('disabled');
			$('#email-submit').attr("disabled", true);
			$('#basic-addon1').css({'color' : '#cc750e'});
			$('#basic-addon1').html('<i class="fa fa-exclamation-circle"></i>');
		} else {
			$('#email-submit').removeClass('disabled');
			$('#email-submit').attr("disabled", false);
			$('#basic-addon1').css({'color' : '#2ecc0e'});
			$('#basic-addon1').html('<i class="fa fa-check-circle"></i>');
		}
	});
	
	$('#emailTie').keypress(function(e){
		if(e.which == 13){
			$('#email-submit').click();
		}
	});
	
	$('#email-submit').click(function(){
		var uid = $('#communicate_form input[name="user_id"]').val();
		var email = $('#communicate_form input[name="emailTie"]').val();
		if(typeof email !== 'undefined' && typeof uid !== 'undefined' && email.length > 0){
			$.post('/overall/insert/insert_emailTie.php', { 'user_id' : uid, 'emailTie' : email }, function(data){
				location.reload(true);
			});
		}
	});
});

function clearContents(element) {
	element.value = '';
}

function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}