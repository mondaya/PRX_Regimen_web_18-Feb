<?php
	if(isset($_REQUEST['action'])){
		$action = $_REQUEST['action'];
		unset($_REQUEST['action']);
		unset($_POST['action']);
		unset($_GET['action']);
		http_response_code(404);
	}
?>