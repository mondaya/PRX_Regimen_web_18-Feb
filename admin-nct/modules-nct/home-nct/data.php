<?php
require_once("../../../includes-nct/config-nct.php");
// Fetch the data
extract($_REQUEST);
//echo "<pre>";print_r($_REQUEST);exit;
if($_REQUEST['type']=='user_views'){
	$views = array();
	$qrySel=$db->pdoQuery("SELECT createdDate,SUM(IF(userType  = '1',1, 0)) AS c, SUM(IF(userType  = '2',1, 0)) AS p FROM tbl_users where isActive='y' GROUP BY DATE(createdDate)",0)->results();
	foreach($qrySel as $fetchRes) {
		$date   = date("Y-m-d",strtotime($fetchRes['createdDate']));
		$views[]= array('date' => $date, 'supplier' => $fetchRes['c'], 'drivers' => $fetchRes['p']);
	}
	print json_encode($views);
	exit;
}
else if($_REQUEST['type']=='products_view'){
	$qrySel=$db->pdoQuery("SELECT SUM(IF(loadStatus = 'pending',1, 0)) AS pending,
										SUM(IF(loadStatus = 'active',1, 0)) AS active,
										SUM(IF(loadStatus = 'dispute',1, 0)) AS dispute,
										SUM(IF(loadStatus = 'completed',1, 0)) AS completed 
										FROM tbl_loads",0)->results();
	foreach($qrySel as $fetchRes) {
				$row['pending'] = $fetchRes['pending'];
				$row['active'] = $fetchRes['active'];
				$row['dispute'] = $fetchRes['dispute'];
				$row['completed'] = $fetchRes['completed'];
	  	}
	print json_encode($row,JSON_NUMERIC_CHECK);
	exit;
}
else if($_REQUEST['type']=='pie') {
	$qrySel=$db->pdoQuery("SELECT DISTINCT COUNT( l.loadId ) AS TotalProjects, c.categoryName AS subCategoryName FROM tbl_loads AS l INNER JOIN tbl_category AS c ON l.categoryId = c.categoryId WHERE c.isActive = 'y' GROUP BY c.categoryName",0)->results();
	$rows = array();
	foreach($qrySel as $fetchRes) {
			$row[0] = $fetchRes['TotalProjects'];
			$row[1] = $fetchRes['subCategoryName'];
			array_push($rows,$row);
	}
	print json_encode($rows);
	exit;
}
?>