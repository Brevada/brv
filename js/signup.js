$(document).ready(function(){
	$('#next').click(function(e){
		e.preventDefault();
		$('#part1').slideUp();
		$('#part2').slideDown();
		return false;
	});
					
	$("#posts-token").tokenInput("/home/get_post_tokens.php", {
		theme: "facebook",
		hintText: 'Specify what you want to get feedback on...',
		preventDuplicates: true
	});

	//Default Tokens
	$("#posts-token").tokenInput("add", {id: 1, name: 'Customer Service'});
	//$("#posts-token").tokenInput("add", {id: 2, name: 'Pricing'});

	//Focus on input
	setTimeout(function() { $('#posts-token').focus(); }, 50);

});

window.onload=function () {
 document.getElementById("password1").onchange=validatePassword;
 document.getElementById("password2").onchange=validatePassword;
}

function validatePassword(){
var pass=document.getElementById("password2").value;
var pass2=document.getElementById("password1").value;
if(pass1==pass2)
	document.getElementById("password2").setCustomValidity("Passwords Don't Match");
else
	document.getElementById("password2").setCustomValidity('');  
//empty string means no validation error
}