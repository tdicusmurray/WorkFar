<?php

class Controller {
	
	protected $oDB;
	
	public function __construct() {
		try {
			$this->oDB = new PDO("mysql:dbname=workfar;host=localhost",
						"admin", // Username
						"97077040249dfe315791a352282e1c4d63155b242f7f1e0a",
						array(PDO::ATTR_PERSISTENT => true));
		} catch(PDOException $e) { exit("Exception:".$e->getMessage()); }
	}
	
	/* Send a JSON object to the browser that's requesting information */
	public function send($aJSON) { die(json_encode($aJSON)); }
	public function sendHeader($aJSON) { header(json_encode($aJSON)); }

	/* Cross Site Request Forgery for posted forms. 
	Checks to make sure the form post came from the correct person */
	public function csrf() { 
		if ( isset($_SESSION['token']) && isset($_POST['token'])) {
			if ( $_SESSION['token'] != $_POST['token'] ) die("Tokens do not match"); 
		} else die("Token not set");
		
		$_SESSION['token'] = " ";
	}
	public function get_csrf($token) { 
		if ( isset($_SESSION['token']) && isset($token)) {
			if ( $_SESSION['token'] != $token ) die("Tokens do not match"); 
		} else die("Token not set");
		
		$_SESSION['token'] = " ";
	}


	/* Display error message, change later to log these */
	public function hackAttempt($message) { exit($message); }
} 
