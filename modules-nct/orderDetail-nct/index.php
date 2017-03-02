<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.orderDetail-nct-nct.php");

	$id = isset($_GET['id']) && $_GET['id']!=''?$_GET['id']:'';
	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	//Check for product exist
	$exist = getTotalRows('tbl_orders',array("id"=>$id),'id');

	if($exist == 0){
		$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Invalid request"));
		redirectPage(SITE_URL);
	}
	
	$table = "tbl_product_deals";
	$module = 'orderDetail-nct';

	$winTitle = 'Order Detail '.SITE_NM;
	$headTitle = 'Order Detail';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new orderDetail($module,$id);

	if(isset($_POST['return']) && $_POST['return'] == 'Submit'){
		extract($_POST);

		//Update order table
		$db->update("tbl_orders",array("deliveryStatus"=>"r"),array("id"=>$id));

		$data = array();
		$data['userId'] = $sessUserId;
		$data['orderId'] = $id;
		$productId = getTableValue("tbl_orders","productId",array("id"=>$id));
		$data['productId'] = $productId;
		$data['subject'] = $subject;
		$data['message'] = $message;
		$data['createdDate'] = date('Y-m-d H:i:s');

		$imageName = $_FILES['imageName']['name'];
		
		$returnId = $db->insert("tbl_return_request",$data)->getLastInsertId();
		//print_r($imageName);

		//For update pending amount
		$amount = getTableValue('tbl_orders','paidAmount',array('id'=>$id));
		$oldPending = getTableValue('tbl_users','pendingAmount',array('id'=>$sessUserId));
		$finalPending = strval($oldPending + $amount);
		$db->update('tbl_users',array('pendingAmount'=>$finalPending),array('id'=>$sessUserId));
		
		//For images
		 $uploads_dir = DIR_UPD."returnImage";
	     if(!file_exists($uploads_dir)){
		 	mkdir($uploads_dir,0777,true);
		 }

		 foreach ($_FILES["imageName"]["error"] as $key => $error) {
		    if ($error == UPLOAD_ERR_OK) {
		        $tmp_name = $_FILES["imageName"]["tmp_name"][$key];
		        $name = $_FILES["imageName"]["name"][$key];
		        move_uploaded_file($tmp_name, "$uploads_dir/$name");
		        $createdDate = date('Y-m-d H:i:s');
		        $valArray = array("returnId"=>$returnId,"imageName"=>$name,"createdDate"=>$createdDate);
			 	$db->insert("tbl_return_image", $valArray);
		    }
		 }
		

		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Order return successfully"));
		redirectPage(SITE_URL.'order/'.$id);
	}

	$pageContent = $mainObj->getPageContent();
		
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>