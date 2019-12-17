<?php
require_once "bulletproof/src/bulletproof.php";

class Photograph extends Controller {
	public function create_photograph() {
		$bulletProof = new ImageUploader\BulletProof;
		try {
			$photograph = $bulletProof->fileTypes(array("gif", "jpg", "jpeg", "png"))
				->uploadDir("/var/www/html/view/members/workers/members")
				->limitSize(["min"=>1000, "max"=>55000]) 
				->shrink(array("width"=>288, "height"=>256)) 
				->upload($_FILES['photograph']);
			$oDB = $this->oDB->prepare("
UPDATE
	user
SET
	photograph = ?
WHERE
	id = ?
");
			$oDB->bindParam(1,$photograph,PDO::PARAM_STR);
			$oDB->bindParam(2,$_SESSION['id'],PDO::PARAM_INT);
			$oDB->execute();
		} catch(\ImageUploader\ImageUploaderException $e) {
		     echo $e->getMessage();
		}
	}
}