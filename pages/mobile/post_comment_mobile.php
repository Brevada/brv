<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/mobile/post_comment_mobile.css');

$post_id = $this->getParameter('post_id');

//GET COUNTRY
$geo = Geography::GetGeo();
$ip = $geo['ip'];
$country = $geo['country'];
?>

<script>
function SubmitForm<?php echo $post_id; ?>() {
var comment<?php echo $post_id; ?>=$("#commentC<?php echo $post_id; ?>").val();

$.post("/overall/insert/insert_comment.php?postid=<?php echo $post_id; ?>&country=<?php echo $country; ?>&ipaddress=<?php echo $ip; ?>", { comment: comment<?php echo $post_id; ?>},
   function(data) {

   });
}

function clearContents(element) {
  element.value='';
}
</script>
    <form id="theform_<?php echo $post_id; ?>"  action="/overall/insert/insert_comment.php"  method="post">
		<textarea name="comment" align="left" id="commentC<?php echo $post_id; ?>" class="text" onfocus="if(this.value == 'Comment on <?php echo $post_name; ?>'){this.value=''; }" onblur="if(this.value == ''){this.value='Comment on <?php echo $post_name; ?>';}">Comment on <?php echo $post_name; ?></textarea>
		<input type="hidden" name="ipaddress" id="ipaddress<?php echo $post_id; ?>" value="11111" />
	
      	<input  type="button" onclick="SubmitForm<?php echo $post_id; ?>(), comment_disappear_<?php echo $post_id; ?>();"  value="Comment" align="center" class="button2" style="float:none;" >
   
   </form>
    
<script type="text/javascript">
function comment_disappear_<?php echo $post_id; ?>() {
	document.getElementById("theform_<?php echo $post_id; ?>").style.display='none';
}
</script>
