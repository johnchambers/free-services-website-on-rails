<?php 

require 'vendor/autoload.php';

#$emailTo = 'yourmail@example.com';
#$siteTitle = 'YourSiteTitle';

$emailTo = getenv('BUSINESS_CONTACT_EMAIL');
$siteTitle = getenv('BUSINESS_NAME');



$sendgrid_api_user = getenv('SENDGRID_USERNAME');
$sendgrid_api_key = getenv('SENDGRID_PASSWORD');




// using SendGrid's PHP Library - https://github.com/sendgrid/sendgrid-php
$sendgrid = new SendGrid($sendgrid_api_user, $sendgrid_api_key);
$email    = new SendGrid\Email();





error_reporting(E_ALL ^ E_NOTICE); // hide all basic notices from PHP

//If the form is submitted
if(isset($_POST['submitted'])) {
	
	// require a name from user
	if(trim($_POST['contactName']) === '') {
		$nameError =  'Forgot your name!'; 
		$hasError = true;
	} else {
		$name = trim($_POST['contactName']);
	}
	
	// need valid email
	if(trim($_POST['email']) === '')  {
		$emailError = 'Forgot to enter in your e-mail address.';
		$hasError = true;
	} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
		$emailError = 'You entered an invalid email address.';
		$hasError = true;
	} else {
		$sender_email = trim($_POST['email']);
	}
		
	// we need at least some content
	/*
	if(trim($_POST['comments']) === '') {
		$commentError = 'You forgot to enter a message!';
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['comments']));
		} else {
			$comments = trim($_POST['comments']);
		}
	}
	*/


	$comments = 'hi';
		
	// upon no failure errors let's email now!
	if(!isset($hasError)) {
		
		$subject = 'New message from '.$name.' through your website '.$siteTitle;
		//$sendCopy = trim($_POST['sendCopy']);
		$body = "Name: $name \n\nEmail: $sender_email \n\nMessage: $comments";
		//$headers = 'From: ' .' <'.$sender_email.'>' . "\r\n" . 'Reply-To: ' . $sender_email;

		//mail($emailTo, $subject, $body, $headers);//orig
		
		//sendgrid
		/*
		$email->addTo($emailTo)
		      ->setFrom($sendgrid_api_user)
		      ->setSubject($subject)
		      ->setHtml($body);
		*/
		$email->addTo($emailTo)
		      ->setFrom($sender_email)
		      ->setSubject($subject)
		      ->setHtml($body);

		$sendgrid->send($email);
		
		
		
        //Autorespond
		$respondSubject = 'Thank you for contacting '.$siteTitle;
		$respondBody = "Your message to $siteTitle has been delivered! \n\nWe will answer back as soon as possible.";
		$respondHeaders = 'From: ' .' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $emailTo;
		
		mail($sender_email, $respondSubject, $respondBody, $respondHeaders);
		
        // set our boolean completion value to TRUE
		$emailSent = true;
	}
}
?>