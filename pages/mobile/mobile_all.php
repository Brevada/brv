<?php
$this->addResource('/css/mobile/mobile_all.css');
$this->addResource("<link ref='shortcut icon' type='image/x-icon' href='/images/check.png'>", true, true);
$this->addResource("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1'/>", true, true);

$url_name = Brevada::validate(Brevada::FromPOSTGET('name'), VALIDATE_DATABASE);

//GET COUNTRY
$geo = Geography::GetGeo();
$country = $geo['country'];
$ip = $geo['ip'];

$query=Database::query("SELECT * FROM users WHERE url_name = '{$url_name}' LIMIT 1");
if($query->num_rows==0){
	//No user exists
}

$id = ''; $user_id = ''; $name = ''; $type = ''; $user_extension = '';
while($rows=$query->fetch_assoc()){
   $id=$rows['id'];
   $user_id=$id;
   $name = $rows['name'];
   $type = $rows['type'];
   $user_extension = $rows['extension'];
}
?>
<div id="cont">
	<div id="pod">
	
				
		<div id="bg_holder" style="height:170px; width:100%; background:url('/user_data/user_images/<?php echo $id; ?>.<?php echo $user_extension; ?>'); background-size:100%;">
			
		</div>
		
		<div id="top_holder" style="height:170px; margin-top:-170px; background:rgba(0,0,0,0.7);">
			
			<div id="pic_holder">
			<img  id="pic" src="/user_data/user_images/<?php echo $id; ?>.<?php echo $user_extension; ?>" />
			</div>
			
			<div id="title_holder">
			<strong><?php echo $name; ?></strong>
	
			<div class="button" id="message_button" onclick="message_show()">Message</div>
			</div>
		</div>
		<div id="message_box" align="left" style="display:none; width:100%;">
		<?php $this->add(new View('../pages/mobile/post_message.php', array('user_id' => $user_id, 'name' => $name))); ?>
		</div>
		<script>
			function message_show() {
				document.getElementById("message_box").style.display='block';
				document.getElementById("message_button").style.display='none';
			}
		</script>
		<div style="margin-top:1px;">
		
			<?php
			$query=Database::query("SELECT * FROM posts WHERE user_id = '{$id}' AND active='yes' ORDER BY id DESC");
			
			$num=0;
			while($rows=$query->fetch_assoc()){
  			$post_id=$rows['id'];
   			$post_name=$rows['name'];
   			$post_extension=$rows['extension'];
   			$post_description=$rows['description'];
   			?>
   			<!-- POST -->
   			
   			
							<?php
					$ip=$_SERVER["REMOTE_ADDR"];
					$check=Database::query("SELECT * FROM feedback WHERE ip_address = '{$ip}' AND post_id='{$post_id}' ORDER BY id DESC");
  				    $check_rate=$check->num_rows;

  				  
  							 ?>
   			
			<div class="post">
				<div id="pic_holder2">
				<?php $this->add(new View('../pages/mobile/show_user_image.php', array('user_id' => $user_id, 'post_id' => $post_id, 'post_extension' => $post_extension))); ?>
				</div>
				<div id="info_holder">
				<strong><?php echo $post_name; ?></strong> 
				<div id="overflow">
				<?php echo $post_description; ?>
				</div>
				
				<div>
				<a  id="expanderHead1<?php echo $post_id; ?>" data-reveal-id="unlock"><div id="button3"><span id="expanderSign1<?php echo $post_id; ?>">+</span> Rate It</div></a>  <a  id="expanderHead2<?php echo $post_id; ?>" data-reveal-id="unlock"><div id="button3"><span id="expanderSign2<?php echo $post_id; ?>">+</span> Comment</div></a>
				<br style="clear:both;" />
				</div>
				
							
 				 			
			
				
				</div>
				<br style="clear:both;" />
				
				
			</div>
			<div id="expanderContent1<?php echo $post_id; ?>" style="display:none; margin-top:0px; padding:4px;">
				 
				 <?php if($check_rate=0){ ?>
				 <form name="theform_<?php echo $post_id; ?>">
				 <select class="select" id="rating_<?php echo $post_id; ?>" name="rating_<?php echo $post_id; ?>" for=carform" >
   					<option value="101">Click here to rate it from 1-100</option>
  				 	<?php for($=00;$i=;$i--){ ?>	
  					<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
				</select>				
				<div  id="button_<?php echo $post_id; ?>">
				<input type="button" onclick="submitRating<?php echo $post_id; ?>();" class="button2" style="margin-top:5px;" value="Submit Rating" />	
				</form>		
				</div>
				<div id="thanks_<?php echo $post_id; ?>"  style="display:none;  width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
				<?php } else{ ?>
				<div id="thanks_<?php echo $post_id; ?>"  style="width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
				<?php } ?>
			
			</div>
			<div id="expanderContent2<?php echo $post_id; ?>" style="display:none; margin-top:0px; padding:4px; ">
				<?php $this->add(new View('../pages/mobile/post_comment_mobile.php', array('post_id' => $post_id))); ?>
			</div>
			<script type="text/javascript">
			$(document).ready(function(){
			$("#expanderHead1<?php echo $post_id; ?>").click(function(){
			$("#expanderContent1<?php echo $post_id; ?>").slideToggle();
			if ($("#expanderSign1<?php echo $post_id; ?>").text() == "+"){
			$("#expanderSign1<?php echo $post_id; ?>").html("-")
			}
			else {
			$("#expanderSign1<?php echo $post_id; ?>").text("+")
			}
			});
			});
			</script>
			<script type="text/javascript">
			$(document).ready(function(){
			$("#expanderHead2<?php echo $post_id; ?>").click(function(){
			$("#expanderContent2<?php echo $post_id; ?>").slideToggle();
			if ($("#expanderSign2<?php echo $post_id; ?>").text() == "+"){
			$("#expanderSign2<?php echo $post_id; ?>").html("-")
			}
			else {
			$("#expanderSign2<?php echo $post_id; ?>").text("+")
			}
			});
			});
			</script>
			<!-- END POST -->
			
			<script type="text/javascript">

function hideRating_<?php echo $post_id; ?>() {
	document.getElementById("rating_<?php echo $post_id; ?>").style.display='none';
	document.getElementById("button_<?php echo $post_id; ?>").style.display='none';
}
function showThanks_<?php echo $post_id; ?>() {
	document.getElementById("thanks_<?php echo $post_id; ?>").style.display='block';
}
</script>

			<script>

function submitRating<?php echo $post_id; ?>() {
var rating<?php echo $post_id; ?>=$("#rating_<?php echo $post_id; ?>").val();

if(rating<?php echo $post_id; ?>==101){

}
else{

 hideRating_<?php echo $post_id; ?>(),
 showThanks_<?php echo $post_id; ?>(),
$.post("insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>", { value: rating<?php echo $post_id; ?>},
  function(data) {

   });
 }  
  
}
</script>
   			
   			<?php
   			$num=$num+1;
			}

			?>
		
		</div>
	
	</div>
	
	
	<div id="powered">
	Powered by <img src="/images/brevada.png" height="12px" />
	</div>
	
</div>