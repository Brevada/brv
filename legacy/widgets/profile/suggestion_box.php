<?php
$id = $this->getParameter('id');
?>

<script type='text/javascript'>
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
</script>

<form  action="/overall/insert/insert_message.php"  method="post">
	<input type="hidden" name="userid" id="ipaddress" value="<?php echo $id; ?>" />
	<textarea class="inp" id="suggestion" placeholder="General suggestions or comments" style="font-size:12px; width:210px; border:0px; margin:0 auto; outline:none; resize:none; height:100px;"></textarea>
	<div class="button4" onclick="SubmitFormSuggestion('<?php echo $id; ?>'), close_suggestion(), thanks_suggestion();" style="width:255px; height:30px; line-height:30px; border-left:0px; border-right:0px;">Submit Suggestions</div>
</form>