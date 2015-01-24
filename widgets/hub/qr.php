<?php
$this->addResource('/css/layout.css');
$user_id = $_SESSION['user_id'];
$user=user($user_id);
?>
<div id="modal_title" class="text_clean">Share your barcode image on your website, reciepts, flyers...</div>
<img src="/user_data/qr/<?php echo $user_id; ?>.png" />