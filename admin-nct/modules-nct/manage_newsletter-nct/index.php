<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.manage_newsletter-nct.php");
	$module = "manage_newsletter-nct";
	$table = "tbl_newsletter";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN),
					array("bootstrap-multiselect.css", SITE_CSS));

	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-multiselect.js", SITE_JS),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));

	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));
	$breadcrumb = array("Newsletter");
	$page_name = "Newsletter";
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Newsletter';
	$winTitle = $headTitle.' - '.SITE_NM;

	if(isset($_POST["submitSendForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$qrySel = $db->select($table, array("*"), array("id"=>$id,"is_active"=>"y"))->result();
		foreach ($subscriber as $email) {
			$to = $email;
			$subject = $qrySel['subject'];
			$msgContent = $qrySel['description'];
			$fname = getTableValue('tbl_users', 'firstName', array('email'=>base64_encode($email)));
			$arrayCont = array("greetings"=>(!empty($fname)?$fname:"Subscriber"), "content"=>$msgContent);
			$message = generateEmailTemplate('newsletter', $arrayCont);
			sendMail($to, array('subject'=>$subject, 'message'=>$message['message']), array());
		}
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'sendemails'));
		redirectPage(SITE_ADM_MOD.$module);
	}

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->name = isset($name) ? $name : '';
		$objPost->subject = isset($subject) ? $subject : '';
		$objPost->description = isset($description) ? $description : '';
		$objPost->is_active	 = isset($is_active	) ? $is_active : 'n';

		if(!empty($objPost->name)){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
				$db->update($table, (array)$objPost, array("id"=>$id));
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'NoPermission'));
				}
			} else {
				if(in_array('add',$Permission)) {
					if(getTotalRows($table, array('name'=>$objPost->name))==0) {
						$objPost->created_date = date('Y-m-d H:i:s');
						$db->insert($table, (array)$objPost);
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
				}
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'fillAllvalues'));
		}
	}
	$objContent=new Content($module);
	$pageContent = $objContent->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");