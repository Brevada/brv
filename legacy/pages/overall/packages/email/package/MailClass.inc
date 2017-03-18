<?php
require_once('../../../class.phpmailer.php');
 
class FreakMailer extends PHPMailer
{
    var $priority = 3;
    var $to_name='Robbie';
    var $to_email='robbie.goldfarb@yahoo.com';
    var $From = 'contact@brevada.com';
    var $FromName = 'contact@brevada.com';
    var $Sender = 'contact@brevada.com';
 
    function FreakMailer()
    {
        global $site;
 
        // Comes from config.php $site array
 
        if($site['smtp_mode'] == 'enabled')
        {
            $this->Host = $site['smtp_host'];
            $this->Port = $site['smtp_port'];
            if($site['smtp_username'] != '')
            {
                $this->SMTPAuth = true;
                $this->Username = $site['smtp_username'];
                $this->Password = $site['smtp_password'];
            }
            $this->Mailer = "smtp";
        }
        if(!$this->From)
        {
            $this->From = $site['from_email'];
        }
        if(!$this->FromName)
        {
            $this-> FromName = $site['from_name'];
        }
        if(!$this->Sender)
        {
            $this->Sender = $site['from_email'];
        }
        $this->Priority = $this->priority;
    }
}
?>