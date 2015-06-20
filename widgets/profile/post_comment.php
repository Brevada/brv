<?php
$this->addResource('/css/post_comment.css');
//$this->addResource('/js/post_comment.js');

$r = $this->getParameter('row');
$post_id = $r['ID'];
$country = $this->getParameter('country');
$ip = $this->getParameter('ip');
$user_id = $this->getParameter('id');
?>

<style>
#button<?php echo $post_id; ?>{
	display:none; width:100%; height:30px; line-height:20px; outline:none; margin-top:-3px; border:0px; border-top:1px solid #dcdcdc; text-align:left;
}
</style>



<form  action="/overall/insert/insert_comment.php"  method="post">
	<textarea name="comment" onfocus="$( '#button<?php echo $post_id; ?>' ).css( 'display', 'block' ); $(this).css( 'height', '80px' );" onblur="" align="left" id="comment<?php echo $post_id; ?>" class="ta" placeholder="Comment"></textarea>
	<input type="hidden" name="ipaddress" id="ipaddress<?php echo $post_id; ?>" value="11111" />
   	<input type="button" onclick="PostComment('<?php echo $post_id; ?>', '<?php echo $country; ?>', '<?php echo $ip; ?>', '<?php echo $user_id; ?>');"  value="Submit Comment" class="button4" id="button<?php echo $post_id; ?>" >
</form>

<script>
function PostComment(post_id, country, ip, user_id) {
	var com = $("#comment"+post_id).val();
	if (com != "") {
		$.post("/overall/insert/insert_comment.php?post_id="+post_id+"&country="+country+"&ipaddress="+ip+"&user_id=" + user_id, { comment: com});
		$("#post_box_comment_" + post_id).hide();
	}
}
</script>