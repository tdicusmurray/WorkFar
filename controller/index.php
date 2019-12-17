<?php

class Index extends Controller {

	public function __construct() {
		parent::__construct();
	}
	
	public function render_seo() {
		require_once("view/home/index.php");
	}
	public function render() {
		require_once("view/index/index.html");
	}
}