<?php
$this->addResource('/css/layout.css');
$user_id = $_SESSION['user_id'];
$user=user($user_id);
?>
<div id="modal_title" class="text_clean">Copy this certification on to the footer of your website, show your customers that you're taking the necessary steps to ensure their satisfaction!</div>
<div id="p_holder">
 	<img src="/images/approved_1.png" width="200px"/>
    <br style="clear:both;" />
 	<textarea class="textarea" style="width:550px; resize:none;"><a href="http://brevada.com/approved"><img src="http://brevada.com/images/approved_1.png" width="200px" /></a></textarea>
 	<br style="clear:both;" />
</div>