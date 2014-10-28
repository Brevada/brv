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
	
	$("#creditsHead").click(function(){
		$("#creditsContent").slideToggle();
		if ($("#creditsSign").text() == "View"){
			$("#creditsSign").text("Hide")
		} else {
			$("#creditsSign").text("View")
		}
	});	

	$('#credit').keyup(function(){			
		var v = $(this).val();
		var deduc1 = ((v-1)*4);
		var deduc2 = 80;
		
		var n = 0;
		if(deduc1 > 80){
			n = (360-deduc2);
		} else {
			n = (360-deduc1);
		}
		
		var f = v*n;
		var save = 360*v;
		var per = f/v;
		
		$('#displayCredit').html("Price: <strong><span style='color:green;'>$"+(f)+"</span></strong> <span style='color:red; font-size:10px;'>Regular: $"+save+", $"+per+" per credit.");
	});
	
	$('#password1').change(validatePassword);
	$('#password2').change(validatePassword);

	$("#credit").keydown(function (e) {
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			 // Allow: Ctrl+A
			(e.keyCode == 65 && e.ctrlKey === true) || 
			 // Allow: home, end, left, right
			(e.keyCode >= 35 && e.keyCode <= 39)) {
				 // let it happen, don't do anything
				 return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}
	});
	
});

function validatePassword(){
	if($('#password1').val() != $('#password2').val()) {
		$("#password2")[0].setCustomValidity("Passwords Don't Match");
	} else {
		$("#password2")[0].setCustomValidity(''); 
	}
	//empty string means no validation error
}

function openPopup() {
    $('#test').show();
}
function closePopup() {
    $('#test').hide();
}