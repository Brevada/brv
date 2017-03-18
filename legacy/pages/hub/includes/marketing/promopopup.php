<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/promopopup.css');
$user_id = $_SESSION['user_id'];
$user=user($user_id);
?>
<div id="promo_blur">
	<div id="promo_modal">
		<div id="promo_quit">x</div>
		<?php
		if($user['level'] > 1){
			$this->add(new View('../hub/includes/marketing/promopopup_content.php'));
		} else {
			$this->add(new View('../hub/includes/upgrade_button.php', array('upgrade_message' => "Upgrade to Get Your Promotional Materials")));
		}
		?>
	</div>
</div>
<script type='text/javascript'>
$(document).ready(function(){
     $("#promos").click(function() { 
    $("#promo_blur").fadeIn(500);
  });
});
$("#promo_quit").click(function(){
  $("#promo_blur").fadeOut(500);
});
</script>