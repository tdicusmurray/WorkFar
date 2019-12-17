<?php 
	session_start();
 	include_once '/var/www/html/controller/controller.php';
	include_once '/var/www/html/controller/marketplace.php';
	$marketplace = new Marketplace();
	if (isset($_SESSION['csrf']) && $_SESSION['csrf'] == $_GET['state'])
		$marketplace->create_connect_account($_GET['code']);
	header("Location: https://www.workfar.com/");
?>