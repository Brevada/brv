<?php
$this->addResource('/css/mobile/post_box_mobile.css');

//GET COUNTRY
$geo = Geography::GetGeo();
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
		<div id="post_top" style="width:100%; background:#fff; border-bottom:0px solid #dcdcdc; ">
			<div style="padding:0px;">
				<div style="float:left; width:90px; overflow:hidden; height:100px;">
					<?php post_pic('100px','auto',$post_id, $id, $post_extension, $user_extension); ?>
				</div>
				<div style="float:left; margin-left:10px; width:130px;">
					<div style="color:#666; font-size:17px;">
					<?php echo $post_name; ?>
					</div>
					<div id="small_link"><?php echo $post_description; ?></div>
					
					
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
					<div class="styled-select" style="padding:0px;">
					<select id="rating<?php echo $post_id; ?>" name="rating<?php echo $post_id; ?>" for=carform" style="">
  					<option id="letter">Click to Rate (1-100)</option>
  						<?php for($=00;$i=;$i--){ ?>	
  					<option id="numbers" value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
					</select>
					</div>
					</div>
					<div id="thanks_<?php echo $post_id; ?>"  style="display:none; float:left; margin-left:10px; width:20px; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Received</div>
			
			</div>
				
			
				
				
				<div id="button_<?php echo $post_id; ?>"  style="float:left; margin-left:10px; width:20px;">
					
					<input type="button" onclick="hideRating_<?php echo $post_id; ?>(), submitRating<?php echo $post_id; ?>(),showThanks_<?php echo $post_id; ?>();" class="button4" style="margin-top:15px; -webkit-appearance: none; border-radius: 0;" value="Submit" />
					
					
					</form>
				
				</div>
				
				<?php } else{ ?>	
					<div id="thanks_<?php echo $post_id; ?>"  style="float:left; margin-left:10px; width:20px; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Received</div>
				<?php } ?>
                <br style="clear:both;" />
			</div>
		</div>
         <br style="clear:both;" />

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
var rating<?php echo $post_id; ?>=$("#rating<?php echo $post_id; ?>").val();
var user<?php echo $post_id; ?>="<?php echo $id; ?>";

$.post("insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>&user_id=<?php echo $id; ?>", { value: rating<?php echo $post_id; ?>, user_id: user<?php echo $post_id; ?>},
  function(data) {

   });
   <?php if($station){ ?>
   location.reload(); 
   <?php } ?>
}
</script>


	
	</div>
     <br style="clear:both;" />
    </div>
     <br style="clear:both;" />
    </div>
	 <br style="clear:both;" />
	
	
