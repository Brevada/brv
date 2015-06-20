<?php
$this->addResource('/css/mobile/post_message.css');

$user_id = $this->getParameter('user_id');
$name = $this->getParameter('name');

//GET COUNTRY
$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];
?>

<script>

function SubmitForm<?php echo $user_id; ?>() {
var comment<?php echo $user_id; ?>=$("#comment<?php echo $user_id; ?>").val();
$.post("/overall/insert/insert_message.php?userid=<?php echo $user_id; ?>", { message: comment<?php echo $user_id; ?>},
   function(data) {

   });
}

function clearContents(element) {
  element.value='';
}
</script>

    <form  action="insert_message.php"  method="post">
		<textarea name="message" maxlength="200" onfocus="clearContents(this);" id="comment<?php echo $user_id; ?>" class="text"  onblur="if(this.value == ''){this.value='Send Suggestion To <?php echo $name; ?>';}">Send Suggestion To <?php echo $name; ?></textarea>
		<input type="hidden" name="userid" id="ipaddress<?php echo $user_id; ?>" value="<?php echo $user_id; ?>" />
	
      	<input  type="button" onclick="SubmitForm<?php echo $user_id; ?>(), comment_disappear_<?php echo $user_id; ?>();"  value="Send Suggestion" align="center" class="button5" >
    </form>
    
<script type="text/javascript">
function comment_disappear_<?php echo $user_id; ?>() {
	document.getElementById("message_box").style.display='none';
}
</script>
