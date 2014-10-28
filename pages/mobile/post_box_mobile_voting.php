<?php
$this->addResource('/css/mobile/post_box_mobile_voting.css');
//GET COUNTRY
$geo = Brevada::GetGeo();
$ip = $geo['ip'];
$country = $geo['country'];

$post_id = $this->getParameter('post_id');
$post_extension = $this->getParameter('post_extension');
$user_extension = $this->getParameter('user_extension');
$id = $this->getParameter('id');
$post_name = $this->getParameter('post_name');
$post_description = $this->getParameter('post_description');
$station = $this->getParameter('station');
?>


	<div style="width:100%; float:left; margin-bottom:0px; margin-top:0px;">	
	<div id="post_box">
		<div id="post_top" style="width:100%; background:#fff; margin-top:5px; border-bottom:0px solid #dcdcdc; padding-top:5px;">
			<div style="padding:5px;">
				<div style="float:left; width:90px; overflow:hidden;">
					<?php $filename='post_images/' . $post_id . '.' . $post_extension; 
						$filename2='user_images/' . $id . '.' . $user_extension;
					if (file_exists($filename)) {?>
				<img src="post_images/<?php echo $post_id; ?>.<?php echo $post_extension; ?>" height="75px" style="overflow:hidden; border-radius:0px; max-width:90px;" />
				<?php } else if(file_exists($filename2)){ ?>
				<img  src="user_images/<?php echo $id; ?>.<?php echo $user_extension; ?>" height="75px"/>
				<?php } else{ ?>
				<img src="../user_data/user_images/default.jpg" height="75px" style="overflow:hidden; border-radius:0px; max-width:90px;" />
				<?php } ?>
				</div>
				<div style="float:left; margin-left:10px; width:230px;">
					<div style="color:#666; font-size:17px;">
					<?php echo $post_name; ?>
					<br />
					<span style="font-size:11px;">
					<?php echo $post_description; ?>
					</span>
					<br />
					</div>
					
					
							<?php
					$ip=$_SERVER["REMOTE_ADDR"];
					$check=Database::query("SELECT * FROM feedback WHERE ip_addres = '{$ip}' AND post_id='{$post_id}' ORDER BY id DESC");
  				    $check_rate=$check->num_rows;
  				   if($station){
  				 		$check_rate=0;
   					}
  				    if($check_rate==0){
  							 ?>
					
					<div id="rating_<?php echo $post_id; ?>" style="color:#888; font-size:12px;">
					
					<form>
					<div class="styled-select">
					<select id="rating<?php echo $post_id; ?>" name="rating<?php echo $post_id; ?>" for="carform" style="">
  					<option id="letter">Click to Rate (1-100)</option>
  						<?php for($i=100;$i>=0;$i--){ ?>	
  					<option id="numbers" onclick="showButton_<?php echo $post_id; ?>();" value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
					</select>
					</div>
					</div>
					<div id="thanks_<?php echo $post_id; ?>"  style="display:none; float:left; margin-left:10px; width:20px; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks!</div>
				</div>
				
				
				
				<div id="button_<?php echo $post_id; ?>"  style="float:left; margin-left:10px; width:20px;">
					
					<input type="button" onclick="hideRating_<?php echo $post_id; ?>(), submitRating<?php echo $post_id; ?>(),showThanks_<?php echo $post_id; ?>();" class="button4" style="margin-top:15px; -webkit-appearance: none; border-radius: 0;" value="Submit" />
					</form>
				
				</div>
				
				
				
				<?php } else{ ?>	
					<div id="thanks_<?php echo $post_id; ?>"  style="float:left; margin-left:10px; width:20px; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks!</div>
				<?php } ?>
				
				<div style="clear:both; margin-bottom:1px;">&nbsp;</div>
			</div>
		</div>

<script type="text/javascript">

function hideRating_<?php echo $post_id; ?>() {
	document.getElementById("rating_<?php echo $post_id; ?>").style.display='none';
	document.getElementById("button_<?php echo $post_id; ?>").style.display='none';
}
function showThanks_<?php echo $post_id; ?>() {
	document.getElementById("thanks_<?php echo $post_id; ?>").style.display='block';
}


function hideThanks_<?php echo $post_id; ?>() {
	document.getElementById("thanks_<?php echo $post_id; ?>").style.display='none';
}
function showRating_<?php echo $post_id; ?>() {
	document.getElementById("rating_<?php echo $post_id; ?>").style.display='block';
	document.getElementById("button_<?php echo $post_id; ?>").style.display='block';
}

function showButton_<?php echo $post_id; ?>() {
	document.getElementById("button_<?php echo $post_id; ?>").style.display='block';
}

</script>

<script>

function submitRating<?php echo $post_id; ?>() {
var rating<?php echo $post_id; ?>=$("#rating<?php echo $post_id; ?>").val();

$.post("insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>", { value: rating<?php echo $post_id; ?>},
  function(data) {

   });
   

}

function reload() {
	   <?php if($station){ ?>
   location.reload(); 
   <?php } ?>
}
</script>


	
	</div></div>
	
	
	
