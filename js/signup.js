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
					
	$("#posts-token").tokenInput("/home/get_post_tokens.php", {
		theme: "facebook",
		hintText: 'Specify what you want to get feedback on...',
		preventDuplicates: true
	});

	//Default Tokens
	$("#posts-token").tokenInput("add", {id: 'Customer Service', name: 'Customer Service'});
	//$("#posts-token").tokenInput("add", {id: 'Pricing', name: 'Pricing'});

	//Focus on input
	setTimeout(function() { $('#posts-token').focus(); }, 50);

});

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