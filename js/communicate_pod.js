$(document).ready(function(){
	$("#reset").click(function() { 
		location.reload(true);
	});

	var input = document.getElementById("emailTie");

	input.onkeyup = function(evt) {
		if (input.value === '') {
			$('#email-submit').addClass('disabled');
			$('#email-submit').attr("disabled", true);
			$('#basic-addon1').css({'color' : '#333333'});
			$('#basic-addon1').html('Email: ');
			return;
		}
		if (validateEmail(input.value)) {
			$('#email-submit').removeClass('disabled');
			$('#email-submit').attr("disabled", false);
			$('#basic-addon1').css({'color' : '#2ecc0e'});
			$('#basic-addon1').html('<i class="fa fa-check-circle"></i>');
		} else {
			$('#email-submit').addClass('disabled');
			$('#email-submit').attr("disabled", true);
			$('#basic-addon1').css({'color' : '#cc750e'});
			$('#basic-addon1').html('<i class="fa fa-exclamation-circle"></i>');
		}
	};
});

function SubmitEmail(user_id, session_id) {
	event.preventDefault();
	var emailTiev = $("#emailTie").val();
	if((emailTiev.length)>4){
		$.post("/overall/insert/insert_emailTie.php?user_id="+user_id+"&session_id=" + session_id, { emailTie: emailTiev});
		//$("#thanks_connect").show();
		location.reload(true);
	}
}

function clearContents(element) {
	element.value = '';
}

function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}




