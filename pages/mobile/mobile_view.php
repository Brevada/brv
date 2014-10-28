<?php
$name = Brevada::FromPOSTGET('name');
?>
<div style="padding-bottom:10px; font-size:13px; color:#555555;">
This is how your page will appear to people giving you feedback on a mobile device.
</div>
<br />
<div style="width:330px; margin:0 auto;">
<iframe src="http://brevada.com/profile_mobile.php?name=<?php echo $name; ?>" frameBorder="0" style="height:400px; width: 320px; overflow-x:hidden; border:1px solid #dcdcdc;"></iframe>
</div>