$(document).ready(function(){
	$(".editHead").click(function(){
		var postid = $(this).attr('postid');
		$("#editContent_" + postid).slideToggle();
		if ($("#editSign_" + postid).text() == "+"){
			$("#editSign_" + postid).text("-")
		} else {
			$("#editSign_" + postid).text("+")
		}
	});
});