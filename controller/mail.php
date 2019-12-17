<?php
require 'phpmailer/PHPMailerAutoload.php';


class Controller_Mail extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public $enabled = true;
	/*  */
	public function send_mail($from_address,$from_name, $to_email,$to_first_name,$to_last_name,$subject,$template_page,$parameters = array(),$attachments = array()) {
		if ($this->enabled) {
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPSecure = 'tls';
			$mail->SMTPAuth = true;
			$mail->Username = "tdicusmurray@gmail.com";
			$mail->Password = "Klasfad4$$$";
			$mail->setFrom($mail->Username, $from_name);
			$mail->addReplyTo($from_address, $from_name);
			$mail->addAddress($to_email, $to_first_name . " " . $to_last_name);
			$mail->Subject = $subject;
			$template = file_get_contents("controller/email_templates/" . $template_page);
			foreach ($parameters as $key => $value) {
				$template = str_replace('%'.$key.'%', $value, $template); 
			}
			$mail->msgHTML($template, dirname(__FILE__));
			$mail->send();
		}
	}
}
