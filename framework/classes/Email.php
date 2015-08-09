<?php
require_once 'packages/PHPMailer-5.2.10/PHPMailerAutoload.php';

class Email
{
	private $mailer = null;

	public function __construct()
	{
		$this->mailer = new PHPMailer;
		$this->mailer->isSMTP();
		$this->mailer->Host = 'mail.brevada.com';
		$this->mailer->SMTPAuth = true;
		$this->mailer->Username = 'noreply@brevada.com';
		$this->mailer->Password = 'Ma!l3r54123';
		$this->mailer->Port = 26;
		
		$this->mailer->From = 'noreply@brevada.com';
		$this->mailer->FromName = 'Brevada Inc.';
		
		$this->mailer->addReplyTo('customercare@brevada.com');
		
		$this->mailer->isHTML(true);
	}
	
	public static function build()
	{
		return new Email();
	}
	
	public function setSubject($s)
	{
		$this->mailer->Subject = $s;
		
		return $this;
	}
	
	public function setBody($s)
	{
		$this->mailer->Body = $s;
		
		return $this;
	}
	
	public function setAltBody($s)
	{
		$this->mailer->AltBody = $s;
		
		return $this;
	}
	
	public function setTo($email, $name = '')
	{
		if(empty($name)){ $name = $email; }
		$this->mailer->addAddress($email, $name);
		
		return $this;
	}
	
	public function loadTemplate($template, $variables = array())
	{
		$template = 'emails/' . $template;
		if(file_exists($template)){
			$contents = file_get_contents($template);
			
			$variables['%date%'] = date('d-m-Y H:i:s');
			$variables['%email%'] = $toEmail;
			$variables['%company_id%'] = $_SESSION['CompanyID'];
			$variables['%account_id%'] = $_SESSION['AccountID'];
			
			$needle = array_keys($variables);
			$haystack = array_values($variables);
			$contents = str_replace($needle, $haystack, $contents);
			
			$this->setBody($contents);
		}
		
		return $this;
	}
	
	public function send()
	{
		if(empty($this->mailer->AltBody)){
			$this->mailer->AltBody = 'You must enable HTML in your email client to view this email.';
		}
		
		if(empty($this->mailer->Body) || empty($this->mailer->Subject)){
			return false;
		}
		
		if(DEBUG){
			Logger:debug('Email sent: ' . var_export($this->mailer, true));
			return true;
		} else {
			return $this->mailer->send();
		}
	}
	
}
?>