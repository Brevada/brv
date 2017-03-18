$(document).ready(function(){
	$("#suggestions_button").click(function () {
		$("#suggestion_box").show("slow");
	});
});

function SubmitFormSuggestion(id) {
var comment = $("#suggestion").val();
$.post("/overall/insert/insert_message.php?userid="+id, { message: comment});
}

function clearContents(element) {
	element.value = '';
}

function close_suggestion() {
	$('#suggestion_box').hide();
}
function thanks_suggestion() {
	$('#thanks_suggestion').show();
}