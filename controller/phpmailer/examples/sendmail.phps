<?php
/**
 * This example shows sending a message using a local sendmail binary.
 */

require '../PHPMailerAutoload.php';


class Controller_Mail extends Controller {

	public function __construct() {
		parent::__construct();
	}

	/*  */
	public function send_mail($from_address,$from_name, $to_email,$to_first_name,$to_last_name,$subject,$template_page,$message,$attachments) {

		$mail = new PHPMailer;

		// Set PHPMailer to use the sendmail transport
		$mail->isSendmail();

		$mail->setFrom($from_address, $from_name);
		$mail->addReplyTo($from_address, $from_name);
		$mail->addAddress($to_email, $to_first_name . " " . $to_last_name);

		//Set the subject line
		$mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML(file_get_contents($template_page), dirname(__FILE__));

		//Replace the plain text body with one created manually
		$mail->AltBody = $message;
		//Attach an image file
		$mail->addAttachment($attachments);

		if (!$mail->send()) {
		    echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    echo "Message sent!";
}

	}
}