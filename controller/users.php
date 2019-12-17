<?php

class User extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public function my_currency() {
$oDB = $this->oDB->prepare("
SELECT	* FROM `user_currency` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$currencies = array();
		while ( $currency = $oDB->fetch(PDO::FETCH_ASSOC)) {
			$currencies[] = $currency;
		}
		if(empty($currencies)) {
			$this->send(array(0,0,0));
		} else
			$this->send($currencies);
	}
	public function is_logged_in() {
		$oDB = $this->oDB->prepare("
SELECT `last_action` FROM `user` WHERE `id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		$time_difference = time() - $user['last_action'];
		$this->update_action(time(),$_SESSION['id']);

		if($time_difference > 86400) {
  			$oDB = $this->oDB->prepare("
UPDATE `user_currency` SET `value` = `value` + 50 WHERE `user_id` = ? AND `currency_id` = 1
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute(); 
			
			$oDB = $this->oDB->prepare("
UPDATE `user_currency` SET `value` = `value` + 10 WHERE `user_id` = ? AND `currency_id` = 2
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute(); 
			
			$oDB = $this->oDB->prepare("
UPDATE `user_currency` SET `value` = `value` + 5 WHERE `user_id` = ? AND `currency_id` = 4
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute(); 
		}
		if(isset($_SESSION['email'])) return true;
		else return false;
	}
	public function is_worker_logged_in() {
		if ($_SESSION['employer'] == 0) return true;
		else return false;
	}
	public function is_employer_logged_in() {
		if ($_SESSION['employer'] == 1) return true;
		else return false;
	}
	public function employer_status() {
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`has_profile`,
	`user`.`email_verified`,
	`work`.`worker_id`,
	`user_payment_information`.`credit_card_href`
FROM
	`user`
LEFT JOIN
	`user_payment_information`
ON (`user`.`id` = `user_payment_information`.`user_id`)
LEFT JOIN
	`work`
ON (`work`.`employer_id` = `user`.`id`)
WHERE
	`user`.`id` = ?
LIMIT 1
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$progress_result = $oDB->fetch(PDO::FETCH_ASSOC);

		$progress_result_array = array();
		if ($progress_result['has_profile'] == 1)
			$progress_result_array['profile_complete'] = true;
		else $progress_result_array['profile_complete'] = false;
		
		if ($progress_result['worker_id'] != "")
			$progress_result_array['hire_worker'] = true;
		else $progress_result_array['hire_worker'] = false;

		if ($progress_result['credit_card_href'] != "")
			$progress_result_array['payment_method'] = true;
		else $progress_result_array['payment_method'] = false;

		if ($progress_result['email_verified'] == 1)
			$progress_result_array['email_verified'] = true;
		else $progress_result_array['email_verified'] = false;
		
		return $progress_result_array;
	}
	public function worker_status() {
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`has_profile`,
	`user`.`email_verified`,
	`work`.`worker_id`,
	`user_payment_information`.`subscription_id`
FROM
	`user`
LEFT JOIN
	`user_payment_information`
ON (`user`.`id` = `user_payment_information`.`user_id`)
LEFT JOIN
	`work`
ON (`work`.`employer_id` = `user`.`id`)
WHERE
	`user`.`id` = ?
LIMIT 1
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$progress_result = $oDB->fetch(PDO::FETCH_ASSOC);

		$progress_result_array = array();
		if ($progress_result['has_profile'] == 1)
			$progress_result_array['profile_complete'] = true;
		else $progress_result_array['profile_complete'] = false;
		
		if ($progress_result['worker_id'] != "")
			$progress_result_array['first_work'] = true;
		else $progress_result_array['first_work'] = false;

		if ($progress_result['subscription_id'] != "")
			$progress_result_array['membership_paid'] = true;
		else $progress_result_array['membership_paid'] = false;

		if ($progress_result['email_verified'] == 1)
			$progress_result_array['email_verified'] = true;
		else $progress_result_array['email_verified'] = false;
		
		return $progress_result_array;
	}
	public function top_workers($desired_category_tag) {
		$desired_category = str_replace("_", " ", $desired_category_tag);

		$oDB = $this->oDB->prepare("
SELECT
	`work_category`.`id`
FROM
	`work_category`
WHERE
	`work_category`.`name` = ?
LIMIT 1
");
		$oDB->bindParam(1,$desired_category,PDO::PARAM_STR);
		$oDB->execute();
		$category = $oDB->fetch(PDO::FETCH_ASSOC);
		
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`pay_rate`,
	`user`.`title`,
	`user`.`rating`,
	`user`.`photograph`,
	`location`.`name` as location	
FROM
	`user`
JOIN
	`location`
ON 
	(`user`.`location_id` = `location`.`id`)
LEFT JOIN
	`user_category`
ON
	(`user_category`.`user_id` = `user`.`id`)
WHERE
	`user_category`.`work_category_id` = ?
ORDER BY
	`user`.`rating`,
	`user`.`hours_completed`
LIMIT 8
");
		$oDB->bindParam(1,$category['id'],PDO::PARAM_INT);
		$oDB->execute();
		
		$aTopWorkers = array();
		while ($aTopWorkersCategory = $oDB->fetch(PDO::FETCH_ASSOC)) {
			$aTopWorkers[] = $aTopWorkersCategory;
		}
	
		return $aTopWorkers;
	}
	public function search_talent($category_id, $rating, 
								  $min_hourly_rate,$max_hourly_rate,
								  $min_hours_completed,$max_hours_completed,$page = 0) {
		
		$page = ($page*5);

		if($category_id == 0) {
			$oDB = $this->oDB->prepare("
SELECT
	`user`.`id`,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`pay_rate`,
	`user`.`description`,
	`user`.`photograph`,
	`user`.`title`,
	`user`.`location_id`,
	`user`.`rating`,
	`user`.`hours_completed`
FROM
	`user`
WHERE
	`user`.`rating` >= ?
AND
	(`user`.`hours_completed` BETWEEN ? AND ?)
AND 
	(`user`.`pay_rate` BETWEEN ? AND ? )
AND
`user`.`has_profile` = 1
ORDER BY `user`.`pay_rate` ASC");
		$oDB->bindParam(1,$rating,PDO::PARAM_INT);
		$oDB->bindParam(2,$min_hours_completed,PDO::PARAM_STR);
		$oDB->bindParam(3,$max_hours_completed,PDO::PARAM_STR);
		$oDB->bindParam(4,$min_hourly_rate,PDO::PARAM_STR);
		$oDB->bindParam(5,$max_hourly_rate,PDO::PARAM_STR);
	} else {
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`id`,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`pay_rate`,
	`user`.`description`,
	`user`.`photograph`,
	`user`.`title`,
	`user`.`location_id`,
	`user`.`rating`,
	`user`.`hours_completed`
FROM
	`user`
JOIN 
	`user_category` 
ON (`user_category`.`user_id` = `user`.`id`)
WHERE
	`user_category`.`work_category_id` = ?
AND
	`user`.`rating` >= ?
AND
	(`user`.`hours_completed` BETWEEN ? AND ?)
AND 
	(`user`.`pay_rate` BETWEEN ? AND ? )
AND
`user`.`has_profile` = 1");
			$oDB->bindParam(1,$category_id,PDO::PARAM_INT);
			$oDB->bindParam(2,$rating,PDO::PARAM_INT);
		$oDB->bindParam(3,$min_hours_completed,PDO::PARAM_STR);
		$oDB->bindParam(4,$max_hours_completed,PDO::PARAM_STR);
		$oDB->bindParam(5,$min_hourly_rate,PDO::PARAM_STR);
		$oDB->bindParam(6,$max_hourly_rate,PDO::PARAM_STR);
		}
		$oDB->execute();
		while ( $aUser = $oDB->fetch(PDO::FETCH_ASSOC) )
			$aUsers[] = $aUser;
		array_unshift($aUsers, '');
		if (isset($aUsers))
			$this->send($aUsers);
		else $this->send(array(false));
	}
	public function getAllLocations() {
		$oDB = $this->oDB->prepare("SELECT * FROM `location`");
		$oDB->execute();
		$locations = array();
		while ($location = $oDB->fetch(PDO::FETCH_ASSOC))
			$locations[] = $location;
			
		$this->send($locations);	
	}
	public function getAllWorkCategories() {
		$oDB = $this->oDB->prepare("SELECT * FROM `work_category`");
		$oDB->execute();
		$categories = array();
		while ($category = $oDB->fetch(PDO::FETCH_ASSOC))
			$categories[] = $category;
			
		$this->send($categories);
	}
	public function getWorkerCategories() {
		$oDB = $this->oDB->prepare("
SELECT `work_category_id` FROM `user_category` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$categories = array();
		while ($category = $oDB->fetch(PDO::FETCH_ASSOC))
			$categories[] = $category;
		
		$oDB = $this->oDB->prepare("
SELECT COUNT(*) as work_category_amount FROM `work_category` LIMIT 1
");
		$oDB->execute();
		$category_count = $oDB->fetch(PDO::FETCH_ASSOC);
		$this->send(array($categories,$category_count));
	}
	public function has_profile() {
		$oDB = $this->oDB->prepare("
SELECT
	`has_profile`
FROM
	`user`
WHERE
	`email` = ?
LIMIT 1
");
		$oDB->bindParam(1,$_SESSION['email'],PDO::PARAM_STR);
		$oDB->execute();
		$response = $oDB->fetch(PDO::FETCH_ASSOC);
		$this->send($response);
	}
	public function update_profile($first_name, $last_name, $title, $hourly_rate, $cover_letter,$location,$categories,$original_categories = array()) {
		$oDB = $this->oDB->prepare("
UPDATE
	`user`
SET
	`first_name` = ?,
	`last_name` = ?,
	`title` = ?,
	`pay_rate` = ?,
	`description` = ?,
	`location_id` = ?,
	`has_profile` = 1
WHERE
	`id` = ?
LIMIT 1
");
		$cover_letter = $cover_letter;
		$first_name = $first_name;
		$last_name = $last_name;
		$title = $title;
		$oDB->bindParam(1,$first_name,PDO::PARAM_STR);
		$oDB->bindParam(2,$last_name,PDO::PARAM_STR);
		$oDB->bindParam(3,$title,PDO::PARAM_STR);
		$oDB->bindParam(4,$hourly_rate,PDO::PARAM_STR);
		$oDB->bindParam(5,$cover_letter,PDO::PARAM_STR);
		$oDB->bindParam(6,$location,PDO::PARAM_INT);
		$oDB->bindParam(7,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		

		if ($categories[0] === $categories[1]) 
			$this->send(array("Cannot select the same category twice."));
			
		for ($i = 0; $i < 2; $i++) {
				$oDB = $this->oDB->prepare("
DELETE FROM
	`user_category`
WHERE
	`user_id` = ?
");
				$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
				$oDB->execute();
		}
		for ($i = 0; $i < 2; $i++) {
				$oDB = $this->oDB->prepare("
INSERT INTO 
	`user_category` (`user_id`,`work_category_id`)
VALUES 
	( ?, ? )
");
				$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
				$oDB->bindParam(2,$categories[$i],PDO::PARAM_INT);
				$oDB->execute();
				}	
	}
	
	public function create_photo() {
		require_once "bulletproof/src/bulletproof.php";

		$bulletProof = new ImageUploader\BulletProof;
		try {
			$image = $bulletProof->fileTypes(array("gif", "jpg", "jpeg", "png"))
				->uploadDir("/var/www/html/view/members/worker/members")
				->limitSize(["min"=>100, "max"=>5500000]) 
				->shrink(array("width"=>288, "height"=>256))
				->upload($_FILES['photo']);
			$oDB = $this->oDB->prepare("
UPDATE
	user
SET
	photograph = ?
WHERE
	id = ?
");
			$prefix = '/var/www/html/';

			if (substr($image, 0, strlen($prefix)) == $prefix) {
			    $image = substr($image, strlen($prefix));
			} 
			$oDB->bindParam(1,$image,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		} catch(\ImageUploader\ImageUploaderException $e) {
		     echo $e->getMessage();
		}
	}
	public function create_video() {
		$allowedExts = array("webm");
		$extension = pathinfo($_FILES['video-blob']['name'], PATHINFO_EXTENSION);
		echo 'nothing';
		if (($_FILES['video-blob']["type"] == "video/webm") 
			&& in_array($extension, $allowedExts)) 
		{
			$_FILES['video-blob']["name"] = rand() .".webm";
		    while (file_exists("/var/www/html/controller/workfar_monitor_videos/" . $_FILES['video-blob']["name"])) {
		    	$_FILES['video-blob']["name"] = rand() .".webm";
		    }
		    move_uploaded_file($_FILES['video-blob']["tmp_name"], "/var/www/html/controller/workfar_monitor_videos/" . $_FILES['video-blob']["name"]);
		} else echo "Invalid file";
		$oDB = $this->oDB->prepare("
SELECT 
	work.id
FROM
	work
JOIN
	user
ON
	(user.id = work.worker_id)
WHERE
	user.id = ?
AND
	work.id = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(2,$_POST['work_id'],PDO::PARAM_INT);
		$oDB->execute();
		$work = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($work['id'])) {
			$oDB = $this->oDB->prepare("
INSERT INTO	worktime_monitor (work_id,video_name) VALUES (?,?)
");
			$oDB->bindParam(1,$work['id'],PDO::PARAM_INT);
			$oDB->bindParam(2, $_FILES['video-blob']["name"],PDO::PARAM_STR);
			$oDB->execute();
			$worktime_monitor_id = $this->oDB->lastInsertId();
			$oDB = $this->oDB->prepare("
SELECT
	COUNT(*) as count,
	timestamp
FROM
	worktime_monitor
WHERE
	work_id = ?
");
			$oDB->bindParam(1,$work['id'],PDO::PARAM_INT);
			$oDB->execute();
			$work = $oDB->fetch(PDO::FETCH_ASSOC);

			if (($work['count'] % 6) === 0 && strtotime($work['timestamp']) > time() + 600)
				return array(true,$worktime_monitor_id);
			else return array(false);
		}
	}
	public function login($sEmail, $sPassword) {
		if ($sEmail === "") $this->send("LOGIN FAILED");
		$oDB = $this->oDB->prepare("
SELECT
	`id`,
	`email`,
	`password`,
	`user_salt`,
	`first_name`,
	`last_name`,
	`pay_rate`,
	`employer`
FROM
	`user`
WHERE
	`email` = ?
LIMIT 1
");
		$oDB->bindParam(1,$sEmail,PDO::PARAM_STR);
		$oDB->execute();
		$aUser = $oDB->fetch(PDO::FETCH_ASSOC);
		
		if ($aUser['password'] === $this->encrypt_password($sPassword, $aUser['user_salt']))
		{
			$_SESSION['email'] = $aUser['email'];
			$_SESSION['id'] = $aUser['id'];
			$_SESSION['employer'] = $aUser['employer'];
			$_SESSION['pay_rate'] = $aUser['pay_rate'];
			$_SESSION['first_name'] = $aUser['first_name'];
			$_SESSION['last_name'] = $aUser['last_name'];
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$aLoginResponse = array(true,$_SESSION['email'],$_SESSION['employer']);
		}
		else
			$aLoginResponse = array("LOGIN FAILED");
		return $aLoginResponse;
	}
	public function getWork() {
	
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`id`,
	`work`.`title`
FROM
	`work`
JOIN
	`user`
ON (`work`.`worker_id` = `user`.`id`)
WHERE
	`user`.`email` = ?
AND
	`work`.`status_id` = 2
");
		$oDB->bindParam(1,$_SESSION['email'],PDO::PARAM_STR);
		$oDB->execute();
		$aWorks = array();
		while( $aWork = $oDB->fetch(PDO::FETCH_ASSOC) )
			$aWorks[] = $aWork;
			
		$this->send($aWorks);
	}
	public function get_employer_profile($user_email) {
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
	`email` = ?
LIMIT 1
");

		$oDB->bindParam(1,$user_email,PDO::PARAM_STR);
		$oDB->execute();
		
		$aUser[] = $oDB->fetch(PDO::FETCH_ASSOC);
		
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`title`,
	`work_employer_feedback`.`communication`,
	`work_employer_feedback`.`availability`,
	`work_employer_feedback`.`profile_comment`
FROM
	`work`
LEFT JOIN
	`work_employer_feedback`
ON
	(`work`.`id` = `work_employer_feedback`.`work_id`)
WHERE
	`employer_id` = ?
AND 
	`work`.`status_id` = 3
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		$aUser[0]['id'] = 0; // Do not give out the user id's
		
		while ($aFeedbackInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[1][] = $aFeedbackInfo;	
			
		$this->send($aUser);
	}
	
	public function get_employer_profile_by_work_id($work_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`user`.`id`,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`pay_rate`,
	`user`.`description`,
	`user`.`photograph`,
	`user`.`title`,
	`location`.`name` as location,
	`user`.`rating`,
	`user`.`has_profile`
FROM
	`user`
JOIN 
	`work` ON (`user`.`id` = `work`.`employer_id`)
LEFT JOIN 
	`location` ON (`user`.`location_id` = `location`.`id`)
WHERE
	`work`.`id` = ?
LIMIT 1
");

		$oDB->bindParam(1,$work_id,PDO::PARAM_STR);
		$oDB->execute();
		
		$aUser[] = $oDB->fetch(PDO::FETCH_ASSOC);
		
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`title`,
	`work_employer_feedback`.`communication`,
	`work_employer_feedback`.`availability`,
	`work_employer_feedback`.`profile_comment`
FROM
	`work`
LEFT JOIN
	`work_employer_feedback`
ON
	(`work`.`id` = `work_employer_feedback`.`work_id`)
WHERE
	`employer_id` = ?
AND 
	`work`.`status_id` = 3
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		$aUser[0]['id'] = 0; // Do not give out the user id's
		
		while ($aFeedbackInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[1][] = $aFeedbackInfo;	
			
		$this->send($aUser);
	}
	public function get_worker_profile_by_ID($id) {
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
		
		$aUser[0] = $oDB->fetch(PDO::FETCH_ASSOC);
		$aUser[0]['orig_description'] = $aUser[0]['description'];
		$aUser[0]['description'] = $aUser[0]['description'];
		
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`title`,
	`work_worker_feedback`.`communication`,
	`work_worker_feedback`.`knowledge`,
	`work_worker_feedback`.`completion`,
	`work_worker_feedback`.`availability`,
	`work_worker_feedback`.`profile_comment`
FROM
	`work`
LEFT JOIN
	`work_worker_feedback`
ON
	(`work`.`id` = `work_worker_feedback`.`work_id`)
WHERE
	`worker_id` = ?
AND 
	`work`.`status_id` = 3
");

		$oDB->bindParam(1,$id,PDO::PARAM_INT);
		$oDB->execute();
		while ($aFeedbackInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[1][] = $aFeedbackInfo;
		$oDB->execute();
		
		$oDB = $this->oDB->prepare("
SELECT
	`name`
FROM
	`user_skill`
WHERE
	`user_id` = ?
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		while ($aSkillInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[2][] = $aSkillInfo;
			
			
		$this->send($aUser);
	}
	public function get_worker_profile($user_email) {
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
	`email` = ?
LIMIT 1
");

		$oDB->bindParam(1,$user_email,PDO::PARAM_STR);
		$oDB->execute();
		
		$aUser[0] = $oDB->fetch(PDO::FETCH_ASSOC);
		$aUser[0]['orig_description'] = $aUser[0]['description'];
		$aUser[0]['description'] = $aUser[0]['description'];
		
		$oDB = $this->oDB->prepare("
SELECT
	`work`.`title`,
	`work_worker_feedback`.`communication`,
	`work_worker_feedback`.`knowledge`,
	`work_worker_feedback`.`completion`,
	`work_worker_feedback`.`availability`,
	`work_worker_feedback`.`profile_comment`
FROM
	`work`
LEFT JOIN
	`work_worker_feedback`
ON
	(`work`.`id` = `work_worker_feedback`.`work_id`)
WHERE
	`worker_id` = ?
AND 
	`work`.`status_id` = 3
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		$aUser[0]['id'] = 0; // Do not give out the user id's
		while ($aFeedbackInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[1][] = $aFeedbackInfo;
		$oDB->execute();
			$oDB = $this->oDB->prepare("
SELECT
	*
FROM
	`user_skill`
WHERE
	`user_id` = ?
");
		$oDB->bindParam(1,$aUser[0]['id'],PDO::PARAM_INT);
		$oDB->execute();
		while ($aSkillInfo = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aUser[2][] = $aSkillInfo;
			
		$aUser[0]['share_link'] = $this->generate_share_link($_SESSION['id']);
		$this->send($aUser);
	}
	public function create_account($sEmail, $sPassword) {
		if ($this->existing_account($sEmail))
			$this->send(array('Existing Account.'));
			
		$iUserSalt = rand();
		$sPasswordActual = $sPassword;
		$sPassword = $this->encrypt_password($sPassword,$iUserSalt);
		$oDB = $this->oDB->prepare("
INSERT INTO
	`user`
	(`email`,`password`,`user_salt`,`employer`,`IP`,`rating`,`photograph`,`linkedin`)
VALUES
	( ?, ?, ?, 0, ?,5,'/view/members/worker/members/new_user.png',0)
");
		$sEmail2 = rtrim($sEmail);
		$oDB->bindParam(1,$sEmail2,PDO::PARAM_STR);
		$oDB->bindParam(2,$sPassword,PDO::PARAM_STR);
		$oDB->bindParam(3,$iUserSalt,PDO::PARAM_INT);
		$oDB->bindParam(4,$_SERVER['REMOTE_ADDR'],PDO::PARAM_STR);
		$oDB->execute();

		$email_verify_hash = rand();
		$user_id = $this->oDB->lastInsertId();
		$oDB = $this->oDB->prepare("
INSERT INTO 
	`user_email_verify`
	(`user_id`,`hash`)
VALUES
	(?, ?)
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$email_verify_hash,PDO::PARAM_STR);
		$oDB->execute();

		if (isset($_POST['worker_id']) && $_POST['worker_id'] != "") {
			include_once 'controller/work.php';
			$work = new Work();
			$oDB = $this->oDB->prepare("
SELECT
	`user`.`title`,
	`user`.`description`,
	`user`.`pay_rate`,
	`user_category`.`work_category_id`
FROM
	`user`
LEFT JOIN
	`user_category`
ON
	`user`.`id` = `user_category`.`user_id`
WHERE
	`user`.`id` = ?
LIMIT 1
");
			$oDB->bindParam(1,$_POST['worker_id'],PDO::PARAM_INT);
			$oDB->execute();
			$worker = $oDB->fetch(PDO::FETCH_ASSOC);
			$work_id = $work->create_work_internal($user_id,$worker['work_category_id'],$worker['title'],$worker['description'],$worker['pay_rate'],20);
			$work->interview_worker($_POST['worker_id'],$work_id);
			$oDB = $this->oDB->prepare("
INSERT INTO `user_contact` 
(`user_id1`,`user_id2`)
VALUES ( ?, ? )
	");
			$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
			$oDB->bindParam(2,$_POST['worker_id'],PDO::PARAM_INT);
			$oDB->execute();
		}
		include_once 'controller/mail.php'; 
		$mail_controller = new Controller_Mail();

		$mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
									 $sEmail,"","",
									"Welcome to WorkFar","email_verification.html",
									array(
										"hash" => $email_verify_hash,
										"email" => $sEmail,
									),
									"content/index/img/logo.png");
		$this->send($this->login($sEmail, $sPasswordActual));
	}
	public function forgot_password($email) {
		$email_verify_hash = rand();
			$oDB = $this->oDB->prepare("
SELECT id FROM user WHERE email = ? LIMIT 1
");
		$oDB->bindParam(1,$email,PDO::PARAM_STR);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		$oDB = $this->oDB->prepare("
UPDATE `user_email_verify` set `hash` = ? where user_id = ?
");
		$oDB->bindParam(1,$email_verify_hash,PDO::PARAM_STR);
		$oDB->bindParam(2,$user['id'],PDO::PARAM_INT);
		$oDB->execute(); 
		include_once 'controller/mail.php'; 
		$mail_controller = new Controller_Mail();

		$mail_controller->send_mail("tdicusmurray@gmail.com","Teddy, Job Recruiter",
									 $email,"","",
									"Change WorkFar.com password?","forgot_password.html",
									array(
										"hash" => $email_verify_hash,
										"email" => $email,
									),
									"content/index/img/logo.png");
	}
	public function get_gigs($user_id) {
		$oDB = $this->oDB->prepare("
SELECT * FROM gig LEFT JOIN gig_package ON (gig_package.gig_id = gig.id) WHERE user_id = ?
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
		$oDB->execute();
		$gigs = array();
		while ( $gig = $oDB->fetch(PDO::FETCH_ASSOC) )
			$gigs[] = $gig;
		return $gigs;
	}
	public function change_password($hash,$email,$new_password) {
			$oDB = $this->oDB->prepare("
SELECT
	`user_email_verify`.`hash`,
	`user`.`email`,
	`user`.`id`,
	`user`.`user_salt`
FROM 
	`user_email_verify`
JOIN
	`user`
ON (`user_email_verify`.`user_id` = `user`.`id`)
WHERE
	`user_email_verify`.`hash` = ?
AND 
	`user`.`email` = ?
LIMIT 1
");
		$oDB->bindParam(1,$hash,PDO::PARAM_STR);
		$oDB->bindParam(2,$email,PDO::PARAM_STR);
		$oDB->execute();
		$user_email_verify = $oDB->fetch(PDO::FETCH_ASSOC);
		if ($user_email_verify) {
			$oDB = $this->oDB->prepare("
UPDATE `user` SET `password` = ? WHERE `id` = ?
");
			$oDB->bindParam(1,$this->encrypt_password($new_password,$user_email_verify['user_salt']),PDO::PARAM_STR);
			$oDB->bindParam(2,$user_email_verify['id'],PDO::PARAM_INT);
			$oDB->execute();
		}
		header("Location: https://WorkFar.com");
	}
	public function email_verify($hash,$email) {
	$email = str_replace(' ', '+', $email);
	$oDB = $this->oDB->prepare("
SELECT
	`user_email_verify`.`hash`,
	`user`.`email`,
	`user`.`id`
FROM 
	`user_email_verify`
JOIN
	`user`
ON (`user_email_verify`.`user_id` = `user`.`id`)
WHERE
	`user_email_verify`.`hash` = ?
AND 
	`user`.`email` = ?
LIMIT 1
");
		$oDB->bindParam(1,$hash,PDO::PARAM_STR);
		$oDB->bindParam(2,$email,PDO::PARAM_STR);
		$oDB->execute();
		$user_email_verify = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($user_email_verify)) {

			$oDB = $this->oDB->prepare("
UPDATE `user` SET `email_verified` = 1 WHERE `id` = ?
");
			$oDB->bindParam(1,$user_email_verify['id'],PDO::PARAM_INT);
			$oDB->execute();

			header("Location: /dashboard");
		} else echo "Invalid Entry";

	}
	public function update_action($time,$user_id) {
		$oDB = $this->oDB->prepare("
UPDATE `user` SET `last_action` = ? WHERE `id` = ?
");
		$oDB->bindParam(1,$time,PDO::PARAM_INT);
		$oDB->bindParam(2,$user_id,PDO::PARAM_INT);
		$oDB->execute();
	}
	public function customerExists() {
		$oDB = $this->oDB->prepare("
SELECT
	 credit_card_href
FROM
	 `user_payment_information`
 WHERE 
 	user_id = ?
 LIMIT 1
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$card_profile = $oDB->fetch(PDO::FETCH_ASSOC);

		if (isset($card_profile['credit_card_href']) && $card_profile['credit_card_href'] != "") return true;
		else return false;
	}
	public function updateCreditCardProfile($href) {
		include_once("controller/marketplace.php");
		$marketplace = new Marketplace();
		$customer_href = $marketplace->create_customer($href);
		$oDB = $this->oDB->prepare("
SELECT id FROM	`user_payment_information` WHERE user_id = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$payment_information = $oDB->fetch(PDO::FETCH_ASSOC);
		if (isset($payment_information['id'])) {
			$oDB = $this->oDB->prepare("
UPDATE `user_payment_information` SET credit_card_href = ? WHERE user_id = ?
");
			$oDB->bindParam(1,$customer_href,PDO::PARAM_INT);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$payment_information = $oDB->fetch(PDO::FETCH_ASSOC);			
		}
		else {
			$oDB = $this->oDB->prepare("
INSERT INTO  
	`user_payment_information` 
	(`user_id`,`credit_card_href`) 
VALUES 
	( ?, ? )
");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->bindParam(2,$customer_href,PDO::PARAM_STR);
			$oDB->execute();
		}
	}
	public function updateCustomer($href) {
		$oDB = $this->oDB->prepare("
SELECT id FROM `user_payment_information` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		if ( isset($user['id']) ) {
			$oDB = $this->oDB->prepare("
UPDATE `user_payment_information` SET customer_href = ? WHERE id = ?
");
			$oDB->bindParam(1,$href,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		} else {
			$oDB = $this->oDB->prepare("
INSERT INTO `user_payment_information` (`customer_href`,`user_id`) VALUES (?,?)
");
			$oDB->bindParam(1,$href,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		}
	}
	public function getCustomerByWorkID($work_id) {
		$oDB = $this->oDB->prepare("
SELECT
	`user_payment_information`.`credit_card_href` as customer_href
FROM
	`work`
JOIN 
	`user_payment_information`
ON
	(`work`.`employer_id` = `user_payment_information`.`user_id`)
WHERE
	`work`.`work_id` = ?
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		$user_payment_information = $oDB->fetch(PDO::FETCH_ASSOC);
		return $user_payment_information['customer_href'];
	}
	public function getBalance() {
		$oDB = $this->oDB->prepare("
SELECT balance FROM user WHERE id = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);
		$this->send($user);
	}
	public function myBalance() {
		$oDB = $this->oDB->prepare("
SELECT balance FROM user WHERE id = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);
		return $user['balance'];
	}
	public function addCharge($charge,$work_id,$worktime_monitor_id,$amount) {
		echo var_dump($charge);
		$oDB = $this->oDB->prepare("
INSERT INTO `work_payment` (`charge_href`,`work_id`,`worktime_monitor_id`,`amount`) VALUES (?,?,?,?)
");
		$oDB->bindParam(1,$charge['id'],PDO::PARAM_STR);
		$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
		$oDB->bindParam(3,$worktime_monitor_id,PDO::PARAM_INT);
		$oDB->bindParam(4,$amount,PDO::PARAM_INT);
		$oDB->execute();

		$oDB = $this->oDB->prepare("
SELECT
	user.balance,
	user.id
FROM
	user
JOIN
	work
ON
	(user.id = work.worker_id)
WHERE
	work.id = ?
");
		$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		$oDB = $this->oDB->prepare("
UPDATE 
	user
SET 
	balance = ?
WHERE
	id = ?
");
		$new_balance = $user['balance'] + $amount;
		$oDB->bindParam(1,$new_balance,PDO::PARAM_INT);
		$oDB->bindParam(2,$user['id'],PDO::PARAM_INT);
		$oDB->execute();
	}
	public function getCustomer($user_id) {
		$oDB = $this->oDB->prepare("
SELECT `credit_card_href` FROM `user_payment_information` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);
		return $user['credit_card_href'];
	}
	public function saveMembership($subscription_id) {
		$oDB = $this->oDB->prepare("
SELECT id FROM `user_payment_information` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		if ( isset($user['id']) ) {
			$oDB = $this->oDB->prepare("
UPDATE `user_payment_information` SET subscription_id = ? WHERE user_id = ?
");
			$oDB->bindParam(1,$subscription_id,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		} else {
			$oDB = $this->oDB->prepare("
INSERT INTO `user_payment_information` (`subscription_id`,`user_id`) VALUES (?,?)
");
			$oDB->bindParam(1,$subscription_id,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		}
	}
	public function hasMembership() {
			$oDB = $this->oDB->prepare("
SELECT `subscription_id` FROM `user_payment_information` WHERE `user_id` = ?");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$customer = $oDB->fetch(PDO::FETCH_ASSOC);
			if (isset($customer['subscription_id']) && $customer['subscription_id'] != "") {
				return true;
			}
			else return false;
	}
	public function getWorkerHref() {
			$oDB = $this->oDB->prepare("
SELECT `credit_card_href` FROM `user_payment_information` WHERE `user_id` = ?");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$customer = $oDB->fetch(PDO::FETCH_ASSOC);
			return $customer['credit_card_href'];
	}
	public function getWorkerRecipient() {
			$oDB = $this->oDB->prepare("
SELECT `bank_href` FROM `user_payment_information` WHERE `user_id` = ?");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$customer = $oDB->fetch(PDO::FETCH_ASSOC);
			return $customer['bank_href'];
	}
	public function setWorkerRecipient($recipient_id) {
		$oDB = $this->oDB->prepare("
SELECT id FROM `user_payment_information` WHERE `user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$user = $oDB->fetch(PDO::FETCH_ASSOC);

		if ( isset($user['id']) ) {
			$oDB = $this->oDB->prepare("
UPDATE `user_payment_information` SET bank_href = ? WHERE user_id = ?
");
			$oDB->bindParam(1,$recipient_id,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		} else {
			$oDB = $this->oDB->prepare("
INSERT INTO `user_payment_information` (`bank_href`,`user_id`) VALUES (?,?)
");
			$oDB->bindParam(1,$recipient_id,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		}
	}
	public function getEmployerCCHref() {
			$oDB = $this->oDB->prepare("
SELECT `credit_card_href` FROM `user_payment_information` WHERE `user_id` = ?");
			$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$customer = $oDB->fetch(PDO::FETCH_ASSOC);
			return $customer['credit_card_href'];
	}
	public function getEmployerCCHrefByWorkId($work_id) {
			$oDB = $this->oDB->prepare("
SELECT `credit_card_href` FROM `user_payment_information` JOIN work ON (work.employer_id = user_payment_information.user_id) WHERE work.`id` = ?");
			$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
			$oDB->execute();
			$customer = $oDB->fetch(PDO::FETCH_ASSOC);
			return $customer['credit_card_href'];
	}
	public function getPayRate($work_id) {
			$oDB = $this->oDB->prepare("
SELECT `hourly_rate` FROM `work` JOIN `user` ON (work.worker_id = user.id) WHERE `work`.`id` = ?
"); 
			$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
			$oDB->execute();
			$work = $oDB->fetch(PDO::FETCH_ASSOC);

			return $work['hourly_rate'];
	}

	public function setPayRate($work_id) {
			$oDB = $this->oDB->prepare("
SELECT `hourly_bid` FROM `work_applicant` WHERE work_id = ?
");
			$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
			$oDB->execute();
			$work = $oDB->fetch(PDO::FETCH_ASSOC);

			$oDB = $this->oDB->prepare("
UPDATE `work` SET `hourly_rate` = ? WHERE `id` = ?
");
			$oDB->bindParam(1,$work['hourly_bid'],PDO::PARAM_STR);
			$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
			$oDB->execute();
			$oDB = $this->oDB->prepare("
SELECT `hourly_rate` FROM `work` WHERE `id` = ?
");
			$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
			$oDB->execute();
			$work = $oDB->fetch(PDO::FETCH_ASSOC);

			return $work['hourly_rate'];
	}
	private function existing_account($sEmail) {
	
		$oDB = $this->oDB->prepare("	
SELECT
	`email`
FROM
	`user`
WHERE
	`email` = ?
");
		$sEmail2 = rtrim($sEmail);
		$oDB->bindParam(1,$sEmail2,PDO::PARAM_STR);
		$oDB->execute();
		$aUser = $oDB->fetch(PDO::FETCH_ASSOC);

		if ($aUser['email'] === $sEmail)
			return true;
		else return false;
	}
	/* TODO UNFINISHED */
	public function login_facebook() {
		require_once( '/var/www/html/controller/hybridauth/Hybrid/thirdparty/Facebook/autoload.php' );
		  $config = array(
		      "base_url" => "auth",
		      "providers" => array (
		        "Facebook" => array (
		          "enabled" => true,
		          "keys"    => array ( "id" => "676956372515686", "secret" => "0a161718a1929492cdda50c8c4aed802" ),
		          "scope"   => ['email', 'user_about_me', 'user_birthday', 'user_hometown'], // optional
		          "display" => "popup" // optional
		    )));
		 
		    require_once( '/var/www/html/controller/hybridauth/Hybrid/Auth.php' );
		 
		    $hybridauth = new Hybrid_Auth( $config );
		 
		    $adapter = $hybridauth->authenticate( "Facebook" );
		 
		    $user_profile = $adapter->getUserProfile();
		    die(var_dump($user_profile));
	}
	private function encrypt_password($sPassword,$sUserSalt,$sGlobalSalt = "qwe323qwref") {
		return sha1(md5($sPassword.$sUserSalt.$sGlobalSalt));
	}
	public function list_member($user_id) {
		$oDB = $this->oDB->prepare("
SELECT 
	`first_name`,
	`last_name`,
	`pay_rate`,
	`description`,
	`title`,
	`rating`,
	`hours_completed`,
	`photograph`,
	`id`
FROM 
	`user`
WHERE
	`user`.`id` = ?
");
		$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
		$oDB->execute();
		$works = array();
		while ( $work = $oDB->fetch(PDO::FETCH_ASSOC) )
			$works[] = $work;
		$gigs = $this->get_gigs($user_id);
		foreach ($gigs as $key => $value) 
			echo $value['name'] . " " . $value['description'] . "<form id='buy_gig" . $value['id'] .  "' class='buy_gig'><input type='hidden' value='" . $value['price'] . "' name='gig_price' /><button class='btn btn-danger'>Instant Buy ($". $value['price'] .")</button></form>";
?>

		<table class='table table-inverse' border='0' style='margin: 0 auto;'>
<?php
		foreach ($works as $key => $value) 
			echo "<tr>
					<td><img alt='" .$value['first_name']." ".$value['last_name']." Remote Work" ."' src='https://workfar.com/".$value['photograph']."' width='256' height='256' /><br>".$value['first_name']." ".$value['last_name']."</td>
					<td>".$value['description']."</td>
					<td><h2>$".$value['pay_rate']."/hr</h2></td>
					<td>".$value['title']."</td>
				  </tr>";
				  
		echo "</tr></table><input type='hidden' id='worker_id_value' name='worker_id' value='".$value['id']."' /><input type='hidden' id='interview_text' value='Interview ".$value['first_name']." ".$value['last_name']."'>";
	
	}
	public function generate_share_link($user_id) {
$oDB = $this->oDB->prepare("
SELECT 
	`first_name`,
	`last_name`,
	`pay_rate`,
	`description`,
	`title`,
	`rating`,
	`hours_completed`,
	`photograph`,
	`id`
FROM 
	`user`
WHERE
	`user`.`id` = ?
LIMIT 1
");
	$oDB->bindParam(1,$user_id,PDO::PARAM_INT);
	$oDB->execute();
	$value = $oDB->fetch(PDO::FETCH_ASSOC);

	return "https://workfar.com/member/".$value['id']."/".$value['title']."/".$value['first_name']."/".$value['pay_rate'];
	}
public function list_members() {
		$oDB = $this->oDB->prepare("
SELECT 
	`first_name`,
	`last_name`,
	`pay_rate`,
	`description`,
	`title`,
	`rating`,
	`hours_completed`,
	`photograph`,
	`id`
FROM 
	`user`
WHERE
	`user`.`has_profile` = 1
");
		$oDB->execute();
		$users = array();
		while ( $user = $oDB->fetch(PDO::FETCH_ASSOC) )
			$users[] = $user;
		echo "<table style='text-align:center;margin: 0 auto;'><tr>";
		$i = 0;
		foreach ($users as $key => $value) {
			echo "<td style='position: relative;border: 1px solid #000000;'>
					<a href='https://workfar.com/member/".$value['id']."/".$value['title']."/".$value['first_name']."/".$value['pay_rate']."'>
					<p style='font-size:18px;'>".substr($value['title'], 0, 30)."</p>
					<img alt='" .$value['first_name']." ".$value['last_name']." Remote Work" ."' class='profile_image' src='https://workfar.com/".$value['photograph']."' width='128' height='128' /><br><p style='font-size:12px; margin-bottom:0px;'>".$value['first_name']."</p><br><br>
					</a>
				  </td>";
			if (($i % 6) == 0) echo "</tr><tr>";
			$i++;
		}
		echo "</tr></table>";
	}
	public function email_blast() {
		include_once 'controller/mail.php';
		$mail = new Controller_Mail();
		$oDB = $this->oDB->prepare("
SELECT 
	`email`,
	`first_name`
FROM 
	`user`
WHERE
	`id` > 5
");
		$oDB->execute();
		while ( $user = $oDB->fetch(PDO::FETCH_ASSOC) ) {
			$mail->send_mail("tdicusmurray@gmail.com","Teddy", $user['email'],"","","[WEEKEND ONLY] " . $user['first_name'] .", Get this NOW before it's gone...","start_working.html",array('email' => $user['email']));		}
	}
	public function forgot_password_verify($hash,$email) {
		$email = str_replace(' ', '+', $email);
		$oDB = $this->oDB->prepare("
SELECT
	`user_email_verify`.`hash`,
	`user`.`email`,
	`user`.`id`,
	`user`.`user_salt`
FROM 
	`user_email_verify`
JOIN
	`user`
ON (`user_email_verify`.`user_id` = `user`.`id`)
WHERE
	`user_email_verify`.`hash` = ?
AND 
	`user`.`email` = ?
LIMIT 1
");
		$oDB->bindParam(1,$hash,PDO::PARAM_STR);
		$oDB->bindParam(2,$email,PDO::PARAM_STR);
		$oDB->execute();
		$user_email_verify = $oDB->fetch(PDO::FETCH_ASSOC);
		if ($user_email_verify) {
			echo "<div style='width:300px;margin: 0 auto;text-align:center;'><link href='/view/index/index.css' rel='stylesheet'><img src='/view/index/logo.gif' /><br>
			<form action='/change_password' method='post'>
			<input type='hidden' name='hash' value='".$user_email_verify['hash']."'>
			<input type='hidden' name='email' value='".$email."' />
			<br><input class='widetextbox' placeholder='New Password' style='border: 1px solid #211F1F !important; width: 200px !important;font-size: 18px; float: left;' type='password' name='new_password' /><br>
			<input type='submit' value='Reset Password' style='float:left;' class='et_pb_contact_submit' /></form></div>";
		} else  echo "Wrong reset code. <a href='https://WorkFar.com'>Go to WorkFar and try again</a>";
	}
}