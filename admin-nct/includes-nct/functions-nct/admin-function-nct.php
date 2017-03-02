<?php
function getLoggedinName() {
	global $db, $adminUserId;
	$qrysel = $db->select("uName","id=".$adminUserId."");
	$fetchUser = mysql_fetch_object($qrysel);
	return trim(addslashes(ucwords($fetchUser->uName)));
}

//check Admin Permission
function chkPermission($module){
	global $db, $adminUserId;
	//"permissions",
	$admSl = $db->select("tbl_admin", array("adminType"), array("id ="=>(int)$adminUserId))->result();
	if(!empty($admSl)){
		$adm = $admSl;
		//echo $adm['adminType']; exit; 
		if($adm['adminType'] == 'g'){
			$moduleId = $db->select("tbl_adminrole", array("id"), array("pagenm ="=>(string)$module))->result();
			$chkPermssion = $db->select("tbl_admin_permission", array("permission"), array("admin_id"=>(int)$adminUserId,"page_id"=>$moduleId['id']))->result();
			if(empty($chkPermssion['permission'])){
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				redirectPage(SITE_ADM_MOD.'home-nct/');
			}
		}
	}
}
function add_admin_activity($activity_array=array()){
	global $db,$adminUserId;
	$admSl = $db->select("tbl_admin", array("adminType"), array("id ="=>(int)$adminUserId))->result();
	if($admSl['adminType'] == 'g'){
		$activity_array['id'] = (isset($activity_array['id']))?$activity_array['id']:0;
		$activity_array['module'] = (isset($activity_array['module']))?getTableValue('tbl_adminrole','id',array("pagenm"=>$activity_array['module'])):0;
		$activity_array['activity'] = (isset($activity_array['activity']))?getTableValue('tbl_subadmin_action','id',array("constant"=>$activity_array['activity'])):0;
		$activity_array['action'] = (isset($activity_array['action']))?$activity_array['action']:'';
		$activity_array['created_date'] = date('Y-m-d H:i:s');
		$activity_array['updated_date'] = date('Y-m-d H:i:s');
		
		
		$val_array = array("activity_type"=>$activity_array['activity'],"page_id"=>$activity_array['module'],"admin_id"=>$adminUserId,"entity_id"=>$activity_array['id'],"entity_action"=>$activity_array['action'],"created_date"=>$activity_array['created_date'],"updated_date"=>$activity_array['updated_date']);
		$db->insert('tbl_admin_activity',$val_array);	
	}
}


function chkModulePermission($module){
	global $db, $adminUserId;
	//"permissions",
	$admSl = $db->select("tbl_admin", array("adminType"), array("id ="=>(int)$adminUserId))->result();
	if(!empty($admSl)){
		$adm = $admSl;
		//echo $adm['adminType']; exit; 
		if($adm['adminType'] == 'g'){
			$moduleId = $db->select("tbl_adminrole", array("id"), array("pagenm ="=>(string)$module))->result();
			$chkPermssion = $db->select("tbl_admin_permission", array("permission"), array("admin_id"=>(int)$adminUserId,"page_id"=>$moduleId['id'],"and permission !="=>""))->result();
			if(!empty($chkPermssion['permission'])){
				$qryRes = $db->pdoQuery("select id,constant from tbl_subadmin_action where id in (".$chkPermssion['permission'].")")->results();
				foreach($qryRes as $fetchRes){
					$permissions[] = $fetchRes["constant"];
				}
			}
		}else{
			$qryRes = $db->select("tbl_subadmin_action", array("id,constant"), array())->results();
			foreach($qryRes as $fetchRes){
				$permissions[] = $fetchRes["constant"];
			}
		}
	}
	return $permissions;
}

// Get Section wise Role Array
function getSectionRoleArray($flag=false) {
	global $db, $adminUserId;
	$arr[]=array();
	$type = '';
	$res1=$db->select('tbl_admin','id,adminType,permissions','id='.$adminUserId, NULL, NULL);
	$res1Fetch = mysql_fetch_object($res1);
	$permission = $res1Fetch->permissions!='' ? $res1Fetch->permissions : 0;

	$res=$db->select('tbl_adminsection','id,type,section_name', NULL, NULL, '`order` ASC');
	if(mysql_num_rows($res)>0) {
		$i=0;
		while($row=mysql_fetch_array($res)) {
			$per_wh_con = '';
			if($res1Fetch->adminType == 'g')
				$per_wh_con=($permission!='0')?(' AND id IN('.str_replace('|',',',$permission.')')):'';
			$status_wh=($res1Fetch->adminType == 's' && $flag == false) ?  " status IN ('a','s')":"status='a'";
			$qry_role="sectionid='".$row['id']."' AND ".$status_wh.$per_wh_con;
			$res_role=$db->select('tbl_adminrole','id,title,pagenm,image', $qry_role, NULL, '`seq` ASC', 0);
			if($tot=mysql_num_rows($res_role)>0) {
				$temp=$j=0;
				while($row_role=mysql_fetch_array($res_role)) {
					$arr[$i]['id']=$row_role['id'];
					$arr[$i]['text']=$row_role['title'];
					$arr[$i]['pagenm']=$row_role['pagenm'];
					$arr[$i]['image']=$row_role['image'];
					if($j==0) {
						$arr[$i]['optlbl']=$row['section_name'];
						$temp=$row['id'];$j++;
					} else if($j==($tot-1)) {
						$j=0;
					}
					$i++;
				}
			}
		}
	}
	return $arr;
}	
function makeConstantFile()
{
	global $db, $adminUserId;
	
	$files = glob(DIR_INC.'language/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	$qrysel1= $db->select("tbl_language", "*",array("status"=>"a"),"", "", 0)->results();
		
	foreach($qrysel1 as $fetchSel)
	{
		$fp = fopen(DIR_INC. "language-nct/".$fetchSel['id'].".php","wb");
		$content = '';
		
		$qsel1 = $db->select("tbl_constant","*",array("languageId"=>$fetchSel['id']))->results();
		
		$content.='<?php ';
		foreach($qsel1 as $fetchSel1)
		{
			if($fetchSel1['constantValue']!=''){
				$content.= ' define("'.stripslashes($fetchSel1['constantName']).'","'.str_replace('###SITE_NM###',SITE_NM,stripslashes($fetchSel1['constantValue'])).'"); ';
			}else{
				$qsel2 = $db->pdoQuery("SELECT constantValue FROM tbl_constant as c LEFT JOIN tbl_language as l ON l.id=c.languageId WHERE l.default_lan='y' and c.constantName='".$fetchSel1['constantName']."'")->result();
				$content.= ' define("'.stripslashes($fetchSel1['constantName']).'","'.str_replace('###SITE_NM###',SITE_NM,stripslashes($qsel2['constantValue'])).'"); ';
			}
		}
		$content.=' ?>';
		fwrite($fp,$content);
		fclose($fp);
	}
}


?>
