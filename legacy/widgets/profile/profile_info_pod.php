<?php
$user_extension = $this->getParameter('user_extension');
$user_id = $this->getParameter('user_id');
$name = $this->getParameter('name');
$type = $this->getParameter('type');
?>

<div id="left_data_holder">
	<div style="width:220px; max-height:240px;overflow:hidden;">
		<?php  if($user_extension=="none"){ ?>
		<img id="banner_pic" src="/user_data/user_images/default.png"  />
		<?php  } else { ?>
		<img id="banner_pic" src="/user_data/user_images/<?php  echo $user_id; ?>.<?php  echo $user_extension; ?>"  />
		<?php  } ?>
	</div>
	<div id="left_data_info" style="float:left;">
		<div id="left_name"><?php echo $name; ?></div>
		<div id="left_description"><?php echo $type; ?></div>
	</div>
	<br style="clear:both;" />
</div>