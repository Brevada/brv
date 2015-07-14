$(document).ready(function(){
	$('#next').click(function(e){
		e.preventDefault();
		$('#part1').fadeOut(0);
		$('#part2').fadeIn(0);
		return false;
	});
	
	$('#back').click(function(e){
		e.preventDefault();
		$('#part2').fadeOut(0);
		$('#part1').fadeIn(0);
		return false;
	});
	
    $('#logo').each(function(i) {
        if (this.complete) {
            $('#signup_box').fadeIn(2000, function(){
				
			});
        } else {
            $(this).load(function() {
				$('#signup_box').fadeIn(2000, function(){
					
				});
            });
        }
    });
	
	$('div.tokens > div.token').click(function(){
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
		} else {
			$(this).addClass('selected');
		}
		updateTokens();
	});
	
	$('#chkAgree').change(function(){
		if(this.checked){
			$('#submit').removeClass('disabled');
			$('#submit').removeAttr('disabled');
		} else {
			$('#submit').addClass('disabled');
			$('#submit').attr('disabled', true);
		}
	});
	
});

function updateTokens(){
	var tokens = [];
	$('div.tokens > div.token').each(function(){
		if($(this).hasClass('selected')){
			tokens.push($(this).data('tokenid'));
		}
	});
	$('#tokens').val(tokens.join(','));
}

window.onload=function () {
	$('#password1').change(validatePassword);
	$('#password2').change(validatePassword);
}

function validatePassword(){
	var pass1=$("#password2").val();
	var pass2=$("#password1").val();
	if(pass1!=pass2){
		document.getElementById("password2").setCustomValidity("Passwords Don't Match");
	} else {
		document.getElementById("password2").setCustomValidity('');  
		//empty string means no validation error
	}
}