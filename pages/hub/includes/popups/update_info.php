<?php
$this->addResource('/css/layout.css'); 

$user_id = $_SESSION['user_id'];

$user = user($user_id);
$name = $user['name'];
$email = $user['email'];
$type = $user['type'];
?>
<div id="modal_title" class="text_clean">Update Info</div>
 <br style="clear:both;" />
  <div  style="margin-top:10px;">
    <div class="text_clean">Edit Info</div>
    <form action="/hub/update/info_change.php" method="post">
      <input class="inp"  name="name" value="<?php  echo $name; ?>" type="text" style="width:230px;">
      </input>
      <input class="inp" type="email"  name="email" value="<?php echo $email; ?>" style="width:230px;" >
      </input>
      <input class="inp" type="text"  name="type" value="<?php echo $type; ?>" placeholder="Company Description" type="text" style="width:280px;">
      </input>
      <br />
      <input class="button2" type="submit" value="Update" style="margin-top:6px; float:left;" />
    </form>
    <br style="clear:both;" />
    <br style="clear:both;" />
    <div class="text_clean">Change Password</div>
    <!-- Password Expander Content -->
    <div style="margin-top:5px; padding:4px;">
      <form action="/hub/update/password_change.php" method="post">
        <input class="inp" id="password1" name="pass" value="New Password" type="text" style="width:210px;" onfocus="if(this.value == 'New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='New Password'; this.type='text';}">
        </input>
        <input class="inp" id="password1"  value="Retype New Password" type="text" style="width:210px;" onfocus="if(this.value == 'Retype New Password'){this.value=''; this.type='password';}" onblur="if(this.value == ''){this.value='Retype New Password'; this.type='text';}">
        </input>
        <input class="button2" type="submit" value="Change"  style="float:left;"/>
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
      <br style="clear:both;" />
    </div>
    <br style="clear:both;" />
  </div>