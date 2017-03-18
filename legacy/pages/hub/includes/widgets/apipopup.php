<?php
$this->addResource('/css/apipopup.css');

$url_name = $this->getParameter('url_name');
$user_id = $this->getParameter('user_id');
?>

<div id="api_blur">
	<div id="api_modal">
		<div id="api_quit">x</div>
		<?php
		if($level > 2){
			$this->add(new View('../hub/includes/widgets/apipopup_content.php', array('url_name' => $url_name, 'user_id' => $user_id)));
		} else {
			$this->add(new View('../hub/includes/upgrade_button.php', array('upgrade_message' => "Upgrade to Use Widgets and Website Integration")));
		}
		?>
	</div>
</div>

<script type='text/javascript'>
$(document).ready(function(){
    $("#widgets").click(function() { 
    $("#api_blur").fadeIn(500);
  });
});

$("#api_quit").click(function(){
  $("#api_blur").fadeOut(500);
});
</script>