<?php

class Message_Board extends Controller {
	public function main_message_board_categories() {
		$oDB = $this->oDB->prepare("
SELECT
	*
FROM
	`message_board_category`
WHERE
	`parent_category_id` = 0
ORDER BY
	`id`
ASC
");
		$oDB->execute();
		while ($aMessageBoardCategory = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aMessageBoardCategories[] = $aMessageBoardCategory;
			
		return $aMessageBoardCategories;
	}
	public function getPostsByCategory($id, $page = 0) {
		$page = ($page*7);
		$oDB = $this->oDB->prepare("
SELECT
	`message_board_post`.`id`,
	`message_board_post`.`name`,
	`message_board_post`.`timestamp`,
	`message_board_post`.`message_count`,
	`user`.`first_name`,
	`user`.`last_name`
FROM
	`message_board_post`
JOIN 
	`user`
ON 
	(`message_board_post`.`user_id` = `user`.`id`)
WHERE
	`message_board_post`.`category_id` = ?
ORDER BY `message_board_post`.`id` DESC
LIMIT ?, 8
");
		$oDB->bindParam(1,$id,PDO::PARAM_INT);
		$oDB->bindParam(2,$page,PDO::PARAM_INT);

		$oDB->execute();
		while ($aPost = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aPosts[] = $aPost;
		if (isset($aPosts))
			return $aPosts;
	}
	public function createNewPost($title,$category,$message) {

		$oDB1 = $this->oDB->prepare("
INSERT INTO `message_board_post` (`name`,`user_id`,`category_id`) VALUES ( ?, ?, ? )
");
		$title = htmlentities($title);
		$oDB1->bindParam(1,$title,PDO::PARAM_STR);
		$oDB1->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB1->bindParam(3,$category,PDO::PARAM_INT);
		$oDB1->execute();
		
		$sql_result = $oDB1->errorInfo();
		if ($sql_result[0] == "0000")
			$result = $this->oDB->lastInsertId();
		else $result = false;
		
		$oDB = $this->oDB->prepare("
INSERT INTO `message_board_post_reply` (`message`,`user_id`,`post_id`) VALUES ( ?, ?, ? )
");
		$message = htmlentities($message);
		$lastReplyInsertId = $this->oDB->lastInsertId();
		$oDB->bindParam(1,$message,PDO::PARAM_STR);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(3,$lastReplyInsertId,PDO::PARAM_INT);
		$oDB->execute();
		
		return $result;
	}
	public function replyToPost($message,$post_id,$reply_id) {
		$oDB = $this->oDB->prepare("
INSERT INTO
	`message_board_post_reply`
	(`message`, `user_id`,`post_id`,`reply_id`)
VALUES
	(?, ?, ?, ?);
");
		$message = htmlentities($message);
		$oDB->bindParam(1,$message,PDO::PARAM_STR);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->bindParam(3,$post_id,PDO::PARAM_INT);
		$oDB->bindParam(4,$reply_id,PDO::PARAM_INT);
				
		$oDB->execute();
	}
	public function vote_up($reply_id) {
		$oDB = $this->oDB->prepare("
SELECT * FROM vote WHERE reply_id = ? AND user_id = ? LIMIT 1
");
		$oDB->bindParam(1,$reply_id,PDO::PARAM_INT);
		$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
				
		$oDB->execute();
		$vote = $oDB->fetch(PDO::FETCH_ASSOC);
		if (!isset($vote['id'])) {
			$oDB = $this->oDB->prepare("
INSERT INTO
	`vote`
	(`reply_id`, `user_id`)
VALUES
	(?, ?);
");
			$oDB->bindParam(1,$reply_id,PDO::PARAM_INT);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
			$this->send(array("result" => true));
		} else $this->send(array("result" => false));
	}
	public function getPostReplies($post_id) {
				
		$oDB = $this->oDB->prepare("
SELECT
	`message_board_post_reply`.`id` as reply_id,
	`message_board_post_reply`.`message`,
	`message_board_post_reply`.`timestamp`,
	`message_board_post`.`id` as post_id,
	`user`.`first_name`,
	`user`.`last_name`,
	`user`.`photograph`,
	`message_board_post_reply`.`votes`
FROM
	`message_board_post_reply`
JOIN
	`message_board_post`
ON
	(`message_board_post`.`id` = `message_board_post_reply`.`post_id`)
JOIN
	`user`
ON (`message_board_post_reply`.`user_id` = `user`.`id`)
WHERE
	`message_board_post_reply`.`post_id` = ?
ORDER BY
	votes DESC,
	reply_id DESC
");
		$oDB->bindParam(1,$post_id,PDO::PARAM_INT);
		
		$oDB->execute();
		while ($aPost = $oDB->fetch(PDO::FETCH_ASSOC))  {
			$aPost['message'] = nl2br($aPost['message']);
			$aPosts[] = $aPost;
		}
		if (isset($aPosts))
			return $aPosts;
	}
	public function my_posts() {
		$oDB = $this->oDB->prepare("
SELECT
	`id`,
	`name`,
	`category_id`,
	`message_count` 
FROM
	`message_board_post`
WHERE
	`user_id` = ?
");
		$oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		$aPosts = array();
		while ($aPost = $oDB->fetch(PDO::FETCH_ASSOC)) 
			$aPosts[] = $aPost;
		if (isset($aPosts))
			return $aPosts;
	}
	public function list_posts() {
		$oDB = $this->oDB->prepare("
SELECT
	`message_board_post`.`id`,
	`message_board_post`.`name`,
	`message_board_category`.`name` as category_name,
	`message_board_post`.`message_count` 
FROM
	`message_board_post`
JOIN
	`message_board_post_reply`
ON
	(`message_board_post`.`id` = `message_board_post_reply`.`post_id`)
JOIN
	`message_board_category`
ON
	(`message_board_post`.`category_id` = `message_board_category`.`id`)
ORDER BY `message_board_post`.`id` DESC
");
		$oDB->execute();
		echo "<table class='table table-inverse' border='0' style='margin: 0 auto;'><tr><td>Post Name</td><td>Category</td><td>Message Count</td></tr>";
		while ($aPost = $oDB->fetch(PDO::FETCH_ASSOC)) {
			echo "<tr><td>".$aPost['name']."</td><td>".$aPost['category_name']."</td><td>".$aPost['message_count']."</td><td></tr>";
		}
		echo "</table>";
	}
}