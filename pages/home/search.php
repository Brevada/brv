<?php
$this->add(new View('../template/main_header.php'));
$this->addResource('/css/search.css');

$needle = Brevada::validate($_POST['needle'], VALIDATE_DATABASE);
?>

<div style="width:100%; margin-top:90px;"><div id="sized_container">		

<?php
if(!empty($needle)){

$query = Database::query("SELECT `id`, `name`, `email`, `url_name`, `extension` FROM `users` WHERE `name` LIKE '%{$needle}%' or `email` LIKE '%{$needle}%'");

if($query->num_rows==0){
	echo "<center><span color='#666666'>No results found.</span></center>";
}

while($row=$query->fetch_assoc()){
   $post_id = $row['id'];   
   $post_name = $row['name'];
   $post_url_name = $row['url_name'];
   $post_extension = $row['extension'];
   //$post_description = $row['description']; // There is no description column.
   
   if(empty($post_name) || empty($post_url_name)){ continue; }
   ?>
	<div id="search_result">
		<div style="float:left; width:150px; overflow:hidden;">
			<?php
			$filename2="../user_data/user_images/{$post_id}.{$post_extension}";
			if (file_exists($filename2)) { ?>
			<img src="/user_data/user_images/<?php echo $post_id; ?>.<?php echo $post_extension; ?>" height="80px" />
			<?php } else { ?>
			<img src="/user_data/user_images/default.jpg" height="80px" style="overflow:hidden; border-radius:0px; max-width:90px;" />
			<?php } ?>
		</div>

		<div style="float:left; width:200px;"><?php echo $post_name; ?></div>
		<div style="float:left; width:400px;">
			<div style="float:left; font-size:12px; color:#444;">Give <?php echo $post_name; ?> feedback on:</div><br />
			<?php
			$query2 = Database::query("SELECT `id`, `name`, `extension` FROM `posts` WHERE `user_id`='{$post_id}'");
			
			if($query2->num_rows == 0){
				echo "This user has no posts.";
			}
			
			while($rows2=$query2->fetch_assoc()){
				$post_id2=$rows2['id'];
				$post_name2=$rows2['name'];
				$post_extension2=$rows2['extension'];
			?>
				<div id="block">
 			  	<div class="left">		  	
 			  		<?php
					$filename2="../user_data/user_images/{$post_id}.{$post_extension}";
					$filename='../user_data/post_images/' . $post_id2 . '.' . $post_extension2;
					if (file_exists($filename)) {?>
				<img src="/user_data/post_images/<?php echo $post_id2; ?>.<?php echo $post_extension2; ?>" height="30px" style="overflow:hidden; border-radius:3px; max-width:90px;" />
				<?php } else if(file_exists($filename2)){ ?>
				<img  src="/user_data/user_images/<?php echo $post_id; ?>.<?php echo $post_extension; ?>" height="30px"/>
				<?php } else { ?>
				<img src="/user_data/user_images/default.jpg" height="30px" style="overflow:hidden; border-radius:0px; max-width:90px;" />
				<?php } ?>
				</div>
 			  	<div class="left"  style="margin-left:5px;margin-top:7px; margin-right:5px;" ><?php echo $post_name2; ?></div>
 			  </div>
 			  <?php } ?>
 			  </div>
 			  <div style="float:right; width:100px;">
 			  	<a href="<?php echo URL.$post_url_name; ?>"><div class="button2">Visit Page</div></a>
 			  </div>
   			<br style="clear:both;" />
   </div>
<?php
	}
} else {
	echo "<center><span color='#666666'>Try typing a keyword into the search box.</span></center>";
}
?>
</div></div>
<?php $this->add(new View('../template/footer.php')); ?>