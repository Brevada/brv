<?php
$user_id = $_SESSION['user_id'];

$user = user($user_id);
$level = $user['level'];

if($level>2){ ?>
<style>
#email_cont {
	padding: 0px;
	width: 500px;
	margin: 0 auto;
}
.em_in {
	width: 470px;
	margin: 10px;
	font-size: 12px;
}

.slideThree {
	width: 80px;
	background: #f9f9f9;
	float:right;
	height:26px;

	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	border-radius: 50px;
	position: relative;
	
	border:1px solid #dcdcdc;
}

.slideThree:after {
	content: 'OFF';
	font: 12px/26px Arial, sans-serif;
	color: #000;
	position: absolute;
	right: 10px;
	z-index: 0;
	font-weight: bold;
	text-shadow: 1px 1px 0px rgba(255,255,255,.15);
}

.slideThree:before {
	content: 'ASK';
	font: 12px/26px Arial, sans-serif;
	color: green;
	position: absolute;
	left: 10px;
	z-index: 0;
	font-weight: bold;
}

.slideThree label {
	display: block;
	width: 34px;
	height: 20px;

	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	border-radius: 50px;

	-webkit-transition: all .4s ease;
	-moz-transition: all .4s ease;
	-o-transition: all .4s ease;
	-ms-transition: all .4s ease;
	transition: all .4s ease;
	cursor: pointer;
	position: absolute;
	top: 3px;
	left: 3px;
	z-index: 1;

	-webkit-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3);
	-moz-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3);
	box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3);
	background: #fcfff4;

	background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
}

.slideThree input[type=checkbox]:checked + label {
	left: 43px;
}

input[type=checkbox] {
	visibility: hidden;
}
</style>
<div id="email_cont">
  <div style="padding:8px; color:#777; font-weight:bold;"> <img src="/images/brevada.png" style="width:130px;" /> <br />
    Email Feedback Gathering System </div>
  <form action="/hub/includes/email/email_feedback_send.php" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
    <input id="in" class="em_in" placeholder="Email" name="email" style="font-size:15px; line-height:30px;" />
    <input id="in" class="em_in" placeholder="Subject" name="subject" style="font-size:15px; line-height:30px; margin-top:-10px; border-top:0px;" />
    <textarea id="in" class="em_in" placeholder="Message" name="message" style="resize:none; margin-top:0px; border-top:1px solid #dcdcdc; border-bottom:1px solid #dcdcdc; font-size:15px; height:150px;"></textarea>
    <?php
	$query=Database::query("SELECT `id`, `level` FROM users WHERE id='{$user_id}'");
	$level = '';
	while($rows=$query->fetch_assoc()){
	   $level=$rows['level'];
	}
	
	$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' ORDER BY id DESC");

	if($query->num_rows==0){
		echo "You have not posted any aspects.";
	}
	while($rows=$query->fetch_assoc()){
	   $post_id=$rows['id'];
	   $post_name=$rows['name'];
	
	?>
    <div style="width:450px; margin:0 auto; height:50px; line-height:50px; overflow:hidden;">
      <div class="slideThree slideThree<?php echo $post_id; ?>">
        <input type="checkbox" value="1" id="slideThree<?php echo $post_id; ?>" name="r<?php echo $post_id; ?>" checked />
        <label for="slideThree<?php echo $post_id; ?>"></label>
      </div>
      <div style="float:left; font-size:13px;"> <?php echo $post_name; ?> </div>
      <br style="clear:both;" />
    </div>
    <?php
	}
	
	?>
    <div style="width:450px; margin:0 auto; margin-top:15px;">
      <input class="button4" type="submit" value="Ask For Feedback" style="width:200px;" />
      <?php } else { ?>
      <?php $this->add(new View('../hub/includes/upgrade_button.php', array('upgrade_message' => "Upgrade To Ask For Email Feedback"))); ?>
      <?php } ?>
    </div>
  </form>
</div>