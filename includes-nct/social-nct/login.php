<?php
	require '../config-nct.php';
	require 'Social.php';
	$Social_obj= new Social();
	if(isset($_GET['facebook'])){
		$Social_obj->facebook();
	}
	if(isset($_GET['google'])){
		$Social_obj->google();
	}
?>
<script type="text/javascript">
	window.close();
</script>