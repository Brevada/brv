$(document).ready(function(){
	$('#next').click(function(event){
		event.preventDefault();
		$('#part1').slideUp();
		$('#part2').slideDown();
		return false;
	});
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