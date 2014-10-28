<?php
$post_id = $this->getParameter('post_id');
$post_extension = $this->getParameter('post_extension');
$user_id = $this->getParameter('user_id');
?>
<?php $filename='../user_data/post_images/' . $post_id . '.' . $post_extension; 
		$filename2='../user_data/user_images/' . $user_id . '.' . $user_extension;
	if (file_exists($filename)) {?>
<img id="post_pic" src="/user_data/post_images/<?php echo $post_id; ?>.<?php echo $post_extension; ?>" 	  />
<?php } else if(file_exists($filename2)){ ?>
<img id="post_pic"  src="/user_data/user_images/<?php echo $user_id; ?>.<?php echo $user_extension; ?>" />
<?php } else{ ?>
<img id="post_pic" src="/user_data/user_images/default.jpg" style="width:100px;"  />
<?php } ?>