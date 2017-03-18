<?php
$this->addResource('/css/layout.css'); 
$user_id = $_SESSION['user_id'];
$user=user($user_id);

require_once "../framework/packages/email/package/class.phpmailer.php";

$email=$_POST['email'];
$user_id=$_POST['user_id'];
$subject=$_POST['subject'];
$message=$_POST['message'];

//Insert the email into our records
Database::query("INSERT INTO emails_sent(user_id, message, subject, emails) VALUES('{$user_id}', '{$message}', '{$subject}', '{$email}')");

$list=explode(",", $email);
$size = count($list);

for($j=0; $j<$size; $j++){

$email = strtolower(trim($list[$j]));

$query = Database::query("SELECT `url_name`, `name`, `email`, `id` FROM users WHERE `id`='{$user_id}' LIMIT 1");

$url_name = ''; $company_name = ''; $company_email = '';
while($row = $query->fetch_assoc()){
	$url_name = $row['url_name'];
	$company_name = $row['name'];
	$company_email = $row['email'];
}

$defaultEmail = $company_email;
$defaultEmailName = $company_name;

$mail = new PHPMailer();

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host="localhost"; // SMTP server
$mail->IsHTML(true);

$mail->From = $company_email;
$mail->AddAddress($email);

$mail->Subject = $subject;
$mail->SetFrom( ($headers['fromEmail'] != "" ? $headers['fromEmail'] : $defaultEmail), ($headers['fromName'] != "" ? $headers['fromName'] : $defaultEmailName) );
$mail->AddReplyTo( ($headers['replyToEmail'] != "" ? $headers['replyToEmail'] : $defaultEmail), ($headers['replyToName'] != "" ? $headers['replyToName'] : $defaultEmailName) );
   
//EMAIL PARTS

$part1="<style>
						#rad{
							width:9px;
						}
						input[type=radio]:checked + span { 
						opacity:0.7;
						}
						.overflow{
								text-overflow: ellipsis;
								width: 250px;
								height:60px;
								white-space: nowrap;
								overflow: hidden;
						}
					
					</style>
					
					<form id='form' method='get' action='http://brevada.com/overall/public/email_friendly_insert.php' >
						
						<!-- EDIT HERE -->
						<input name='email' value='{$email}' type='hidden' />
						<!-- -->
						
						<input name='user_id' value='{$user_id}' type='hidden' />
						
						<div id='whole_container' style='padding:6px; border:1px solid #dcdcdc; background:#f8f8f8;'>";
//PART2 

$part2="";
	
								$query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' AND active='yes' ORDER BY id DESC");
								
								if($query->num_rows==0){
								
								}
								while($rows=$query->fetch_assoc()){
								   $post_id=$rows['id'];
								   $post_name=$rows['name'];
								   $post_description=$rows['description'];
								   
								if(isset($_POST["r{$post_id}"])){
								 $part2 .= "<div id='line_holder' style='padding:5px; margin:5px; background:#eee; font-size:12px; color:#555; font-family:helvetica;'>
						
							
							<div style='float:left; padding:6px; 
								text-overflow: ellipsis;
								width: 150px;
								white-space: nowrap;
								overflow: hidden;
							'>
								<strong>{$post_name}</strong>
								<br />
								{$post_description}
							</div>
							
							
								<input id='rad' type='radio' name='r{$post_id}' value='101' style='display:none !important;'/>
							
								<div style='float:left; padding:6px; line-height:25px; '>
							<label style='cursor:pointer; margin-left:10px;'>
								<input id='rad' type='radio' name='r{$post_id}' value='20' style='width:6px; background:#cd0000;'/>
								<span style='
								padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
								border:1px solid #dcdcdc;
							
								text-align:center;
							
								-webkit-border-radius: 2px;
								-moz-border-radius: 2px;
								border-radius: 2px;
							
								background: #f8f8f8;'>
								Very Bad
								</span>
							</label>
							</div>
								<div style='float:left; padding:6px; line-height:25px; '>
								<label style='cursor:pointer; margin-left:6px;'>
									<input id='rad' type='radio' name='r{$post_id}' value='50' style='width:6px; background:#cd0000;'/>
									<span style='
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;'>
									Bad
									</span>
								</label>
								</div>
								<div style='float:left; padding:6px; line-height:25px; '>
								<label style='cursor:pointer; margin-left:6px;'>
									<input id='rad' type='radio' name='r" . $post_id . "' value='65' style='width:6px; background:#cd0000;'/>
									<span style='
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;'>
									Ok
									</span>
								</label>
								</div>
								<div style='float:left; padding:6px; line-height:25px; '>
								<label style='cursor:pointer; margin-left:6px;'>
									<input id='rad' type='radio' name='r{$post_id}' value='80' style='width:6px; background:#cd0000;'/>
									<span style='
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;'>
									Good
									</span>
								</label>
								</div>
								<div style='float:left; padding:6px; line-height:25px; '>
								<label style='cursor:pointer; margin-left:6px;'>
									<input id='rad' type='radio' name='r{$post_id}' value='100' style='width:6px; background:#cd0000;'/>
									<span style='
									padding:5px; margin-left:3px; font-size:12px; font-family:helvetica; color:#555;
									border:1px solid #dcdcdc;
							
									text-align:center;
							
									-webkit-border-radius: 2px;
									-moz-border-radius: 2px;
									border-radius: 2px;
							
									background: #f8f8f8;'>
									Excellent
									</span>
								</label>
								</div>
						
						
						
							<br style='clear:both;' />
						
							</div>
								 ";
								}
								
								
								}


/////


$part3="<input type='submit' width='350px' value='Submit Your Ratings' style='width:350px; padding:5px; cursor:pointer; outline:none; margin:0 auto; margin:5px; border:1px solid #cd0000; background:#f8f8f8; color:#cd0000; font-family:helvetica; font-size:12px; font-weight:bold; cursor:pointer;     -webkit-appearance: none;
    border-radius: 0;' />
					
							</form>
							
							<div class='text_clean' style='margin:5px;'>
								<!-- EDIT HERE -->
								<a href='http://brevada.com/profile.php?name={$url_name}&reviewer={$email}' target='_TOP'>Not Working?</a>
								<!-- -->
							</div>
							
							<div style='width:150px; text-align:center; margin:0 auto; font-size:11px; font-family:helvetica; padding:5px;'>
							Powered by <a href='http://brevada.com'><span style='color:#cd0000; font-weight:bold;'>Brevada</span></a>
							</div>
							
						</div>";
						

   
$mail->Body = $message . "<br /><br />" . $part1 . $part2 . $part3;
$mail->WordWrap = 50;

if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
 // echo 'Message has been sent.';
}

}

Brevada::Redirect('/dashboard');
?>