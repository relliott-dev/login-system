<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This PHP script is used for sending a code activation to a user via email using phpMailer
 	* It uses HTML and sets the recipient, subject and the code
	* 
	*/

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	require __DIR__.'/PHPMailer/src/Exception.php';
	require __DIR__.'/PHPMailer/src/PHPMailer.php';
	require __DIR__.'/PHPMailer/src/SMTP.php';

	use PHPMailer\PHPMailer\PHPMailer;	
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

	function smtpmailer($email, $subj, $code)
	{
		$mail = new PHPMailer(true);

		try
		{
			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = 'ssl';
			$mail->Host = 'mail.example.com';
			$mail->Port = 465;
			$mail->Username = 'noreply@example.com';
			$mail->Password = 'yourpassword';
			
			$htmlContent = file_get_contents(__DIR__.'/passwordemail.html');
			$htmlContent = str_replace('$code', $code, $htmlContent);
			
			$mail->isHTML(true);
			$mail->setFrom('noreply@example.com', 'Support Team');
			$mail->addReplyTo('noreply@example.com', 'Support Team');
			$mail->Subject = $subj;
			$mail->Body = $htmlContent;
			$mail->addAddress($email);
			
			$mail->send();
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

?>
