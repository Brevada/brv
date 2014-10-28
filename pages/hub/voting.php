<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/voting.css');
$this->addResource('/js/voting.js');

$this->addResource('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1" />', true, true);

//GET COUNTRY
$geo = Brevada::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$url_name = empty($_GET['name']) ? '' : $_GET['name'];
$user_id = $_SESSION['user_id'];

$query = Database::query("SELECT `id`, `name`, `type`, `extension` FROM users WHERE id='{$user_id}' LIMIT 1");

if($query->num_rows==0){
	//No user exists
	Brevada::Redirect('/404');
}

$id = $user_id; $name = ''; $type = ''; $user_extension = '';

while($row = $query->fetch_assoc()){
   $name = $row['name'];
   $type = $row['type'];
   $user_extension = $row['extension'];
}
?>
<div id="cont">
	<div id="pod">
		<div id="bg_holder" style="height:170px; width:100%; background:url('/user_data/user_images/<?php echo "{$id}.{$user_extension}"; ?>'); background-size:100%;"></div>
		<div id="top_holder" style="height:170px; margin-top:-170px; background:rgba(0,0,0,0.7);">
			<div id="pic_holder">
				<img  id="pic" src="/user_data/user_images/<?php echo "{$id}.{$user_extension}"; ?>" />
			</div>
			<div id="title_holder">
				<strong><?php echo $name; ?></strong><br />
				<div class="button" id="message_button" onclick="message_show()">Suggestions</div>
			</div>
		</div>
		<div id="message_box" align="left" style="display:none; width:100%;">
		<?php $this->add(new View('../pages/mobile/post_message.php', array('user_id' => $user_id, 'name' => $name))); ?>
		</div>
		<div style="margin-top:1px;">
			<?php
			$query=Database::query("SELECT * FROM posts WHERE `user_id` = '{$id}' AND active='yes' ORDER BY `id` DESC");
			while($rows=$query->fetch_assoc()){
				$post_id = $rows['id'];
				$post_name = $rows['name'];
				$post_extension = $rows['extension'];
				$post_description = $rows['description'];
  			?>
			<div class="post" style="float:left;">
				<div id="pic_holder2">
				<?php post_pic('auto', '100px', $post_id, $user_id, $post_extension, $user_extension); ?>
				</div>
				<div id="info_holder" class="overflow" style="padding-top:15px;">
					<strong><?php echo $post_name; ?></strong> 
					<div id="overflow"><?php echo $post_description; ?>&nbsp;</div>		
				</div>
				<div id="info_holder" class="overflow" style="float:left; padding-top:12px; width:300px;">
					<div id="buttons<?php echo $post_id; ?>">
						<div onclick="rate('20',<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$user_id}'"; ?>)"  class="button4" id="face_button">
							<img id="face" src="/images/cry.png" />
						</div>
						<div onclick="rate('45',<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$user_id}'"; ?>)" class="button4" id="face_button">
							<img id="face" src="/images/sad.png" />
						</div>
						<div onclick="rate('63',<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$user_id}'"; ?>)" class="button4" id="face_button">
							<img id="face" src="/images/medium.png" />
						</div>
						<div onclick="rate('84',<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$user_id}'"; ?>)" class="button4" id="face_button">
							<img id="face" src="/images/happy.png" />
						</div>
						<div onclick="rate('100',<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$user_id}'"; ?>)" class="button4" id="face_button">
							<img id="face" src="/images/love.png" />
						</div>
						<br style="clear:both;" />
					</div>
					<div id="thanks_<?php echo $post_id; ?>"  style="display:none; width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
				</div>
			 </div>
   			<?php } ?>
		<?php $this->add(new View('../pages/mobile/widget_bottom.php')); ?>
		</div>
	</div>
</div>