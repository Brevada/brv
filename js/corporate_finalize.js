$(document).ready(function(){
	$("#expanderHead6Products").click(function(){
		$("#expanderContent6Products").slideToggle();
		if ($("#expanderSign6Products").text() == "+"){
			$("#expanderSign6Products").text("-")
		} else {
			$("#expanderSign6Products").text("+")
		}
	});
				
	$("#expanderHead5Products").click(function(){
		$("#expanderContent5Products").slideToggle();
		if ($("#expanderSign5Products").text() == "+"){
			$("#expanderSign5Products").text("-")
		} else {
			$("#expanderSign5Products").text("+")
		}
	});
			
	$("#expanderHead9Products").click(function(){
		$("#expanderContent9Products").slideToggle();
		if ($("#expanderSign9Products").text() == "+"){
			$("#expanderSign9Products").text("-")
		} else {
			$("#expanderSign9Products").text("+")
		}
	});
			
	$('#password1').change(validatePassword);
	$('#password2').change(validatePassword);
});

function validatePassword(){
	if($("password1").val() != $("password2").val()) {
		$("password2")[0].setCustomValidity("Passwords Don't Match");
	} else {
		//empty string means no validation error
		$("#password2")[0].setCustomValidity(''); 
	}
}

function openPopup() {
    $('#test').show();
}
function closePopup() {
    $('#test').hide();
}