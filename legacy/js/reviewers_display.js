$(document).ready(function(){
	$(".reviewHead").click(function(){
		var id = $(this).attr('reviewerid');
		$("#reviewContent"+id).slideToggle();
		if ($("#reviewSign"+id).text() == "+"){
			$("#reviewSign"+id).text("-")
		} else {
			$("#reviewSign"+id).text("+")
		}
	});
});
