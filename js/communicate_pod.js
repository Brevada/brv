function SubmitEmail(user_id, session_id) {
	event.preventDefault();
	var emailTiev = $("#emailTie").val();
	if((emailTiev.length)>4){
		$.post("/overall/insert/insert_emailTie.php?user_id="+user_id+"&session_id=" + session_id, { emailTie: emailTiev});
		$("#communicate_form").hide();
		$("#prizes").hide();
		$("#thanks_connect").show();
	}
}

function clearContents(element) {
	element.value = '';
}