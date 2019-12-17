<?php

class Talk extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public function get_user_name($chat_peer_id) {
		$oDB = $this->oDB->prepare("
SELECT `first_name`,`last_name` FROM `user` WHERE `chat_peer_id` = ?
");
		$oDB->bindParam(1,$chat_peer_id,PDO::PARAM_STR);
		$oDB->execute();
		return $oDB->fetch(PDO::FETCH_ASSOC);
	}
	public function most_recent_chat_id($user_id) {
		$oDB = $this->oDB->prepare("
SELECT `chat_peer_id` FROM `user` WHERE `id` = ?
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_STR);
		$oDB->execute();
		return $oDB->fetch(PDO::FETCH_ASSOC);
	}
	public function get_contact_list($user_id,$chat_peer_id,$chat_id) {
		$oDB = $this->oDB->prepare("
UPDATE 
	`user`
SET 
	`chat_peer_id` = ?,
	`chat_id` = ?
WHERE
	`id` = ?
LIMIT 1
");
		$oDB->bindParam(1,$chat_peer_id,PDO::PARAM_STR);
		$oDB->bindParam(2,$chat_id,PDO::PARAM_STR);
		$oDB->bindParam(3,$user_id,PDO::PARAM_INT);
		$oDB->execute();
		$contacts = array();
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`id` as user_id,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`chat_peer_id`,
	`user`.`chat_id`
FROM 
	`user_contact`
JOIN
	`user`
ON (`user`.`id` = `user_contact`.`user_id2`)
WHERE 
	`user_contact`.`user_id1` = ?
AND 
	`user`.`chat_peer_id` IS NOT NULL
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
		$oDB->execute();

		while ( $contact = $oDB->fetch(PDO::FETCH_ASSOC) )
			$contacts[] = $contact;

		$oDB = $this->oDB->prepare("
SELECT
	`user`.`id` as user_id,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`chat_peer_id`,
	`user`.`chat_id`
FROM 
	`user_contact`
JOIN
	`user`
ON (`user`.`id` = `user_contact`.`user_id1`)
WHERE 
	`user_contact`.`user_id2` = ?
AND 
	`user`.`chat_peer_id` IS NOT NULL
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
		$oDB->execute();

		while ( $contact = $oDB->fetch(PDO::FETCH_ASSOC) )
			$contacts[] = $contact;

		return $contacts;
	}

	// Called when user sends message P2P for history, called when recipient is offline
	public function record_text_message($message) {


	}
	public function translate() {


	}
}