<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/loader.css');
$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0'/>", true, true);
$loaderDestination = $this->getParameter('destination');
$loaderWait = empty($this->getParameter('wait')) ? '800' : $this->getParameter('wait');
?>

<div style="background:#f8f8f8; width:100%; height:100%; position:fixed; top:0px; left:0px;  z-index:99999999999999;">
	<div align="center" style="width:100%; position:absolute; height:150px; top:50%; margin-top:-90px; ">
		<div id="load_container">
			<img id="pic" src="/images/brevada.png" style="height:32px; margin:10px;  position:relative; z-index:100; opacity:1;" />
			<div id="warningGradientOuterBarG">
				<div id="warningGradientFrontBarG" class="warningGradientAnimationG">
					<div class="warningGradientBarLineG"></div>
					<div class="warningGradientBarLineG"></div>
					<div class="warningGradientBarLineG"></div>
					<div class="warningGradientBarLineG"></div>
					<div class="warningGradientBarLineG"></div>
					<div class="warningGradientBarLineG"></div>
				</div>
			</div>
		</div>	
	</div>
</div>

<script type='text/javascript'>
$(document).ready(function(){
	setTimeout(function(){
		window.location = '<?php echo $loaderDestination; ?>';
	}, <?php echo $loaderWait; ?>);
});
</script>