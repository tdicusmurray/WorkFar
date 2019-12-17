<?php

class Work extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public function create_work($category,$title,$description,$hourly_budget,$budget_hours,$fixed_price,$job_type,$payment_type) {
		$title         = htmlentities($title);
		$description   = htmlentities($description);
		$category      = htmlentities($category);
		$hourly_budget = htmlentities($hourly_budget);
		$budget_hours  = htmlentities($budget_hours);
		if ($fixed_price == "on") $fixed_price = true;
		else $fixed_price = false;

		if ($job_type == "on") $job_type = true;
		else $job_type = false;

		if ($payment_type == "on") $payment_type = true;
		else $payment_type = false;
		$oDB = $this->oDB->prepare("
INSERT INTO
	`work` (`title`,`description`,`employer_id`,`category_id`,`budget`,`budget_hours`,`status_id`,`fixed_price`,`office`,`payment_type`,`link`)
VALUES
	( ?,?,?,?,?,?,1,?,?,?,'')
");
		$oDB->bindParam(1,$title,PDO::PARAM_STR);
		$oDB->bindParam(2,$description,PDO::PARAM_STR);
		$oDB->bindParam(3,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(4,$category,PDO::PARAM_INT);
		$oDB->bindParam(5,$hourly_budget,PDO::PARAM_STR);
		$oDB->bindParam(6,$budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(7,$fixed_price,PDO::PARAM_INT);
		$oDB->bindParam(8,$job_type,PDO::PARAM_INT);
		$oDB->bindParam(9,$payment_type,PDO::PARAM_INT);
		$oDB->execute();
		$this->send($oDB->errorInfo());
	}
	public function create_work_internal($user_id,$category,$title,$description,$hourly_budget,$budget_hours,$link = "") {
		$title         = htmlentities($title);
		$description   = htmlentities($description);
		$category      = htmlentities($category);
		$hourly_budget = htmlentities($hourly_budget);
		$budget_hours  = htmlentities($budget_hours);

		$oDB = $this->oDB->prepare("
INSERT INTO
	`work` (`title`,`description`,`employer_id`,`category_id`,`budget`,`budget_hours`,`status_id`,`fixed_price`,`link`,`payment_type`)
VALUES
	( ?,?,?,?,?,?,1,0,?,1 )
");
		$oDB->bindParam(1,$title,PDO::PARAM_STR);
		$oDB->bindParam(2,$description,PDO::PARAM_STR);
		$oDB->bindParam(3,$user_id,PDO::PARAM_INT);
		$oDB->bindParam(4,$category,PDO::PARAM_INT);
		$oDB->bindParam(5,$hourly_budget,PDO::PARAM_STR);
		$oDB->bindParam(6,$budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(7,$link,PDO::PARAM_STR);
		
		$oDB->execute();
		return $this->oDB->lastInsertId();
	}
	public function employer_end_work($work_id,$profile_comment, $communication, $availability,$knowledge,$completion) {
	$response = array();
	if (!isset($work_id) || !isset($profile_comment) || !isset($communication) || !isset($availability) 
		|| !isset($knowledge) || !isset($completion) ) {
		$response['response'] = 3; // not all fields were set, possible hack attempt
		$this->send($response);
	}
	
	$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_worker_feedback`
WHERE
	`work_id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCheckFeedbackExists = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($aCheckFeedbackExists['id'])) {
			$response['response'] = 2; // Feedback Exists Already
			$this->send($response);
		} else {
		// Make sure the work id belongs to that employer
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`
FROM
	`user`
LEFT JOIN 
	`work`
ON
	(`user`.`id` = `work`.`employer_id`)
WHERE
	`user`.`id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		
		$oDB->execute();
		$aCorrectPermissions = $oDB->fetch(PDO::FETCH_ASSOC);
		if(isset($aCorrectPermissions['id'])) {
				if($communication > 5 || $availability > 5 || $knowledge > 5 || $completion > 5 ||
					$communication < 1 || $availability < 1 || $knowledge < 1 || $completion < 1) 
					$this->hackAttempt("Tried to manipulate rating average.");
					
					
				$oDB = $this->oDB->prepare("
INSERT INTO
	`work_worker_feedback`(`work_id`,`communication`,`availability`,`knowledge`,`completion`,`profile_comment`)
VALUES ( ?, ?, ?, ?, ?, ?)");
			
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->bindParam(2,$communication,PDO::PARAM_INT);
				$oDB->bindParam(3,$availability,PDO::PARAM_INT);
				$oDB->bindParam(4,$knowledge,PDO::PARAM_INT);
				$oDB->bindParam(5,$completion,PDO::PARAM_INT);
				$profile_comment = htmlentities($profile_comment);
				$oDB->bindParam(6,$profile_comment,PDO::PARAM_STR);
				
				$oDB->execute();
				
				$oDB = $this->oDB->prepare("
UPDATE
	`work`
SET
	`status_id` = 3
WHERE
	`id` = ?");
			
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->execute();
				$response['work_id'] = $work_id;
				$response['response'] = 1;
				$this->send($response);
			}
		}
	}
	public function employer_rate_work($work_id,$profile_comment,$communication,$availability,$knowledge,$completion) {
	$response = array();
	if (!isset($work_id) || !isset($profile_comment) || !isset($communication) || !isset($availability) 
		|| !isset($knowledge) || !isset($completion) ) {
		$response['response'] = 3; // not all fields were set, possible hack attempt
		$this->send($response);
	}
	
	$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_worker_feedback`
WHERE
	`work_id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCheckFeedbackExists = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($aCheckFeedbackExists['id'])) {
			$response['response'] = 2; // Feedback Exists Already
			$this->send($response);
		} else {
		// Make sure the work id belongs to that employer
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`
FROM
	`user`
LEFT JOIN 
	`work`
ON
	(`user`.`id` = `work`.`employer_id`)
WHERE
	`user`.`id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		
		$oDB->execute();
		$aCorrectPermissions = $oDB->fetch(PDO::FETCH_ASSOC);
		if(isset($aCorrectPermissions['id'])) {
				if($communication > 5 || $availability > 5 || $knowledge > 5 || $completion > 5 ||
					$communication < 1 || $availability < 1 || $knowledge < 1 || $completion < 1) 
					$this->hackAttempt("Tried to manipulate rating average.");
					
					
				$oDB = $this->oDB->prepare("
INSERT INTO
	`work_worker_feedback`(`work_id`,`communication`,`availability`,`knowledge`,`completion`,`profile_comment`)
VALUES ( ?, ?, ?, ?, ?, ?)");
			
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->bindParam(2,$communication,PDO::PARAM_INT);
				$oDB->bindParam(3,$availability,PDO::PARAM_INT);
				$oDB->bindParam(4,$knowledge,PDO::PARAM_INT);
				$oDB->bindParam(5,$completion,PDO::PARAM_INT);
				$profile_comment = htmlentities($profile_comment);
				$oDB->bindParam(6,$profile_comment,PDO::PARAM_STR);
				
				$oDB->execute();
				
				$response['work_id'] = $work_id;
				$response['response'] = 1;
				$this->send($response);
			}
		}
	}
	public function worker_end_work($work_id,$profile_comment,$communication,$availability) {
	$response = array();
	if (!isset($work_id) || !isset($profile_comment) || !isset($communication) || !isset($availability) ) {
		$response['response'] = 3; // not all fields were set, possible hack attempt
		$this->send($response);
	}
	// Checking if feedback already exists
	$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_employer_feedback`

WHERE
	`work_id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCheckFeedbackExists = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($aCheckFeedbackExists['id'])) {
			$response['response'] = "Feedback Already Exists"; // Feedback Exists Already
			$this->send($response);
		} else {
		// Make sure the work id belongs to that worker
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`
FROM
	`user`
JOIN 
	`work`
ON
	(`user`.`id` = `work`.`worker_id`)
WHERE
	`user`.`id` = ?
AND
	`work`.`id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCorrectPermissions = $oDB->fetch(PDO::FETCH_ASSOC);

		if(isset($aCorrectPermissions['id'])) {
				if($communication > 5 || $availability > 5 || $communication < 1 || $availability < 1) 
					$this->hackAttempt("Tried to manipulate rating average.");
					
				$work_id = htmlentities($work_id);
				$communication = htmlentities($communication);
				$availability =  htmlentities($availability);
				$profile_comment = htmlentities($profile_comment);
				$oDB = $this->oDB->prepare("
INSERT INTO
	`work_employer_feedback`(`work_id`,`communication`,`availability`,`profile_comment`)
VALUES ( ?, ?, ?, ?)");
			
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->bindParam(2,$communication,PDO::PARAM_INT);
				$oDB->bindParam(3,$availability,PDO::PARAM_INT);
				$oDB->bindParam(4,$profile_comment,PDO::PARAM_STR);
				
				$oDB->execute();
				
				$oDB = $this->oDB->prepare("
UPDATE
	`work`
SET
	`status_id` = 3
WHERE
	`id` = ?");
			
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->execute();
				$response['work_id'] = $work_id;
				$response['response'] = 1;
				$this->send($response);
			} else { echo "Hack Attempt"; }
		}
	}
	public function worker_rate_work($work_id,$profile_comment,$communication,$availability) {
	$response = array();
	if (!isset($work_id) || !isset($profile_comment) || !isset($communication) || !isset($availability) ) {
		$response['response'] = 3; // not all fields were set, possible hack attempt
		$this->send($response);
	}
	// Checking if feedback already exists
	$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_employer_feedback`
WHERE
	`work_id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCheckFeedbackExists = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($aCheckFeedbackExists['id'])) {
			$response['response'] = 2; // Feedback Exists Already
			$this->send($response);
		} else {
		// Make sure the work id belongs to that worker
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`
FROM
	`user`
JOIN 
	`work`
ON
	(`user`.`id` = `work`.`worker_id`)
WHERE
	`user`.`id` = ?
AND
	`work`.`id` = ?
LIMIT 1
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCorrectPermissions = $oDB->fetch(PDO::FETCH_ASSOC);
		if(isset($aCorrectPermissions['id'])) {
				if($communication > 5 || $availability > 5 || $communication < 1 || $availability < 1) 
					$this->hackAttempt("Tried to manipulate rating average.");
					
					
				$oDB = $this->oDB->prepare("
INSERT INTO
	`work_employer_feedback`(`work_id`,`communication`,`availability`,`profile_comment`)
VALUES ( ?, ?, ?, ?)");
			
				$oDB->bindParam(1,htmlentities($work_id),PDO::PARAM_INT);
				$oDB->bindParam(2,htmlentities($communication),PDO::PARAM_INT);
				$oDB->bindParam(3,htmlentities($availability),PDO::PARAM_INT);
				$oDB->bindParam(4,htmlentities($profile_comment),PDO::PARAM_STR);
				
				$oDB->execute();

				$response['work_id'] = $work_id;
				$response['response'] = 1;
				$this->send($response);
			}
		}
	}
	public function view_work($work_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`work_category`.`name` as category_name,
	`work`.`id`,
	`work`.`title`,
	`work`.`description`,
	`work`.`budget`,
	`work`.`budget_hours`,
	`work`.`created_date`,
     AVG(`work_applicant`.`hourly_bid`) as hourly_bid_avg
FROM
	`work`
JOIN
	`work_category` ON ( `work_category`.`id` = `work`.`category_id`)
LEFT JOIN
	`work_applicant` ON (`work_applicant`.`work_id` = `work`.`id`)
WHERE
	`work`.`id` = ?
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		$aWork = $oDB->fetch(PDO::FETCH_ASSOC);
		$aWork['description'] = nl2br($aWork['description']);
		$this->send($aWork);
	}
	public function apply_work($work_id,$hourly_bid,$hours_per_week,$letter) {
	$response = array();	
	$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_applicant`
WHERE
	`worker_id` = ?
AND
	`work_id` = ?
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		
		$oDB->execute();
		$aCheckAppExists = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($aCheckAppExists['id'])) {
			$response['response'] = 2; // Application Exists Already
			$this->send($response);
		}
		
	$oDB = $this->oDB->prepare("
SELECT
	COUNT(*) AS `amt`
FROM
	`work_applicant`
WHERE
	`worker_id` = ?
AND
	`created_date`+10080 > ?
AND
	`employer_initiated` = false
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$time = time();
		$oDB->bindParam(2,$time,PDO::PARAM_STR);
		
		$oDB->execute();
		$aAmtworkApps = $oDB->fetch(PDO::FETCH_ASSOC);
		if ($aAmtworkApps['amt'] >= 10) {
			$response['response'] = 0; // Freelancer has gone over their application limit
			$this->send($response);
		}
		$oDB = $this->oDB->prepare("
SELECT
	`has_profile`
FROM
	`user`
WHERE
	`id` = ?	
");
		$oDB->bindParam(1,$_SESSION['id'], PDO::PARAM_INT);
		$oDB->execute();
		
		$has_profile = $oDB->fetch(PDO::FETCH_ASSOC);
		if ($has_profile['has_profile'] != 1) {
			$response['response'] = 3;
			$this->send($response);
		}
		$oDB = $this->oDB->prepare("
INSERT INTO
	`work_applicant` (`work_id`,`worker_id`,`hourly_bid`,`hours_per_week`,`letter`)
VALUES
	( ?,?,?,?,? )
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(3,$hourly_bid,PDO::PARAM_INT);
		$oDB->bindParam(4,$hours_per_week,PDO::PARAM_INT);
		$letter = htmlentities($letter);
		$oDB->bindParam(5,$letter,PDO::PARAM_STR);
		
		$oDB->execute();
		$response['work_id'] = $work_id;
		$response['response'] = 1; // Successfully created work application
		$oDB = $this->oDB->prepare("
SELECT `user`.`first_name`, `user`.`last_name`, `user`.`email`,`work`.`title` FROM `work`
LEFT JOIN `work_applicant` ON (`work`.`id` = `work_applicant`.`work_id`)
LEFT JOIN `user` ON (`user`.`id` = `work`.`worker_id`) WHERE `work`.`id` = ?
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		$result = $oDB->fetch(PDO::FETCH_ASSOC);
	    include_once 'controller/mail.php'; 
	    $mail_controller = new Controller_Mail();
	    $mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
								$result['email'],"","",
								$result['first_name'] . " " . $result['last_name'] . " applied for job ". $result['title'] . " at $".$hourly_bid."/hr",
								"apply_work.html",
								array(
									"worker_name" => $result['first_name'] . " " . $result['last_name'],
									 "email" =>$result['email'], "title" =>  $result['title'], "hourly_rate" => $hourly_bid
								),
								"content/index/img/logo.png");
		$this->send($response);
	}
	/* includes interview requests */
	public function my_applications() { 
		$oDB = $this->oDB->prepare("
SELECT
	`work_applicant`.`id`,
	`work_applicant_interview`.`interview_time` as `interview_time`,
	`work`.`title`,
	`employer`.`first_name`,
	`employer`.`last_name`,
	`work`.`hourly_rate`,
	`work`.`status_id`
FROM
	`work_applicant`
LEFT JOIN
	`work_applicant_interview`
ON
	(`work_applicant_interview`.`work_applicant_id` = `work_applicant`.`id`)
JOIN
	`work`
ON
	(`work_applicant`.`work_id` = `work`.`id`)
JOIN
	`user` AS `employer`
ON
	(`work`.`employer_id` = `employer`.`id`)
WHERE
	`work_applicant`.`worker_id` = ?
AND
	`work`.`status_id` = 1");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		
		while ( $aApplication = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aApplications[] = $aApplication;
		}
		if (isset($aApplications))
			$this->send($aApplications);
	}
	public function my_work() {
		$aWorks = array();
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`,
	`work`.`employer_id`,
	`work`.`hourly_rate`,
	`work`.`status_id`
FROM
	`work`
JOIN
	`user`
ON 
	( `work`.`worker_id` = `user`.`id` )
WHERE
	`email` = ?");
		$oDB->bindParam(1,$_SESSION['email'],PDO::PARAM_STR);
		$oDB->execute();
		$i = 0;
		
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
			if( $aWork['status_id'] == 3 ) {
			$oDB2 = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_employer_feedback`
WHERE
	`work_id` = ?
LIMIT 1
");
			$oDB2->bindParam(1,$aWork['id'],PDO::PARAM_INT);
			$oDB2->execute();
			if ( !($aFeedbackExists = $oDB2->fetch(PDO::FETCH_ASSOC)) )
				$aWorks[$i]['no_feedback'] = true;
			}
			$i++;
		}
		$this->send($aWorks);
	}
	public function getEmployerWork() {
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`,
	`work`.`status_id`,
	`work`.`hourly_rate`,
	`work_worker_feedback`.`id` AS `feedback_exists`,
	`worker`.`first_name`,
	`worker`.`last_name`,
	`worker`.`photograph`
FROM
	`work`
LEFT JOIN
	`user` AS `worker`
ON
	( `work`.`worker_id` = `worker`.`id` )
JOIN
	`user` AS `employer`
ON
	( `work`.`employer_id` = `employer`.`id`)
LEFT JOIN
	`work_worker_feedback`
ON
	(`work`.`id` = `work_worker_feedback`.`work_id`)
WHERE
	`employer`.`id` = ?
AND
	`work`.`status_id` < 3
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$aWorks = array();
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
		}
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`,
	`work`.`status_id`,
	`work`.`hourly_rate`,
	`work_worker_feedback`.`id` AS `feedback_exists`,
	`worker`.`first_name`,
	`worker`.`last_name`,
	`worker`.`photograph`,
	SUM(`work_payment`.`amount`) payment_total
FROM
	`work`
RIGHT JOIN
	`work_payment`
ON
	( `work_payment`.`work_id` = `work`.`id` )
LEFT JOIN
	`user` AS `worker`
ON
	( `work`.`worker_id` = `worker`.`id` )
JOIN
	`user` AS `employer`
ON
	( `work`.`employer_id` = `employer`.`id`)
LEFT JOIN
	`work_worker_feedback`
ON
	(`work`.`id` = `work_worker_feedback`.`work_id`)
WHERE
	`employer`.`id` = ?
AND
	`work`.`status_id` = 3
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
		}
		
		$this->send($aWorks);
	}
	public function getEmployerOpenWork() {
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`
FROM
	`work`
WHERE
	`work`.`employer_id` = ?
AND 
	`status_id` = 1
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_STR);
		$oDB->execute();
		$aWorks = array();
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
		}
		
		$this->send($aWorks);
	}
	public function getEmployerInterviews($work_id) {

	$oDB = $this->oDB->prepare("
SELECT
	`work_applicant`.`id` AS `work_applicant_id`,
	`work_applicant_interview`.`interview_time` as `interview_time`,
	`work`.`id` AS `work_id`,
	`worker`.`id`,
	`worker`.`first_name`,
	`worker`.`last_name`,
	`worker`.`pay_rate`,
	`worker`.`photograph`,
	`worker`.`title`,
	`worker`.`rating`
FROM
	`work_applicant`
RIGHT JOIN
	`work_applicant_interview`
ON
	(`work_applicant_interview`.`work_applicant_id` = `work_applicant`.`id`)
JOIN
	`work`
ON
	(`work`.`id` = `work_applicant`.`work_id`)
JOIN
	`user` AS `worker`
ON 
	(`work_applicant`.`worker_id` = `worker`.`id`)
JOIN
	`user` AS `employer`
ON 
	(`work`.`employer_id` = `employer`.`id`)
WHERE
	`employer`.`email` = ?
AND
	`work_applicant`.`work_id` = ?
");
		$oDB->bindParam(1,$_SESSION['email'],PDO::PARAM_STR);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
		}
		if ( isset($aWorks) )
			$this->send($aWorks);
		else $this->send(array(false));
	}
	public function getEmployerApplicants($work_id) {

	$oDB = $this->oDB->prepare("
SELECT
	`work_applicant`.`id` AS `work_applicant_id`,
	`work_applicant_interview`.`id` as interview_exists,
	`work`.`id` AS `work_id`,
	`worker`.`id`,
	`worker`.`first_name`,
	`worker`.`last_name`,
	`worker`.`pay_rate`,
	`worker`.`photograph`,
	`worker`.`title`,
	`worker`.`rating`
FROM
	`work_applicant`
LEFT JOIN
	`work_applicant_interview`
ON
	(`work_applicant_interview`.`work_applicant_id` = `work_applicant`.`id`)
JOIN
	`work`
ON
	(`work`.`id` = `work_applicant`.`work_id`)
JOIN
	`user` AS `worker`
ON 
	(`work_applicant`.`worker_id` = `worker`.`id`)
JOIN
	`user` AS `employer`
ON 
	(`work`.`employer_id` = `employer`.`id`)
WHERE
	`employer`.`email` = ?
AND
	`work_applicant`.`work_id` = ?
");
		$oDB->bindParam(1,$_SESSION['email'],PDO::PARAM_STR);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWorks[] = $aWork;
		}
		if ( isset($aWorks) )
			$this->send($aWorks);
		else $this->send(array(false));
	}
	
	/* Interview a worker who has not applied to any work */
	public function interview_worker($worker_id,$work_id) {
			$oDB = $this->oDB->prepare("
SELECT
	`id`
FROM
	`work_applicant`
WHERE
	`work_id` = ?
AND
	`worker_id` = ?
LIMIT 1
");
			$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
			$oDB->bindParam(2,$worker_id,PDO::PARAM_INT);
			$oDB->execute();
			$result = $oDB->fetch(PDO::FETCH_ASSOC);
			if (!isset($result['id'])) { // Interview request does not exist, proceed
				$oDB = $this->oDB->prepare("
SELECT `pay_rate` FROM user WHERE id = ?
");
				$oDB->bindParam(1,$worker_id,PDO::PARAM_INT);
				$oDB->execute();
				$pay_rate = $oDB->fetch(PDO::FETCH_ASSOC);

				$oDB = $this->oDB->prepare("
INSERT INTO `work_applicant` (`work_id`,`worker_id`,`employer_initiated`,`hourly_bid`)
VALUES ( ?, ?, 1, ? )
");
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->bindParam(2,$worker_id,PDO::PARAM_INT);
				$oDB->bindParam(3,$pay_rate['pay_rate'],PDO::PARAM_STR);
				$oDB->execute();
			
				$work_applicant_id = $this->oDB->lastInsertId();
				$oDB = $this->oDB->prepare("
INSERT INTO `work_applicant_interview` (`work_applicant_id`) VALUES ( ? )
");
				$oDB->bindParam(1,$work_applicant_id,PDO::PARAM_INT);
				$oDB->execute();
			$oDB = $this->oDB->prepare("
SELECT
	`first_name`,
	`last_name`,
	`email`
FROM
	`user`
WHERE
	`id` = ?
LIMIT 1
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$result = $oDB->fetch(PDO::FETCH_ASSOC);
				$oDB = $this->oDB->prepare("
SELECT
	`first_name`,
	`last_name`,
	`email`
FROM
	`user`
WHERE
	`id` = ?
LIMIT 1
	");
				$oDB->bindParam(1,$worker_id,PDO::PARAM_INT);
				$oDB->execute();
				$worker = $oDB->fetch(PDO::FETCH_ASSOC);
				$oDB = $this->oDB->prepare("
INSERT INTO `user_contact` (`user_id1`,`user_id2`)
VALUES ( ?, ? )
	");
				$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
				$oDB->bindParam(2,$worker_id,PDO::PARAM_INT);
				$oDB->execute();
				$name = $result['first_name'];
				if ($name == "") $name = "WorkFar";
			    include_once 'controller/mail.php'; 
			    $mail_controller = new Controller_Mail();
			    $mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
										 $worker['email'],"","",
										$name. " has a remote job for you.",
										"interview.html",
										array(
											"employer_name" => $name,
											 "email" => $worker['email']
										),
										"content/index/img/logo.png");
		}
	}
	public function interview_applicant($work_applicant_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work_applicant`.`worker_id`,
	`user`.`email`,
	employer.`email` as employer_email
FROM
	`work`
JOIN
	`work_applicant`
ON ( `work`.`id` = `work_applicant`.`work_id`)
JOIN
	`user`
ON (`work_applicant`.`worker_id` = `user`.`id`)
JOIN
	`user` as employer
ON (`user`.`id` = `work`.`employer_id`)
WHERE
	`work`.`employer_id` = ?
AND
	`work_applicant`.`id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$work_applicant_id,PDO::PARAM_INT);
		$oDB->execute();
		$result = $oDB->fetch(PDO::FETCH_ASSOC);
		
		if (isset($result['id'])) { // Application exists, proceed to interview
			$oDB = $this->oDB->prepare("
INSERT INTO `user_contact` (`user_id1`,`user_id2`)
VALUES ( ?, ? )
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->bindParam(2,$result['worker_id'],PDO::PARAM_INT);
			$oDB->execute();
			$oDB = $this->oDB->prepare("
INSERT INTO `work_applicant_interview` (`work_applicant_id`,`work_id`)
VALUES ( ?, ? )
");
			$oDB->bindParam(1,$work_applicant_id,PDO::PARAM_INT);
			$oDB->bindParam(2,$result['worker_id'],PDO::PARAM_INT);
			$oDB->execute();

			include_once 'controller/mail.php'; 
		    $mail_controller = new Controller_Mail();
		    $mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
									 $result['email'],"","",
									$result['employer_email']." has a remote job for you.",
									"interview.html",
									array(
										"employer_name" => $result['employer_email'],
										"email" => $result['email']
									),
									"content/index/img/logo.png");
		}
	}
	public function hire_applicant($applicant_id) {
	$oDB = $this->oDB->prepare("
SELECT
	`work`.`id` as work_id,
	`work`.`title`,
	`user`.`id` employer_id,
	`user`.`first_name`,
	`user`.`last_name`,
	 worker.id as worker_id,
	`work_applicant`.`hourly_bid`,
	`user_payment_information`.`credit_card_href`
FROM
	`work`
JOIN
	`work_applicant`
ON (`work`.`id` = `work_applicant`.`work_id`)
JOIN
	`user`
ON (user.id = work.`employer_id`)
JOIN
	user as worker
ON (worker.id = work_applicant.worker_id)
JOIN
	`user_payment_information`
ON (`user`.`id` = `user_payment_information`.`user_id`)
WHERE
	`work`.`employer_id` = ?
AND
	`work_applicant`.`id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$applicant_id,PDO::PARAM_INT);
		$oDB->execute();
		$result = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($result['work_id'])) {
			$oDB = $this->oDB->prepare("
UPDATE
	`work`
SET
	`worker_id` = ?,
	`status_id` = 2,
	`hourly_rate` = ?
WHERE
	`id` = ?
");
			$oDB->bindParam(1,$result['worker_id'],PDO::PARAM_INT);
			$oDB->bindParam(2,$result['hourly_bid'],PDO::PARAM_STR);
			$oDB->bindParam(3,$result['work_id'],PDO::PARAM_INT);
			$oDB->execute();
			

			$oDB = $this->oDB->prepare("
SELECT
	`user`.`email`
FROM
	`user`
WHERE
	`user`.`id` = ?
");
			$oDB->bindParam(1,$result['worker_id'],PDO::PARAM_INT);
			$oDB->execute();
			$worker = $oDB->fetch(PDO::FETCH_ASSOC);
			include_once 'controller/mail.php'; 
		    $mail_controller = new Controller_Mail();
		    $mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
									 $worker['email'],"","",
									$result['first_name'] . " hired you as a " .$result['title'],
									"hired.html",
									array(
										 "employer_name" => $result['first_name'] . " " . $result['last_name'],
										 "email"=> $worker['email'], "hourly_rate" => $result['hourly_bid'], 
										 "title" => $result['title']
									),
									"content/index/img/logo.png");
		    return $result;
		}
	}
	public function hire_again($work_id) {
		$oDB = $this->oDB->prepare("
UPDATE
	`work`
SET
	`status_id` = 2
WHERE
	`id` = ?
AND
	`employer_id` = ?
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
	}
	public function search_work($category_id, $min_budget,$max_budget, 
								$min_budget_hours, $max_budget_hours, $rating, $page = 0) {
		$page = ($page*7);
		if ($category_id == 0) {
$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`,
	`work`.`description`,
	`user`.`first_name` as employer_name,
	`user`.`photograph`,
	`user`.`rating`,
	`work`.`budget`,
	`work`.`budget_hours`,
	`work`.`hourly_rate`,
	`work`.`fixed_price`,
	`work`.`link`
FROM
	`work`
JOIN
	`user` ON (`user`.`id` = `work`.`employer_id`)
WHERE
	(`work`.`budget` BETWEEN ? AND ?)
AND
	(`work`.`budget_hours` BETWEEN ? AND ?)
AND
	(`user`.`rating` >= ? )
AND
	`work`.`status_id` = 1
ORDER BY `work`.`id` DESC
LIMIT ?, 250");
		$oDB->bindParam(1,$min_budget,PDO::PARAM_STR);
		$oDB->bindParam(2,$max_budget,PDO::PARAM_STR);
		$oDB->bindParam(3,$min_budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(4,$max_budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(5,$rating,PDO::PARAM_INT);
		$oDB->bindParam(6,$page,PDO::PARAM_INT);
		$oDB->execute();
		} else {
			$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`,
	`work`.`description`,
	`user`.`first_name` as employer_name,
	`user`.`photograph`,
	`user`.`rating`,
	`work`.`budget`,
	`work`.`budget_hours`,
	`work`.`hourly_rate`,
	`work`.`fixed_price`,
	`work`.`link`
FROM
	`work`
JOIN
	`user` ON (`user`.`id` = `work`.`employer_id`)
WHERE
	`work`.`category_id` = ?
AND
	(`work`.`budget` BETWEEN ? AND ?)
AND
	(`work`.`budget_hours` BETWEEN ? AND ?)
AND
	(`user`.`rating` >= ? )
AND
	`work`.`status_id` = 1
ORDER BY `work`.`id` DESC
LIMIT ?, 20");
		$oDB->bindParam(1,$category_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$min_budget,PDO::PARAM_STR);
		$oDB->bindParam(3,$max_budget,PDO::PARAM_STR);
		$oDB->bindParam(4,$min_budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(5,$max_budget_hours,PDO::PARAM_INT);
		$oDB->bindParam(6,$rating,PDO::PARAM_INT);
		$oDB->bindParam(7,$page,PDO::PARAM_INT);
		$oDB->execute();
	}
		$aWorks = array();

		
		while ( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$aWork['description'] = substr($aWork['description'],0,100) . "...";
			$aWorks[] = $aWork;
		}
		$this->send($aWorks);
	}
	public function getApplicationCount() {
		$oDB = $this->oDB->prepare("
SELECT
	COUNT(*) AS `amt`
FROM
	`work_applicant`
WHERE
	`worker_id` = ?
AND
	`created_date`+10080 > ?
AND
	`employer_initiated` = false
");
		
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$time = time();
		$oDB->bindParam(2,$time,PDO::PARAM_STR);
		
		$oDB->execute();
		$aAmtworkApps = $oDB->fetch(PDO::FETCH_ASSOC);
		$this->send($aAmtworkApps);
	}
	private function getEmployer($employer_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`first_name`,
	`photograph`,
	`rating`
FROM
	`user`
WHERE 
	`id` = ?");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		$employer = $oDB->fetch(PDO::FETCH_ASSOC);
		return $employer;
	}
	public function getHoldHref($work_id) {
		$oDB = $this->oDB->prepare("
SELECT
	credit_card_href
FROM
	user_payment_information
JOIN
	user
ON (user_payment_information.user_id = user.id)
JOIN
	`work`
ON (work.employer_id = user.id)
WHERE 
	work.`id` = ?");
		$oDB->bindParam(1, $work_id, PDO::PARAM_INT);
		$oDB->execute();
		$work = $oDB->fetch(PDO::FETCH_ASSOC);
		return $work['credit_card_href'];
	}
	public function getWorkPayments($worker_id) {
		$oDB = $this->oDB->prepare("
SELECT
	work_payment.charge_href,
	work_payment.amount,
	work.title,
	employer.first_name,
	employer.last_name
FROM
	work_payment
JOIN
	`work`
ON (work.id = work_payment.work_id)
JOIN
	user
ON (work.worker_id = user.id)
JOIN
	user as employer
ON (work.employer_id = employer.id)
WHERE 
	`user`.`id` = ?");
		$oDB->bindParam(1, $worker_id, PDO::PARAM_INT);
		$oDB->execute();
		$work_payments = array(true,array());
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[1][] = $work;
		}
		$work_payments[2] = array();
		return $work_payments;
	}
	public function getEmployerWorkPayments($employer_id) {
		$oDB = $this->oDB->prepare("
SELECT
	work_payment.charge_href,
	work_payment.amount,
	work_payment.created,
	work.title,
	user.first_name,
	user.last_name
FROM
	work_payment
JOIN
	`work`
ON (work.id = work_payment.work_id)
JOIN
	user
ON (work.worker_id = user.id)
JOIN
	user as employer
ON (work.employer_id = employer.id)
WHERE 
	`employer`.`id` = ?
ORDER BY 
	created 
DESC");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		$work_payments = array(true,array());
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[1][] = $work;
		}
		$work_payments[2] = array();
		$oDB = $this->oDB->prepare("
SELECT 
	SUM(`work_payment`.`amount`) today
FROM 
	`work_payment` 
JOIN 
	`work` 
ON 
	(`work`.`id` = `work_payment`.`work_id`) 
JOIN 
	`user` as `employer` 
ON 
	(`work`.`employer_id` = `employer`.`id`) 
WHERE 
	`employer`.`id` = ?
AND 
	`work_payment`.`created` BETWEEN NOW() - INTERVAL 1 DAY AND NOW()");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[2]['today'] = $work['today'];
		}
		$oDB = $this->oDB->prepare("
SELECT 
	SUM(`work_payment`.`amount`) this_week
FROM 
	`work_payment` 
JOIN 
	`work` 
ON 
	(`work`.`id` = `work_payment`.`work_id`) 
JOIN 
	`user` as `employer` 
ON 
	(`work`.`employer_id` = `employer`.`id`) 
WHERE 
	`employer`.`id` = ?
AND 
	`work_payment`.`created` BETWEEN NOW() - INTERVAL 7 DAY AND NOW()");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[2]['this_week'] = $work['this_week'];
		}
		$oDB = $this->oDB->prepare("
SELECT 
	SUM(`work_payment`.`amount`) last_week
FROM 
	`work_payment` 
JOIN 
	`work` 
ON 
	(`work`.`id` = `work_payment`.`work_id`) 
JOIN 
	`user` as `employer` 
ON 
	(`work`.`employer_id` = `employer`.`id`) 
WHERE 
	`employer`.`id` = ?
AND 
	`work_payment`.`created` BETWEEN NOW() - INTERVAL 14 DAY AND NOW()");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[2]['last_week'] = $work['last_week'];
		}
		$oDB = $this->oDB->prepare("
SELECT 
	SUM(`work_payment`.`amount`) as 30day
FROM 
	`work_payment` 
JOIN 
	`work` 
ON 
	(`work`.`id` = `work_payment`.`work_id`) 
JOIN 
	`user` as `employer` 
ON 
	(`work`.`employer_id` = `employer`.`id`) 
WHERE 
	`employer`.`id` = ?
AND 
	`work_payment`.`created` BETWEEN NOW() - INTERVAL 30 DAY AND NOW()");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[2]['30day'] = $work['30day'];
		}
		$oDB = $this->oDB->prepare("
SELECT 
	SUM(`work_payment`.`amount`) 365day
FROM 
	`work_payment` 
JOIN 
	`work` 
ON 
	(`work`.`id` = `work_payment`.`work_id`) 
JOIN 
	`user` as `employer` 
ON 
	(`work`.`employer_id` = `employer`.`id`) 
WHERE 
	`employer`.`id` = ?
AND 
	`work_payment`.`created` BETWEEN NOW() - INTERVAL 365 DAY AND NOW()");
		$oDB->bindParam(1, $employer_id, PDO::PARAM_INT);
		$oDB->execute();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$work_payments[2]['365day'] = $work['365day'];
		}
		return $work_payments;
	}
	public function setHoldHref($card_hold,$work_id) {
		$oDB = $this->oDB->prepare("
UPDATE work SET order_href = ? WHERE id = ?");
		$oDB->bindParam(1, $card_hold, PDO::PARAM_STR);
		$oDB->bindParam(2, $work_id, PDO::PARAM_INT);
		$oDB->execute();
	}
	public function getTopTestimonials() {
		$oDB = $this->oDB->prepare("
SELECT DISTINCT
	`work_category`.`id`,
	`work_category`.`name`,
	`work_worker_feedback`.`profile_comment`,
	`work`.`title`,
	`work_worker_feedback`.`overall`
FROM
	`work_worker_feedback`
JOIN
	`work`
ON
	(`work_worker_feedback`.`work_id` = `work`.`id`)
JOIN
	`work_category`
ON
	(`work_category`.`id` = `work`.`category_id`)
WHERE
	`work_worker_feedback`.`communication` >= 4
AND
	`work_worker_feedback`.`knowledge` >= 4
AND
	`work_worker_feedback`.`completion` >= 4
AND
	`work_worker_feedback`.`availability` >= 4
ORDER BY 
	RAND()
LIMIT 6 
");
		$oDB->execute();
		$testimonials = array();
		while ( $testimonial = $oDB->fetch(PDO::FETCH_ASSOC) )
			$testimonials[] = $testimonial;
		
		return $testimonials;
	
	}
	public function send_dispute_message($work_payment_dispute_id,$message) {
        try {
        	$oDB = $this->oDB->prepare("
SELECT
	work_payment.id
FROM
	work_payment_dispute
JOIN
	work_payment
ON (work_payment_dispute.work_payment_id = work_payment.id)
JOIN
	work
ON (work_payment.work_id = work.id)
WHERE
	work.employer_id = ?
OR
	work.worker_id = ?
");
            $oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
            $oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
            $oDB->execute();
            $work_payment = $oDB->fetch(PDO::FETCH_ASSOC);

            if (isset($work_payment)) {
	            $oDB = $this->oDB->prepare("
INSERT INTO
	`work_payment_dispute_message`
	(`work_payment_dispute_id`,`message`,`user_id`) 
VALUES
(?,?,?);
	");
	            $oDB->bindParam(1,$work_payment_dispute_id,PDO::PARAM_INT);
	            $oDB->bindParam(2,$message,PDO::PARAM_STR);
	            $oDB->bindParam(3,$_SESSION['id'],PDO::PARAM_INT);
	            $oDB->execute();
	            $work_payment_dispute_message_id = $this->oDB->lastInsertId();
	            $oDB = $this->oDB->prepare("
SELECT
	work_payment_dispute_message.datetime
FROM
	work_payment_dispute_message
WHERE
	work_payment_dispute_message.id = ?
");
            $oDB->bindParam(1,$work_payment_dispute_message_id,PDO::PARAM_INT);
            $oDB->execute();
            $work_payment_dispute_message = $oDB->fetch(PDO::FETCH_ASSOC);

	            $this->send(
	            		array(
	            			"message" => $message,
	            			"first_name" => $_SESSION['first_name'],
	            			"last_name" => $_SESSION['last_name'],
	            			"datetime" => $work_payment_dispute_message['datetime']));
            }
        } catch (Exception $e) {
            var_dump($e);
        }
	}
	public function view_disputes() {
		$oDB = $this->oDB->prepare("
SELECT 
	`work_payment_dispute`.`id`,
	`work_payment_dispute_message`.`message`,
	`work_payment_dispute_message`.`datetime`,
	`user`.`first_name`,
	`user`.`last_name`
FROM 
	`work_payment_dispute`
LEFT JOIN
	`work_payment_dispute_message`
ON
	(`work_payment_dispute`.`id` = `work_payment_dispute_message`.`work_payment_dispute_id`)
JOIN
	`work_payment`
ON 
	(`work_payment`.`id` = `work_payment_dispute`.`work_payment_id`)
JOIN
	`work`
ON
	(`work`.`id` = `work_payment`.`work_id`)
JOIN
	`user`
ON
	(`user`.`id` = `work_payment_dispute_message`.`user_id`)
WHERE
	`work_payment_dispute`.`status` = 0
AND
	(`work`.`employer_id` = ?
	OR `work`.`worker_id` = ?)
ORDER BY
	`work_payment_dispute_message`.`datetime` 
DESC
LIMIT 1
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$dispute_message = $oDB->fetch(PDO::FETCH_ASSOC);		
		return $dispute_message;
	}
	public function view_dispute($dispute_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`work_payment_dispute_message`.`message`,
	`work_payment_dispute_message`.`datetime`,
	`user`.`first_name`,
	`user`.`last_name`,
	`worktime_monitor`.`id` as worktime_monitor_id
FROM
	`work_payment_dispute`
LEFT JOIN
	`work_payment_dispute_message`
ON
	(`work_payment_dispute`.`id` = `work_payment_dispute_message`.`work_payment_dispute_id`)
JOIN
	`work_payment`
ON 
	(`work_payment`.`id` = `work_payment_dispute`.`work_payment_id`)
JOIN
	`work`
ON
	(`work`.`id` = `work_payment`.`work_id`)
JOIN
	`user`
ON
	(`user`.`id` = `work_payment_dispute_message`.`user_id`)
JOIN
	`worktime_monitor`
ON
	(`worktime_monitor`.`id` = `work_payment`.`worktime_monitor_id`)
WHERE
	`work_payment_dispute`.`id` = ?
AND
	(`work`.`employer_id` = ?
	OR `work`.`worker_id` = ?
	OR `user`.`moderator` = 1)
");
		$oDB->bindParam(1,$dispute_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(3,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$dispute_messages = array();
		while ( $dispute_message = $oDB->fetch(PDO::FETCH_ASSOC) )
			$dispute_messages[] = $dispute_message;
		
		return $dispute_messages;
	}
	public function list_work() {
		$oDB = $this->oDB->prepare("
SELECT 
	work.*,
	employer.first_name,
	employer.last_name,
	worker.first_name as worker_first_name,
	worker.last_name as worker_last_name,
	`work_category`.name as category_name
FROM 
	`work`
LEFT JOIN
	`user` as employer
ON
	(`work`.`employer_id` = employer.`id`)

LEFT JOIN
	`user` as worker
ON
	(`work`.`worker_id` = worker.`id`)
LEFT JOIN
	`work_category` 
ON
	(`work`.`category_id` = `work_category`.`id`)
ORDER BY 
`id` 
ASC
");
		$oDB->execute();
		$works = array();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) )
			$works[] = $work;
		echo "<table class='table table-inverse' border='0' style='margin: 0 auto;'>
				<tr>
					<td>Title</td>
					<td>Description</td>
					<td>Employer</td>
					<td>Worker</td>
					<td>Category</td>
					<td>Budget</td>
					<td>Budget Hours</td>
					<td>Office</td>
					";
		foreach ($works as $key => $value) 
			echo "<tr>
					<td>".htmlentities($value['title'])."</td>
					<td>".htmlentities($value['description'])."</td>
					<td>".htmlentities($value['first_name']." ".$value['last_name'])."</td>
					<td>".htmlentities($value['worker_first_name']." ".$value['worker_last_name'])."</td>
					<td>".$value['category_name']."</td>
					<td>$".$value['budget']."/hr</td>
					<td>".$value['budget_hours']."</td>
					<td>".$value['office']."</td>

				</tr>";

		echo "</tr></table>";
	}
	public function job_blast($keyword,$location) {
		require "/var/www/html/controller/jobs/vendor/autoload.php";
		$providers = [
		    'Careercast' => [],
		    'Craigslist' => [],
		    'Dice' => [],
		    'Github' => [],
		    'Govt' => [],
		    'Ieee' => [],
		    'Jobinventory' => [],
		    'Monster' => [],
		    'Stackoverflow' => []
		];
		$client = new \JobApis\Jobs\Client\JobsMulti($providers);
		$client->setKeyword($keyword)
		       ->setLocation($location)
		       ->setPage(1, 10);
		$options = [
		    'maxAge' => 30,              // Maximum age (in days) of listings
		    'maxResults' => 50,         // Maximum number of results
		    'orderBy' => 'datePosted',   // Field to order results by
		    'order' => 'desc'           // Order ('asc' or 'desc')
		];
		$jobs = $client->getAllJobs($options);
		for ( $i = 0; $i < 10; $i++ ) {
			if (null !== $jobs->get($i))
				$this->create_work_internal(5,0,$jobs->get($i)->name,"",1,1,$jobs->get($i)->url);
		}
	}
}