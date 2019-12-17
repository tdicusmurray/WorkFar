<?php

class SEO extends Controller {
	public function get_employer_profile($id) {
		$oDB = $this->oDB->prepare("
SELECT
	`id`,
	`first_name`,
	`last_name`,
	`pay_rate`,
	`description`,
	`photograph`,
	`title`,
	`location_id`,
	`rating`,
	`has_profile`
FROM
	`user`
WHERE
	`id` = ?
LIMIT 1
");

		$oDB->bindParam(1,$id,PDO::PARAM_STR);
		$oDB->execute();
		
		$aUser[] = $oDB->fetch(PDO::FETCH_ASSOC);
		
		$oDB = $this->oDB->prepare("
SELECT
	`job`.`title`,
	`job_employer_feedback`.`communication`,
	`job_employer_feedback`.`availability`,
	`job_employer_feedback`.`profile_comment`
FROM
	`job`
LEFT JOIN
	`job_employer_feedback`
ON
	(`job`.`id` = `job_employer_feedback`.`job_id`)
WHERE
	`employer_id` = ?
AND 
	`job`.`status_id` = 3
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		
		while ($aFeedbackInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[1][] = $aFeedbackInfo;	
		return $aUser;
	}
	public function list_employers($letter) {
		$oDB = $this->oDB->prepare("
SELECT
	`id`,
	`first_name`,
	`last_name`,
	`photograph`
FROM
	`user`
WHERE
	`employer` = 1
AND
	`has_profile` = 1
AND
	SUBSTR(`first_name`,1,1) = ?
ORDER BY 
	`id` 
DESC");
		$oDB->bindParam(1, $letter, PDO::PARAM_STR);
		$oDB->execute();
		
		while ( $aUser = $oDB->fetch(PDO::FETCH_ASSOC) )
			$aUsers[] = $aUser;
		if (isset($aUsers))	
			return $aUsers;
	}
}