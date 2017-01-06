$(document).ready(function(){
	$("#expanderHead7Products").click(function(){
		$("#expanderContent7Products").slideToggle();
		if ($("#expanderSign7Products").text() == "+"){
			$("#expanderSign7Products").html("-")
		} else {
			$("#expanderSign7Products").text("+")
		}
	});
	
	$(".pic_close").click(function() {
		$("#pic_modal_background").fadeOut("fast");
		$("#pic_outer_modal").fadeOut("fast");
	});
});