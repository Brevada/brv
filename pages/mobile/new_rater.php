<?php
$check_rate = $this->getParameter('check_rate');
$post_id = $this->getParameter('post_id');
$ip = $this->getParameter('ip');
$country = $this->getParameter('country');
?>
<div id="select_holder">
			<?php if($check_rate==0){ ?>
				 <form name="form">
				 <script>
				$( document ).ready(function() {
  					
			$("select").change(function(){
 			$('#submit').css('background','#bc0101');
 			$('#submit').css('color','#f9f9f9');
 			$('#submit').css('cursor','pointer');
			});
			
			});
				 </script>
				 <style>
				 #submit{
				 	background:#eee;
				 	color:#999;
				 	cursor:default;
				 	
				 }
				 </style>
				 <select class="select" id="rating_<?php echo $post_id; ?>" name="rating_<?php echo $post_id; ?>" for="carform" >
   					<option id="click" value="101" selected="selected">Click to rate it from 1-100</option>
  				 	<?php for($i=100;$i>=0;$i--){ ?>	
  					<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
				</select>				
				<div  id="button_<?php echo $post_id; ?>">
				<input type="button"   onclick="submitRating<?php echo $post_id; ?>();" class="button2" id="submit" style="margin-top:0px;" value="Submit Rating" />	

				</form>		
				</div>
				<div id="thanks_<?php echo $post_id; ?>"  style="display:none;  width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
				<?php } else{ ?>
				<div id="thanks_<?php echo $post_id; ?>"  style="width:100%; font-size:11px; color:#dc0101; font-weight:bold; font-family:verdana;">Thanks for rating.</div>
				<?php } ?>
			</div>
			
			<script type="text/javascript">

function hideRating_<?php echo $post_id; ?>() {
	document.getElementById("rating_<?php echo $post_id; ?>").style.display='none';
	document.getElementById("button_<?php echo $post_id; ?>").style.display='none';
}
function showThanks_<?php echo $post_id; ?>() {
	document.getElementById("thanks_<?php echo $post_id; ?>").style.display='block';
}

function submitRating<?php echo $post_id; ?>() {
var rating<?php echo $post_id; ?>=$("#rating_<?php echo $post_id; ?>").val();

if(rating<?php echo $post_id; ?>==101){

}
else{

 hideRating_<?php echo $post_id; ?>(),
 showThanks_<?php echo $post_id; ?>(),
$.post("/overall/insert/insert_rating.php?postid=<?php echo $post_id; ?>&ipaddress=<?php echo $ip; ?>&country=<?php echo $country; ?>", { value: rating<?php echo $post_id; ?>},
  function(data) {

   });
 }  
}
</script>