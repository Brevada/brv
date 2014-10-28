<?php
$this->add(new View('../pages/mobile/header_mobile.php'));
$this->addResource('/pages/overall/packages/dygraph-combined.js');
$this->addResource('/css/mobile/hub_mobile.css');

if(!Brevada::IsLoggedIn())
{
	Brevada::Redirect('/home/login.php');
}

$user_id = $_SESSION['user_id']; 

$query=Database::query("SELECT * FROM users WHERE id='{$user_id}' LIMIT 1");

if($query->num_rows==0){
	Brevada::Redirect('/home/logout.php');
}

$name = ''; $email = ''; $url_name = ''; $active = ''; $logins = ''; $extension = ''; $type = '';
$trial = ''; $picture = ''; $expiry_date = ''; $user_extension = '';
while($rows=$query->fetch_assoc()){
   $name = $rows['name'];
   $email = $rows['email'];
   $url_name = $rows['url_name'];
   $active = $rows['active'];
   $logins = $rows['logins'];
   $extension = $rows['extension'];
   $type = $rows['type'];
   $trial = $rows['trial'];
   $picture = $rows['picture'];
   $expiry_date = $rows['expiry_date'];
   $user_extension = $rows['extension'];
}

if( $expiry_date < date("Y-m-d") ){
	$active = 'no';
}

if($active=='no'){
$message="You're Almost There!";
}
else{
$message='Membership Expired';
}
	
?>

<script>
function openPopup() {
    $('#test').show();
}

function closePopup() {
    $('#test').hide();
}
</script>



<?php if($trial==1){ ?>
<div id="trial_box">
<form name="_xclick"  action="https://www.paypal.com/cgi-bin/webscr" method="post">
   			 <input type="hidden" name="cmd" value="_xclick">
   			 <input type="hidden" name="business" value="payments@brevada.com">
    		 <input type="hidden" name="currency_code" value="USD">
    		 <input type="hidden" name="item_name" value="Brevada 1 Year Membership">
   			 <input type="hidden" name="amount" value="49.99">
   			 <input type="hidden" name="return" value="http://www.brevada.com/thanks.php">
   			 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
   			 <input type="submit" class="buttong"  name="submit" value="Activate Full Subscription Now" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
			 </form>
</div>

<?php } ?>


<?php if($active!='yes'){ ?>
<div id="locked">
	<div id="locked_main" align="center" >
		<div align="center" style="float:left;">
			<div style="color:#f9f9f9;"><strong><?php echo $message; ?></strong></div>
			<div style="margin-top:4px;"><span style="font-size:12px;">The use of Brevada.com costs $49.99.</span></div>
			<form name="_xclick"  action="https://www.paypal.com/cgi-bin/webscr" method="post">
   			 <input type="hidden" name="cmd" value="_xclick">
   			 <input type="hidden" name="business" value="payments@brevada.com">
    		 <input type="hidden" name="currency_code" value="USD">
    		 <input type="hidden" name="item_name" value="Brevada 1 Year Membership">
   			 <input type="hidden" name="amount" value="49.99">
   			 <input type="hidden" name="return" value="http://www.brevada.com/thanks.php">
   			 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
   			 <input type="submit" class="buttong"  name="submit" value="Pay Now" style="border:none; color:#f9f9f9;">
			 </form>
			 <div style="margin-top:10px;">Or</div>
			 <br />
			 <div style="margin-top:0px; font-size:12px;">Enter a promo code: <form action="promo_validate.php" method="post"><input type="text" name="promo"  style="width:30px; margin-top:10px; padding:10px; font-size:12px; outline:none; border:1px solid #f7f7f7;" /><input type="submit" value="&rarr;" name="submit" style="padding:11px; cursor:pointer; background:#f9f9f9; border:1px solid #f3f3f3; font-weight:bold; width:30px; margin-left:5px; outline:none;"></form></div>
			 <br />
			 <span style="font-size:11px; font-weight:bold;">Refresh this page after payment.</span>
		</div>
	</div>
</div>
<?php } ?>



<div  style="width:100%; margin-top:29px; height:40px; padding-top:7px;">

 	
 		<div style="float:left; display:none; margin-top:-5px; opacity:1;">
	<?php if($extension=="none"){ ?>
	<img src="/user_data/user_images/default.jpg" width="40px" />
	<?php } else{ ?>
	<img src="/user_data/user_images/<?php echo $user_id; ?>.<?php echo $user_extension; ?>" width="40px" />
	<?php } ?>
	</div>
	
	<br />

	
	 		 <style>
 		 #side_link{
 		 padding:5px; border-bottom:1px solid #dcdcdc;
 		 color:#dc0101;
 		 font-size:12px;
 		 text-decoration:none;
 		 font-style:none;
 		 cursor:pointer;
 		 background:#fcfcfc;
 		 }
 		 #side_link:hover{
 		 background:#fff;
 		 }
 		 </style>
 		 
 		  		
 		<?php 
		
		$sql ="UPDATE users SET logins=`logins`+1 WHERE id='{$user_id}'";
		Database::query($sql);
		?>
	
	 <div id="hub_side" style="display:none;">
 			<div id="hub_side_top">
 				<a  id="expanderHead6Products" data-reveal-id="unlock"><div id="side_link">Change Picture</div></a>
 				 			<script type="text/javascript">
			$(document).ready(function(){
			$("#expanderHead6Products").click(function(){
			$("#expanderContent6Products").slideToggle();
			if ($("#expanderSign6Products").text() == "+"){
			$("#expanderSign6Products").html("-")
			}
			else {
			$("#expanderSign6Products").text("+")
			}
			});
			});
			</script>
			<div id="expanderContent6Products" style="display:none; margin-top:10px; padding:4px;">
				<form action="picture_change.php" method="post" enctype="multipart/form-data"> <input type="file" name="file" /> <center><input class="button2" type="submit" name="submit" value="Change" /></center> </form>
			</div>
 				
 				<a  id="expanderHead7Products" data-reveal-id="unlock"><div id="side_link">Change Password</div></a>
 								 			<script type="text/javascript">
			$(document).ready(function(){
			$("#expanderHead7Products").click(function(){
			$("#expanderContent7Products").slideToggle();
			if ($("#expanderSign7Products").text() == "+"){
			$("#expanderSign7Products").html("-")
			}
			else {
			$("#expanderSign7Products").text("+")
			}
			});
			});
			</script>
				<div id="expanderContent7Products" style="display:none; margin-top:10px; padding:4px;">
					<form action="password_change.php" method="post">
						<input class="inp" id="password1" name="pass" value="New Password" type="text" style="width:210px;" onfocus="if(this.value == 'New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='New Password'; this.type='text';}"></input>
						<input class="inp" id="password1"  value="Retype New Password" type="text" style="width:210px;" onfocus="if(this.value == 'Retype New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='Retype New Password'; this.type='text';}"></input>
						<center><input class="button2" type="submit" value="Change" /></center>
					</form>
					<script type="text/javascript">
							window.onload=function () {
   						 document.getElementById("password1").onchange=validatePassword;
   						 document.getElementById("password2").onchange=validatePassword;
						}
						function validatePassword(){
						var pass=document.getElementById("password2").value;
						var pass2=document.getElementById("password1").value;
						if(pass1!=pass2)
    						document.getElementById("password2").setCustomValidity("Passwords Don't Match");
						else
    						document.getElementById("password2").setCustomValidity('');  
						//empty string means no validation error
						}
						</script>
				</div>
 			</div>
 			<br style="clear:both;" />
 		</div>
 		
	<div id="hub_side">
		
		<style>
		#mobile_link{
			width:100%;
			font-size:12px;
			color:#555;
			font-family:helvetica;
			font-weight:bold;
			height:20px
			line-height:20px;
			margin-top:10px;
			
		}
		#mlink_thing{
			float:left;
			padding:5px;
			line-height:20px;
		}
		</style>
		
		<a href="/hub/voting" target="_BLANK" style="text-decoration:none;">
		<div id="mobile_link">
			<div id="mlink_thing">
 			Login to Voting Station
			</div>
			
			<br style="clear:both;" />
 		
		</div>
 	 	</a>

 	</div>
 	
 	<div style="width:100%;">

 		<?php
 		$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' ORDER BY id DESC");

	
		while($rows=$query->fetch_assoc()){
  		 $post_id=$rows['id'];
  		 $active = $rows['active'];
  		 $post_name=$rows['name'];
  		 $post_extension=$rows['extension'];
  		 $post_description=$rows['description'];
  		 
		 $this->add(new View('../pages/mobile/hub_box_mobile.php', array('post_id' => $post_id, 'active' => $active, 'post_name' => $post_name, 'post_extension'=>$post_extension, 'post_description'=>$post_description, 'user_id'=>$user_id, 'extension' => $extension)));
		}
		?>
 		<?php if($trial==1){ ?>
 		<div class="message">
 		<strong>Activate full subscription</strong> to add specific aspects of your business other than just 'overall satisfaction'. (eg. customer service, location, price...)
 		</div>
 		<?php } ?>
 		

 	</div>
 	


<br style="clear:both;"/>



<div style="font-size:12px; font-family:helvetica;">
<center>
			<span style="font-size:11px;">
			
			<a href="/home/logout.php"><span style="color:#bc0101; font-size:14px;">Logout</span></a>
			
			<br style="clear:both;"/>
			<br style="clear:both;"/>
			
			&copy; 2013 brevada.com </span>
			
		
</center>
</div>

	<br style="clear:both;"/>