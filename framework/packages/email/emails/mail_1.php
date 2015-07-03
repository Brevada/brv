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


$mail->Subject  = "Brevada - Getting Started";
$mail->SetFrom( ($headers['fromEmail'] != "" ? $headers['fromEmail'] : $defaultEmail), ($headers['fromName'] != "" ? $headers['fromName'] : $defaultEmailName) );
$mail->AddReplyTo( ($headers['replyToEmail'] != "" ? $headers['replyToEmail'] : $defaultEmail), ($headers['replyToName'] != "" ? $headers['replyToName'] : $defaultEmailName) );
   
$mail->Body     = "
<style>
#head{
	font-size:12px;
	font-weight:bold;
	color:#bc0101;
	font-family:helvetica;
}
#box{
	border:1px solid #dcdcdc;
	padding:5px;
}
</style>

<span style='color:#333; font-family:helvetica; font-size:11px;'>
<strong>Welcome to Brevada! </strong><br /> 
<br />
My name is Robbie and I'm the founder of Brevada.com. Myself and the rest of the team are here to make sure you are able to gather valuable feedback in a professional way that reflects well on your business. Please feel free to let us know if there is anything at all we can do to ensure your complete satisfaction.
<br /><br />
Below is a bit of information on how to get started.
<br /><br />
<strong>Firstly</strong>, you want to upload a new picture by clicking 'change picture'. A nice and colourful picture/logo will ensure that all your Brevada material looks good.
<br /><br />
<strong>Second</strong>, upload products, services, or aspects of your business that you would like to get feedback on. This can be done by clicking the red '+ Add New Aspect' button. 
These will vary depending on the nature of your business. For example, a hotel might choose: Cleanliness, Pricing, Location, Customer Service, Facilities, and Overall Satisfaction. (Overall Satisfaction is automatically there for all users when they sign up).
<br /><br />
Now you're 100% ready to go, enjoy using the features described below, and more! 
<br /><br />
<div id='box'>
<span id='head'>Brevada Page:</span><br />
You can share your Brevada page (brevada.com/yourcompanyname) on your material, website, and receipts. This website is optimized for both mobile and desktop browsers. Here, customers can rate or comment on the aspects of your business you specified.
<br /><br />
<span id='head'>Barcode:</span><br />
You can share your profile barcode. When scanned, your barcode will open your company's Brevada page on the mobile device, customers can then give feedback right there.
<br /><br />
<span id='head'>Website Widgets:</span><br />
If you have a website, you can integrate the Website Widgets on your site allowing customers to give you feedback through our system on YOUR website.
<br /><br />
<span id='head'>Voting Station:</span><br />
The voting station is optimized for tablets and is designed for you to leave it open in your store or office and customers can give you feedback at your location.
<br /><br />
<span id='head'>Promo Material:</span><br />
To make sharing your page and getting feedback easier, Brevada provides you with some attractive promo material that has your Brevada page URL and Barcode on it. This can be found under the 'Promo Material' section.
<br /><br />
<span id='head'>Results:</span><br />
All results and comments are visible on the main page when you log on. Graphical display of ratings over time is also provided.
</div>
<br /><br />
Customers that have used Brevada so far all agree that the Brevada page and the rest of the tools provided give your business a professional edge, internet presence, improved customer communication, and of course, a great means to gathering valuable feedback.

<br /><br />

Once again, let us know if you have any questions or if there is anything we can do to help.
<br /><br />
Robbie Goldfarb<br />
Founder<br />
robbie@brevada.com<br />

<br /><br />
<span style='color:#333; font-family:helvetica; font-size:10px;'>
Visit <a href='http://brevada.com/dashboard' style='color:#cd0000;'>your profile</a> to get started! <br /> Feel free to contact us at any point at contact@brevada.com.
</font
</span>
";
$mail->WordWrap = 50;

if(!$mail->Send()) {
  //echo 'Message was not sent.';
  //echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  //echo 'Message has been sent.';
}
?>
    