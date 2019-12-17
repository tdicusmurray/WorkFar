<?php

class WorkTime_Monitor extends Controller {

	public function __construct() {
		parent::__construct();
	}
	public function is_logged_in() {
		if (isset($_SESSION['email'])) return true;
		else return false;
	}
	public function work_history($work_id) {
		$oDB = $this->oDB->prepare("
SELECT 
	`worktime_monitor`.`id`,
	`worktime_monitor`.`video_name`,
	`worktime_monitor`.`timestamp`,
	`work_payment`.`charge_href`
FROM
	`worktime_monitor`
LEFT JOIN
	`work_payment`
ON
	(`worktime_monitor`.`id` = `work_payment`.`worktime_monitor_id`)
JOIN
	`work` 
ON
	(`worktime_monitor`.`work_id` = `work`.`id`)
JOIN
	`user`
ON	(`work`.`worker_id` = `user`.`id`)
WHERE
	`work`.`id` = ? 
AND
	`user`.`id` = ?
ORDER BY
	`worktime_monitor`.`timestamp`
ASC
");
		$oDB->bindParam(1, $work_id,PDO::PARAM_INT);
		$oDB->bindParam(2, $_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		while($worktime_monitor = $oDB->fetch(PDO::FETCH_ASSOC)) {
			echo '<span class="worker_view"><video width="200" height="200" charge_href="'.$worktime_monitor['charge_href'].'" work_id="'.$work_id.'" controls muted src="work_show_video/'.$work_id.'/'.$worktime_monitor['video_name'] .'"></video></span>';	
		}
	}
	public function work_show_video($work_id,$video_name) {
		$filename = "controller/workfar_monitor_videos/".$video_name;
		if(file_exists($filename)) {
		    //Get file type and set it as Content Type
		    $finfo = finfo_open(FILEINFO_MIME_TYPE);
		    header('Content-Type: ' . finfo_file($finfo, $filename));
		    finfo_close($finfo);

		    //Use Content-Disposition: attachment to specify the filename
		    header('Content-Disposition: attachment; filename='.basename($filename));

		    //No cache
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');

		    //Define file size
		    header('Content-Length: ' . filesize($filename));

		    ob_clean();
		    flush();
		    readfile($filename);
		    exit;
		}
	}
	public function employer_history($work_id) {
			$oDB = $this->oDB->prepare("
SELECT 
	`worktime_monitor`.`id`,
	`worktime_monitor`.`video_name`,
	`worktime_monitor`.`timestamp`
FROM
	`worktime_monitor`
JOIN
	`work` 
ON
	(`worktime_monitor`.`work_id` = `work`.`id`)
JOIN
	`user`
ON	(`work`.`employer_id` = `user`.`id`)
WHERE
	`work`.`id` = ? 
AND
	`user`.`id` = ?
ORDER BY
	`worktime_monitor`.`timestamp`
ASC
");
		
		$oDB->bindParam(1, $work_id,PDO::PARAM_INT);
		$oDB->bindParam(2, $_SESSION['id'],PDO::PARAM_INT);
		$oDB->execute();
		while($worktime_monitor = $oDB->fetch(PDO::FETCH_ASSOC)) {
			echo '<span class="employer_view"><video id="'.$worktime_monitor['id'].'" style="width:200px; height: 200px;" controls><source src="show_video/'.$work_id.'/'.$worktime_monitor['video_name'] .'" type="video/webm" /></video></span>';
		}
	}
	public function dispute_video($worktime_monitor_id) {
		$oDB = $this->oDB->prepare("
SELECT
	worktime_monitor.id
FROM
	worktime_monitor
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
LIMIT 1
");
        $oDB->bindParam(1,$_SESSION['id'],PDO::PARAM_INT);
        $oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
        $oDB->execute();
        $work_payment = $oDB->fetch(PDO::FETCH_ASSOC);

        if (isset($work_payment)) {
        	$oDB = $this->oDB->prepare("
SELECT 
	`worktime_monitor`.`id`,
	`worktime_monitor`.`video_name`,
	`worktime_monitor`.`timestamp`,
	`worktime_monitor`.`work_id`
FROM
	`worktime_monitor`
WHERE
	`worktime_monitor`.`id` = ?
LIMIT 1
");
			$oDB->bindParam(1, $worktime_monitor_id,PDO::PARAM_INT);
			$oDB->execute();
			while($worktime_monitor = $oDB->fetch(PDO::FETCH_ASSOC)) {
				echo '<span id="dispute_video"><video id="'.$worktime_monitor['id'].'" style="width:500px; height: 300px;" controls><source src="show_video/'.$worktime_monitor['work_id'].'/'.$worktime_monitor['video_name'] .'" type="video/webm" /></video></span>';
			}
		}
	}
	public function serveFilePartial($fileName, $fileTitle = null, $contentType = 'video/webm')
{
	if( !file_exists($fileName) )
		throw New \Exception(sprintf('File not found: %s', $fileName));
	if( !is_readable($fileName) )
		throw New \Exception(sprintf('File not readable: %s', $fileName));
	### Remove headers that might unnecessarily clutter up the output
	header_remove('Cache-Control');
	header_remove('Pragma');
	### Default to send entire file
	$byteOffset = 0;
	$byteLength = $fileSize = filesize($fileName);
	header('Accept-Ranges: bytes', true);
	header(sprintf('Content-Type: %s', $contentType), true);
	if( $fileTitle )header('Content-Type: ' . finfo_file($finfo, $filename));
		header(sprintf('Content-Disposition: attachment; filename="%s"', $fileTitle));
	### Parse Content-Range header for byte offsets, looks like "bytes=11525-" OR "bytes=11525-12451"
	if( isset($_SERVER['HTTP_RANGE']) && preg_match('%bytes=(\d+)-(\d+)?%i', $_SERVER['HTTP_RANGE'], $match) )
	{
		### Offset signifies where we should begin to read the file
		$byteOffset = (int)$match[1];
		### Length is for how long we should read the file according to the browser, and can never go beyond the file size
		if( isset($match[2]) ){
			$finishBytes = (int)$match[2];
	        	$byteLength = $finishBytes + 1;
		} else {
			$finishBytes = $fileSize - 1;
		}
	
		$cr_header = sprintf('Content-Range: bytes %d-%d/%d', $byteOffset, $finishBytes, $fileSize);
	
		header("HTTP/1.1 206 Partial content");
		header($cr_header);  ### Decrease by 1 on byte-length since this definition is zero-based index of bytes being sent
	}
	$byteRange = $byteLength - $byteOffset;
	header(sprintf('Content-Length: %d', $byteRange));
	header(sprintf('Expires: %s', date('D, d M Y H:i:s', time() + 60*60*24*90) . ' GMT'));
	$buffer = ''; 	### Variable containing the buffer
	$bufferSize = 512 * 16; ### Just a reasonable buffer size
	$bytePool = $byteRange; ### Contains how much is left to read of the byteRange
	if( !$handle = fopen($fileName, 'r') )
		throw New \Exception(sprintf("Could not get handle for file %s", $fileName));
	if( fseek($handle, $byteOffset, SEEK_SET) == -1 )
		throw New \Exception(sprintf("Could not seek to byte offset %d", $byteOffset));
	while( $bytePool > 0 )
	{
		$chunkSizeRequested = min($bufferSize, $bytePool); ### How many bytes we request on this iteration
		### Try readin $chunkSizeRequested bytes from $handle and put data in $buffer
		$buffer = fread($handle, $chunkSizeRequested);
		### Store how many bytes were actually read
		$chunkSizeActual = strlen($buffer);
		### If we didn't get any bytes that means something unexpected has happened since $bytePool should be zero already
		if( $chunkSizeActual == 0 )
		{
			### For production servers this should go in your php error log, since it will break the output
			trigger_error('Chunksize became 0', E_USER_WARNING);
			break;
		}
		### Decrease byte pool with amount of bytes that were read during this iteration
		$bytePool -= $chunkSizeActual;
		### Write the buffer to output
		print $buffer;
		### Try to output the data to the client immediately
		flush();
	}
	exit();
}
	public function show_video($work_id,$video_name) {
		$filename = "controller/workfar_monitor_videos/".$video_name;
		if(file_exists($filename)) {
			$this->serveFilePartial($filename, null, 'video/webm');
		}
	}
	public function upload_keystrokes($keystrokes) {
	$keystrokes = htmlentities($keystrokes);
	$oDB = $this->oDB->prepare("
UPDATE
	`worktime_monitor`
SET
	`keyboard_activity` = ?
WHERE
	`keyboard_activity` = ''
AND
	`id` = ?
");
				$oDB->bindParam(1,$keystrokes,PDO::PARAM_STR);
				$oDB->bindParam(2,$_SESSION['work_id'],PDO::PARAM_INT);
				$oDB->execute();
	
	}
	/* EXPLOITABLE I THINK.....no checking of the image data, possibly cross site injection */
	public function save_screenshot($work_id) {
	/* Need to make sure they are on the right work */
				$screenshot = file_get_contents('php://input');
				$oDB = $this->oDB->prepare("
INSERT INTO 
	`worktime_monitor`(`image`,`work_id`)
VALUES 
	( ?,? )
");
				$oDB->bindParam(1,$screenshot,PDO::PARAM_STR);
				$oDB->bindParam(2,$work_id,PDO::PARAM_INT);
				$oDB->execute();
				$_SESSION['work_id'] = $this->oDB->lastInsertId();

				$oDB = $this->oDB->prepare("
SELECT 
	`user_payment_information`.`credit_card_href`,
	`user_payment_information`.`credit_card_id` 
FROM 
	`user_payment_information`
JOIN 
	`work`
ON
	(`work`.`employer_id` = `user_payment_information`.`user_id`)
WHERE
	`work`.`id` = ?
LIMIT 1
");
				$oDB->bindParam(1,$work_id,PDO::PARAM_INT);
				$oDB->execute();
				$payment_information = $oDB->fetch(PDO::FETCH_ASSOC);
				include_once 'controller/payment.php';
				$payment = new Controller_Payment();
				$payment->chargeCard($payment_information['credit_card_href'], $payment_information['credit_card_id']);

				return $_SESSION['work_id'];
	}
}