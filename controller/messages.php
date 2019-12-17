<?php

class Message extends Controller {
	public function save_message($message, $to, $from) {
		$oDB = $this->oDB->prepare("
INSERT INTO 
	`message` 
	(`sender_id`,`receiver_id`,`message`,`read`)
VALUES 
	( ?, ?, ?, 0 )
");
		$oDB->bindParam(1, $from, PDO::PARAM_STR);
		$oDB->bindParam(2, $to, PDO::PARAM_STR);
		$oDB->bindParam(3, $message, PDO::PARAM_STR);
		$oDB->execute();
		
	}
	public function get_unread_count() {
		
		$oDB = $this->oDB->prepare("
SELECT
	COUNT(*) AS `count`
FROM
	`message`
WHERE
	`receiver_id` = ?
AND
	`read` = 0
");
		$oDB->bindParam(1, $_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		
		$aMessageCount = $oDB->fetch(PDO::FETCH_ASSOC);
		$this->send($aMessageCount);
	}
	
}