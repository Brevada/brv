<?php
$this->addResource('/css/layout.css'); 

$user_id = $_SESSION['user_id'];

$user = user($user_id);
?>
<div id="modal_title" class="text_clean">Copy your page URL and share it everywhere!</div>
<textarea class="textarea" style="width:500px; margin:0 auto; margin-top:10px;">Give us feedback at: http://brevada.com/<?php echo $user['url_name']; ?></textarea>