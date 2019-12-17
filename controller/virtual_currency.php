<?php

class Virtual_Currency extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public function virtual_rewards() {
		$oDB = $this->oDB->prepare("
SELECT `reward_item`.*,`currency`.`name` AS `currency_name` FROM `reward_item` LEFT JOIN `currency` ON (`reward_item`.`currency_id` = `currency`.`id`)
");
		$oDB->execute();
		$reward_items = array();
		while ( $reward_item = $oDB->fetch(PDO::FETCH_ASSOC)) {
			$reward_items[] = $reward_item;
		}
		$this->send($reward_items);
	}
}