<?php

require("../package/class.phpmailer.php");

$defaultEmail="support@brevada.com";
$defaultEmailName="Brevada";

$mail = new PHPMailer();

$mail->IsSMTP();  // telling the class to use SMTP
$mail->Host="localhost"; // SMTP server
$mail->IsHTML(true);
//$mail->Port = "25";

$mail->From = "support@brevada.com";
$mail->AddAddress($email);
//$mail->SMTPDebug = 1;


$mail->Subject  = "Payment Recieved";
$mail->SetFrom( ($headers['fromEmail'] != "" ? $headers['fromEmail'] : $defaultEmail), ($headers['fromName'] != "" ? $headers['fromName'] : $defaultEmailName) );
$mail->AddReplyTo( ($headers['replyToEmail'] != "" ? $headers['replyToEmail'] : $defaultEmail), ($headers['replyToName'] != "" ? $headers['replyToName'] : $defaultEmailName) );
   
$mail->Body     = "Thank you for purchasing one year of Brevada usage, your payment has been recieved. Visit <a href='http://brevada.com/dashboard' style='color:#cd0000;'>your profile</a> to get started! <br /> Feel free to contact us at any point at contact@brevada.com";
$mail->WordWrap = 50;

if(!$mail->Send()) {
  //echo 'Message was not sent.';
  //echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  //echo 'Message has been sent.';
}
?>
    