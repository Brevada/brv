<?php
$this->addResource('/css/post_box.css');
$this->addResource('/js/post_box.js');

$r = $this->getParameter('row');

$post_id = $r['id'];
$post_name = $r['name'];
$post_extension = $r['extension'];
$post_description = $r['description'];
$reviewer = $this->getParameter('reviewer');
$ip = $this->getParameter('ip');
$user_id = $this->getParameter('id');
$user_extension = $this->getParameter('user_extension');
?>

<div id="box_<?php echo $post_id; ?>" class='post_box_container'>	
<div id="post_box" style="border-bottom-right-radius:0px;">
<div  id="post_top" style="width:100%; background:#fff; border-bottom:1px solid #dcdcdc; height:50px; overflow:hidden;">
	<div style="padding:5px;">
		<div style="float:left; width:50px; height:35px; overflow:hidden;">
			<?php post_pic('50px','auto',$post_id, $user_id, $post_extension, $user_extension); ?>
		</div>
		<div style="float:left; margin-left:10px; width:430px;" title="<?php echo $post_description; ?>">
			<div style="color:#555; font-size:17px;">
			<?php echo $post_name; ?>
			</div>
			<div style="color:#888; font-size:12px;">
			<?php echo substr($post_description,0,85); if(strlen($post_description)>85){echo '...';} ?>
			</div>
		</div>
	</div>
</div>

<?php				
$checkQuery = Database::query("SELECT * FROM feedback WHERE ip_address='{$ip}' AND post_id='{$post_id}' ORDER BY `id` DESC");

if($checkQuery->num_rows==0){
?>
  							 
<div id="full_bar2_<?php echo $post_id; ?>" style="display:none; width:100%; border-top:0px solid #eee; background:#f8f8f8; font-size:11px; color:#333;">
	<div style="padding:5px;">Rate it from 1-100 by clicking the rating bar:</div>
</div>
<?php if(Brevada::IsInternetExplorer()){ ?>
<div  id="rating_<?php echo $post_id; ?>" style="color:#888; font-size:12px; width:100%;">
	<form>
	<div class="styled-select" style="float:left;">
		<select id="rating<?php echo $post_id; ?>" name="rating<?php echo $post_id; ?>" form="carform">
		<option>Click here to Rate From 1-100</option>
		<?php
		for($i=100;$i>=0;$i--){
			echo "<option value='{$i}'>{$i}</option>";
		}
		?>
		</select>
		<div id="button_<?php echo $post_id; ?>"  style=" margin-left:85px; width:20px;">
			<input type="button" onclick="submitRating(<?php echo "'{$post_id}', '{$ip}', '{$country}', '{$reviewer}', '"+ ($station ? 'true' : 'false') +"'"; ?>);" class="button4" style="margin-top:7px;" value="Submit" />
		</div>
	</div>
	</form>
</div>
<?php } else { //Not Internet Explorer ?>		
<div id="full_bar_<?php echo $post_id; ?>"  align="center" style="width:100%; border-top:0px solid #dcdcdc; background:rgb(0,255,0);">
	<div style="padding:0px; background:green;"><?php $this->add(new View('../widgets/profile/new_rating_bar.php', array('row' => $r, 'country' => $this->getParameter('country'), 'ip' => $this->getParameter('ip'), 'id' => $this->getParameter('id'), 'reviewer' => $reviewer))); ?></div>
</div>
<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="display:none;width:100%; border-top:0px solid #dcdcdc;">Thanks for rating.</div>	
<?php
	}
} else { ?>
	<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="width:100%; border-top:0px solid #dcdcdc;">Already rated.</div>	
<?php } ?>
</div>
<div class="post_box" id="post_box_comment_<?php echo $post_id; ?>" style="margin-top:0px; border-top:0px; border-radius:0px; ">
	<div style="padding:0px;"><?php $this->add(new View('../widgets/profile/post_comment.php', array('row' => $r, 'country' => $this->getParameter('country'), 'ip' => $this->getParameter('ip'), 'id' => $this->getParameter('id')))); ?></div>
</div>
</div>