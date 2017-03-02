<?php	
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.users-nct.php");		
	$module = "users-nct";
	$table = "tbl_users";
	
	
	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));
	
	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));
	
	chkPermission($module);
	$Permission=chkModulePermission($module);
	
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' User';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->fname = isset($firstName) ? $firstName : '';
		

		$oldCredit = getTableValue('tbl_users','creditAmount',array('id'=>$id));
		$objPost->creditAmount = $oldCredit;
		
		if($addAmount > 0){
			
			$objPost->creditAmount = $oldCredit + $addAmount;
			//For insert in admin payment history
			$data = array();
			$data['userId'] = $id;
			$data['amount'] = $addAmount;
			$data['transactionType'] = 'a';
			$data['createdDate'] = date('Y-m-d H:i:s');
			$db->insert('tbl_admin_payment_history',$data);


		}else if($removeAmount > 0){
			if($removeAmount > $objPost->creditAmount){
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'You can\'t remove more than credit amount.'));
				redirectPage(SITE_ADM_MOD.$module);
			}
			
			$objPost->creditAmount = $oldCredit - $removeAmount;

			//For insert in admin payment history
			$data = array();
			$data['userId'] = $id;
			$data['amount'] = $removeAmount;
			$data['transactionType'] = 'r';
			$data['createdDate'] = date('Y-m-d H:i:s');
			$db->insert('tbl_admin_payment_history',$data);
		}
		
		$objPost->status = isset($status) ? $status : '';
		
		if($objPost->fname != "" && strlen($objPost->fname) > 0 ){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$db->update($table, array("firstName"=>$objPost->fname,"creditAmount"=>$objPost->creditAmount,"isActive"=>$objPost->status), array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
					}	
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = array('type'=>'err','var'=>'fillAllvalues');
		}
	}	
	
	$objUsers=new Users($module);
	$pageContent = $objUsers->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");