<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.coupon_nct.php");
	$module = "coupon_nct";
	$table = "tbl_coupons";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-datepicker/css/datepicker.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));
	$scripts= array("core/datatable.js",
			  array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
			  array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
			  array("bootstrap-datepicker/js/bootstrap-datepicker.js",SITE_ADM_PLUGIN),
			  array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));
	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Coupons';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->coupon_code = isset($coupon_code) ? $coupon_code : '';
		$objPost->start_date = isset($start_date) ? $start_date : '';
		$objPost->end_date = isset($end_date) ? $end_date : '';
		$objPost->discount = isset($discount) ? $discount : '';
		$objPost->is_active = isset($status) ? $status : 'n';
		$objPost->ipaddress = get_ip_address();

		if(!empty($objPost->coupon_code) && !empty($objPost->start_date) && !empty($objPost->end_date) && !empty($objPost->discount)){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
					$db->update($table, (array)$objPost, array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
					}
			} else {
				if(in_array('add',$Permission)){
					if(getTotalRows($table, array('coupon_code'=>$objPost->coupon_code), 'userId')==0){
						$objPost->created_date = date('Y-m-d H:i:s');
						$id = $db->insert($table, (array)$objPost)->getLastInsertId();

						//For notification
	    				$users = $db->pdoQuery("Select id,firstName,email from tbl_users where isActive='y'")->results();

	    				foreach ($users as $key => $value) {
	    					$newPrormoPosted = getTableValue("tbl_notifications","newPrormoPosted",array("userId"=>$value['id']));

	    					//For dashboard notifications
	    					$db->insert("tbl_user_notifications",array("notificationType"=>5,"fromId"=>'',"toId"=>$value['id'],"refId"=>$id,"createdDate"=>date("Y-m-d H:i:s")));

	    					//For email notification
	    					if($newPrormoPosted == 'y'){
								$contArray = array(
									"USER_NM"=>$value['firstName'],
									"PROMO"=>$objPost->coupon_code,
									"START"=>date('m-d-Y',strtotime($objPost->start_date)),
									"END"=>date('m-d-Y',strtotime($objPost->end_date)),
									"DISCOUNT"=>$objPost->discount

								);
								sendMail(base64_decode($value['email']),"new_promocode_posted",$contArray);
							}
	    				}

						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					}else{
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
					}
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

	$objUsers=new Coupons($module);
	$pageContent = $objUsers->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");