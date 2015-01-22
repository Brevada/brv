<?php
$this->addResource('/css/new_post.css');
$this->addResource('/js/new_post.js');

?>
<div id="newPost" style="width:80%; color:#777; font-size:17px; margin:0 auto; margin-top:15px; display:none;">
Add another <span id="emphasis">product</span>, <span id="emphasis">service</span> or <span id="emphasis">other aspect of your business</span> that you would like feedback on:<br />
		<?php if(true){ //??? ?>
		<form action="/overall/insert/insert_post.php" method="post" enctype="multipart/form-data" style="margin-top:15px;">
		<input id="inp" name="user_id" type="hidden" value="<?php echo $user_id; ?>" />
		<div class="left"><input id="inp" name="name" placeholder="Title" style="width:150px;" /></div>
		<div class="left"><input class="description_input" id="inp" name="description" placeholder="Description (optional)" style="width:300px; font-size:13px;" /></div>
		<!--
		<div style="height:0px;overflow:hidden; float:left;">
			<input type="file" id="fileInput" name="file" />
		</div>
		<button type="button" id="but" class="button4" onclick="chooseFile(), clearButton();" style="float:left; outline:none; margin-top:4px; padding:8px; opacity:0.8;">Choose Image (optional)</button>
		<img id="prev" onclick="chooseFile();" src="" alt="your image" height="30px" width="30px" style="float:left; margin-top:4px; display:none; cursor:pointer;" />		
		-->
		<div class="left">
		<input class="button4" name="submit" type="submit" value="Create" style="padding:8px; margin-top:4px; margin-left:5px;" />
		</div>
		<br style="clear:both;" />
	</form>
	<?php } else { ?>
	<div id="home_text" style="width:400px; margin-top:10px; margin-bottom:10px; color:#ddd;">
	You must upgrade to add new aspects.
	</div>
	<?php } ?>
	<br style="clear:both;" />
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:10px;">eg. "Customer Service"</div>	
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:180px;">"How well did our staff team ensure your satisfaction."</div>
	<br style="clear:both;" />
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:10px;">eg. "Location"</div>	
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:180px;">"Was our location convenient for you."</div>
	<br style="clear:both;" />
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:10px;">eg. "Penne Arrabiata"</div>	
	<div style="float:left; margin-top:-15px; font-size:11px; margin-left:180px;">"Our spiciest pasta, let us know what you think."</div>
	<br style="clear:both;" />
	<div style="display:none; float:left; margin-top:-15px; font-size:11px; margin-left:10px; color:#444;">For more suggestions organized by industry, <a href="/suggestions.php">click here</a>.</div>
</div>