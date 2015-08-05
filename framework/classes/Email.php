<?php
require_once 'packages/PHPMailer-5.2.10/PHPMailerAutoload.php';

class Email
{
	public static function getMailer()
	{
		return new PHPMailer;
	}
	
	public static function send($toEmail, $toName, $subject, $message, $altmessage='')
	{
		if(empty($toEmail)){ return false; }
	
		if(empty($toName)){ $toName = $toEmail; }
		if(empty($altmessage)){ $altmessage = 'You must enable HTML in your email client to view this email.'; }
	
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = 'mail.brevada.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@brevada.com';
		$mail->Password = 'Ma!l3r54123';
		$mail->Port = 26;
		
		$mail->From = 'noreply@brevada.com';
		$mail->FromName = 'Brevada Inc.';
		$mail->addAddress($toEmail, $toName);
		$mail->addReplyTo('customerservice@brevada.com');
		$mail->isHTML(true);
		
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AltBody = $altmessage;
		
		return $mail->send();
	}
}
?>